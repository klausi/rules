<?php

/**
 * @file
 * Contains \Drupal\rules\Form\Expression\ConditionContainerForm.
 */

namespace Drupal\rules\Form\Expression;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\Core\Url;
use Drupal\rules\Engine\ConditionExpressionContainerInterface;

class ConditionContainerForm implements ExpressionFormInterface {

  use StringTranslationTrait;

  /**
   * The rule expression object this form is for.
   *
   * @var \Drupal\rules\Engine\ConditionExpressionContainerInterface
   */
  protected $conditionContainer;

  /**
   * Creates a new object of this class.
   */
  public function __construct(ConditionExpressionContainerInterface $condition_container) {
    $this->conditionContainer = $condition_container;
  }

  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state) {
    $form['conditions'] = array(
      '#type' => 'container',
    );

    $form['conditions']['table'] = array(
      '#theme' => 'table',
      '#caption' => $this->t('Conditions'),
      '#header' => array($this->t('Elements'), $this->t('Operations')),
      '#empty' => t('None'),
    );

    $form['add_condition'] = [
      '#theme' => 'menu_local_action',
      '#link' => [
        'title' => $this->t('Add condition'),
        'url' => Url::fromRoute('rules.reaction_rule.condition.add', [
          'rules_reaction_rule' => $this->conditionContainer->getRoot()->getConfigEntityId(),
        ]),
      ],
    ];

    return $form;
  }

}
