<?php

namespace Drupal\Tests\contact_storage_clear\Functional;

use Drupal\contact\Entity\Message;
use Drupal\Tests\BrowserTestBase;

/**
 * Tests the functionality of the Contact storage clear module.
 *
 * @group contact_storage_clear
 */
class ContactStorageClearTest extends BrowserTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = ['contact_storage'];

  /**
   * The module installer.
   *
   * @var \Drupal\Core\Extension\ModuleInstallerInterface
   */
  protected $moduleInstaller;

  /**
   * The database schema.
   *
   * @var \Drupal\Core\Database\Schema
   */
  protected $schema;

  /**
   * The name of the table that should be dropped during installation.
   *
   * @var string
   */
  protected $table = 'contact_message';

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();

    $this->moduleInstaller = $this->container->get('module_installer');

    /** @var \Drupal\Core\Database\Connection $connection */
    $connection = $this->container->get('database');
    $this->schema = $connection->schema();
  }

  /**
   * Tests that installing the module uninstalls the contact message schema.
   */
  public function testSchemaUninstallation() {
    $this->assertTrue($this->schema->tableExists($this->table));

    /** @var \Drupal\Core\Extension\ModuleInstallerInterface $module_installer */
    $module_installer = $this->moduleInstaller;
    $module_installer->install(['contact_storage_clear'], FALSE);
    $this->assertFalse($this->schema->tableExists($this->table));

    $contact_message = Message::create([
      'contact_form' => 'personal',
    ]);
    $contact_message->save();
  }

}
