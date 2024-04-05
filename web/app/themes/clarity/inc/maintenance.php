<?php

namespace MOJ\Intranet;

class Maintenance
{

    /**
     * Capability required by the user to access the Maintenance plugin menu entry.
     *
     * @var string $capability
     */
    private string $capability = 'manage_options';

    /**
     * Array of fields that should be displayed in the settings page.
     *
     * @var array $fields
     */
    private array $fields = [
        'maintenance' => [
            [
                'id' => 'maintenance_mode_status',
                'label' => 'Maintenance mode status',
                'description' => 'Check this box to enable maintenance mode. The frontend of the site will still be
                accessible but only administrators will be able to log in',
                'type' => 'checkbox',
            ],
            [
                'id' => 'maintenance_mode_message',
                'label' => 'Maintenance mode message',
                'description' => 'This message will be displayed on the login screen whilst maintenance mode is active',
                'type' => 'text',
            ],
        ],
        'notification' => [
            [
                'id' => 'notification_status',
                'label' => 'Display notification warning',
                'description' => 'Check this box to display a dismissible notification in the admin section of the site',
                'type' => 'checkbox',
            ],
            [
                'id' => 'notification_title',
                'label' => 'Notification title',
                'type' => 'text',
            ],
            [
                'id' => 'notification_content',
                'label' => 'Notification body',
                'type' => 'textarea',
            ],
            [
                'id' => 'notification_level',
                'label' => 'Importance',
                'type' => 'select',
                'options' => [
                    'warning' => 'Warning',
                    'info' => 'Info'
                ]
            ],
            [
                'id' => 'notification_updated',
                'type' => 'hash',
            ],
        ]
    ];

    public function __construct()
    {
        add_action('admin_init', [$this, 'settings_init']);
        add_action('admin_menu', [$this, 'options_page']);

        // Displays a message on the login page when maintenance mode is active.
        add_filter('login_message', function () {
            $options = get_option('maintenance_options');
            $current_state = $options['maintenance_mode_status'] ?? false;
            $message = $options['maintenance_mode_message'] ?? 'Site undergoing maintenance';
            if ($current_state) {
                return '<p class="message"><b>' . $message . '</b></p>';
            }
            return null;
        });
    }

    /**
     * Registers the settings page and renders the fields
     *
     */
    public function settings_init(): void
    {
        // Register a new setting this page.
        register_setting('maintenance', 'maintenance_options');

        // Register a new section.
        add_settings_section(
            'maintenance-section',
            __('', 'maintenance'),
            [$this, 'render_maintenance_section'],
            'maintenance'
        );
        add_settings_section(
            'notification-section',
            __('', 'maintenance'),
            [$this, 'render_notification_section'],
            'maintenance'
        );

        foreach ($this->fields as $group => $fields) {
            foreach ($fields as $field) {
                add_settings_field(
                    $field['id'],
                    __($field['label'], 'maintenance'),
                    [$this, 'render_field'],
                    'maintenance',
                    "{$group}-section",
                    [
                        'label_for' => $field['id'],
                        'field' => $field,
                    ]
                );
            }
        }

        $options = get_option('maintenance_options');
        $current_state = $options['maintenance_mode_status'] ?? false;
        if ($current_state) {
            $this->show_maintenance_mode();
        }

        // Get the contents of the notification related fields
        $notification_status = $options['notification_status'] ?? false;
        $notification_title = $options['notification_title'] ?? '';
        $notification_content = $options['notification_content'] ?? '';
        $notification_level = $options['notification_level'] ?? 'info';
        $notification_id = $options['notification_updated'] ?? '';

        if ($notification_status and ($notification_title || $notification_content)) {
            $current_user = wp_get_current_user();
            $user_id = $current_user->ID;
            // If the user has not previously dismissed the notification message, and the notification message is active display the banner.
            add_action('admin_notices', function () use ($user_id, $notification_title, $notification_content, $notification_level, $notification_id) {
                if (!get_user_meta($user_id, "maintenance_notification_{$notification_id}_dismissed")) {
                    $this->show_notification_banner($notification_level, $notification_title, $notification_content);
                }
            });
        }
        // If the banner is dismissed, update the user_meta table, so it's not shown again.
        $this->dismiss_notification_banner($notification_id);
    }

    /**
     * Set up the maintenance submenu page
     */
    public function options_page(): void
    {
        add_submenu_page(
            'options-general.php', /* Parent Menu Slug */
            'Maintenance settings', /* Page Title */
            'Maintenance', /* Menu Title */
            $this->capability, /* Capability */
            'maintenance', /* Menu Slug */
            [$this, 'render_options_page'], /* Callback */
        );
    }

    /**
     * Renders the options page
     */
    public function render_options_page(): void
    {

        // check user capabilities
        if (!current_user_can($this->capability)) {
            return;
        }

        if (isset($_GET['settings-updated'])) {
            add_settings_error('maintenance_messages', 'maintenance_message', __('Settings Saved', 'maintenance'), 'updated');
        }

        $options = get_option('maintenance_options');
        $notification_title = $options['notification_title'] ?? '';
        $notification_content = $options['notification_content'] ?? '';
        $notification_level = $options['notification_level'] ?? 'info';

        ?>
        <div class="wrap">
            <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
            <h2 class="description"></h2>
            <form action="options.php" method="post">
                <?php
                settings_fields('maintenance');
                do_settings_sections('maintenance');
                ?>
                <h3>Preview</h3>
                <?php
                // Display a preview of the notification banner
                $this->show_notification_banner($notification_level, $notification_title, $notification_content, true);
                submit_button('Save Settings');
                ?>
            </form>
        </div>
        <?php
    }

    /**
     * Render a settings field.
     *
     * @param array $args A mixed array of arguments to render the different field types
     */
    public function render_field(array $args): void
    {

        $field = $args['field'];

        // Get the value of the setting we've registered with register_setting()
        $options = get_option('maintenance_options');
        // Generate a unique key so that dismissed banners are reshown if the content changes
        $notification_hash = $this->get_notification_hash($options['notification_content'], $options['notification_title']);

        switch ($field['type']) {
            case "text":
            {
                ?>
                <input
                    type="text"
                    id="<?php echo esc_attr($field['id']); ?>"
                    name="maintenance_options[<?php echo esc_attr($field['id']); ?>]"
                    value="<?php echo isset($options[$field['id']]) ? esc_attr($options[$field['id']]) : ''; ?>"
                >
                <p class="description">
                    <?php esc_html_e($field['description'], 'maintenance'); ?>
                </p>
                <?php
                break;
            }

            case "checkbox":
            {
                ?>
                <input
                    type="checkbox"
                    id="<?php echo esc_attr($field['id']); ?>"
                    name="maintenance_options[<?php echo esc_attr($field['id']); ?>]"
                    value="1"
                    <?php echo isset($options[$field['id']]) ? (checked($options[$field['id']], 1, false)) : (''); ?>
                >
                <p class="description">
                    <?php esc_html_e($field['description'], 'maintenance'); ?>
                </p>
                <?php
                break;
            }

            case "textarea":
            {
                ?>
                <textarea
                    id="<?php echo esc_attr($field['id']); ?>"
                    name="maintenance_options[<?php echo esc_attr($field['id']); ?>]"
                ><?php echo isset($options[$field['id']]) ? esc_attr($options[$field['id']]) : ''; ?></textarea>
                <p class="description">
                    <?php esc_html_e($field['description'], 'maintenance'); ?>
                </p>
                <?php
                break;
            }

            case "select":
            {
                ?>
                <select
                    id="<?php echo esc_attr($field['id']); ?>"
                    name="maintenance_options[<?php echo esc_attr($field['id']); ?>]"
                >
                    <?php foreach ($field['options'] as $key => $option) { ?>
                        <option value="<?php echo $key; ?>"
                            <?php echo isset($options[$field['id']]) ? (selected($options[$field['id']], $key, false)) : (''); ?>
                        >
                            <?php echo $option; ?>
                        </option>
                    <?php } ?>
                </select>
                <p class="description">
                    <?php esc_html_e($field['description'], 'maintenance'); ?>
                </p>
                <?php
                break;
            }
            case "hash":
            {
                ?>
                <input
                    type="hidden"
                    id="<?php echo esc_attr($field['id']); ?>"
                    name="maintenance_options[<?php echo esc_attr($field['id']); ?>]"
                    value="<?php echo $notification_hash; ?>"
                >
                <?php
                break;
            }
        }
    }

    /**
     * @param array $args
     * @return void
     */
    public function render_maintenance_section(array $args): void
    {
        ?>
        <h2 id="<?php echo esc_attr($args['id']); ?>"><?php esc_html_e('Maintenance mode', 'maintenance'); ?></h2>
        <?php
    }

    /**
     * @param array $args
     * @return void
     */
    public function render_notification_section(array $args): void
    {
        ?>
        <h2 id="<?php echo esc_attr($args['id']); ?>"><?php esc_html_e('Notifications', 'notification'); ?></h2>
        <?php
    }

    /**
     * Enables maintenance mode; when a non-administrator tries to access the admin section they will be redirected to
     * the login page and logged out.
     *
     * @return void
     */
    private function show_maintenance_mode()
    {
        $current_user = wp_get_current_user();
        if (!in_array('administrator', $current_user->roles)) {
            $logout_url = wp_login_url() . '?mode=maintenance';
            wp_logout();
            wp_redirect($logout_url, 302);
        }
    }

    /**
     * @param string $notification_level Used to display a different icon depending on the level of notice
     * @param string $notification_title The title for the banner
     * @param string $notification_content The content of the banner
     * @param bool $preview If true, the notification banner will be displayed only in the settings form and the
     * link will be disabled
     *
     * @return void
     */
    private function show_notification_banner(string $notification_level, string $notification_title, string $notification_content, bool $preview = false)
    {
        $inline = $preview ? ' inline' : '';
        $link = $preview ? '' : '?maintenance_notification_ignore';
        echo '<div class="moj-maintenance-notification notice-' . $notification_level . ' update-nag notice' . $inline . '" style="display: block">
                     <div class="moj-maintenance-notification__img" style="display: flex; align-items: center">
                         <span class="moj-maintenance-notification__icon dashicons dashicons-' . $notification_level . '" style="font-size: 35px; width: fit-content; display: block; height:fit-content"></span>
                         <h2 class="moj-maintenance-notification__header" style="margin:0 0 0 10px">' . $notification_title . '</h2>
                     </div>
                     <div>
                       <p class="moj-maintenance-notification__content">' . $notification_content . '</p>
                       <a class="moj-maintenance-notification__dismiss" href="' . $link . '">Dismiss</a>
                     </div>
                  </div>';
    }

    /**
     * Allows individual users to dismiss the notification banner by setting metadata
     *
     * @param string $notification_id The unique ID of the banner
     * @return void
     */
    private function dismiss_notification_banner(string $notification_id): void
    {
        $current_user = wp_get_current_user();
        $user_id = $current_user->ID;

        if (isset($_GET['maintenance_notification_ignore'])) {
            add_user_meta($user_id, "maintenance_notification_{$notification_id}_dismissed", 'true', true);
        }
    }

    /**
     * Generates a unique id based on the content of the banner
     *
     * @param string $content The banner's content
     * @param string $title The banner's title
     * @return string
     */
    private function get_notification_hash(string $content, string $title): string
    {
        return hash("md5", $content . $title);
    }
}

new Maintenance();
