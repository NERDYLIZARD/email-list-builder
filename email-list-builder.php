<?php
/*
 * Plugin Name: Email List Builder
 * Text Domain: email-list-builder
*/


/* !0. TABLE OF CONTENTS */

/*

  1. HOOKS
		1.1 - registers all our custom shortcodes
		1.2 - register custom admin column headers
		1.3 - register custom admin column data
		1.4 - register ajax actions

	2. SHORTCODES
		2.1 - elb_register_shortcodes()
		2.2 - elb_form_shortcode()

	3. FILTERS
		3.1 - elb_subscriber_column_headers()
		3.2 - elb_subscriber_column_data()
      3.2.2 - elb_register_custom_admin_titles()
      3.2.3 - elb_custom_admin_titles()
		3.3 - elb_list_column_headers()
		3.4 - elb_list_column_data()

	4. EXTERNAL SCRIPTS

	5. ACTIONS
		5.1 - elb_save_subscription()
		5.2 - elb_save_subscriber()
		5.3 - elb_add_subscription()

	6. HELPERS
		6.1 - elb_subscriber_has_subscription()
		6.2 - elb_get_subscriber_id()
		6.3 - elb_get_subscritions()
		6.4 - elb_return_json()
		6.5 - elb_get_acf_key()
		6.6 - elb_get_subscriber_data()

	7. CUSTOM POST TYPES

	8. ADMIN PAGES

	9. SETTINGS

	10. MISCELLANEOUS

*/




/* !1. HOOKS */

// 1.1 - register all custom shortcodes on init
add_action('init', 'elb_register_shortcodes');

// 1.2 - register custom admin's column header
add_filter('manage_edit-elb_subscriber_columns', 'elb_subscriber_column_headers');
add_filter('manage_edit-elb_list_columns', 'elb_list_column_headers');


// 1.3 - register custom admin's column data
add_filter('manage_elb_subscriber_posts_custom_column', 'elb_subscriber_column_data', 1, 2);
add_filter('manage_elb_list_posts_custom_column', 'elb_list_column_data', 1, 2);

add_action('admin_head-edit.php', 'elb_register_custom_admin_titles');

// 1.4
// hint: register ajax actions
add_action('wp_ajax_nopriv_elb_save_subscription', 'elb_save_subscription'); // regular website visitor
add_action('wp_ajax_elb_save_subscription', 'elb_save_subscription'); // admin user



/* !2. SHORTCODES */

// 2.1 - register all shortcodes
function elb_register_shortcodes()
{
  add_shortcode('elb_form', 'elb_form_shortcode');
}

// 2.2 - return html subscription form
function elb_form_shortcode($args, $content = '')
{

  $list_id = isset($args['id']) ? (int)$args['id'] : 0;

  // html form
  $output = '
    <div class="elb">
		
			<form id="elb_form" name="elb_form" class="elb-form" method="post"
			  action="' . admin_url() . 'admin-ajax.php?action=elb_save_subscription"
			>
			  
			  <input type="hidden" name="elb_list" value="' . $list_id . '">
			
				<p class="elb-input-container">
				
					<label>Your Name</label><br />
					<input type="text" name="elb_fname" placeholder="First Name" />
					<input type="text" name="elb_lname" placeholder="Last Name" />
				
				</p>
				
				<p class="elb-input-container">
				
					<label>Your Email</label><br />
					<input type="email" name="elb_email" placeholder="you@email.com" />
				
				</p>
  ';

  // append input content
  if (strlen($content))
    $output .= '<div class="elb_content">'. wpautop($content) . '</div>';

  // append submit button
  $output .= '
        <p class="elb-input-container">
          <input type="submit" name="elb_submit" value="Sign Me Up!" />
        </p>
      </form>	
    </div>
  ';

  return $output;
}



/* !3. FILTERS */

// 3.1
function elb_subscriber_column_headers($columns)
{
  // custom header fields
  $columns = [
    'cb'    => '<input type="checkbox"/>',
    'title' => __('Subscriber Name'),
    'email' => __('Email'),
  ];
  return $columns;
}

// 3.2
function elb_subscriber_column_data($columns, $post_id)
{
  $output = '';

  switch ($columns) {
    case 'title':
      $fname = get_field('elb_fname', $post_id);
      $lname = get_field('elb_lname', $post_id);
      $output .= $fname . ' ' . $lname;
      break;
    case 'email':
      $output .= get_field('elb_email', $post_id);
      break;
  }
  echo $output;
}

// 3.2.1
// hint: registers special custom admin title columns
function elb_register_custom_admin_titles()
{
  add_filter('the_title', 'elb_custom_admin_titles', 99, 2);
}

// 3.2.2
// hint: handles custom admin title "title" column data for post types without titles
function elb_custom_admin_titles($title, $post_id)
{
  global $post;

  $output = $title;

  if (isset($post->post_type)) {
    switch ($post->post_type) {
      case 'elb_subscriber':
        $fname = get_field('elb_fname', $post_id);
        $lname = get_field('elb_lname', $post_id);
        $output = $fname . ' ' . $lname;
        break;
    }
  }
  return $output;
}

// 3.3
function elb_list_column_headers($column)
{
  // custom header fields
  $columns = [
    'cb'    => '<input type="checkbox"/>',
    'title' => __('List Name'),
  ];
  return $columns;
}

// 3.4
function elb_list_column_data($columns, $post_id)
{
  $output = '';

  switch ($columns) {
    // just template for now
    case 'example':
      break;
  }
  echo $output;
}


/* !4. EXTERNAL SCRIPTS */




/* !5. ACTIONS */
// 5.1
function elb_save_subscription()
{
  $result = [
    'status'  => 0,
    'message' => 'Subscription was not saved. '
  ];

  try {

	  $list_id = (int) $_POST['elb_list'];

	  $subscriber_data = [
		  'fname' => sanitize_text_field( $_POST['elb_fname'] ),
		  'lname' => sanitize_text_field( $_POST['elb_lname'] ),
		  'email' => sanitize_email( $_POST['elb_email'] ),
	  ];

	  // attempt to create/save subscriber
	  $subscriber_id = elb_save_subscriber( $subscriber_data );

	  // if successfully create/save subscriber
	  if ( $subscriber_id ) {
		  // check if the list has been subscribed
      if (elb_subscriber_has_subscription($subscriber_id, $list_id)) {
        // get list object
        $list = get_post($list_id);
        $result['message'] .= esc_attr( $subscriber_data['email'] .' is already subscribed to '. $list->post_title .'.');
      }
      else {
        $subscriber_saved = elb_add_subscription($subscriber_id, $list_id);

        if ($subscriber_saved) {
          $result['status'] = 1;
          $result['message'] = 'Successfully Subscribed';
        }
      }
	  }
  }
  catch (Exception $exception) {}

  // return json
	elb_return_json($result);

}

// 5.2
// hint: save/create a subscriber
function elb_save_subscriber($subscriber_data)
{
  // set default to indicate error in saving
  $subscriber_id = 0;

  try {
	  $subscriber_id = elb_get_subscriber_id( $subscriber_data['email'] );

	  // if she's new subscriber
	  if ( !$subscriber_id ) {
		  // add new subscriber
		  $subscriber_id = wp_insert_post( [
			  'post_type'   => 'elb_subscriber',
			  'post_title'  => $subscriber_data['fname'] . ' ' . $subscriber_data['lname'],
			  'post_status' => 'publish'
		  ], true );
	  }
    // update subscriber's data
    update_field(elb_get_acf_key('elb_fname'), $subscriber_data['fname'], $subscriber_id);
    update_field(elb_get_acf_key('elb_lname'), $subscriber_data['lname'], $subscriber_id);
    update_field(elb_get_acf_key('elb_email'), $subscriber_data['email'], $subscriber_id);

  }
  catch (Exception $exception) {
    // runtime error i.e. saving failed
  }

  return $subscriber_id;
}

// 5.3
function elb_add_subscription($subscriber_id, $list_id)
{
  $subscription_saved = false;

  if (!elb_subscriber_has_subscription($subscriber_id, $list_id)) {

    $subscriptions = elb_get_subscriptions($subscriber_id);
	  $subscriptions []= $list_id;

	  // update ACF
	  update_field( elb_get_acf_key( 'elb_subscriptions' ), $subscriptions, $subscriber_id );

	  $subscription_saved = true;
  }

  return $subscription_saved;
}


/* !6. HELPERS */

// 6.1
function elb_subscriber_has_subscription($subscriber_id, $list_id)
{
  $has_subscription = false;

  $subscriptions = elb_get_subscriptions($subscriber_id);

	if (in_array($list_id, $subscriptions))
    $has_subscription = true;

  return $has_subscription;

}

// 6.2
// hint: retrieves a subscriber_id from an email address
function elb_get_subscriber_id($email)
{
  $subscriber_id = 0;

  try {
    $subscriber_query = new WP_Query([
      'post_type'       => 'elb_subscriber',
      'posts_per_page'  => 1,
      'meta_key'        => 'elb_email',
      'meta_query'      => [
        'key'     => 'elb_email',
        'value'   => $email,
        'compare' => '='
      ]
    ]);

    // if subscriber exists
    if ($subscriber_query->have_posts()) {
      // get her id
      $subscriber_query->the_post();
      $subscriber_id = get_the_ID();
    }

  } catch (Exception $exception) {

  }

  // reset query init by have_posts()
  wp_reset_query();
  return (int)$subscriber_id;
}


// 6.3
// hint: return subscription's id
function elb_get_subscriptions($subscriber_id)
{
  $subscriptions = [];

  $lists = get_field(elb_get_acf_key('elb_subscriptions'), $subscriber_id);

  if ($lists) {
    foreach ( $lists as $list )
      $subscriptions []= $list->ID;
  }

  return (array)$subscriptions;
}

// 6.4
function elb_return_json($php_array)
{
  $json_format = json_encode($php_array);

  // stop all other processes and return
  die($json_format);
}

// 6.5
// hint: gets the unique act field key from the field name
function elb_get_acf_key($field_name)
{
  // field_id extract from Advanced Custom Field form
  switch ($field_name) {
    case 'elb_fname':
      return 'field_59aa6f41ef4d5';
    case 'elb_lname':
      return 'field_59aa6f83ef4d6';
    case 'elb_email':
      return 'field_59aa6f96ef4d7';
    case 'elb_subscriptions':
      return 'field_59aa6fbaef4d8';
  }
  return $field_name;
}


/* !7. CUSTOM POST TYPES */




/* !8. ADMIN PAGES */




/* !9. SETTINGS */




/* !10. MISCELLANEOUS */



