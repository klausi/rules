<?php

/**
 * @file
 * Contains \Drupal\Tests\rules\Integration\Engine\PrepareMetadataTest.
 */

namespace Drupal\Tests\rules\Integration\Engine;

use Drupal\rules\Context\ContextConfig;
use Drupal\rules\Engine\ExecutionMetadataState;
use Drupal\Tests\rules\Integration\RulesEntityIntegrationTestBase;

/**
 * Tests that the setup of the execution metadata state for an expression works.
 *
 * @group rules
 */
class PrepareMetadataTest extends RulesEntityIntegrationTestBase {

  /**
   * Tests that a variable can be added by an action and is then available.
   */
  public function testAddingVariable() {
    $rule = $this->rulesExpressionManager->createRule();
    $rule->addAction('rules_variable_add', ContextConfig::create()
      ->setValue('type', 'string')
      ->setValue('value', '')
      ->provideAs('variable_added', 'result')
    );

    $state = ExecutionMetadataState::create();
    $rule->prepareExecutionMetadataState($state);
    $this->assertTrue($state->hasDataDefinition('result'));
  }

}
