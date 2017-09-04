<?php
/**
 * Date: 04-Sep-17
 * Time: 21:48
 */

if(function_exists("register_field_group"))
{
	register_field_group(array (
		'id' => 'acf_subscriber-details',
		'title' => 'Subscriber Details',
		'fields' => array (
			array (
				'key' => 'field_59aa6f41ef4d5',
				'label' => 'First Name',
				'name' => 'elb_fname',
				'type' => 'text',
				'required' => 1,
				'default_value' => '',
				'placeholder' => '',
				'prepend' => '',
				'append' => '',
				'formatting' => 'html',
				'maxlength' => '',
			),
			array (
				'key' => 'field_59aa6f83ef4d6',
				'label' => 'Last Name',
				'name' => 'elb_lname',
				'type' => 'text',
				'default_value' => '',
				'placeholder' => '',
				'prepend' => '',
				'append' => '',
				'formatting' => 'html',
				'maxlength' => '',
			),
			array (
				'key' => 'field_59aa6f96ef4d7',
				'label' => 'Email Address',
				'name' => 'elb_email',
				'type' => 'email',
				'required' => 1,
				'default_value' => '',
				'placeholder' => '',
				'prepend' => '',
				'append' => '',
			),
			array (
				'key' => 'field_59aa6fbaef4d8',
				'label' => 'Subscriptions',
				'name' => 'elb_subscriptions',
				'type' => 'post_object',
				'post_type' => array (
					0 => 'elb_list',
				),
				'taxonomy' => array (
					0 => 'all',
				),
				'allow_null' => 1,
				'multiple' => 1,
			),
		),
		'location' => array (
			array (
				array (
					'param' => 'post_type',
					'operator' => '==',
					'value' => 'elb_subscriber',
					'order_no' => 0,
					'group_no' => 0,
				),
			),
		),
		'options' => array (
			'position' => 'acf_after_title',
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