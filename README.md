# Openfed D8

OpenFed is a general-purpose, multilingual Drupal 8 distribution.

It is developed by/for the Belgian Federal Public Service for ICT (Fedict) as
part of the Fast2Web offering.


## Installation

The recommended way to install Openfed is by using
[Composer](https://getcomposer.org/doc/00-intro.md#installation-linux-unix-osx).

You should clone this repository and run the following command on the project
root like:

```
composer create-project openfed/openfed8-project MY_PROJECT
```

When installing the given `composer.json` some tasks are taken care of:

* Drupal will be installed in the `docroot`-directory.
* Modules (packages of type `drupal-module`) will be placed in
`docroot/modules/contrib/`
* Theme (packages of type `drupal-theme`) will be placed in
`docroot/themes/contrib/`
* Profiles (packages of type `drupal-profile`) will be placed in
`docroot/profiles/contrib/`
* Downloads Drupal scaffold files such as `index.php`, or `.htaccess`
* Creates `sites/default/files`-directory.
* Set default configuration for some modules.


Node that until the project gets published on drupal.org, the structure
`docroot/profiles/contrib/openfed` will be created with a bash script, moving
the Modules and Themes to this folder, as well as the *.make files and all the
necessary files to install the Openfed profile.

Now, with all the necessary files at `docroot`, it's possible to install
Openfed, like you would with any other distribution.

There is still a problem when trying to add Openfed8 to your own GIT
repository due to the use of DEV versions of some modules (like weight module).
When adding Openfed8 project to a GIT repo, we advise to remove the ".git"
folder from the project directory.
Remove .git folders from the following modules:
- weight: [root]/profiles/openfed/modules/contrib/weight/.git
