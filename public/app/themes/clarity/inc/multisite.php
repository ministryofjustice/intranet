<?php

/**
 * Helper functions related to multisite
 *
 * @package Clarity
 **/

namespace MOJ\Intranet;

use Roots\WPConfig\Config;

class Multisite
{
    // Set this to true when blog id 1 has only hq.
    const MULTISITE_MIGRATION_COMPLETE = false;

    /**
     * Infer the agency domain by the multisite path.
     * 
     * @param string $path The path of the multisite
     * @return string|null The agency identifier or null if not found
     */
    private static function agencyIdFromPath($path)
    {
        $path_parts = explode('/', $path);

        return count($path_parts) === 2 ? $path_parts[1] : null;
    }

    /**
     * Infer the agency domain by the multisite domain.
     * 
     * @param string $domain The domain of the multisite
     * @return string The agency identifier
     */
    private static function agencyIdFromDomain($domain)
    {
        // Extract the subdomain from the domain
        $url_parts = explode('.', $domain);
        return $url_parts[0];
    }

    /**
     * Infer the agency from the current blog's domain or path
     * 
     * @return string|null The agency identifier or false if not on a multisite
     */
    public static function getAgencyId()
    {
        // If we are not on a multisite, return false.
        if (!is_multisite()) {
            return null;
        }

        // If migration is not complete and we are on blog_id 1, return false.
        // This is because we will have hq and at least one other agency on the same blog.
        if (!self::MULTISITE_MIGRATION_COMPLETE && get_current_blog_id() === 1) {
            return null;
        }

        // If the migration is complete and we are on blog_id 1, return hq.
        if (self::MULTISITE_MIGRATION_COMPLETE && get_current_blog_id() === 1) {
            return 'hq';
        }

        // Get the domain and path from the wp_blogs table
        $blog_details = get_blog_details(get_current_blog_id());

        // If the blog details are empty, return false.
        if (!$blog_details) {
            return null;
        }

        // Infer the agency id from the domain or path
        return is_subdomain_install() ? self::agencyIdFromDomain($blog_details->domain) : self::agencyIdFromPath($blog_details->path);
    }


    /**
     * Get the blog id by the agency id
     * 
     * @param string $agencyId The agency identifier
     * @return int|null The blog id or null if not multisite
     */
    public static function getBlogIdByAgencyId($agencyId)
    {
        if (!is_multisite()) {
            return null;
        }

        // Despite the config variable name, this is the root domain of the multisite.
        $root_domain = Config::get('DOMAIN_CURRENT_SITE');

        if (is_subdomain_install()) {
            $site_domain = 'hq' === $agencyId ? $root_domain : $agencyId . '.' . $root_domain;
            $blog_id = get_blog_id_from_url($site_domain);
        } else {
            $site_path = 'hq' === $agencyId ? '/' : '/' . $agencyId;
            $blog_id = get_blog_id_from_url($root_domain, $site_path);
        }

        // If the blog id is found, return it.
        if ($blog_id) {
            return $blog_id;
        }

        // If we haven't completed migration, then assume the agency is in blog id 1.
        if (!self::MULTISITE_MIGRATION_COMPLETE) {
            return 1;
        }

        return $blog_id;
    }

    /**
     * Check if the agency taxonomy is enabled for the current blog
     * 
     * @return bool True if the agency taxonomy is enabled, false otherwise
     */
    public static function isAgencyTaxonomyEnabled()
    {
        // If we are not on a multisite, return true.
        if (!is_multisite()) {
            return true;
        }

        // If migration is not complete and we are on blog_id 1, return true.
        if (!self::MULTISITE_MIGRATION_COMPLETE && get_current_blog_id() === 1) {
            return true;
        }

        return false;
    }

    /**
     * Check if the region taxonomy is enabled for the current blog
     * 
     * @return bool True if the region taxonomy is enabled, false otherwise
     */
    public static function isRegionTaxonomyEnabled()
    {
        if (!is_multisite()) {
            return true;
        }

        if (!self::MULTISITE_MIGRATION_COMPLETE && get_current_blog_id() === 1) {
            return true;
        }

        // We are on hmcts's blog.
        if ('hmcts' === self::getAgencyId()) {
            return true;
        }

        return false;
    }
}
