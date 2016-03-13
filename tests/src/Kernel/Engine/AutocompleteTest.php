<?php

/**
 * @file
 * Contains \Drupal\Tests\rules\Kernel\Engine\AutocompleteTest.
 */

namespace Drupal\Tests\rules\Kernel\Engine;

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
   * The sample rules component used for testing autocomplete suggestions.
   *
   * @var \Drupal\rules\Engine\RulesComponent
   */
  protected $component;

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

    $rule = $this->expressionManager->createRule();
    $rule->addAction('rules_data_set');

    $this->component = RulesComponent::create($rule)
      ->addContextDefinition('node', ContextDefinition::create('entity:node'));
  }

  /**
   * Tests autocompletion works for a variable in the metadata state.
   */
  public function testAutocomplete() {
    $rule = $this->expressionManager->createRule();
    $action = $this->expressionManager->createAction('rules_data_set');
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
    $results = $this->component->autocomplete('node.uid.en');

    $this->assertSame(['node.uid.entity'], $results);
  }

  /**
   * Tests that "node." returns all available fields on a node.
   */
  public function testAllNodeFields() {
    $results = $this->component->autocomplete('node.');

    $expected = [
      'node.changed',
      'node.created',
      'node.default_langcode',
      'node.langcode',
      'node.nid',
      'node.promote',
      'node.revision_log',
      'node.revision_timestamp',
      'node.revision_translation_affected',
      'node.revision_uid',
      'node.status',
      'node.sticky',
      'node.title',
      'node.type',
      'node.uid',
      'node.uuid',
      'node.vid',
    ];
    $this->assertSame($expected, $results);
  }

}
