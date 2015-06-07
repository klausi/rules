<?php

/**
 * @file
 * Contains \Drupal\rules\EventSubscriber\GenericEventSubscriber.
 */

namespace Drupal\views\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Subsscribes to Symfony events and maps them to Rules events.
 */
class GenericEventSubscriber implements EventSubscriberInterface {

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    // Can we read config here and get the list of active reaction rules with
    // their event names?
  }

}
