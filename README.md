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

Now, with all the necessary files at `docroot`, it's possible to install
Openfed, like you would with any other distribution.

There is still a problem when trying to add Openfed8 to your own GIT
repository due to the use of DEV versions of some modules (like field_default_token module).
When adding Openfed8 project to a GIT repo, we advise to remove the ".git"
folder from the project directory.
Remove .git folders from the following modules:
- field_default_token: [root]/profiles/openfed/modules/contrib/field_default_token/.git
