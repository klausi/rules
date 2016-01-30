<?php

/**
 * @file
 * Contains \Drupal\Tests\rules\Integration\Engine\LoopTest.
 */

namespace Drupal\Tests\rules\Integration\Engine;

use Drupal\rules\Context\ContextConfig;
use Drupal\rules\Context\ContextDefinition;
use Drupal\rules\Engine\RulesComponent;
use Drupal\Tests\rules\Integration\RulesEntityIntegrationTestBase;

/**
 * Test Rules execution with the loop plugin.
 *
 * @group rules
 */
class LoopTest extends RulesEntityIntegrationTestBase {

  /**
   * Tests that list items in the loop can be used during execution.
   */
  public function testListItemUsage() {
    // The rule contains a list of strings that will be concatenated into one
    // variable.
    $rule = $this->rulesExpressionManager->createRule();
    $rule->addAction('rules_variable_add', ContextConfig::create()
      ->setValue('type', 'string')
      ->setValue('value', '')
      ->provideAs('variable_added', 'result')
    );

    $loop = $this->rulesExpressionManager->createInstance('rules_loop', ['list' => 'string_list']);
    $loop->addAction('rules_data_set', ContextConfig::create()
      ->map('data', 'result')
      ->setValue('value', '{{result}} {{list_item}}')
      ->process('value', 'rules_tokens')
    );

    $rule->addExpressionObject($loop);

    $result = RulesComponent::create($rule)
      ->addContextDefinition('string_list', ContextDefinition::create('string')->setMultiple())
      ->provideContext('result')
      ->setContextValue('string_list', ['Hello', 'world', 'this', 'is', 'the',
        'loop',
      ])
      ->execute();

    $this->assertEquals(' Hello world this is the loop', $result['result']);
  }

  /**
   * Tests that list items can be renamed for usage in nested loops.
   */
  public function testListItemRenaming() {
    // The rule contains a list of strings that will be concatenated into one
    // variable.
    $rule = $this->rulesExpressionManager->createRule();
    $rule->addAction('rules_variable_add', ContextConfig::create()
      ->setValue('type', 'string')
      ->setValue('value', '')
      ->provideAs('variable_added', 'result')
    );

    $outer_loop = $this->rulesExpressionManager->createInstance('rules_loop', [
      'list' => 'outer_list',
      'list_item' => 'outer_item',
    ]);
    $outer_loop->addAction('rules_data_set', ContextConfig::create()
      ->map('data', 'result')
      ->setValue('value', '{{result}} {{outer_item}}')
      ->process('value', 'rules_tokens')
    );

    $inner_loop = $this->rulesExpressionManager->createInstance('rules_loop', [
      'list' => 'inner_list',
      'list_item' => 'inner_item',
    ]);
    $inner_loop->addAction('rules_data_set', ContextConfig::create()
      ->map('data', 'result')
      ->setValue('value', '{{result}} {{inner_item}}')
      ->process('value', 'rules_tokens')
    );

    $outer_loop->addExpressionObject($inner_loop);
    $rule->addExpressionObject($outer_loop);

    $result = RulesComponent::create($rule)
      ->addContextDefinition('outer_list', ContextDefinition::create('string')->setMultiple())
      ->addContextDefinition('inner_list', ContextDefinition::create('string')->setMultiple())
      ->provideContext('result')
      ->setContextValue('outer_list', ['Outer 1', 'Outer 2'])
      ->setContextValue('inner_list', ['Inner 1', 'Inner 2', 'Inner 3'])
      ->execute();

    $this->assertEquals(' Outer 1 Inner 1 Inner 2 Inner 3 Outer 2 Inner 1 Inner 2 Inner 3', $result['result']);
  }

}
