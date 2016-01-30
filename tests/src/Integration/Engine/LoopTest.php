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

  /**
   * Test the integrity check for loop item names that conflict with others.
   */
  public function testItemNameConflict() {
    $rule = $this->rulesExpressionManager->createRule();

    $loop = $this->rulesExpressionManager->createInstance('rules_loop', [
      'list' => 'string_list',
      'list_item' => 'existing_name',
    ]);

    $rule->addExpressionObject($loop);

    $violations = RulesComponent::create($rule)
      ->addContextDefinition('string_list', ContextDefinition::create('string')->setMultiple())
      ->addContextDefinition('existing_name', ContextDefinition::create('string'))
      ->checkIntegrity();

    $this->assertEquals(1, iterator_count($violations));
    $this->assertEquals(
      'List item name <em class="placeholder">existing_name</em> conflicts with an existing variable.',
      (string) $violations[0]->getMessage()
    );
  }

  /**
   * Tests that the specified list variable exists in the execution state.
   */
  public function testListExists() {
    $rule = $this->rulesExpressionManager->createRule();

    $loop = $this->rulesExpressionManager->createInstance('rules_loop', [
      'list' => 'unknown_list',
    ]);

    $rule->addExpressionObject($loop);

    $violations = RulesComponent::create($rule)
      ->checkIntegrity();

    $this->assertEquals(1, iterator_count($violations));
    $this->assertEquals(
      'List variable <em class="placeholder">unknown_list</em> does not exist.',
      (string) $violations[0]->getMessage()
    );
  }

  /**
   * Tests that a loop must have a list configured.
   */
  public function testMissingList() {
    $rule = $this->rulesExpressionManager->createRule();

    // Empty loop configuration, 'list' is missing.
    $loop = $this->rulesExpressionManager->createInstance('rules_loop', []);
    $rule->addExpressionObject($loop);

    $violations = RulesComponent::create($rule)
      ->checkIntegrity();

    $this->assertEquals(1, iterator_count($violations));
    $this->assertEquals(
      'List variable is missing.',
      (string) $violations[0]->getMessage()
    );
  }

}
