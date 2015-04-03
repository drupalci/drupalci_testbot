<?php
/**
 * @file
 * Contains
 */

namespace DrupalCI\Plugin\Preprocess;


interface VariableInterface {

  /**
   * @return string|array
   *   The name of a DCI variable. Can be a list of them, too.
   */
  public function target();

  /**
   * Changes a DCI variable based on another.
   *
   * @param $target_variable_original
   *   The value of the DCI variable specified by $this->target().
   * @param $source_value
   *   The value of the DCI variable specified by the annotation.
   * @return string
   *   The new value of the DCI variable specified by $this->target().
   */
  public function process($target_variable_original, $source_value);

}
