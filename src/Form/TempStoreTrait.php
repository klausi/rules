<?php

/**
 * @file
 * Contains \Drupal\rules\Form\TempStoreTrait.
 */

namespace Drupal\rules\Form;

use Drupal\user\SharedTempStoreFactory;

/**
 * Provides methods for modified rules configurations in temporary storage.
 */
trait TempStoreTrait {

  /**
   * The tempstore factory.
   *
   * @var \Drupal\user\SharedTempStoreFactory
   */
  protected $tempStoreFactory;

  /**
   * The temporary store for the rules configuration.
   *
   * @var \Drupal\user\SharedTempStore
   */
  protected $tempStore;

  protected function saveToTempStore() {
    $this->getTempStore()->set($this->getRuleConfig()->id(), $this->getRuleConfig());
  }

  protected function getTempStore() {
    if (!isset($this->tempStore)) {
      $this->tempStore = $this->getTempStoreFactory()->get($this->getRuleConfig()->getEntityTypeId());
    }
    return $this->tempStore;
  }

  protected function getTempStoreFactory() {
    if (!isset($this->tempStoreFactory)) {
      $this->tempStoreFactory = \Drupal::service('user.shared_tempstore');
    }
    return $this->tempStoreFactory;
  }

  public function setTempStoreFactory(SharedTempStoreFactory $temp_store_factory) {
    $this->tempStoreFactory = $temp_store_factory;
  }

  protected function isLocked() {
    // If there is an object in the temporary storage from another user then
    // this configuration is locked.
    if ($this->getTempStore()->get($this->getRuleConfig()->id())
      && !$this->getTempStore()->getIfOwner($this->getRuleConfig()->id())
    ) {
      return TRUE;
    }
    return FALSE;
  }

  protected function getLockMetaData() {
    return $this->getTempStore()->getMetadata($this->getRuleConfig()->id());
  }

  protected function isEdited() {
    if ($this->getTempStore()->get($this->getRuleConfig()->id())) {
      return TRUE;
    }
    return FALSE;
  }

  protected function deleteFromTempStore() {
    $this->getTempStore()->delete($this->getRuleConfig()->id());
  }

  protected function getRuleConfig() {
    return $this->ruleConfig;
  }

}
