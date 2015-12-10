<?php

/**
 * @file
 * Contains \Drupal\Tests\rules\Unit\RulesConditionContainerTest.
 */

namespace Drupal\Tests\rules\Unit;

use Drupal\Component\Uuid\Php;
use Drupal\rules\Engine\ConditionExpressionContainer;
use Drupal\rules\Engine\ExpressionManagerInterface;
use Drupal\rules\Engine\ExecutionStateInterface;

/**
 * @coversDefaultClass \Drupal\rules\Engine\ConditionExpressionContainer
 * @group rules
 */
class RulesConditionContainerTest extends RulesUnitTestBase {

  /**
   * Creates a mocked condition container.
   *
   * @param array $methods
   *   The methods to mock.
   * @param string $class
   *   The name of the created mock class.
   *
   * @return \Drupal\rules\Engine\ConditionExpressionContainerInterface
   *   The mocked condition container.
   */
  protected function getMockConditionContainer(array $methods = [], $class = 'RulesConditionContainerMock') {
    return $this->getMockForAbstractClass(
      ConditionExpressionContainer::class, [
        [],
        'test_id',
        [],
        $this->prophesize(ExpressionManagerInterface::class)->reveal(),
        new Php(),
      ], $class, TRUE, TRUE, TRUE, $methods
    );
  }

  /**
   * Tests adding conditions to the condition container.
   *
   * @covers ::addExpressionObject
   */
  public function testAddExpressionObject() {
    $container = $this->getMockConditionContainer();
    $container->addExpressionObject($this->trueConditionExpression->reveal());

    $property = new \ReflectionProperty($container, 'conditions');
    $property->setAccessible(TRUE);

    $this->assertArrayEquals([$this->trueConditionExpression->reveal()], array_values($property->getValue($container)));
  }

  /**
   * Tests negating the result of the condition container.
   *
   * @covers ::negate
   * @covers ::isNegated
   */
  public function testNegate() {
    $container = $this->getMockForAbstractClass(RulesConditionContainerTestStub::class, [], '', FALSE);

    $this->assertFalse($container->isNegated());
    $this->assertTrue($container->execute());

    $container->negate(TRUE);
    $this->assertTrue($container->isNegated());
    $this->assertFalse($container->execute());
  }

  /**
   * Tests executing the condition container.
   *
   * @covers ::execute
   */
  public function testExecute() {
    $container = $this->getMockForAbstractClass(RulesConditionContainerTestStub::class, [], '', FALSE);
    $this->assertTrue($container->execute());
  }

  /**
   * Tests that a nested expression can be retrieved by UUID.
   */
  public function testLookupExpression() {
    $container = $this->getMockForAbstractClass(RulesConditionContainerTestStub::class, [
      [],
      'test_id',
      [],
      $this->prophesize(ExpressionManagerInterface::class)->reveal(),
      new Php(),
    ], '', TRUE);
    $container->addExpressionObject($this->trueConditionExpression->reveal());
    $uuid = $container->getIterator()->key();
    $this->assertSame($this->trueConditionExpression->reveal(), $container->getExpression($uuid));
    $this->assertFalse($container->getExpression('invalid UUID'));
  }

  /**
   * Tests deleting a condition from the container.
   */
  public function testDeletingCondition() {
    $container = $this->getMockForAbstractClass(RulesConditionContainerTestStub::class, [
      [],
      'test_id',
      [],
      $this->prophesize(ExpressionManagerInterface::class)->reveal(),
      new Php(),
    ], '', TRUE);
    $container->addExpressionObject($this->trueConditionExpression->reveal());
    $container->addExpressionObject($this->falseConditionExpression->reveal());

    // Delete the first condition.
    $uuid = $container->getIterator()->key();
    $container->deleteExpression($uuid);
    foreach ($container as $condition) {
      $this->assertEquals($this->falseConditionExpression->reveal(), $condition);
    }
  }

}

/**
 * Class used for overriding evalute() as this does not work with PHPunit.
 */
abstract class RulesConditionContainerTestStub extends ConditionExpressionContainer {

  /**
   * {@inheritdoc}
   */
  public function evaluate(ExecutionStateInterface $state) {
    return TRUE;
  }

}
