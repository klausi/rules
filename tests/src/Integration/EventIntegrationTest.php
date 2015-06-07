<?php

/**
 * @file
 * Contains \Drupal\Tests\rules\EventIntegrationTest.
 */

namespace Drupal\Tests\rules\Integration;

use Drupal\rules\Engine\RulesEventManager;
use Drupal\Tests\rules\Integration\RulesIntegrationTestBase;

/**
 * Checks that Rules event plugins can be registered and are found.
 */
class EventIntegrationTest extends RulesIntegrationTestBase {

  /**
   * The Rules event plugin manager.
   *
   * @var \Drupal\rules\Engine\RulesEventManager
   */
  protected $eventManager;

  /**
   * {@inheritdoc}
   */
  public function setUp() {
    parent::setUp();
    $this->moduleHandler->expects($this->any())
      ->method('getModuleDirectories')
      ->willReturn(['rules' => __DIR__ . '/../../..']);
    $this->eventManager = new RulesEventManager($this->moduleHandler);
  }

  /**
   * Tests that the event plugin can be instantiated.
   */
  public function testUserLoginEventInvocation() {
    $event = $this->eventManager->createInstance('rules_user_login');
    $user_context_definition = $event->getContextDefinition('account');
    $this->assertSame('entity:user', $user_context_definition->getDataType());
  }

}
