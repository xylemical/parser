<?php

declare(strict_types=1);

namespace Xylemical\Parser\Tree;

use Xylemical\Parser\Tree\NodeInterface;
use Xylemical\Parser\Tree\VisitorOperation;

/**
 * Provides a node visitor interface.
 */
interface VisitorInterface {

  /**
   * Called when entering a node.
   *
   * @param \Xylemical\Parser\Tree\NodeInterface $node
   *   The node.
   * @param int $sequence
   *   The position in a sequence. For individual nodes this is always 0.
   *
   * @return \Xylemical\Parser\Tree\VisitorOperation|null
   *   A visitor operation or NULL.
   */
  public function enter(NodeInterface $node, int $sequence): ?VisitorOperation;

  /**
   * Called when leaving a node.
   *
   * @param \Xylemical\Parser\Tree\NodeInterface $node
   *   The node.
   * @param int $sequence
   *   The position in a sequence. For individual nodes this is always zero.
   *
   * @return \Xylemical\Parser\Tree\VisitorOperation|null
   *   A visitor operation or NULL.
   */
  public function leave(NodeInterface $node, int $sequence): ?VisitorOperation;

}
