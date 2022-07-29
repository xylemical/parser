<?php

declare(strict_types=1);

namespace Xylemical\Parser\Tree;

use Xylemical\Parser\Tree\ChildrenInterface;
use Xylemical\Parser\Tree\NodeInterface;
use function call_user_func;
use function method_exists;
use function property_exists;

/**
 * Provides generic children operation.
 *
 * This updates the parent node using getters and setters, or property
 * modification.
 */
class Children implements ChildrenInterface, \Iterator {

  /**
   * The parent node.
   *
   * @var \Xylemical\Parser\Tree\NodeInterface
   */
  protected NodeInterface $parent;

  /**
   * The child keys for the node.
   *
   * @var string[]
   */
  protected array $keys;

  /**
   * The current iterator pointer.
   *
   * @var int
   */
  protected int $pointer = 0;

  /**
   * Children constructor.
   *
   * @param \Xylemical\Parser\Tree\NodeInterface $parent
   *   The parent.
   * @param string[] $keys
   *   The child keys.
   */
  public function __construct(NodeInterface $parent, array $keys = []) {
    $this->parent = $parent;
    $this->keys = array_values($keys);
  }

  /**
   * {@inheritdoc}
   */
  public function offsetExists(mixed $offset) {
    return in_array($offset, $this->keys);
  }

  /**
   * {@inheritdoc}
   */
  public function offsetGet(mixed $offset) {
    return $this->get($offset);
  }

  /**
   * {@inheritdoc}
   */
  public function offsetSet(mixed $offset, mixed $value) {
    $this->set($offset, $value);
  }

  /**
   * {@inheritdoc}
   */
  public function offsetUnset(mixed $offset) {
    $this->set($offset, NULL);
  }

  /**
   * {@inheritdoc}
   */
  public function count() {
    return count($this->keys);
  }

  /**
   * {@inheritdoc}
   */
  public function current(): NodeInterface|ChildrenInterface|null {
    $name = $this->name();
    return $this->get($name);
  }

  /**
   * {@inheritdoc}
   */
  public function next() {
    do {
      $this->pointer++;
    } while (!$this->get($this->name()) && $this->pointer < count($this->keys));
  }

  /**
   * {@inheritdoc}
   */
  public function key(): string {
    return $this->name();
  }

  /**
   * {@inheritdoc}
   */
  public function valid(): bool {
    return isset($this->keys[$this->pointer]);
  }

  /**
   * {@inheritdoc}
   */
  public function rewind() {
    $this->pointer = 0;
  }

  /**
   * Get the current name.
   *
   * @return string
   *   The name.
   */
  protected function name(): string {
    return $this->keys[$this->pointer] ?? '';
  }

  /**
   * Get the value from the node.
   *
   * @param string $name
   *   The child name.
   *
   * @return \Xylemical\Parser\Tree\NodeInterface|\Xylemical\Parser\Tree\ChildrenInterface|null
   *   The value.
   */
  protected function get(string $name): NodeInterface|ChildrenInterface|null {
    if (method_exists($this->parent, "get{$name}")) {
      return call_user_func([$this->parent, "get{$name}"]);
    }
    if (property_exists($this->parent, $name)) {
      return $this->parent->{$name};
    }
    return NULL;
  }

  /**
   * Set the value for the node.
   *
   * @param string $name
   *   The child name.
   * @param \Xylemical\Parser\Tree\NodeInterface|\Xylemical\Parser\Tree\ChildrenInterface|null $value
   *   The value.
   */
  protected function set(string $name, NodeInterface|ChildrenInterface|null $value) {
    if (method_exists($this->parent, "set{$name}")) {
      call_user_func([$this->parent, "set{$name}"], $value);
    }
    elseif (property_exists($this->parent, $name)) {
      $this->parent->{$name} = $value;
    }
  }

}
