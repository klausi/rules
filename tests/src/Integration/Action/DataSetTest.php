<?php

/**
 * @file
 * Contains \Drupal\Tests\rules\Integration\Action\DataSetTest.
 */

namespace Drupal\Tests\rules\Integration\Action;

use Drupal\rules\Core\RulesActionInterface;
use Drupal\Tests\rules\Integration\RulesIntegrationTestBase;


/**
 * @coversDefaultClass \Drupal\rules\Plugin\RulesAction\DataSet
 * @group rules_actions
 */
class DataSetTest extends RulesIntegrationTestBase {

  /**
   * The action to be tested.
   *
   * @var RulesActionInterface
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
   * Tests that primitive values can be set.
   *
   * @covers ::execute
   */
  public function testPrimitiveValues() {
    $this->action->setContextValue('data', 'original')
      ->setContextValue('value', 'replacement');
    $this->action->execute();

    $this->assertSame('replacement', $this->action->getContextValue('data'));
  }

}
