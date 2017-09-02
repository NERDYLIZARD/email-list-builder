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

	6. HELPERS

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


/* !2. SHORTCODES */

// 2.1 - register all shortcodes
function elb_register_shortcodes()
{
  add_shortcode('elb_form', 'elb_form_shortcode');
}

// 2.2 - return html subscription form
function elb_form_shortcode($args, $content = '')
{
  // html form
  $output = '
    <div class="elb">
		
			<form id="elb_form" name="elb_form" class="elb-form" method="post">
			
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




/* !6. HELPERS */




/* !7. CUSTOM POST TYPES */




/* !8. ADMIN PAGES */




/* !9. SETTINGS */




/* !10. MISCELLANEOUS */



