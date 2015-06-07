<?php

/**
 * @file
 * Contains \Drupal\rules\Engine\EventDispatcher
 */

namespace Drupal\rules\Engine;

/**
 * Handles Rules event invocations and dispatches to configured rules.
 */
class EventDispatcher {

  /**
   * Invokes configured rules for the given event.
   *
   * @param string $event_name
   *   The event's name.
   * @param ...
   *   Pass parameters for the context of this event, as defined in
   *   *rules_events.yml. Example given:
   *   @code
   *     $dispatcher->invokeEvent('rules_user_login', $account);
   *   @endcode
   */
  public function invokeEvent() {
    // Load all Rules configs that listen to the event and execute them.
    // Throw exception if the event is unknown.
  }
}
