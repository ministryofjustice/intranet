<?php

if( function_exists('acf_add_local_field_group') ):

  acf_add_local_field_group(array (
      'key' => 'group_574d8d8831351',
      'title' => 'Agencies',
      'fields' => array (
          array (
              'key' => 'field_574d8d8cd511a',
              'label' => 'Used by',
              'name' => 'term_used_by',
              'type' => 'taxonomy',
              'instructions' => '',
              'required' => 0,
              'conditional_logic' => 0,
              'wrapper' => array (
                  'width' => '',
                  'class' => '',
                  'id' => '',
              ),
              'taxonomy' => 'agency',
              'field_type' => 'checkbox',
              'allow_null' => 0,
              'add_term' => 1,
              'save_terms' => 0,
              'load_terms' => 0,
              'return_format' => 'id',
              'multiple' => 0,
          ),
      ),
      'location' => array (
          array (
              array (
                  'param' => 'taxonomy',
                  'operator' => '==',
                  'value' => 'news_category',
              ),
          ),
          array (
              array (
                  'param' => 'taxonomy',
                  'operator' => '==',
                  'value' => 'resource_category',
              ),
          ),
      ),
      'menu_order' => 0,
      'position' => 'normal',
      'style' => 'default',
      'label_placement' => 'top',
      'instruction_placement' => 'label',
      'hide_on_screen' => '',
      'active' => 1,
      'description' => '',
  ));

endif;
