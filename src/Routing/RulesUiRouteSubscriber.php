<?php

/**
 * @file
 * Contains \Drupal\rules\Routing\RulesUiRouteSubscriber.
 */

namespace Drupal\rules\Routing;

use Drupal\Core\Routing\RouteSubscriberBase;
use Drupal\Core\Routing\RoutingEvents;
use Drupal\rules\Ui\RulesUiDefinition;
use Drupal\rules\Ui\RulesUiManagerInterface;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

/**
 * Adds routes generated by the rules UI handlers.
 */
class RulesUiRouteSubscriber extends RouteSubscriberBase {

  /**
   * The rules UI manager.
   *
   * @var \Drupal\rules\Ui\RulesUiManagerInterface
   */
  protected $rulesUiManager;

  /**
   * Constructs the object.
   *
   * @param \Drupal\rules\Ui\RulesUiManagerInterface $rules_ui_manager
   *   The rules UI manager.
   */
  public function __construct(RulesUiManagerInterface $rules_ui_manager) {
    $this->rulesUiManager = $rules_ui_manager;
  }

  /**
   * {@inheritdoc}
   */
  protected function alterRoutes(RouteCollection $collection) {
    foreach ($this->rulesUiManager->getDefinitions() as $name => $definition) {
      $ui_definition = $this->rulesUiManager->getDefinition($name);
      $this->registerRoutes($ui_definition, $collection);
    }
  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    $events = parent::getSubscribedEvents();
    // Should run after AdminRouteSubscriber so the routes can inherit admin
    // status of the edit routes on entities. Therefore priority -210.
    $events[RoutingEvents::ALTER] = ['onAlterRoutes', -210];
    return $events;
  }

  /**
   * Registers the routes as needed for the UI.
   *
   * @param \Drupal\rules\Ui\RulesUiDefinition $ui_definition
   *   The definition of the RulesUI for which to register the routes.
   * @param \Symfony\Component\Routing\RouteCollection $collection
   *   The route collection to which to add the routes.
   */
  protected function registerRoutes(RulesUiDefinition $ui_definition, RouteCollection $collection) {
    $base_route = $collection->get($ui_definition->base_route);

    $options = [
      'parameters' => ($base_route->getOption('parameters') ?: []),
      '_admin_route' => $base_route->getOption('_admin_route') ?: FALSE,
      '_rules_ui' => $ui_definition->id,
    ];
    $requirements = [
      '_permission' => $ui_definition->permissions ?: $base_route->getRequirement('_permission'),
    ];

    $route = (new Route($base_route->getPath() . '/add/{expression_id}'))
      ->addDefaults([
        '_form' => '\Drupal\rules\Form\AddExpressionForm',
        '_title_callback' => '\Drupal\rules\Form\AddExpressionForm::getTitle',
      ])
      ->addOptions($options)
      ->addRequirements($requirements);
    $collection->add($ui_definition->base_route . '.expression.add', $route);

    $route = (new Route($base_route->getPath() . '/edit/{uuid}'))
      ->addDefaults([
        '_form' => '\Drupal\rules\Form\EditExpressionForm',
        '_title_callback' => '\Drupal\rules\Form\EditExpressionForm::getTitle',
      ])
      ->addOptions($options)
      ->addRequirements($requirements);
    $collection->add($ui_definition->base_route . '.expression.edit', $route);

    $route = (new Route($base_route->getPath() . '/delete/{uuid}'))
      ->addDefaults([
        '_form' => '\Drupal\rules\Form\DeleteExpressionForm',
        '_title' => 'Delete expression',
      ])
      ->addOptions($options)
      ->addRequirements($requirements);
    $collection->add($ui_definition->base_route . '.expression.delete', $route);

    $route = (new Route($base_route->getPath() . '/break-lock'))
      ->addDefaults([
        '_form' => '\Drupal\rules\Form\BreakLockForm',
        '_title' => 'Break lock',
      ])
      ->addOptions($options)
      ->addRequirements($requirements);
    $collection->add($ui_definition->base_route . '.break_lock', $route);

    $route = (new Route($base_route->getPath() . '/autocomplete/{uuid}'))
      ->addDefaults([
        '_controller' => '\Drupal\rules\Controller\AutocompleteController::autocomplete',
      ])
      ->addOptions($options)
      ->addRequirements($requirements);
    $collection->add($ui_definition->base_route . '.autocomplete', $route);
  }

}
