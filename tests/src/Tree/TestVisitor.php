<?php

declare(strict_types=1);

namespace Xylemical\Parser\Tree;

use Xylemical\Parser\Tree\NodeInterface;
use Xylemical\Parser\Tree\VisitorInterface;
use Xylemical\Parser\Tree\VisitorOperation;

/**
 * Provides a test visitor.
 */
class TestVisitor implements VisitorInterface {

  /**
   * Tracks enters.
   *
   * @var array
   */
  public array $enters = [];

  /**
   * Tracks leaves.
   *
   * @var array
   */
  public array $leaves = [];

  /**
   * Allows for an entry condition result.
   *
   * @var array
   */
  public array $enterCondition = [];

  /**
   * Allows for an exit condition result.
   *
   * @var array
   */
  public array $leaveCondition = [];

  /**
   * Tests children behaviour.
   *
   * @var \Xylemical\Parser\Tree\NodeInterface|null
   */
  private ?NodeInterface $test = NULL;

  /**
   * Tests children behaviour.
   *
   * @var \Xylemical\Parser\Tree\NodeInterface|null
   */
  public ?NodeInterface $node = NULL;

  /**
   * {@inheritdoc}
   */
  public function enter(NodeInterface $node, int $sequence): ?VisitorOperation {
    $this->enters[] = [$node, $sequence];
    if ($this->enterCondition && $this->enterCondition[0] === $node) {
      return $this->enterCondition[1];
    }
    return NULL;
  }

  /**
   * {@inheritdoc}
   */
  public function leave(NodeInterface $node, int $sequence): ?VisitorOperation {
    $this->leaves[] = [$node, $sequence];
    if ($this->leaveCondition && $this->leaveCondition[0] === $node) {
      return $this->leaveCondition[1];
    }
    return NULL;
  }

  /**
   * Get the test node.
   *
   * @return \Xylemical\Parser\Tree\NodeInterface|null
   *   The test node.
   */
  public function getTest(): ?NodeInterface {
    return $this->test;
  }

  /**
   * Set the test node.
   *
   * @param \Xylemical\Parser\Tree\NodeInterface|null $node
   *   The node.
   *
   * @return $this
   */
  public function setTest(?NodeInterface $node): static {
    $this->test = $node;
    return $this;
  }

}
