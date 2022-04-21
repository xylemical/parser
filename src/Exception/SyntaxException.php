<?php

namespace Xylemical\Parser\Exception;

use Xylemical\Parser\Token;

/**
 * Triggers when the syntax is problematic.
 */
class SyntaxException extends \Exception {

  /**
   * The token.
   *
   * @var \Xylemical\Parser\Token|null
   */
  protected ?Token $token = NULL;

  /**
   * UnexpectedTokenException constructor.
   *
   * @param string $message
   *   The message.
   * @param \Xylemical\Parser\Token|null $token
   *   The token.
   * @param int $code
   *   The code.
   * @param \Throwable|null $previous
   *   The previous exception.
   */
  public function __construct(string $message = "", ?Token $token = NULL, int $code = 0, ?\Throwable $previous = NULL) {
    parent::__construct($message, $code, $previous);
    $this->token = $token;
  }

  /**
   * Get the token.
   *
   * @return \Xylemical\Parser\Token|null
   *   The token.
   */
  public function getToken(): ?Token {
    return $this->token;
  }

}
