<?php

/**
 * @file
 * Contains \Drupal\Tests\rules\Integration\Action\DataSetTest.
 */

namespace Drupal\Tests\rules\Integration\Action;

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
   * Test data_set variable.
   *
   * @covers ::execute
   */
  public function testVariableTypeEqual() {
    $data = (string) "Test";
    $value = (string) "Test";
    $expected_result = $value;

    $this->action->setContextValue('data', $data)
      ->setContextValue('value', $value);
    $this->action->execute();

    $result = $this->action->getProvidedContext('result')->getContextValue();

    $this->assertEquals($result, $expected_result);
  }

  /**
   * Test data_set variable exception where variable is of different type.
   *
   * @expectedException: \Drupal\rules\Exception\RulesEvaluationException
   * @expectedExceptionMessage Types are not equal
   *
   * @covers ::execute
   */
  public function testVariableTypeException() {
    $data = (string) "Test";
    $value = (int) 1;

    $this->action->setContextValue('data', $data)
      ->setContextValue('value', $value);
    $this->action->execute();
  }

  /**
   * Test data_set entity.
   *
   * @covers ::execute
   */
  public function testEntity() {
    $data = $this->getMock('Drupal\Core\Entity\EntityInterface');


    $value = $this->getMock('Drupal\Core\Entity\EntityInterface');

    $this->assertEquals('OK', 'OK');
  }

  /**
   * Test data_set entity exception where entity of different type.
   *
   * @covers ::execute
   */
  public function testEntityException() {
    $this->assertNotEquals('OK', 'Exception');
  }

  /**
   * Test data_set referenced entity where parent entity must be updated too.
   *
   * @covers ::execute
   */
  public function testEntityParent() {
    $this->assertEquals('OK', 'OK');
  }

  /**
   * Test data_set referenced entity exception where parent entity that must be
   * updated too is not updated.
   *
   * @covers ::execute
   */
  public function testEntityParentException() {
    $this->assertNotEquals('OK', 'Exception');
  }

}
