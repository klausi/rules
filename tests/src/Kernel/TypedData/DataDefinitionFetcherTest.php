<?php

/**
 * @file
 * Contains Drupal\Tests\rules\Kernel\TypedData\DataDefinitionFetcherTest.
 */

namespace Drupal\Tests\rules\Kernel\TypedData;

use Drupal\Core\Entity\TypedData\EntityDataDefinition;
use Drupal\KernelTests\KernelTestBase;
use Drupal\field\Entity\FieldConfig;
use Drupal\field\Entity\FieldStorageConfig;
use Drupal\Core\Field\FieldStorageDefinitionInterface;

/**
 * @coversDefaultClass \Drupal\rules\TypedData\DataFetcher
 *
 * @group rules
 */
class DataDefinitionFetcherTest extends KernelTestBase {

  /**
   * The typed data manager.
   *
   * @var \Drupal\rules\TypedData\TypedDataManagerInterface
   */
  protected $typedDataManager;

  /**
   * A node used for testing.
   *
   * @var \Drupal\node\NodeInterface
   */
  protected $node;

  /**
   * An entity type manager used for testing.
   *
   * @var \Drupal\Core\Entity\EntityTypeManager
   */
  protected $entityTypeManager;

  /**
   * Modules to enable.
   *
   * @var array
   */
  public static $modules = ['rules', 'system', 'node', 'field', 'text', 'user'];

  /**
   * {@inheritdoc}
   */
  public function setUp() {
    parent::setUp();
    $this->typedDataManager = $this->container->get('typed_data_manager');

    $this->entityTypeManager = $this->container->get('entity_type.manager');
    $this->entityTypeManager->getStorage('node_type')
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

    $this->installSchema('system', ['sequences']);
    $this->installEntitySchema('user');
    $this->installEntitySchema('node');

    $this->node = $this->entityTypeManager->getStorage('node')
      ->create([
        'title' => 'test',
        'type' => 'page',
      ]);
  }

  /**
   * @covers ::fetchDefinitionByPropertyPath
   */
  public function testFetchingByBasicPropertyPath() {
    $target_definition = $this->node
      ->getTypedData()
      ->getDataDefinition()
      ->getPropertyDefinition('title')
      ->getItemDefinition()
      ->getPropertyDefinition('value');

    $fetched_definition = $this->typedDataManager
      ->getDataFetcher()
      ->fetchDefinitionByPropertyPath(
        $this->node->getTypedData()->getDataDefinition(),
        'title.0.value'
      );

    $this->assertSame($target_definition, $fetched_definition);
  }

  /**
   * @covers ::fetchDefinitionBySubPaths
   */
  public function testFetchingByBasicSubPath() {
    $target_definition = $this->node
      ->getTypedData()
      ->getDataDefinition()
      ->getPropertyDefinition('title')
      ->getItemDefinition()
      ->getPropertyDefinition('value');

    $fetched_definition = $this->typedDataManager
      ->getDataFetcher()
      ->fetchDefinitionBySubPaths(
        $this->node->getTypedData()->getDataDefinition(),
        ['title', '0', 'value']
      );

    $this->assertSame($target_definition, $fetched_definition);
  }

  /**
   * @covers ::fetchDefinitionByPropertyPath
   */
  public function testFetchingEntityReference() {
    $target_definition = $this->node
      ->getTypedData()
      ->getDataDefinition()
      ->getPropertyDefinition('uid')
      ->getItemDefinition()
      ->getPropertyDefinition('entity');

    $fetched_definition = $this->typedDataManager
      ->getDataFetcher()
      ->fetchDefinitionByPropertyPath(
        $this->node->getTypedData()->getDataDefinition(),
        'uid.entity'
      );

    $this->assertSame($target_definition, $fetched_definition);
  }

  /**
   * @cover fetchDefinitionByPropertyPath
   */
  public function testFetchingAcrossReferences() {
    $target_definition = $this->node
      ->getTypedData()
      ->getDataDefinition()
      ->getPropertyDefinition('uid')
      ->getItemDefinition()
      ->getPropertyDefinition('entity')
      ->getTargetDefinition()
      ->getPropertyDefinition('name')
      ->getItemDefinition()
      ->getPropertyDefinition('value');

    $fetched_definition = $this->typedDataManager
      ->getDataFetcher()
      ->fetchDefinitionByPropertyPath(
        $this->node->getTypedData()->getDataDefinition(),
        'uid.entity.name.value'
      );

    $this->assertSame($target_definition, $fetched_definition);
  }

  /**
   * @covers ::fetchDefinitionByPropertyPath
   */
  public function testFetchingAtValidPositions() {
    $target_definition = $this->node
      ->getTypedData()
      ->getDataDefinition()
      ->getPropertyDefinition('field_integer')
      ->getItemDefinition()
      ->getPropertyDefinition('value');

    $fetched_definition = $this->typedDataManager
      ->getDataFetcher()
      ->fetchDefinitionByPropertyPath(
        $this->node->getTypedData()->getDataDefinition(),
        'field_integer.0.value'
      );

    $this->assertSame($target_definition, $fetched_definition);

    $fetched_definition = $this->typedDataManager
      ->getDataFetcher()
      ->fetchDefinitionByPropertyPath(
        $this->node->getTypedData()->getDataDefinition(),
        'field_integer.1.value'
      );

    $this->assertSame($target_definition, $fetched_definition);
  }

  /**
   * @cover fetchDefinitionByPropertyPath
   * @expectedException \Drupal\Core\TypedData\Exception\MissingDataException
   * @expectedExceptionMessage Unable to apply data selector 'field_integer.0.value' at 'field_integer.0'
   */
  /*public function testFetchingValueAtInvalidPosition() {
    $this->node->field_integer->setValue([]);

    // This should trigger an exception.
    $this->typedDataManager->getDataFetcher()
      ->fetchDefinitionByPropertyPath($this->node->getTypedData(), 'field_integer.0.value')
      ->getValue();
  }

  /**
   * @cover fetchDefinitionByPropertyPath
   * @expectedException \InvalidArgumentException
   * @expectedExceptionMessage Unable to apply data selector 'field_invalid.0.value' at 'field_invalid'
   */
  /*public function festFetchingInvalidProperty() {
    // This should trigger an exception.
    $this->typedDataManager->getDataFetcher()
      ->fetchDefinitionByPropertyPath($this->node->getTypedData(), 'field_invalid.0.value')
      ->getValue();
  }

  /**
   * @cover fetchDefinitionByPropertyPath
   */
  /*public function testFetchingEmptyProperty() {
    $this->node->field_integer->setValue([]);

    $fetched_value = $this->typedDataManager->getDataFetcher()
      ->fetchDefinitionByPropertyPath($this->node->getTypedData(), 'field_integer')
      ->getValue();
    $this->assertEquals($fetched_value, []);
  }

  /**
   * @cover fetchDefinitionByPropertyPath
   * @expectedException \Drupal\Core\TypedData\Exception\MissingDataException
   */
  /*public function testFetchingNotExistingListItem() {
    $this->node->field_integer->setValue([]);

    // This will throw an exception.
    $this->typedDataManager->getDataFetcher()
      ->fetchDefinitionByPropertyPath($this->node->getTypedData(), 'field_integer.0')
      ->getValue();
  }

  /**
   * @cover fetchDefinitionByPropertyPath
   * @expectedException \Drupal\Core\TypedData\Exception\MissingDataException
   * @expectedExceptionMessageRegExp #Unable to apply data selector 'field_integer.0.value' at 'field_integer':.*#
   */
  /*public function testFetchingFromEmptyData() {
    $data_empty = $this->typedDataManager->create(EntityDataDefinition::create('node'));
    // This should trigger an exception.
    $this->typedDataManager->getDataFetcher()
      ->fetchDefinitionByPropertyPath($data_empty, 'field_integer.0.value')
      ->getValue();
  }

  /**
   * @cover fetchDefinitionByPropertyPath
   */
  /*public function testBubbleableMetadata() {
    $this->node->field_integer->setValue([]);
    // Save the node, so that it gets an ID and it has a cache tag.
    $this->node->save();
    // Also add a user for testing cache tags of references.
    $user = $this->entityTypeManager->getStorage('user')
      ->create([
        'name' => 'test',
        'type' => 'user',
      ]);
    $user->save();
    $this->node->uid->entity = $user;

    $bubbleable_metadata = new BubbleableMetadata();
    $this->typedDataManager->getDataFetcher()
      ->fetchDefinitionByPropertyPath($this->node->getTypedData(), 'title.value', $bubbleable_metadata)
      ->getValue();

    $expected = ['node:' . $this->node->id()];
    $this->assertEquals($expected, $bubbleable_metadata->getCacheTags());

    // Test cache tags of references are added correctly.
    $this->typedDataManager->getDataFetcher()
      ->fetchDefinitionByPropertyPath($this->node->getTypedData(), 'uid.entity.name', $bubbleable_metadata)
      ->getValue();

    $expected = ['node:' . $this->node->id(), 'user:' . $user->id()];
    $this->assertEquals($expected, $bubbleable_metadata->getCacheTags());
  }*/

}
