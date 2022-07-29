<?php

namespace Xylemical\Parser\Token\Exception;

use PHPUnit\Framework\TestCase;
use Xylemical\Parser\Exception\SyntaxException;
use Xylemical\Parser\Token\Token;

/**
 * Tests \Xylemical\Parser\Token\Exception\TokenException.
 */
class TokenExceptionTest extends TestCase {

  /**
   * Test the sanity of the exception.
   */
  public function testSanity(): void {
    $token = new Token('test', 'test', 2, 2);
    $exception = new SyntaxException('message', $token);
    $this->assertEquals($token, $exception->getToken());
  }

}
