<?php

declare(strict_types=1);

namespace Xylemical\Parser\Token;

use Xylemical\Parser\Token\LocatableTokenInterface;

/**
 * Provides for a token coming from a known source.
 */
interface IdentifiableTokenInterface extends LocatableTokenInterface {

  /**
   * Get the filename.
   *
   * @return string
   *   The filename.
   */
  public function getFile(): string;

}
