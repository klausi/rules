<?php

/**
 * @file
 * Contains \Drupal\rules\Entity\ReactionRuleStorage.
 */

namespace Drupal\rules\Entity;

use Drupal\Core\Config\Entity\ConfigEntityStorage;

/**
 * Storage handler for reaction rule config entities.
 */
class ReactionRuleStorage extends ConfigEntityStorage {

  /**
   * Returns a list of event names that are used by active reaction rules.
   *
   * @return string[]
   *   The list of event names keyed by event name.
   */
  public function getRegisteredEvents() {
    $events = [];
    foreach ($this->loadMultiple() as $rules_config) {
      $event = $rules_config->getEvent();
      if ($event && !isset($events[$event])) {
        $events[$event] = $event;
      }
    }
    return $events;
  }

}
