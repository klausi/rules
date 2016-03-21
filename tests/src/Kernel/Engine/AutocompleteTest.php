<?php

/**
 * @file
 * Contains \Drupal\Tests\rules\Kernel\Engine\AutocompleteTest.
 */

namespace Drupal\Tests\rules\Kernel\Engine;

use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\field\Entity\FieldConfig;
use Drupal\field\Entity\FieldStorageConfig;
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
  public static $modules = ['field', 'rules', 'node', 'user'];

  /**
   * {@inheritdoc}
   */
  public function setUp() {
    parent::setUp();

    $this->installEntitySchema('user');

    $entity_type_manager = $this->container->get('entity_type.manager');
    $entity_type_manager->getStorage('node_type')
      ->create(['type' => 'page'])
      ->save();

    // Create a multi-value integer field for testing.
    FieldStorageConfig::create([
      'field_name' => 'field_integer',
      'type' => 'integer',
      'entity_type' => 'node',
      'cardinality' => FieldStorageDefinitionInterface::CARDINALITY_UNLIMITED,
    ])->save();
    FieldConfig::create([
      'field_name' => 'field_integer',
      'entity_type' => 'node',
      'bundle' => 'page',
    ])->save();
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

    $this->assertSame([
      [
        'value' => 'entity',
        'label' => 'entity',
      ],
      [
        'value' => 'entity.',
        'label' => 'entity...',
      ],
    ], $results);
  }

  /**
   * Test various node example data selectors.
   */
  public function testNodeAutocomplete() {
    $rule = $this->expressionManager->createRule();
    $rule->addAction('rules_data_set');

    $component = RulesComponent::create($rule)
      ->addContextDefinition('node', ContextDefinition::create('entity:node:page'));

    // Tests that "node.uid.en" returns the suggestion "node.uid.entity".
    $results = $component->autocomplete('node.uid.en');
    $this->assertSame([
      [
        'value' => 'node.uid.entity',
        'label' => 'node.uid.entity',
      ],
      [
        'value' => 'node.uid.entity.',
        'label' => 'node.uid.entity...',
      ],
    ], $results);

    // Tests that "node." returns all available fields on a node.
    $results = $component->autocomplete('node.');
    $expected = [
      [
        'value' => 'node.changed',
        'label' => 'node.changed',
      ],
      [
        'value' => 'node.changed.',
        'label' => 'node.changed...',
      ],
      [
        'value' => 'node.created',
        'label' => 'node.created',
      ],
      [
        'value' => 'node.created.',
        'label' => 'node.created...',
      ],
      [
        'value' => 'node.default_langcode',
        'label' => 'node.default_langcode',
      ],
      [
        'value' => 'node.default_langcode.',
        'label' => 'node.default_langcode...',
      ],
      [
        'value' => 'node.field_integer',
        'label' => 'node.field_integer',
      ],
      [
        'value' => 'node.field_integer.',
        'label' => 'node.field_integer...',
      ],
      [
        'value' => 'node.langcode',
        'label' => 'node.langcode',
      ],
      [
        'value' => 'node.langcode.',
        'label' => 'node.langcode...',
      ],
      [
        'value' => 'node.nid',
        'label' => 'node.nid',
      ],
      [
        'value' => 'node.nid.',
        'label' => 'node.nid...',
      ],
      [
        'value' => 'node.promote',
        'label' => 'node.promote',
      ],
      [
        'value' => 'node.promote.',
        'label' => 'node.promote...',
      ],
      [
        'value' => 'node.revision_log',
        'label' => 'node.revision_log',
      ],
      [
        'value' => 'node.revision_log.',
        'label' => 'node.revision_log...',
      ],
      [
        'value' => 'node.revision_timestamp',
        'label' => 'node.revision_timestamp',
      ],
      [
        'value' => 'node.revision_timestamp.',
        'label' => 'node.revision_timestamp...',
      ],
      [
        'value' => 'node.revision_translation_affected',
        'label' => 'node.revision_translation_affected',
      ],
      [
        'value' => 'node.revision_translation_affected.',
        'label' => 'node.revision_translation_affected...',
      ],
      [
        'value' => 'node.revision_uid',
        'label' => 'node.revision_uid',
      ],
      [
        'value' => 'node.revision_uid.',
        'label' => 'node.revision_uid...',
      ],
      [
        'value' => 'node.status',
        'label' => 'node.status',
      ],
      [
        'value' => 'node.status.',
        'label' => 'node.status...',
      ],
      [
        'value' => 'node.sticky',
        'label' => 'node.sticky',
      ],
      [
        'value' => 'node.sticky.',
        'label' => 'node.sticky...',
      ],
      [
        'value' => 'node.title',
        'label' => 'node.title',
      ],
      [
        'value' => 'node.title.',
        'label' => 'node.title...',
      ],
      [
        'value' => 'node.type',
        'label' => 'node.type',
      ],
      [
        'value' => 'node.type.',
        'label' => 'node.type...',
      ],
      [
        'value' => 'node.uid',
        'label' => 'node.uid',
      ],
      [
        'value' => 'node.uid.',
        'label' => 'node.uid...',
      ],
      [
        'value' => 'node.uuid',
        'label' => 'node.uuid',
      ],
      [
        'value' => 'node.uuid.',
        'label' => 'node.uuid...',
      ],
      [
        'value' => 'node.vid',
        'label' => 'node.vid',
      ],
      [
        'value' => 'node.vid.',
        'label' => 'node.vid...',
      ],
    ];
    $this->assertSame($expected, $results);

    // Tests that "node.uid.entity.na" returns "node.uid.entity.name".
    $results = $component->autocomplete('node.uid.entity.na');
    $this->assertSame([
      [
        'value' => 'node.uid.entity.name',
        'label' => 'node.uid.entity.name',
      ],
      [
        'value' => 'node.uid.entity.name.',
        'label' => 'node.uid.entity.name...',
      ],
    ], $results);

    // A multi-valued field should show numeric indices suggestions.
    $results = $component->autocomplete('node.field_integer.');
    $this->assertSame([
      [
        'value' => 'node.field_integer.0',
        'label' => 'node.field_integer.0',
      ],
      [
        'value' => 'node.field_integer.0.',
        'label' => 'node.field_integer.0...',
      ],
      [
        'value' => 'node.field_integer.1',
        'label' => 'node.field_integer.1',
      ],
      [
        'value' => 'node.field_integer.1.',
        'label' => 'node.field_integer.1...',
      ],
      [
        'value' => 'node.field_integer.2',
        'label' => 'node.field_integer.2',
      ],
      [
        'value' => 'node.field_integer.2.',
        'label' => 'node.field_integer.2...',
      ],
      [
        'value' => 'node.field_integer.value',
        'label' => 'node.field_integer.value',
      ],
    ], $results);

    // A single-valued field should not show numeric indices suggestions.
    $results = $component->autocomplete('node.title.');
    $this->assertSame([
      [
        'value' => 'node.title.value',
        'label' => 'node.title.value',
      ],
    ], $results);

    // A single-valued field should not show numeric indices suggestions.
    $results = $component->autocomplete('n');
    $this->assertSame([
      [
        'value' => 'node',
        'label' => 'node',
      ],
      [
        'value' => 'node.',
        'label' => 'node...',
      ],
    ], $results);
  }

  /**
   * Tests that autocomplete results for a flat list are correct.
   */
  public function testListAutocomplete() {
    $rule = $this->expressionManager->createRule();
    $rule->addAction('rules_data_set');

    $context_definition = ContextDefinition::create('integer');
    $context_definition->setMultiple();
    $component = RulesComponent::create($rule)
      ->addContextDefinition('list', $context_definition);

    $results = $component->autocomplete('list.');
    $this->assertSame([
      [
        'value' => 'list.0',
        'label' => 'list.0',
      ],
      [
        'value' => 'list.1',
        'label' => 'list.1',
      ],
      [
        'value' => 'list.2',
        'label' => 'list.2',
      ],
    ], $results);
  }

}
