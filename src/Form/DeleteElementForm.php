<?php

/**
 * @file
 * Contains \Drupal\rules\Form\DeleteElementForm.
 */

namespace Drupal\rules\Form;
use Drupal\Core\Form\ConfirmFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\rules\Plugin\RulesExpression\ReactionRule;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Removes an element from a rule.
 */
class DeleteElementForm extends ConfirmFormBase {

  /**
   * The reaction rule the element is deleted from.
   *
   * @var \Drupal\rules\Plugin\RulesExpression\ReactionRule
   */
  protected $rule;

  /**
   * The index of the element in the rule.
   *
   * @var int
   */
  protected $index;

  /**
   * Constructor.
   */
  public function __construct() {
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static();
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'rules_delete_element';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, ReactionRule $rules_reaction_rule = NULL, $index = NULL) {
    $this->rule = $rules_reaction_rule;
    $this->index = $index;
    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function getConfirmText() {
    return $this->t('Delete');
  }

  /**
   * {@inheritdoc}
   */
  public function getQuestion() {
    dpm($this->index);
    return $this->t('Are you sure you want to delete %title ?', array('%title' => $this->rule->label()));
  }

  /**
   * {@inheritdoc}
   */
  public function getCancelUrl() {
    return $this->rule->urlInfo();
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    dpm('yeah');
    $form_state->setRedirectUrl($this->getCancelUrl());
  }

}
