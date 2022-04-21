<?php

namespace Xylemical\Parser;

use Xylemical\Parser\Exception\UnexpectedEndException;
use Xylemical\Parser\Exception\UnexpectedTokenException;

/**
 * The token stream.
 */
class TokenStream implements \Iterator, \Countable {

  /**
   * The token stream.
   *
   * @var \Xylemical\Parser\Token[]
   */
  protected array $tokens = [];

  /**
   * The current iterative location.
   *
   * @var int
   */
  protected int $pointer = 0;

  /**
   * Stream constructor.
   *
   * @param \Xylemical\Parser\Token[] $tokens
   *   The tokens.
   */
  public function __construct(array $tokens = []) {
    $this->addTokens($tokens);
  }

  /**
   * Get the tokens.
   *
   * @return \Xylemical\Parser\Token[]
   *   The tokens.
   */
  public function getTokens(): array {
    return $this->tokens;
  }

  /**
   * Add tokens to the stream.
   *
   * @param \Xylemical\Parser\Token[] $tokens
   *   The tokens.
   *
   * @return $this
   */
  public function addTokens(array $tokens): static {
    foreach ($tokens as $token) {
      $this->addToken($token);
    }
    return $this;
  }

  /**
   * Add a token to the stream.
   *
   * @param \Xylemical\Parser\Token $token
   *   The token.
   *
   * @return $this
   */
  public function addToken(Token $token): static {
    $this->tokens[] = $token;
    return $this;
  }

  /**
   * Check the next token.
   *
   * @return \Xylemical\Parser\Token|null
   *   The token or NULL.
   */
  public function peek(): ?Token {
    return $this->tokens[0] ?? NULL;
  }

  /**
   * Consume the current token.
   *
   * @return \Xylemical\Parser\Token|null
   *   The token.
   */
  public function consume(): ?Token {
    return array_shift($this->tokens);
  }

  /**
   * Check the next in stream is type.
   *
   * @param string $type
   *   The type.
   * @param string|string[]|null $value
   *   When not NULL, matches the string, or one of the strings.
   *
   * @return bool
   *   The result.
   */
  public function is(string $type, mixed $value = NULL): bool {
    if (isset($this->tokens[0])) {
      $token = $this->tokens[0];
      if ($token->getType() !== $type) {
        return FALSE;
      }

      return match (TRUE) {
        is_array($value) && !in_array($token->getToken(), $value),
          !is_null($value) && $token->getToken() !== $value => FALSE,
        default => TRUE
      };
    }
    return FALSE;
  }

  /**
   * Check the next in stream is one of the types.
   *
   * @param string[] $types
   *   The token types.
   *
   * @return bool
   *   The result.
   */
  public function isOneOf(array $types): bool {
    if (isset($this->tokens[0])) {
      return in_array($this->tokens[0]->getType(), $types);
    }
    return FALSE;
  }

  /**
   * Expect the next token to match the type and value.
   *
   * @param string $type
   *   The token type.
   * @param string|string[]|null $value
   *   The value to match. See is().
   *
   * @return \Xylemical\Parser\Token
   *   The expected token.
   *
   * @throws \Xylemical\Parser\Exception\SyntaxException
   */
  public function expect(string $type, mixed $value = NULL): Token {
    if (!$this->is($type, $value)) {
      if ($token = $this->consume()) {
        throw new UnexpectedTokenException('Unexpected token.', $token);
      }
      throw new UnexpectedEndException();
    }

    return $this->consume();
  }

  /**
   * Expect the next token to match one of the types.
   *
   * @param string[] $types
   *   The types.
   *
   * @return \Xylemical\Parser\Token
   *   The expected token.
   *
   * @throws \Xylemical\Parser\Exception\SyntaxException
   */
  public function expectOneOf(array $types): Token {
    if (!$this->isOneOf($types)) {
      if ($token = $this->consume()) {
        throw new UnexpectedTokenException('Unexpected token.', $token);
      }
      throw new UnexpectedEndException();
    }
    return $this->consume();
  }

  /**
   * Optionally get the next token to match the type and value.
   *
   * @param string $type
   *   The token type.
   * @param string|string[]|null $value
   *   The value to match. See is().
   *
   * @return \Xylemical\Parser\Token|null
   *   The optional token.
   */
  public function optional(string $type, mixed $value = NULL): ?Token {
    if ($this->is($type, $value)) {
      return $this->consume();
    }
    return NULL;
  }

  /**
   * Expect the next token to match one of the types.
   *
   * @param string[] $types
   *   The types.
   *
   * @return \Xylemical\Parser\Token|null
   *   The expected token.
   */
  public function optionalOneOf(array $types): ?Token {
    if ($this->isOneOf($types)) {
      return $this->consume();
    }
    return NULL;
  }

  /**
   * Push a token back to the beginning of the stream.
   *
   * @param \Xylemical\Parser\Token $token
   *   The token.
   *
   * @return $this
   */
  public function push(Token $token): static {
    array_unshift($this->tokens, $token);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function current(): ?Token {
    return $this->tokens[$this->pointer] ?? NULL;
  }

  /**
   * {@inheritdoc}
   */
  public function key(): int {
    return $this->pointer;
  }

  /**
   * {@inheritdoc}
   */
  public function valid() {
    return $this->pointer < count($this->tokens);
  }

  /**
   * {@inheritdoc}
   */
  public function rewind() {
    $this->pointer = 0;
  }

  /**
   * {@inheritdoc}
   */
  public function next(): void {
    if ($this->pointer < count($this->tokens)) {
      $this->pointer++;
    }
  }

  /**
   * {@inheritdoc}
   */
  public function count(): int {
    return \count($this->tokens);
  }

}
