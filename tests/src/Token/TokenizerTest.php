<?php

namespace Xylemical\Parser\Token;

use PHPUnit\Framework\TestCase;
use Xylemical\Parser\Token\Exception\TokenException;
use Xylemical\Parser\Token\Token;
use Xylemical\Parser\Token\Tokenizer;

/**
 * Tests \Xylemical\Parser\Token\Tokenizer.
 */
class TokenizerTest extends TestCase {

  /**
   * Provides the test for tokenization.
   *
   * @return array
   *   The test data.
   */
  public function providerTestParse(): array {
    return json_decode(file_get_contents(__DIR__ . '/../../fixtures/token-test.json'), TRUE);
  }

  /**
   * Tests the parsing functionality.
   *
   * @dataProvider providerTestParse
   */
  public function testParse(string $name, string $input, array $stream, array $options = []): void {
    $tokenizer = new Tokenizer();
    $tokenizer->setPatterns($options['patterns'] ?? []);
    foreach ($options['refinements'] ?? [] as $token => $patterns) {
      $tokenizer->setRefinements($token, $patterns);
    }

    $exception = FALSE;
    try {
      $tokenStream = $tokenizer->tokenize($input);
    }
    catch (TokenException $e) {
      $exception = TRUE;
    }

    $this->assertEquals($options['syntax'] ?? FALSE, $exception, "{$name} syntax exception");
    if (($options['syntax'] ?? NULL) || empty($tokenStream)) {
      return;
    }

    foreach ($stream as $key => $value) {
      $token = new Token($value[0], $value[1], $value[2], $value[3]);
      $this->assertEquals($token, $tokenStream->consume(), "{$name} token {$key} => {$value[1]}.");
    }

    $this->assertEquals(0, $tokenStream->count(), "{$name} end of stream.");
  }

  /**
   * Test the sanity.
   */
  public function testSanity(): void {
    $tokenizer = new Tokenizer();
    $this->assertEquals([], $tokenizer->getPatterns());
    $this->assertEquals('', $tokenizer->getPattern('token'));
    $this->assertEquals([], $tokenizer->getRefinements());
    $this->assertEquals([], $tokenizer->getRefinements('token'));
    $this->assertEquals('', $tokenizer->getRefinement('token', 'word'));

    $tokenizer->setPattern('token', '\d+');
    $this->assertEquals(['token' => '\d+'], $tokenizer->getPatterns());
    $this->assertEquals('\d+', $tokenizer->getPattern('token'));

    $tokenizer->setPatterns(['abc' => '\d+']);
    $this->assertEquals(['token' => '\d+', 'abc' => '\d+'], $tokenizer->getPatterns());

    $tokenizer->resetPattern('token');
    $this->assertEquals(['abc' => '\d+'], $tokenizer->getPatterns());

    $tokenizer->resetPattern();
    $this->assertEquals([], $tokenizer->getPatterns());

    $tokenizer->setRefinement('token', 'word', '\w+');
    $this->assertEquals(['token' => ['word' => '\w+']], $tokenizer->getRefinements());
    $this->assertEquals(['word' => '\w+'], $tokenizer->getRefinements('token'));
    $this->assertEquals('\w+', $tokenizer->getRefinement('token', 'word'));

    $tokenizer->setRefinement('token', 'digit', '\d+');
    $tokenizer->resetRefinement('token', 'word');
    $this->assertEquals(['digit' => '\d+'], $tokenizer->getRefinements('token'));
    $tokenizer->resetRefinement('token');
    $this->assertEquals([], $tokenizer->getRefinements('token'));
  }

  /**
   * Test the overrides from subclasses.
   */
  public function testOverrides(): void {
    $tokenizer = new TestTokenizer();
    $this->assertEquals(['word' => '\w+'], $tokenizer->getPatterns());
    $this->assertEquals(['word' => ['digit' => '\d+']], $tokenizer->getRefinements());

    $tokenizer->setPattern('word', '\d+');
    $tokenizer->setRefinement('word', 'digit', '\w+');
    $this->assertEquals(['word' => '\d+'], $tokenizer->getPatterns());
    $this->assertEquals(['word' => ['digit' => '\w+']], $tokenizer->getRefinements());

    $tokenizer->resetPattern('word');
    $this->assertEquals(['word' => '\w+'], $tokenizer->getPatterns());
    $tokenizer->setPattern('digit', '\d+');
    $this->assertEquals(['word' => '\w+', 'digit' => '\d+'], $tokenizer->getPatterns());

    $tokenizer->resetPattern();
    $this->assertEquals(['word' => '\w+'], $tokenizer->getPatterns());

    $tokenizer->setRefinement('word', 'word', '\w');
    $this->assertEquals(['word' => ['digit' => '\w+', 'word' => '\w']], $tokenizer->getRefinements());

    $tokenizer->resetRefinement('word', 'word');
    $this->assertEquals(['word' => ['digit' => '\w+']], $tokenizer->getRefinements());
    $tokenizer->resetRefinement('word');
    $this->assertEquals(['word' => ['digit' => '\d+']], $tokenizer->getRefinements());

    $tokenizer->setRefinement('token', 'token', 'token');
    $this->assertEquals([
      'word' => ['digit' => '\d+'],
      'token' => ['token' => 'token'],
    ], $tokenizer->getRefinements());

    $tokenizer->resetRefinement();
    $this->assertEquals(['word' => ['digit' => '\d+']], $tokenizer->getRefinements());
  }

}

/**
 * A test tokenizer with overridden constants.
 */
class TestTokenizer extends Tokenizer {
  protected const PATTERNS = [
    'word' => '\w+',
  ];

  protected const REFINEMENTS = [
    'word' => [
      'digit' => '\d+',
    ],
  ];

}
