CHANGELOG
=========

15 September 2021 - Version 9.10
----------------------------
  Update Drupal core.
  Update Entity Embed module.
  Add missing regions to Openfed Admin theme.
  Fix partialDate module issue.
  Issue #116: Patch to fix admin_toolbar translation issues.
  Update kiso.
  Issue 3222938: Remove empty hook_install.
  Fix svg files treated as xml files.

30 August 2021 - Version 9.9
----------------------------
  Update Drupal core.
  Update Webform module.

1 July 2021 - Version 9.8
----------------------------
  Fix gitignore by reverting wrong ignore rule introduced in previous release.


31 May 2021 - Version 9.7
----------------------------
  Update Drupal core due to Moderately critical - Cross Site Scripting - SA-CORE-2021-003


22 April 2021 - Version 9.6
----------------------------
  Update Drupal core due to Critical - Cross-site scripting - SA-CORE-2021-002

01 April 2021 - Version 9.5
----------------------------
- Added Antibot module

29 March 2021 - Version 9.4
----------------------------
- Update Webform module due to sa-contrib-2021-004

02 February 2021 - Version 9.3
----------------------------
- Fix Openfed SVG field translation+moderation issue

21 January 2021 - Version 9.2
----------------------------
 - Core update to version 8.9.13 due to SA-CORE-2021-001
 - d.org 3071446: Remove invalid iframe attributes from oembed
 - Make openfed_federalheader module aware of existing translations
 - Fix menu_link issues with new patches

16 December 2020 - Version 9.1
----------------------------
 - Core update to version 8.9.11
 - Issue #3188679: Add Matomo module

07 December 2020 - Version 9.0
----------------------------
 - Update Core to version 8.9.10
 - Image Effects: patch to allow smooth update
 - Update Kiso to version 2.6
 - Add image_widget_crop module
 - Add scheduler module and a deprecated status message about scheduled_updates
 - Add block_class module
 - Partial compatibility with Composer 2.0
 - Add sharemessage deprecation status message
 - Several module version updates
 - Add views_block_filter_block, memcache, geocoder, date_popup, address, optional_end_date, menu_firstchild, multiselect, conditional_fields, sitemap
 - Add update restrictions, requiring Openfed 8.x-8.7, due to taxonomy_access_fix

Issue list available at https://github.com/openfed/openfed8/milestone/6?closed=1

**Notes**:
 - Make sure you are using the latest Openfed 8.x-8.x version before updating to Openfed 8.x-9.0 (Requirement for Taxonomy Access Fix)
 - Theme must be checked due to the issue described on https://www.drupal.org/project/drupal/issues/3169918
 - scheduled_updates is not going to be supported anymore and Openfed 8.9.x is the last version where it will be included
 - sharemessage is not supported anymore and Openfed 8.9.x is the last version where it will be included
 - entityqueue module was updated and there is a CR for it, which may require manual intervention https://www.drupal.org/node/3123878
 - IMCE link on user profile was replaced by an item, under admin menu item "Content"
 - The rules submodule "rules_ban" was removed so, if you are upgrading from a previous version of Openfed, uninstall it first

26 November 2020 - Version 8.6
----------------------------
  Update Drupal Core due to SA-CORE-2020-012 and SA-CORE-2020-013
  Update Twitter Block module

16 September 2020 - Version 8.5
----------------------------
  Update to Drupal Core due to SA-CORE-2020-007/008/009/010/011

18 June 2020 - Version 8.4
----------------------------
  Update to Drupal Core due to SA-CORE-2020-004/5/6
  Updated Core patches
  Performance improvement to menu_trail_by_path module

22 May 2020 - Version 8.3
----------------------------
  Update to Drupal Core due to SA-CORE-2020-002
  Update Webforms due to security issues from SA-CONTRIB-2020-011 until SA-CONTRIB-2020-018
  Added simpleSAMLphp Authentication module
  Updated Kiso theme
  Fixed administration menu items for node translation creation
  Revert CKEditor HTML filter settings
  Defined default config for extlink module
  Fixed encoding issue for downloaded files on IE

13 March 2020 - Version 8.2
----------------------------
  Update to Drupal Core 8.8.4 due to SA-CORE-2020-001
  Update CKEditor addons version

10 March 2020 - Version 8.1
----------------------------
  Update to Drupal Core 8.8.3

28 February 2020 - Version 8.0
----------------------------
  Stable release of Openfed8, using Drupal Core 8.8.2
  Several module updates
  Several patches updates
  Additional patches and modules to help with the migration process
  New administration theme Openfed Admin, to fix sporadic issues

  Complete list of major fixed issues: https://github.com/openfed/openfed8/milestone/5

19 December 2019 - Version 8.0-rc4
----------------------------
  Update to Drupal Core due to SA-CORE-2019-0[09 to 12]
  Update to Smart Trim due to SA-CONTRIB-2019-092
  Update to Taxonomy Access Fix due to SA-CONTRIB-2019-093

11 December 2019 - Version 8.0-rc3
----------------------------
  Update Menu Trail by Path patch to fix compatibility errors.
  Update Field Group, Leaflet, Leaflet Maptiler and Pathauto.

06 December 2019 - Version 8.0-rc2
----------------------------
  Update Drupal Core to version 8.x-8.0
  Update Entity Reference Revisions to 8.x-1.7-rc2

27 November 2019 - Version 8.0-rc1
----------------------------
  Update Drupal Core to version 8.x-8.0-rc1
  Update Pathauto to 8.x-1.6-beta1

19 November 2019 - Version 7.3
----------------------------
  Update Kiso to version 2.3

16 October 2019 - Version 7.2
----------------------------
  Removed unnecessary config, which caused installation problems

11 October 2019 - Version 7.1
----------------------------
  Since version 7.0:
    * Reverted to Ctools 3.0
    * Re-added Media Entity module due to uninstall problems
    * Improved the update mechanism

10 October 2019 - Version 7.0
----------------------------
  Stable version 7.0.
  This includes Core, several contrib module updates and:
    Upgrade to Drush 9
    Compatibility with PHP 7.3
    Added Taxonomy Access Fix module
    Added Leaflet and Maptiler modules
    Added Kiso as a required Theme
    Added Measuremail module
    Added Openfed Social module.
    Removed Media Entity module - check install notes.
  This version also fixes issues with IEF, Entity Browser and bulk actions.

10 May 2019 - Version 7.0-beta1
----------------------------
  Releasing the fist beta for 8.7.x

10 May 2019 - Version 6.1
----------------------------
  Update to Drupal Core 8.6.16 (SA-CORE-2019-007)

22 March 2019 - Version 6.0
----------------------------
  Security updates:
    Update to EU Cookie Compliance 8.x-1.5 (SA-CONTRIB-2019-033)

  Other updates:
    Drupal Core updates
    Contrib modules and themes updates
    Config updates to start using core Content Moderation instead of Workbench Moderation

28 February 2019 - Version 6.0-beta7
----------------------------
  Update to Drupal Core 8.6.10 (SA-CORE-2019-003)
  Update to Facets 8.x-1.3 (SA-CONTRIB-2019-030)
  Update to Metatag 8.x-1.8 (SA-CONTRIB-2019-021)
  Update to Paragraphs Tool 8.x-1.6 (SA-CONTRIB-2019-023)
  Update to Translation Management Tool 8.x-1.7 (SA-CONTRIB-2019-024)

23 January 2019 - Version 6.0-beta6
----------------------------
  Update several Media modules.
  Fixed Media related errors during installation.
  Fixed Openfed update hooks related with media updates.

17 January 2019 - Version 6.0-beta4 (same as 6.0-beta5)
----------------------------
  Update to Drupal Core 8.6.7 (SA-CORE-2019-001 and SA-CORE-2019-002)
  Fixed Openfed installation issues.

14 December 2018 - Version 6.0-beta3
----------------------------
  Update to Drupal Core 8.6.4
  Updated Several modules:
    bootstrap (3.13.0 => 3.16.0)
    ds (3.1.0 => 3.2.0)
    entity (1.0.0-beta4 => 1.0.0-rc1)
    entity_browser (1.4.0 => 1.6.0)
    entity_reference_revisions (1.4.0 => 1.6.0)
    entityqueue (1.0.0-alpha7 => 1.0.0-alpha8)
    facets (1.0.0-beta2 => 1.2.0)
    fast_404 (1.0.0-alpha2 => 1.0.0-alpha3)
    field_formatter (1.1.0 => 1.2.0)
    google_analytics (2.2.0 => 2.3.0)
    honeypot (1.27.0 => 1.29.0)
    image_effects (1.1.0 => 1.2.0)
    imce (1.6.0 => 1.7.0)
    language_selection_page (2.2.0 => 2.3.0)
    link_attributes (1.3.0 => 1.5.0)
    m4032404 (1.0.0-alpha3 => 1.0.0-alpha4)
    menu_breadcrumb (1.5.0 => 1.7.0)
    token (1.1.0 => 1.5.0)
    metatag (1.5.0 => 1.7.0)
    pathauto (1.2.0 => 1.3.0)
    redirect (1.1.0 => 1.3.0)
    typed_data (1.0.0-alpha1 => 1.0.0-alpha2)
    rules (3.0.0-alpha3 => 3.0.0-alpha4)
    seckit (1.0.0-alpha2 => 1.1.0)
    super_login (1.0.0 => 1.2.0)
    username_enumeration_prevention (1.0.0-beta1 => 1.0.0-beta2)
    webform (5.0.0-rc12 => 5.0.0-rc29)
    weight (3.1.0-alpha1 => 3.1.0-alpha2)
    admin_toolbar (1.24.0 => 1.25.0)
  Added some ckeditor plugins:
    find
    anchor_link
    abbreviation
  Several small improvements
  Updated Paragraphs and patch necessary for translations.

18 October 2018 - Version 6.0-beta2
----------------------------
  Update to Drupal Core 8.6.2 (SA-CORE-2018-006)
  Update to Workbench Moderation 8.x-1.4 (SA-CONTRIB-2018-067)

02 August 2018 - Version 5.4
----------------------------
  Update to Drupal Core 8.5.6 (SA-CORE-2018-005)
  Fixed module versions on the .make file
  Updated EU Cookie Compliance module to version 1.2

24 May 2018 - Version 5.3
----------------------------
  Updates filter format config, allowing a title attribute on a abbr HTML tag
  Added core patch due to issue 2877994

15 May 2018 - Version 5.2
----------------------------
  Updated Disable Route Normalizer module, fixing a Panels related issue

04 May 2018 - Version 5.1
----------------------------
  Updated Menu Block and updated patch

30 April 2018 - Version 5.0
----------------------------
  First release of Openfed 5.x

30 April 2018 - Version 4.8
----------------------------
  Update to Drupal Core 8.4.8 (SA-CORE-2018-004)

29 March 2018 - Version 4.7
----------------------------
  Update to Drupal Core 8.4.6 (SA-CORE-2018-002)

22 February 2018 - Version 4.6
----------------------------
  Update to Drupal Core 8.4.5
  Update CKEditor Upload Image to version 1.5
  Update Bootstrap theme to version 3.10

21 February 2018 - Version 4.5
----------------------------
  Update modules
  Added Workbench Access Menu Link module
  Added patch to EU Cookie Compliance
  Small config updates
  Improved update path for path 4.3

01 February 2018 - Version 4.4
----------------------------
  Issue #2936485 by yo30, rutiolma: Fix dependency declaration
  Issue #2938850 by rutiolma: Add Block visibility conditions
  Enabled admin_toolbar:admin_toolbar_links_access_filter module by default
  Make file: fix typo and updated libraries versions
  Core Bug Fix: Unable to save a translation if the path alias changes
  Wrong active trail with multiple menu items with the same path
  Update core and modules

02 January 2018 - Version 4.3
----------------------------
  Updated Redirect module to a stable version
  Uptated Context module to the recommended version - no update path provided
  Updated Moderation states for Embeddables
  Added openfed_svg_file module and Embeddable SVG configuration

19 December 2017 - Version 4.2
----------------------------
  Update installation procedure.
  Added workbench_moderation_actions module
  Updated patches

12 December 2017 - Version 4.1 (Drupal.org Failure)
----------------------------
  Update modules, including a config_update due to a security issue.
  Update Bootstrap theme due to Drupal 8.4.x update issues.

11 December 2017 - Version 4.0
----------------------------
  Update Core to version 8.4.3
  Module updates
  Patch updates

29 November 2017 - Version 1.5
----------------------------
  Small bug fixes
  Updated patches
  Updated modules
  Updated flexible HTML allowed tags

13 November 2017 - Version 1.4
----------------------------
  New modile append_file_info
  Updated File Entity Module
  Added patches to core and focal point
  Updated flexible HTML allowed tags

26 October 2017 - Version 1.3
----------------------------
  Drupal update to version 8.3.7
  Module updates
  New patches

26 October 2017 - Version 1.3
----------------------------
  Drupal update to version 8.3.7
  Module updates
  New patches

25 October 2017 - Version 1.2
----------------------------
  Drupal update to version 8.3.5
  Module updates
  New patches
  -- Not releasead due to a .make file error

13 July 2017 - Version 1.1
----------------------------
  Issue #2888362: updated Drupal core to 8.3.5

2 June 2017 - Version 1.0
----------------------------
  No changes

1 June 2017 - Version 1.0-rc11
----------------------------
  Upgraded Drupal modules

15 May 2017 - Version 1.0-rc10
----------------------------
  Upgraded Drupal modules

15 May 2017 - Version 1.0-rc9
----------------------------
  Added allowed_formats module
  Added content_translation_workflow module
  Updated display suite module to 3.x version

14 May 2017 - Version 1.0-rc8
----------------------------
  Upgraded Drupal modules

14 May 2017 - Version 1.0-rc7
----------------------------
  Upgraded Drupal modules and Drupal core to 8.3.2
  Added anchor_link module

20 April 2017 - Version 1.0-rc6
----------------------------
  Added federal header module
  Upgraded Drupal modules and Drupal core to 8.3.1

13 March 2017 - Version 1.0-rc5
----------------------------
  Fixes a problem with the drupal.org 1.0-rc4 release.

10 March 2017 - Version 1.0-rc4
----------------------------
  Issue #14: added page_manager module
  Issue #17: Removed menus from basic page config
  Issue #18: Added Flexible HTML missing buttons
  Issue #19: Make Menu Block to follow active trail
  Issue #20: Update search_api_solr and search_api_solr_multilingual to…
  Issue #21: Prevent ctools fatal error
  Issue #23: Upgraded to Drupal 8.3-rc1

3 March 2017 - Version 1.0-rc3
----------------------------
  Issue #6: removed Basic HTML; set Flexible HTML as default input format
  Issue #10: Fixed toolbar_theme installation issue and set permissions…
  Issue #12: Removed extra Field menu tab; Renamed Media menu tab @ con…
  Issue #13: Added Page Body field to the display

2 March 2017 - Version 1.0-rc2
----------------------------
  Release candidate.

1 March 2017 - Version 1.0-rc1
----------------------------
  Release candidate.

17 February 2017 - Version 1.0-beta1
----------------------------
  Beta release.

14 February 2017 - Version 0.4.2
----------------------------
  #111: Updated README file; updated modules.
  #118: Updated Redirect module to beta3.
  #121: Added menu_breadcrumb and menu_trail_by_path modules.
  #123: Added rules module; removed scheduled_updates.

3 February 2017 - Version 0.4.1
----------------------------
  #60: Added Partial Date (dev)
  #77: replaced content_access by workbench_access.
  #112: Replaced content_moderation by workbench_moderation.

24 January 2017 - Version 0.4
----------------------------
  Fourth dev release.

12 December 2016 - Version 0.3
----------------------------
  Third dev release.

22 November 2016 - Version 0.2
----------------------------
  Second dev release.

25 October 2016 - Version 0.1
------------------------------
  First dev release.
