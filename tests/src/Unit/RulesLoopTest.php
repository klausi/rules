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
  public function testTwoActionExecution() {
    $execution_state = $this->prophesize(ExecutionStateInterface::class);
    // Provide a list with 2 items, so the action is excuted 4 times.
    $execution_state->getVariable('list')->willReturn([1, 2]);

    $this->testActionExpression->executeWithState($execution_state->reveal())
      ->shouldBeCalledTimes(4);

    $this->loop
      ->addExpressionObject($this->testActionExpression->reveal())
      ->addExpressionObject($this->testActionExpression->reveal())
      ->executeWithState($execution_state->reveal());
  }

  /**
   * Tests that nested action sets work.
   */
  public function testNestedActionExecution() {
    $execution_state = $this->prophesize(ExecutionStateInterface::class);
    // Provide two lists with a nested loop, so the action is executed 3 times:
    // 1 time in the outer loop, 2 times in the inner loop
    $execution_state->getVariable('list')->willReturn([1]);
    $execution_state->getVariable('inner_list')->willReturn([1, 2]);

    $this->testActionExpression->executeWithState($execution_state->reveal())
      ->shouldBeCalledTimes(3);

    $inner = new RulesLoop(['list' => 'inner_list'], '', [], $this->expressionManager->reveal(), new Php());
    $inner->addExpressionObject($this->testActionExpression->reveal());

    $this->loop->addExpressionObject($this->testActionExpression->reveal())
      ->addExpressionObject($inner)
      ->executeWithState($execution_state->reveal());
  }

}
