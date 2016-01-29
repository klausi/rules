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
  public function testListItemSelector() {
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
      //->setValue('value', 'bam')
      ->process('value', 'rules_tokens')
    );

    $rule->addExpressionObject($loop);

    $result = RulesComponent::create($rule)
      ->addContextDefinition('string_list', ContextDefinition::create('string')->setMultiple())
      ->provideContext('result')
      ->setContextValue('string_list', ['Hello', 'world', 'this', 'is', 'the', 'loop'])
      ->execute();

    $this->assertEquals(' Hello world this is the loop', $result['result']);
  }

}
