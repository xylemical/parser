<?php

namespace Xylemical\Parser;

use Xylemical\Parser\Exception\SyntaxException;

/**
 * Provides tokenization of strings using regex patterns.
 */
class Tokenizer {

  /**
   * Default primary patterns used to tokenize the input string.
   */
  protected const PATTERNS = [];

  /**
   * Default refinements of primary patterns.
   */
  protected const REFINEMENTS = [];

  /**
   * The patterns used for generating tokens.
   *
   * @var string[]
   */
  protected array $patterns;

  /**
   * The patterns used to refine tokens.
   *
   * @var string[][]
   */
  protected array $refinements = [];

  /**
   * The compiled pattern matches.
   *
   * @var string[]
   */
  protected array $compiled = [];

  /**
   * Tokenizer constructor.
   */
  public function __construct() {
    $this->patterns = static::PATTERNS;
    $this->refinements = static::REFINEMENTS;
  }

  /**
   * Get the patterns.
   *
   * @return string[]
   *   The patterns.
   */
  public function getPatterns(): array {
    return $this->patterns;
  }

  /**
   * Get the pattern for the token.
   *
   * @param string $token
   *   The token.
   *
   * @return string
   *   The pattern.
   */
  public function getPattern(string $token): string {
    return $this->patterns[$token] ?? '';
  }

  /**
   * Set multiple primary patterns.
   *
   * @param string[] $patterns
   *   The patterns for the primary tokens.
   *
   * @return $this
   */
  public function setPatterns(array $patterns): static {
    foreach ($patterns as $name => $pattern) {
      $this->setPattern($name, $pattern);
    }
    return $this;
  }

  /**
   * Set a primary pattern.
   *
   * @param string $name
   *   The name.
   * @param string $pattern
   *   The pattern.
   *
   * @return $this
   */
  public function setPattern(string $name, string $pattern): static {
    $this->patterns[$name] = $pattern;
    return $this;
  }

  /**
   * Reset a primary pattern.
   *
   * This removes it if not in the defined patterns, or resets it to the
   * original pattern.
   *
   * @param string $name
   *   The name.
   *
   * @return $this
   */
  public function resetPattern(string $name = ''): static {
    $this->patterns = $this->doReset($this->patterns, $name, static::PATTERNS);
    return $this;
  }

  /**
   * Get the refinements.
   *
   * @param string $token
   *   The token.
   *
   * @return array
   *   The refinements.
   */
  public function getRefinements(string $token = ''): array {
    if ($token) {
      return $this->refinements[$token] ?? [];
    }
    return $this->refinements;
  }

  /**
   * Get the refinement.
   *
   * @param string $token
   *   The token.
   * @param string $refinement
   *   The refinement token.
   *
   * @return string
   *   The results.
   */
  public function getRefinement(string $token, string $refinement): string {
    return $this->refinements[$token][$refinement] ?? '';
  }

  /**
   * Set the refinement token patterns.
   *
   * @param string $token
   *   The token.
   * @param string[] $patterns
   *   The patterns.
   *
   * @return $this
   */
  public function setRefinements(string $token, array $patterns): static {
    $this->refinements[$token] = [];
    foreach ($patterns as $name => $pattern) {
      $this->setRefinement($token, $name, $pattern);
    }
    return $this;
  }

  /**
   * Adds a refinement pattern for an existing token.
   *
   * @param string $token
   *   The token.
   * @param string $name
   *   The refinement token name.
   * @param string $pattern
   *   The refinement token pattern.
   *
   * @return $this
   */
  public function setRefinement(string $token, string $name, string $pattern): static {
    $this->refinements[$token][$name] = $pattern;
    return $this;
  }

  /**
   * Reset a refinement.
   *
   * @param string $token
   *   The token name. When left blank resets all refinements.
   * @param string $refinement
   *   The refinement name.
   *
   * @return $this
   */
  public function resetRefinement(string $token = '', string $refinement = ''): static {
    if ($refinement) {
      $this->refinements[$token] = $this->doReset(
        $this->refinements[$token] ?? [],
        $refinement,
        static::REFINEMENTS[$token] ?? []);
    }
    else {
      $this->refinements = $this->doReset($this->refinements, $token, static::REFINEMENTS);
    }
    return $this;
  }

  /**
   * Perform a reset on an array.
   *
   * @param array $source
   *   The source options.
   * @param string $index
   *   The option to reset.
   * @param array $resets
   *   The reset values.
   */
  protected function doReset(array $source, string $index, array $resets): array {
    if ($index) {
      $source[$index] = $resets[$index] ?? NULL;
    }
    else {
      $source = $resets;
    }
    return array_filter($source);
  }

  /**
   * Compiles the tokens into a regex pattern.
   *
   * @return string
   *   The regex.
   */
  protected function compile(array $patterns): string {
    $key = md5(serialize($patterns));
    if (isset($this->compiled[$key])) {
      return $this->compiled[$key];
    }

    $patterns = array_map(function ($token, $pattern) {
      $pattern = str_replace('`', '\\`', $pattern);
      return "(*MARK:{$token}){$pattern}";
    }, array_keys($patterns), $patterns);

    $this->compiled[$key] = '`(?:' . implode('|', $patterns) . ')`A';
    return $this->compiled[$key];
  }

  /**
   * Get the line endings used in the line.
   *
   * @param string $line
   *   The line.
   *
   * @return string
   *   The eol marker.
   */
  protected function getLineEnding(string $line): string {
    if (preg_match("`(?:\r\n|\r|\n)`", $line, $match)) {
      return $match[0];
    }
    return '';
  }

  /**
   * Process a string using things like refinements.
   *
   * @param array $patterns
   *   The patterns to match against.
   * @param string $input
   *   The string input to parse.
   * @param array $refinements
   *   The tokens under refinement.
   * @param int $line
   *   The line number.
   * @param int $column
   *   The column number.
   * @param \Xylemical\Parser\TokenStream $stream
   *   The token stream.
   *
   * @throws \Xylemical\Parser\Exception\SyntaxException
   */
  protected function process(array $patterns, string $input, array $refinements, int &$line, int &$column, TokenStream $stream): void {
    if (!$patterns) {
      throw new SyntaxException();
    }
    $eol = $this->getLineEnding($input);
    $eolLength = strlen($eol);

    $pattern = $this->compile($patterns);

    while ($input) {
      if (!preg_match($pattern, $input, $match)) {
        throw new SyntaxException('Unable to match to a token.');
      }

      $token = $match['MARK'];
      $value = $match[0];
      $input = substr($input, strlen($value));
      if (isset($this->refinements[$token]) && !in_array($token, $refinements)) {
        $this->process(
          $this->refinements[$token] + $patterns,
          $value,
          array_merge($refinements, [$token]),
          $line,
          $column,
          $stream
        );
      }
      else {
        $stream->addToken(new Token($token, $value, $line, $column));

        if ($eol && (($pos = strrpos($value, $eol)) !== FALSE)) {
          $line += substr_count($value, $eol);
          $remainder = substr($value, $pos + $eolLength);
          $column = 1 + strlen($remainder);
        }
        else {
          $column += strlen($value);
        }
      }
    }
  }

  /**
   * Tokenizes the input string.
   *
   * @param string $input
   *   The input to parse.
   *
   * @return \Xylemical\Parser\TokenStream
   *   The token stream.
   *
   * @throws \Xylemical\Parser\Exception\SyntaxException
   */
  public function tokenize(string $input): TokenStream {
    $stream = new TokenStream();
    $line = 1;
    $column = 1;
    $this->process($this->patterns, $input, [], $line, $column, $stream);
    return $stream;
  }

}
