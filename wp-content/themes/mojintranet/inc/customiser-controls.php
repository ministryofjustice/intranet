<?php

// Taken from http://www.paulund.co.uk/custom-wordpress-controls

if ( ! class_exists( 'WP_Customize_Control' ) )
    return NULL;

class News_Dropdown_Custom_Control extends WP_Customize_Control
{
    private $news_stories = false;
    private $posts = false;
    private $pages = false;

    public function __construct($manager, $id, $args = array(), $options = array())
    {
        $postargs = wp_parse_args($options, array('numberposts' => '-1'));

        $context = Agency_Context::get_agency_context();

        $postargs['tax_query'] =  array(
            array(
                'taxonomy' => 'agency',
                'field'    => 'slug',
                'terms'    => $context,
            )
        );

        $this->news_stories = get_posts(wp_parse_args($postargs, array('post_type' => 'news')));
        $this->posts = get_posts(wp_parse_args($postargs, array('post_type' => 'post')));
        $this->pages = get_posts(wp_parse_args($postargs, array('post_type' => 'page')));

        parent::__construct( $manager, $id, $args );
    }

    /**
    * Render the content on the theme customizer page
    */
    public function render_content()
    {
        $available_types = [];

        if(!empty($this->news_stories)) {
            $available_types['news'] = 'News';
        }

        if(!empty($this->posts)) {
            $available_types['post'] = 'Blog Post';
        }

        if(!empty($this->pages)) {
            $available_types['page'] = 'Page';
        }

        if(count($available_types) > 0)
        {
            if(!empty($this->value())) {
                $current_type = get_post_type($this->value());
            }
            else {
                $current_type = 'news';
            }
            ?>
                <label>
                    <span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
                    Type:
                    <select id="<?php echo $this->id; ?>-type">
                        <?php foreach ($available_types as $type_value => $type_name) { ?>
                            <option value="<?php echo $type_value; ?>" <?php if($type_value == $current_type){?>selected="selected"<?php } ?>>
                                <?php echo $type_name; ?>
                            </option>
                        <?php } ?>
                    </select>
                    <div>
                    Item: <input id="<?php echo $this->id; ?>-autocomplete" value="<?php echo get_the_title($this->value()); ?>">
                    <input id="<?php echo $this->id; ?>" value="<?php echo $this->value(); ?>" <?php $this->link(); ?> type="hidden">
                    <a href="#" onclick="clearField('<?php echo $this->id; ?>');">Clear</a>
                    </div>
                </label>

                <script type='text/javascript'>

                  jQuery(function($) {

                    var news_list = [
                      <?php
                        foreach ( $this->news_stories as $post ) {
                          printf('{');
                          printf('postid: "%s", label: "%s"', $post->ID, addslashes($post->post_title));
                          printf('},');
                        }
                      ?>
                    ];

                    var post_list = [
                      <?php
                        foreach ( $this->posts as $post ) {
                          printf('{');
                          printf('postid: "%s", label: "%s"', $post->ID, addslashes($post->post_title));
                          printf('},');
                        }
                      ?>
                    ];

                    var page_list = [
                      <?php
                        foreach ( $this->pages as $post ) {
                          printf('{');
                          printf('postid: "%s", label: "%s"', $post->ID, addslashes($post->post_title));
                          printf('},');
                        }
                      ?>
                    ];

                      $("#<?=$this->id ?>-type").change(function() {
                          $("#<?=$this->id ?>-autocomplete").autocomplete( "destroy" );

                          var source_list = news_list;

                          if($(this).val() == 'post') {
                              source_list = post_list;
                          }
                          else if ($(this).val() == 'page') {
                              source_list = page_list;
                          }

                          $("#<?=$this->id ?>-autocomplete").autocomplete({
                              source: source_list,
                              select: function(event,ui) {
                                  $("#<?=$this->id ?>").val(ui.item.postid).trigger('change');
                              }
                          });

                      });

                    $("#<?=$this->id ?>-autocomplete").autocomplete({
                      source: <?php echo $current_type . '_list'; ?>,
                      select: function(event,ui) {
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
