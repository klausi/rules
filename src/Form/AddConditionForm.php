<?php

/**
 * @file
 * Contains \Drupal\rules\Form\AddConditionForm.
 */

namespace Drupal\rules\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

class AddConditionForm extends FormBase {

  /**
   * The condition plugin manager.
   *
   * @var \Drupal\Core\Condition\ConditionManager
   */
  protected $conditionManager;

  /**
   * Creates a new object of this class.
   */
  public function __construct(\Drupal\Core\Condition\ConditionManager $condition_manager) {
    $this->conditionManager = $condition_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static($container->get('plugin.manager.condition'));
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
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

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'rules_ui_condition_add';
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {

  }

}
