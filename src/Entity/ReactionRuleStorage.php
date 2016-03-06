<?php

/**
 * @file
 * Contains \Drupal\rules\Entity\ReactionRuleStorage.
 */

namespace Drupal\rules\Entity;

use Drupal\Component\Uuid\UuidInterface;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Config\Entity\ConfigEntityStorage;
use Drupal\Core\DrupalKernelInterface;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\Core\State\StateInterface;
use Drupal\rules\Core\RulesEventManager;
use Drupal\rules\Engine\RegisteredEventsList;
use Symfony\Component\DependencyInjection\ContainerInterface;


/**
 * Storage handler for reaction rule config entities.
 *
 * @todo Create an interface for this.
 */
class ReactionRuleStorage extends ConfigEntityStorage {

  /**
   * The state service.
   *
   * @var \Drupal\Core\State\StateInterface
   */
  protected $stateService;

  /**
   * The Drupal kernel.
   *
   * @var \Drupal\Core\DrupalKernelInterface.
   */
  protected $drupalKernel;

  /**
   * The event manager.
   *
   * @var \Drupal\rules\Core\RulesEventManager
   */
  protected $eventManager;

  /**
   * Constructs a ReactionRuleStorage object.
   *
   * @param \Drupal\Core\Entity\EntityTypeInterface $entity_type
   *   The entity type definition.
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   The config factory service.
   * @param \Drupal\Component\Uuid\UuidInterface $uuid_service
   *   The UUID service.
   * @param \Drupal\Core\Language\LanguageManagerInterface $language_manager
   *   The language manager.
   * @param \Drupal\Core\State\StateInterface $state_service
   *   The state service.
   * @param \Drupal\rules\Core\RulesEventManager $event_manager
   *   The Rules event manager.
   */
  public function __construct(EntityTypeInterface $entity_type, ConfigFactoryInterface $config_factory, UuidInterface $uuid_service, LanguageManagerInterface $language_manager, StateInterface $state_service, DrupalKernelInterface $drupal_kernel, RulesEventManager $event_manager) {
    parent::__construct($entity_type, $config_factory, $uuid_service, $language_manager);

    $this->stateService = $state_service;
    $this->drupalKernel = $drupal_kernel;
    $this->eventManager = $event_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function createInstance(ContainerInterface $container, EntityTypeInterface $entity_type) {
    return new static(
      $entity_type,
      $container->get('config.factory'),
      $container->get('uuid'),
      $container->get('language_manager'),
      $container->get('state'),
      $container->get('kernel'),
      $container->get('plugin.manager.rules_event')
    );
  }

  /**
   * Rebuilds the list of registered events from all active reaction rules.
   *
   * @return \Drupal\rules\Engine\RegisteredEventsList
   *   The list of registered events.
   */
  protected function rebuildRegisteredEvents() {
    $registered_events = new RegisteredEventsList();
    // @todo filter by active rules.
    foreach ($this->loadMultiple() as $rules_config) {
      $this->registerEventsFrom($rules_config, $registered_events);
    }
    return $registered_events;
  }

  /**
   * {@inheritdoc}
   */
  public function save(EntityInterface $entity) {
    // We need to get the registered events before the rule is saved, in order
    // to be able to check afterwards if we need to rebuild the container or
    // not.
    $registered_events = $this->stateService->get('rules.registered_events', new RegisteredEventsList());
    $return = parent::save($entity);

    // After the reaction rule is saved, we need to rebuild the container,
    // otherwise the reaction rule will not fire. However, we can do an
    // optimization: if every event was already registered before, we do not
    // have to rebuild the container.
    $container_rebuild = FALSE;
    if (!$this->hasEventsRegisteredFrom($entity)) {
      $container_rebuild = TRUE;
    }
    // Update the state of registered events.
    // @todo filter by active rules.
    $this->registerEventsFrom($entity, $registered_events);
    $this->stateService->set('rules.registered_events', $registered_events);

    if ($container_rebuild) {
      $this->drupalKernel->rebuildContainer();
    }

    return $return;
  }

  /**
   * {@inheritdoc}
   */
  public function delete(array $entities) {
    $registered_events = $this->stateService->get('rules.registered_events', new RegisteredEventsList());
    // After deleting a set of reaction rules, sometimes we may need to rebuild
    // the container, to clean it up, so that the generic subscriber is not
    // registered in the container for the rule events which we do not use
    // anymore. So we do that if there is any change in the registered events,
    // after the reaction rules are deleted.
    $events_before = $registered_events->getEventNames();
    parent::delete($entities);

    foreach ($entities as $entity) {
      foreach ($entity->getEventNames() as $event_name) {
        $registered_events->remove($event_name, $entity->id());
      }
    }
    $this->stateService->set('rules.registered_events', $registered_events);

    $events_after = $registered_events->getEventNames();

    // Update the state of registered events and rebuild the container.
    if ($events_before != $events_after) {
      $this->drupalKernel->rebuildContainer();
    }
  }

  protected function registerEventsFrom(ReactionRuleConfig $rules_config, RegisteredEventsList $registered_events) {
    // Cleanup: make sure any old events from an old version of this rule are
    // removed.
    $registered_events->removeRule($rules_config->id());
    foreach ($rules_config->getEventNames() as $event_name) {
      $event_base_name = $this->eventManager->getEventBaseName($event_name);
      $registered_events->set($event_base_name, $rules_config->id(), $event_name);
    }
  }

  protected function hasEventsRegisteredFrom(ReactionRuleConfig $rules_config, RegisteredEventsList $registered_events) {
    foreach ($rules_config->getEventNames() as $event_name) {
      $event_base_name = $this->eventManager->getEventBaseName($event_name);
      if (!$registered_events->hasEvent($event_base_name)) {
        return FALSE;
      }
    }
    return TRUE;
  }

}
