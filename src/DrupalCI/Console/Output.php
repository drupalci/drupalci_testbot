<?php

/**
 * @file
 * Contains \DrupalCI\Console\Output.
 */

namespace DrupalCI\Console;

class Output {

  /**
   * @var \Symfony\Component\Console\Output\OutputInterface
   */
  static protected $output;

  public static function getOutput() {
    return static::$output;
  }

  public static function setOutput($output) {
    static::$output = $output;
  }

  /**
   * @param string|array $messages
   */
  public static function writeLn($messages) {
    static::$output->writeln($messages);
  }

  /**
   * @param string|array $messages
   */
  public static function write($messages) {
    static::$output->write($messages);
  }

  public static function error($type, $message) {
    if (!empty($type)) {
      static::$output->writeln("<error>$type</error>");
    }
    if (!empty($message)) {
      static::$output->writeln("<comment>$message</comment>");
    }
  }

}
