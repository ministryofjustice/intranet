<?php

// Taken from http://www.paulund.co.uk/custom-wordpress-controls

if ( ! class_exists( 'WP_Customize_Control' ) )
    return NULL;

class Content_Dropdown_Custom_Control extends WP_Customize_Control
{

    public function __construct($manager, $id, $args = array(), $options = array())
    {
        parent::__construct( $manager, $id, $args );
    }

    /**
     * Render the content on the theme customizer page
     */
    public function render_content()
    {
        $context = Agency_Context::get_agency_context();

        $available_types = [
            'news' => "News",
            'post' => "Blog Post",
            'page' => "Page"
        ];

        $value = $this->value();
        if (!empty($value)) {
            $current_type = get_post_type($this->value());
        } else {
            $current_type = 'news';
        }
        ?>
        <label>
            <span class="customize-control-title"><?php echo esc_html($this->label); ?></span>
            Type:
            <select id="<?php echo $this->id; ?>-type">
                <?php foreach ($available_types as $type_value => $type_name) { ?>
                    <option value="<?php echo $type_value; ?>"
                            <?php if ($type_value == $current_type){ ?>selected="selected"<?php } ?>>
                        <?php echo $type_name; ?>
                    </option>
                <?php } ?>
            </select>
            <div>
                Item: <input id="<?php echo $this->id; ?>-autocomplete"
                             value="<?php echo get_the_title($this->value()); ?>">
                <input id="<?php echo $this->id; ?>" value="<?php echo $this->value(); ?>" <?php $this->link(); ?>
                       type="hidden">
                <a href="#" onclick="clearField('<?php echo $this->id; ?>');">Clear</a>
            </div>
        </label>

        <script type='text/javascript'>

            jQuery(function ($) {

                activateAutoComplete('<?php echo $current_type; ?>');

                $("#<?=$this->id ?>-type").change(function () {
                    $("#<?=$this->id ?>-autocomplete").autocomplete("destroy");
                    $("#<?=$this->id ?>").val('').trigger('change');
                    $("#<?=$this->id ?>-autocomplete").val('');

                    activateAutoComplete($(this).val());
                });

                function activateAutoComplete(postType) {

                    $("#<?=$this->id ?>-autocomplete").autocomplete({
                        source:
                            function( request, response ) {
                                $.ajax({
                                    url : "<?php echo admin_url('admin-ajax.php'); ?>",
                                    dataType: "json",
                                    data : {
                                        action : 'get_autocomplete_items',
                                        post_type : postType,
                                        context: '<?php echo $context; ?>',
                                        search_term: request.term
                                    },
                                    success : function(data) {
                                        response(data);
                                    }
                                });
                            }
                        ,
                        select: function(event,ui) {
                            $("#<?=$this->id ?>").val(ui.item.postid).trigger('change');
                        }
                    });
                }

            });

            function clearField(callingEl) {
                jQuery(function ($) {
                    $("#" + callingEl).val('').trigger('change');
                    $("#" + callingEl + "-autocomplete").val('');
                });
            }
        </script>
            <?php

    }
}
