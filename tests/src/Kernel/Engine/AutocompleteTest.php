<?php

/**
 * @file
 * Contains \Drupal\Tests\rules\Kernel\Engine\AutocompleteTest.
 */

namespace Drupal\Tests\rules\Kernel\Engine;

use Drupal\rules\Context\ContextConfig;
use Drupal\rules\Context\ContextDefinition;
use Drupal\rules\Engine\RulesComponent;
use Drupal\Tests\rules\Kernel\RulesDrupalTestBase;

/**
 * Tests that data selector autocomplete results work correctly.
 *
 * @group rules
 */
class AutocompleteTest extends RulesDrupalTestBase {

  /**
   * {@inheritdoc}
   */
  public static $modules = ['rules', 'node', 'user'];

  /**
   * {@inheritdoc}
   */
  public function setUp() {
    parent::setUp();

    $this->installEntitySchema('user');
  }

  /**
   * Tests autocompletion works for a variable in the metadata state.
   */
  public function testAutocomplete() {
    $rule = $this->expressionManager->createRule();
    $action = $this->expressionManager->createAction('rules_action');
    $action->setConfiguration(ContextConfig::create()
      ->map('entity', 'entity')
      ->toArray()
    );
    $rule->addExpressionObject($action);

    $results = RulesComponent::create($rule)
      ->addContextDefinition('entity', ContextDefinition::create('entity'))
      ->autocomplete('e', $action);

    $this->assertSame(['entity'], $results);
  }

  /**
   * Tests that "node.uid.en" returns the suggestion "node.uid.entity".
   */
  public function testNodeFieldAutocomplete() {
    $rule = $this->expressionManager->createRule();
    $action = $this->expressionManager->createAction('rules_action');
    $action->setConfiguration(ContextConfig::create()
      ->map('node', 'node')
      ->toArray()
    );
    $rule->addExpressionObject($action);

    $results = RulesComponent::create($rule)
      ->addContextDefinition('node', ContextDefinition::create('entity:node'))
      ->autocomplete('node.uid.en', $action);

    $this->assertSame(['node.uid.entity'], $results);
  }

}
