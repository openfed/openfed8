# Openfed D8

OpenFed is a general-purpose, multilingual Drupal 8 distribution.

It is developed by/for the Belgian Federal Public Service for ICT (Fedict) as
part of the Fast2Web offering.


## Usage

### Installation

The recommended way to install Openfed is using
[Composer](https://getcomposer.org/doc/00-intro.md#installation-linux-unix-osx).

You can use our [Openfed Project](https://github.com/openfed/openfed8-project/tree/10.x) template like:

```
composer create-project openfed/openfed8-project MY_PROJECT
```

After that you can just install Openfed like any other distribution.

### Update

**Important note before update**: Make sure you are running Openfed 8.x-9.11 or higher before updating to Openfed8 10.x

In order to successfully update to Openfed 8.x-10.x from a Openfed 8.x-9.x version, you should disable the following modules:
- toolbar_themes
- sharemessage
- simple_gmap
- scheduled_updates
- field_default_token
- contact_storage_clear
- yamlform_clear
- features

You should also update your theme if you are using twig_tweak 1.x, previously shipped with Openfed 8.x-9.x and earlier versions. See the following page to know what changed https://www.drupal.org/docs/contributed-modules/twig-tweak/migrating-to-twig-tweak-2x

If these steps are not performed, Openfed be able to update.

### Server updates

Openfed 8.x-10.x requires PHP 7.3. Make sure that the right version of PHP is being used before updating.

## Bug Report

You should use, preferably, Drupal.org [Openfed issue queue](https://www.drupal.org/project/issues/openfed).
