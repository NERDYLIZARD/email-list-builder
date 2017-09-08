<?php
/**
 * Date: 04-Sep-17
 * Time: 22:34
 */

add_action( 'init', 'elb_register_elb_list' );
function elb_register_elb_list() {

	/**
	 * Post Type: Lists.
	 */

	$labels = array(
		"name" => __( "Lists", "email-list-builder" ),
		"singular_name" => __( "List", "email-list-builder" ),
	);

	$args = array(
		"label" => __( "Lists", "email-list-builder" ),
		"labels" => $labels,
		"description" => "",
		"public" => false,
		"publicly_queryable" => true,
		"show_ui" => true,
		"show_in_rest" => false,
		"rest_base" => "",
		"has_archive" => false,
		"show_in_menu" => false,
		"exclude_from_search" => true,
		"capability_type" => "post",
		"map_meta_cap" => true,
		"hierarchical" => false,
		"rewrite" => array( "slug" => "elb_list", "with_front" => false ),
		"query_var" => true,
		"supports" => array( "title" ),
	);

	register_post_type( "elb_list", $args );
}


// Advanced Custom Fields
if(function_exists("register_field_group"))
{
	register_field_group(array (
		'id' => 'acf_list-settings',
		'title' => 'List Settings',
		'fields' => array (
			array (
				'key' => 'field_59b210ccd9a69',
				'label' => 'Enable Reward on Opt-in',
				'name' => 'elb_enable_reward',
				'type' => 'radio',
				'instructions' => 'Whether or not to reward subscribers when they sign up to the list.',
				'choices' => array (
					0 => 'No',
					1 => 'Yes',
				),
				'other_choice' => 0,
				'save_other_choice' => 0,
				'default_value' => 0,
				'layout' => 'vertical',
			),
			array (
				'key' => 'field_59b2116ad9a6a',
				'label' => 'Reward Title',
				'name' => 'elb_reward_title',
				'type' => 'text',
				'required' => 1,
				'conditional_logic' => array (
					'status' => 1,
					'rules' => array (
						array (
							'field' => 'field_59b210ccd9a69',
							'operator' => '==',
							'value' => '1',
						),
					),
					'allorany' => 'all',
				),
				'default_value' => '',
				'placeholder' => '',
				'prepend' => '',
				'append' => '',
				'formatting' => 'html',
				'maxlength' => '',
			),
			array (
				'key' => 'field_59b211b6d9a6b',
				'label' => 'Reward File',
				'name' => 'elb_reward_file',
				'type' => 'file',
				'required' => 1,
				'conditional_logic' => array (
					'status' => 1,
					'rules' => array (
						array (
							'field' => 'field_59b210ccd9a69',
							'operator' => '==',
							'value' => '1',
						),
					),
					'allorany' => 'all',
				),
				'save_format' => 'object',
				'library' => 'all',
			),
		),
		'location' => array (
			array (
				array (
					'param' => 'post_type',
					'operator' => '==',
					'value' => 'elb_list',
					'order_no' => 0,
					'group_no' => 0,
				),
			),
		),
		'options' => array (
			'position' => 'normal',
			'layout' => 'default',
			'hide_on_screen' => array (
				0 => 'permalink',
				1 => 'the_content',
				2 => 'excerpt',
				3 => 'custom_fields',
				4 => 'discussion',
				5 => 'comments',
				6 => 'revisions',
				7 => 'slug',
				8 => 'author',
				9 => 'format',
				10 => 'featured_image',
				11 => 'categories',
				12 => 'tags',
				13 => 'send-trackbacks',
			),
		),
		'menu_order' => 0,
	));
}
