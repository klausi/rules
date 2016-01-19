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
    $violation_list = $rule->integrityCheck($config_state);
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
    $violation_list = $rule->integrityCheck($config_state);
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
          ->map('entity', 'entity'))
        ->addAction('rules_entity_save', ContextConfig::create()
          ->map('entity', 'entity'));

    $config_state = ConfigurationState::create([
      'entity' => $this->typedDataManager->createDataDefinition('entity'),
    ]);
    // Get the UUID of the second action.
    $iterator = $rule->getIterator();
    $iterator->next();
    $uuid = $iterator->key();
    $rule->integrityCheckUntil($uuid, $config_state);
    // @todo PHPunit has no ->pass() method, so this is ugly.
    $this->assertNull(NULL, 'Integrity check invocation works.');
  }

}
