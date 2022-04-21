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
    $this->assertTrue($token->is('type', 'token'));
    $this->assertTrue($token->is('type'));
    $this->assertFalse($token->is('type', 'bar'));
    $this->assertFalse($token->is('safe'));
    $this->assertFalse($token->match('type', '/^bar$/'));
    $this->assertTrue($token->match('type', '/^token$/'));
    $this->assertFalse($token->match('safe', '/^bar$/'));
    $this->assertFalse($token->match('safe', '/^token$/'));
  }

}
