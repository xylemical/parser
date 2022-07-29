<?php

declare(strict_types=1);

namespace Xylemical\Parser\Tree;

use Xylemical\Parser\Tree\ChildrenInterface;
use Xylemical\Parser\Tree\NodeInterface;
use Xylemical\Token\Token;

/**
 * Provides a test node.
 */
class TestNode implements NodeInterface {

  /**
   * The test token.
   *
   * @var \Xylemical\Token\Token|null
   */
  public ?Token $token;

  /**
   * TestNode constructor.
   *
   * @param \Xylemical\Token\Token|null $token
   *   The token.
   */
  public function __construct(?Token $token = NULL) {
    $this->token = $token;
  }

  /**
   * {@inheritdoc}
   */
  public function getChildren(): ?ChildrenInterface {
    return NULL;
  }

}
