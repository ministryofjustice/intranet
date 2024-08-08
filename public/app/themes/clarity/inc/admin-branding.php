<?php

namespace MOJ\Intranet;

defined('ABSPATH') || exit;

/**
 * Hooks into WP and changes the branding.
 * In effect; removing WP logos and replacing them with MoJ branding
 */
class AdminBranding
{
    /**
     * States the release version of branding assets
     *
     * @var float
     */
    private float $version = 1.0;

    /**
     * Bootstraps the branding class
     *
     * @uses actions()
     */
    public function __construct()
    {
        $this->actions();
    }

    /**
     * A collection of WP actions to hook into.
     * Methods in this class are injected into the WP ecosystem here
     *
     * @uses add_action()
     *
     * @return void
     */
    public function actions(): void
    {
        add_action('login_enqueue_scripts', [$this, 'login'], 10);
    }

    /**
     * Enqueue CSS and JavaScript for use on Login screens
     *
     * @uses $version
     * @uses wp_enqueue_style(), wp_enqueue_script(), get_template_directory_uri()
     *
     * @return void
     */
    public function login(): void
    {
        wp_enqueue_style(
            'justice-branding-login-css',
            get_template_directory_uri() . '/dist/css/login.min.css',
            '',
            $this->version
        );

        wp_enqueue_script(
            'justice-branding-login-js',
            get_template_directory_uri() . '/dist/js/login.min.js',
            null,
            $this->version,
            true
        );
    }
}
