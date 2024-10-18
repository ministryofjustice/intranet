<?php

namespace MOJ\Intranet;

/**
 * Class TransientAdminNotices
 *
 * Handles displaying admin notices in WordPress after a page redirect or reload.
 *
 * This is a vendored php package that's been copied from the original source 
 * and modified to the MOJ\Intranet namespace.
 *
 * @see https://github.com/wpscholar/wp-transient-admin-notices - Original source
 * 
 * @author Micah Wood
 * @package MOJ\Intranet
 */

class TransientAdminNotices implements \Countable, \IteratorAggregate {

	const TYPES = [
		'success',
		'info',
		'warning',
		'error'
	];

	/**
	 * Queue of notices stored during the current page load
	 *
	 * @var array
	 */
	protected $notices = [];

	/**
	 * Transient name
	 *
	 * @var string
	 */
	protected $transient;

	/**
	 * TransientAdminNotices constructor.
	 *
	 * @param string $transient The name of the transient. Note: You may want to make this user-specific!
	 */
	public function __construct( $transient ) {
		$this->transient = $transient;
		add_action( 'admin_notices', array( $this, 'render' ) );
		add_action( 'shutdown', array( $this, 'save' ) );
	}

	/**
	 * Add a new notice to the queue
	 *
	 * @param string $key
	 * @param string $message
	 * @param string $type
	 */
	public function add( $key, $message, $type = 'info' ) {
		$this->notices[ $key ] = [
			'message' => $message,
			'type'    => in_array( $type, self::TYPES ) ? $type : 'info',
		];
	}

	/**
	 * Check if a notice exists in the queue
	 *
	 * @param string $key
	 *
	 * @return bool
	 */
	public function has( $key ) {
		return array_key_exists( $key, $this->notices );
	}

	/**
	 * Get a notice from the queue
	 *
	 * @param string $key
	 *
	 * @return string|null
	 */
	public function get( $key ) {
		return $this->has( $key ) ? $this->notices[ $key ] : null;
	}

	/**
	 * Remove a notice from the queue
	 *
	 * @param string $key
	 */
	public function remove( $key ) {
		unset( $this->notices[ $key ] );
	}

	/**
	 * Purge all notices from queue
	 */
	public function purge() {
		$this->notices = [];
	}

	/**
	 * Count number of notices in the queue
	 */
	public function count(): int {
		return count( $this->notices );
	}

	/**
	 * Get array iterator for notices
	 *
	 * @return \ArrayIterator
	 */
	public function getIterator(): \ArrayIterator {
		return new \ArrayIterator( $this->notices );
	}

	/**
	 * Set transient
	 */
	public function save() {
		if ( $this->count() ) {
			set_transient( $this->transient, $this->notices, 10 );
		}
	}

	/**
	 * Get transient
	 */
	public function fetch() {
		return get_transient( $this->transient );
	}

	/**
	 * Delete transient
	 */
	public function delete() {
		delete_transient( $this->transient );
	}

	/**
	 * Render one or all notices from previous page load.
	 *
	 * @param string|null $key
	 */
	public function render( $key = null ) {

		$notices = $this->fetch();

		// We render all notices by default, unless a specific key is passed.
		if ( $key ) {
			$notices = isset( $notices, $notices[ $key ] ) ? $notices[ $key ] : [];
		}

		// Loop through and render all notices
		if ( $notices && is_array( $notices ) ) {

			$allowed_html = wp_kses_allowed_html();
			$allowed_html['p'] = [];

			foreach ( $notices as $notice ) {
				if ( isset( $notice['message'], $notice['type'] ) ) {

					printf(
						'<div class="%s">%s</div>',
						esc_attr( "notice notice-{$notice['type']}" ),
						wp_kses( wpautop( $notice['message'] ), $allowed_html )
					);

				}
			}

		}

	}

}

/**
 * Init the TransientAdminNotices class
 */

new TransientAdminNotices('theme_user_notice:' . get_current_user_id());
