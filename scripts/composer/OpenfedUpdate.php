<?php

namespace OpenfedProject\composer;

use Composer\Script\Event;
use Drupal\Core\DrupalKernel;
use Symfony\Component\HttpFoundation\Request;
use ZipArchive;

class OpenfedUpdate {

  /**
   * @var string
   */
  protected static $openfed8Repo = 'https://github.com/openfed/openfed8-project';

  /**
   * @var string
   */
  protected static $openfed8Zip = 'https://github.com/openfed/openfed8-project/archive/refs/tags/';

  /**
   * @var string
   */
  protected static $latestOpenfedVersion;

  /**
   * Update current Openfed8 project files.
   *
   * @param \Composer\Script\Event $event
   */
  public static function update(Event $event) {
    self::_setLatestOpenfedVersion();

    // Check if there's a new Openfed version and update if so.
    if (self::_newVersionExists()) {
      echo "\n\n---- Project files will be updated to Openfed version " . self::$latestOpenfedVersion . "\n";

      $url = self::$openfed8Zip . self::$latestOpenfedVersion . '.zip';
      $zipFile = self::$latestOpenfedVersion . '.zip';
      $extractPath = self::$latestOpenfedVersion;

      $zip_resource = fopen($zipFile, "w");

      self::_initDrupalContainer();
      /** @var GuzzleHttp\Psr\Response $response */
      $response = \Drupal::httpClient()->get($url, ['sink' => $zip_resource]);

      if (!$response) {
        echo "Error :- ";
      }

      $zip = new ZipArchive;
      if ($zip->open($zipFile) != "true") {
        throw new \ErrorException("Error :- Unable to open the Zip File.");
      }

      $zip->extractTo($extractPath);
      $zip->close();

      unlink($zipFile);
      unlink('./composer.libraries.json');
      unlink($extractPath . DIRECTORY_SEPARATOR . 'openfed8-project-' . self::$latestOpenfedVersion . DIRECTORY_SEPARATOR . 'composer.json');
      unlink($extractPath . DIRECTORY_SEPARATOR . 'openfed8-project-' . self::$latestOpenfedVersion . DIRECTORY_SEPARATOR . '.gitignore');
      unlink($extractPath . DIRECTORY_SEPARATOR . 'openfed8-project-' . self::$latestOpenfedVersion . DIRECTORY_SEPARATOR . 'README.md');

      self::_recurseCopy($extractPath . DIRECTORY_SEPARATOR . 'openfed8-project-' . self::$latestOpenfedVersion, '.');
      self::_deleteDirectory($extractPath);

      echo "---- Files updated. You still have to check your composer.json manually.\n\n";
    }
  }

  /**
   * Copy files from one dir to another.
   *
   * @param $src
   *  Source directory.
   * @param $dst
   *  Destination directory.
   */
  private static function _recurseCopy($src, $dst) {
    $dir = opendir($src);
    @mkdir($dst);
    while (FALSE !== ($file = readdir($dir))) {
      if (($file != '.') && ($file != '..')) {
        if (is_dir($src . DIRECTORY_SEPARATOR . $file)) {
          recurse_copy($src . DIRECTORY_SEPARATOR . $file, $dst . DIRECTORY_SEPARATOR . $file);
        }
        else {
          copy($src . DIRECTORY_SEPARATOR . $file, $dst . DIRECTORY_SEPARATOR . $file);
        }
      }
    }
    closedir($dir);
  }

  /**
   * Delete directory from filesystem.
   *
   * @param $dir
   *  The directory to remove.
   *
   * @return bool
   *   True on success, false otherwise.
   */
  public static function _deleteDirectory($dir) {
    if (!file_exists($dir)) {
      return TRUE;
    }
    if (!is_dir($dir)) {
      return unlink($dir);
    }
    foreach (scandir($dir) as $item) {
      if ($item == '.' || $item == '..') {
        continue;
      }
      if (!self::_deleteDirectory($dir . DIRECTORY_SEPARATOR . $item)) {
        return FALSE;
      }
    }
    return rmdir($dir);
  }

  /**
   * Checks if there's a more recent version of Openfed.
   *
   * @return bool
   *   True if there's a new version, false otherwise.
   */
  private static function _newVersionExists() {
    $composer_openfed = json_decode(file_get_contents('composer.openfed.json'), TRUE);
    $current_version = $composer_openfed['require']['openfed/openfed8'];

    // If current version is dev, we don't need to check if there's a newer
    // version.
    if (strpos($current_version, 'dev') !== FALSE) {
      return FALSE;
    }

    return version_compare(self::$latestOpenfedVersion, $current_version, '>');
  }

  /**
   * Set the latest Openfed8 version variable.
   */
  private static function _setLatestOpenfedVersion() {
    $latest_openfed_version = explode("\n", trim(shell_exec("git -c 'versionsort.suffix=-' ls-remote --tags --sort='-v:refname' " . self::$openfed8Repo . " | cut --delimiter='/' --fields=3 | grep -v -")));
    self::$latestOpenfedVersion = $latest_openfed_version[0];
  }

  /**
   * Initiates Drupal container.
   *
   * @throws \Exception
   */
  private static function _initDrupalContainer() {
    $autoloader = require_once getcwd() . '/docroot/autoload.php';
    $request = Request::createFromGlobals();
    $kernel = DrupalKernel::createFromRequest($request, $autoloader, 'prod');
    $kernel->boot();
    $kernel->preHandle($request);
    if (PHP_SAPI !== 'cli') {
      $request->setSession($kernel->getContainer()->get('session'));
    }
  }
}
