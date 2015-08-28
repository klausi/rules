<?php

/**
 * @file
 * Contains \Drupal\Tests\rules\Integration\Action\EntityDeleteTest.
 */

namespace Drupal\Tests\rules\Integration\Action;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Tests\rules\Integration\RulesEntityIntegrationTestBase;

/**
 * @coversDefaultClass \Drupal\rules\Plugin\RulesAction\EntityDelete
 * @group rules_actions
 */
class EntityDeleteTest extends RulesEntityIntegrationTestBase {

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

    $this->action = $this->actionManager->createInstance('rules_entity_delete');
  }

  /**
   * Tests the summary.
   *
   * @covers ::summary
   */
  public function testSummary() {
    $this->assertEquals('Delete entity', $this->action->summary());
  }

  /**
   * Tests the action execution.
   *
   * @covers ::execute
   */
  public function testActionExecution() {
    $entity = $this->prophesize(EntityInterface::class);
    $entity->delete()->shouldBeCalledTimes(1);
    // Wed don't care about the cache methods, but they will be called so we
    // have to mock them.
    $entity->getCacheContexts()->willReturn([]);
    $entity->getCacheTags()->willReturn([]);
    $entity->getCacheMaxAge()->willReturn(-1);

    $this->action->setContextValue('entity', $entity->reveal());
    $this->action->execute();
  }

}
