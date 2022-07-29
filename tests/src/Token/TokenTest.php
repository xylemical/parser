<?php

namespace Xylemical\Parser\Token;

use PHPUnit\Framework\TestCase;

/**
 * Tests \Xylemical\Parser\Token\Token.
 */
class TokenTest extends TestCase {

  /**
   * Test sanity.
   */
  public function testSanity(): void {
    $token = new Token('type', 'token', 2, 4);
    $this->assertEquals('type', $token->getType());
    $this->assertEquals('token', $token->getValue());
    $this->assertEquals('token', (string) $token);
    $this->assertEquals('', $token->getFile());
    $this->assertEquals(2, $token->getLine());
    $this->assertEquals(4, $token->getColumn());
    $this->assertTrue($token->is('type', 'token'));
    $this->assertTrue($token->is('type'));
    $this->assertFalse($token->is('type', 'bar'));
    $this->assertFalse($token->is('safe'));
    $this->assertFalse($token->match('/^bar$/'));
    $this->assertTrue($token->match('/^token$/'));

    $token->setType('safe')
      ->setValue('value')
      ->setLine(3)
      ->setColumn(5)
      ->setFile('test.php');
    $this->assertEquals('safe', $token->getType());
    $this->assertEquals('value', $token->getValue());
    $this->assertEquals('test.php', $token->getFile());
    $this->assertEquals(3, $token->getLine());
    $this->assertEquals(5, $token->getColumn());

    $source = (new Token('type', 'token', 2, 4))
      ->setFile('value.php');
    $token->copy($source);
    $this->assertEquals('type', $token->getType());
    $this->assertEquals('token', $token->getValue());
    $this->assertEquals('value.php', $token->getFile());
    $this->assertEquals(2, $token->getLine());
    $this->assertEquals(4, $token->getColumn());
  }

}
