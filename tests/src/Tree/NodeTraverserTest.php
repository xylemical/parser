<?php

declare(strict_types=1);

namespace Xylemical\Parser\Tree;

use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

/**
 * Tests \Xylemical\Parser\NodeTraverser.
 */
class NodeTraverserTest extends TestCase {

  use ProphecyTrait;

  /**
   * Create a mock node.
   *
   * @param array|null $children
   *   The children.
   *
   * @return \Xylemical\Parser\Tree\NodeInterface
   *   The mock node.
   */
  protected function getMockNode(?array $children = NULL): NodeInterface {
    $node = $this->prophesize(NodeInterface::class);
    if (is_null($children)) {
      $node->getChildren()->willReturn(NULL);
    }
    else {
      $handle = NULL;
      foreach ($children as $key => $value) {
        $node->{$key} = $value;
      }
      $node->getChildren()->will(function ($args) use (&$handle, $children) {
        if (!$handle) {
          // @phpstan-ignore-next-line
          $handle = new Children($this->reveal(), array_keys($children));
        }
        return $handle;
      });
    }
    return $node->reveal();
  }

  /**
   * Tests basic functionality.
   */
  public function testSanity(): void {
    $traverser = new NodeTraverser();
    $this->assertEquals([], $traverser->getVisitors());

    $visitor = new TestVisitor();
    $traverser->addVisitor($visitor);
    $this->assertEquals([$visitor], $traverser->getVisitors());
  }

  /**
   * Test the simple node visitor behaviour.
   */
  public function testNodeStandard(): void {
    $a = new TestVisitor();
    $b = new TestVisitor();
    $traverser = new NodeTraverser([$a, $b]);

    $node = $this->getMockNode();

    $result = $traverser->traverse($node);
    $this->assertSame($node, $result);
    $this->assertEquals(1, count($a->enters));
    $this->assertSame($node, $a->enters[0][0]);
    $this->assertEquals(1, count($a->leaves));
    $this->assertSame($node, $a->leaves[0][0]);

    $this->assertEquals(1, count($b->enters));
    $this->assertSame($node, $b->enters[0][0]);
    $this->assertEquals(1, count($b->leaves));
    $this->assertSame($node, $b->leaves[0][0]);
  }

  /**
   * Tests simple node with children.
   */
  public function testChildrenStandard(): void {
    $a = new TestVisitor();
    $b = new TestVisitor();
    $traverser = new NodeTraverser([$a, $b]);

    $ca = $this->getMockNode();
    $cb = $this->getMockNode();

    $node = $this->getMockNode(['a' => $ca, 'b' => $cb, 'c' => NULL]);

    $result = $traverser->traverse($node);
    $this->assertSame($node, $result);
    $this->assertEquals(3, count($a->enters));
    $this->assertSame($node, $a->enters[0][0]);
    $this->assertSame($ca, $a->enters[1][0]);
    $this->assertEquals(0, $a->enters[1][1]);
    $this->assertSame($cb, $a->enters[2][0]);
    $this->assertEquals(1, $a->enters[2][1]);
    $this->assertEquals(3, count($a->leaves));
    $this->assertSame($ca, $a->leaves[0][0]);
    $this->assertEquals(0, $a->leaves[0][1]);
    $this->assertSame($cb, $a->leaves[1][0]);
    $this->assertEquals(1, $a->leaves[1][1]);
    $this->assertSame($node, $a->leaves[2][0]);

    $this->assertEquals(3, count($b->enters));
    $this->assertSame($node, $b->enters[0][0]);
    $this->assertSame($ca, $b->enters[1][0]);
    $this->assertSame($cb, $b->enters[2][0]);
    $this->assertEquals(3, count($b->leaves));
    $this->assertSame($ca, $b->leaves[0][0]);
    $this->assertSame($cb, $b->leaves[1][0]);
    $this->assertSame($node, $b->leaves[2][0]);
  }

  /**
   * Tests enter() replace node.
   */
  public function testNodeEnterReplace(): void {
    $a = new TestVisitor();
    $b = new TestVisitor();
    $traverser = new NodeTraverser([$a, $b]);

    $node = $this->getMockNode();
    $replacement = $this->getMockNode();

    $a->enterCondition = [$node, VisitorOperation::replaceNode($replacement)];

    $result = $traverser->traverse($node);
    $this->assertSame($replacement, $result);
    $this->assertEquals(1, count($a->enters));
    $this->assertSame($node, $a->enters[0][0]);
    $this->assertEquals(1, count($a->leaves));
    $this->assertSame($replacement, $a->leaves[0][0]);

    $this->assertEquals(1, count($b->enters));
    $this->assertSame($replacement, $b->enters[0][0]);
    $this->assertEquals(1, count($b->leaves));
    $this->assertSame($replacement, $b->leaves[0][0]);
  }

  /**
   * Test enter() replace node on child.
   */
  public function testChildEnterReplaceNode(): void {
    $a = new TestVisitor();
    $b = new TestVisitor();
    $traverser = new NodeTraverser([$a, $b]);

    $ca = $this->getMockNode();
    $cb = $this->getMockNode();

    $replacement = $this->getMockNode();

    $a->enterCondition = [$cb, VisitorOperation::replaceNode($replacement)];

    $node = $this->getMockNode(['a' => $ca, 'b' => $cb]);

    $result = $traverser->traverse($node);
    $this->assertSame($node, $result);
    $this->assertSame($replacement, $node->getChildren()['b']);

    $this->assertEquals(3, count($a->enters));
    $this->assertSame($node, $a->enters[0][0]);
    $this->assertSame($ca, $a->enters[1][0]);
    $this->assertSame($cb, $a->enters[2][0]);
    $this->assertEquals(3, count($a->leaves));
    $this->assertSame($ca, $a->leaves[0][0]);
    $this->assertSame($replacement, $a->leaves[1][0]);
    $this->assertSame($node, $a->leaves[2][0]);

    $this->assertEquals(3, count($b->enters));
    $this->assertSame($node, $b->enters[0][0]);
    $this->assertSame($ca, $b->enters[1][0]);
    $this->assertSame($replacement, $b->enters[2][0]);
    $this->assertEquals(3, count($b->leaves));
    $this->assertSame($ca, $b->leaves[0][0]);
    $this->assertSame($replacement, $b->leaves[1][0]);
    $this->assertSame($node, $b->leaves[2][0]);
  }

}
