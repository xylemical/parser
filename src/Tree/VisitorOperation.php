<?php

declare(strict_types=1);

namespace Xylemical\Parser\Tree;

use Xylemical\Parser\Tree\NodeInterface;

/**
 * Provides an operation regarding traversal.
 */
class VisitorOperation {

  /**
   * Replaces the node during the traversal process.
   *
   * Visitors that follow will be presented the replacement node.
   */
  public const REPLACE_NODE = 1;

  /**
   * Stops the traversal completely.
   *
   * Visitors that follow will not be called at any remaining levels.
   */
  public const STOP_TRAVERSAL = 2;

  /**
   * Skips traversing the children.
   *
   * Visitors will be called on the current node.
   */
  public const SKIP_CHILDREN = 3;

  /**
   * Skips traversing the current node, and it's children.
   *
   * Visitors will be called up to the visitor that called for skipping the
   * current node.
   */
  public const SKIP_SELF = 4;

  /**
   * Removes the current node from the children.
   *
   * Visitors will still be called for the current node.
   */
  public const REMOVE_NODE = 5;

  /**
   * The type of the operation.
   *
   * @var int
   */
  protected int $type;

  /**
   * The replacement node.
   *
   * @var \Xylemical\Parser\Tree\NodeInterface|null
   */
  protected ?NodeInterface $replacement;

  /**
   * VisitorOperation constructor.
   *
   * @param int $type
   *   The operation type.
   * @param \Xylemical\Parser\Tree\NodeInterface|null $replacement
   *   The replacement node.
   */
  public function __construct(int $type, ?NodeInterface $replacement = NULL) {
    $this->type = $type;
    $this->replacement = $replacement;
  }

  /**
   * Get the replacement node.
   *
   * @return \Xylemical\Parser\Tree\NodeInterface|null
   *   The node.
   */
  public function getReplacement(): ?NodeInterface {
    return $this->replacement;
  }

  /**
   * Check the operation completely stops.
   *
   * @return bool
   *   The result.
   */
  public function isStop(): bool {
    return $this->type === self::STOP_TRAVERSAL;
  }

  /**
   * Check the operation skips the current node visiting process.
   *
   * @return bool
   *   The result.
   */
  public function skipsSelf(): bool {
    return $this->type === self::SKIP_SELF;
  }

  /**
   * Check operation skips processing of the children.
   *
   * @return bool
   *   The result.
   */
  public function skipsChildren(): bool {
    return $this->type === self::SKIP_SELF || $this->type === self::SKIP_CHILDREN;
  }

  /**
   * Check node removal.
   *
   * @return bool
   *   The result.
   */
  public function isRemoval(): bool {
    return $this->type === self::REMOVE_NODE;
  }

  /**
   * Creates a traversal stop operation.
   *
   * @return \Xylemical\Parser\VisitorOperation
   *   The operation.
   */
  public static function stop(): VisitorOperation {
    return new VisitorOperation(self::STOP_TRAVERSAL);
  }

  /**
   * Creates a skip children operation.
   *
   * @return \Xylemical\Parser\VisitorOperation
   *   The operation.
   */
  public static function skipChildren(): VisitorOperation {
    return new VisitorOperation(self::SKIP_CHILDREN);
  }

  /**
   * Creates a skip self operation.
   *
   * @return \Xylemical\Parser\VisitorOperation
   *   The operation.
   */
  public static function skipSelf(): VisitorOperation {
    return new VisitorOperation(self::SKIP_SELF);
  }

  /**
   * Creates a replace node operation.
   *
   * @param \Xylemical\Parser\Tree\NodeInterface $replacement
   *   The replacement node.
   *
   * @return \Xylemical\Parser\VisitorOperation
   *   The operation.
   */
  public static function replaceNode(NodeInterface $replacement): VisitorOperation {
    return new VisitorOperation(self::REPLACE_NODE, $replacement);
  }

  /**
   * Creates a remove node operation.
   *
   * @return \Xylemical\Parser\VisitorOperation
   *   The operation.
   */
  public static function removeNode(): VisitorOperation {
    return new VisitorOperation(self::REMOVE_NODE);
  }

}
