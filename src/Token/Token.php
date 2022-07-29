<?php

namespace Xylemical\Parser\Token;

/**
 * Provides a generic token.
 */
class Token implements IdentifiableTokenInterface {

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
  protected string $value;

  /**
   * The filename.
   *
   * @var string
   */
  protected string $filename = '';

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
   * @param string $value
   *   The token value.
   * @param int $line
   *   The line.
   * @param int $column
   *   The column.
   */
  public function __construct(string $type, string $value, int $line = 0, int $column = 0) {
    $this->type = $type;
    $this->value = $value;
    $this->line = $line;
    $this->column = $column;
  }

  /**
   * {@inheritdoc}
   */
  public function getType(): string {
    return $this->type;
  }

  /**
   * Set the type.
   *
   * @param string $type
   *   The type.
   *
   * @return $this
   */
  public function setType(string $type): static {
    $this->type = $type;
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getValue(): string {
    return $this->value;
  }

  /**
   * Set the value.
   *
   * @param string $value
   *   The value.
   *
   * @return $this
   */
  public function setValue(string $value): static {
    $this->value = $value;
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getFile(): string {
    return $this->filename;
  }

  /**
   * Set the filename.
   *
   * @param string $filename
   *   The filename.
   *
   * @return $this
   */
  public function setFile(string $filename): static {
    $this->filename = $filename;
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getLine(): int {
    return $this->line;
  }

  /**
   * Set the line.
   *
   * @param int $line
   *   The line.
   *
   * @return $this
   */
  public function setLine(int $line): static {
    $this->line = $line;
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getColumn(): int {
    return $this->column;
  }

  /**
   * Set the column.
   *
   * @param int $column
   *   The column.
   *
   * @return $this
   */
  public function setColumn(int $column): static {
    $this->column = $column;
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function is(string $type, string $value = ''): bool {
    if ($this->type === $type) {
      if (!$value || $this->value === $value) {
        return TRUE;
      }
    }
    return FALSE;
  }

  /**
   * {@inheritdoc}
   */
  public function match(string $regex): bool {
    return preg_match($regex, $this->value);
  }

  /**
   * Copy a token value.
   *
   * @param \Xylemical\Parser\Token\TokenInterface $token
   *   The token.
   *
   * @return $this
   */
  public function copy(TokenInterface $token): static {
    $this->type = $token->getType();
    $this->value = $token->getValue();

    if ($token instanceof LocatableTokenInterface) {
      $this->line = $token->getLine();
      $this->column = $token->getColumn();

      if ($token instanceof IdentifiableTokenInterface) {
        $this->filename = $token->getFile();
      }
    }

    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function __toString(): string {
    return $this->value;
  }

}
