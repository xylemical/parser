<?php

declare(strict_types=1);

namespace Xylemical\Parser\Exception;

use Xylemical\Parser\Tree\NodeInterface;

/**
 * Triggered when problem with traversal behaviour.
 */
class TraversalException extends \Exception {

  /**
   * The node where traversal failed.
   *
   * @var \Xylemical\Parser\Tree\NodeInterface|null
   */
  protected ?NodeInterface $node;

  /**
   * TraversalException constructor.
   *
   * @param string $message
   *   The message.
   * @param \Xylemical\Parser\Tree\NodeInterface|null $node
   *   The node where traversal failed.
   * @param int $code
   *   The code.
   * @param \Throwable|null $previous
   *   The previous.
   */
  public function __construct(string $message = "", ?NodeInterface $node = NULL, int $code = 0, ?\Throwable $previous = NULL) {
    parent::__construct($message, $code, $previous);
    $this->node = $node;
  }

  /**
   * Get the node where traversal failed.
   *
   * @return \Xylemical\Parser\Tree\NodeInterface|null
   *   The node.
   */
  public function getNode(): ?NodeInterface {
    return $this->node;
  }

}
