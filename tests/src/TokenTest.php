<?php

namespace Xylemical\Parser;

use PHPUnit\Framework\TestCase;

/**
 * Tests \Xylemical\Parser\Token.
 */
class TokenTest extends TestCase {

  /**
   * Test sanity.
   */
  public function testSanity(): void {
    $token = new Token('type', 'token', 2, 4);
    $this->assertEquals('type', $token->getType());
    $this->assertEquals('token', $token->getToken());
    $this->assertEquals(2, $token->getLine());
    $this->assertEquals(4, $token->getColumn());
  }

}
