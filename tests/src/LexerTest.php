<?php

namespace Xylemical\Parser;

use PHPUnit\Framework\TestCase;

/**
 * Tests \Xylemical\Parser\Lexer.
 */
class LexerTest extends TestCase {

  /**
   * Tests something that doesn't need testing.
   */
  public function testSanity(): void {
    $stream = new TokenStream();
    $lexer = new Lexer();
    $this->assertNull($lexer->generate($stream));
  }

}
