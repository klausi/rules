<?php

/**
 * @file
 * Contains \Drupal\rules\Tests\NodeIntegrationTest.
 */

namespace Drupal\rules\Tests;

use Drupal\rules\Context\ContextConfig;

/**
 * Test using the Rules API with nodes.
 *
 * @group rules
 */
class NodeIntegrationTest extends RulesDrupalTestBase {

  /**
   * Modules to enable.
   *
   * @var array
   */
  public static $modules = ['node', 'field', 'text', 'user'];

  /**
   * {@inheritdoc}
   */
  public function setUp() {
    parent::setUp();

    $this->installSchema('system', ['sequences']);
    $this->installEntitySchema('user');
    $this->installEntitySchema('node');
  }

  /**
   * Tests that a complex data selector can be applied to nodes.
   */
  public function testNodeDataSelector() {
    $entity_manager = $this->container->get('entity.manager');
    $entity_manager->getStorage('node_type')
      ->create(['type' => 'page'])
      ->save();

    $node = $entity_manager->getStorage('node')
      ->create([
        'title' => 'test',
        'type' => 'page',
      ]);

    $user = $entity_manager->getStorage('user')
      ->create([
        'name' => 'test value',
      ]);

    $user->save();
    $node->setOwner($user);

    $rule = $this->expressionManager->createRule([
      'context_definitions' => [
        'node' => [
          'type' => 'entity:node',
          'label' => 'Node',
        ],
      ],
    ]);

    // Test that the long detailed data selector works.
    $rule->addCondition('rules_test_string_condition', ContextConfig::create()
      ->map('text', 'node:uid:0:entity:name:0:value')
    );

    // Test that the shortened data selector without list indices.
    $rule->addCondition('rules_test_string_condition', ContextConfig::create()
      ->map('text', 'node:uid:entity:name:value')
    );

    $rule->addAction('rules_test_log');
    $rule->setContextValue('node', $node);
    $rule->execute();
  }

  /**
   * Tests that a node is automatically saved after being changed in an action.
   */
  public function testNodeAutoSave() {
    $entity_manager = $this->container->get('entity.manager');
    $entity_manager->getStorage('node_type')
      ->create(['type' => 'page'])
      ->save();

    $node = $entity_manager->getStorage('node')
      ->create([
        'title' => 'test',
        'type' => 'page',
      ]);

    // We use the rules_test_node action plugin which marks its node context for
    // auto saving.
    // @see \Drupal\rules_test\Plugin\Action\TestNodeAction
    $action = $this->expressionManager->createAction('rules_test_node')
    ->setConfiguration([
      'context_definitions' => [
        'node' => [
          'type' => 'entity:node',
          'label' => 'Node',
        ],
        'title' => [
          'type' => 'string',
          'label' => 'Title',
        ],
      ]
    ] + ContextConfig::create()
        ->map('node', 'node')
        ->map('title', 'title')
        ->toArray()
    );

    $action->setContextValue('node', $node);
    $action->setContextValue('title', 'new title');
    $action->execute();

    $this->assertNotNull($node->id(), 'Node ID is set, which means that the node has been saved.');
  }

  /**
   * Tests that tokens in action parameters get replaced.
   */
  public function testTokenReplacements() {
    $entity_manager = $this->container->get('entity.manager');
    $entity_manager->getStorage('node_type')
      ->create(['type' => 'page'])
      ->save();

    $node = $entity_manager->getStorage('node')
      ->create([
        'title' => 'test',
        'type' => 'page',
      ]);

    $user = $entity_manager->getStorage('user')
      ->create([
        'name' => 'klausi',
      ]);

    $user->save();
    $node->setOwner($user);

    // Configure a simple rule with one action.
    $action = $this->expressionManager->createInstance('rules_action',
      ContextConfig::create()
        ->map('message', 'message')
        ->map('type', 'type')
        ->process('message', 'rules_tokens')
        ->setConfigKey('action_id', 'rules_system_message')
        ->toArray()
    );

    $rule = $this->expressionManager->createRule([
      'context_definitions' => [
        'node' => [
          'type' => 'entity:node',
        ],
        'message' => [
          'type' => 'string',
        ],
        'type' => [
          'type' => 'string',
        ],
      ],
    ]);
    $rule->setContextValue('node', $node);
    $rule->setContextValue('message', 'Hello [node:uid:entity:name:value]!');
    $rule->setContextValue('type', 'status');
    $rule->addExpressionObject($action);
    $rule->execute();

    $messages = drupal_set_message();
    $this->assertEqual($messages['status'][0]['message'], 'Hello klausi!');
  }

  /**
   * Tests that date formatting tokens on node fields get replaced.
   */
  public function testDateTokens() {
    $entity_manager = $this->container->get('entity.manager');
    $entity_manager->getStorage('node_type')
      ->create(['type' => 'page'])
      ->save();

    $node = $entity_manager->getStorage('node')
      ->create([
        'title' => 'test',
        'type' => 'page',
        // Set the created date to the first second in 1970.
        'created' => 1,
      ]);

    // Configure a simple rule with one action.
    $action = $this->expressionManager->createInstance('rules_action',
      ContextConfig::create()
        ->map('message', 'message')
        ->map('type', 'type')
        ->process('message', 'rules_tokens')
        ->setConfigKey('action_id', 'rules_system_message')
        ->toArray()
    );

    $rule = $this->expressionManager->createRule([
      'context_definitions' => [
        'node' => [
          'type' => 'entity:node',
        ],
        'message' => [
          'type' => 'string',
        ],
        'type' => [
          'type' => 'string',
        ],
      ],
    ]);
    $rule->setContextValue('node', $node);
    $rule->setContextValue('message', 'The node was created in the year [node:created:custom:Y]');
    $rule->setContextValue('type', 'status');
    $rule->addExpressionObject($action);
    $rule->execute();

    $messages = drupal_set_message();
    $this->assertEqual($messages['status'][0]['message'], 'The node was created in the year 1970');
  }

}
