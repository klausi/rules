<?php

/**
 * @file
 * Contains \Drupal\rules\Tests\ContextIntegrationTest.
 */

namespace Drupal\rules\Tests;

use Drupal\rules\Context\ContextConfig;
use Drupal\rules\Context\ContextDefinition;
use Drupal\rules\Exception\RulesEvaluationException;

/**
 * Tests the the extended core context API with Rules.
 *
 * @group rules
 */
class ContextIntegrationTest extends RulesDrupalTestBase {

  /**
   * Modules to enable.
   *
   * @var array
   */
  public static $modules = ['node', 'user'];

  /**
   * {@inheritdoc}
   */
  public function setUp() {
    parent::setUp();

    $this->installEntitySchema('user');
    $this->installEntitySchema('node');
  }

  /**
   * Tests that a required context mapping that is NULL throws an exception.
   */
  public function testRequiredNullMapping() {
    $entity_manager = $this->container->get('entity.manager');
    $entity_manager->getStorage('node_type')
      ->create(['type' => 'page'])
      ->save();

    $node = $entity_manager->getStorage('node')
      ->create([
        'title' => 'test',
        'type' => 'page',
      ]);

    // Configure a simple rule with one action. The node ID is NULL and does not
    // exist yet.
    $action = $this->expressionManager->createInstance('rules_action',
      ContextConfig::create()
        ->setConfigKey('action_id', 'rules_test_string')
        ->map('text', 'node:nid:0:value')
        ->toArray()
    );

    $rule = $this->expressionManager->createRule([
      'context_definitions' => [
        'node' => ContextDefinition::create('entity:node')->toArray(),
      ],
    ]);
    $rule->setContextValue('node', $node);
    $rule->addExpressionObject($action);
    try {
      $rule->execute();
      $this->fail('No exception thrown when required context value is NULL');
    }
    catch (RulesEvaluationException $e) {
      $this->pass('Exception thrown as expected when a required context is NULL');
    }
  }

  /**
   * Tests that a required context value that is NULL throws an exception.
   */
  public function testRequiredNullValue() {
    $entity_manager = $this->container->get('entity.manager');
    $entity_manager->getStorage('node_type')
      ->create(['type' => 'page'])
      ->save();

    $node = $entity_manager->getStorage('node')
      ->create([
        'title' => 'test',
        'type' => 'page',
      ]);

    // Configure a simple rule with one action. The required 'text' context is
    // set to be NULL.
    $action = $this->expressionManager->createInstance('rules_action',
      ContextConfig::create()
        ->setConfigKey('action_id', 'rules_test_string')
        ->setValue('text', NULL)
        ->toArray()
    );

    $rule = $this->expressionManager->createRule([
      'context_definitions' => [
        'node' => ContextDefinition::create('entity:node')->toArray(),
      ],
    ]);
    $rule->setContextValue('node', $node);
    $rule->addExpressionObject($action);
    try {
      $rule->execute();
      $this->fail('No exception thrown when required context value is NULL');
    }
    catch (RulesEvaluationException $e) {
      $this->pass('Exception thrown as expected when a required context is NULL');
    }
  }

  /**
   * Tests that NULL values for contexts are allowed if specified.
   */
  public function testAllowNullValue() {
    $entity_manager = $this->container->get('entity.manager');
    $entity_manager->getStorage('node_type')
      ->create(['type' => 'page'])
      ->save();

    // Create a node with a title set to NULL.
    $node = $entity_manager->getStorage('node')
      ->create([
        'type' => 'page',
      ]);
    $node->setTitle(NULL);

    // Configure a simple rule with one action.
    $action = $this->expressionManager->createInstance('rules_action',
      ContextConfig::create()
        ->setConfigKey('action_id', 'rules_data_set')
        ->map('data', 'node:title:0:value')
        ->map('value', 'new_title')
        ->toArray()
    );

    $rule = $this->expressionManager->createRule([
      'context_definitions' => [
        'node' => ContextDefinition::create('entity:node')->toArray(),
        'new_title' => ContextDefinition::create('string')->toArray(),
      ],
    ]);
    $rule->setContextValue('node', $node);
    $rule->setContextValue('new_title', 'new title');
    $rule->addExpressionObject($action);
    $rule->execute();

    $this->assertEqual('new title', $node->getTitle());
    $this->assertNotNull($node->id(), 'Node ID is set, which means that the node has been auto-saved.');
  }

}
