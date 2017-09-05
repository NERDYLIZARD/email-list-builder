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
	  1.5 - load external files to public website
	  1.6 - Advanced Custom Fields Settings
		1.7 - register custom menus

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
		3.5 - elb_admin_menus()


	4. EXTERNAL SCRIPTS
		4.1 - include ACF
		4.2 - elb_public_scripts()


	5. ACTIONS
		5.1 - elb_save_subscription()
		5.2 - elb_save_subscriber()
		5.3 - elb_add_subscription()

	6. HELPERS
		6.1 - elb_subscriber_has_subscription()
		6.2 - elb_get_subscriber_id()
		6.3 - elb_get_subscriptions()
		6.4 - elb_return_json()
		6.5 - elb_get_acf_key()
		6.6 - elb_get_subscriber_data()
		6.7 - elb_get_page_select()

	7. CUSTOM POST TYPES
		7.1 - subscribers
		7.2 - lists

	8. ADMIN PAGES
		8.1 - elb_dashboard_admin_page()
		8.2 - elb_import_admin_page()
		8.3 - elb_options_admin_page()

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

// 1.4 - register ajax actions
add_action('wp_ajax_nopriv_elb_save_subscription', 'elb_save_subscription'); // regular website visitor
add_action('wp_ajax_elb_save_subscription', 'elb_save_subscription'); // admin user

// 1.5 - load external files to public website
add_action('wp_enqueue_scripts', 'elb_public_scripts');

// 1.6  - Advanced Custom Fields Settings
add_filter('acf/settings/path', 'elb_acf_settings_path');
add_filter('acf/settings/dir', 'elb_acf_settings_dir');
add_filter('acf/settings/show_admin', 'elb_acf_show_admin');
if( !defined('ACF_LITE') ) define('ACF_LITE',true); // turn off ACF plugin menu

// 1.7 - register custom menus
add_action('admin_menu', 'elb_admin_menus');



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

	$title = isset($args['title']) ? $args['title'] : '';

  // html form
  $output = '
    <div class="elb">
		
			<form id="elb_form" name="elb_form" class="elb-form" method="post"
			  action="' . admin_url() . 'admin-ajax.php?action=elb_save_subscription">
			  
			  <input type="hidden" name="elb_list" value="' . $list_id . '">';

	if( strlen($title) )
		$output .= '<h3 class="elb-title">'. $title .'</h3>';


			$output .=	'
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
    'cb'        => '<input type="checkbox"/>',
    'title'     => __('List Name'),
	  'shortcode' => __('Short Code')
  ];
  return $columns;
}

// 3.4
function elb_list_column_data($columns, $post_id)
{
  $output = '';

  switch ($columns) {
    // just template for now
    case 'shortcode':
	    $output .= '[elb_form id="'. $post_id .'"]';
      break;
  }
  echo $output;
}

// 3.5
function elb_admin_menus()
{
	// main menu
	$top_menu_item = 'elb_dashboard_admin_page';

	add_menu_page('', 'List Builder', 'manage_options', $top_menu_item, $top_menu_item, 'dashicons-email-alt');

	// submenu items
	add_submenu_page($top_menu_item, 'Email List Builder', 'Dashboard', 'manage_options', $top_menu_item);
	add_submenu_page($top_menu_item, '', 'Email Lists', 'manage_options','edit.php?post_type=elb_list');
	add_submenu_page($top_menu_item, '', 'Subscribers', 'manage_options','edit.php?post_type=elb_subscriber');
	add_submenu_page($top_menu_item, 'Import Subscribers', 'Import Subscribers', 'manage_options','elb_import_admin_page', 'elb_import_admin_page');
	add_submenu_page($top_menu_item, 'List Builder Options', 'Plugin Options', 'manage_options','elb_options_admin_page', 'elb_options_admin_page');

}


/* !4. EXTERNAL SCRIPTS */

// 4.1 - Include ACF
include_once( plugin_dir_path( __FILE__ ) .'lib/advanced-custom-fields/acf.php' );


// 4.2
// hint: loads external files into PUBLIC website
function elb_public_scripts()
{
	// registration
	wp_register_script(
		'email-list-builder-js-public',
		plugins_url('/js/public/email-list-builder.js',__FILE__),
		['jquery'], '', true
	);
	wp_register_style('email-list-builder-css-public', plugins_url('/css/public/email-list-builder.css',__FILE__));

	// enqueue
	wp_enqueue_script('email-list-builder-js-public');
	wp_enqueue_style('email-list-builder-css-public');
}



/* !5. ACTIONS */
// 5.1
function elb_save_subscription()
{
  $result = [
    'status'  => 0,
    'message' => 'Subscription was not saved. ',
	  'error'   => '',
		'errors'  => []
  ];

  // assign input variables
	$fname = $_POST['elb_fname'];
	$lname = $_POST['elb_lname'];
	$email = $_POST['elb_email'];

  // validate input
	$errors = [];

	if (!strlen($fname)) $errors['elb_fname'] = 'First Name is Required';
	if (!strlen($email)) $errors['elb_email'] = 'Email is Required';
	if (strlen($email) && !is_email($email)) $errors['elb_email'] = 'Email is invalid';

	// if there are the validation errors
	if (count($errors)) {
		$result['error'] = 'Please fill up the required fields';
		$result['errors'] = $errors;
		elb_return_json( $result );
	}

  try {

	  $list_id = (int) $_POST['elb_list'];

	  $subscriber_data = [
		  'fname' => sanitize_text_field( $fname ),
		  'lname' => sanitize_text_field( $lname ),
		  'email' => sanitize_email( $email ),
	  ];

	  // attempt to create/save subscriber
	  $subscriber_id = elb_save_subscriber( $subscriber_data );

	  // if successfully create/save subscriber
	  if ( $subscriber_id ) {
		  // check if the list has been subscribed
      if (elb_subscriber_has_subscription($subscriber_id, $list_id)) {
        // get list object
        $list = get_post($list_id);
        $result['error'] .= esc_attr( $subscriber_data['email'] .' is already subscribed to '. $list->post_title .'.');
      }
      else {
        $subscriber_saved = elb_add_subscription($subscriber_id, $list_id);

        if ($subscriber_saved) {
          $result['status'] = 1;
          $result['message'] = 'Successfully Subscribed';
        }
        else {
        	$result['error'] = 'Unable to save subscription';
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
      // instantiate post object
	    $subscriber_query->the_post();
	    // get her id
	    $subscriber_id = get_the_ID();
    }

  } catch (Exception $exception) {

  }

  // reset query init by tbe_post()
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

// 6.7
// hint: returns html for a page selector
function elb_get_page_select( $input_name="elb_page", $input_id="", $parent=-1, $value_field="id", $selected_value="" )
{
	// get WP pages
	$pages = get_pages([
		'sort_order'  => 'asc',
		'sort_column' => 'post_title',
		'post_type'   => 'page',
		'parent'      => $parent,
		'status'      => ['draft','publish'],
	]);

	// setup our select html
	$select = '<select name="'. $input_name .'" ';

	// IF $input_id was passed in
	if( strlen($input_id) ):

		// add an input id to our select html
		$select .= 'id="'. $input_id .'" ';

	endif;

	// setup our first select option
	$select .= '><option value="">- Select One -</option>';

	// loop over all the pages
	foreach ( $pages as &$page ):

		// get the page id as our default option value
		$value = $page->ID;

		// determine which page attribute is the desired value field
		switch( $value_field ) {
			case 'slug':
				$value = $page->post_name;
				break;
			case 'url':
				$value = get_page_link( $page->ID );
				break;
			default:
				$value = $page->ID;
		}

		// check if this option is the currently selected option
		$selected = '';
		if( $selected_value == $value ):
			$selected = ' selected="selected" ';
		endif;

		// build our option html
		$option = '<option value="' . $value . '" '. $selected .'>';
		$option .= $page->post_title;
		$option .= '</option>';

		// append our option to the select html
		$select .= $option;

	endforeach;

	// close our select html tag
	$select .= '</select>';

	// return our new select
	return $select;

}


/* !7. CUSTOM POST TYPES */
// 7.1 - subscribers
include_once( plugin_dir_path( __FILE__ ) . 'cpt/elb_subscriber.php');

// 7.2 - lists
include_once( plugin_dir_path( __FILE__ ) . 'cpt/elb_list.php');




/* !8. ADMIN PAGES */

// 8.1
// hint: dashboard admin page
function elb_dashboard_admin_page() {


	$output = '
		<div class="wrap">
			
			<h2>Email List Builder</h2>
			
			<p>The ultimate email list building plugin for WordPress. Capture new subscribers. Reward subscribers with a custom download upon opt-in. Build unlimited lists. Import and export subscribers easily with .csv</p>
		
		</div>
	';

	echo $output;

}

// 8.2
// hint: import subscribers admin page
function elb_import_admin_page() {


	$output = '
		<div class="wrap">
			
			<h2>Import Subscribers</h2>
			
			<p>Page description...</p>
		
		</div>
	';

	echo $output;

}

// 8.3
// hint: plugin options admin page
function elb_options_admin_page() {

	echo('<div class="wrap">
		
		<h2>Email List Builder Options</h2>
		
		<form action="options.php" method="post">
			
			<table class="form-table">
			
				<tbody>
			
					<tr>
						<th scope="row"><label for="elb_manage_subscription_page_id">Manage Subscriptions Page</label></th>
						<td>
							'. elb_get_page_select( 'elb_manage_subscription_page_id', 'elb_manage_subscription_page_id', 0, 'id', '') .'
							<p class="description" id="elb_manage_subscription_page_id-description">This is the page where Email List Builder will send subscribers to manage their subscriptions. <br />
								IMPORTANT: the page you select must contain the shortcode: <strong>[elb_manage_subscriptions]</strong>.</p>
						</td>
					</tr>
					
			
					<tr>
						<th scope="row"><label for="elb_confirmation_page_id">Opt-In Page</label></th>
						<td>
							'. elb_get_page_select( 'elb_confirmation_page_id', 'elb_confirmation_page_id', 0, 'id', '' ) .'
							<p class="description" id="elb_confirmation_page_id-description">This is the page where Email List Builder will send subscribers to confirm their subscriptions. <br />
								IMPORTANT: the page you select must contain the shortcode: <strong>[elb_confirm_subscription]</strong>.</p>
						</td>
					</tr>
					
			
					<tr>
						<th scope="row"><label for="elb_reward_page_id">Download Reward Page</label></th>
						<td>
							'. elb_get_page_select( 'elb_reward_page_id', 'elb_reward_page_id', 0, 'id', '' ) .'
							<p class="description" id="elb_reward_page_id-description">This is the page where Email List Builder will send subscribers to retrieve their reward downloads. <br />
								IMPORTANT: the page you select must contain the shortcode: <strong>[elb_download_reward]</strong>.</p>
						</td>
					</tr>
			
					<tr>
						<th scope="row"><label for="elb_default_email_footer">Email Footer</label></th>
						<td>');


							// wp_editor will act funny if it's stored in a string so we run it like this...
							wp_editor( '', 'elb_default_email_footer', [ 'textarea_rows' => 8 ] );


							echo('<p class="description" id="elb_default_email_footer-description">The default text that appears at the end of emails generated by this plugin.</p>
						</td>
					</tr>
			
					<tr>
						<th scope="row"><label for="elb_download_limit">Reward Download Limit</label></th>
						<td>
							<input type="number" name="elb_download_limit" value="0" class="" />
							<p class="description" id="elb_download_limit-description">The amount of downloads a reward link will allow before expiring.</p>
						</td>
					</tr>
			
				</tbody>
				
			</table>
		
			<p class="submit">
				<input type="submit" name="submit" id="submit" class="button button-primary" value="Save Changes">
			</p>
		
		
		</form>
	
	</div>');

}



/* !9. SETTINGS */




/* !10. MISCELLANEOUS */



