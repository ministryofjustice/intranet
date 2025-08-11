<?php

namespace MOJ\Intranet;

if (!defined('ABSPATH')) {
    die();
}

use Agency_Context;
use MOJ\Intranet\Agency;
use Roots\WPConfig\Config;

/**
 * This class is related to granting the Intranet users access to the Intranet Archive.
 */

class IntranetArchiveLink
{
    /**
     * @var string
     */
    private $agency_context = '';

    /**
     * @var string
     */
    private $archive_url = '';

    /**
     * @var string
     */
    private $archive_link_payload_encoded = '';

    /**
     * @var string
     */
    private $archive_link_signature = '';


    public function __construct()
    {
        add_action('wp_dashboard_setup', [$this, 'addDashboardWidget']);
    }

    /**
     * Add the Intranet Archive widget to the dashboard.
     * 
     * The widget will be hidden if the config is not valid (this could be intentional, to disable the feature).
     * The widget will only be shown to users who have an agency context and whose agency has an archive.
     * 
     * @return void
     */
    public function addDashboardWidget(): void
    {
        // If the config is not valid, return early.
        if (!$this->configIsValid()) {
            return;
        }

        // If the user's agency is not valid, return early.
        if (!$this->userAgencyHasArchive()) {
            return;
        }

        // Get the Intranet Archive URL.
        $this->archive_url = Config::get('INTRANET_ARCHIVE_URL');

        // Current timestamp, plus 60 seconds.
        $archive_link_payload_array = ['expiry' => time() + 60, 'agency' => $this->agency_context, 'hostname' => parse_url(home_url(), PHP_URL_HOST)];

        // Encode the array.
        $this->archive_link_payload_encoded = base64_encode(json_encode($archive_link_payload_array));

        // Create the signature.
        $this->archive_link_signature = base64_encode(hash_hmac('sha256', $this->archive_link_payload_encoded, Config::get('INTRANET_ARCHIVE_SHARED_SECRET'), true));

        add_meta_box(
            'intranet_archive_link_dashboard_widget',
            'Accessing the Intranet Archive',
            [$this, 'renderWidget'],
            'dashboard',
            'side'
        );
    }

    /**
     * Check if the config is valid.
     * 
     * @return bool
     */
    public function configIsValid(): bool
    {
        return Config::get('INTRANET_ARCHIVE_URL') && Config::get('INTRANET_ARCHIVE_SHARED_SECRET');
    }

    /**
     * Check if the user has edit permissions and the user's agency has an archive.
     * 
     * @return bool
     */
    public function userAgencyHasArchive(): bool
    {
        // Only show this widget to users who can have an agency context.
        if (!Agency_Context::current_user_can_have_context()) {
            return false;
        }

        // Get the current user's agency context.
        $this->agency_context = Agency_Context::get_agency_context();

        // Does the current user's agency have an archive?
        $agency_has_archive = (new Agency())->getList()[$this->agency_context]['has_archive'] ?? null;

        // If the current agency does not have an archive, return early.
        if (!$agency_has_archive) {
            return false;
        }

        return true;
    }

    /**
     * Render the Intranet Archive widget.
     * 
     * @return void
     */
    public function renderWidget(): void
    {
        $admin_url = admin_url();

        echo "
        Historic snapshots of the intranet are available to view on the intranet archive.<br><br>
        <form method='POST' action='{$this->archive_url}'>
            <input type='hidden' name='payload' value='{$this->archive_link_payload_encoded}'>
            <input type='hidden' name='sig' value='{$this->archive_link_signature}'>
            <button type='submit' class='button button-primary'>Go to Intranet Archive</button>
        </form>
        <span class='refresh'><a href='{$admin_url}'>Refresh this screen</a> to reveal the secure link.</span>
        <style>
            #intranet_archive_link_dashboard_widget:not(.expired) .refresh {
                display: none;
            }
            #intranet_archive_link_dashboard_widget.expired form {
                display: none;
            }
        </style>
        <script>
            // Hide the from after 30s has passed.
            setTimeout(() => {
                document.getElementById('intranet_archive_link_dashboard_widget').classList.add('expired');
            }, 30_000);
        </script>
        ";
    }
}

new IntranetArchiveLink();
