<?php

// Taken from http://www.paulund.co.uk/custom-wordpress-controls

if ( ! class_exists( 'WP_Customize_Control' ) )
    return NULL;

class News_Dropdown_Custom_Control extends WP_Customize_Control
{
    private $posts = false;

    public function __construct($manager, $id, $args = array(), $options = array())
    {
        $postargs = wp_parse_args($options, array('numberposts' => '-1', 'post_type' => 'news'));
        $this->posts = get_posts($postargs);

        parent::__construct( $manager, $id, $args );
    }

    /**
    * Render the content on the theme customizer page
    */
    public function render_content()
    {
        if(!empty($this->posts))
        {
            ?>
                <label>
                    <span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
                    <input id="<?php echo $this->id; ?>-autocomplete" value="<?php echo get_the_title($this->value()); ?>">
                    <input id="<?php echo $this->id; ?>" value="<?php echo $this->value(); ?>" <?php $this->link(); ?> type="hidden">
                    <a href="#" onclick="clearField('<?php echo $this->id; ?>');">Clear</a>
                </label>

                <script type='text/javascript'>

                  jQuery(function($) {

                    var newsposts = [
                      <?php
                        foreach ( $this->posts as $post ) {
                          printf('{');
                          printf('postid: "%s", label: "%s"', $post->ID, $post->post_title);
                          printf('},');
                        }
                      ?>
                    ];

                    $("#<?=$this->id ?>-autocomplete").autocomplete({
                      source: newsposts,
                      change: function(event,ui) {
                        $("#<?=$this->id ?>").val(ui.item.postid).trigger('change');
                      }
                    });

                  });

                  function clearField(callingEl) {
                    jQuery(function($) {
                      $("#"+callingEl).val('').trigger('change');
                      $("#"+callingEl+"-autocomplete").val('');
                    });
                  }


                </script>
            <?php
        }
    }
}

?>
