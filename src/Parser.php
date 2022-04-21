<?php

namespace Xylemical\Parser;

use Xylemical\Parser\Exception\IncompleteGrammarException;

/**
 * General parser..
 */
class Parser {

  /**
   * The tokenizer.
   *
   * @var \Xylemical\Parser\Tokenizer
   */
  protected Tokenizer $tokenizer;

  /**
   * The lexer.
   *
   * @var \Xylemical\Parser\Lexer
   */
  protected Lexer $lexer;

  /**
   * Parser constructor.
   *
   * @param \Xylemical\Parser\Tokenizer $tokenizer
   *   The tokenizer.
   * @param \Xylemical\Parser\Lexer $lexer
   *   The lexer.
   */
  public function __construct(Tokenizer $tokenizer, Lexer $lexer) {
    $this->tokenizer = $tokenizer;
    $this->lexer = $lexer;
  }

  /**
   * Get the tokenizer.
   *
   * @return \Xylemical\Parser\Tokenizer
   *   The tokenizer.
   */
  public function getTokenizer(): Tokenizer {
    return $this->tokenizer;
  }

  /**
   * Set the tokenizer.
   *
   * @param \Xylemical\Parser\Tokenizer $tokenizer
   *   The tokenizer.
   *
   * @return $this
   */
  public function setTokenizer(Tokenizer $tokenizer): static {
    $this->tokenizer = $tokenizer;
    return $this;
  }

  /**
   * Get the lexer.
   *
   * @return \Xylemical\Parser\Lexer
   *   The lexer.
   */
  public function getLexer(): Lexer {
    return $this->lexer;
  }

  /**
   * Set the lexer.
   *
   * @param \Xylemical\Parser\Lexer $lexer
   *   The lexer.
   *
   * @return $this
   */
  public function setLexer(Lexer $lexer): static {
    $this->lexer = $lexer;
    return $this;
  }

  /**
   * Parse input and generate content.
   *
   * @param string $input
   *   The input.
   *
   * @return mixed
   *   The generated content.
   *
   * @throws \Xylemical\Parser\Exception\SyntaxException
   */
  public function parse(string $input): mixed {
    $stream = $this->tokenizer->tokenize($input);
    $result = $this->lexer->generate($stream);
    if (count($stream)) {
      throw new IncompleteGrammarException('There are still tokens to be processed.', $stream->peek());
    }
    return $result;
  }

}
