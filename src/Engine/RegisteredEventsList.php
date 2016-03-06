<?php

/**
 * @file
 * Contains \Drupal\rules\Engine\RegisteredEventsList.
 */

namespace Drupal\rules\Engine;

/**
 * Stores the registered reaction rule IDs per event name.
 */
class RegisteredEventsList extends \ArrayObject {

  public function set($event_base_name, $rule_id, $event_name) {
    $this[$event_base_name][$event_name][$rule_id] = TRUE;
  }

  public function hasEvent($event_base_name) {
    return isset($this[$event_base_name]);
  }

  public function removeRule($rule_id) {
    foreach ($this as &$registry) {
      foreach ($registry as &$rule_list) {
        unset($rule_list[$rule_id]);
      }
    }
  }

  public function getEventNames() {
    return array_keys($this->getArrayCopy());
  }

  public function getRuleIdsFor($event_base_name, array $qualified_event_names) {
    if (empty($this[$event_base_name])) {
      return [];
    }
    $ids = [];
    if (!empty($this[$event_base_name][$event_base_name])) {
      $ids = $this[$event_base_name][$event_base_name];
    }
    foreach ($qualified_event_names as $qualified_event_name) {
      if (!empty($this[$event_base_name][$qualified_event_name])) {
        $ids += $this[$event_base_name][$qualified_event_name];
      }
    }
    return array_keys($ids);
  }

}
