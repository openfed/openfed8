#!/bin/bash
#
# Creates a profile folder inside the docroot.
#
# This is a temporary workaround until Openfed 8.x gets published on
# drupal.org. This can also be used as a helper to test new versions of
# Openfed profile, if they are not commited on drupal.org git repo.
# This function should run after modules and theme folders get populated.

# Set some variables.
SCRIPT_DIR="$( cd "$( dirname "$0" )" && pwd )"
PROJECT_ROOT="$( dirname "$(dirname "$SCRIPT_DIR")" )"
DRUPAL_ROOT="$PROJECT_ROOT/docroot"

# Lets create the folder structure inside drupal root.
mkdir -p "$DRUPAL_ROOT/profiles/contrib/openfed"
PROFILE_ROOT="$DRUPAL_ROOT/profiles/contrib/openfed"

# Lets copy the Files and Configfrom project root.
for file in *.make *.yml config *.profile src
do
  echo "Copying $file to $PROFILE_ROOT"
  cp -a "$file" $PROFILE_ROOT
done

# Lets move the modules and theme folder from the drupal root to the profile root
rm -rf "$PROFILE_ROOT/modules" && mv "$DRUPAL_ROOT/modules" "$PROFILE_ROOT" && rm -rf "$DRUPAL_ROOT/modules"
rm -rf "$PROFILE_ROOT/themes" && mv "$DRUPAL_ROOT/themes" "$PROFILE_ROOT" && rm -rf "$DRUPAL_ROOT/themes"
# rm -rf "$PROFILE_ROOT/libraries" && mv "$DRUPAL_ROOT/libraries" "$PROFILE_ROOT" && rm -rf "$DRUPAL_ROOT/libraries"
echo "Moved modules and themes folders to $PROFILE_ROOT"

# Lets move the custom modules from the project root to the profile folder
rm -rf "$PROFILE_ROOT/modules/openfed_features" && cp -aR "$PROJECT_ROOT/modules"/* "$PROFILE_ROOT/modules"
