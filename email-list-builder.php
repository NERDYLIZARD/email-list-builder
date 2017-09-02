<?php
/*
 * Plugin Name: Email List Builder
 * Text Domain: email-list-builder
*/


/* !0. TABLE OF CONTENTS */

/*

	1. HOOKS
		1.1 - registers all our custom shortcodes

	2. SHORTCODES
		2.1 - elb_register_shortcodes()
		2.2 - elb_form_shortcode()

	3. FILTERS

	4. EXTERNAL SCRIPTS

	5. ACTIONS

	6. HELPERS

	7. CUSTOM POST TYPES

	8. ADMIN PAGES

	9. SETTINGS

	10. MISCELLANEOUS

*/




/* !1. HOOKS */

// 1.1 - register all custom shortcodes
add_action('init', 'elb_register_shortcodes');



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




/* !4. EXTERNAL SCRIPTS */




/* !5. ACTIONS */




/* !6. HELPERS */




/* !7. CUSTOM POST TYPES */




/* !8. ADMIN PAGES */




/* !9. SETTINGS */




/* !10. MISCELLANEOUS */



