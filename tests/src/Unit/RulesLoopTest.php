<?php

/**
 * @file
 * Contains \Drupal\Tests\rules\Unit\RulesLoopTest.
 */

namespace Drupal\Tests\rules\Unit;

use Drupal\Component\Uuid\Php;
use Drupal\rules\Plugin\RulesExpression\RulesLoop;
use Drupal\rules\Plugin\RulesExpression\RulesAction;
use Drupal\rules\Engine\ExecutionStateInterface;
use Prophecy\Argument;

/**
 * @coversDefaultClass \Drupal\rules\Plugin\RulesExpression\RulesLoop
 * @group rules
 */
class RulesLoopTest extends RulesUnitTestBase {

  /**
   * The action set being tested.
   *
   * @var \Drupal\rules\Plugin\RulesExpression\RulesLoop
   */
  protected $loop;

  /**
   * {@inheritdoc}
   */
  public function setUp() {
    parent::setUp();

    // Configure the loop with a variable called 'list' in the state that should
    // be iterated over.
    $this->loop = new RulesLoop(['list' => 'list'], '', [], $this->expressionManager->reveal(), new Php());
  }

  /**
   * Tests that an action in the loop fires.
   */
  public function testActionExecution() {
    $execution_state = $this->prophesize(ExecutionStateInterface::class);
    // Provide a list with 2 items, so the action is excuted twice.
    $execution_state->getVariable('list')->willReturn([1, 2]);
    
    $this->testActionExpression->executeWithState($execution_state->reveal())
      ->shouldBeCalledTimes(2);

    $this->loop->addExpressionObject($this->testActionExpression->reveal())
      ->executeWithState($execution_state->reveal());
  }

  /**
   * Tests that two actions in the set fire both.
   */
  /*public function testTwoActionExecution() {
    // The method on the test action must be called twice.
    $this->testActionExpression->executeWithState(
      Argument::type(ExecutionStateInterface::class))->shouldBeCalledTimes(2);

    $this->loop->addExpressionObject($this->testActionExpression->reveal())
      ->addExpressionObject($this->testActionExpression->reveal())
      ->execute();
  }

  /**
   * Tests that nested action sets work.
   */
  /*public function testNestedActionExecution() {
    // The method on the test action must be called twice.
    $this->testActionExpression->executeWithState(
      Argument::type(ExecutionStateInterface::class))->shouldBeCalledTimes(2);

    $inner = new RulesLoop([], '', [], $this->expressionManager->reveal(), new Php());
    $inner->addExpressionObject($this->testActionExpression->reveal());

    $this->loop->addExpressionObject($this->testActionExpression->reveal())
      ->addExpressionObject($inner)
      ->execute();
  }

  /**
   * Tests that a nested action can be retrieved by UUID.
   */
  /*public function testLookupAction() {
    $this->loop->addExpressionObject($this->testActionExpression->reveal());
    $uuid = $this->loop->getIterator()->key();
    $this->assertSame($this->testActionExpression->reveal(), $this->loop->getExpression($uuid));
    $this->assertFalse($this->loop->getExpression('invalid UUID'));
  }

  /**
   * Tests deleting an action from the container.
   */
  /*public function testDeletingAction() {
    $this->loop->addExpressionObject($this->testActionExpression->reveal());
    $second_action = $this->prophesize(RulesAction::class);
    $this->loop->addExpressionObject($second_action->reveal());

    // Get the UUID of the first action added.
    $uuid = $this->loop->getIterator()->key();
    $this->loop->deleteExpression($uuid);
    // Now only the second action remains.
    foreach ($this->loop as $action) {
      $this->assertSame($second_action->reveal(), $action);
    }
  }*/

}
