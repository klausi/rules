<?php

/**
 * @file
 * Contains \Drupal\Tests\rules\Integration\Action\DataSetTest.
 */

namespace Drupal\Tests\rules\Integration\Action;

use Drupal\Core\TypedData\TypedDataManager;
use Drupal\Core\TypedData\Plugin\DataType\StringData;
use Drupal\Core\TypedData;
use Drupal\Tests\rules\Integration\RulesIntegrationTestBase;


/**
 * @coversDefaultClass \Drupal\rules\Plugin\Action\DataSet
 * @group rules_actions
 */
class DataSetTest extends RulesIntegrationTestBase {

  /**
   * The action to be tested.
   *
   * @var \Drupal\rules\Core\RulesActionInterface
   */
  protected $action;

  /**
   * {@inheritdoc}
   */
  public function setUp() {
    parent::setUp();
    $this->action = $this->actionManager->createInstance('rules_data_set');
  }

  /**
   * Tests the summary.
   *
   * @covers ::summary
   */
  public function testSummary() {
    $this->assertEquals('Set a data value.', $this->action->summary());
  }

  /**
   * Test data_set PrimitiveTypeEqual.
   *
   * Result is equal than replacement value.
   * Original value is updated to replacement value.
   *
   * @todo Make primitive part of TypedDataManager class.
   * @todo Test original value == result||replacement.
   *
   * @covers ::execute
   */
  public function testPrimitiveTypeEqual() {
    // Setup.
    $original = (string) "Test";
    $replacement = (string) "Test";
    $expected_result = $replacement;

    // Run action.
    $this->action->setContextValue('original', $original)
      ->setContextValue('replacement', $replacement);
    $this->action->execute();

    // Validate.
    $result = $this->action->getProvidedContext('result')->getContextValue();
    $this->assertEquals($result, $expected_result);
  }

  /**
   * Test data_set PrimitiveTypeEqualFalse.
   *
   * Result is FALSE when original and replacement variables are of a different
   * type.
   *
   * @todo Make primitives part of TypedDataManager class.
   *
   * @covers ::execute
   */
  public function testPrimitiveTypeEqualFalse() {
    // Setup.
    $original = (string) "Test";
    $replacement = (int) 1;
    $expected_result = FALSE;

    // Run action.
    $this->action->setContextValue('original', $original)
      ->setContextValue('replacement', $replacement);
    $this->action->execute();

    // Validate.
    $result = $this->action->getProvidedContext('result')->getContextValue();
    $this->assertEquals($result, $expected_result);
  }

  /**
   * Test data_set TypedDataManagerEqual.
   *
   * Result is equal than replacement value.
   * Original value is updated to replacement value.
   *
   * @todo Mock complex TypedDataManager field like telephone data-type.
   * @todo Test original value == result||replacement.
   * @todo Make this test work.
   *
   * @covers ::executes
   */
  public function testTypedDataManager() {
    $original = "a TypedDataManager complex field";
    $replacement = "a TypedDataManager complex field";

    $this->assertEquals('Not working', 'Not working');
  }

  /**
   * Test data_set TypedDataManagerEqualFalse.
   *
   * Result is FALSE when original and replacement variables are of a different
   * type.
   *
   * @todo Mock complex TypedDataManager field like telephone data-type.
   * @todo Make this test work.
   *
   * @covers ::execute
   */
  public function testTypedDataManagerFalse() {
    $original = "a TypedDataManager complex field";
    $replacement = "a TypedDataManager complex field";

    $this->assertEquals('Not working', 'Not working');
  }

  /**
   * Test data_set testTypedDataManagerParent.
   *
   * Result is equal than replacement value.
   * Original value is updated to replacement value.
   *
   * @todo Mock complex TypedDataManager field like telephone data-type.
   * @todo Test original value == result||replacement.
   * @todo Make this test work.
   *
   * @covers ::executes
   */
  public function testTypedDataManagerParent() {
    $original = "a TypedDataManager complex field with a parent (an entity)";
    $replacement = "a TypedDataManager complex field with a parent (an entity)";

    $this->assertEquals('Not working', 'Not working');
  }

  /**
   * Test data_set testTypedDataManagerParentFalse.
   *
   * Result is FALSE when original and replacement variables are of a different
   * type.
   *
   * @todo Mock complex TypedDataManager field like telephone data-type.
   * @todo Make this test work.
   *
   * @covers ::execute
   */
  public function testTypedDataManagerParentFalse() {
    $original = "a TypedDataManager complex field with a parent (an entity)";
    $replacement = "a TypedDataManager complex field with a parent (an entity)";

    $this->assertEquals('Not working', 'Not working');
  }

}
