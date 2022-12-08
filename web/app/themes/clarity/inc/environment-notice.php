<?php

namespace MOJ\Intranet;

class environmentnotice
{
    public function __construct()
    {
        add_action('admin_notices', [$this, 'content']);
    }

    /**
     * @return string
     */
    public function content(): string
    {
        if (getenv('WP_ENV') === 'production') {
            return '';
        }

        echo '<style>
            body.toplevel_page_elasticpress #wpbody .moj-intro-notice.update-nag,
            body.elasticpress_page_elasticpress-settings #wpbody .moj-intro-notice.update-nag,
            body.elasticpress_page_elasticpress-sync #wpbody .moj-intro-notice.update-nag,
            body.elasticpress_page_elasticpress-health #wpbody .moj-intro-notice.update-nag,
            body.elasticpress_page_elasticpress-query-log #wpbody .moj-intro-notice.update-nag,
            body.elasticpress_page_elasticpress-weighting #wpbody .moj-intro-notice.update-nag,
            body.elasticpress_page_elasticpress-synonyms #wpbody .moj-intro-notice.update-nag,
            body.elasticpress_page_elasticpress-weighting #wpbody .moj-intro-notice.update-nag {
                margin: 15px 20px 10px 1px;
            }
        </style>';

        echo '<div class="moj-intro-notice notice-warning update-nag notice">
            <div class="intro-notice-img-wrap" style="min-width: 60px;margin-top: -9px;margin-left: -9px;">
                <span class="dashicons dashicons-warning" style="display: block;font-size: 44px;color: orange;"></span>
            </div>
            <div>
                <h3 class="intro-notice-header" style="margin-top:1px">
                    Your current environment is:
                    <span style="text-transform: uppercase;color: green;">' . getenv('WP_ENV') . '</span></h3>
            </div>
        </div>';

        return '';
    }
}
