<?php

namespace OpenfedProject\composer;

use Composer\Script\Event;
use Drupal\Core\DrupalKernel;
use Symfony\Component\HttpFoundation\Request;

/**
 * A class to do several validations before updating to Openfed 8.x-10.0. This
 * will help maintainers to do an informed update.
 *
 * This file is present in Openfed 8.x-9.11+ so composer can early call it in
 * Openfed 8.x-10.0 and make a check before having to update Openfed. As such,
 * this file is not supposed to be used by Openfed 8.x-9.x.
 */
class OpenfedValidations {

  /**
   *
   */
  public static function validateUpdate810(Event $event) {

    // This check will assure that other checks will be performed only if this
    // is a Drupal site, otherwise they will be ignored (like for first time
    // installations).
    if (!self::_isDrupalSite()) {
      return;
    }

    // We should run the validations only on Openfed 8.x-10.0 when we're
    // updating from Openfed 8.x-9.x.
    if (!self::_checkProjectVersion()) {
      return;
    }

    // Composer.json requires some manual updates, this will check if those
    // updates were done.
    self::_checkComposerFile();

    // Some modules were removed from Openfed 8.10 and they should be deleted
    // before updating to this version.
    self::_checkDeprecatedModules();

    // Twig Tweak module was updated so, if used, it should be checked for
    // compatibility issues.
    self::_checkTwigTweak2Compatibility();

    // self::_checkTwigTweak3Compatibility();
  }

  /**
   * Check if current site is a Drupal site.
   *
   * @return bool
   */
  private static function _isDrupalSite() {
    $output = trim(shell_exec('drush status --field="Drupal bootstrap"'));
    if (empty($output)) {
      return FALSE;
    }
    return TRUE;
  }

  /**
   * Checks if the current version is at least Openfed8 10.0.
   *
   * @return bool
   *   Return true if current version is 10.0 or more, false otherwise.
   */
  private static function _checkProjectVersion() {
    $composer_openfed = json_decode(file_get_contents('composer.openfed.json'), TRUE);
    $current_version = $composer_openfed['require']['openfed/openfed8'];
    preg_match('/(?:[\d+\.?]+[a-zA-Z0-9-]*)/', $current_version, $matches);
    // If current version is dev, we should ignore version check.
    if (strpos($current_version, 'dev') !== FALSE) {
      return FALSE;
    }

    return version_compare($matches[0], '10.0', '>=');
  }

  /**
   * Checks if composer file was updated as it should.
   *
   * @throws \ErrorException
   */
  private static function _checkComposerFile() {
    // We'll make sure that the composer merge is updated in the old composer
    // file.
    $composer_file = json_decode(file_get_contents('composer.json'), TRUE);
    $merged_composers = array_search('composer.libraries.json', $composer_file['extra']['merge-plugin']['require']);
    if (strpos($composer_file['require']['wikimedia/composer-merge-plugin'], '^1.') !== FALSE || $merged_composers !== FALSE) {
      throw new \ErrorException("Your composer.json doesn't seem to be up to date.");
    }
  }

  /**
   * Checks if deprecated modules are enabled end stops update if that's the
   * case.
   *
   * @throws \ErrorException
   */
  private static function _checkDeprecatedModules() {
    $modules_to_check = [
      'toolbar_themes',
      'sharemessage',
      'simple_gmap',
      'scheduled_updates',
      'field_default_token',
      'contact_storage_clear',
      'yamlform_clear',
      'features',
    ];
    foreach ($modules_to_check as $module) {
      $output = trim(shell_exec('drush pml --field="status" --filter="' . $module . '"'));
      if ($output == 'Enabled') {
        throw new \ErrorException("You can't proceed with Openfed update until you uninstall $module. See Openfed 8x-10.0 release notes.");
      }
    }
  }

  /**
   * Checks if Twig Tweak is enabled.
   *
   * @return bool
   */
  private static function _isTwigTweakEnabled() {
    $module = 'twig_tweak';
    $output = trim(shell_exec('drush pml --field="status" --filter="' . $module . '"'));
    if ($output == 'Enabled') {
      return TRUE;
    }
    return FALSE;
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

  /**
   * Check template for Twig Tweak 2.x compatibility issues.
   * See https://www.drupal.org/docs/contributed-modules/twig-tweak/migrating-to-twig-tweak-2x
   *
   * @throws \ErrorException
   */
  private static function _checkTwigTweak2Compatibility() {
    if (self::_isTwigTweakEnabled()) {
      self::_initDrupalContainer();

      // 1. Check Rendering Blocks.
      $available_blocks = array_keys(\Drupal::service('plugin.manager.block')->getDefinitions());
      $search = shell_exec('grep --include \*.twig -r "drupal_block" ./docroot/themes/ ./config/');
      $pattern = '/drupal_block\([\'|"]([a-zA-Z0-9\-_]+)[\'|"]\)/';
      preg_match_all($pattern, $search, $matches);

      foreach ($matches[1] as $block_id) {
        // If one of the block matches is an available block plugin id, we
        // should not proceed.
        if (!in_array($block_id, $available_blocks)) {
          throw new \ErrorException("You should convert your theme to Twig Tweak 2.x before updating Openfed. See https://www.drupal.org/docs/contributed-modules/twig-tweak/migrating-to-twig-tweak-2x#rendering-blocks.");
        }
      }

      // 2. Check preg_replace filter declaration.
      $search = shell_exec('grep --include \*.twig -r "preg_replace" ./docroot/themes/ ./config/');
      $pattern = '/preg_replace\([\'|"]\/?/';
      preg_match_all($pattern, $search, $matches);

      foreach ($matches[0] as $expression) {
        // If one of the block matches is an available block plugin id, we
        // should not proceed.
        if (substr($expression, -1) != '/') {
          throw new \ErrorException("You should convert your theme to Twig Tweak 2.x before updating Openfed. See https://www.drupal.org/docs/contributed-modules/twig-tweak/migrating-to-twig-tweak-2x#preg-replace-filter.");
        }
      }

      // 3. Check Entity access.
      $twig_tweak_check_access = \Drupal::service('settings')->get('twig_tweak_check_access');
      if ($twig_tweak_check_access === FALSE) {
        throw new \ErrorException("You should convert your settings.php to Twig Tweak 2.x before updating Openfed. See https://www.drupal.org/docs/contributed-modules/twig-tweak/migrating-to-twig-tweak-2x#entity-access.");
      }

      // 4. Check Region wrapper.
      // 5. Check Default field language.
      // 6. Check preg_replace filter declaration.
      $search = shell_exec('grep --include \*.twig -r "drupal_set_message" ./docroot/themes/ ./config/');
      $pattern = '/drupal_set_message\([\'|"]/';
      preg_match_all($pattern, $search, $matches);

      if (!empty($matches[0])) {
        throw new \ErrorException("You should convert your theme to Twig Tweak 2.x before updating Openfed. See https://www.drupal.org/docs/contributed-modules/twig-tweak/migrating-to-twig-tweak-2x#default-field-language.");
      }

    }
  }

  /**
   * Check template for Twig Tweak 3.x compatibility issues.
   * See https://git.drupalcode.org/project/twig_tweak/-/blob/3.x/docs/migration-to-3.x.md
   *
   * @throws \ErrorException
   */
  private static function _checkTwigTweak3Compatibility() {
    if (self::_isTwigTweakEnabled()) {
      // @todo for Openfed 11.
    }
  }

}
