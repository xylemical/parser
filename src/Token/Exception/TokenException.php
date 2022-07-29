<?php

declare(strict_types=1);

namespace Xylemical\Parser\Token\Exception;

use Xylemical\Parser\Token\TokenInterface;

/**
 * Triggers with a token exception.
 */
class TokenException extends \Exception {

  /**
   * The token discovered.
   *
   * @var \Xylemical\Parser\Token\TokenInterface|null
   */
  protected ?TokenInterface $token;

  /**
   * UnexpectedTokenException constructor.
   *
   * @param string $message
   *   The message.
   * @param \Xylemical\Parser\Token\TokenInterface|null $token
   *   The token.
   * @param int $code
   *   The code.
   * @param \Throwable|null $previous
   *   The previous exception.
   */
  public function __construct(string $message = "", ?TokenInterface $token = NULL, int $code = 0, ?\Throwable $previous = NULL) {
    parent::__construct($message, $code, $previous);
    $this->token = $token;
  }

  /**
   * Get the token.
   *
   * @return \Xylemical\Parser\Token\TokenInterface|null
   *   The token.
   */
  public function getToken(): ?TokenInterface {
    return $this->token;
  }

}
