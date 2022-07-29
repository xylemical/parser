<?php

namespace Xylemical\Parser\Token;

use Xylemical\Parser\Exception\UnexpectedEndException;
use Xylemical\Parser\Exception\UnexpectedSyntaxException;

/**
 * The token stream.
 */
class TokenStream implements TokenStreamInterface, \Iterator {

  /**
   * The token stream.
   *
   * @var \Xylemical\Parser\Token\TokenInterface[]
   */
  protected array $tokens = [];

  /**
   * The current iterative location.
   *
   * @var int
   */
  protected int $pointer = 0;

  /**
   * Stream constructor.
   *
   * @param \Xylemical\Parser\Token\TokenInterface[] $tokens
   *   The tokens.
   */
  public function __construct(array $tokens = []) {
    $this->addTokens($tokens);
  }

  /**
   * {@inheritdoc}
   */
  public function __clone() {
    foreach ($this->tokens as $index => $token) {
      $this->tokens[$index] = clone($token);
    }
  }

  /**
   * {@inheritdoc}
   */
  public function getTokens(): array {
    return $this->tokens;
  }

  /**
   * {@inheritdoc}
   */
  public function addTokens(array $tokens): static {
    foreach ($tokens as $token) {
      $this->addToken($token);
    }
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function addToken(TokenInterface $token): static {
    $this->tokens[] = $token;
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function peek(): ?TokenInterface {
    return $this->tokens[0] ?? NULL;
  }

  /**
   * {@inheritdoc}
   */
  public function consume(): ?TokenInterface {
    return array_shift($this->tokens);
  }

  /**
   * {@inheritdoc}
   */
  public function match(string $regex): bool {
    if (isset($this->tokens[0])) {
      return $this->tokens[0]->match($regex);
    }
    return FALSE;
  }

  /**
   * {@inheritdoc}
   */
  public function is(string $type, mixed $value = NULL): bool {
    if (isset($this->tokens[0])) {
      $token = $this->tokens[0];
      if ($token->getType() !== $type) {
        return FALSE;
      }

      return match (TRUE) {
        is_array($value) && !in_array($token->getValue(), $value),
          !is_null($value) && $token->getValue() !== $value => FALSE,
        default => TRUE
      };
    }
    return FALSE;
  }

  /**
   * {@inheritdoc}
   */
  public function isOneOf(array $types): bool {
    if (isset($this->tokens[0])) {
      return in_array($this->tokens[0]->getType(), $types);
    }
    return FALSE;
  }

  /**
   * {@inheritdoc}
   */
  public function expect(string $type, mixed $value = NULL): TokenInterface {
    if (!$this->is($type, $value)) {
      if ($token = $this->consume()) {
        throw new UnexpectedSyntaxException('Unexpected token.', $token);
      }
      throw new UnexpectedEndException();
    }

    return $this->consume();
  }

  /**
   * {@inheritdoc}
   */
  public function expectOneOf(array $types): TokenInterface {
    if (!$this->isOneOf($types)) {
      if ($token = $this->consume()) {
        throw new UnexpectedSyntaxException('Unexpected token.', $token);
      }
      throw new UnexpectedEndException();
    }
    return $this->consume();
  }

  /**
   * {@inheritdoc}
   */
  public function expectMatch(string $regex): TokenInterface {
    if (!$this->match($regex)) {
      if ($token = $this->consume()) {
        throw new UnexpectedSyntaxException('Unexpected token.', $token);
      }
      throw new UnexpectedEndException();
    }

    return $this->consume();
  }

  /**
   * {@inheritdoc}
   */
  public function optional(string $type, mixed $value = NULL): ?TokenInterface {
    if ($this->is($type, $value)) {
      return $this->consume();
    }
    return NULL;
  }

  /**
   * {@inheritdoc}
   */
  public function optionalOneOf(array $types): ?TokenInterface {
    if ($this->isOneOf($types)) {
      return $this->consume();
    }
    return NULL;
  }

  /**
   * {@inheritdoc}
   */
  public function optionalMatch(string $regex): ?TokenInterface {
    if ($this->match($regex)) {
      return $this->consume();
    }
    return NULL;
  }

  /**
   * {@inheritdoc}
   */
  public function push(TokenInterface $token): static {
    array_unshift($this->tokens, $token);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function current(): ?TokenInterface {
    return $this->tokens[$this->pointer] ?? NULL;
  }

  /**
   * {@inheritdoc}
   */
  public function key(): int {
    return $this->pointer;
  }

  /**
   * {@inheritdoc}
   */
  public function valid() {
    return $this->pointer < count($this->tokens);
  }

  /**
   * {@inheritdoc}
   */
  public function rewind() {
    $this->pointer = 0;
  }

  /**
   * {@inheritdoc}
   */
  public function next(): void {
    if ($this->pointer < count($this->tokens)) {
      $this->pointer++;
    }
  }

  /**
   * {@inheritdoc}
   */
  public function count(): int {
    return \count($this->tokens);
  }

  /**
   * {@inheritdoc}
   */
  public function offsetExists(mixed $offset) {
    return isset($this->tokens[$offset]);
  }

  /**
   * {@inheritdoc}
   */
  public function offsetGet(mixed $offset) {
    return $this->tokens[$offset] ?? NULL;
  }

  /**
   * {@inheritdoc}
   */
  public function offsetSet(mixed $offset, mixed $value) {
    // The token stream should not perform updates.
  }

  /**
   * {@inheritdoc}
   */
  public function offsetUnset(mixed $offset) {
    // The token stream should not perform deletions.
  }

  /**
   * {@inheritdoc}
   */
  public function __toString(): string {
    return implode('', $this->tokens);
  }

  /**
   * {@inheritdoc}
   */
  public function merge(TokenStreamInterface $stream): static {
    $this->tokens = array_merge($this->tokens, $stream->getTokens());
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function clear(): static {
    $this->tokens = [];
    return $this;
  }

}
