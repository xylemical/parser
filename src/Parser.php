<?php

namespace Xylemical\Parser;

use Xylemical\Parser\Exception\IncompleteGrammarException;
use Xylemical\Parser\Token\TokenizerInterface;
use Xylemical\Parser\Tree\NodeInterface;

/**
 * Provides a generalized parser.
 */
class Parser {

  /**
   * The tokenizer.
   *
   * @var \Xylemical\Parser\Token\TokenizerInterface
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
   * @param \Xylemical\Parser\Token\TokenizerInterface $tokenizer
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
   * @return \Xylemical\Parser\Token\TokenizerInterface
   *   The tokenizer.
   */
  public function getTokenizer(): TokenizerInterface {
    return $this->tokenizer;
  }

  /**
   * Set the tokenizer.
   *
   * @param \Xylemical\Parser\Token\TokenizerInterface $tokenizer
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
   * @return \Xylemical\Parser\Tree\NodeInterface
   *   The generated abstract syntax tree.
   *
   * @throws \Xylemical\Parser\Exception\SyntaxException
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
