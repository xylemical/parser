<?php

declare(strict_types=1);

namespace Xylemical\Parser\Token;

use Xylemical\Parser\Token\TokenInterface;

/**
 * Provides the definition of a token stream.
 */
interface TokenStreamInterface extends \ArrayAccess, \Traversable, \Countable, \Stringable {

  /**
   * Token streams must provide a deep clone.
   */
  public function __clone();

  /**
   * Get the tokens.
   *
   * @return \Xylemical\Parser\Token\TokenInterface[]
   *   The tokens.
   */
  public function getTokens(): array;

  /**
   * Add tokens to the stream.
   *
   * @param \Xylemical\Parser\Token\TokenInterface[] $tokens
   *   The tokens.
   *
   * @return $this
   */
  public function addTokens(array $tokens): static;

  /**
   * Add a token to the stream.
   *
   * @param \Xylemical\Parser\Token\TokenInterface $token
   *   The token.
   *
   * @return $this
   */
  public function addToken(TokenInterface $token): static;

  /**
   * Check the next token.
   *
   * @return \Xylemical\Parser\Token\TokenInterface|null
   *   The token or NULL.
   */
  public function peek(): ?TokenInterface;

  /**
   * Consume the current token.
   *
   * @return \Xylemical\Parser\Token\TokenInterface|null
   *   The token.
   */
  public function consume(): ?TokenInterface;

  /**
   * Check the next in stream matches the regex.
   *
   * @param string $regex
   *   The regex.
   *
   * @return bool
   *   The result.
   */
  public function match(string $regex): bool;

  /**
   * Check the next in stream is type.
   *
   * @param string $type
   *   The type.
   * @param string|string[]|null $value
   *   When not NULL, matches the string, or one of the strings.
   *
   * @return bool
   *   The result.
   */
  public function is(string $type, mixed $value = NULL): bool;

  /**
   * Check the next token in stream is one of the types.
   *
   * @param string[] $types
   *   The token types.
   *
   * @return bool
   *   The result.
   */
  public function isOneOf(array $types): bool;

  /**
   * Expect the next token to match the type and value.
   *
   * @param string $type
   *   The token type.
   * @param string|string[]|null $value
   *   The value to match. See is().
   *
   * @return \Xylemical\Parser\Token\TokenInterface
   *   The expected token.
   *
   * @throws \Xylemical\Parser\Token\Exception\UnexpectedTokenException
   * @throws \Xylemical\Parser\Token\Exception\UnexpectedEndException
   */
  public function expect(string $type, mixed $value = NULL): TokenInterface;

  /**
   * Expect the next token to match one of the types.
   *
   * @param string[] $types
   *   The types.
   *
   * @return \Xylemical\Parser\Token\TokenInterface
   *   The expected token.
   *
   * @throws \Xylemical\Parser\Token\Exception\UnexpectedTokenException
   * @throws \Xylemical\Parser\Token\Exception\UnexpectedEndException
   */
  public function expectOneOf(array $types): TokenInterface;

  /**
   * Expect the next token to match the regex.
   *
   * @param string $regex
   *   The types.
   *
   * @return \Xylemical\Parser\Token\TokenInterface
   *   The expected token.
   *
   * @throws \Xylemical\Parser\Token\Exception\UnexpectedTokenException
   * @throws \Xylemical\Parser\Token\Exception\UnexpectedEndException
   */
  public function expectMatch(string $regex): TokenInterface;

  /**
   * Optionally get the next token to match the type and value.
   *
   * @param string $type
   *   The token type.
   * @param string|string[]|null $value
   *   The value to match. See is().
   *
   * @return \Xylemical\Parser\Token\TokenInterface|null
   *   The optional token.
   */
  public function optional(string $type, mixed $value = NULL): ?TokenInterface;

  /**
   * Expect the next token to match one of the types.
   *
   * @param string[] $types
   *   The types.
   *
   * @return \Xylemical\Parser\Token\TokenInterface|null
   *   The expected token.
   */
  public function optionalOneOf(array $types): ?TokenInterface;

  /**
   * Optionally get the next token to match the type and value.
   *
   * @param string $regex
   *   The regex.
   *
   * @return \Xylemical\Parser\Token\TokenInterface|null
   *   The optional token.
   */
  public function optionalMatch(string $regex): ?TokenInterface;

  /**
   * Push a token back to the beginning of the stream.
   *
   * @param \Xylemical\Parser\Token\TokenInterface $token
   *   The token.
   *
   * @return $this
   */
  public function push(TokenInterface $token): static;

  /**
   * Merges another token stream.
   *
   * @param \Xylemical\Token\TokenStreamInterface $stream
   *   The token stream.
   *
   * @return $this
   */
  public function merge(TokenStreamInterface $stream): static;

  /**
   * Clears the token stream.
   *
   * @return $this
   */
  public function clear(): static;

}
