<?php

declare(strict_types=1);

namespace Xylemical\Parser\Tree;

use Xylemical\Parser\Tree\ChildrenInterface;

/**
 * Provides a base node of an abstract syntax tree.
 */
interface NodeInterface {

  /**
   * Get the sub nodes of this node.
   *
   * @return \Xylemical\Parser\Tree\ChildrenInterface|null
   *   A contains indexed references to either a node or child nodes.
   */
  public function getChildren(): ?ChildrenInterface;

}
