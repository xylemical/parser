<?php

declare(strict_types=1);

namespace Xylemical\Parser\Token;

/**
 * Provides the base token definition.
 */
interface TokenInterface extends \Stringable {

  /**
   * Get the type.
   *
   * @return string
   *   The type.
   */
  public function getType(): string;

  /**
   * Get the token value.
   *
   * @return string
   *   The token.
   */
  public function getValue(): string;

  /**
   * Check the token matches a type and optional value.
   *
   * @param string $type
   *   The type.
   * @param string $value
   *   The value.
   *
   * @return bool
   *   The result.
   */
  public function is(string $type, string $value = ''): bool;

  /**
   * Match the token based on value.
   *
   * @param string $regex
   *   The regex.
   *
   * @return bool
   *   The result.
   */
  public function match(string $regex): bool;

}
