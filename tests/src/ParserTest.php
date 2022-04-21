<?php

namespace Xylemical\Parser;

use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Xylemical\Parser\Exception\SyntaxException;

/**
 * Tests \Xylemical\Parser\Parser.
 */
class ParserTest extends TestCase {

  use ProphecyTrait;

  /**
   * Provides the test data for testParser().
   *
   * @return array[]
   *   The test data.
   */
  public function providerTestParser(): array {
    return [
      [
        "Full consume test.",
        ['word' => '\w+'],
        "test",
        ['word', 'test'],
        TRUE,
      ],
      [
        "Partial consume test.",
        ['word' => '\w+', 'ws' => '[ ]+'],
        "test test",
        [],
        FALSE,
      ],
    ];
  }

  /**
   * Test Parser.
   *
   * @dataProvider providerTestParser
   */
  public function testParser(string $name, array $patterns, string $input, array $expected, bool $success): void {
    $lexer = $this->prophesize(Lexer::class);
    // @phpstan-ignore-next-line
    $lexer->generate(Argument::any())->will(function ($args) {
      return $args[0]->consume();
    });

    $tokenizer = (new Tokenizer())->setPatterns($patterns);

    $exception = FALSE;
    try {
      $parser = new Parser($tokenizer, $lexer->reveal());
      $item = $parser->parse($input);
    }
    catch (SyntaxException $e) {
      $exception = TRUE;
    }

    $this->assertEquals($success, !$exception);
    if (!$success || empty($item)) {
      return;
    }

    $token = new Token($expected[0], $expected[1], 1, 1);
    $this->assertEquals($token, $item);
  }

  /**
   * Test sanity.
   */
  public function testSanity(): void {
    $tokenizer = new Tokenizer();
    $lexer = new Lexer();
    $parser = new Parser(new Tokenizer(), new Lexer());
    $this->assertFalse($tokenizer === $parser->getTokenizer());
    $this->assertFalse($lexer === $parser->getLexer());

    $parser->setTokenizer($tokenizer)->setLexer($lexer);
    $this->assertTrue($tokenizer === $parser->getTokenizer());
    $this->assertTrue($lexer === $parser->getLexer());
  }

}
