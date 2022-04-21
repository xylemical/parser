<?php

namespace Xylemical\Parser;

use PHPUnit\Framework\TestCase;
use Xylemical\Parser\Exception\UnexpectedEndException;
use Xylemical\Parser\Exception\UnexpectedTokenException;

/**
 * Tests \Xylemical\Parser\TokenStream.
 */
class TokenStreamTest extends TestCase {

  /**
   * Test sanity.
   */
  public function testSanity(): void {
    $stream = new TokenStream();
    $this->assertEquals(0, $stream->count());
    $this->assertNull($stream->peek());
    $this->assertFalse($stream->is('type'));
    $this->assertFalse($stream->is('type', 'value'));
    $this->assertFalse($stream->isOneOf(['type', 'safe']));
    $this->assertEquals([], iterator_to_array($stream));
    $this->assertEquals([], $stream->getTokens());

    $token = new Token('type', 'safe', 1, 1);
    $stream->addToken($token);
    $this->assertEquals(1, $stream->count());
    $this->assertEquals($token, $stream->peek());
    $this->assertFalse($stream->is('safe'));
    $this->assertTrue($stream->is('type'));
    $this->assertTrue($stream->is('type', 'safe'));
    $this->assertFalse($stream->isOneOf(['safe']));
    $this->assertEquals([$token], iterator_to_array($stream));
    $this->assertEquals([$token], $stream->getTokens());

    $another = new Token('safe', 'type', 1, 2);
    $stream->addTokens([$another]);
    $this->assertEquals(2, $stream->count());
    $this->assertEquals($token, $stream->peek());
    $this->assertTrue($stream->is('type'));
    $this->assertTrue($stream->is('type', 'safe'));
    $this->assertFalse($stream->isOneOf(['safe']));
    $this->assertEquals([$token, $another], iterator_to_array($stream));
    $this->assertEquals([$token, $another], $stream->getTokens());

    $result = $stream->consume();
    $this->assertEquals($token, $result);

    $stream->push($token);
    $stream->push($another);
    $this->assertEquals([$another, $token, $another], $stream->getTokens());
  }

  /**
   * Tests an expect with expected and unexpected tokens.
   */
  public function testExpect(): void {
    $token = new Token('test', 'value', 1, 1);
    $stream = new TokenStream([$token]);
    $this->assertEquals($token, $stream->expect('test'));

    $stream->addToken($token);
    $this->expectException(UnexpectedTokenException::class);
    $stream->expect('foo');
  }

  /**
   * Tests an expect without any tokens.
   */
  public function testExpectEndOfStream(): void {
    $stream = new TokenStream();
    $this->expectException(UnexpectedEndException::class);
    $stream->expect('foo');
  }

  /**
   * Tests an expectOneOf with expected and unexpected tokens.
   */
  public function testExpectOneOf(): void {
    $token = new Token('test', 'value', 1, 1);
    $stream = new TokenStream([$token]);
    $this->assertEquals($token, $stream->expectOneOf(['safe', 'test']));

    $stream->addToken($token);
    $this->expectException(UnexpectedTokenException::class);
    $stream->expectOneOf(['foo', 'bar']);
  }

  /**
   * Tests an expectOneOf without any tokens.
   */
  public function testExpectOneOfEndOfStream(): void {
    $stream = new TokenStream();
    $this->expectException(UnexpectedEndException::class);
    $stream->expectOneOf(['foo', 'bar']);
  }

  /**
   * Tests an optional with tokens.
   */
  public function testOptional(): void {
    $token = new Token('test', 'value', 1, 1);
    $stream = new TokenStream([$token]);
    $this->assertEquals($token, $stream->optional('test'));
    $this->assertEquals([], $stream->getTokens());

    $stream->addToken($token);
    $this->assertNull($stream->optional('foo'));
    $this->assertEquals(1, count($stream));
  }

  /**
   * Tests an optionalOneOf with tokens.
   */
  public function testOptionalOneOf(): void {
    $token = new Token('test', 'value', 1, 1);
    $stream = new TokenStream([$token]);
    $this->assertEquals($token, $stream->optionalOneOf(['safe', 'test']));

    $stream->addToken($token);
    $this->assertNull($stream->optionalOneOf(['foo', 'bar']));
    $this->assertEquals(1, count($stream));
  }

}
