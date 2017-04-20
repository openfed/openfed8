core = 8.x
api = 2
defaults[projects][subdir] = contrib

;
; Libraries
; Please fill the following out. Type may be one of get, git, bzr or svn, and url is the url of the download.
;

libraries[colorbox][download][type] = "get"
libraries[colorbox][download][url] = "https://github.com/jackmoore/colorbox/archive/1.6.3.zip"
libraries[colorbox][directory_name] = "colorbox"
libraries[colorbox][destination] = "libraries"

libraries[uploadimage][download][type] = "get"
libraries[uploadimage][download][url] = "http://download.ckeditor.com/uploadimage/releases/uploadimage_4.6.2.zip"
libraries[uploadimage][directory_name] = "uploadimage"
libraries[uploadimage][destination] = "libraries/ckeditor/plugins"

libraries[uploadwidget][download][type] = "get"
libraries[uploadwidget][download][url] = "http://download.ckeditor.com/uploadwidget/releases/uploadwidget_4.6.2.zip"
libraries[uploadwidget][directory_name] = "uploadwidget"
libraries[uploadwidget][destination] = "libraries/ckeditor/plugins"

libraries[filetools][download][type] = "get"
libraries[filetools][download][url] = "http://download.ckeditor.com/filetools/releases/filetools_4.6.2.zip"
libraries[filetools][download][directory_name] = "ckeditor/plugins/filetools"
libraries[filetools][directory_name] = "filetools"
libraries[filetools][destination] = "libraries/ckeditor/plugins"

libraries[notification][download][type] = "get"
libraries[notification][download][url] = "http://download.ckeditor.com/notification/releases/notification_4.6.2.zip"
libraries[notification][directory_name] = "notification"
libraries[notification][destination] = "libraries/ckeditor/plugins"

libraries[notificationaggregator][download][type] = "get"
libraries[notificationaggregator][download][url] = "http://download.ckeditor.com/notificationaggregator/releases/notificationaggregator_4.6.2.zip"
libraries[notificationaggregator][directory_name] = "notificationaggregator"
libraries[notificationaggregator][destination] = "libraries/ckeditor/plugins"

;
; Themes
; Please fill the following out. Type may be one of get, git, bzr or svn, and url is the url of the download.
;
projects[bootstrap][version] = "3.3"
projects[adminimal_theme][version] = "1.3"

;
; Modules
; Please fill the following out. Type may be one of get, git, bzr or svn, and url is the url of the download.
;
projects[addanother][version] = "1.0-rc1"
projects[admin_toolbar][version] = "1.19"
projects[admin_toolbar][patch][2855786] = "https://www.drupal.org/files/issues/admin_toolbar-hide-items-with-empty-subtrees.patch"
projects[admin_toolbar_content_languages][version] = "1.0-beta2"
projects[alertbox][version] = "1.0-beta3"
projects[back_to_top][version] = "1.0-beta2"
projects[ckeditor_uploadimage][type] = module
projects[ckeditor_uploadimage][download][type] = git
projects[ckeditor_uploadimage][download][revision] = 7e5a65a0b809dec16a14a585bedc56d27cd9b77b
projects[ckeditor_uploadimage][download][branch] = "8.x-1.x"
projects[colorbox][version] = "1.3"
projects[components][version] = "1.0"
projects[config_update][version] = "1.3"
projects[contact_storage][version] = "1.0-beta8"
projects[content_browser][version] = "1.0-alpha4"
projects[context][version] = "1.0-alpha1"
projects[country][version] = "1.0-beta3"
projects[crop][version] = "1.2"
projects[cshs][version] = "1.0-beta3"
projects[cshs][patch][2852474 = "https://www.drupal.org/files/issues/2852474-1.patch"
projects[ctools][version] = "3.0-beta2"
projects[ctools][patch][2831521] = "https://www.drupal.org/files/issues/ctools-unserialize-plugin-collection-2831521-1.patch"
projects[diff][version] = "1.0-rc1"
projects[ds][version] = "3.0-beta2"
projects[embed][version] = "1.0"
projects[entity][version] = "1.0-alpha4"
projects[entity_browser][version] = "1.0"
projects[entity_embed][version] = "1.0-beta2"
projects[entity_reference_revisions][version] = "1.2"
projects[entityqueue][version] = "1.0-alpha6"
projects[eu_cookie_compliance][version] = "1.0-beta7"
projects[extlink][version] = "1.0"
projects[facets][version] = "1.0-alpha9"
projects[fast_404][version] = "1.0-alpha2"
projects[fences][version] = "2.0-alpha1"
projects[field_default_token][download][type] = git
projects[field_default_token][download][revision] = cec380512962857e62a1c050fde77eb8d27652dc
projects[field_default_token][download][branch] = "8.x-1.x"
projects[field_default_token][patch][2860580] = "https://www.drupal.org/files/issues/2841292-7-and-2854384-4.patch"
projects[field_formatter][version] = "1.0"
projects[field_group][version] = "1.0-rc6"
projects[file_entity][version] = "2.0-beta3"
projects[focal_point][version] = "1.0-beta4"
projects[google_analytics][version] = "2.1"
projects[honeypot][version] = "1.24"
projects[image_effects][version] = "1.0"
projects[imce][version] = "1.5"
projects[inline_entity_form][version] = "1.0-beta1"
projects[l10n_client][version] = "1.0-alpha1"
projects[language_cookie][version] = "1.0-beta1"
projects[language_selection_page][version] = "2.0"
projects[layout_plugin][version] = "1.0-alpha23"
projects[link_attributes][version] = "1.0"
projects[linkit][version] = "5.0-beta5"
projects[m4032404][version] = "1.0-alpha3"
projects[media_entity][version] = "1.6"
projects[media_entity_document][version] = "1.1"
projects[media_entity_image][version] = "1.2"
projects[menu_block][version] = "1.4"
projects[menu_block][patch][2756675] = "https://www.drupal.org/files/issues/2811337-13_0.patch"
projects[menu_breadcrumb][version] = "1.0"
projects[menu_trail_by_path][version] = "1.1"
projects[metatag][version] = "1.0"
projects[node_edit_redirect][version] = "1.0-rc2"
projects[ofed_switcher][version] = "1.2"
projects[override_node_options][version] = "2.0"
projects[page_manager][version] = "4.0-beta1"
projects[panels][version] = "4.0-beta1"
projects[paragraphs][download][type] = git
projects[paragraphs][download][revision] = a0b7747a7141ae19686c0d5d226c82e44ac51c7f
projects[paragraphs][download][branch] = "8.x-1.x"
projects[paragraphs][patch][2461695] = "https://www.drupal.org/files/issues/meta_support-2461695-164.patch"
projects[pathauto][version] = "1.0-rc1"
projects[redirect][version] = "1.0-alpha5"
projects[role_delegation][version] = "1.0-alpha1"
projects[rules][version] = "3.0-alpha2"
projects[search_api][version] = "1.0-rc2"
projects[search_api_attachments][version] = "1.0-alpha5"
projects[search_api_autocomplete][version] = "1.0-alpha1"
projects[search_api_solr][version] = "1.0-beta2"
projects[search_api_solr_multilingual][version] = "1.0-beta1"
projects[seckit][version] = "1.0-alpha2"
projects[securelogin][version] = "1.4"
projects[sharemessage][version] = "1.0-beta2"
projects[simple_gmap][version] = "1.2"
projects[simple_sitemap][version] = "2.9"
projects[smart_trim][version] = "1.0"
projects[soundcloudfield][version] = "1.0-alpha1"
projects[soundcloudfield][patch][2760787] = "https://www.drupal.org/files/issues/soundcloudfield-fatal_error_occurs_on_all_pages-2760787-3.patch"
projects[super_login][version] = "1.0-beta2"
projects[tmgmt][version] = "1.1"
projects[token][version] = "1.0-rc1"
projects[toolbar_themes][version] = "1.0-alpha4"
projects[toolbar_themes][patch][2856979] = "https://www.drupal.org/files/issues/config_permission-2856979-6.patch"
projects[twig_field_value][version] = "1.1"
projects[twig_tweak][version] = "1.6"
projects[twitter_block][version] = "3.0-alpha0"
projects[typed_data][version] = "1.0-alpha1"
projects[url_embed][version] = "1.0-alpha1"
projects[username_enumeration_prevention][version] = "1.0-beta1"
projects[username_enumeration_prevention][patch][2483015] = "https://www.drupal.org/files/issues/fix_broken_installation-2483015-9.patch"
projects[video_embed_field][version] = "1.4"
projects[views_slideshow][version] = "4.4"
projects[webform][version] = "5.0-beta11"
projects[weight][type] = module
projects[weight][download][type] = git
projects[weight][download][revision] = 0b710388b2033793be066e88a9bcd078aa01edb6
projects[weight][download][branch] = "8.x-3.x"
projects[workbench_access][version] = "1.0-alpha4"
projects[workbench_moderation][version] = "1.2"
projects[xmlrpc][version] = "1.0-beta1"
