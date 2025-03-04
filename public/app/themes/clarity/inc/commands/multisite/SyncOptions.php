<?php

/**
 * This command is used to get content stats.
 * 
 * Usage:
 *  dry-run:  wp sync-options 2 laa
 *  real-run: wp sync-options 2 laa sync
 */

namespace MOJ\Intranet;

use WP_CLI;
use MOJ\Intranet\Agency;

class SyncOptions
{

    const IGNORE_PREFIXES = [
        '_site_transient_',
        '_transient_',
        '_wp_session_',
        'acf_pro_license_status',
        'acf_escaped_html_notice_dismissed',
        'acf_pro_license',
        'acf_site_health',
        'acf_version',
        'active_plugins',
        'action_scheduler_',
        'admin_email_lifespan',
        'adminhash',
        'amazon_s3_and_cloudfront_pro_version',
        'as3cf_add_metadata_last_started',
        'as3cf_schema_version',
        'as3cf_constant_AS3CF_SETTINGS',
        'auto_core_update_notified',
        'auto_update_',
        'auto_plugin_theme_update_emails',
        'bcn_',
        'bedrock_autoloader',
        'blogname',
        'blog_public',
        'blogdescription',
        'cms_tpv_',
        'cron',
        'current_theme',
        'db_upgraded',
        'db_version',
        'disallowed_keys',
        'dxw-extras_',
        'emergency_', // Always has agency prefix
        // ElasticPress
        'ep_version',
        'ep_feature_requirement_statuses',
        'ep_skip_install',
        'ep_host',
        'ep_credentials',
        'ep_language',
        'ep_last_sync',
        'ep_last_cli_index',
        'ep_sync_history',
        '_moj_es_current_index_name',
        'ewww_',
        'exactdn',
        'finished_updating_',
        'finished_splitting_shared_terms',
        'fresh_site',
        'fus_settings',
        'gadwp_version',
        'hack_file',
        'heatmapWP_',
        'hipchat_',
        'html_type',
        'home',
        'https_detection_errors',
        'instance_uid_totalpoll',
        'initial_db_version',
        'iwp_backup_table_version',
        'law_',
        'link_manager_enabled',
        'limit_login_',
        'mailserver_',
        'maintenance_options',
        'markdown',
        'markdown_version',
        'mic_make2x',
        'moj_plugin_versions',
        'need_to_know_', // Always has agency prefix
        'new_admin_email',
        'oasiswf_',
        // The agency wide banner should stay on blog id 1.
        'options_agency_wide_banner_',
        '_options_agency_wide_banner_',
        'options_enable_agency_wide_banner',
        '_options_enable_agency_wide_banner',
        'options_beta_banner_text', // Always has agency prefix
        '_options_beta_banner_text', // Always has agency prefix
        'pods_component_settings',
        'manage-multiple-blogs',
        'masterslider_',
        'megamenu_',
        'msp_',
        'moj_component_settings_popup_message_cache',
        'mtphr_post_duplicator_settings',
        'nestedpages_',
        'page_for_posts', // Should be set later
        'page_on_front', // Should be set later
        'pods_framework_',
        'post-type-',
        'options_prior_political_party_banners',
        '_options_prior_political_party_banners',
        'recently_edited',
        'recently_activated',
        'recency_bonus',
        'recovery_keys',
        'recovery_mode_',
        'redirection_options',
        'relevanssi_',
        'relenvassi_',
        'rewrite_media_to_s3_',
        'rewrite_rules',
        'schema-ActionScheduler_',
        'sidebars_widgets', // Should be set later
        'siteurl',
        "smplsess",
        '_split_terms',
        'sticky_posts',
        'stylesheet',
        'stylesheet_root',
        'switchuser_recent_imp_users',
        'template',
        'template_root',
        'theme_mods_govintranetpress',
        'theme_mods_twenty',
        'theme_mods_mojintranet',
        'theme_mods_intranet-theme-clarity',
        'theme_switched',
        'tikemp_recent_imp_users',
        'uninstall_plugins',
        'update_replace_s3_urls_session',
        'upgrade_to_pro',
        'user_role_editor',
        'ure_',
        'user_count',
        'wblm_',
        'wp_backup_user_roles',
        'widget_',
        'wp_calendar_block_has_published_posts',
        'wp_crontrol_',
        'wp_force_deactivated_plugins',
        'wp_page_for_privacy_policy', // This will need to be set manually on each site.
        'wp_user_roles',
        'wpe-',
        'wpe_',
        'wpengine_',
        'WPLANG'
    ];

    const IGNORE_SUFFIXES = [
        // These are database values related to deprecated plugins or code.
        '_homepage_feature_item_1',
        '_homepage_feature_item_2',
        // Homepage slider
        '_homepage_slide_headline_1',
        '_homepage_slide_headline_2',
        '_homepage_slide_headline_3',
        '_homepage_slide_image_1',
        '_homepage_slide_image_2',
        '_homepage_slide_image_3',
        '_homepage_slide_alt_text_1',
        '_homepage_slide_alt_text_2',
        '_homepage_slide_alt_text_3',
        '_homepage_slide_url_1',
        '_homepage_slide_url_2',
        '_homepage_slide_url_3',
        // Homepage video
        'homepage_video_switch',
        '_homepage_video_title',
        '_homepage_video_excerpt',
        '_homepage_video_youtubeurl',
        // Homepage pols
        '_homepage_polls_shortcode',
        // Notifications
        '_notified',
        // typo for __jo
        '__JO',
        // Typo, single _ near end.
        'options_ospt_feature_item_right_news_ospt',
        '_options_ospt_feature_item_right_news_ospt',
        // visited links - deprecated
        '_visited_links',
        // For empty fields like `agency_children`
        '_children',
        // This is an old way to tie taxonomy terms in to agencies. It's now stored in the wp_termmeta table 
        // with meta_keys like term_used_by and _term_used_by.
        // It's entered on a page like this:
        // https://intranet.justice.gov.uk/wp/wp-admin/edit-tags.php?taxonomy=campaign_category&post_type=news
        // And it's how we know what Campaign Categoreies to show on edit screens.
        // https://intranet.justice.gov.uk/wp/wp-admin/post-new.php?post_type=news
        '_term_used_by',
    ];

    const COPY_PREFIXES = [
        'admin_email',
        'avatar_rating',
        'avatar_default',
        'blog_charset',
        'can_compress_scripts',
        'category_base',
        'comment_order',
        'comment_moderation',
        'comment_registration',
        'comment_max_links',
        'comment_previously_approved',
        'comments_notify',
        'comments_per_page',
        // Other comment fields
        'close_comments_for_old_posts',
        'close_comments_days_old',
        'thread_comments',
        'thread_comments_depth',
        'page_comments',
        'date_format',
        'default_category',
        'default_comments_page',
        'default_comment_status',
        'default_email_category',
        'default_link_category',
        'default_ping_status',
        'default_pingback_flag',
        'default_post_format',
        'default_role',
        'gmt_offset',
        'ep_feature_settings',
        'ep_bulk_setting',
        'elasticpress_weighting',
        'links_updated_date_format',
        'nav_menu_options',
        'moderation_keys',
        'moderation_notify',
        'moj_component_settings',
        'permalink_structure',
        'ping_sites',
        'posts_per_page',
        'posts_per_rss',
        'require_name_email',
        'rss_use_excerpt',
        'show_avatars',
        'show_comments_cookies_opt_in',
        'show_on_front',
        'site_icon',
        'start_of_week',
        'tag_base',
        'tantan_wordpress_s3',
        'theme_mods_clarity',
        'time_format',
        'timezone_string',
        'uploads_use_yearmonth_folders',
        'upload_path',
        'upload_url_path',
        'users_can_register',
        'use_balanceTags',
        'use_smilies',
        'use_trackback',
        'using_application_passwords',
        'wp_attachment_pages_enabled',
        // totalpoll
        "totalpoll_hide_global_pointers",
        "totalpoll_hide_welcome",
        "totalpoll_hide_poll_pointers",
        "totalpoll_db_version",
        "totalpoll_options_repository",
        "totalpoll_first_usage",
        "totalpoll_tracking",
        "totalpoll_onboarding",
        "totalpoll-lite_review",
        // Image sizes
        "thumbnail_size_w",
        "thumbnail_size_h",
        "thumbnail_crop",
        "medium_size_w",
        "medium_size_h",
        "large_size_w",
        "large_size_h",
        "medium_large_size_w",
        "medium_large_size_h",
        // images
        'image_default_link_type',
        'image_default_size',
        'image_default_align',
    ];

    const COPY_SUFFIXES = [];

    public function __invoke($args): void
    {
        WP_CLI::log('SyncOptions: starting');

        // Get the target blog id and agency name from the arguments.
        $blog_id = $args[0];
        $agency_slug = $args[1];

        if (empty($args[0]) || empty($args[1])) {
            WP_CLI::log('SyncOptions: missing arguments');
            return;
        }

        WP_CLI::log('SyncOptions: getting all options from the database');

        global $wpdb;

        $suppress      = $wpdb->suppress_errors();
        $alloptions_db = $wpdb->get_results("SELECT option_name, option_value, autoload FROM $wpdb->options");

        $wpdb->suppress_errors($suppress);

        $options = array();
        foreach ((array) $alloptions_db as $o) {
            $options[$o->option_name] = [$o->option_value, $o->autoload];
        }

        WP_CLI::log('SyncOptions: getting options for transform');
        $transform_options = $this->getAgencyFieldsToTransform($agency_slug);

        WP_CLI::log('SyncOptions: getting options to ignore for this agency');
        $ignore_agency_options = $this->getAgencyFieldsToIgnore($agency_slug);

        WP_CLI::log('SyncOptions: getting other agencies to ignore');
        $other_agency_slugs = $this->getOtherAgencySlugs($agency_slug);

        WP_CLI::log('SyncOptions: getting options for other agencies to ignore');
        // Loop the other agencies and get their options
        foreach ($other_agency_slugs as $other_agency_slug) {
            $other_agency_options = array_keys($this->getAgencyFieldsToTransform($other_agency_slug));
            $other_agency_options_2 = $this->getAgencyFieldsToIgnore($other_agency_slug);
            $ignore_agency_options = array_merge($ignore_agency_options, $other_agency_options, $other_agency_options_2);
        }


        // Loop through every option on blog id 1.
        // Assign it into 3 categories:
        // 1. Options to ignore, i.e. they are from unused plugins, or from different agencies.
        // 2. Options to sync, i.e. they are from active plugins.
        // 3. Options to modify before syncing, i.e. they contain an agency name.

        // Get all options
        // $options = wp_load_alloptions();
        $options_to_ignore = [];
        $options_to_sync = [];
        $options_to_modify = [];

        foreach ($options as $option_name => $option_value) {
            // Ignore options that start with any entry in self::IGNORE_PREFIXES, use regex
            if (preg_match('/^(' . implode('|', self::IGNORE_PREFIXES) . ')/', $option_name)) {
                $options_to_ignore[$option_name] = $option_value;
                continue;
            }

            // Ignore options that end with any entry in self::IGNORE_SUFFIXES, use regex
            if (preg_match('/(' . implode('|', self::IGNORE_SUFFIXES) . ')$/', $option_name)) {
                $options_to_ignore[$option_name] = $option_value;
                continue;
            }

            // Ignore options from other agencies if they are in $ignore_agency_options
            if (in_array($option_name, $ignore_agency_options)) {
                $options_to_ignore[$option_name] = $option_value;
                continue;
            }

            // Copy options that start with any entry in self::COPY_PREFIXES
            if (preg_match('/^(' . implode('|', self::COPY_PREFIXES) . ')/', $option_name)) {
                $options_to_sync[$option_name] = $option_value;
                continue;
            }

            // Sync options that are in the transform list
            if (array_key_exists($option_name, $transform_options)) {
                // Log the options that are being transformed
                // WP_CLI::log('SyncOptions: transforming ' . $option_name . ' to ' . $transform_options[$option_name]);
                $options_to_modify[$transform_options[$option_name]] = $option_value;
                continue;
            }

            // Log the uncategorised options
            WP_CLI::log('SyncOptions: uncategorised option ' . $option_name);
            WP_CLI::log('SyncOptions: value ' . json_encode($option_value, JSON_PRETTY_PRINT));
        }


        if (empty($args[2]) || $args[2] !== 'sync') {

            // Log the numbers of options in each category
            WP_CLI::log('SyncOptions: options to ignore: ' . count($options_to_ignore));
            WP_CLI::log('SyncOptions: options to sync: ' . count($options_to_sync));
            WP_CLI::log('SyncOptions: options to modify: ' . count($options_to_modify));

            // Log the options that weren't uncategorised
            $uncategorised = count($options) - count($options_to_ignore) - count($options_to_sync) - count($options_to_modify);
            WP_CLI::log('SyncOptions: uncategorised options: ' . $uncategorised);

            WP_CLI::log('SyncOptions: dry run complete');
            return;
        }
        
        // Do the sync here
        WP_CLI::log('SyncOptions: switching blog');
        // Set the blog id
        switch_to_blog($blog_id);
        
        WP_CLI::log('SyncOptions: updating options');
        // Sync the options
        foreach ($options_to_sync as $option_name => $option_value) {
            update_option($option_name, $option_value[0], $option_value[1] === 'yes');
        }

        foreach ($options_to_modify as $option_name => $option_value) {
            update_option($option_name, $option_value[0], $option_value[1] === 'yes');
        }

        WP_CLI::log('SyncOptions: complete');
    }

    public function getOtherAgencySlugs($agency_slug)
    {
        $all_agencies = (new Agency())->getList();

        $other_agencies = array_filter($all_agencies, function ($agency) use ($agency_slug) {
            return $agency['shortcode'] !== $agency_slug && $agency['is_integrated'];
        });

        return [
            ...array_keys($other_agencies),
            // Test agency
            'test',
            // typo
            'ostp',
            // Old agencies
            'ppo',
            // Empty string, because there are some entries like `options__quick_links_menu_title_17`
            ''
        ];
    }

    public function getAgencyFieldsToTransform($agency_slug)
    {
        return [
            "{$agency_slug}_featured_story1" => 'featured_story1',
            "{$agency_slug}_featured_story2" => 'featured_story2',
            "options_{$agency_slug}_quick_links_0_quick_link_title" => 'options_quick_links_0_quick_link_title',
            "options_{$agency_slug}_quick_links_1_quick_link_title" => 'options_quick_links_1_quick_link_title',
            "options_{$agency_slug}_quick_links_2_quick_link_title" => 'options_quick_links_2_quick_link_title',
            "options_{$agency_slug}_quick_links_3_quick_link_title" => 'options_quick_links_3_quick_link_title',
            "options_{$agency_slug}_quick_links_4_quick_link_title" => 'options_quick_links_4_quick_link_title',
            "options_{$agency_slug}_quick_links_5_quick_link_title" => 'options_quick_links_5_quick_link_title',
            "options_{$agency_slug}_quick_links_6_quick_link_title" => 'options_quick_links_6_quick_link_title',
            "options_{$agency_slug}_quick_links_7_quick_link_title" => 'options_quick_links_7_quick_link_title',
            "options_{$agency_slug}_quick_links_8_quick_link_title" => 'options_quick_links_8_quick_link_title',
            "options_{$agency_slug}_quick_links_9_quick_link_title" => 'options_quick_links_9_quick_link_title',
            "options_{$agency_slug}_quick_links_10_quick_link_title" => 'options_quick_links_10_quick_link_title',
            "options_{$agency_slug}_quick_links_11_quick_link_title" => 'options_quick_links_11_quick_link_title',
            "options_{$agency_slug}_quick_links_12_quick_link_title" => 'options_quick_links_12_quick_link_title',
            "options_{$agency_slug}_quick_links_13_quick_link_title" => 'options_quick_links_13_quick_link_title',
            "options_{$agency_slug}_quick_links_14_quick_link_title" => 'options_quick_links_14_quick_link_title',
            "options_{$agency_slug}_quick_links_15_quick_link_title" => 'options_quick_links_15_quick_link_title',
            "options_{$agency_slug}_quick_links_16_quick_link_title" => 'options_quick_links_16_quick_link_title',
            "options_{$agency_slug}_quick_links_17_quick_link_title" => 'options_quick_links_17_quick_link_title',
            "options_{$agency_slug}_quick_links_18_quick_link_title" => 'options_quick_links_18_quick_link_title',
            "options_{$agency_slug}_quick_links_19_quick_link_title" => 'options_quick_links_19_quick_link_title',
            "options_{$agency_slug}_quick_links_20_quick_link_title" => 'options_quick_links_20_quick_link_title',
            "_options_{$agency_slug}_quick_links_0_quick_link_title" => '_options_quick_links_0_quick_link_title',
            "_options_{$agency_slug}_quick_links_1_quick_link_title" => '_options_quick_links_1_quick_link_title',
            "_options_{$agency_slug}_quick_links_2_quick_link_title" => '_options_quick_links_2_quick_link_title',
            "_options_{$agency_slug}_quick_links_3_quick_link_title" => '_options_quick_links_3_quick_link_title',
            "_options_{$agency_slug}_quick_links_4_quick_link_title" => '_options_quick_links_4_quick_link_title',
            "_options_{$agency_slug}_quick_links_5_quick_link_title" => '_options_quick_links_5_quick_link_title',
            "_options_{$agency_slug}_quick_links_6_quick_link_title" => '_options_quick_links_6_quick_link_title',
            "_options_{$agency_slug}_quick_links_7_quick_link_title" => '_options_quick_links_7_quick_link_title',
            "_options_{$agency_slug}_quick_links_8_quick_link_title" => '_options_quick_links_8_quick_link_title',
            "_options_{$agency_slug}_quick_links_9_quick_link_title" => '_options_quick_links_9_quick_link_title',
            "_options_{$agency_slug}_quick_links_10_quick_link_title" => '_options_quick_links_10_quick_link_title',
            "_options_{$agency_slug}_quick_links_11_quick_link_title" => '_options_quick_links_11_quick_link_title',
            "_options_{$agency_slug}_quick_links_12_quick_link_title" => '_options_quick_links_12_quick_link_title',
            "_options_{$agency_slug}_quick_links_13_quick_link_title" => '_options_quick_links_13_quick_link_title',
            "_options_{$agency_slug}_quick_links_14_quick_link_title" => '_options_quick_links_14_quick_link_title',
            "_options_{$agency_slug}_quick_links_15_quick_link_title" => '_options_quick_links_15_quick_link_title',
            "_options_{$agency_slug}_quick_links_16_quick_link_title" => '_options_quick_links_16_quick_link_title',
            "_options_{$agency_slug}_quick_links_17_quick_link_title" => '_options_quick_links_17_quick_link_title',
            "_options_{$agency_slug}_quick_links_18_quick_link_title" => '_options_quick_links_18_quick_link_title',
            "_options_{$agency_slug}_quick_links_19_quick_link_title" => '_options_quick_links_19_quick_link_title',
            "_options_{$agency_slug}_quick_links_20_quick_link_title" => '_options_quick_links_20_quick_link_title',
            "options_{$agency_slug}_quick_links" => 'options_quick_links',
            "_options_{$agency_slug}_quick_links" => '_options_quick_links',
            // Banner
            "{$agency_slug}_banner_image_enable" => 'banner_image_enable',
            "{$agency_slug}_banner_image" => 'banner_image',
            "{$agency_slug}_banner_link" => 'banner_link',
            "{$agency_slug}_banner_alt" => 'banner_alt',
            "{$agency_slug}_banner_image_side_enable" => 'banner_image_side_enable',
            "{$agency_slug}_banner_image_side" => 'banner_image_side',
            "{$agency_slug}_banner_image_side_title" => 'banner_image_side_title',
            "{$agency_slug}_banner_link_side" => 'banner_link_side',
            "{$agency_slug}_banner_alt_side"  => 'banner_alt_side',
            // Need to know
            "{$agency_slug}_need_to_know_headline1" => 'need_to_know_headline1',
            "{$agency_slug}_need_to_know_url1" => 'need_to_know_url1',
            "{$agency_slug}_need_to_know_image1" => 'need_to_know_image1',
            "{$agency_slug}_need_to_know_alt1" => 'need_to_know_alt1',
            "{$agency_slug}_need_to_know_headline2" => 'need_to_know_headline2',
            "{$agency_slug}_need_to_know_url2" => 'need_to_know_url2',
            "{$agency_slug}_need_to_know_image2" => 'need_to_know_image2',
            "{$agency_slug}_need_to_know_alt2" => 'need_to_know_alt2',
            "{$agency_slug}_need_to_know_headline3" => 'need_to_know_headline3',
            "{$agency_slug}_need_to_know_url3" => 'need_to_know_url3',
            "{$agency_slug}_need_to_know_image3" => 'need_to_know_image3',
            "{$agency_slug}_need_to_know_alt3" => 'need_to_know_alt3',
            // Beta banner
            "options_{$agency_slug}_enable_beta_message" => 'options_enable_beta_message',
            "_options_{$agency_slug}_enable_beta_message" => '_options_enable_beta_message',
            "options_{$agency_slug}_beta_banner_text" => 'options_beta_banner_text',
            "_options_{$agency_slug}_beta_banner_text" => '_options_beta_banner_text',
            // Emergency 1 - is this deprecated, even though it's in the codebase?
            "{$agency_slug}_emergency_toggle" => 'emergency_toggle',
            "{$agency_slug}_emergency_title" => 'emergency_title',
            "{$agency_slug}_emergency_type" => 'emergency_type',
            "{$agency_slug}_homepage_control_emergency_message" => 'homepage_control_emergency_message',
            "{$agency_slug}_emergency_date" => 'emergency_date',
            // Emergency banner 2?
            "options_{$agency_slug}_enable_notification" => 'options_enable_notification',
            "_options_{$agency_slug}_enable_notification" => '_options_enable_notification',
            "options_{$agency_slug}_notification_title" => 'options_notification_title',
            "_options_{$agency_slug}_notification_title" => '_options_notification_title',
            "options_{$agency_slug}_notification_message" => 'options_notification_message',
            "_options_{$agency_slug}_notification_message" => '_options_notification_message',
            "options_{$agency_slug}_notification_date" => 'options_notification_date',
            "_options_{$agency_slug}_notification_date" => '_options_notification_date',
            "options_{$agency_slug}_notification_type" => 'options_notification_type',
            "_options_{$agency_slug}_notification_type" => '_options_notification_type',
            // Full width banner
            "options_{$agency_slug}_check_box_to_turn_banner_on" => 'options_check_box_to_turn_banner_on',
            "_options_{$agency_slug}_check_box_to_turn_banner_on" => '_options_check_box_to_turn_banner_on',
            "options_{$agency_slug}_homepage_banner_image" => 'options_homepage_banner_image',
            "_options_{$agency_slug}_homepage_banner_image" => '_options_homepage_banner_image',
            "options_{$agency_slug}_homepage_banner_link" => 'options_homepage_banner_link',
            "_options_{$agency_slug}_homepage_banner_link" => '_options_homepage_banner_link',
            "options_{$agency_slug}_homepage_banner_alt_text" => 'options_homepage_banner_alt_text',
            "_options_{$agency_slug}_homepage_banner_alt_text" => '_options_homepage_banner_alt_text',
            // Banner right
            "options_{$agency_slug}_enable_banner_right_side" => 'options_enable_banner_right_side',
            "_options_{$agency_slug}_enable_banner_right_side" => '_options_enable_banner_right_side',
            // Banner sidebar header
            "options_{$agency_slug}_banner_sidebar_header" => 'options_banner_sidebar_header',
            "_options_{$agency_slug}_banner_sidebar_header" => '_options_banner_sidebar_header',
            // Homepage banner sidebar
            "options_{$agency_slug}_homepage_sidebar_banner_image" => 'options_homepage_sidebar_banner_image',
            "_options_{$agency_slug}_homepage_sidebar_banner_image" => '_options_homepage_sidebar_banner_image',
            "options_{$agency_slug}_homepage_sidebar_banner_link" => 'options_homepage_sidebar_banner_link',
            "_options_{$agency_slug}_homepage_sidebar_banner_link" => '_options_homepage_sidebar_banner_link',
            "options_{$agency_slug}_homepage_sidebar_banner_alt_text" => 'options_homepage_sidebar_banner_alt_text',
            "_options_{$agency_slug}_homepage_sidebar_banner_alt_text" => '_options_homepage_sidebar_banner_alt_text',
            // 
            "options_{$agency_slug}_post_type_list" => 'options_post_type_list',
            "_options_{$agency_slug}_post_type_list" => '_options_post_type_list',
            // 
            "options_{$agency_slug}_agency_list" => 'options_agency_list',
            "_options_{$agency_slug}_agency_list" => '_options_agency_list',
            // 
            "options_{$agency_slug}_post_type_list_right" => 'options_post_type_list_right',
            "_options_{$agency_slug}_post_type_list_right" => '_options_post_type_list_right',
            //
            "options_{$agency_slug}_agency_list_right" => 'options_agency_list_right',
            "_options_{$agency_slug}_agency_list_right" => '_options_agency_list_right',
            // Quick links menu
            "options_{$agency_slug}_quick_links_menu_title_1" => 'options_quick_links_menu_title_1',
            "options_{$agency_slug}_quick_links_menu_title_2" => 'options_quick_links_menu_title_2',
            "options_{$agency_slug}_quick_links_menu_title_3" => 'options_quick_links_menu_title_3',
            "options_{$agency_slug}_quick_links_menu_title_4" => 'options_quick_links_menu_title_4',
            "options_{$agency_slug}_quick_links_menu_title_5" => 'options_quick_links_menu_title_5',
            "options_{$agency_slug}_quick_links_menu_title_6" => 'options_quick_links_menu_title_6',
            "options_{$agency_slug}_quick_links_menu_title_7" => 'options_quick_links_menu_title_7',
            "options_{$agency_slug}_quick_links_menu_title_8" => 'options_quick_links_menu_title_8',
            "options_{$agency_slug}_quick_links_menu_title_9" => 'options_quick_links_menu_title_9',
            "options_{$agency_slug}_quick_links_menu_title_10" => 'options_quick_links_menu_title_10',
            "options_{$agency_slug}_quick_links_menu_title_11" => 'options_quick_links_menu_title_11',
            "options_{$agency_slug}_quick_links_menu_title_12" => 'options_quick_links_menu_title_12',
            "options_{$agency_slug}_quick_links_menu_title_13" => 'options_quick_links_menu_title_13',
            "options_{$agency_slug}_quick_links_menu_title_14" => 'options_quick_links_menu_title_14',
            "options_{$agency_slug}_quick_links_menu_title_15" => 'options_quick_links_menu_title_15',
            "options_{$agency_slug}_quick_links_menu_title_16" => 'options_quick_links_menu_title_16',
            "options_{$agency_slug}_quick_links_menu_title_17" => 'options_quick_links_menu_title_17',
            "options_{$agency_slug}_quick_links_menu_title_18" => 'options_quick_links_menu_title_18',
            "options_{$agency_slug}_quick_links_menu_title_19" => 'options_quick_links_menu_title_19',
            "options_{$agency_slug}_quick_links_menu_title_20" => 'options_quick_links_menu_title_20',
            "_options_{$agency_slug}_quick_links_menu_title_1" => '_options_quick_links_menu_title_1',
            "_options_{$agency_slug}_quick_links_menu_title_2" => '_options_quick_links_menu_title_2',
            "_options_{$agency_slug}_quick_links_menu_title_3" => '_options_quick_links_menu_title_3',
            "_options_{$agency_slug}_quick_links_menu_title_4" => '_options_quick_links_menu_title_4',
            "_options_{$agency_slug}_quick_links_menu_title_5" => '_options_quick_links_menu_title_5',
            "_options_{$agency_slug}_quick_links_menu_title_6" => '_options_quick_links_menu_title_6',
            "_options_{$agency_slug}_quick_links_menu_title_7" => '_options_quick_links_menu_title_7',
            "_options_{$agency_slug}_quick_links_menu_title_8" => '_options_quick_links_menu_title_8',
            "_options_{$agency_slug}_quick_links_menu_title_9" => '_options_quick_links_menu_title_9',
            "_options_{$agency_slug}_quick_links_menu_title_10" => '_options_quick_links_menu_title_10',
            "_options_{$agency_slug}_quick_links_menu_title_11" => '_options_quick_links_menu_title_11',
            "_options_{$agency_slug}_quick_links_menu_title_12" => '_options_quick_links_menu_title_12',
            "_options_{$agency_slug}_quick_links_menu_title_13" => '_options_quick_links_menu_title_13',
            "_options_{$agency_slug}_quick_links_menu_title_14" => '_options_quick_links_menu_title_14',
            "_options_{$agency_slug}_quick_links_menu_title_15" => '_options_quick_links_menu_title_15',
            "_options_{$agency_slug}_quick_links_menu_title_16" => '_options_quick_links_menu_title_16',
            "_options_{$agency_slug}_quick_links_menu_title_17" => '_options_quick_links_menu_title_17',
            "_options_{$agency_slug}_quick_links_menu_title_18" => '_options_quick_links_menu_title_18',
            "_options_{$agency_slug}_quick_links_menu_title_19" => '_options_quick_links_menu_title_19',
            "_options_{$agency_slug}_quick_links_menu_title_20" => '_options_quick_links_menu_title_20',
            "options_{$agency_slug}_quick_links_menu_link_1" => 'options_quick_links_menu_link_1',
            "options_{$agency_slug}_quick_links_menu_link_2" => 'options_quick_links_menu_link_2',
            "options_{$agency_slug}_quick_links_menu_link_3" => 'options_quick_links_menu_link_3',
            "options_{$agency_slug}_quick_links_menu_link_4" => 'options_quick_links_menu_link_4',
            "options_{$agency_slug}_quick_links_menu_link_5" => 'options_quick_links_menu_link_5',
            "options_{$agency_slug}_quick_links_menu_link_6" => 'options_quick_links_menu_link_6',
            "options_{$agency_slug}_quick_links_menu_link_7" => 'options_quick_links_menu_link_7',
            "options_{$agency_slug}_quick_links_menu_link_8" => 'options_quick_links_menu_link_8',
            "options_{$agency_slug}_quick_links_menu_link_9" => 'options_quick_links_menu_link_9',
            "options_{$agency_slug}_quick_links_menu_link_10" => 'options_quick_links_menu_link_10',
            "options_{$agency_slug}_quick_links_menu_link_11" => 'options_quick_links_menu_link_11',
            "options_{$agency_slug}_quick_links_menu_link_12" => 'options_quick_links_menu_link_12',
            "options_{$agency_slug}_quick_links_menu_link_13" => 'options_quick_links_menu_link_13',
            "options_{$agency_slug}_quick_links_menu_link_14" => 'options_quick_links_menu_link_14',
            "options_{$agency_slug}_quick_links_menu_link_15" => 'options_quick_links_menu_link_15',
            "options_{$agency_slug}_quick_links_menu_link_16" => 'options_quick_links_menu_link_16',
            "options_{$agency_slug}_quick_links_menu_link_17" => 'options_quick_links_menu_link_17',
            "options_{$agency_slug}_quick_links_menu_link_18" => 'options_quick_links_menu_link_18',
            "options_{$agency_slug}_quick_links_menu_link_19" => 'options_quick_links_menu_link_19',
            "options_{$agency_slug}_quick_links_menu_link_20" => 'options_quick_links_menu_link_20',
            "_options_{$agency_slug}_quick_links_menu_link_1" => '_options_quick_links_menu_link_1',
            "_options_{$agency_slug}_quick_links_menu_link_2" => '_options_quick_links_menu_link_2',
            "_options_{$agency_slug}_quick_links_menu_link_3" => '_options_quick_links_menu_link_3',
            "_options_{$agency_slug}_quick_links_menu_link_4" => '_options_quick_links_menu_link_4',
            "_options_{$agency_slug}_quick_links_menu_link_5" => '_options_quick_links_menu_link_5',
            "_options_{$agency_slug}_quick_links_menu_link_6" => '_options_quick_links_menu_link_6',
            "_options_{$agency_slug}_quick_links_menu_link_7" => '_options_quick_links_menu_link_7',
            "_options_{$agency_slug}_quick_links_menu_link_8" => '_options_quick_links_menu_link_8',
            "_options_{$agency_slug}_quick_links_menu_link_9" => '_options_quick_links_menu_link_9',
            "_options_{$agency_slug}_quick_links_menu_link_10" => '_options_quick_links_menu_link_10',
            "_options_{$agency_slug}_quick_links_menu_link_11" => '_options_quick_links_menu_link_11',
            "_options_{$agency_slug}_quick_links_menu_link_12" => '_options_quick_links_menu_link_12',
            "_options_{$agency_slug}_quick_links_menu_link_13" => '_options_quick_links_menu_link_13',
            "_options_{$agency_slug}_quick_links_menu_link_14" => '_options_quick_links_menu_link_14',
            "_options_{$agency_slug}_quick_links_menu_link_15" => '_options_quick_links_menu_link_15',
            "_options_{$agency_slug}_quick_links_menu_link_16" => '_options_quick_links_menu_link_16',
            "_options_{$agency_slug}_quick_links_menu_link_17" => '_options_quick_links_menu_link_17',
            "_options_{$agency_slug}_quick_links_menu_link_18" => '_options_quick_links_menu_link_18',
            "_options_{$agency_slug}_quick_links_menu_link_19" => '_options_quick_links_menu_link_19',
            "_options_{$agency_slug}_quick_links_menu_link_20" => '_options_quick_links_menu_link_20',
            // Most popular
            "options_{$agency_slug}_most_popular_title" => 'options_most_popular_title',
            "_options_{$agency_slug}_most_popular_title" => '_options_most_popular_title',
            "options_{$agency_slug}_most_popular_link_1" => 'options_most_popular_link_1',
            "options_{$agency_slug}_most_popular_link_2" => 'options_most_popular_link_2',
            "options_{$agency_slug}_most_popular_link_3" => 'options_most_popular_link_3',
            "options_{$agency_slug}_most_popular_link_4" => 'options_most_popular_link_4',
            "options_{$agency_slug}_most_popular_link_5" => 'options_most_popular_link_5',
            "_options_{$agency_slug}_most_popular_link_1" => '_options_most_popular_link_1',
            "_options_{$agency_slug}_most_popular_link_2" => '_options_most_popular_link_2',
            "_options_{$agency_slug}_most_popular_link_3" => '_options_most_popular_link_3',
            "_options_{$agency_slug}_most_popular_link_4" => '_options_most_popular_link_4',
            "_options_{$agency_slug}_most_popular_link_5" => '_options_most_popular_link_5',
            "options_{$agency_slug}_most_popular_text_1" => 'options_most_popular_text_1',
            "options_{$agency_slug}_most_popular_text_2" => 'options_most_popular_text_2',
            "options_{$agency_slug}_most_popular_text_3" => 'options_most_popular_text_3',
            "options_{$agency_slug}_most_popular_text_4" => 'options_most_popular_text_4',
            "options_{$agency_slug}_most_popular_text_5" => 'options_most_popular_text_5',
            "_options_{$agency_slug}_most_popular_text_1" => '_options_most_popular_text_1',
            "_options_{$agency_slug}_most_popular_text_2" => '_options_most_popular_text_2',
            "_options_{$agency_slug}_most_popular_text_3" => '_options_most_popular_text_3',
            "_options_{$agency_slug}_most_popular_text_4" => '_options_most_popular_text_4',
            "_options_{$agency_slug}_most_popular_text_5" => '_options_most_popular_text_5',
            // External services
            "options_{$agency_slug}_external_services" => 'options_external_services',
            "_options_{$agency_slug}_external_services" => '_options_external_services',
            // External services title
            "options_{$agency_slug}_external_services_external_services_title_1" => 'options_external_services_external_services_title_1',
            "options_{$agency_slug}_external_services_external_services_title_2" => 'options_external_services_external_services_title_2',
            "options_{$agency_slug}_external_services_external_services_title_3" => 'options_external_services_external_services_title_3',
            "options_{$agency_slug}_external_services_external_services_title_4" => 'options_external_services_external_services_title_4',
            "options_{$agency_slug}_external_services_external_services_title_5" => 'options_external_services_external_services_title_5',
            "options_{$agency_slug}_external_services_external_services_title_6" => 'options_external_services_external_services_title_6',
            "options_{$agency_slug}_external_services_external_services_title_7" => 'options_external_services_external_services_title_7',
            "options_{$agency_slug}_external_services_external_services_title_8" => 'options_external_services_external_services_title_8',
            "options_{$agency_slug}_external_services_external_services_title_9" => 'options_external_services_external_services_title_9',
            "options_{$agency_slug}_external_services_external_services_title_10" => 'options_external_services_external_services_title_10',
            "_options_{$agency_slug}_external_services_external_services_title_1" => '_options_external_services_external_services_title_1',
            "_options_{$agency_slug}_external_services_external_services_title_2" => '_options_external_services_external_services_title_2',
            "_options_{$agency_slug}_external_services_external_services_title_3" => '_options_external_services_external_services_title_3',
            "_options_{$agency_slug}_external_services_external_services_title_4" => '_options_external_services_external_services_title_4',
            "_options_{$agency_slug}_external_services_external_services_title_5" => '_options_external_services_external_services_title_5',
            "_options_{$agency_slug}_external_services_external_services_title_6" => '_options_external_services_external_services_title_6',
            "_options_{$agency_slug}_external_services_external_services_title_7" => '_options_external_services_external_services_title_7',
            "_options_{$agency_slug}_external_services_external_services_title_8" => '_options_external_services_external_services_title_8',
            "_options_{$agency_slug}_external_services_external_services_title_9" => '_options_external_services_external_services_title_9',
            "_options_{$agency_slug}_external_services_external_services_title_10" => '_options_external_services_external_services_title_10',
            // External services link
            "options_{$agency_slug}_external_services_external_services_url_1" => 'options_external_services_external_services_url_1',
            "options_{$agency_slug}_external_services_external_services_url_2" => 'options_external_services_external_services_url_2',
            "options_{$agency_slug}_external_services_external_services_url_3" => 'options_external_services_external_services_url_3',
            "options_{$agency_slug}_external_services_external_services_url_4" => 'options_external_services_external_services_url_4',
            "options_{$agency_slug}_external_services_external_services_url_5" => 'options_external_services_external_services_url_5',
            "options_{$agency_slug}_external_services_external_services_url_6" => 'options_external_services_external_services_url_6',
            "options_{$agency_slug}_external_services_external_services_url_7" => 'options_external_services_external_services_url_7',
            "options_{$agency_slug}_external_services_external_services_url_8" => 'options_external_services_external_services_url_8',
            "options_{$agency_slug}_external_services_external_services_url_9" => 'options_external_services_external_services_url_9',
            "options_{$agency_slug}_external_services_external_services_url_10" => 'options_external_services_external_services_url_10',
            "_options_{$agency_slug}_external_services_external_services_url_1" => '_options_external_services_external_services_url_1',
            "_options_{$agency_slug}_external_services_external_services_url_2" => '_options_external_services_external_services_url_2',
            "_options_{$agency_slug}_external_services_external_services_url_3" => '_options_external_services_external_services_url_3',
            "_options_{$agency_slug}_external_services_external_services_url_4" => '_options_external_services_external_services_url_4',
            "_options_{$agency_slug}_external_services_external_services_url_5" => '_options_external_services_external_services_url_5',
            "_options_{$agency_slug}_external_services_external_services_url_6" => '_options_external_services_external_services_url_6',
            "_options_{$agency_slug}_external_services_external_services_url_7" => '_options_external_services_external_services_url_7',
            "_options_{$agency_slug}_external_services_external_services_url_8" => '_options_external_services_external_services_url_8',
            "_options_{$agency_slug}_external_services_external_services_url_9" => '_options_external_services_external_services_url_9',
            "_options_{$agency_slug}_external_services_external_services_url_10" => '_options_external_services_external_services_url_10',
            // Header logo
            "options_{$agency_slug}_header_logo" => 'options_header_logo',
            "_options_{$agency_slug}_header_logo" => '_options_header_logo',
            // TODO come back to values like `options_laa_feature_item_left_news__laa because they are not simple, agency is in 2 places

            // hq
            "options_{$agency_slug}_feature_item_left_post__hq" => 'options_feature_item_left_post__hq',
            "options_{$agency_slug}_feature_item_right_post__hq" => 'options_feature_item_right_post__hq',
            "options_{$agency_slug}_feature_item_left_news__hq" => 'options_feature_item_left_news__hq',
            "options_{$agency_slug}_feature_item_right_news__hq" => 'options_feature_item_right_news__hq',
            "options_{$agency_slug}_feature_item_left_pages__hq" => 'options_feature_item_left_pages__hq',
            "options_{$agency_slug}_feature_item_right_pages__hq" => 'options_feature_item_right_pages__hq',
            "options_{$agency_slug}_feature_item_left_note__hq" => 'options_feature_item_left_note__hq',
            "options_{$agency_slug}_feature_item_right_note__hq" => 'options_feature_item_right_note__hq',
            "_options_{$agency_slug}_feature_item_left_post__hq" => '_options_feature_item_left_post__hq',
            "_options_{$agency_slug}_feature_item_right_post__hq" => '_options_feature_item_right_post__hq',
            "_options_{$agency_slug}_feature_item_left_news__hq" => '_options_feature_item_left_news__hq',
            "_options_{$agency_slug}_feature_item_right_news__hq" => '_options_feature_item_right_news__hq',
            "_options_{$agency_slug}_feature_item_left_pages__hq" => '_options_feature_item_left_pages__hq',
            "_options_{$agency_slug}_feature_item_right_pages__hq" => '_options_feature_item_right_pages__hq',
            "_options_{$agency_slug}_feature_item_left_note__hq" => '_options_feature_item_left_note__hq',
            "_options_{$agency_slug}_feature_item_right_note__hq" => '_options_feature_item_right_note__hq',
            // ospt
            "options_{$agency_slug}_feature_item_left_post__ospt" => 'options_feature_item_left_post__ospt',
            "options_{$agency_slug}_feature_item_right_post__ospt" => 'options_feature_item_right_post__ospt',
            "options_{$agency_slug}_feature_item_left_news__ospt" => 'options_feature_item_left_news__ospt',
            "options_{$agency_slug}_feature_item_right_news__ospt" => 'options_feature_item_right_news__ospt',
            "options_{$agency_slug}_feature_item_left_pages__ospt" => 'options_feature_item_left_pages__ospt',
            "options_{$agency_slug}_feature_item_right_pages__ospt" => 'options_feature_item_right_pages__ospt',
            "_options_{$agency_slug}_feature_item_left_post__ospt" => '_options_feature_item_left_post__ospt',
            "_options_{$agency_slug}_feature_item_right_post__ospt" => '_options_feature_item_right_post__ospt',
            "_options_{$agency_slug}_feature_item_left_news__ospt" => '_options_feature_item_left_news__ospt',
            "_options_{$agency_slug}_feature_item_right_news__ospt" => '_options_feature_item_right_news__ospt',
            "_options_{$agency_slug}_feature_item_left_pages__ospt" => '_options_feature_item_left_pages__ospt',
            "_options_{$agency_slug}_feature_item_right_pages__ospt" => '_options_feature_item_right_pages__ospt',
            // jac
            "options_{$agency_slug}_feature_item_left_post__jac" => 'options_feature_item_left_post__jac',
            "options_{$agency_slug}_feature_item_right_post__jac" => 'options_feature_item_right_post__jac',
            "options_{$agency_slug}_feature_item_left_news__jac" => 'options_feature_item_left_news__jac',
            "options_{$agency_slug}_feature_item_right_news__jac" => 'options_feature_item_right_news__jac',
            "options_{$agency_slug}_feature_item_left_pages__jac" => 'options_feature_item_left_pages__jac',
            "options_{$agency_slug}_feature_item_right_pages__jac" => 'options_feature_item_right_pages__jac',
            "_options_{$agency_slug}_feature_item_left_post__jac" => '_options_feature_item_left_post__jac',
            "_options_{$agency_slug}_feature_item_right_post__jac" => '_options_feature_item_right_post__jac',
            "_options_{$agency_slug}_feature_item_left_news__jac" => '_options_feature_item_left_news__jac',
            "_options_{$agency_slug}_feature_item_right_news__jac" => '_options_feature_item_right_news__jac',
            "_options_{$agency_slug}_feature_item_left_pages__jac" => '_options_feature_item_left_pages__jac',
            "_options_{$agency_slug}_feature_item_right_pages__jac" => '_options_feature_item_right_pages__jac',
            // hmcts
            "options_{$agency_slug}_feature_item_left_post__hmcts" => 'options_feature_item_left_post__hmcts',
            "options_{$agency_slug}_feature_item_right_post__hmcts" => 'options_feature_item_right_post__hmcts',
            "options_{$agency_slug}_feature_item_left_news__hmcts" => 'options_feature_item_left_news__hmcts',
            "options_{$agency_slug}_feature_item_right_news__hmcts" => 'options_feature_item_right_news__hmcts',
            "options_{$agency_slug}_feature_item_left_pages__hmcts" => 'options_feature_item_left_pages__hmcts',
            "options_{$agency_slug}_feature_item_right_pages__hmcts" => 'options_feature_item_right_pages__hmcts',
            "_options_{$agency_slug}_feature_item_left_post__hmcts" => '_options_feature_item_left_post__hmcts',
            "_options_{$agency_slug}_feature_item_right_post__hmcts" => '_options_feature_item_right_post__hmcts',
            "_options_{$agency_slug}_feature_item_left_news__hmcts" => '_options_feature_item_left_news__hmcts',
            "_options_{$agency_slug}_feature_item_right_news__hmcts" => '_options_feature_item_right_news__hmcts',
            "_options_{$agency_slug}_feature_item_left_pages__hmcts" => '_options_feature_item_left_pages__hmcts',
            "_options_{$agency_slug}_feature_item_right_pages__hmcts" => '_options_feature_item_right_pages__hmcts',
            // jo
            "options_{$agency_slug}_feature_item_left_post__jo" => 'options_feature_item_left_post__jo',
            "options_{$agency_slug}_feature_item_right_post__jo" => 'options_feature_item_right_post__jo',
            "options_{$agency_slug}_feature_item_left_news__jo" => 'options_feature_item_left_news__jo',
            "options_{$agency_slug}_feature_item_right_news__jo" => 'options_feature_item_right_news__jo',
            "options_{$agency_slug}_feature_item_left_pages__jo" => 'options_feature_item_left_pages__jo',
            "options_{$agency_slug}_feature_item_right_pages__jo" => 'options_feature_item_right_pages__jo',
            "_options_{$agency_slug}_feature_item_left_post__jo" => '_options_feature_item_left_post__jo',
            "_options_{$agency_slug}_feature_item_right_post__jo" => '_options_feature_item_right_post__jo',
            "_options_{$agency_slug}_feature_item_left_news__jo" => '_options_feature_item_left_news__jo',
            "_options_{$agency_slug}_feature_item_right_news__jo" => '_options_feature_item_right_news__jo',
            "_options_{$agency_slug}_feature_item_left_pages__jo" => '_options_feature_item_left_pages__jo',
            "_options_{$agency_slug}_feature_item_right_pages__jo" => '_options_feature_item_right_pages__jo',
            // opg
            "options_{$agency_slug}_feature_item_left_post__opg" => 'options_feature_item_left_post__opg',
            "options_{$agency_slug}_feature_item_right_post__opg" => 'options_feature_item_right_post__opg',
            "options_{$agency_slug}_feature_item_left_news__opg" => 'options_feature_item_left_news__opg',
            "options_{$agency_slug}_feature_item_right_news__opg" => 'options_feature_item_right_news__opg',
            "options_{$agency_slug}_feature_item_left_pages__opg" => 'options_feature_item_left_pages__opg',
            "options_{$agency_slug}_feature_item_right_pages__opg" => 'options_feature_item_right_pages__opg',
            "_options_{$agency_slug}_feature_item_left_post__opg" => '_options_feature_item_left_post__opg',
            "_options_{$agency_slug}_feature_item_right_post__opg" => '_options_feature_item_right_post__opg',
            "_options_{$agency_slug}_feature_item_left_news__opg" => '_options_feature_item_left_news__opg',
            "_options_{$agency_slug}_feature_item_right_news__opg" => '_options_feature_item_right_news__opg',
            "_options_{$agency_slug}_feature_item_left_pages__opg" => '_options_feature_item_left_pages__opg',
            "_options_{$agency_slug}_feature_item_right_pages__opg" => '_options_feature_item_right_pages__opg',
            // cica
            "options_{$agency_slug}_feature_item_left_post__cica" => 'options_feature_item_left_post__cica',
            "options_{$agency_slug}_feature_item_right_post__cica" => 'options_feature_item_right_post__cica',
            "options_{$agency_slug}_feature_item_left_news__cica" => 'options_feature_item_left_news__cica',
            "options_{$agency_slug}_feature_item_right_news__cica" => 'options_feature_item_right_news__cica',
            "options_{$agency_slug}_feature_item_left_pages__cica" => 'options_feature_item_left_pages__cica',
            "options_{$agency_slug}_feature_item_right_pages__cica" => 'options_feature_item_right_pages__cica',
            "_options_{$agency_slug}_feature_item_left_post__cica" => '_options_feature_item_left_post__cica',
            "_options_{$agency_slug}_feature_item_right_post__cica" => '_options_feature_item_right_post__cica',
            "_options_{$agency_slug}_feature_item_left_news__cica" => '_options_feature_item_left_news__cica',
            "_options_{$agency_slug}_feature_item_right_news__cica" => '_options_feature_item_right_news__cica',
            "_options_{$agency_slug}_feature_item_left_pages__cica" => '_options_feature_item_left_pages__cica',
            "_options_{$agency_slug}_feature_item_right_pages__cica" => '_options_feature_item_right_pages__cica',
            // laa
            "options_{$agency_slug}_feature_item_left_post__laa" => 'options_feature_item_left_post__laa',
            "options_{$agency_slug}_feature_item_right_post__laa" => 'options_feature_item_right_post__laa',
            "options_{$agency_slug}_feature_item_left_news__laa" => 'options_feature_item_left_news__laa',
            "options_{$agency_slug}_feature_item_right_news__laa" => 'options_feature_item_right_news__laa',
            "options_{$agency_slug}_feature_item_left_pages__laa" => 'options_feature_item_left_pages__laa',
            "options_{$agency_slug}_feature_item_right_pages__laa" => 'options_feature_item_right_pages__laa',
            "_options_{$agency_slug}_feature_item_left_post__laa" => '_options_feature_item_left_post__laa',
            "_options_{$agency_slug}_feature_item_right_post__laa" => '_options_feature_item_right_post__laa',
            "_options_{$agency_slug}_feature_item_left_news__laa" => '_options_feature_item_left_news__laa',
            "_options_{$agency_slug}_feature_item_right_news__laa" => '_options_feature_item_right_news__laa',
            "_options_{$agency_slug}_feature_item_left_pages__laa" => '_options_feature_item_left_pages__laa',
            "_options_{$agency_slug}_feature_item_right_pages__laa" => '_options_feature_item_right_pages__laa',
            // lawcom
            "options_{$agency_slug}_feature_item_left_post__lawcom" => 'options_feature_item_left_post__lawcom',
            "options_{$agency_slug}_feature_item_right_post__lawcom" => 'options_feature_item_right_post__lawcom',
            "options_{$agency_slug}_feature_item_left_news__lawcom" => 'options_feature_item_left_news__lawcom',
            "options_{$agency_slug}_feature_item_right_news__lawcom" => 'options_feature_item_right_news__lawcom',
            "options_{$agency_slug}_feature_item_left_pages__lawcom" => 'options_feature_item_left_pages__lawcom',
            "options_{$agency_slug}_feature_item_right_pages__lawcom" => 'options_feature_item_right_pages__lawcom',
            "_options_{$agency_slug}_feature_item_left_post__lawcom" => '_options_feature_item_left_post__lawcom',
            "_options_{$agency_slug}_feature_item_right_post__lawcom" => '_options_feature_item_right_post__lawcom',
            "_options_{$agency_slug}_feature_item_left_news__lawcom" => '_options_feature_item_left_news__lawcom',
            "_options_{$agency_slug}_feature_item_right_news__lawcom" => '_options_feature_item_right_news__lawcom',
            "_options_{$agency_slug}_feature_item_left_pages__lawcom" => '_options_feature_item_left_pages__lawcom',
            "_options_{$agency_slug}_feature_item_right_pages__lawcom" => '_options_feature_item_right_pages__lawcom',
            // pb
            "options_{$agency_slug}_feature_item_left_posts__pb" => 'options_feature_item_left_post__pb', // Note - changes 'posts' to 'post'
            "options_{$agency_slug}_feature_item_right_posts__pb" => 'options_feature_item_right_post__pb', // Note - changes 'posts' to 'post'
            "options_{$agency_slug}_feature_item_left_news__pb" => 'options_feature_item_left_news__pb',
            "options_{$agency_slug}_feature_item_right_news__pb" => 'options_feature_item_right_news__pb',
            "options_{$agency_slug}_feature_item_left_pages__pb" => 'options_feature_item_left_pages__pb',
            "options_{$agency_slug}_feature_item_right_pages__pb" => 'options_feature_item_right_pages__pb',
            "_options_{$agency_slug}_feature_item_left_posts__pb" => '_options_feature_item_left_post__pb',
            "_options_{$agency_slug}_feature_item_right_posts__pb" => '_options_feature_item_right_post__pb',
            "_options_{$agency_slug}_feature_item_left_news__pb" => '_options_feature_item_left_news__pb',
            "_options_{$agency_slug}_feature_item_right_news__pb" => '_options_feature_item_right_news__pb',
            "_options_{$agency_slug}_feature_item_left_pages__pb" => '_options_feature_item_left_pages__pb',
            "_options_{$agency_slug}_feature_item_right_pages__pb" => '_options_feature_item_right_pages__pb',
            // My work links
            "options_{$agency_slug}_my_work_links" => 'options_my_work_links',
            "_options_{$agency_slug}_my_work_links" => '_options_my_work_links',
            // My work links (title)
            "options_{$agency_slug}_my_work_links_0_my_work_link_title" => 'options_my_work_links_0_my_work_link_title',
            "options_{$agency_slug}_my_work_links_1_my_work_link_title" => 'options_my_work_links_1_my_work_link_title',
            "options_{$agency_slug}_my_work_links_2_my_work_link_title" => 'options_my_work_links_2_my_work_link_title',
            "options_{$agency_slug}_my_work_links_3_my_work_link_title" => 'options_my_work_links_3_my_work_link_title',
            "options_{$agency_slug}_my_work_links_4_my_work_link_title" => 'options_my_work_links_4_my_work_link_title',
            "options_{$agency_slug}_my_work_links_5_my_work_link_title" => 'options_my_work_links_5_my_work_link_title',
            "options_{$agency_slug}_my_work_links_6_my_work_link_title" => 'options_my_work_links_6_my_work_link_title',
            "options_{$agency_slug}_my_work_links_7_my_work_link_title" => 'options_my_work_links_7_my_work_link_title',
            "options_{$agency_slug}_my_work_links_8_my_work_link_title" => 'options_my_work_links_8_my_work_link_title',
            "_options_{$agency_slug}_my_work_links_0_my_work_link_title" => '_options_my_work_links_0_my_work_link_title',
            "_options_{$agency_slug}_my_work_links_1_my_work_link_title" => '_options_my_work_links_1_my_work_link_title',
            "_options_{$agency_slug}_my_work_links_2_my_work_link_title" => '_options_my_work_links_2_my_work_link_title',
            "_options_{$agency_slug}_my_work_links_3_my_work_link_title" => '_options_my_work_links_3_my_work_link_title',
            "_options_{$agency_slug}_my_work_links_4_my_work_link_title" => '_options_my_work_links_4_my_work_link_title',
            "_options_{$agency_slug}_my_work_links_5_my_work_link_title" => '_options_my_work_links_5_my_work_link_title',
            "_options_{$agency_slug}_my_work_links_6_my_work_link_title" => '_options_my_work_links_6_my_work_link_title',
            "_options_{$agency_slug}_my_work_links_7_my_work_link_title" => '_options_my_work_links_7_my_work_link_title',
            "_options_{$agency_slug}_my_work_links_8_my_work_link_title" => '_options_my_work_links_8_my_work_link_title',
            // My work links (url)
            "options_{$agency_slug}_my_work_links_0_my_work_link_url" => 'options_my_work_links_0_my_work_link_url',
            "options_{$agency_slug}_my_work_links_1_my_work_link_url" => 'options_my_work_links_1_my_work_link_url',
            "options_{$agency_slug}_my_work_links_2_my_work_link_url" => 'options_my_work_links_2_my_work_link_url',
            "options_{$agency_slug}_my_work_links_3_my_work_link_url" => 'options_my_work_links_3_my_work_link_url',
            "options_{$agency_slug}_my_work_links_4_my_work_link_url" => 'options_my_work_links_4_my_work_link_url',
            "options_{$agency_slug}_my_work_links_5_my_work_link_url" => 'options_my_work_links_5_my_work_link_url',
            "options_{$agency_slug}_my_work_links_6_my_work_link_url" => 'options_my_work_links_6_my_work_link_url',
            "options_{$agency_slug}_my_work_links_7_my_work_link_url" => 'options_my_work_links_7_my_work_link_url',
            "options_{$agency_slug}_my_work_links_8_my_work_link_url" => 'options_my_work_links_8_my_work_link_url',
            "_options_{$agency_slug}_my_work_links_0_my_work_link_url" => '_options_my_work_links_0_my_work_link_url',
            "_options_{$agency_slug}_my_work_links_1_my_work_link_url" => '_options_my_work_links_1_my_work_link_url',
            "_options_{$agency_slug}_my_work_links_2_my_work_link_url" => '_options_my_work_links_2_my_work_link_url',
            "_options_{$agency_slug}_my_work_links_3_my_work_link_url" => '_options_my_work_links_3_my_work_link_url',
            "_options_{$agency_slug}_my_work_links_4_my_work_link_url" => '_options_my_work_links_4_my_work_link_url',
            "_options_{$agency_slug}_my_work_links_5_my_work_link_url" => '_options_my_work_links_5_my_work_link_url',
            "_options_{$agency_slug}_my_work_links_6_my_work_link_url" => '_options_my_work_links_6_my_work_link_url',
            "_options_{$agency_slug}_my_work_links_7_my_work_link_url" => '_options_my_work_links_7_my_work_link_url',
            "_options_{$agency_slug}_my_work_links_8_my_work_link_url" => '_options_my_work_links_8_my_work_link_url',
        ];
    }

    public function getAgencyFieldsToIgnore($agency_slug)
    {
        return [
            "options_{$agency_slug}_external_services_link_1",
            "options_{$agency_slug}_external_services_link_2",
            "options_{$agency_slug}_external_services_link_3",
            "options_{$agency_slug}_external_services_link_4",
            "options_{$agency_slug}_external_services_link_5",
            "options_{$agency_slug}_external_services_link_6",
            "options_{$agency_slug}_external_services_link_7",
            "options_{$agency_slug}_external_services_link_8",
            "options_{$agency_slug}_external_services_link_9",
            "options_{$agency_slug}_external_services_link_10",
            "_options_{$agency_slug}_external_services_link_1",
            "_options_{$agency_slug}_external_services_link_2",
            "_options_{$agency_slug}_external_services_link_3",
            "_options_{$agency_slug}_external_services_link_4",
            "_options_{$agency_slug}_external_services_link_5",
            "_options_{$agency_slug}_external_services_link_6",
            "_options_{$agency_slug}_external_services_link_7",
            "_options_{$agency_slug}_external_services_link_8",
            "_options_{$agency_slug}_external_services_link_9",
            "_options_{$agency_slug}_external_services_link_10",
            // External services (old fields that are no longer used)
            "options_{$agency_slug}_external_services_title_1",
            "options_{$agency_slug}_external_services_title_2",
            "options_{$agency_slug}_external_services_title_3",
            "options_{$agency_slug}_external_services_title_4",
            "options_{$agency_slug}_external_services_title_5",
            "options_{$agency_slug}_external_services_title_6",
            "options_{$agency_slug}_external_services_title_7",
            "options_{$agency_slug}_external_services_title_8",
            "options_{$agency_slug}_external_services_title_9",
            "options_{$agency_slug}_external_services_title_10",
            "_options_{$agency_slug}_external_services_title_1",
            "_options_{$agency_slug}_external_services_title_2",
            "_options_{$agency_slug}_external_services_title_3",
            "_options_{$agency_slug}_external_services_title_4",
            "_options_{$agency_slug}_external_services_title_5",
            "_options_{$agency_slug}_external_services_title_6",
            "_options_{$agency_slug}_external_services_title_7",
            "_options_{$agency_slug}_external_services_title_8",
            "_options_{$agency_slug}_external_services_title_9",
            "_options_{$agency_slug}_external_services_title_10",
            // ostp
            "options_{$agency_slug}_feature_item_left_post__ostp",
            "options_{$agency_slug}_feature_item_right_post__ostp",
            "options_{$agency_slug}_feature_item_left_news__ostp",
            "options_{$agency_slug}_feature_item_right_news__ostp",
            "options_{$agency_slug}_feature_item_left_pages__ostp",
            "options_{$agency_slug}_feature_item_right_pages__ostp",
            "_options_{$agency_slug}_feature_item_left_post__ostp",
            "_options_{$agency_slug}_feature_item_right_post__ostp",
            "_options_{$agency_slug}_feature_item_left_news__ostp",
            "_options_{$agency_slug}_feature_item_right_news__ostp",
            "_options_{$agency_slug}_feature_item_left_pages__ostp",
            "_options_{$agency_slug}_feature_item_right_pages__ostp",
            // ppo
            "options_{$agency_slug}_feature_item_left_posts__ppo",
            "options_{$agency_slug}_feature_item_right_posts__ppo",
            "options_{$agency_slug}_feature_item_left_news__ppo",
            "options_{$agency_slug}_feature_item_right_news__ppo",
            "options_{$agency_slug}_feature_item_left_pages__ppo",
            "options_{$agency_slug}_feature_item_right_pages__ppo",
            "_options_{$agency_slug}_feature_item_left_posts__ppo",
            "_options_{$agency_slug}_feature_item_right_posts__ppo",
            "_options_{$agency_slug}_feature_item_left_news__ppo",
            "_options_{$agency_slug}_feature_item_right_news__ppo",
            "_options_{$agency_slug}_feature_item_left_pages__ppo",
            "_options_{$agency_slug}_feature_item_right_pages__ppo",
            // Old format...
            "options_{$agency_slug}_quick_links_0_quick_link_url",
            "options_{$agency_slug}_quick_links_1_quick_link_url",
            "options_{$agency_slug}_quick_links_2_quick_link_url",
            "options_{$agency_slug}_quick_links_3_quick_link_url",
            "options_{$agency_slug}_quick_links_4_quick_link_url",
            "options_{$agency_slug}_quick_links_5_quick_link_url",
            "options_{$agency_slug}_quick_links_6_quick_link_url",
            "options_{$agency_slug}_quick_links_7_quick_link_url",
            "options_{$agency_slug}_quick_links_8_quick_link_url",
            "options_{$agency_slug}_quick_links_9_quick_link_url",
            "options_{$agency_slug}_quick_links_10_quick_link_url",
            "options_{$agency_slug}_quick_links_11_quick_link_url",
            "options_{$agency_slug}_quick_links_12_quick_link_url",
            "options_{$agency_slug}_quick_links_13_quick_link_url",
            "_options_{$agency_slug}_quick_links_0_quick_link_url",
            "_options_{$agency_slug}_quick_links_1_quick_link_url",
            "_options_{$agency_slug}_quick_links_2_quick_link_url",
            "_options_{$agency_slug}_quick_links_3_quick_link_url",
            "_options_{$agency_slug}_quick_links_4_quick_link_url",
            "_options_{$agency_slug}_quick_links_5_quick_link_url",
            "_options_{$agency_slug}_quick_links_6_quick_link_url",
            "_options_{$agency_slug}_quick_links_7_quick_link_url",
            "_options_{$agency_slug}_quick_links_8_quick_link_url",
            "_options_{$agency_slug}_quick_links_9_quick_link_url",
            "_options_{$agency_slug}_quick_links_10_quick_link_url",
            "_options_{$agency_slug}_quick_links_11_quick_link_url",
            "_options_{$agency_slug}_quick_links_12_quick_link_url",
            "_options_{$agency_slug}_quick_links_13_quick_link_url",

        ];
    }
}

// 1. Register the instance for the callable parameter.
$instance = new SyncOptions();
WP_CLI::add_command('sync-options', $instance);

// 2. Register object as a function for the callable parameter.
WP_CLI::add_command('sync-options', 'MOJ\Intranet\SyncOptions');
