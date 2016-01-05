<?php

/**
 * @file
 * Contains \Drupal\rules\Form\Expression\ConditionForm.
 */

namespace Drupal\rules\Form\Expression;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\rules\Condition\ConditionManager;
use Drupal\rules\Context\ContextConfig;
use Drupal\rules\Core\RulesConditionInterface;
use Drupal\rules\Engine\ConditionExpressionInterface;
use Drupal\rules\Form\Expression\ExpressionFormInterface;

/**
 * UI form for adding/editing a Rules condition.
 */
class ConditionForm implements ExpressionFormInterface {

  use ContextFormTrait;
  use StringTranslationTrait;

  /**
   * The condition plugin manager.
   *
   * @var ConditionManager
   */
  protected $conditionManager;

  /**
   * The condition expression that is edited in the form.
   *
   * @var ConditionExpressionInterface
   */
  protected $conditionExpression;

  /**
   * Creates a new object of this class.
   */
  public function __construct(ConditionExpressionInterface $condition_expression, ConditionManager $condition_manager) {
    $this->conditionManager = $condition_manager;
    $this->conditionExpression = $condition_expression;
  }

  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state) {
    $condition_name = $form_state->get('condition');

    // Step 1 of the multistep form.
    if (!$condition_name) {
      $condition_definitions = $this->conditionManager->getGroupedDefinitions();
      $options = [];
      foreach ($condition_definitions as $group => $definitions) {
        foreach ($definitions as $id => $definition) {
          $options[$group][$id] = $definition['label'];
        }
      }

      $form['condition'] = [
        '#type' => 'select',
        '#title' => $this->t('Condition'),
        '#options' => $options,
        '#required' => TRUE,
        '#empty_value' => $this->t('- Select -'),
      ];
      $form['continue'] = [
        '#type' => 'submit',
        '#value' => $this->t('Continue'),
        '#name' => 'continue',
      ];

      return $form;
    }

    // Step 2 of the form.
    /** @var RulesConditionInterface $condition */
    $condition = $this->conditionManager->createInstance($condition_name);

    $form['summary'] = [
      '#markup' => $condition->summary(),
    ];
    $form['condition'] = [
      '#type' => 'value',
      '#value' => $condition_name,
    ];

    $context_defintions = $condition->getContextDefinitions();

    $form['context']['#tree'] = TRUE;
    foreach ($context_defintions as $context_name => $context_definition) {
      $form = $this->buildContextForm($form, $form_state, $context_name, $context_definition);
    }

    $form['save'] = [
      '#type' => 'submit',
      '#value' => $this->t('Save'),
      '#name' => 'save',
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    if ($form_state->getTriggeringElement()['#name'] == 'save') {
      $reaction_config = $form_state->get('reaction_config');
      $expression = $reaction_config->getExpression();

      $context_config = ContextConfig::create();
      foreach ($form_state->getValue('context') as $context_name => $value) {
        if ($form_state->get("context_$context_name") == 'selector') {
          $context_config->map($context_name, $value['setting']);
        }
        else {
          $context_config->setValue($context_name, $value['setting']);
        }
      }

      $expression->addCondition($form_state->getValue('condition'), $context_config);
      // Set the expression again so that the config is copied over to the
      // config entity.
      $reaction_config->setExpression($expression);
      $reaction_config->save();

      drupal_set_message($this->t('Your changes have been saved.'));

      $form_state->setRedirect('entity.rules_reaction_rule.edit_form', [
        'rules_reaction_rule' => $reaction_config->id(),
      ]);
    }
    else {
      $form_state->set('condition', $form_state->getValue('condition'));
      $form_state->setRebuild();
    }
  }

}
