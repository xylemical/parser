<?php

namespace Xylemical\Parser\Token;

use PHPUnit\Framework\TestCase;
use Xylemical\Parser\Token\Exception\UnexpectedEndException;
use Xylemical\Parser\Token\Exception\UnexpectedTokenException;
use Xylemical\Parser\Token\Token;
use Xylemical\Parser\Token\TokenStream;

/**
 * Tests \Xylemical\Parser\Token\TokenStream.
 */
class TokenStreamTest extends TestCase {

  /**
   * Test sanity.
   */
  public function testSanity(): void {
    $stream = new TokenStream();
    $this->assertEquals('', (string) $stream);
    $this->assertEquals(0, $stream->count());
    $this->assertNull($stream->peek());
    $this->assertFalse($stream->is('type'));
    $this->assertFalse($stream->is('type', 'value'));
    $this->assertFalse($stream->isOneOf(['type', 'safe']));
    $this->assertEquals([], iterator_to_array($stream));
    $this->assertEquals([], $stream->getTokens());
    $this->assertFalse($stream->match('/^test$/'));
    $this->assertFalse($stream->match('/^safe$/'));
    $this->assertFalse(isset($stream[0]));

    $token = new Token('type', 'safe', 1, 1);
    $stream->addToken($token);
    $this->assertEquals('safe', (string) $stream);
    $this->assertEquals(1, $stream->count());
    $this->assertEquals($token, $stream->peek());
    $this->assertFalse($stream->is('safe'));
    $this->assertTrue($stream->is('type'));
    $this->assertTrue($stream->is('type', 'safe'));
    $this->assertFalse($stream->isOneOf(['safe']));
    $this->assertEquals([$token], iterator_to_array($stream));
    $this->assertEquals([$token], $stream->getTokens());
    $this->assertTrue(isset($stream[0]));

    $another = new Token('safe', 'type', 1, 2);
    $stream->addTokens([$another]);
    $this->assertEquals('safetype', (string) $stream);
    $this->assertEquals(2, $stream->count());
    $this->assertEquals($token, $stream->peek());
    $this->assertTrue($stream->is('type'));
    $this->assertTrue($stream->is('type', 'safe'));
    $this->assertFalse($stream->match('/^test$/'));
    $this->assertTrue($stream->match('/^safe$/'));
    $this->assertFalse($stream->isOneOf(['safe']));
    $this->assertEquals([$token, $another], iterator_to_array($stream));
    $this->assertEquals([$token, $another], $stream->getTokens());

    $result = $stream->consume();
    $this->assertEquals($token, $result);

    $stream->push($token);
    $stream->push($another);
    $this->assertEquals([$another, $token, $another], $stream->getTokens());

    $stream->merge(new TokenStream([$another, $token]));
    $this->assertEquals([
      $another,
      $token,
      $another,
      $another,
      $token,
    ], $stream->getTokens());

    $stream->clear();
    $this->assertEquals([], $stream->getTokens());
  }

  /**
   * Test deep clone.
   */
  public function testClone(): void {
    $a = new Token('a', 'a');
    $b = new Token('b', 'b');
    $stream = new TokenStream([$a, $b]);
    $clone = clone $stream;
    $this->assertEquals(count($stream), count($clone));
    foreach ($stream as $index => $item) {
      $this->assertNotSame($item, $clone[$index]);
    }
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
   * Tests an expectMatch with expected and unexpected tokens.
   */
  public function testExpectMatch(): void {
    $token = new Token('test', 'value', 1, 1);
    $stream = new TokenStream([$token]);
    $this->assertEquals($token, $stream->expectMatch('/^value$/'));

    $stream->addToken($token);
    $this->expectException(UnexpectedTokenException::class);
    $stream->expectMatch('/^type$/');
  }

  /**
   * Tests an expectMatch without any tokens.
   */
  public function testExpectMatchEndOfStream(): void {
    $stream = new TokenStream();
    $this->expectException(UnexpectedEndException::class);
    $stream->expectMatch('/^value$/');
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

  /**
   * Tests an optionalMatch with tokens.
   */
  public function testOptionalMatch(): void {
    $token = new Token('test', 'value', 1, 1);
    $stream = new TokenStream([$token]);
    $this->assertEquals($token, $stream->optionalMatch('/^value$/'));

    $stream->addToken($token);
    $this->assertNull($stream->optionalMatch('/^type$/'));
    $this->assertEquals(1, count($stream));
  }

}
