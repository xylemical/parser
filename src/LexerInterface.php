<?php

declare(strict_types=1);

namespace Xylemical\Parser;

use Xylemical\Ast\NodeInterface;
use Xylemical\Token\TokenStreamInterface;

/**
 * Provides the conversion of tokens into an abstract syntax tree.
 */
interface LexerInterface {

  /**
   * Generates content from the token stream.
   *
   * @param \Xylemical\Token\TokenStreamInterface $stream
   *   The stream.
   *
   * @return \Xylemical\Ast\NodeInterface
   *   The abstract syntax tree.
   *
   * @throws \Xylemical\Token\Exception\TokenException
   */
  public function generate(TokenStreamInterface $stream): NodeInterface;

}
