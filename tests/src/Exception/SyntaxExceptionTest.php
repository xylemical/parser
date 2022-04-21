<?php

namespace Xylemical\Parser\Exception;

use PHPUnit\Framework\TestCase;
use Xylemical\Parser\Token;

/**
 * Tests \Xylemical\Parser\Exception\SyntaxException.
 */
class SyntaxExceptionTest extends TestCase {

  /**
   * Test the sanity of the exception.
   */
  public function testSanity(): void {
    $token = new Token('test', 'test', 2, 2);
    $exception = new SyntaxException('message', $token);
    $this->assertEquals($token, $exception->getToken());
  }

}
