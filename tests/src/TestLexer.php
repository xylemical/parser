<?php

declare(strict_types=1);

namespace Xylemical\Parser;

use Xylemical\Parser\Tree\NodeInterface;
use Xylemical\Parser\Token\TokenStreamInterface;

/**
 * Provides a test lexer.
 */
class TestLexer implements LexerInterface {

  /**
   * {@inheritdoc}
   */
  public function generate(TokenStreamInterface $stream): NodeInterface {
    throw new \Exception('Just a test lexer');
  }

}
