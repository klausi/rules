<?php

/**
 * @file
 * Contains \Drupal\rules\Form\AddExpressionForm.
 */

namespace Drupal\rules\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\rules\Engine\ExpressionManagerInterface;
use Drupal\rules\Entity\ReactionRuleConfig;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 *
 */
class AddExpressionForm extends FormBase {

  /**
   * The Rules expression manager to get expression plugins.
   *
   * @var ExpressionManagerInterface
   */
  protected $expressionManager;

  /**
   * Creates a new object of this class.
   */
  public function __construct(ExpressionManagerInterface $expression_manager) {
    $this->expressionManager = $expression_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static($container->get('plugin.manager.rules_expression'));
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, ReactionRuleConfig $reaction_config = NULL, $expression_id = NULL) {
    $expression = $this->expressionManager->createInstance($expression_id);
    $form_state->set('reaction_config', $reaction_config);
    $form_state->set('expression', $expression);
    $form_handler = $expression->getFormHandler();
    $form = $form_handler->form($form, $form_state);
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'rules_expression_add';
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $expression = $form_state->get('expression');
    $form_handler = $expression->getFormHandler();
    $form_handler->submitForm($form, $form_state);
  }

  public function getTitle() {
    return 'foo';
  }

}
