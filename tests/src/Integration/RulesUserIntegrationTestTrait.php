<?php

/**
 * @file
 * Contains \Drupal\Tests\rules\Integration\RulesUserIntegrationTestTrait.
 */

namespace Drupal\Tests\rules\Integration;

use Drupal\user\UserInterface;

/**
 * Trait for Rules integration tests with user entities.
 */
trait RulesUserIntegrationTestTrait {

  /**
   * Creates a mocked user.
   *
   * @return UserInterface|\Prophecy\Prophecy\ProphecyInterface
   *   The mocked user.
   */
  protected function getMockedUser() {
    $account = $this->prophesize(UserInterface::class);
    // Cache methods are irrelevant for the tests but might be called.
    $account->getCacheContexts()->willReturn([]);
    $account->getCacheTags()->willReturn([]);
    $account->getCacheMaxAge()->willReturn(0);
    return $account;
  }

}
