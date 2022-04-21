<?php

namespace Xylemical\Parser;

/**
 * Provides the means to convert the token stream into something meaningful.
 */
class Lexer {

  /**
   * Generates content from the token stream.
   *
   * @param \Xylemical\Parser\TokenStream $stream
   *   The stream.
   *
   * @return mixed
   *   The content.
   *
   * @throws \Xylemical\Parser\Exception\SyntaxException
   */
  public function generate(TokenStream $stream): mixed {
    return NULL;
  }

}
