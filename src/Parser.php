<?php

namespace Xylemical\Parser;

use Xylemical\Ast\NodeInterface;
use Xylemical\Parser\Exception\IncompleteGrammarException;
use Xylemical\Token\TokenizerInterface;

/**
 * Provides a generalized parser.
 */
class Parser {

  /**
   * The tokenizer.
   *
   * @var \Xylemical\Token\TokenizerInterface
   */
  protected TokenizerInterface $tokenizer;

  /**
   * The lexer.
   *
   * @var \Xylemical\Parser\LexerInterface
   */
  protected LexerInterface $lexer;

  /**
   * Parser constructor.
   *
   * @param \Xylemical\Token\TokenizerInterface $tokenizer
   *   The tokenizer.
   * @param \Xylemical\Parser\LexerInterface $lexer
   *   The lexer.
   */
  public function __construct(TokenizerInterface $tokenizer, LexerInterface $lexer) {
    $this->tokenizer = $tokenizer;
    $this->lexer = $lexer;
  }

  /**
   * Get the tokenizer.
   *
   * @return \Xylemical\Token\TokenizerInterface
   *   The tokenizer.
   */
  public function getTokenizer(): TokenizerInterface {
    return $this->tokenizer;
  }

  /**
   * Set the tokenizer.
   *
   * @param \Xylemical\Token\TokenizerInterface $tokenizer
   *   The tokenizer.
   *
   * @return $this
   */
  public function setTokenizer(TokenizerInterface $tokenizer): static {
    $this->tokenizer = $tokenizer;
    return $this;
  }

  /**
   * Get the lexer.
   *
   * @return \Xylemical\Parser\LexerInterface
   *   The lexer.
   */
  public function getLexer(): LexerInterface {
    return $this->lexer;
  }

  /**
   * Set the lexer.
   *
   * @param \Xylemical\Parser\LexerInterface $lexer
   *   The lexer.
   *
   * @return $this
   */
  public function setLexer(LexerInterface $lexer): static {
    $this->lexer = $lexer;
    return $this;
  }

  /**
   * Parse input and generate an abstract syntax tree.
   *
   * @param string $input
   *   The input.
   *
   * @return \Xylemical\Ast\NodeInterface
   *   The generated abstract syntax tree.
   *
   * @throws \Xylemical\Token\Exception\TokenException
   */
  public function parse(string $input): NodeInterface {
    $stream = $this->tokenizer->tokenize($input);
    $result = $this->lexer->generate($stream);
    if (count($stream)) {
      throw new IncompleteGrammarException('There are still tokens to be processed.', $stream->peek());
    }
    return $result;
  }

}
