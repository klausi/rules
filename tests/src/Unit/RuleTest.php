<?php

/**
 * @file
 * Contains \Drupal\Tests\rules\Unit\RuleTest.
 */

namespace Drupal\Tests\rules\Unit;

use Drupal\rules\Context\ContextDefinition;
use Drupal\rules\Engine\ExpressionPluginManager;
use Drupal\rules\Plugin\RulesExpression\Rule;

/**
 * @coversDefaultClass \Drupal\rules\Plugin\RulesExpression\Rule
 * @group rules
 */
class RuleTest extends RulesUnitTestBase {

  /**
   * The rules expression plugin manager.
   *
   * @var \Drupal\rules\Engine\ExpressionPluginManager|\Prophecy\Prophecy\ProphecyInterface
   */
  protected $expressionManager;

  /**
   * The rule being tested.
   *
   * @var \Drupal\rules\Plugin\RulesExpression\RuleInterface
   */
  protected $rule;

  /**
   * The primary condition container of the rule.
   *
   * @var \Drupal\rules\Engine\ConditionExpressionContainerInterface
   */
  protected $conditions;

  /**
   * The primary action container of the rule.
   *
   * @var \Drupal\rules\Engine\ActionExpressionContainerInterface
   */
  protected $actions;

  /**
   * {@inheritdoc}
   */
  public function setUp() {
    parent::setUp();

    $this->expressionManager = $this->prophesize(ExpressionPluginManager::class);

    $this->conditions = $this->getMockAnd();
    $this->expressionManager->createInstance('rules_and', [])->willReturn($this->conditions);

    $this->actions = $this->getMockActionSet();
    $this->expressionManager->createInstance('rules_action_set', [])->willReturn($this->actions);

    $this->rule = new Rule([], 'rules_rule', [], $this->expressionManager->reveal());
  }

  /**
   * Tests that a rule is constructed with condition and action containers.
   *
   * @covers ::__construct
   */
  public function testContainersOnConstruct() {
    $this->assertSame($this->conditions, $this->rule->getConditions());
    $this->assertSame($this->actions, $this->rule->getActions());
  }

  /**
   * Tests the condition container setter and getter.
   *
   * @covers ::setConditions
   * @covers ::getConditions
   */
  public function testSetConditionsGetConditions() {
    $or = $this->getMockOr();
    $this->rule->setConditions($or);
    $this->assertSame($or, $this->rule->getConditions());

    $and = $this->getMockAnd();
    $this->rule->setConditions($and);
    $this->assertSame($and, $this->rule->getConditions());
  }

  /**
   * Tests the condition container setter and getter.
   *
   * @covers ::setActions
   * @covers ::getActions
   */
  public function testSetActionsGetActions() {
    $action_set = $this->getMockActionSet();
    $this->rule->setActions($action_set);
    $this->assertSame($action_set, $this->rule->getActions());
  }

  /**
   * Tests that an action fires if a condition passes.
   *
   * @covers ::execute
   */
  public function testActionExecution() {
    // The method on the test action must be called once.
    $this->testActionExpression->expects($this->once())
      ->method('executeWithState');

    $this->rule
      ->addExpressionObject($this->trueConditionExpression->reveal())
      ->addExpressionObject($this->testActionExpression)
      ->execute();
  }

  /**
   * Tests that an action does not fire if a condition fails.
   *
   * @covers ::execute
   */
  public function testConditionFails() {
    // The execute method on the action must never be called.
    $this->testActionExpression->expects($this->never())
      ->method('execute');

    $this->rule
      ->addExpressionObject($this->falseConditionExpression)
      ->addExpressionObject($this->testActionExpression)
      ->execute();
  }

  /**
   * Tests that an action fires if a condition passes.
   *
   * @covers ::execute
   */
  public function testTwoConditionsTrue() {
    // The method on the test action must be called once.
    $this->testActionExpression->expects($this->once())
      ->method('executeWithState');

    $this->rule
      ->addExpressionObject($this->trueConditionExpression->reveal())
      ->addExpressionObject($this->trueConditionExpression->reveal())
      ->addExpressionObject($this->testActionExpression)
      ->execute();
  }

  /**
   * Tests that an action does not fire if a condition fails.
   *
   * @covers ::execute
   */
  public function testTwoConditionsFalse() {
    // The execute method on the action must never be called.
    $this->testActionExpression->expects($this->never())
      ->method('execute');

    $this->rule
      ->addExpressionObject($this->trueConditionExpression->reveal())
      ->addExpressionObject($this->falseConditionExpression)
      ->addExpressionObject($this->testActionExpression)
      ->execute();
  }

  /**
   * Tests that nested rules are properly executed.
   *
   * @covers ::execute
   */
  public function testNestedRules() {
    $this->testActionExpression->expects($this->once())
      ->method('executeWithState');

    $nested = $this->getMockRule()
      ->addExpressionObject($this->trueConditionExpression->reveal())
      ->addExpressionObject($this->testActionExpression);

    $this->rule
      ->addExpressionObject($this->trueConditionExpression->reveal())
      ->addExpressionObject($nested)
      ->execute();
  }

  /**
   * Tests that a context definiton object is created from configuration.
   */
  public function testContextDefinitionFromConfig() {
    $rule = new Rule([
      'context_definitions' => [
        'node' => ContextDefinition::create('entity:node')
          ->setLabel('node')
          ->toArray()
      ],
    ], 'rules_rule', [], $this->expressionManager->reveal());
    $context_definition = $rule->getContextDefinition('node');
    $this->assertSame($context_definition->getDataType(), 'entity:node');
  }

  /**
   * Tests that provided context definitons are created from configuration.
   */
  public function testProvidedDefinitionFromConfig() {
    $rule = new Rule([
      'provided_definitions' => [
        'node' => ContextDefinition::create('entity:node')
          ->setLabel('node')
          ->toArray()
      ],
    ], 'rules_rule', [], $this->expressionManager->reveal());
    $provided_definition = $rule->getProvidedContextDefinition('node');
    $this->assertSame($provided_definition->getDataType(), 'entity:node');
  }

}
