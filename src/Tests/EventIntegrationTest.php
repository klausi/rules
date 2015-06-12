<?php

/**
 * @file
 * Contains \Drupal\rules\Tests\EventIntegrationTest.
 */

namespace Drupal\rules\Tests;

use Drupal\Core\Entity\EntityManager;
use Drupal\simpletest\KernelTestBase;

/**
 * Test for the Symfony event mapping to Rules events.
 *
 * @group rules
 */
class EventIntegrationTest extends KernelTestBase {

  /**
   * Modules to enable.
   *
   * @var array
   */
  public static $modules = ['rules', 'rules_test', 'system'];

  /**
   * {@inheritdoc}
   */
  public function setUp() {
    parent::setUp();
  }

  /**
   * Test that the user login hook triggers the Rules event listener.
   */
  public function testUserLoginEvent() {
    // Set a fake entity manager just for testing here.
    $entity_manager = $this->container->get('entity.manager');
    $test_entity_manager = new TestEntityManager($entity_manager);
    $this->container->set('entity.manager', $test_entity_manager);

    $account = $this->container->get('current_user');
    rules_user_login($account);
  }

}

class TestEntityManager implements \Drupal\Core\Entity\EntityManagerInterface {

  protected $originalEntityManager;

  public $isGetStorageCalled = FALSE;

  public function __construct(EntityManager $original_entity_manager) {
    $this->originalEntityManager = $original_entity_manager;
  }

  /**
   * {@inheritdoc}
   */
  public function getStorage($entity_type) {
    if ($entity_type == 'rules_component') {
      $this->isGetStorageCalled = TRUE;
    }
    return $this->originalEntityManager->getStorage($entity_type);
  }

  public function __call($name, $arguments) {
    call_user_func_array([$this->originalEntityManager, $name], $arguments);
  }

}
