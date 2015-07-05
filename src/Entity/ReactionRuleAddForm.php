<?php

/**
 * @file
 * Contains \Drupal\rules\Entity\ReactionRuleAddForm.
 */

namespace Drupal\rules\Entity;

use Drupal\Core\Form\FormStateInterface;

/**
 * Provides a form to add a reaction rule.
 */
class ReactionRuleAddForm extends RulesComponentFormBase {

  /**
   * {@inheritdoc}
   */
  protected function actions(array $form, FormStateInterface $form_state) {
    $actions = parent::actions($form, $form_state);
    $actions['submit']['#value'] = $this->t('Save');
    return $actions;
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    parent::save($form, $form_state);

    drupal_set_message($this->t('Reaction rule %label has been created.', ['%label' => $this->entity->label()]));
  }

}
