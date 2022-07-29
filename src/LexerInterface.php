<?php

declare(strict_types=1);

namespace Xylemical\Parser;

use Xylemical\Parser\Token\TokenStreamInterface;
use Xylemical\Parser\Tree\NodeInterface;

/**
 * Provides the conversion of tokens into an abstract syntax tree.
 */
interface LexerInterface {

  /**
   * Generates content from the token stream.
   *
   * @param \Xylemical\Parser\Token\TokenStreamInterface $stream
   *   The stream.
   *
   * @return \Xylemical\Parser\Tree\NodeInterface
   *   The abstract syntax tree.
   *
   * @throws \Xylemical\Parser\Exception\SyntaxException
   */
  public function generate(TokenStreamInterface $stream): NodeInterface;

}
