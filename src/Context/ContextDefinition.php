<?php

/**
 * @file
 * Contains \Drupal\rules\Context\ContextDefinition.
 */

namespace Drupal\rules\Context;

use \Drupal\Core\Plugin\Context\ContextDefinition as ContextDefinitionCore;

/**
 * Extends the core context definition class with useful methods.
 */
class ContextDefinition extends ContextDefinitionCore implements ContextDefinitionInterface {

  /**
   * The mapping of config export keys to internal properties.
   *
   * @var array
   */
  protected static $nameMap = [
    'type' => 'dataType',
    'label' => 'label',
    'description' => 'description',
    'multiple' => 'isMultiple',
    'required' => 'isRequired',
    'default_value' => 'defaultValue',
    'constraints' => 'constraints',
    'allow_null' => 'allowNull',
  ];

  /**
   * Whether the context value is allowed to be NULL or not.
   *
   * @var bool
   */
  protected $allowNull = FALSE;

  /**
   * Exports the definition as an array.
   *
   * @return array
   *   An array with values for all definition keys.
   */
  public function toArray() {
    $values = [];
    $defaults = get_class_vars(__CLASS__);
    foreach (static::$nameMap as $key => $property_name) {
      // Only export values for non-default properties.
      if ($this->$property_name !== $defaults[$property_name]) {
        $values[$key] = $this->$property_name;
      }
    }
    return $values;
  }

  /**
   * Creates a definition object from an exported array of values.
   *
   * @param array $values
   *   The array of values, as returned by toArray().
   *
   * @return static
   *   The created definition.
   */
  public static function createFromArray($values) {
    $definition = static::create($values['type']);
    foreach (array_intersect_key(static::$nameMap, $values) as $key => $name) {
      $definition->$name = $values[$key];
    }
    return $definition;
  }

  /**
   * {@inheritdoc}
   */
  public function isAllowedNull() {
    return $this->allowNull;
  }

  /**
   * {@inheritdoc}
   */
  public function setAllowNull($null_allowed) {
    $this->allowNull = $null_allowed;
    return $this;
  }

}
