<?php
/**
 * @file
 * Contains \DrupalCI\Plugin\Preprocess\DCI_Patch\PreprocessDCI_Patch
 *
 * PreProcesses DCI_Patch variables, updating the job definition with a setup:patch: section.
 */

namespace DrupalCI\Plugin\PreProcess\DCI_Patch;

use DrupalCI\Plugin\JobTypes\JobInterface;

/**
 * @PluginID("DCI_Patch")
 */
class PreprocessDCI_Patch {

  /**
   * {@inheritdoc}
   *
   * DCI_Patch_Preprocessor
   *
   * Takes a string defining patches to be applied, and converts this to a
   * 'setup:patch:' array as expected to appear in a job definition
   */
  public function run(JobInterface $job, $data) {
    // Input format: (string) $data = "file1.patch,patch_directory1;[file2.patch,patch_directory2];..."
    // Desired Result:
    //      $job->Definition[setup][patch][] = array('patch_file' => 'file1.patch', 'patch_directory' => 'patch_directory1')
    //      $job->Definition[setup][patch][] = array('patch_file' => 'file2.patch', 'patch_directory' => 'patch_directory2')
    //      ...


  }
}

