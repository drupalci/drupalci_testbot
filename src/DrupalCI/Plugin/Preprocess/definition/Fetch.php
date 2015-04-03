<?php
/**
 * @file
 * Contains \DrupalCI\Plugin\Preprocess\definition\Fetch
 *
 * PreProcesses DCI_Fetch variables, updating the job definition with a setup:fetch: section.
 */

namespace DrupalCI\Plugin\Preprocess\definition;

/**
 * @PluginID("fetch")
 */
class Fetch {

  /**
   * {@inheritdoc}
   *
   * DCI_Fetch_Preprocessor
   *
   * Takes a string defining files to be fetched, and converts this to a
   * 'setup:fetch:' array as expected to appear in a job definition
   *
   * Input format: (string) $value = "http://example.com/file1.patch,destination_directory1;[http://example.com/file2.patch,destination_directory2];..."
   * Desired Result: [
   * array('url' => 'http://example.com/file1.patch', 'fetch_directory' => 'fetch_directory1')
   * array('url' => 'http://example.com/file2.patch', 'fetch_directory' => 'fetch_directory2')
   *      ...   ]
   */
  public function process(array &$definition, $value) {
    if (empty($definition['setup']['fetch'])) {
      $definition['setup']['fetch'] = [];
    }
    foreach (explode(';', $value) as $fetch_string) {
      list($fetch['url'], $patch['fetch_directory']) = explode(',', $fetch_string);
      $definition['setup']['fetch'][] = $fetch;
    }
  }
}

