<?php

/**
 * @file
 * Contains \DrupalCI\Plugin\PluginManager.
 */

namespace DrupalCI\Plugin;

use Drupal\Component\Annotation\Plugin\Discovery\AnnotatedClassDiscovery;
use Drupal\Component\Plugin\Exception\PluginNotFoundException;

class PluginManager {

  /**
   * @var array
   */
  protected $plugins;

  /**
   * @var string
   */
  protected $superPluginType;

  /**
   * @var array
   */
  protected $pluginDefinitions;

  public function __construct($super_plugin_type) {
    $this->superPluginType = $super_plugin_type;
  }

  /**
   * Discovers the list of available plugins.
   */
  protected function discoverPlugins() {
    $dir = "src/DrupalCI/Plugin/$this->superPluginType";
    $plugin_definitions = [];
    foreach (new \DirectoryIterator($dir) as $file) {
      if ($file->isDir() && !$file->isDot()) {
        $plugin_type = $file->getFilename();
        $plugin_namespaces = ["DrupalCI\\Plugin\\$this->superPluginType\\$plugin_type" => ["$dir/$plugin_type"]];
        $discovery  = new AnnotatedClassDiscovery($plugin_namespaces, 'Drupal\Component\Annotation\PluginID');
        $plugin_definitions[$plugin_type] = $discovery->getDefinitions();
      }
    }
    return $plugin_definitions;
  }

  /**
   * {@inheritdoc}
   */
  public function hasPlugin($type, $plugin_id) {
    if (!isset($this->pluginDefinitions)) {
      $this->pluginDefinitions = $this->discoverPlugins();
    }
    return (isset($this->pluginDefinitions[$type][$plugin_id]) || isset($this->pluginDefinitions['generic'][$plugin_id]));
  }

  /**
   * {@inheritdoc}
   */
  public function getPlugin($type, $plugin_id, $configuration = []) {
    if (!isset($this->plugins[$type][$plugin_id])) {
      if (!$this->hasPlugin($type, $plugin_id)) {
        throw new PluginNotFoundException("Plugin type $type plugin id $plugin_id not found.");
      }
      $plugin_definition = isset($this->pluginDefinitions[$type][$plugin_id]) ?
        $this->pluginDefinitions[$type][$plugin_id] :
        $this->pluginDefinitions['generic'][$plugin_id];
      $this->plugins[$type][$plugin_id] = new $plugin_definition['class']($configuration, $plugin_id, $plugin_definition);
    }
    return $this->plugins[$type][$plugin_id];
  }

}
