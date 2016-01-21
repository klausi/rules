<?php

/**
 * @file
 * Contains \Drupal\Tests\rules\Integration\Engine\IntegrityCheckTest.
 */

namespace Drupal\Tests\rules\Integration\Engine;

use Drupal\rules\Context\ContextConfig;
use Drupal\rules\Engine\ConfigurationState;
use Drupal\Tests\rules\Integration\RulesEntityIntegrationTestBase;

/**
 * Test the integrity check functionality during configuration time.
 *
 * @group rules
 */
class IntegrityCheckTest extends RulesEntityIntegrationTestBase {

  /**
   * Tests that the integrity check can be invoked.
   */
  public function testIntegrityCheck() {
    $rule = $this->rulesExpressionManager->createRule();
    $rule->addAction('rules_entity_save', ContextConfig::create()
      ->map('entity', 'entity')
    );

    $config_state = ConfigurationState::create([
      'entity' => $this->typedDataManager->createDataDefinition('entity'),
    ]);
    $violation_list = $rule->checkIntegrity($config_state);
    $this->assertEquals(iterator_count($violation_list), 0);
  }

  /**
   * Tests that a wrongly configured variable name triggers a violation.
   */
  public function testUnknownVariable() {
    $rule = $this->rulesExpressionManager->createRule();
    $rule->addAction('rules_entity_save', ContextConfig::create()
      ->map('entity', 'unknown_variable')
    );

    $config_state = ConfigurationState::create([]);
    $violation_list = $rule->checkIntegrity($config_state);
    $this->assertEquals(iterator_count($violation_list), 1);
    $violation = $violation_list->getIterator()->current();
    $this->assertEquals('Data selector unknown_variable for context entity is invalid.', $violation->getMessage());
  }

  /**
   * Tests that the integrity check with UUID works.
   */
  public function testCheckUuid() {
    $rule = $this->rulesExpressionManager->createRule();
    // Just use a rule with 2 dummy actions.
    $rule->addAction('rules_entity_save', ContextConfig::create()
          ->map('entity', 'unknown_variable_1'))
        ->addAction('rules_entity_save', ContextConfig::create()
          ->map('entity', 'unknown_variable_2'));

    $config_state = ConfigurationState::create([
      'entity' => $this->typedDataManager->createDataDefinition('entity'),
    ]);

    $all_violations = $rule->checkIntegrity($config_state);
    $this->assertEquals(2, iterator_count($all_violations));

    // Get the UUID of the second action.
    $iterator = $rule->getIterator();
    $iterator->next();
    $uuid = $iterator->key();

    $uuid_violations = $all_violations->getFor($uuid);
    $this->assertEquals(1, count($uuid_violations));
    $violation = reset($uuid_violations);
    $this->assertEquals('Data selector unknown_variable_2 for context entity is invalid.', $violation->getMessage());
  }

}
