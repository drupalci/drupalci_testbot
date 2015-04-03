<?php
/**
 * @file
 * Contains \DrupalCI\Plugin\Preprocess\definition\Patch
 *
 * PreProcesses DCI_Patch variables, updating the job definition with a setup:patch: section.
 */

namespace DrupalCI\Plugin\Preprocess\definition;

/**
 * @PluginID("patch")
 */
class Patch {

  /**
   * {@inheritdoc}
   *
   * DCI_Patch_Preprocessor
   *
   * Takes a string defining patches to be applied, and converts this to a
   * 'setup:patch:' array as expected to appear in a job definition
   */
  public function process(array &$definition, $value) {
    if (empty($definition['setup']['patch'])) {
      $definition['setup']['patch'] = [];
    }
    foreach (explode(';', $value) as $patch_string) {
      list($patch['patch_file'], $patch['patch_directory']) = explode(',', $patch_string);
      $definition['setup']['patch'][] = $patch;
    }
    // Input format: (string) $value = "file1.patch,patch_directory1;[file2.patch,patch_directory2];..."
    // Desired Result: [
    // ]
    // array('patch_file' => 'file1.patch', 'patch_directory' => 'patch_directory1')
    // array('patch_file' => 'file2.patch', 'patch_directory' => 'patch_directory2')
    //      ...
  }
}

