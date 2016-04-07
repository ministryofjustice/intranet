<?php

namespace MOJIntranet\Taxonomies;

abstract class Taxonomy {
    /**
     * The name of the taxonomy.
     * Name should only contain lowercase letters and the underscore character, and not be more than 32 characters long
     * (database structure restriction).
     *
     * @var string
     */
    protected $name = null;

    /**
     * Name of the object types for the taxonomy object. Object-types can be built-in Post Type or any Custom Post Type
     * that may be registered.
     *
     * @var string[]
     */
    protected $objectType = array();

    /**
     * An array of arguments passed to register_taxonomy.
     * See: https://codex.wordpress.org/Function_Reference/register_taxonomy#Arguments
     *
     * @var array
     */
    protected $args = array();

    /**
     * BaseTaxonomy constructor.
     */
    public function __construct()
    {
        add_action('init', array($this, 'register'));
    }

    /**
     * Register the taxonomy with WordPress.
     */
    public function register()
    {
        register_taxonomy($this->name, $this->objectType, $this->args);
    }
}
