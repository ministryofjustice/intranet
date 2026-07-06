<?php

/**
 * This file is a place for hooks that must be loaded very early.
 */

defined('ABSPATH') || exit;


/**
 * Prevent scheduling of unwanted TotalPoll cron hooks.
 * 
 * @see https://developer.wordpress.org/reference/hooks/pre_schedule_event/
 */
add_filter('pre_schedule_event', function ($pre, $event) {
    // Block attempts to schedule unwanted TotalPoll cron hooks.
    if (isset($event->hook) && in_array($event->hook, ['totalpoll_weekly_environment', 'totalpoll_daily_activity'], true)) {
        return new \WP_Error('blocked_event', 'Scheduling ' . $event->hook . ' is disabled.', ['skip-log' => true]);
    }

    return $pre;
}, 10, 2);
