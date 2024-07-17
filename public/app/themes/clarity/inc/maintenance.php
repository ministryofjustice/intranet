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
    ];

    public function __construct()
    {
        add_action('admin_init', [$this, 'settings_init']);
        add_action('admin_menu', [$this, 'options_page']);

        // Displays a message on the login page when maintenance mode is active.
        add_filter('login_message', function () {
            $options = get_option('maintenance_options', [
                'maintenance_mode_status' => 0,
                'maintenance_mode_message' => '',
            ]);
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

        foreach ($this->fields as $group => $fields) {
            foreach ($fields as $field) {
                add_settings_field(
                    $field['id'],
                    __(isset($field['label']) ? $field['label'] : '', 'maintenance'),
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

        $options = get_option('maintenance_options', [
            'maintenance_mode_status' => 0,
            'maintenance_mode_message' => '',
        ]);

        $current_state = $options['maintenance_mode_status'] ?? false;

        if ($current_state) {
            $this->show_maintenance_mode();
        }
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

        $options = get_option('maintenance_options', [
            'maintenance_mode_status' => 0,
            'maintenance_mode_message' => '',
        ]);

        ?>
        <div class="wrap">
            <h1><?= esc_html(get_admin_page_title()); ?></h1>
            <h2 class="description"></h2>
            <form action="options.php" method="post">
                <?php
                settings_fields('maintenance');
                do_settings_sections('maintenance');
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
        $options = get_option('maintenance_options', [
            'maintenance_mode_status' => 0,
            'maintenance_mode_message' => '',
        ]);

        switch ($field['type']) {
            case "text":
                ?>
                <input
                    type="text"
                    id="<?= esc_attr($field['id']); ?>"
                    name="maintenance_options[<?= esc_attr($field['id']); ?>]"
                    value="<?= isset($options[$field['id']]) ? esc_attr($options[$field['id']]) : ''; ?>"
                >
                <?php
                break;

            case "checkbox":
                ?>
                <input
                    type="checkbox"
                    id="<?= esc_attr($field['id']); ?>"
                    name="maintenance_options[<?= esc_attr($field['id']); ?>]"
                    value="1"
                    <?= isset($options[$field['id']]) ? (checked($options[$field['id']], 1, false)) : (''); ?>
                >
                <?php
                break;
        }

        if (isset($field['description'])) {
            ?>
                <p class="description">
                    <?php esc_html_e($field['description'], 'maintenance'); ?>
                </p>
            <?php
        }
    }

    /**
     * @param array $args
     * @return void
     */
    public function render_maintenance_section(array $args): void
    {
        ?>
        <h2 id="<?= esc_attr($args['id']); ?>"><?php esc_html_e('Maintenance mode', 'maintenance'); ?></h2>
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
}

new Maintenance();
