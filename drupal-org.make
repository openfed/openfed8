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
libraries[colorbox][type] = "library"

libraries[uploadimage][download][type] = "get"
libraries[uploadimage][download][url] = "http://download.ckeditor.com/uploadimage/releases/uploadimage_4.6.2.zip"
libraries[uploadimage][download][directory_name] = "ckeditor/plugins/uploadimage"
libraries[uploadimage][download][type] = "library"

libraries[uploadwidget][download][type] = "get"
libraries[uploadwidget][download][url] = "http://download.ckeditor.com/uploadwidget/releases/uploadwidget_4.6.2.zip"
libraries[uploadwidget][download][directory_name] = "ckeditor/plugins/uploadwidget"
libraries[uploadwidget][download][type] = "library"

libraries[filetools][download][type] = "get"
libraries[filetools][download][url] = "http://download.ckeditor.com/filetools/releases/filetools_4.6.2.zip"
libraries[filetools][download][directory_name] = "ckeditor/plugins/filetools"
libraries[filetools][download][type] = "library"

libraries[notification][download][type] = "get"
libraries[notification][download][url] = "http://download.ckeditor.com/notification/releases/notification_4.6.2.zip"
libraries[notification][download][directory_name] = "ckeditor/plugins/notification"
libraries[notification][download][type] = "library"

libraries[notificationaggregator][download][type] = "get"
libraries[notificationaggregator][download][url] = "http://download.ckeditor.com/notificationaggregator/releases/notificationaggregator_4.6.2.zip"
libraries[notificationaggregator][download][directory_name] = "ckeditor/plugins/notificationaggregator"
libraries[notificationaggregator][download][type] = "library"

;
; Themes
; Please fill the following out. Type may be one of get, git, bzr or svn, and url is the url of the download.
;
projects[bootstrap][version] = "3.1"
projects[adminimal_theme][version] = "1.3"

;
; Modules
; Please fill the following out. Type may be one of get, git, bzr or svn, and url is the url of the download.
;
projects[addanother][version] = "1.0-rc1"
projects[admin_toolbar][version] = "1.18"
projects[admin_toolbar][patch][2855720] = "https://www.drupal.org/files/issues/admin_toolbar-bring-back-logout.patch"
projects[admin_toolbar_content_languages][version] = "1.0-beta2"
projects[alertbox][version] = "1.0-beta2"
projects[back_to_top][version] = "1.0-beta2"
projects[ckeditor_uploadimage][version] = "1.3"
projects[colorbox][download][type] = git
projects[colorbox][download][revision] = bf511a8594ef69cbba37ef1ea0e72fb9727d15f6
projects[colorbox][download][branch] = "8.x-1.x"
projects[components][version] = "1.0"
projects[config_update][version] = "1.3"
projects[contact_storage][version] = "1.0-beta8"
projects[content_browser][version] = "1.0-alpha4"
projects[context][version] = "1.0-alpha1"
projects[country][version] = "1.0-beta3"
projects[crop][version] = "1.0"
projects[ctools][version] = "3.0-alpha27"
projects[devel][version] = "1.0-rc1"
projects[diff][version] = "1.0-rc1"
projects[ds][version] = "2.6"
projects[embed][version] = "1.0-rc3"
projects[entity][version] = "1.0-alpha4"
projects[entity_browser][version] = "1.0-rc2"
projects[entity_embed][version] = "1.0-beta2"
projects[entity_reference_revisions][version] = "1.2"
projects[entityqueue][version] = "1.0-alpha6"
projects[eu_cookie_compliance][version] = "1.0-beta7"
projects[extlink][version] = "1.0"
projects[facets][version] = "1.0-alpha7"
projects[fast_404][version] = "1.0-alpha2"
projects[features][version] = "3.2"
projects[fences][version] = "2.0-alpha1"
projects[field_formatter][version] = "1.0"
projects[field_group][version] = "1.0-rc6"
projects[file_entity][version] = "2.0-beta3"
projects[focal_point][version] = "1.0-beta4"
projects[google_analytics][version] = "2.1"
projects[honeypot][version] = "1.23"
projects[image_effects][version] = "1.0-alpha5"
projects[imce][version] = "1.5"
projects[inline_entity_form][version] = "1.0-beta1"
projects[l10n_client][version] = "1.0-alpha1"
projects[language_cookie][version] = "1.0-beta1"
projects[language_selection_page][version] = "2.0"
projects[layout_plugin][version] = "1.0-alpha23"
projects[link_attributes][version] = "1.0"
projects[linkit][version] = "5.0-beta4"
projects[m4032404][version] = "1.0-alpha3"
projects[media_entity][version] = "1.6"
projects[media_entity_document][version] = "1.1"
projects[media_entity_image][version] = "1.2"
projects[menu_block][version] = "1.4"
projects[menu_breadcrumb][version] = "1.0"
projects[menu_trail_by_path][version] = "1.1"
projects[metatag][version] = "1.0"
projects[node_edit_redirect][version] = "1.0-rc2"
projects[ofed_switcher][version] = "1.1"
projects[override_node_options][version] = "2.0"
projects[panels][version] = "3.0-beta5"
projects[paragraphs][download][type] = git
projects[paragraphs][download][revision] = a0b7747a7141ae19686c0d5d226c82e44ac51c7f
projects[paragraphs][download][branch] = "8.x-1.x"
projects[paragraphs][patch][2461695] = "https://www.drupal.org/files/issues/meta_support-2461695-164.patch"
projects[pathauto][version] = "1.0-rc1"
projects[redirect][version] = "1.0-alpha4"
projects[role_delegation][version] = "1.0-alpha1"
projects[rules][version] = "3.0-alpha2"
projects[search_api][version] = "1.0-beta4"
projects[search_api_attachments][version] = "1.0-alpha5"
projects[search_api_autocomplete][version] = "1.0-alpha1"
projects[search_api_solr][version] = "1.0-beta1"
projects[search_api_solr_multilingual][version] = "1.0-alpha3"
projects[seckit][version] = "1.0-alpha2"
projects[securelogin][version] = "1.4"
projects[sharemessage][version] = "1.0-beta2"
projects[shs][version] = "1.0-alpha1"
projects[simple_gmap][version] = "1.2"
projects[simple_sitemap][version] = "2.8"
projects[smart_trim][version] = "1.0"
projects[soundcloudfield][version] = "1.0-alpha1"
projects[soundcloudfield][patch][2760787] = "https://www.drupal.org/files/issues/soundcloudfield-fatal_error_occurs_on_all_pages-2760787-3.patch"
projects[super_login][version] = "1.0-beta2"
projects[tmgmt][version] = "1.1"
projects[token][version] = "1.0-rc1"
projects[toolbar_themes][version] = "1.0-alpha4"
projects[twig_field_value][version] = "1.1"
projects[twig_tweak][version] = "1.5"
projects[twitter_block][version] = "2.1"
projects[typed_data][version] = "1.0-alpha1"
projects[url_embed][version] = "1.0-alpha1"
projects[username_enumeration_prevention][version] = "1.0-beta1"
projects[username_enumeration_prevention][patch][2483015] = "https://www.drupal.org/files/issues/fix_broken_installation-2483015-9.patch"
projects[video_embed_field][version] = "1.4"
projects[views_slideshow][version] = "4.3"
projects[weight][type] = module
projects[weight][download][type] = git
projects[weight][download][revision] = 0b710388b2033793be066e88a9bcd078aa01edb6
projects[weight][download][branch] = "8.x-3.x"
projects[workbench_access][version] = "1.0-alpha2"
projects[workbench_moderation][version] = "1.2"
projects[xmlrpc][version] = "1.0-beta1"
projects[yamlform][version] = "1.0-beta29"