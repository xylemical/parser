<?php

namespace Xylemical\Parser;

use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Xylemical\Parser\Exception\SyntaxException;
use Xylemical\Parser\Token\Token;
use Xylemical\Parser\Token\Tokenizer;
use Xylemical\Parser\Tree\NodeInterface;

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
    $node = $this->prophesize(NodeInterface::class);
    $node = $node->reveal();

    $token = NULL;
    $lexer = $this->prophesize(LexerInterface::class);
    $lexer->generate(Argument::any())->will(function ($args) use ($node, &$token) {
      $token = $args[0]->consume();
      return $node;
    });

    $tokenizer = (new Tokenizer())->setPatterns($patterns);

    $exception = FALSE;
    try {
      $parser = new Parser($tokenizer, $lexer->reveal());
      /** @var \Xylemical\Parser\Tree\TestNode $item */
      $item = $parser->parse($input);
    }
    catch (SyntaxException $e) {
      $exception = TRUE;
    }

    $this->assertEquals($success, !$exception);
    if (!$success || empty($item)) {
      return;
    }

    $expected = new Token($expected[0], $expected[1], 1, 1);
    $this->assertEquals($expected, $token);
  }

  /**
   * Test sanity.
   */
  public function testSanity(): void {
    $tokenizer = new Tokenizer();
    $lexer = $this->getMockBuilder(LexerInterface::class)->getMock();
    $parser = new Parser(new Tokenizer(), new TestLexer());
    $this->assertFalse($tokenizer === $parser->getTokenizer());
    $this->assertFalse($lexer === $parser->getLexer());

    $parser->setTokenizer($tokenizer)->setLexer($lexer);
    $this->assertTrue($tokenizer === $parser->getTokenizer());
    $this->assertTrue($lexer === $parser->getLexer());
  }

}
