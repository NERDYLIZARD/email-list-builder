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
		"show_in_menu" => true,
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