<?php

/**
 * @file
 * Contains \Drupal\rules\EventSubscriber\GenericEventSubscriber.
 */

namespace Drupal\rules\EventSubscriber;

use Drupal\Core\Entity\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

/**
 * Subsscribes to Symfony events and maps them to Rules events.
 */
class GenericEventSubscriber implements EventSubscriberInterface {

  /**
   * The entity manager used for loading reaction rule config entities.
   *
   * @var \Drupal\Core\Entity\EntityManagerInterface
   */
  protected $entityManager;

  /**
   * Constructor.
   *
   * @param \Drupal\Core\Entity\EntityManagerInterface $entity_manager
   *   The entity manager.
   */
  public function __construct(EntityManagerInterface $entity_manager) {
    $this->entityManager = $entity_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    // Can we read config here and get the list of active reaction rules with
    // their event names?
    $events = [];
    $callback = ['onRulesEvent', 100];
    // @todo The 'rules_user_login' event is hard-coded here, but this should
    //   be a list of active configured reation rule events.
    $registered_event_names = ['rules_user_login'];

    foreach ($registered_event_names as $event_name) {
      $events[$event_name][] = $callback;
    }
    return $events;
  }

  /**
   * Reacts on the given event and invokes configured reaction rules.
   *
   * @param \Symfony\Component\EventDispatcher\GenericEvent $event
   *   The event object containing context for the event.
   * @param string $event_name
   *   The vent name.
   */
  public function onRulesEvent(GenericEvent $event, $event_name) {
    // 1. Load reaction rule config entities by $event_name.
    // 2. For each entity get reaction rule expression.
    // 3. Set context value from $event into the rule expression.
    // 4. Execute reaction rule.

    // @todo This is just a placeholder for now so that we can test this
    //   invocation. Should be reaction_rule once we have that.
    $this->entityManager->getStorage('rules_component');
  }

}
