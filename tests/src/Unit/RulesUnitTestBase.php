<?php

/**
 * @file
 * Contains \Drupal\Tests\rules\Unit\RulesUnitTestBase.
 */

namespace Drupal\Tests\rules\Unit;

use Drupal\rules\Engine\ActionExpressionInterface;
use Drupal\rules\Engine\ConditionExpressionInterface;
use Drupal\rules\Engine\ExecutionStateInterface;
use Drupal\rules\Engine\ExpressionManagerInterface;
use Drupal\Tests\UnitTestCase;
use Prophecy\Argument;

/**
 * Helper class with mock objects.
 */
abstract class RulesUnitTestBase extends UnitTestCase {

  /**
   * A mocked condition that always evaluates to TRUE.
   *
   * @var \Drupal\rules\Engine\ConditionExpressionInterface|\Prophecy\Prophecy\ProphecyInterface
   */
  protected $trueConditionExpression;

  /**
   * A mocked condition that always evaluates to FALSE.
   *
   * @var \Drupal\rules\Engine\ConditionExpressionInterface|\Prophecy\Prophecy\ProphecyInterface
   */
  protected $falseConditionExpression;

  /**
   * A mocked dummy action object.
   *
   * @var \Drupal\rules\Engine\ActionExpressionInterface|\Prophecy\Prophecy\ProphecyInterface
   */
  protected $testActionExpression;

  /**
   * The mocked expression manager object.
   *
   * @var \Drupal\rules\Engine\ExpressionPluginManager|\Prophecy\Prophecy\ProphecyInterface
   */
  protected $expressionManager;

  /**
   * {@inheritdoc}
   */
  public function setUp() {
    parent::setUp();

    $this->trueConditionExpression = $this->prophesize(ConditionExpressionInterface::class);
    // Ignore UUID calls in the unit tests.
    $this->trueConditionExpression->getUuid()->willReturn(NULL);
    $this->trueConditionExpression->setUuid(Argument::type('string'))->willReturn(NULL);

    $this->trueConditionExpression->execute()->willReturn(TRUE);
    $this->trueConditionExpression->executeWithState(
      Argument::type(ExecutionStateInterface::class))->willReturn(TRUE);

    $this->falseConditionExpression = $this->prophesize(ConditionExpressionInterface::class);
    // Ignore UUID calls in the unit tests.
    $this->falseConditionExpression->getUuid()->willReturn(NULL);
    $this->falseConditionExpression->setUuid(Argument::type('string'))->willReturn(NULL);
    $this->falseConditionExpression->execute()->willReturn(FALSE);
    $this->falseConditionExpression->executeWithState(
      Argument::type(ExecutionStateInterface::class))->willReturn(FALSE);

    $this->testActionExpression = $this->prophesize(ActionExpressionInterface::class);
    // Ignore UUID calls in the unit tests.
    $this->testActionExpression->getUuid()->willReturn(NULL);
    $this->testActionExpression->setUuid(Argument::type('string'))->willReturn(NULL);

    $this->expressionManager = $this->prophesize(ExpressionManagerInterface::class);
  }

}
