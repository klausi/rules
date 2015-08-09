<?php

/**
 * @file
 * Contains \Drupal\Tests\rules\Unit\RulesUnitTestBase.
 */

namespace Drupal\Tests\rules\Unit;

use Drupal\rules\Engine\ActionExpressionInterface;
use Drupal\rules\Engine\ConditionExpressionInterface;
use Drupal\rules\Engine\RulesStateInterface;
use Drupal\rules\Engine\ExpressionPluginManagerInterface;
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

    $this->trueConditionExpression->execute()->willReturn(TRUE);
    $this->trueConditionExpression->executeWithState(
      Argument::type(RulesStateInterface::class))->willReturn(TRUE);

    $this->falseConditionExpression = $this->prophesize(ConditionExpressionInterface::class);
    $this->falseConditionExpression->execute()->willReturn(FALSE);
    $this->falseConditionExpression->executeWithState(
      Argument::type(RulesStateInterface::class))->willReturn(FALSE);

    $this->testActionExpression = $this->prophesize(ActionExpressionInterface::class);

    $this->expressionManager = $this->prophesize(ExpressionPluginManagerInterface::class);
  }

  /**
   * Creates an 'and' condition container with the basic plugin methods mocked.
   *
   * @param array $methods
   *   (optional) The methods to mock.
   *
   * @return \Drupal\rules\Engine\ConditionExpressionContainerInterface
   *   The mocked 'and' condition container.
   */
  protected function getMockAnd(array $methods = []) {
    $methods += ['getPluginId', 'getBasePluginId', 'getDerivativeId', 'getPluginDefinition'];

    $and = $this->getMockBuilder('Drupal\rules\Plugin\RulesExpression\RulesAnd')
      ->setMethods($methods)
      ->disableOriginalConstructor()
      ->getMock();

    $this->expectsGetPluginId($and, 'rules_and')
      ->expectsGetDerivativeId($and, NULL)
      ->expectsGetBasePluginId($and, 'rules_and')
      ->expectsGetPluginDefinition($and, 'rules_and', 'Condition set (AND)');

    return $and;
  }

  /**
   * Creates an 'or' condition container with the basic plugin methods mocked.
   *
   * @param array $methods
   *   (optional) The methods to mock.
   *
   * @return \Drupal\rules\Engine\ConditionExpressionContainerInterface
   *   The mocked 'or' condition container.
   */
  protected function getMockOr(array $methods = []) {
    $methods += ['getPluginId', 'getBasePluginId', 'getDerivativeId', 'getPluginDefinition'];

    $or = $this->getMockBuilder('Drupal\rules\Plugin\RulesExpression\RulesOr')
      ->setMethods($methods)
      ->disableOriginalConstructor()
      ->getMock();

    $this->expectsGetPluginId($or, 'rules_or')
      ->expectsGetDerivativeId($or, NULL)
      ->expectsGetBasePluginId($or, 'rules_or')
      ->expectsGetPluginDefinition($or, 'rules_or', 'Condition set (OR)');

    return $or;
  }

  /**
   * Creates an action set with the basic plugin methods mocked.
   *
   * @param array $methods
   *   (optional) The methods to mock.
   *
   * @return \Drupal\rules\Engine\ActionExpressionContainerInterface
   *   The mocked action container.
   */
  protected function getMockActionSet(array $methods = []) {
    $methods += ['getPluginId', 'getBasePluginId', 'getDerivativeId', 'getPluginDefinition'];

    $actions = $this->getMockBuilder('Drupal\rules\Plugin\RulesExpression\ActionSet')
      ->setMethods($methods)
      ->disableOriginalConstructor()
      ->getMock();

    $this->expectsGetPluginId($actions, 'rules_action_set')
      ->expectsGetDerivativeId($actions, NULL)
      ->expectsGetBasePluginId($actions, 'rules_action_set')
      ->expectsGetPluginDefinition($actions, 'rules_action_set', 'Rules Action');

    return $actions;
  }

  /**
   * Sets the mocked plugin to expect calls to 'getPluginId'.
   *
   * @param \PHPUnit_Framework_MockObject_MockObject $plugin
   *   The mocked plugin instance.
   * @param string $id
   *   (optional) The id of the plugin. Defaults to an empty string.
   *
   * @return $this
   *   The current object for chaining.
   */
  protected function expectsGetPluginId(\PHPUnit_Framework_MockObject_MockObject $plugin, $id = '') {
    $plugin->expects($this->any())
      ->method('getPluginId')
      ->will($this->returnValue($id));

    return $this;
  }

  /**
   * Sets the mocked plugin to expect calls to 'getBasePluginId'.
   *
   * @param \PHPUnit_Framework_MockObject_MockObject $plugin
   *   The mocked plugin instance.
   * @param string $id
   *   (optional) The base id of the plugin. Defaults to an empty string.
   *
   * @return $this
   *   The current object for chaining.
   */
  protected function expectsGetBasePluginId(\PHPUnit_Framework_MockObject_MockObject $plugin, $id = '') {
    $plugin->expects($this->any())
      ->method('getBasePluginId')
      ->will($this->returnValue($id));

    return $this;
  }

  /**
   * Sets the mocked plugin to expect calls to 'getDerivativeId'.
   *
   * @param \PHPUnit_Framework_MockObject_MockObject $plugin
   *   The mocked plugin instance.
   * @param string $id
   *   (optional) The derivative id of the plugin. Defaults to NULL.
   *
   * @return $this
   *   The current object for chaining.
   */
  protected function expectsGetDerivativeId(\PHPUnit_Framework_MockObject_MockObject $plugin, $id = NULL) {
    $plugin->expects($this->any())
      ->method('getDerivativeId')
      ->will($this->returnValue(NULL));

    return $this;
  }

  /**
   * Sets the mocked plugin to expect calls to 'getPluginDefinition'.
   *
   * @param \PHPUnit_Framework_MockObject_MockObject $plugin
   *   The mocked plugin instance.
   * @param string $id
   *   (optional) The id of the plugin. Defaults to an empty string.
   * @param string $label
   *   (optional) The label of the plugin. Defaults to NULL.
   * @param string $provider
   *   (optional) The name of the providing module. Defaults to 'rules'.
   * @param array $other
   *   (optional) Any other values to set as the plugin definition.
   *
   * @return $this
   *   The current object for chaining.
   */
  protected function expectsGetPluginDefinition(\PHPUnit_Framework_MockObject_MockObject $plugin, $id = '', $label = NULL, $provider = 'rules', array $other = []) {
    $defaults = [
      'type' => '',
      'id' => $id,
      'class' => get_class($plugin),
      'provider' => $provider,
    ];

    if (isset($label)) {
      $definition['label'] = $label;
    }

    $plugin->expects($this->any())
      ->method('getPluginDefinition')
      ->will($this->returnValue($other + $defaults));

    return $this;
  }

}
