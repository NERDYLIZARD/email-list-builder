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
		1.8 - load external files in WordPress admin
		1.9 - register plugin options
		1.10 - register activate/deactivate/uninstall functions
		1.11 - register on page load action i.e 'wp'

	2. SHORTCODES
		2.1 - elb_register_shortcodes()
		2.2 - elb_form_shortcode()
		2.3 - elb_manage_subscriptions_shortcode()
		2.4 - elb_confirm_subscription_shortcode()
		2.5 - elb_download_reward_shortcode()

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
		5.4 - elb_unsubscribe()
		5.5 - elb_remove_subscriptions()
		5.6 - elb_send_subscriber_email()
		5.7 - elb_confirm_subscription()
		5.8 - elb_create_plugin_tables()
		5.9 - elb_activate_plugin()
		5.10 - elb_add_reward_link()
		5.11 - elb_trigger_reward_download()
		5.12 - elb_update_reward_link_downloads()
		5.13 - elb_download_subscribers_csv()
		5.14 - elb_parse_import_csv()

	6. HELPERS
		6.1 - elb_subscriber_has_subscription()
		6.2 - elb_get_subscriber_id()
		6.3 - elb_get_subscriptions()
		6.4 - elb_return_json()
		6.5 - elb_get_acf_key()
		6.6 - elb_get_subscriber_data()
		6.7 - elb_get_page_select()
		6.8 - elb_get_default_page_options()
		6.9 - elb_get_option()
		6.10 - elb_get_current_options()
		6.11 - elb_get_manage_subscriptions_html()
		6.12 - elb_get_email_template()
		6.13 - elb_validate_list()
		6.14 - elb_validate_subscriber()
		6.15 - elb_get_manage_subscriptions_link()
		6.16 - elb_get_querystring_start()
		6.17 - elb_get_optin_link()
		6.18 - elb_get_message_html()
		6.19 - elb_get_list_reward()
		6.20 - elb_get_reward_link()
		6.21 - elb_generate_reward_uid()
		6.22 - elb_get_reward()
		6.23 - elb_get_list_subscribers()
		6.24 - elb_get_list_subscriber_count()
		6.25 - elb_get_csv_export_link()
		6.26 - elb_csv_to_array()

	7. CUSTOM POST TYPES
		7.1 - subscribers
		7.2 - lists

	8. ADMIN PAGES
		8.1 - elb_dashboard_admin_page()
		8.2 - elb_import_admin_page()
		8.3 - elb_options_admin_page()

	9. SETTINGS
		9.1 - elb_register_options()

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
add_action('wp_ajax_nopriv_elb_unsubscribe', 'elb_unsubscribe'); // regular website visitor
add_action('wp_ajax_elb_unsubscribe', 'elb_unsubscribe'); // admin user
add_action('wp_ajax_elb_download_subscribers_csv', 'elb_download_subscribers_csv'); // admin users
add_action('wp_ajax_elb_parse_import_csv', 'elb_parse_import_csv'); // admin users


// 1.5 - load external files to public website
add_action('wp_enqueue_scripts', 'elb_public_scripts');

// 1.6  - Advanced Custom Fields Settings
add_filter('acf/settings/path', 'elb_acf_settings_path');
add_filter('acf/settings/dir', 'elb_acf_settings_dir');
add_filter('acf/settings/show_admin', 'elb_acf_show_admin');
if( !defined('ACF_LITE') ) define('ACF_LITE',true); // turn off ACF plugin menu

// 1.7 - register custom menus
add_action('admin_menu', 'elb_admin_menus');

// 1.8 - load external files in WordPress admin
add_action('admin_enqueue_scripts', 'elb_admin_scripts');

// 1.9 - register plugin options
add_action('admin_init', 'elb_register_options');

// 1.10 - register activate/deactivate/uninstall functions
register_activation_hook(__FILE__, 'elb_activate_plugin');

// 1.11 - register on page load action
// trigger reward downloads
add_action('wp', 'elb_trigger_reward_download');


/* !2. SHORTCODES */

// 2.1 - register all shortcodes
function elb_register_shortcodes()
{
  add_shortcode('elb_form', 'elb_form_shortcode');
	add_shortcode('elb_manage_subscriptions', 'elb_manage_subscriptions_shortcode');
	add_shortcode('elb_confirm_subscription', 'elb_confirm_subscription_shortcode');
	add_shortcode('elb_download_reward','elb_download_reward_shortcode');
}

// 2.2 - return html subscription form
function elb_form_shortcode($args, $content = '')
{

  $list_id = isset($args['id']) ? (int)$args['id'] : 0;

	$title = isset($args['title']) ? $args['title'] : '';

  // html form
  $output = '
    <div class="elb">
		
			<form id="elb_register_form" name="elb_form" class="elb-form" method="post"
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

// 2.3
// hint: displays a form for managing the users list subscriptions
// example: [elb_manage_subscriptions]
function elb_manage_subscriptions_shortcode( $args, $content = '' )
{
	// setup return string
	$output = '<div class="elb elb-manage-subscriptions">';

	try {

		// get the email address from the URL
		$email = ( isset( $_GET['email'] ) ) ? sanitize_email( $_GET['email'] ) : '';

		// get the subscriber id from the email address
		$subscriber_id = elb_get_subscriber_id( $email );

		// IF subscriber exists
		if( $subscriber_id ) {
			// get subscriptions html
			$output .= elb_get_manage_subscriptions_html( $subscriber_id );
		}
		else {
			// invalid link
			$output .= '<p>This link is invalid.</p>';
		}

	} catch(Exception $exception ) {}

	// close our html div tag
	$output .= '</div>';

	// return our html
	return $output;

}

// 2.4
// hint: displays subscription opt-in confirmation text and link to manage subscriptions
// example: [elb_confirm_subscription]
function elb_confirm_subscription_shortcode( $args, $content = '' )
{
	// setup output variable
	$output = '<div class="elb">';

	// setup email and list_id variables and handle if they are not defined in the GET scope
	$email = ( isset( $_GET['email'] ) ) ? sanitize_email( $_GET['email'] ) : '';
	$list_id = ( isset( $_GET['list'] ) ) ? esc_attr( $_GET['list'] ) : 0;

	// get subscriber id from email
	$subscriber_id = elb_get_subscriber_id( $email );
	$subscriber = get_post( $subscriber_id );

	// IF we found a subscriber matching that email address
	if( $subscriber_id && elb_validate_subscriber( $subscriber ) ):

		// get list object
		$list = get_post( $list_id );

		// IF list and subscriber are valid
		if( elb_validate_list( $list ) ):


			// IF subscriptions has not yet been added
			if( !elb_subscriber_has_subscription( $subscriber_id, $list_id) ):

				// complete opt-in
				$optin_complete = elb_confirm_subscription( $subscriber_id, $list_id );

				if( !$optin_complete ):

					$output .= elb_get_message_html('Due to an unknown error, we were unable to confirm your subscription.', 'error');
					$output .= '</div>';

					return $output;

				endif;

			endif;

			// get confirmation message html and append it to output
			$output .= elb_get_message_html( 'Your subscription to '. $list->post_title .' has now been confirmed.', 'confirmation' );

			// get manage subscriptions link
			$manage_subscriptions_link = elb_get_manage_subscriptions_link( $email );

			// append link to output
			$output .= '<p><a href="'. $manage_subscriptions_link .'">Click here to manage your subscriptions.</a></p>';

		else:

			$output .= elb_get_message_html( 'This link is invalid.', 'error');

		endif;

	else:

		$output .= elb_get_message_html( 'This link is invalid. Invalid Subscriber '. $email .'.', 'error');

	endif;

	// close .elb div
	$output .= '</div>';

	// return output html
	return $output;

}

// 2.5
// [elb_download_reward]
// hint: returns a message if the download link has expired or is invalid
function elb_download_reward_shortcode( $args, $content = '' )
{
	$output = '';

	$uid = (isset($_GET['reward'])) ? (string)$_GET['reward'] : 0;

	// get reward form link uid
	$reward = elb_get_reward( $uid );

	// IF reward was found
	if( !$reward )
		$output .= elb_get_message_html( 'This link is invalid.', 'error');
	//	if there is the reward
	else {
		// if download limits aren't exceeded
		if ($reward['downloads'] >= elb_get_option('elb_download_limit')) {
			$output .= elb_get_message_html( 'This link has reached it\'s download limit.', 'warning');
		}
	}

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
    'cb'          => '<input type="checkbox"/>',
    'title'       => __('List Name'),
    'reward'      => __('Optin Reward'),
	  'subscribers' => __('Subscribers'),
	  'shortcode'   => __('Short Code')
  ];
  return $columns;
}

// 3.4
function elb_list_column_data($columns, $post_id)
{
  $output = '';

  switch ($columns) {
	  case 'reward':
		  $reward = elb_get_list_reward( $post_id );
		  if ( $reward )
			  $output .= '<a href="' . $reward['file']['url'] . '" download="' . $reward['title'] . '">' . $reward['title'] . '</a>';
		  break;

    case 'subscribers':
    	$subscriber_count = elb_get_list_subscriber_count($post_id);
    	$export_href = elb_get_csv_export_link($post_id);
	    $output .= $subscriber_count;
	    $output .= $subscriber_count ? ' <a href="' . $export_href . '">Export</a>' : '';
      break;

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

// 4.3
// hint: loads external files into wordpress ADMIN
function elb_admin_scripts() {

	// register scripts with WordPress's internal library
	wp_register_script('email-list-builder-js-admin', plugins_url('/js/admin/email-list-builder.js',__FILE__), ['jquery'],'',true);

	// add to que of scripts that get loaded into every admin page
	wp_enqueue_script('email-list-builder-js-admin');
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

	  // if failed to create/save subscriber
	  if ( !$subscriber_id ) {
		  // saving error
		  $result['error'] = 'Unable to save subscription';
		  elb_return_json( $result );
	  }

	  // check if the list has been subscribed
    if (elb_subscriber_has_subscription($subscriber_id, $list_id)) {
      // get list object
      $list = get_post($list_id);
      $result['error'] .= esc_attr( $subscriber_data['email'] .' is already subscribed to '. $list->post_title .'.');
      elb_return_json($result);
    }

	  // send confirmation email
    $email_sent = elb_send_subscriber_email( $subscriber_id, 'new_subscription', $list_id );

    if ( ! $email_sent ) {
	    // mailing error
	    $result['error'] = 'Unable to send confirmation email';
    } else {
	    // success
	    $result['status']  = 1;
	    $result['message'] = 'Success! A confirmation email has been sent to ' . $subscriber_data['email'];
    }
	}
  catch (Exception $exception ) {}

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
  catch (Exception $exception ) {
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


// 5.4
// hint: removes one or more subscriptions from a subscriber and notifies them via email
// this function is a ajax form handler...
// expects form post data: $_POST['subscriber_id'] and $_POST['list_id']
function elb_unsubscribe()
{
	// setup default result data
	$result = [
		'status'  => 0,
		'message' => 'Subscriptions were NOT updated. ',
		'error'   => '',
		'errors'  => [],
	];

	$list_ids = ( isset($_POST['list_ids']) ) ? $_POST['list_ids'] : [];
	$subscriber_id = ( isset($_POST['subscriber_id']) ) ? esc_attr( (int)$_POST['subscriber_id'] ) : 0;

	// validate if subscription is selected
	if (empty($list_ids)) {
		$result['error'] = 'Please select the subscriptions';
		elb_return_json( $result );
	}

	try {

		elb_remove_subscriptions( $subscriber_id, $list_ids );

		// setup success status and message
		$result['status'] = 1;
		$result['message'] = 'Subscriptions updated. ';

		// get the updated list of subscriptions as html
		$result['html'] = elb_get_manage_subscriptions_html( $subscriber_id );

	} catch( Exception $exception ) {}

	// return result as json
	elb_return_json( $result );

}

// 5.5
// hint: removes a single subscription from a subscriber
function elb_remove_subscriptions( $subscriber_id, $unsubscribed_list_ids )
{
	// single input as number, turn it to array so that it can work with array_diff()
	if (!is_array($unsubscribed_list_ids))
		$unsubscribed_list_ids = [$unsubscribed_list_ids];

	// get current subscriptions
	$subscriptions = elb_get_subscriptions( $subscriber_id );

	// remove unsubscribed lists
	$subscriptions = array_diff($subscriptions, $unsubscribed_list_ids);

	// update elb_subscriptions
	update_field(elb_get_acf_key( 'elb_subscriptions'), $subscriptions, $subscriber_id);

}

// 5.6
// hint: sends a unqiue customized email to a subscriber
function elb_send_subscriber_email( $subscriber_id, $email_template_name, $list_id )
{
	// setup return variable
	$email_sent = false;

	// get email template data
	$email_template_object = elb_get_email_template( $subscriber_id, $email_template_name, $list_id );

	// IF email template data was found
	if( !empty( $email_template_object ) ) {

		// get subscriber data
		$subscriber_data = elb_get_subscriber_data( $subscriber_id );

		// set wp_mail headers
		$wp_mail_headers = [ 'Content-Type: text/html; charset=UTF-8' ];

//		$email_sent = true;

		// use wp_mail to send email
		$email_sent = wp_mail( [ $subscriber_data['email'] ] , $email_template_object['subject'], $email_template_object['body'], $wp_mail_headers );
	}

	return $email_sent;

}

// 5.7
// hint: adds subscription to database and emails subscriber confirmation email
function elb_confirm_subscription( $subscriber_id, $list_id )
{
	// add new subscription
	$subscription_saved = elb_add_subscription( $subscriber_id, $list_id );

	// IF subscription was saved
	if ( $subscription_saved ) {
		// send thank you email
		$email_sent = elb_send_subscriber_email( $subscriber_id, 'subscription_confirmed', $list_id );
		if ($email_sent)
			return true;
	}
	return false;
}

// 5.8
// hint: creates custom tables for our plugin
function elb_create_plugin_tables()
{
	global $wpdb;

	// setup return value
	$return_value = false;

	try {

		$table_name = $wpdb->prefix . "elb_reward_links";
		$charset_collate = $wpdb->get_charset_collate();

		// sql for our table creation
		$sql = "CREATE TABLE $table_name (
			id mediumint(11) NOT NULL AUTO_INCREMENT,
			uid varchar(128) NOT NULL,
			subscriber_id mediumint(11) NOT NULL,
			list_id mediumint(11) NOT NULL,
			attachment_id mediumint(11) NOT NULL,
			downloads mediumint(11) DEFAULT 0 NOT NULL ,
			UNIQUE KEY id (id)
			) $charset_collate;";

		// make sure we include wordpress functions for dbDelta
		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

		// dbDelta will create a new table if none exists or update an existing one
		dbDelta($sql);

		// return true
		$return_value = true;

	} catch( Exception $exception ) {

		// php error

	}

	// return result
	return $return_value;

}

// 5.9
function elb_activate_plugin()
{
	// setup DB table
	elb_create_plugin_tables();
}

// 5.10
// hint: adds new reward links to the database
function elb_add_reward_link( $uid, $subscriber_id, $list_id, $attachment_id )
{
	global $wpdb;

	try {

		$table_name = $wpdb->prefix . 'elb_reward_links';

		$wpdb->insert(
			$table_name,
			[
				'uid'           => $uid,
				'subscriber_id' => $subscriber_id,
				'list_id'       => $list_id,
				'attachment_id' => $attachment_id
			],
			// type
			[ '%s', '%d', '%d', '%d' ]
		);

		return true;

	} catch (Exception $exception) {
		return false;
	}

}


// 5.11
// hint: triggers a download of the reward file
function elb_trigger_reward_download()
{
	global $post;

	if ($post->ID == elb_get_option('elb_reward_page_id') && isset($_GET['reward'])) {

		$uid = (string)$_GET['reward'];

		$reward = elb_get_reward($uid);

		if ($reward && $reward['downloads'] < elb_get_option( 'elb_download_limit')) {

			elb_update_reward_links_downloads($uid);

			// get the reward mimetype
			$mimetype = $reward['file']['mime_type'];
			// extract the filetype from the mimetype
			$mimetype_array = explode('/', $mimetype);
			$filetype = $mimetype_array[1];

			// setup file headers
			header("Content-type: ".$mimetype,true,200);
			header("Content-Disposition: attachment; filename=".$reward['title'] .'.'. $filetype);
			header("Pragma: no-cache");
			header("Expires: 0");
			readfile($reward['file']['url']);
			exit();

		}
	}
}

// 5.12
// hint: increases reward link download count by one
function elb_update_reward_link_downloads( $uid )
{
	global $wpdb;

	try {
		$table_name = $wpdb->prefix . 'elb_reward_links';

		$downloads = $wpdb->get_var(
			$wpdb->prepare("
				SELECT downloads
				FROM $table_name
				WHERE uid = %s
			", $uid)
		);

		$downloads += 1;

		$wpdb->query(
			$wpdb->prepare("
				UPDATE $table_name
				SET downloads = $downloads
				WHERE uid = %s
			", $uid)
		);

	} catch (Exception $exception) {
		return false;
	}
	return true;

}

// 5.13
// hint: generates a .csv file of subscribers data
// expects $_GET['list_id'] as URL's query
function elb_download_subscribers_csv()
{
	$list_id = (isset($_GET['list_id'])) ? (int)$_GET['list_id'] : 0;

	// get array of subscriber's ids of the list
	$subscriber_ids = elb_get_list_subscribers($list_id);

	if (!$subscriber_ids)
		return false;


	// get current time
	try {
		$now = new DateTime();
	} catch (Exception $exception) {
		echo $exception->getMessage();
		return false;
	}

	// generate filename for exporting file
	$csv_filename = 'email-list-builder-export-list_id-'. $list_id .'-date-'. $now->format('d-m-Y'). '.csv';
	$fullpath_csv_filename = plugin_dir_path(__FILE__) . 'exports/' . $csv_filename;

	// open new file in write mode
	$csv_file = fopen($fullpath_csv_filename, 'w');

	/*Set up csv headers*/
	// by any subscriber data
	$subscriber_data = elb_get_subscriber_data($subscriber_ids[0]);

	// remove name and subscriptions field
	unset($subscriber_data['name']);
	unset($subscriber_data['subscriptions']);

	$csv_headers = [];
	foreach ($subscriber_data as $header => $field)
		$csv_headers []= $header;

	// write csv_header on csv file
	fputcsv($csv_file, $csv_headers);

	/*Set up csv fields*/
	foreach ($subscriber_ids as $subscriber_id) {
		$subscriber_data = elb_get_subscriber_data($subscriber_id);
		unset($subscriber_data['name']);
		unset($subscriber_data['subscriptions']);

		fputcsv($csv_file, $subscriber_data);
	}

	// open new file in read mode
	$csv_file = fopen($fullpath_csv_filename, 'r');

	// read csv file for echoing
	$csv_data = fread($csv_file, filesize($fullpath_csv_filename));

	fclose($csv_file);

	// setup file headers
	header("Content-type: application/csv");
	header("Content-Disposition: attachment; filename=". $csv_filename);
	// echo the contents of our file and return it to the browser
	echo($csv_data);
	// exit php processes
	exit;

}

// 5.14
// hint: this function retrieves a csv file from the server and parses the data into a php array
// it then returns that array in a json formatted object
// this function is a ajax post form handler
// expects: $_POST['elb_import_file_id']
function elb_parse_import_csv()
{
	$result = [
		'status'  => 0,
		'message' => 'Could not parse import CSV. ',
		'error'   => '',
		'data'    => []
	];

	try {
		// get file id from ajax post
		$attachment_id = (isset($_POST['elb_import_file_id'])) ? $_POST['elb_import_file_id'] : 0;

		// get filename
		$filename = get_attached_file($attachment_id);

		if ($filename) {
			// parse csv file into php array
			$csv_data = elb_csv_to_array($filename, ',');

			// IF we were able to parse the file and there's data in it
			if ($csv_data && count($csv_data)) {
				$result = [
					'status'  => 1,
					'message' => 'CSV Import data parsed successfully. ',
					'error'   => '',
					'data'    => $csv_data
				];
			}
		} else {
			$result['error'] = 'The import file does not exist. ';
		}

	} catch (Exception $exception) {
		$result['error'] = 'Unexpected Error occurred. ';
	}

	return elb_return_json($result);

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
      	[
	        'key'     => 'elb_email',
	        'value'   => $email,
	        'compare' => '='
        ]
      ]
    ]);

    // if subscriber exists
    if ($subscriber_query->have_posts()) {
      // instantiate post object
	    $subscriber_query->the_post();
	    // get her id
	    $subscriber_id = get_the_ID();
    }

  } catch (Exception $exception ) {

  }

  // reset post instantiated by the_post()
  wp_reset_postdata();
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
  	// subscriber settings
    case 'elb_fname':
      return 'field_59aa6f41ef4d5';
    case 'elb_lname':
      return 'field_59aa6f83ef4d6';
    case 'elb_email':
      return 'field_59aa6f96ef4d7';
    case 'elb_subscriptions':
      return 'field_59aa6fbaef4d8';

	  // list settings
	  case 'elb_enable_reward':
		  return 'field_59b210ccd9a69';
		  break;
	  case 'elb_reward_title':
		  return 'field_59b2116ad9a6a';
		  break;
	  case 'elb_reward_file':
		  return 'field_59b211b6d9a6b';
		  break;
  }
  return $field_name;
}

// 6.6
// hint: returns an array of subscriber data including subscriptions
function elb_get_subscriber_data( $subscriber_id )
{
	// setup subscriber_data
	$subscriber_data = [];

	// get subscriber object
	$subscriber = get_post( $subscriber_id );

	// IF subscriber object is valid
	if( isset($subscriber->post_type) && $subscriber->post_type == 'elb_subscriber' )
	{
		$fname = get_field( elb_get_acf_key( 'elb_fname' ), $subscriber_id );
		$lname = get_field( elb_get_acf_key( 'elb_lname' ), $subscriber_id );

		// build subscriber_data for return
		$subscriber_data = [
			'name'          => $fname . ' ' . $lname,
			'fname'         => $fname,
			'lname'         => $lname,
			'email'         => get_field( elb_get_acf_key( 'elb_email' ), $subscriber_id ),
			'subscriptions' => elb_get_subscriptions( $subscriber_id )
		];
	}
	// return subscriber_data
	return $subscriber_data;

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
	foreach ( $pages as $page ):

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

// 6.8
function elb_get_default_options()
{
	$defaults = [];

	try {
		// get front page id
		$front_page_id = get_option('page_on_front');

		// setup default email footer
		$email_footer = '
			<p>
				Sincerely, <br /><br />
				The '. get_bloginfo('name') .' Team<br />
				<a href="'. get_bloginfo('url') .'">'. get_bloginfo('url') .'</a>
			</p>
		';

		// setup defaults array
		$defaults = [
			'elb_manage_subscription_page_id' => $front_page_id,
			'elb_confirmation_page_id'        => $front_page_id,
			'elb_reward_page_id'              => $front_page_id,
			'elb_email_footer'                => $email_footer,
			'elb_download_limit'              => 3,
		];

	} catch( Exception $exception ) {}

	// return defaults
	return $defaults;

}

// 6.9
function elb_get_option($option_name)
{

	try {

		// get default option values
		$defaults = elb_get_default_options();

		// get the requested option
		switch( $option_name ) {

			// subscription page id
			case 'elb_manage_subscription_page_id':
				return get_option('elb_manage_subscription_page_id') ?
					get_option('elb_manage_subscription_page_id') :
					$defaults['elb_manage_subscription_page_id'];

			// confirmation page id
			case 'elb_confirmation_page_id':
				return get_option('elb_confirmation_page_id') ?
					get_option('elb_confirmation_page_id') :
					$defaults['elb_confirmation_page_id'];

			// reward page id
			case 'elb_reward_page_id':
				return get_option('elb_reward_page_id') ?
					get_option('elb_reward_page_id') :
					$defaults['elb_reward_page_id'];

			// email footer
			case 'elb_email_footer':
				return get_option('elb_email_footer') ?
					get_option('elb_email_footer') :
					$defaults['elb_email_footer'];

			// reward download limit
			case 'elb_download_limit':
				return get_option('elb_download_limit') ?
					(int)get_option('elb_download_limit') :
					$defaults['elb_download_limit'];
		}

	} catch( Exception $exception ) {}

	// default
	return '';

}

// 6.10
function elb_get_current_options()
{
	$current_options = [];

	try {
		$current_options = [
			'elb_manage_subscription_page_id' => elb_get_option('elb_manage_subscription_page_id'),
			'elb_confirmation_page_id'        => elb_get_option('elb_confirmation_page_id'),
			'elb_reward_page_id'              => elb_get_option('elb_reward_page_id'),
			'elb_email_footer'                => elb_get_option('elb_email_footer'),
			'elb_download_limit'              => elb_get_option('elb_download_limit'),
		];

	} catch (Exception $exception) {}

	return $current_options;
}

// 6.11
// hint: generates an html form for managing subscriptions
function elb_get_manage_subscriptions_html( $subscriber_id )
{
	$output = '';

	try {
		// get the subscriber data
		$subscriber_data = elb_get_subscriber_data( $subscriber_id );

		// get subscriptions
		$lists = $subscriber_data['subscriptions'];

		// set the title
		$title = $subscriber_data['fname'] .'\'s Subscriptions';

		// build out output html
		$output = '
			<form id="elb_manage_subscriptions_form" class="elb-form" method="post"
	      action="' . admin_url() . 'admin-ajax.php?action=elb_unsubscribe">
				
				<input type="hidden" name="subscriber_id" value="'. $subscriber_id .'">
				
				<h3 class="elb-title">'. $title .'</h3>';

		if( !count($lists) ):

			$output .='<p>There are no active subscriptions.</p>';

		else:

			$output .= '<table>
						<tbody>';

			// loop over lists
			foreach( $lists as $list_id ):

				$list_object = get_post( $list_id );

				$output .= '<tr>
								<td>'.
				           $list_object->post_title
				           .'</td>
								<td>
									<label>
										<input 
											type="checkbox" name="list_ids[]" 
											value="'. $list_object->ID .'" 
										/> UNSUBSCRIBE
									</label>
								</td>
							</tr>';

			endforeach;

			// close up our output html
			$output .='</tbody>
					</table>
					
					<p><input type="submit" value="Save Changes" /></p>';

		endif;

		$output .='
				</form>
			';

	} catch( Exception $exception ) {}

	// return output
	return $output;

}

// 6.12
// hint: returns an array of email template data IF the template exists
function elb_get_email_template( $subscriber_id, $email_template_name, $list_id )
{
	// setup return variable
	$selected_email_template = [];

	// create new array to store email templates
	$email_templates = [];

	// get list object
	$list = get_post( $list_id );

	// get subscriber object
	$subscriber = get_post( $subscriber_id );

	if( !elb_validate_list( $list ) || !elb_validate_subscriber( $subscriber ) ):

		// the list or the subscriber is not valid

	else:

		// get subscriber data
		$subscriber_data = elb_get_subscriber_data( $subscriber_id );

		// get unique manage subscription link
		$manage_subscriptions_link = elb_get_manage_subscriptions_link( $subscriber_data['email'], $list_id );

		// get default email header
		$default_email_header = '
			<p>
				Hello, '. $subscriber_data['fname'] .'
			</p>
		';

		// get default email footer
		$default_email_footer = elb_get_option('elb_email_footer');

		// setup unsubscribe text
		$unsubscribe_text = '
			<br /><br />
			<hr />
			<p><a href="'. $manage_subscriptions_link .'">Click here to unsubscribe</a> from this or any other email list.</p>';



		// setup email templates

		// get unique opt-in link
		$optin_link = elb_get_optin_link( $subscriber_data['email'], $list_id );

		// reward
		$reward = elb_get_list_reward($list_id);

		// assign reward text if there is reward
		if ($reward) {
			switch ($email_template_name) {
				case 'new_subscription':
					$reward_text = '<p>After confirming your subscription, we will send you a link for a FREE DOWNLOAD of '. $reward['title'] .'</p>';
					break;
				case 'subscription_confirmed':
					$download_limit = elb_get_option('elb_download_limit');
					$download_link = elb_get_reward_link($subscriber_id, $list_id);
					$reward_text = '<p>Here is your <a href="'. $download_link .'">UNIQUE DOWNLOAD LINK</a> for '. $reward['title'] .'. This link will expire after '. $download_limit .' downloads</p>';
					break;
				default:
					$reward_text = '';
					break;
			}
		}

		// template: new_subscription
		$email_templates['new_subscription'] = [
			'subject' => 'Thank you for subscribing to '. $list->post_title .'! Please confirm your subscription.',
			'body' => '
					'. $default_email_header .'
					<p>Thank you for subscribing to '. $list->post_title .'!</p>
					<p>Please <a href="'. $optin_link .'">click here to confirm your subscription.</a></p>
					'. $reward_text . $default_email_footer . $unsubscribe_text,
		];

		// template: subscription_confirmed
		$email_templates['subscription_confirmed'] = [
			'subject' => 'You are now subscribed to '. $list->post_title .'!',
			'body' => '
					'. $default_email_header .'
					<p>Thank you for confirming your subscription. You are now subscribed to '. $list->post_title .'!</p>
					'. $reward_text . $default_email_footer . $unsubscribe_text,
		];

	endif;

	// IF the requested email template exists
	if( isset( $email_templates[ $email_template_name ] ) ):

		// add template data to return variable
		$selected_email_template = $email_templates[ $email_template_name ];

	endif;

	// return template data
	return $selected_email_template;

}

// 6.13
// hint: validates whether the post object exists and that it's a validate post_type
function elb_validate_list( $list_object )
{
	if( isset($list_object->post_type) && $list_object->post_type == 'elb_list' )
		return true;

	return false;
}

// 6.14
// hint: validates whether the post object exists and that it's a validate post_type
function elb_validate_subscriber( $subscriber_object )
{
	if( isset($subscriber_object->post_type) && $subscriber_object->post_type == 'elb_subscriber' )
		return true;

	return false;
}

// 6.15
// hint: returns a unique link for managing a particular users subscriptions
function elb_get_manage_subscriptions_link( $email, $list_id = 0 )
{
	$link_href = '';

	try {

		$page = get_post( elb_get_option('elb_manage_subscription_page_id') );

		$permalink = get_permalink($page);

		// get character to start querystring
			// i.e determined by how client set the url
			// e.g if abc.com/xyz, then append ?query=blabla
			//  else abc.com/?page=xyz then append &query=blabla
		$startquery = elb_get_querystring_start( $permalink );

		$link_href = $permalink . $startquery .'email='. urlencode($email) .'&list='. $list_id;

	} catch( Exception $exception ) {

		//$link_href = $e->getMessage();

	}

	return esc_url($link_href);

}

// 6.16
// hint: returns the appropriate character for the begining of a querystring
function elb_get_querystring_start( $permalink ) {

	// setup our default return variable
	$querystring_start = '&';

	// IF ? is not found in the permalink
	if( false === strpos($permalink, '?') ):
		$querystring_start = '?';
	endif;

	return $querystring_start;

}

// 6.17
// hint: returns a unique link for opting into an email list
function elb_get_optin_link( $email, $list_id = 0 ) {

	$link_href = '';

	try {

		$page = get_post( elb_get_option('elb_confirmation_page_id') );

		$permalink = get_permalink($page);

		// get character to start querystring
		$startquery = elb_get_querystring_start( $permalink );

		$link_href = $permalink . $startquery .'email='. urlencode($email) .'&list='. $list_id;

	} catch( Exception $exception ) {

		//$link_href = $e->getMessage();

	}

	return esc_url($link_href);

}

// 6.18
// hint: returns html for messags
function elb_get_message_html( $message, $message_type ) {

	$output = '';

	try {

		switch( $message_type ) {
			case 'warning':
				$message_class = 'elb-warning';
				break;
			case 'error':
				$message_class = 'elb-error';
				break;
			default:
				$message_class = 'elb-confirmation';
				break;
		}

		$output .= '
			<div class="elb-message-container">
				<div class="elb-message '. $message_class .'">
					<p>'. $message .'</p>
				</div>
			</div>
		';

	} catch( Exception $exception ) {

	}

	return $output;

}


// 6.19
// hint: returns false if list has no reward or returns the object containing file and title if it does
function elb_get_list_reward( $list_id ) {

	// setup return data
	$reward_data = [];

	// get enable_reward value
	$enable_reward = ( get_field( elb_get_acf_key('elb_enable_reward'), $list_id) ) ? true : false;

	// IF reward is enabled for this list
	if( $enable_reward ) {

		// get reward file
		$reward_file = ( get_field( elb_get_acf_key( 'elb_reward_file' ), $list_id ) ) ?
										get_field( elb_get_acf_key( 'elb_reward_file' ), $list_id ) :
										false;
		// get reward title
		$reward_title = ( get_field( elb_get_acf_key( 'elb_reward_title' ), $list_id ) ) ?
										get_field( elb_get_acf_key( 'elb_reward_title' ), $list_id ) :
										'Reward';


		// IF reward_file is a valid array
		if ( is_array( $reward_file ) ) {


			// setup return data
			$reward_data = [
				'file'  => $reward_file,
				'title' => $reward_title,
			];

		}
	}
	// return $reward_data
	return $reward_data;

}

// 6.20
// hint: returns a unique link for downloading a reward file
function elb_get_reward_link( $subscriber_id, $list_id )
{
	$link_href = '';

	try {

		$page = get_post(elb_get_option('elb_reward_page_id'));
		$permalink = get_permalink($page);

		$uid = elb_generate_reward_uid($subscriber_id, $list_id);

		$reward = elb_get_list_reward( $list_id );

		if ($uid && $reward) {
			$link_added = elb_add_reward_link($uid, $subscriber_id, $list_id, $reward['file']['id']);

			if ($link_added) {
				$startquery = elb_get_querystring_start($permalink);
				$link_href = $permalink . $startquery .'reward='. urlencode($uid);
			}
		}

	} catch (Exception $exception) {}

	return esc_url($link_href);
}

// 6.21
// hint: generates a unique
function elb_generate_reward_uid( $subscriber_id, $list_id )
{
	$uid = '';

	$subscriber = get_post($subscriber_id);
	$list = get_post($list_id);

	if( elb_validate_subscriber( $subscriber ) && elb_validate_list( $list ) ) {
		$reward = elb_get_list_reward($list_id);
		if ($reward) {
			$uid = uniqid('elb', true);
		}
	}

	return $uid;
}

// 6.22
// hint: returns false if list has no reward or returns the object containing file and title if it does
// get reward of list + download times
function elb_get_reward( $uid )
{
	$reward_data = false;

	global $wpdb;

	try {

		$table_name = $wpdb->prefix . "elb_reward_links";

		// get list id
		$list_id = $wpdb->get_var(
			$wpdb->prepare("
				SELECT list_id 
				FROM $table_name 
				WHERE uid = %s
			", $uid)
		);

		// get number of download times
		$downloads = $wpdb->get_var(
			$wpdb->prepare("
				SELECT downloads 
				FROM $table_name 
				WHERE uid = %s
			", $uid)
		);

		$reward = elb_get_list_reward($list_id);

		if ($reward) {
			$reward_data = $reward;
			$reward_data['downloads'] = $downloads;
		}
	} catch (Exception $exception) {}

	return $reward_data;
}

// 6.23
// hint: returns an array of subscriber_id's
function elb_get_list_subscribers( $list_id = 0 )
{
	$list = get_post($list_id);

	try {
		// if no argument is pass, return all subscribers from all lists
		if ( $list_id === 0 ) {
			$subscribers_query = new WP_Query( [
				'post_type'      => 'elb_subscriber',
				'published'      => true,
				// get all
				'posts_per_page' => -1,
				'orderby'        => 'post_date',
				'order'          => 'DESC',
			] );
		} // there is specific list_id as argument, return only subscribers from that list
		else if (elb_validate_list($list)) {
			$subscribers_query = new WP_Query( [
				'post_type'      => 'elb_subscriber',
				'published'      => true,
				'posts_per_page' => -1,
				'orderby'        => 'post_date',
				'order'          => 'DESC',
				'status'         => 'publish',
				'meta_query'     => [
					[
						'key'     => 'elb_subscriptions',
						'value'   => ':"' . $list->ID . '"',
						'compare' => 'LIKE'
					]
				]
			] );
		}
	} catch (Exception $exception) {
		return false;
	}

	$subscriber_ids = [];


	if (isset($subscribers_query) && $subscribers_query->have_posts()) {
		// avoid the_posts() which resulting peculiar result when wp_reset_postdata()
		$subscribers = $subscribers_query->posts;

		foreach ($subscribers as $subscriber) {
			$subscriber_ids []= $subscriber->ID;
		}
	}

	wp_reset_postdata();
	return $subscriber_ids;

}

// 6.24
function elb_get_list_subscriber_count($list_id)
{
	return count(elb_get_list_subscribers($list_id));
}

// 6.25
function elb_get_csv_export_link($list_id = 0)
{
	return esc_url(admin_url() . 'admin-ajax.php?action=elb_download_subscribers_csv&list_id=' . $list_id);
}

// 6.26
// hint: this function reads a csv file and converts the contents into a php array
function elb_csv_to_array($filename = '', $delimiter = ',')
{

	// this is an important setting!
	ini_set('auto_detect_line_endings', true);

	// IF the file doesn't exist or the file is not readable return false
	if(!file_exists($filename) || !is_readable($filename))
		return FALSE;

	// setup our return data
	$return_data = [];

	// IF we can open and read the file
	if (($handle = fopen($filename, 'r')) !== FALSE) {

		$row = 0;

		$headers = [];
		// while data exists loop over data
		while (($data = fgetcsv($handle, 1000, $delimiter)) !== FALSE) {
			// count the number of items in this data
			$num = count($data);
			// increment our row variable
			$row++;
			// loop over all items and append them to our row data
			for ($c=0; $c < $num; $c++) {
				// if this is the first row set it up as our header
				if( $row == 1):
					$headers []= $data[$c];
				else:
					// all rows greater than 1
					// add row data item
					$return_data[$row-2][$headers[$c]] = $data[$c];
				endif;
			}
		}

		// close our file
		fclose($handle);
	}

	// return the new data as a php array
	return $return_data;
}


	/* !7. CUSTOM POST TYPES */
// 7.1 - subscribers
include_once( plugin_dir_path( __FILE__ ) . 'cpt/elb_subscriber.php');

// 7.2 - lists
include_once( plugin_dir_path( __FILE__ ) . 'cpt/elb_list.php');


/* !8. ADMIN PAGES */

// 8.1
// hint: dashboard admin page
function elb_dashboard_admin_page()
{
	$export_href = elb_get_csv_export_link();

	$output = '
		<div class="wrap">
			
			<h2>Email List Builder</h2>
			
			<p>The ultimate email list building plugin for WordPress. Capture new subscribers. Reward subscribers with a custom download upon opt-in. Build unlimited lists. Import and export subscribers easily with .csv</p>
		
			<p><a href="'. $export_href .'"  class="button button-primary">Export All Subscriber Data</a></p>

		</div>
	';

	echo $output;

}

// 8.2
// hint: import subscribers admin page
function elb_import_admin_page()
{
	// enque special scripts required for our file import field
	wp_enqueue_media();

	echo('
	
	<div class="wrap" id="import_subscribers">
			
			<h2>Import Subscribers</h2>
						
			<form id="import_form_1">
			
				<table class="form-table">
				
					<tbody>
				
						<tr>
							<th scope="row"><label for="elb_import_file">Import CSV</label></th>
							<td>
								
								<div class="wp-uploader">
								    <input type="text" name="elb_import_file_url" class="file-url regular-text" accept="csv">
								    <input type="hidden" name="elb_import_file_id" class="file-id" value="0" />
								    <input type="button" name="upload-btn" class="upload-btn button-secondary" value="Upload">
								</div>
								
								
								<p class="description" id="elb_import_file-description">This is the page where Snappy List Builder will send subscribers to manage their subscriptions. <br />
									IMPORTANT: In order to work, the page you select must contain the shortcode: <strong>[elb_manage_subscriptions]</strong>.</p>
							</td>
						</tr>
						
					</tbody>
					
				</table>
				
			</form>
			
			<form id="import_form_2">
				
				<table class="form-table">
				
					<tbody class="elb-dynamic-content">
						
					</tbody>
					
					<tbody class="form-table show-only-on-valid" style="display: none">
						
						<tr>
							<th scope="row"><label>Import To List</label></th>
							<td>
								<select name="elb_import_list_id">');


	// get all our email lists
	$lists = get_posts(
		array(
			'post_type'			  =>'elb_list',
			'status'			    =>'publish',
			'posts_per_page'  => -1,
			'orderby'         => 'post_title',
			'order'           => 'ASC',
		)
	);

	// loop over each email list
	foreach( $lists as &$list ):

		// create the select option for that list
		$option = '
												<option value="'. $list->ID .'">
													'. $list->post_title .'
												</option>';

		// echo the new option
		echo $option;


	endforeach;

	echo('</select>
								<p class="description"></p>
							</td>
						</tr>
						
					</tbody>
					
				</table>
				
				<p class="submit show-only-on-valid" style="display:none"><input type="submit" name="submit" id="submit" class="button button-primary" value="Import"></p>
				
			</form>
			
	</div>
	
	');
}

// 8.3
// hint: plugin options admin page
function elb_options_admin_page()
{
	// get the default values for our options
	$options = elb_get_current_options();

	echo('<div class="wrap">
		
		<h2>Email List Builder Options</h2>
		
		<form action="options.php" method="post">');

			// outputs a unique nounce for our plugin options
			settings_fields('elb_plugin_options');
			// generates a unique hidden field with our form handling url
			@do_settings_fields('elb_plugin_options');

			echo('<table class="form-table">
			
				<tbody>
			
					<tr>
						<th scope="row"><label for="elb_manage_subscription_page_id">Manage Subscriptions Page</label></th>
						<td>
							'. elb_get_page_select( 'elb_manage_subscription_page_id', 'elb_manage_subscription_page_id', 0, 'id', $options['elb_manage_subscription_page_id'] ) .'
							<p class="description" id="elb_manage_subscription_page_id-description">This is the page where Email List Builder will send subscribers to manage their subscriptions. <br />
								IMPORTANT: In order to work, the page you select must contain the shortcode: <strong>[elb_manage_subscriptions]</strong>.</p>
						</td>
					</tr>
					
			
					<tr>
						<th scope="row"><label for="elb_confirmation_page_id">Opt-In Page</label></th>
						<td>
							'. elb_get_page_select( 'elb_confirmation_page_id', 'elb_confirmation_page_id', 0, 'id', $options['elb_confirmation_page_id'] ) .'
							<p class="description" id="elb_confirmation_page_id-description">This is the page where Email List Builder will send subscribers to confirm their subscriptions. <br />
								IMPORTANT: In order to work, the page you select must contain the shortcode: <strong>[elb_confirm_subscription]</strong>.</p>
						</td>
					</tr>
					
			
					<tr>
						<th scope="row"><label for="elb_reward_page_id">Download Reward Page</label></th>
						<td>
							'. elb_get_page_select( 'elb_reward_page_id', 'elb_reward_page_id', 0, 'id', $options['elb_reward_page_id'] ) .'
							<p class="description" id="elb_reward_page_id-description">This is the page where Email List Builder will send subscribers to retrieve their reward downloads. <br />
								IMPORTANT: In order to work, the page you select must contain the shortcode: <strong>[elb_download_reward]</strong>.</p>
						</td>
					</tr>
			
					<tr>
						<th scope="row"><label for="elb_email_footer">Email Footer</label></th>
						<td>');


							// wp_editor will act funny if it's stored in a string so we run it like this...
							wp_editor( $options['elb_email_footer'], 'elb_email_footer', [ 'textarea_rows' => 8 ] );


							echo('<p class="description" id="elb_email_footer-description">The default text that appears at the end of emails generated by this plugin.</p>
						</td>
					</tr>
			
					<tr>
						<th scope="row"><label for="elb_download_limit">Reward Download Limit</label></th>
						<td>
							<input type="number" name="elb_download_limit" value="'. $options['elb_download_limit'] .'" class="" />
							<p class="description" id="elb_download_limit-description">The amount of downloads a reward link will allow before expiring.</p>
						</td>
					</tr>
			
				</tbody>
				
			</table>');

	// outputs the WP submit button html
	@submit_button();


	echo('</form>
	
	</div>');

}



/* !9. SETTINGS */
function elb_register_options()
{
	register_setting('elb_plugin_options', 'elb_manage_subscription_page_id');
	register_setting('elb_plugin_options', 'elb_confirmation_page_id');
	register_setting('elb_plugin_options', 'elb_reward_page_id');
	register_setting('elb_plugin_options', 'elb_email_footer');
	register_setting('elb_plugin_options', 'elb_download_limit');
}



/* !10. MISCELLANEOUS */




