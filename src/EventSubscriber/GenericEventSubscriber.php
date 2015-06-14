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
    // Register this listener for every event that is used by a reaction rule.
    $events = [];
    $callback = ['onRulesEvent', 100];

    // @todo this does not work, the plugins are simply not ready at this point.
    //   Try using the state system next.
    try {
      $storage = \Drupal::entityManager()->getStorage('rules_reaction_rule');
    }
    catch (\Drupal\Component\Plugin\Exception\PluginNotFoundException $e) {
      // This can be called early and in case our reaction rule entity type does
      // not exist yet we just return no events.
      return [];
    }

    $registered_event_names = $storage->getRegisteredEvents();

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
    // Load reaction rule config entities by $event_name.
    $storage = $this->entityManager->getStorage('rules_reaction_rule');
    // @todo Only load active reaction rules here.
    $configs = $storage->loadByProperties(['event' => $event_name]);

    // Loop over all rules and execute them.
    foreach ($configs as $rules_config) {
      $reaction_rule = $rules_config->getExpression();
      $subject = $event->getSubject();
      $context_names = array_keys($reaction_rule->getContextDefinitions());

      // Set the subject as the first context of the rule.
      if ($subject) {
        $context_name = array_shift($context_names);
        $reaction_rule->setContextValue($context_name, $subject);
      }
      // Set the rest of arguments as further context values on the rule.
      foreach ($event->getArguments() as $name => $value) {
        $reaction_rule->setContextValue($name, $value);
      }

      $reaction_rule->execute();
    }
  }

}
