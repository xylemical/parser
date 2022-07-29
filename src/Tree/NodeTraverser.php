<?php

declare(strict_types=1);

namespace Xylemical\Parser\Tree;

use Xylemical\Parser\Tree\ChildrenInterface;
use Xylemical\Parser\Tree\Exception\TraversalException;
use Xylemical\Parser\Tree\NodeInterface;
use Xylemical\Parser\Tree\VisitorInterface;
use Xylemical\Parser\Tree\VisitorOperation;
use function gettype;
use function is_null;

/**
 * Provides generic node traversal.
 */
class NodeTraverser {

  /**
   * The visitors.
   *
   * @var \Xylemical\Parser\Tree\VisitorInterface[]
   */
  protected array $visitors = [];

  /**
   * NodeTraverser constructor.
   *
   * @param \Xylemical\Parser\Tree\VisitorInterface[] $visitors
   *   The visitors.
   */
  public function __construct(array $visitors = []) {
    foreach ($visitors as $visitor) {
      $this->addVisitor($visitor);
    }
  }

  /**
   * Traverse the node tree.
   *
   * @param \Xylemical\Parser\Tree\NodeInterface $node
   *   The node.
   *
   * @return \Xylemical\Parser\Tree\NodeInterface
   *   The resulting node.
   *
   * @throws \Xylemical\Parser\Exception\TraversalException
   */
  public function traverse(NodeInterface $node): NodeInterface {
    $result = $this->traverseNode($node, 0);
    return $result?->getReplacement() ?: $node;
  }

  /**
   * Get the visitors.
   *
   * @return \Xylemical\Parser\Tree\VisitorInterface[]
   *   The visitors.
   */
  public function getVisitors(): array {
    return $this->visitors;
  }

  /**
   * Add a visitor.
   *
   * @param \Xylemical\Parser\Tree\VisitorInterface $visitor
   *   The visitor.
   *
   * @return $this
   */
  public function addVisitor(VisitorInterface $visitor): static {
    $this->visitors[] = $visitor;
    return $this;
  }

  /**
   * Perform enter() operation on node.
   *
   * @param \Xylemical\Parser\Tree\NodeInterface $node
   *   The node.
   * @param int $sequence
   *   The position.
   * @param int $completed
   *   The visitor index completed.
   *
   * @return \Xylemical\Parser\Tree\VisitorOperation|null
   *   The visitor operation or NULL.
   */
  protected function enter(NodeInterface $node, int $sequence, int &$completed): ?VisitorOperation {
    $return = NULL;
    $target = $node;
    foreach ($this->visitors as $index => $visitor) {
      $completed = $index;
      if (is_null($result = $visitor->enter($target, $sequence))) {
        continue;
      }

      $return = $result;
      $target = $result->getReplacement() ?: $target;
      if ($result->isStop() || $result->skipsSelf()) {
        return $result;
      }
    }

    return $return;
  }

  /**
   * Perform leave() operation on node.
   *
   * @param \Xylemical\Parser\Tree\NodeInterface $node
   *   The node.
   * @param int $sequence
   *   The position.
   * @param int $completed
   *   The completed visitor index.
   *
   * @return \Xylemical\Parser\Tree\VisitorOperation|null
   *   The visitor operation or NULL.
   */
  protected function leave(NodeInterface $node, int $sequence, int $completed): VisitorOperation|null {
    $return = NULL;
    $target = $node;
    foreach ($this->visitors as $index => $visitor) {
      if ($index > $completed) {
        break;
      }

      if (is_null($result = $visitor->leave($target, $sequence))) {
        continue;
      }

      $return = $result;
      $target = $result->getReplacement() ?: $target;
      if ($result->isStop()) {
        return $result;
      }
    }
    return $return;
  }

  /**
   * Traverses and individual node.
   *
   * @param \Xylemical\Parser\Tree\NodeInterface $node
   *   The node.
   * @param int $sequence
   *   The sequence position.
   *
   * @return \Xylemical\Parser\Tree\VisitorOperation|null
   *   The visitor operation or NULL.
   *
   * @throws \Xylemical\Parser\Exception\TraversalException
   */
  protected function traverseNode(NodeInterface $node, int $sequence): VisitorOperation|null {
    $completed = 0;

    $result = $this->enter($node, $sequence, $completed);
    if ($result?->isStop() || $result?->isRemoval()) {
      return $result;
    }

    if (!$result?->skipsChildren() && ($children = $node->getChildren())) {
      $result = $this->traverseChildren($children);
      if ($result?->isStop()) {
        return $result;
      }
    }

    return $this->leave($node, $sequence, $completed);
  }

  /**
   * Traverses the children.
   *
   * @param \Xylemical\Parser\Tree\ChildrenInterface $children
   *   The children sequence.
   *
   * @return \Xylemical\Parser\Tree\VisitorOperation|null
   *   The visitor operation or NULL.
   *
   * @throws \Xylemical\Parser\Exception\TraversalException
   */
  protected function traverseChildren(ChildrenInterface $children): VisitorOperation|null {
    $sequence = -1;
    foreach ($children as $name => $child) {
      $sequence++;
      if ($child instanceof NodeInterface) {
        $result = $this->traverseNode($child, $sequence);
        if ($result?->isStop()) {
          return $result;
        }
        elseif ($result?->isRemoval()) {
          unset($children[$name]);
        }
        elseif ($child = $result?->getReplacement()) {
          $children[$name] = $child;
        }
        continue;
      }

      if ($child instanceof ChildrenInterface) {
        if ($result = $this->traverseChildren($child)) {
          return $result;
        }
        continue;
      }

      throw new TraversalException("Unexpected child of type " . gettype($child) . ".");
    }

    return NULL;
  }

}
