<?php

declare(strict_types=1);

namespace Xylemical\Parser\Token;

/**
 * Provides location information for the token.
 */
interface LocatableTokenInterface extends TokenInterface {

  /**
   * Get the line number.
   *
   * @return int
   *   The line.
   */
  public function getLine(): int;

  /**
   * Get the column number.
   *
   * @return int
   *   The column.
   */
  public function getColumn(): int;

}
