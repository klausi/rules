<?php

/**
 * @file
 * Contains \Drupal\rules\Form\EditExpressionForm.
 */

namespace Drupal\rules\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\rules\Engine\ExpressionManagerInterface;
use Drupal\rules\Entity\ReactionRuleConfig;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * UI form to edit an expression like a condition or action in a rule.
 */
class EditExpressionForm extends FormBase {

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
  public function buildForm(array $form, FormStateInterface $form_state, ReactionRuleConfig $reaction_config = NULL, $uuid = NULL) {
    $form_state->set('reaction_config', $reaction_config);
    $rule_expression = $reaction_config->getExpression();
    $expression_inside = $rule_expression->getExpression($uuid);
    if (!$expression_inside) {
      throw new NotFoundHttpException();
    }
    $form_state->set('expression', $expression_inside);
    $form_handler = $expression_inside->getFormHandler();
    $form = $form_handler->form($form, $form_state);
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'rules_expression_edit';
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $expression = $form_state->get('expression');
    $form_handler = $expression->getFormHandler();
    $form_handler->submitForm($form, $form_state);
  }

  /**
   * Provides the page title on the form.
   */
  public function getTitle(ReactionRuleConfig $reaction_config, $uuid) {
    $rule_expression = $reaction_config->getExpression();
    $expression_inside = $rule_expression->getExpression($uuid);
    return $this->t('Edit @expression', ['@expression' => $expression_inside->getLabel()]);
  }

}
