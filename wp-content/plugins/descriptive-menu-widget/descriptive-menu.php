<?php
/*
Plugin Name: Descriptive Menu
Plugin URI: http://www.acumensoftwaredesign.com/wordpress-plugins/
Description: Render menus with a description with complete flexibility for developers.
Version: 1.1
Author: Kerry Ritter
Author URI: http://www.kerryritter.com/
License: GPL
Copyright: Acumen Consulting
*/

class Menu_With_Description extends Walker_Nav_Menu {
	// This class was written by Christian "Kriesi" Budschedl
	// http://www.kriesi.at/archives/improve-your-wordpress-navigation-menu-output

	private $show_icon = false;

	// Parameters get passed Descriptive_Menu.widget in the wp_nav_menu call
	function __construct($show_icon, $wrap_link) {
		$this->show_icon = $show_icon;
		$this->wrap_link = $wrap_link;
	}

	// This renders each element
    function start_el(&$output, $item, $depth = 0, $args = Array(), $id = 0) {
        global $wp_query;
        $indent = ( $depth ) ? str_repeat( "\t", $depth ) : '';
        
        $class_names = $value = '';

        $classes = empty( $item->classes ) ? array() : (array) $item->classes;

        $class_names = join( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item ) ) . " descriptive-menu-item";
        $class_names = ' class="' . esc_attr( $class_names ) . '"';

        $output .= $indent . '<li id="menu-item-'. $item->ID . '"' . $value . $class_names .'>';

        $anchor_attributes = ! empty( $item->attr_title ) ? ' title="' . esc_attr( $item->attr_title ) .'"' : '';
        $anchor_attributes .= ! empty( $item->target ) ? ' target="' . esc_attr( $item->target ) .'"' : '';
        $anchor_attributes .= ! empty( $item->xfn ) ? ' rel="' . esc_attr( $item->xfn ) .'"' : '';
        $anchor_attributes .= ! empty( $item->url ) ? ' href="' . esc_attr( $item->url ) .'"' : '';
        $anchor_attributes .= $this->wrap_link ? ' style="display:block"' : '';
        $anchor = '<a'. $anchor_attributes .'>';

        $item_output = $args->before;

        $item_output .= $this->wrap_link ? $anchor : "";
        $item_output .= $this->show_icon ? '<span class="descriptive-menu-icon"></span>' : '';
        $item_output .= '<span class="descriptive-menu-link">';
        $item_output .= ($this->wrap_link == false) ? $anchor : "";
        $item_output .= $args->link_before . apply_filters( 'the_title', $item->title, $item->ID ) . $args->link_after;
        $item_output .= ($this->wrap_link == false) ? "</a>" : "";
        $item_output .= '</span><p class="descriptive-menu-description">' . $item->description . '</p>';

        $item_output .= $this->wrap_link ? "</a>" : "";
        $item_output .= $args->after;

        $output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
    }
}

class Descriptive_Menu extends WP_Widget {
	function __construct() {
		parent::__construct(
			'descriptive_menu', // Base ID
			'Descriptive Menu', // Name
			array( 'description' => __( 'Render menus with a description.', 'text_domain' ), ) // Args
		);
	}

	// This renders the widget 
	public function widget($args, $instance) {
		$nav_menu = ! empty( $instance['nav_menu'] ) ? wp_get_nav_menu_object( $instance['nav_menu'] ) : false;
		if ( !$nav_menu ) { return; }

		$instance['title'] = apply_filters( 'widget_title', empty( $instance['title'] ) ? '' : $instance['title'], $instance, $this->id_base );
		$class = apply_filters( 'widget_title', $instance['class'] );
		$show_icon = isset( $instance['show_icon'] ) ? $instance['show_icon'] : false;
		$wrap_link = isset( $instance['wrap_link'] ) ? $instance['wrap_link'] : false;

		echo $args['before_widget'];

		if ( !empty($instance['title']) ) {
			echo $args['before_title'] . $instance['title'] . $args['after_title'];
		}

		wp_nav_menu( array( 'fallback_cb' => '', 'menu' => $nav_menu, 'menu_class' => $class . ' descriptive-menu', 'walker' => new Menu_With_Description($show_icon, $wrap_link) ) );

		echo $args['after_widget'];
	}

	// This is the admin view of the widget
	public function form( $instance ) {
		$title = isset( $instance[ 'title' ] ) ? $instance[ 'title' ] : __( 'New title', 'text_domain' );
		$nav_menu = isset( $instance[ 'nav_menu' ] ) ? $instance[ 'nav_menu' ] : __( 'menu_id', 'text_domain' );
		$class = isset( $instance[ 'class' ] ) ? $instance[ 'class' ] : __( '', 'text_domain' );
		$show_icon = isset( $instance['show_icon'] ) ? $instance['show_icon'] : false;
		$wrap_link = isset( $instance['wrap_link'] ) ? $instance['wrap_link'] : false;

		$menus = get_terms( 'nav_menu', array( 'hide_empty' => false ) );

		if ( !$menus ) {
			echo '<p>'. sprintf( __('No menus have been created yet. <a href="%s">Create some</a>.'), admin_url('nav-menus.php') ) .'</p>';
			return;
		}
		?>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('nav_menu'); ?>"><?php _e('Select Menu:'); ?></label>
			<select id="<?php echo $this->get_field_id('nav_menu'); ?>" name="<?php echo $this->get_field_name('nav_menu'); ?>">
			<?php
				foreach ( $menus as $menu ) {
					echo '<option value="' . $menu->term_id . '"'
						. selected( $nav_menu, $menu->term_id, false )
						. '>'. $menu->name . '</option>';
				}
			?>
			</select>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'class' ); ?>"><?php _e( 'Classes:' ); ?></label> 
			<input class="widefat" id="<?php echo $this->get_field_id( 'class' ); ?>" name="<?php echo $this->get_field_name( 'class' ); ?>" type="text" value="<?php echo esc_attr( $class ); ?>" />
		</p>
		<p>
			<input class="checkbox" type="checkbox" <?php checked( $show_icon ); ?> id="<?php echo $this->get_field_id( 'show_icon' ); ?>" name="<?php echo $this->get_field_name( 'show_icon' ); ?>" />
			<label for="<?php echo $this->get_field_id( 'show_icon' ); ?>"><?php _e( 'Include an element for an icon?' ); ?></label>
		</p>
		<p>
			<input class="checkbox" type="checkbox" <?php checked( $wrap_link ); ?> id="<?php echo $this->get_field_id( 'wrap_link' ); ?>" name="<?php echo $this->get_field_name( 'wrap_link' ); ?>" />
			<label for="<?php echo $this->get_field_id( 'wrap_link' ); ?>"><?php _e( 'Wrap the entire menu item inside the anchor tag?<small style="line-height: 130%;display: block;margin-top: 5px;"><em>When checked, the anchor starting tag is put in front of the descriptive-menu-icon element with an inline-style of display:block;. This allows the entire menu item to act as a link, instead of just the menu title text.</em></small>' ); ?></label>
		</p>
		<?php 
	}

	// This sanitizes the options before WordPress saves them
	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
		$instance['nav_menu'] = ( ! empty( $new_instance['nav_menu'] ) ) ? strip_tags( $new_instance['nav_menu'] ) : '';
		$instance['class'] = ( ! empty( $new_instance['class'] ) ) ? strip_tags( $new_instance['class'] ) : '';
		$instance['show_icon'] = isset( $new_instance['show_icon'] ) ? (bool) $new_instance['show_icon'] : false;
		$instance['wrap_link'] = isset( $new_instance['wrap_link'] ) ? (bool) $new_instance['wrap_link'] : false;

		return $instance;
	}
} 

add_action('widgets_init', create_function('', 'return register_widget("Descriptive_Menu");'));