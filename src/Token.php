<?php

namespace Xylemical\Parser;

/**
 * The token.
 */
class Token {

  /**
   * The type of the token.
   *
   * @var string
   */
  protected string $type;

  /**
   * The token value.
   *
   * @var string
   */
  protected string $token;

  /**
   * The line number.
   *
   * @var int
   */
  protected int $line;

  /**
   * The column position.
   *
   * @var int
   */
  protected int $column;

  /**
   * Token constructor.
   *
   * @param string $type
   *   The type.
   * @param string $token
   *   The token.
   * @param int $line
   *   The line.
   * @param int $column
   *   The column.
   */
  public function __construct(string $type, string $token, int $line, int $column) {
    $this->type = $type;
    $this->token = $token;
    $this->line = $line;
    $this->column = $column;
  }

  /**
   * Get the type.
   *
   * @return string
   *   The type.
   */
  public function getType(): string {
    return $this->type;
  }

  /**
   * Get the token.
   *
   * @return string
   *   The token.
   */
  public function getToken(): string {
    return $this->token;
  }

  /**
   * Get the line number.
   *
   * @return int
   *   The line.
   */
  public function getLine(): int {
    return $this->line;
  }

  /**
   * Get the column number.
   *
   * @return int
   *   The column.
   */
  public function getColumn(): int {
    return $this->column;
  }

}
