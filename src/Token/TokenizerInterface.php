<?php

declare(strict_types=1);

namespace Xylemical\Parser\Token;

/**
 * Provides tokenization behaviours.
 */
interface TokenizerInterface {

  /**
   * Tokenizes the input string.
   *
   * @param string $input
   *   The input to parse.
   *
   * @return \Xylemical\Parser\Token\TokenStreamInterface
   *   The token stream.
   *
   * @throws \Xylemical\Parser\Exception\SyntaxException
   */
  public function tokenize(string $input): TokenStreamInterface;

}
