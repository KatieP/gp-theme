<?php

/*
 * TIPS:
 * http://wordpress.stackexchange.com/questions/1567/best-collection-of-code-for-your-functions-php-file
 * 
 * REMINDER:
 * __('string') = returns a language translation of 'string'.
 * _e('string') = echos a language translation of 'string'.
 * 
 */

/*
 * This is doesn't work. Puts header in header tag!
add_action( 'admin_head', 'gp_add_admin_header' );
function gp_add_admin_header() {
	include("header.php");
}
*/

add_filter( 'admin_footer_text', 'gp_add_admin_footer' );
function gp_add_admin_footer() {
	echo 'Welcome to the Green Pages backend editor! Go back to <a href="http://www.thegreenpages.com.au/">front end</a>';
}

add_filter( 'update_footer', 'gp_remove_version_footer', 9999);
function gp_remove_version_footer() {return '&nbsp;';}

add_filter('wp_mail_from','yoursite_wp_mail_from');
function yoursite_wp_mail_from($content_type) {
	return 'wordpress@thegreenpages.com.au';
}

add_filter('wp_mail_from_name','yoursite_wp_mail_from_name');
function yoursite_wp_mail_from_name($name) {
	return 'WordPress';
}


function cm_subscribe($subscribe = '') {
	if ($subscribe == 'true' || $subscribe = 'false') {
		if (is_user_logged_in()) {
			
			#$current_user = wp_get_current_user();
			global $current_user;
			
			require_once 'template/createsend-api/csrest_subscribers.php';
			
			$wrap = new CS_REST_Subscribers('6f745fb4dad5ab592b5bac0f23d9e826', 'fd592f119aba9e1a50c9c7f09119e0ff');
	
			if ($subscribe == 'true') {
				$result = $wrap->update($current_user->user_email, array(
					'EmailAddress' => $current_user->user_email,
				    'Name' => $current_user->display_name,
				    'CustomFields' => array(
				        array(
				            'Key' => 'Wordpress-id',
				            'Value' => $current_user->ID
				        ),
				        array(
				            'Key' => 'postcode',
				            'Value' => $current_user->locale_postcode
				        )
				    ),
				    'Resubscribe' => true
				));
			}
			if ($subscribe == 'false') {
				$result = $wrap->update($current_user->user_email, array(
					'EmailAddress' => $current_user->user_email,
				    'Name' => $current_user->display_name,
				    'CustomFields' => array(
				        array(
				            'Key' => 'Wordpress-id',
				            'Value' => $current_user->ID
				        ),
				        array(
				            'Key' => 'postcode',
				            'Value' => $current_user->locale_postcode
				        )
				    ),
				    'Resubscribe' => false
				));
			}
			
			if($result->was_successful()) {
				return true;
			} else {
				return false;
			}
			/*** SHOULD ADD SOMETHING HERE TO MONITOR SUCCESSFUL OR UNSUCCESSFUL UPDATE RESULT ***/
		}
	}
}

function cm_update_current_user() {
	/*
     * Campaign Monitor
     * 
     * We are going to check if logged in user is subscribed to the mail list or not.
     * 
     * We must check the results against Wordpress $current_user->subscription["subscription-greenrazor"] value. If $subscriberGreenRazor = true then $current_user->subscription["subscription-greenrazor"] must be updated to true as well.
     * 
     * true = don't show subscribe dialog
     * false = do show subscribe dialog
     * 
     * Note1: This isn't the best way to do this. Ideally Create/Send should send the results itself. In this case there us a margin of error - if the user never visits the site or their profile update page then the value of $current_user->subscription["subscription-greenrazor"] may be incorrect.
     * Note2: There is a timing issue with this - any updates to $current_user->subscription["subscription-greenrazor"] do not take effect until next time the user visits a page.
	 */
	
	#$current_user = wp_get_current_user(); #is global variable?! should change this everywhere.
	global $current_user;
	
	$subscriberGreenRazor = false;
	if (is_user_logged_in()) {
        require_once 'template/createsend-api/csrest_subscribers.php';
        	
        $wrap = new CS_REST_Subscribers('6f745fb4dad5ab592b5bac0f23d9e826', 'fd592f119aba9e1a50c9c7f09119e0ff');
		$result = $wrap->get($current_user->user_email);

		if($result->was_successful()) {
			#var_dump($result->response);
			$subscriberGreenRazor = true;
		}
	}
        
	$subscription_post = $current_user->subscription;
	
	if ($current_user->subscription["subscription-greenrazor"] != "true" && $subscriberGreenRazor == true) {
		if (is_array($subscription_post)) {
			if (array_key_exists('subscription-greenrazor', $subscription_post)) {
				$subscription_post['subscription-greenrazor']='true';
			} else {
				$subscription_post = $subscription_post + array('subscription-greenrazor'=>'true');
			}
		} else {
			$subscription_post = array('subscription-greenrazor'=>'true');
		}
		update_usermeta($current_user->ID, 'subscription', $subscription_post );
	}
        
	if ($current_user->subscription["subscription-greenrazor"] == "true" && $subscriberGreenRazor == false) {
		if (is_array($subscription_post)) {
			if (array_key_exists('subscription-greenrazor', $subscription_post)) {
				$subscription_post['subscription-greenrazor']='false';
			} else {
				$subscription_post = $subscription_post + array('subscription-greenrazor'=>'false');
			}
		} else {
			$subscription_post = array('subscription-greenrazor'=>'false');
		}
        update_usermeta($current_user->ID, 'subscription', $subscription_post );
    	
	}
}

function add_jquery_data() { 
	global $current_user;
	if ( parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH) == "/wp-admin/profile.php" || parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH) == "/wp-admin/user-edit.php" ) {
		if ( parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH) == "/wp-admin/profile.php" ) { ?>
			<script type="text/javascript">
			$(document).ready(function(){
				$("#message.updated > p:first").html('<strong>Profile updated</strong>. <a href="<?php echo get_author_posts_url($current_user->ID) ?>">View your public profile now.</a>'); // profile options
				$("form#your-profile > h3:first").before('<div class="view-profile"><a href="<?php echo get_author_posts_url($current_user->ID) ?>">< View your public profile</a></div>');
				$("div#profile-page > h2:first").after('<div id="message" class="notice"><p>We\'ve only just added this feature so it\'s a work in progress. There will be functionality added in the near future!</p></div>');
			});
			</script>
			<style>
				/* View profile box */
				.view-profile {
					position: absolute;
					top: 460px;
					right: 10%;
					left: 900px;
					text-align: center;
					font-weight: bold;
					padding: 10px;
					width: 170px;
					background-color: #eeeeee;
					border-radius: 6px;
					font-size: 11px;
				}
				.view-profile a {
					text-decoration: none;
				}
				#message.notice {
					border: 1px solid  #5aa666;
					padding: 0px 0.6em;
					border-radius: 3px;
					background-color: #d0fbd7;
					height: 34px;
					overflow: hidden;
				}
				#message.notice p {
					margin-top: 9px;
				}
			</style>
		<?php }
		if( !current_user_can('edit_others_posts') ) { // we want admins and editors to still see it ?>
	<script type="text/javascript">
		$(document).ready(function(){
			$("form#your-profile > h3:first").hide(); // profile options
			$("form#your-profile > table:first").hide(); // profile options
			$("table.form-table:eq(1) tr:eq(3)").hide(); // nickname
		});
	</script>
		<?php } ?>
	<script type="text/javascript">
		var info;
		$(document).ready(function(){
			var descriptionOptions = {
					'countWhat': 'characters',
					'maxCharacterSize': 500,
					'originalStyle': 'originalTextareaInfo',
					'warningStyle' : 'warningTextareaInfo',
					'warningNumber': 40,
					'displayFormat' : '#input Characters / #max Characters Left | #words Words'
			};
			$('#description').textareaCount(descriptionOptions);
		
			var biochangeOptions = {
					'countWhat': 'words',
					'maxCharacterSize': 50,
					'originalStyle': 'originalTextareaInfo',
					'warningStyle' : 'warningTextareaInfo',
					'warningNumber': 40,
					'displayFormat' : '#words Words / #max Words Left | #input Characters'
			};
			$('#bio_change').textareaCount(biochangeOptions);
			
			var bioprojectsOptions = {
					'countWhat': 'characters',
					'maxCharacterSize': 500,
					'originalStyle': 'originalTextareaInfo',
					'warningStyle' : 'warningTextareaInfo',
					'warningNumber': 40,
					'displayFormat' : '#input Characters / #max Characters Left | #words Words'
			};
			$('#bio_projects').textareaCount(bioprojectsOptions);
			
			var biostuffOptions = {
					'countWhat': 'characters',
					'maxCharacterSize': 500,
					'originalStyle': 'originalTextareaInfo',
					'warningStyle' : 'warningTextareaInfo',
					'warningNumber': 40,
					'displayFormat' : '#input Characters / #max Characters Left | #words Words'
			};
			$('#bio_stuff').textareaCount(biostuffOptions);
		
			var editorblurbOptions = {
					'countWhat': 'characters',
					'maxCharacterSize': 1000,
					'originalStyle': 'originalTextareaInfo',
					'warningStyle' : 'warningTextareaInfo',
					'warningNumber': 40,
					'displayFormat' : '#input Characters / #max Characters Left | #words Words'
			};
			$('#editors_blurb').textareaCount(editorblurbOptions);

			var contributorsblurbOptions = {
					'countWhat': 'characters',
					'maxCharacterSize': 1000,
					'originalStyle': 'originalTextareaInfo',
					'warningStyle' : 'warningTextareaInfo',
					'warningNumber': 40,
					'displayFormat' : '#input Characters / #max Characters Left | #words Words'
			};
			$('#contributors_blurb').textareaCount(contributorsblurbOptions);
		});	
	</script>
	<?php } 
	if (get_post_type() == 'gp_events' || get_post_type() == 'gp_competitions') { ?>
	<script type="text/javascript">
		jQuery(document).ready(function($) {
			$(".tfdate").datepicker({
			    dateFormat: 'D, M d, yy',
			    showOn: 'button',
			    buttonImage: '/yourpath/icon-datepicker.png',
			    buttonImageOnly: true,
			    numberOfMonths: 3,

			});
		});		
	</script>

	<style>
		.charleft {text-align: right;color: #666;}
		
		/* Columns CPT Events */
		th#gp_col_ev_date, th#gp_col_ev_cat {width:150px}
		td.gp_col_ev_date em{color:gray;}
		th#gp_col_ev_times{width:150px}
		th#gp_col_ev_thumb{width:100px}
		
		th#gp_col_menu_order{width:60px}
		td.gp_col_menu_id{color:gray;text-align:right;}
		td.gp_col_menu_order{color:#219b38;text-align:right;}
		th#gp_col_menu_thumb{width:100px}
		td.gp_col_menu_thumb img{border:1px solid #d8d8d8}
		th#gp_col_menu_cat,th#gp_col_menu_title,th#gp_col_menu_size{width:150px}
		
		/* Metabox */
		.tf-meta {  }
		.tf-meta ul li { height: 20px; clear:both; margin: 0 0 15px 0;}
		.tf-meta ul li label { width: 100px; display:block; float:left; padding-top:4px; }
		.tf-meta ul li input { width:125px; display:block; float:left; }
		.tf-meta ul li em { width: 200px; display:block; float:left; color:gray; margin-left:10px; padding-top: 4px}
	</style>
	<?php }
	if ( get_user_role( array('subscriber', 'contributor', 'author', 'editor') ) ) {
	?>
	<style>
		#screen-meta {display: none;}
		.pgcache_purge {display: none;}
		#add_video, #add_audio, #add_media {display: none;}
		.subsubsub {display: none;}
	</style>
	<?php }
	if ((get_post_type() == 'gp_advertorial' || get_post_type() == 'gp_competitions') && (get_post_status( $ID ) == 'auto-draft' || get_post_status( $ID ) == 'draft')) {
		if ( get_user_role( array('subscriber', 'contributor') ) ) {
	?>
	<script type="text/javascript">
		$(document).ready(function(){
			var label = $('#publish').text(); 
			$('#publish').text('');
			$('#publish').val('Submit & Pay');
			$('#publish').text(label);
		});
	</script>
	<?php } 
	}
}
add_filter('admin_head', 'add_jquery_data');

add_action('wp_head', 'gp_after_scripts');
function gp_after_scripts() {
	if(!is_admin()){
		?>
		<!--[if lte IE 8]>
			<meta http-equiv="X-UA-Compatible" content="chrome=1">
			<script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
		<![endif]-->
		
		<script type="text/javascript">
			/*$(document).ready(function() {
				$('#auth-youraccount').renderDash("#auth-dash-account");
				$('#auth-yourfavourites').renderDash("#auth-dash-favourites");
				$('#auth-yournotifications').renderDash("#auth-dash-notifications");
			 });*/
		</script>
		
		<script type="text/javascript">
			$(document).ready(function() {
				$().piroBox_ext({
				piro_speed : 700,
				bg_alpha : 0.5,
				piro_scroll : true // pirobox always positioned at the center of the page
				});
			});
		</script>
		
		<!--[if lte IE 6]>
			<link type="text/css" rel="stylesheet" media="all" href="<?php echo $template_url; ?>/template/ie6.css" />
		<![endif]-->
		<?php
	}
}

/** REMOVE WORDPRESS (in 3.1+) ADMIN BAR **/
function my_function_admin_bar(){
    return false;
}
add_filter( 'show_admin_bar' , 'my_function_admin_bar');

/** ADD & REMOVE CONTACT METHODS **/
function my_new_contactmethods( $contactmethods ) {
	// Remove
	unset($contactmethods['aim']);
	unset($contactmethods['jabber']);
	unset($contactmethods['yim']);
	
	// Add Facebook
    $contactmethods['facebook'] = 'Facebook URL';
    
    // Add Linkedin
    $contactmethods['linkedin'] = 'Linkedin URL';
    
    // Add Twitter
    $contactmethods['twitter'] = 'Twitter ID';
    
    // Add Skype
    $contactmethods['skype'] = 'Skype ID';
    
    return $contactmethods;
}
add_filter('user_contactmethods','my_new_contactmethods',10,1);

/** ADD REWRITE RULES **/
function change_author_permalinks() {
    global $wp_rewrite;
   	$wp_rewrite->author_base = 'profile'; # see this for changing slug by role - http://wordpress.stackexchange.com/questions/17106/change-author-base-slug-for-different-roles
    #$wp_rewrite->flush_rules();
}
add_action('init','change_author_permalinks');

/** AUTHOR EDIT REWRITE RULE **/
#add_action( 'author_rewrite_rules', 'edit_author_slug' ); #new-edit
function edit_author_slug( $author_rules )
{
    $author_rules['profile/([^/]+)/edit/?$'] = 'index.php?author_name=$matches[1]&author_edit=1';
    $author_rules['profile/([^/]+)/edit/settings?$'] = 'index.php?author_name=$matches[1]&author_edit=2';
    $author_rules['profile/([^/]+)/edit/locale?$'] = 'index.php?author_name=$matches[1]&author_edit=3';
    $author_rules['profile/([^/]+)/edit/notifications?$'] = 'index.php?author_name=$matches[1]&author_edit=4';
    $author_rules['profile/([^/]+)/edit/subscriptions?$'] = 'index.php?author_name=$matches[1]&author_edit=5';
    $author_rules['profile/([^/]+)/edit/privacy?$'] = 'index.php?author_name=$matches[1]&author_edit=6';
    return $author_rules;
}

/** EVENTS FILTER BY STATE REWRITE RULES **/
#add_rewrite_rule('^AU/([^/]*)/?','index.php?p=12&filterby_state=$matches[1]','top');

/** REGISTER CUSTOM QUERY VARS **/
# I'm assuming that query vars are registered in order to ensure rewrite rules work properly - in other words, don't change the order of the query vars.
add_filter( 'query_vars', 'register_query_vars' );
function register_query_vars( $query_vars )
{
	$query_vars[] = 'author_edit';
	$query_vars[] = 'filterby_state';
    return $query_vars;
}

#add_filter( 'author_template', 'edit_author_template' );
#function edit_author_template( $author_template )
#{
    #if ( get_query_var( 'author_edit' ) ) {
        #locate_template( array( 'edit-author.php', $author_template ), true );
    #}
    #return $author_template;
#}

/** ADD CUSTOM REWRITE RULES **/
# see: http://wordpress.stackexchange.com/questions/4127/custom-taxonomy-and-pages-rewrite-slug-conflict-gives-404
function my_rewrite_rules( $wp_rewrite ) {
	$newrules = array();
	$states_au = array('NSW', 'QLD', 'VIC', 'WA', 'SA', 'NT', 'ACT', 'TAS');
	
	$newrules['events/AU/(' . implode($states_au, "|") . ')/page/?([0-9]{1,})?'] = 'index.php?post_type=gp_events&filterby_state=$matches[1]&paged=$matches[2]';
	$newrules['events/AU/(' . implode($states_au, "|") . ')/?'] = 'index.php?post_type=gp_events&filterby_state=$matches[1]';
	$newrules['events/AU/page/?([0-9]{1,})?'] = 'index.php?post_type=gp_events&paged=$matches[1]';
	$newrules['events/AU/?'] = 'index.php?post_type=gp_events';
	#$newrules['sitemaps.xml?'] = 'sitemaps-xml/';
	$wp_rewrite->rules = $newrules+$wp_rewrite->rules;
}
add_filter('generate_rewrite_rules','my_rewrite_rules');

/** SWITCH TEMPLATES **/

function twocolumn_template() {
	if ( get_query_var( 'author_edit' ) ) {
		$template = TEMPLATEPATH . '/twocolumn.php';
		if ( file_exists($template) ) {
			include($template);
		}
	}
}
#add_action('template_redirect', 'twocolumn_template'); #new-edit


/** CHECK USER ROLES **/
function get_user_role($roles_to_check = array('subscriber'), $user_id = 0) {
	global $wp_roles;
	global $current_user;
	$role = False;
	
	if ( !is_array( $roles_to_check ) && !is_object($roles_to_check) ) {
		$roles_to_check = array($roles_to_check);
	}
	
	if ( $user_id == 0 ) {
		#$user = wp_get_current_user();
		$user =  $current_user;
	} else {
		$user = new WP_User( $user_id );
	}

	if ( !empty( $user->roles ) && is_array( $user->roles ) ) {
		if ( (int)array_intersect( $roles_to_check, $user->roles ) ) {
			$role = True;
			/* $role = array_shift($roles); */
			/* $role = isset($wp_roles->role_names[$role]) ? translate_user_role($wp_roles->role_names[$role] ) : false */
		} 
	}

	return $role;	
}

/** GET PROFILE USER **/
function get_profile_user () {
	
}

/*
add_action( 'admin_init', 'user_avatar_setup' );

function user_avatar_setup () { */
	
	/* Define user avatar settings. Dependant on the user-avatar plugin. */
	/*define( 'USER_AVATAR_THUMB_WIDTH', 50 );
	define( 'USER_AVATAR_THUMB_HEIGHT', 50 );
	define( 'USER_AVATAR_FULL_WIDTH', 190 );
	define( 'USER_AVATAR_FULL_HEIGHT', 190 );
	
	echo USER_AVATAR_FULL_WIDTH;
}*/

add_action( 'show_user_profile', 'my_show_extra_profile_fields' );
add_action( 'edit_user_profile', 'my_show_extra_profile_fields' );

function my_show_extra_profile_fields( $user ) {
	
	$profiletypes_user = get_the_author_meta( 'profiletypes', $user->ID );
	$profiletypes_values = array('administrator', 'editor', 'contributor', 'subscriber');
	
	$rolesubscriber = 'subscriber';
	$roleauthor = 'author';
	$roleeditor = 'editor';
	$rolecontributor = 'contributor';
	
	
	global $current_user;
	$user_roles = $current_user->roles;
	$user_role = array_shift($user_roles);
	
	if ( !get_user_role( array( $profiletypes_user['profiletypes'] ) ) && in_array( $profiletypes_user['profiletypes'], $profiletypes_values ) && in_array( $user_role, $profiletypes_values ) ) {
		${'role'. $profiletypes_user['profiletypes']} = $user_role;
		${'role'. $user_role} = $profiletypes_user['profiletypes'];
	}

	if ( get_user_role( array($rolesubscriber, 'administrator') ) ) {
		$bio_change = get_the_author_meta( 'bio_change', $user->ID );
		$bio_projects = get_the_author_meta( 'bio_projects', $user->ID );
		$bio_stuff = get_the_author_meta( 'bio_stuff', $user->ID );
		echo ('
		<table class="form-table">
			<tr>
				<th><label for="bio_change">How I Would Change the World (in 50 words or less!)</label></th>
				<td>
					<textarea name="bio_change" id="bio_change" rows="5" cols="30">' . $bio_change . '</textarea>
				</td>
			</tr>
			<tr>
				<th><label for="bio_projects">Green Projects I Need Help With</label></th>
				<td>
					<textarea name="bio_projects" id="bio_projects" rows="5" cols="30">' . $bio_projects . '</textarea>
					<br /><span class="description">Are you working on something and need to find like minded people with complimentary skills? Explain your project and put the word out - the perfect helper/partner/collaborator might be just round the corner!</span>
				</td>
			</tr>
			<tr>
				<th><label for="bio_stuff">Green Stuff I\'m Into</label></th>
				<td>
					<textarea name="bio_stuff" id="bio_stuff" rows="5" cols="30">' . $bio_stuff . '</textarea>
					<br /><span class="description">Write a few brief words about the environmental, social or world changing issues that get you fired up.</span>
				</td>
			</tr>
		</table>
		');
	}
	
	$locale_postcode = get_the_author_meta( 'locale_postcode', $user->ID );
	echo ('
	<h3>Locale</h3>
	
	<table class="form-table">
		<tr>
			<th><label for="locale_postcode">Postcode</label></th>
			<td><input type="text" name="locale_postcode" id="locale_postcode" class="regular-text" maxlenght="4" value="' . esc_attr($locale_postcode) . '" /><br />
			<span class="description">Receive notifications green things happening close to you on your green toolbar</span></td>
		</tr>
	</table>
	');
	
	if ( get_user_role( array($rolesubscriber, 'administrator') ) ) {
		$employment_jobtitle = get_the_author_meta( 'employment_jobtitle', $user->ID );
		$employment_currentemployer = get_the_author_meta( 'employment_currentemployer', $user->ID );
		echo ('
		<h3>Employment Details</h3>
		
		<table class="form-table">
			<tr>
				<th><label for="employment_jobtitle">Job Title</label></th>
				<td><input type="text" name="employment_jobtitle" id="employment_jobtitle" class="regular-text" maxlenght="4" value="' . esc_attr($employment_jobtitle) . '" /></td>
			</tr>
			<tr>
				<th><label for="employment_currentemployer">Current Employer</label></th>
				<td><input type="text" name="employment_currentemployer" id="employment_currentemployer" class="regular-text" maxlenght="4" value="' . esc_attr($employment_currentemployer) . '" /></td>
			</tr>
		</table>
		');
	}
	
	if ( get_user_role( array($roleeditor, $roleauthor, 'administrator') ) ) {
		$editors_blurb = get_the_author_meta( 'editors_blurb', $user->ID );
		echo ('
		<h3>Editor\'s Profile</h3>
		
		<table class="form-table">
			<tr>
				<th><label for="editors_blurb">Editors Blurb</label></th>
				<td><textarea name="editors_blurb" id="editors_blurb" rows="5" cols="30">' . $editors_blurb . '</textarea><br />
				<span class="description">Tell visitors a little about yourself and your role at Green Pages. Make it fun! Visible on every Editor profile.</span></td>
			</tr>
		</table>
		');
	}
	
	if ( get_user_role( array($rolecontributor, 'administrator') ) ) {
		$contributors_blurb = get_the_author_meta( 'contributors_blurb', $user->ID );
		$contributors_posttagline = get_the_author_meta( 'contributors_posttagline', $user->ID );
		echo ('
		<h3>Contributor\'s Profile</h3>
		
		<table class="form-table">
			<tr>
				<th><label for="contributors_blurb">Contributors Blurb</label></th>
				<td><textarea name="contributors_blurb" id="contributors_blurb" rows="5" cols="30">' . $contributors_blurb . '</textarea><br />
				<span class="description">Tell visitors a little about your organisation. Make it fun! Visible on every Contributor profile.</span></td>
			</tr>
			<tr>
				<th><label for="contributors_posttagline">Contributors Post Tagline</label></th>
				<td><input type="text" maxlenght="255" name="contributors_posttagline" id="contributors_posttagline" class="regular-text" value="' . $contributors_posttagline . '" /><br />
				<span class="description">In a couple sentences tell visitors a little about your organisation. Visible at end of each post you create.</span></td>
			</tr>
		</table>
		');
	}

	echo ('
	<h3>Notification Settings</h3>

	<table class="form-table">

		<tr>
			<th></th>
			<th>Receive my notifitions in a weekly email update</th>
			<th>Receive my notifications only on my dashboard</th>
		</tr>
	'); 
		
		$notification_items = array("notification-email" => "Delivery method");
		$notification_user = get_the_author_meta( 'notification', $user->ID );

		if ( is_array( $notification_items ) ) {
			foreach ( $notification_items as $key => $value ) {
				$checked = false;
				if ( is_array( $notification_user ) ) {
					if ( array_key_exists( $key, $notification_user ) ) {
						if ( $notification_user[$key] == "true" ) {
							$checked = true;
						}
					}
				}
		
		echo ('		
		<tr>
			<th><label for="' . esc_attr($key) . '">' . $value . '</label></th>
			<td><input type="radio" name="' . esc_attr($key) . '" id="' . esc_attr($key) . '" value="true" ');
		if ( $checked == true ) {echo "checked=\"checked\"";} 
		echo ('
		 /></td>
	   	<td><input type="radio" name="' . esc_attr($key) . '" id="' . esc_attr($key) . '" value="false" ');
	   	if ( $checked == false ) {echo "checked=\"checked\"";}
	   	echo ('
	   	 /></td>
		</tr>
		');
		
			}
		}
	?>
	
	</table>
	
	<?php
	echo ('
	<h3>Subscriptions</h3>

	<table class="form-table">

		<tr>
			<th></th>
			<th>Subscribed</th>
			<th>Not subscribed</th>
		</tr>
	'); 
		
		$subscription_items = array("subscription-greenrazor" => "Green Razor newsletter");
		$subscription_user = get_the_author_meta( 'subscription', $user->ID );
		
		if ( is_array( $subscription_items ) ) {
			foreach ( $subscription_items as $key => $value ) {
				$checked = false;
				if ( is_array( $subscription_user ) ) {
					if ( array_key_exists( $key, $subscription_user ) ) {
						if ( $subscription_user[$key] == "true" ) {
							$checked = true;
						}
					}
				}
		
				echo ('		
				<tr>
					<th><label for="' . esc_attr($key) . '">' . $value . '</label></th>
					<td><input type="radio" name="' . esc_attr($key) . '" id="' . esc_attr($key) . '" value="true" ');
				if ( $checked == true ) {echo "checked=\"checked\"";} 
				echo ('
				 /></td>
			   	<td><input type="radio" name="' . esc_attr($key) . '" id="' . esc_attr($key) . '" value="false" ');
			   	if ( $checked == false ) {echo "checked=\"checked\"";}
			   	echo ('
			   	 /></td>
				</tr>
				');
		
			}
		}
		?>
		
	</table>
	
	<?php
	if ( get_user_role( array('administrator') ) ) {
		$profiletypes_items = array('profiletypes');
		# $profiletypes_users and $profiletypes_values defined top of function

		if ( is_array( $profiletypes_items ) ) {
		
			echo ('
			<h3>Display profile as...</h3>
			<table class="form-table"><tr>
			');
			foreach ( $profiletypes_values as $value ) {echo ('<th>' . ucfirst($value) . '</th>');}				
			echo ('</tr>');
			
			if ( get_user_role( array('subscriber'), $user->ID ) ) {$checked = 'subscriber';} else {$checked = 'subscriber';}
			if ( get_user_role( array('administrator'), $user->ID ) ) {$checked = 'administrator';}
			if ( get_user_role( array('contributor'), $user->ID ) ) {$checked = 'contributor';}
			if ( get_user_role( array('editor', 'author'), $user->ID ) ) {$checked = 'editor';}
			
			foreach ( $profiletypes_items as $itemvalue  ) {
				if ( is_array( $profiletypes_user ) ) {
					if ( array_key_exists( $itemvalue, $profiletypes_user ) ) {
						$checked = $profiletypes_user[$itemvalue];
					}
				}
				echo ('<tr>');
				foreach ( $profiletypes_values as $value ) {
					if ($checked == $value) {$checkthis = ' checked="checked"';} else {$checkthis = '';}
					echo ('<td><input type="radio" name="' . esc_attr($itemvalue) . '" id="' . esc_attr($itemvalue) . '" value="' . esc_attr($value) . '"' . $checkthis . ' /></td>');
					$checkthis = '';
				}
				echo ('</tr>');
			}
			
			echo ('</table>');
		}
	}
	?>

<?php

} 

add_action( 'personal_options_update', 'my_save_extra_profile_fields' );
add_action( 'edit_user_profile_update', 'my_save_extra_profile_fields' );

function my_save_extra_profile_fields( $user_id ) {
	if ( !current_user_can( 'edit_user', $user_id ) ) {
		return false;
	}
	
	$notification_items = array("notification-email");
	$notification_post = array();
	$notification_values = array("true", "false");

	if ( is_array( $notification_items ) ) {
		foreach ( $notification_items as $value ) {
			if ( isset ( $_POST[$value] ) && in_array ( $_POST[$value], $notification_values ) ) {
				$notification_post = $notification_post + array($value => $_POST[$value]);
			}
		}
	}
	
	$subscription_items = array("subscription-greenrazor");
	$subscription_post = array();
	$subscription_values = array("true", "false");
	
	if ( is_array( $subscription_items ) ) {
		foreach ( $subscription_items as $value ) {
			if ( isset ( $_POST[$value] ) && in_array ( $_POST[$value], $subscription_values ) ) {
				$subscription_post = $subscription_post + array($value => $_POST[$value]);
			}
		}
	}
	
	$profiletypes_items = array("profiletypes");
	$profiletypes_post = array();
	$profiletypes_values = array("administrator", "editor", "contributor", "subscriber");
	
	if ( is_array( $profiletypes_items ) ) {
		foreach ( $profiletypes_items as $value ) {
			if ( isset ( $_POST[$value] ) && in_array ( $_POST[$value], $profiletypes_values ) ) {
				$profiletypes_post = $profiletypes_post + array($value => $_POST[$value]);
			}
		}
	}
	
	update_usermeta($user_id, 'bio_change', $_POST['bio_change'] );
	update_usermeta($user_id, 'bio_projects', $_POST['bio_projects'] );
	update_usermeta($user_id, 'bio_stuff', $_POST['bio_stuff'] );
	update_usermeta($user_id, 'locale_postcode', $_POST['locale_postcode'] );
	update_usermeta($user_id, 'employment_jobtitle', $_POST['employment_jobtitle'] );
	update_usermeta($user_id, 'employment_currentemployer', $_POST['employment_currentemployer'] );
	update_usermeta($user_id, 'editors_blurb', $_POST['editors_blurb'] );
	update_usermeta($user_id, 'contributors_blurb', $_POST['contributors_blurb'] );
	update_usermeta($user_id, 'contributors_posttagline', $_POST['contributors_posttagline'] );
	update_usermeta($user_id, 'notification', $notification_post );
	
	/*** UPDATE CAMPAIGN MONITOR - USER GREENRAZOR SUBSCRIPTION ***/
	if (cm_subscribe($subscription_post['subscription-greenrazor'])) {
		update_usermeta($user_id, 'subscription', $subscription_post );
	} else {
		$subscription_post['subscription-greenrazor']='false';
		update_usermeta($user_id, 'subscription', $subscription_post );
	}
	
	update_usermeta($user_id, 'profiletypes', $profiletypes_post );
}


/*** NEW POST TYPES ***/

/* news */

$newsargs = array(
    'label' => __( 'News' ),
    'labels' => array(
	    'name' => _x( 'News', 'post type general name' ),
	    'singular_name' => _x( 'News', 'post type singular name' ),
	    'add_new' => _x( 'Add New', 'news' ),
	    'add_new_item' => __( 'Add New News' ),
	    'edit_item' => __( 'Edit News' ),
	    'new_item' => __( 'New News' ),
	    'view_item' => __( 'View News' ),
	    'search_items' => __( 'Search News' ),
	    'not_found' =>  __( 'No news found' ),
	    'not_found_in_trash' => __( 'No news found in Trash' ),
	    'parent_item_colon' => ''
	),
    'public' => true,
    'can_export' => true,
    'show_ui' => true,
    '_builtin' => false,
    '_edit_link' => 'post.php?post=%d', // ?
    'capability_type' => 'post',
    'menu_icon' => get_bloginfo( 'template_url' ).'/template/newspaper.png',
    'hierarchical' => false,
    'rewrite' => array( 'slug' => 'news', 'with_front' => FALSE ),
    'supports'=> array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'trackbacks', 'comments', 'revisions', 'page-attributes', 'custom-fields' ),
    'show_in_nav_menus' => true,
    'taxonomies' => array( 'gp_jobs_category', 'post_tag'),
	'has_archive' => true
);

$newstaxonomy = array(
	'label' => __( 'News Category' ),
	'labels' => array(
		'name' => _x( 'Categories', 'taxonomy general name' ),
		'singular_name' => _x( 'Category', 'taxonomy singular name' ),
		'search_items' =>  __( 'Search Categories' ),
		'popular_items' => __( 'Popular Categories' ),
		'all_items' => __( 'All Categories' ),
		'parent_item' => null,
		'parent_item_colon' => null,
		'edit_item' => __( 'Edit Category' ),
		'update_item' => __( 'Update Category' ),
		'add_new_item' => __( 'Add New Category' ),
		'new_item_name' => __( 'New Category Name' ),
		'separate_items_with_commas' => __( 'Separate categories with commas' ),
		'add_or_remove_items' => __( 'Add or remove categories' ),
		'choose_from_most_used' => __( 'Choose from the most used categories' )
	),
	'hierarchical' => true,
	'show_ui' => true,
	'query_var' => true,
	'rewrite' => array( 'slug' => 'news-category' )
);

/* events */
$eventargs = array(
    'label' => __( 'Events' ),
    'labels' => array(
	    'name' => _x( 'Events', 'post type general name' ),
	    'singular_name' => _x( 'Event', 'post type singular name' ),
	    'add_new' => _x( 'Add New', 'events' ),
	    'add_new_item' => __( 'Add New Event' ),
	    'edit_item' => __( 'Edit Event' ),
	    'new_item' => __( 'New Event' ),
	    'view_item' => __( 'View Event' ),
	    'search_items' => __( 'Search Events' ),
	    'not_found' =>  __( 'No events found' ),
	    'not_found_in_trash' => __( 'No events found in Trash' ),
	    'parent_item_colon' => ''
	),
    'public' => true,
    'can_export' => true,
    'show_ui' => true,
    '_builtin' => false,
    '_edit_link' => 'post.php?post=%d', // ?
    'capability_type' => 'post',
    'menu_icon' => get_bloginfo( 'template_url' ).'/template/date.png',
    'hierarchical' => false,
    'rewrite' => array( 'slug' => 'events', 'with_front' => FALSE ),
	'supports'=> array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'trackbacks', 'comments', 'revisions', 'page-attributes', 'custom-fields' ),
    'show_in_nav_menus' => true,
    'taxonomies' => array( 'gp_events_category', 'post_tag'),
	'has_archive' => true
);

$eventtaxonomy = array(
	'label' => __( 'Event Category' ),
	'labels' => array(
		'name' => _x( 'Categories', 'taxonomy general name' ),
		'singular_name' => _x( 'Category', 'taxonomy singular name' ),
		'search_items' =>  __( 'Search Categories' ),
		'popular_items' => __( 'Popular Categories' ),
		'all_items' => __( 'All Categories' ),
		'parent_item' => null,
		'parent_item_colon' => null,
		'edit_item' => __( 'Edit Category' ),
		'update_item' => __( 'Update Category' ),
		'add_new_item' => __( 'Add New Category' ),
		'new_item_name' => __( 'New Category Name' ),
		'separate_items_with_commas' => __( 'Separate categories with commas' ),
		'add_or_remove_items' => __( 'Add or remove categories' ),
		'choose_from_most_used' => __( 'Choose from the most used categories' )
	),
	'hierarchical' => true,
	'show_ui' => true,
	'query_var' => true,
	'rewrite' => array( 'slug' => 'event-category' )
);

/* jobs */

$jobargs = array(
    'label' => __( 'Jobs' ),
    'labels' => array(
	    'name' => _x( 'Jobs', 'post type general name' ),
	    'singular_name' => _x( 'Job', 'post type singular name' ),
	    'add_new' => _x( 'Add New', 'jobs' ),
	    'add_new_item' => __( 'Add New Job' ),
	    'edit_item' => __( 'Edit Job' ),
	    'new_item' => __( 'New Job' ),
	    'view_item' => __( 'View Job' ),
	    'search_items' => __( 'Search Jobs' ),
	    'not_found' =>  __( 'No jobs found' ),
	    'not_found_in_trash' => __( 'No jobs found in Trash' ),
	    'parent_item_colon' => ''
	),
    'public' => true,
    'can_export' => true,
    'show_ui' => true,
    '_builtin' => false,
    '_edit_link' => 'post.php?post=%d', // ?
    'capability_type' => 'post',
    'menu_icon' => get_bloginfo( 'template_url' ).'/template/user_gray.png',
    'hierarchical' => false,
    'rewrite' => array( 'slug' => 'jobs', 'with_front' => FALSE ),
    'supports'=> array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'trackbacks', 'revisions', 'page-attributes', 'custom-fields' ),
    'show_in_nav_menus' => true,
    'taxonomies' => array( 'gp_jobs_category', 'post_tag'),
	'has_archive' => true
);

$jobtaxonomy = array(
	'label' => __( 'Job Category' ),
	'labels' => array(
		'name' => _x( 'Categories', 'taxonomy general name' ),
		'singular_name' => _x( 'Category', 'taxonomy singular name' ),
		'search_items' =>  __( 'Search Categories' ),
		'popular_items' => __( 'Popular Categories' ),
		'all_items' => __( 'All Categories' ),
		'parent_item' => null,
		'parent_item_colon' => null,
		'edit_item' => __( 'Edit Category' ),
		'update_item' => __( 'Update Category' ),
		'add_new_item' => __( 'Add New Category' ),
		'new_item_name' => __( 'New Category Name' ),
		'separate_items_with_commas' => __( 'Separate categories with commas' ),
		'add_or_remove_items' => __( 'Add or remove categories' ),
		'choose_from_most_used' => __( 'Choose from the most used categories' )
	),
	'hierarchical' => true,
	'show_ui' => true,
	'query_var' => true,
	'rewrite' => array( 'slug' => 'job-category' )
);

/* competitions */

$competitionargs = array(
    'label' => __( 'Competitions' ),
    'labels' => array(
	    'name' => _x( 'Competitions', 'post type general name' ),
	    'singular_name' => _x( 'Competition', 'post type singular name' ),
	    'add_new' => _x( 'Add New ($250)', 'competitions' ),
	    'add_new_item' => __( 'Add New Competition - Price $250 (Charged only when post is approved for publication.)' ),
	    'edit_item' => __( 'Edit Competition' ),
	    'new_item' => __( 'New Competition' ),
	    'view_item' => __( 'View Competition' ),
	    'search_items' => __( 'Search Competitions' ),
	    'not_found' =>  __( 'No competitions found' ),
	    'not_found_in_trash' => __( 'No competitions found in Trash' ),
	    'parent_item_colon' => ''
	),
    'public' => true,
    'can_export' => true,
    'show_ui' => true,
    '_builtin' => false,
    '_edit_link' => 'post.php?post=%d', // ?
    'capability_type' => 'post',
    'menu_icon' => get_bloginfo( 'template_url' ).'/template/rosette.png',
    'hierarchical' => false,
    'rewrite' => array( 'slug' => 'competitions' ,'with_front' => FALSE ),
    'supports'=> array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'trackbacks', 'comments', 'revisions', 'page-attributes', 'custom-fields' ),
    'show_in_nav_menus' => true,
    'taxonomies' => array( 'gp_competitions_category', 'post_tag'),
	'has_archive' => true
);

$competitiontaxonomy = array(
	'label' => __( 'Competition Category' ),
	'labels' => array(
		'name' => _x( 'Categories', 'taxonomy general name' ),
		'singular_name' => _x( 'Category', 'taxonomy singular name' ),
		'search_items' =>  __( 'Search Categories' ),
		'popular_items' => __( 'Popular Categories' ),
		'all_items' => __( 'All Categories' ),
		'parent_item' => null,
		'parent_item_colon' => null,
		'edit_item' => __( 'Edit Category' ),
		'update_item' => __( 'Update Category' ),
		'add_new_item' => __( 'Add New Category' ),
		'new_item_name' => __( 'New Category Name' ),
		'separate_items_with_commas' => __( 'Separate categories with commas' ),
		'add_or_remove_items' => __( 'Add or remove categories' ),
		'choose_from_most_used' => __( 'Choose from the most used categories' )
	),
	'hierarchical' => true,
	'show_ui' => true,
	'query_var' => true,
	'rewrite' => array( 'slug' => 'competition-category' )
);

    
/* interviews */
    
$peopleargs = array(
    'label' => __( 'Interviews' ),
    'labels' => array(
	    'name' => _x( 'Interviews', 'post type general name' ),
	    'singular_name' => _x( 'Interview', 'post type singular name' ),
	    'add_new' => _x( 'Add New', 'Interview' ),
	    'add_new_item' => __( 'Add New Interview' ),
	    'edit_item' => __( 'Edit Interview' ),
	    'new_item' => __( 'New Interview' ),
	    'view_item' => __( 'View Interview' ),
	    'search_items' => __( 'Search Interviews' ),
	    'not_found' =>  __( 'No interviews found' ),
	    'not_found_in_trash' => __( 'No interviews found in Trash' ),
	    'parent_item_colon' => ''
	),
    'public' => true,
    'can_export' => true,
    'show_ui' => true,
    '_builtin' => false,
    '_edit_link' => 'post.php?post=%d', // ?
    'capability_type' => 'post',
    'menu_icon' => get_bloginfo( 'template_url' ).'/template/cup.png',
    'hierarchical' => false,
    'rewrite' => array( 'slug' => 'people' ,'with_front' => FALSE ),
    'supports'=> array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'trackbacks', 'comments', 'revisions', 'page-attributes', 'custom-fields' ),
    'show_in_nav_menus' => true,
    'taxonomies' => array( 'gp_interviews_category', 'post_tag'),
	'has_archive' => true
);

$peopletaxonomy = array(
	'label' => __( 'Interviews Category' ),
	'labels' => array(
		'name' => _x( 'Categories', 'taxonomy general name' ),
		'singular_name' => _x( 'Category', 'taxonomy singular name' ),
		'search_items' =>  __( 'Search Categories' ),
		'popular_items' => __( 'Popular Categories' ),
		'all_items' => __( 'All Categories' ),
		'parent_item' => null,
		'parent_item_colon' => null,
		'edit_item' => __( 'Edit Category' ),
		'update_item' => __( 'Update Category' ),
		'add_new_item' => __( 'Add New Category' ),
		'new_item_name' => __( 'New Category Name' ),
		'separate_items_with_commas' => __( 'Separate categories with commas' ),
		'add_or_remove_items' => __( 'Add or remove categories' ),
		'choose_from_most_used' => __( 'Choose from the most used categories' )
	),
	'hierarchical' => true,
	'show_ui' => true,
	'query_var' => true,
	'rewrite' => array( 'slug' => 'interview-category' )
);



/* Katie Patrick */
    
$katiepatrickargs = array(
    'label' => __( 'Katie Patrick' ),
    'labels' => array(
	    'name' => _x( 'Katie Patrick', 'post type general name' ),
	    'singular_name' => _x( 'Katie Patrick', 'post type singular name' ),
	    'add_new' => _x( 'Add New', 'Story' ),
	    'add_new_item' => __( 'Add New Story' ),
	    'edit_item' => __( 'Edit Story' ),
	    'new_item' => __( 'New Story' ),
	    'view_item' => __( 'View Story' ),
	    'search_items' => __( 'Search Stories' ),
	    'not_found' =>  __( 'No stories found' ),
	    'not_found_in_trash' => __( 'No stories found in Trash' ),
	    'parent_item_colon' => ''
	),
    'public' => true,
    'can_export' => true,
    'show_ui' => true,
    '_builtin' => false,
    '_edit_link' => 'post.php?post=%d', // ?
    'capability_type' => 'post',
    'menu_icon' => get_bloginfo( 'template_url' ).'/template/katiepatrick.png',
    'hierarchical' => false,
    'rewrite' => array( 'slug' => 'katie-patrick' ,'with_front' => FALSE ),
    'supports'=> array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'trackbacks', 'comments', 'revisions', 'page-attributes', 'custom-fields' ),
    'show_in_nav_menus' => true,
    'taxonomies' => array( 'gp_katiepatrick_category', 'post_tag'),
	'has_archive' => true
);

$katiepatricktaxonomy = array(
	'label' => __( 'Katie Patrick Category' ),
	'labels' => array(
		'name' => _x( 'Categories', 'taxonomy general name' ),
		'singular_name' => _x( 'Category', 'taxonomy singular name' ),
		'search_items' =>  __( 'Search Categories' ),
		'popular_items' => __( 'Popular Categories' ),
		'all_items' => __( 'All Categories' ),
		'parent_item' => null,
		'parent_item_colon' => null,
		'edit_item' => __( 'Edit Category' ),
		'update_item' => __( 'Update Category' ),
		'add_new_item' => __( 'Add New Category' ),
		'new_item_name' => __( 'New Category Name' ),
		'separate_items_with_commas' => __( 'Separate categories with commas' ),
		'add_or_remove_items' => __( 'Add or remove categories' ),
		'choose_from_most_used' => __( 'Choose from the most used categories' )
	),
	'hierarchical' => true,
	'show_ui' => true,
	'query_var' => true,
	'rewrite' => array( 'slug' => 'katie-patrick-category' )
);


/* Product Review */
    
$productreviewargs = array(
    'label' => __( 'Product Reviews' ),
    'labels' => array(
	    'name' => _x( 'Product Review', 'post type general name' ),
	    'singular_name' => _x( 'Product Review', 'post type singular name' ),
	    'add_new' => _x( 'Add New', 'Product Review' ),
	    'add_new_item' => __( 'Add New Product Review' ),
	    'edit_item' => __( 'Edit Product Review' ),
	    'new_item' => __( 'New Product Review' ),
	    'view_item' => __( 'View Product Review' ),
	    'search_items' => __( 'Search Product Reviews' ),
	    'not_found' =>  __( 'No product reviews found' ),
	    'not_found_in_trash' => __( 'No product reviews found in Trash' ),
	    'parent_item_colon' => ''
	),
    'public' => true,
    'can_export' => true,
    'show_ui' => true,
    '_builtin' => false,
    '_edit_link' => 'post.php?post=%d', // ?
    'capability_type' => 'post',
    'menu_icon' => get_bloginfo( 'template_url' ).'/template/icon-productreview.png',
    'hierarchical' => false,
    'rewrite' => array( 'slug' => 'product-review' ,'with_front' => FALSE ),
    'supports'=> array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'trackbacks', 'comments', 'revisions', 'page-attributes', 'custom-fields' ),
    'show_in_nav_menus' => true,
    'taxonomies' => array( 'gp_productreview_category', 'post_tag'),
	'has_archive' => true
);

$productreviewtaxonomy = array(
	'label' => __( 'Product Review Category' ),
	'labels' => array(
		'name' => _x( 'Categories', 'taxonomy general name' ),
		'singular_name' => _x( 'Category', 'taxonomy singular name' ),
		'search_items' =>  __( 'Search Categories' ),
		'popular_items' => __( 'Popular Categories' ),
		'all_items' => __( 'All Categories' ),
		'parent_item' => null,
		'parent_item_colon' => null,
		'edit_item' => __( 'Edit Category' ),
		'update_item' => __( 'Update Category' ),
		'add_new_item' => __( 'Add New Category' ),
		'new_item_name' => __( 'New Category Name' ),
		'separate_items_with_commas' => __( 'Separate categories with commas' ),
		'add_or_remove_items' => __( 'Add or remove categories' ),
		'choose_from_most_used' => __( 'Choose from the most used categories' )
	),
	'hierarchical' => true,
	'show_ui' => true,
	'query_var' => true,
	'rewrite' => array( 'slug' => 'product-review-category' )
);


/* Advertorial */
    
$advertorialargs = array(
    'label' => __( 'Advertorials' ),
    'labels' => array(
	    'name' => _x( 'Advertorials', 'post type general name' ),
	    'singular_name' => _x( 'Advertorial', 'post type singular name' ),
	    'add_new' => _x( 'Add New ($89)', 'Advertorial' ),
	    'add_new_item' => __( 'Add New Advertorial - Just $89! (Charged only when post is approved for publication.)' ),
	    'edit_item' => __( 'Edit Advertorial' ),
	    'new_item' => __( 'New Advertorial' ),
	    'view_item' => __( 'View Advertorial' ),
	    'search_items' => __( 'Search Advertorials' ),
	    'not_found' =>  __( 'No advertorials found' ),
	    'not_found_in_trash' => __( 'No advertorials found in Trash' ),
	    'parent_item_colon' => ''
	),
    'public' => true,
    'can_export' => true,
    'show_ui' => true,
    '_builtin' => false,
    '_edit_link' => 'post.php?post=%d', // ?
    'capability_type' => 'post',
    'menu_icon' => get_bloginfo( 'template_url' ).'/template/icon-advertorial.png',
    'hierarchical' => false,
    'rewrite' => array( 'slug' => 'new-stuff' ,'with_front' => FALSE ),
    'supports'=> array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'trackbacks', 'comments', 'revisions', 'page-attributes', 'custom-fields' ),
    'show_in_nav_menus' => true,
    'taxonomies' => array( 'gp_advertorial_category', 'post_tag'),
	'has_archive' => true
);

$advertorialtaxonomy = array(
	'label' => __( 'Advertorial Category' ),
	'labels' => array(
		'name' => _x( 'Categories', 'taxonomy general name' ),
		'singular_name' => _x( 'Category', 'taxonomy singular name' ),
		'search_items' =>  __( 'Search Categories' ),
		'popular_items' => __( 'Popular Categories' ),
		'all_items' => __( 'All Categories' ),
		'parent_item' => null,
		'parent_item_colon' => null,
		'edit_item' => __( 'Edit Category' ),
		'update_item' => __( 'Update Category' ),
		'add_new_item' => __( 'Add New Category' ),
		'new_item_name' => __( 'New Category Name' ),
		'separate_items_with_commas' => __( 'Separate categories with commas' ),
		'add_or_remove_items' => __( 'Add or remove categories' ),
		'choose_from_most_used' => __( 'Choose from the most used categories' )
	),
	'hierarchical' => true,
	'show_ui' => true,
	'query_var' => true,
	'rewrite' => array( 'slug' => 'advertorial-category' )
);


/* NGO Campaigns */
    
$ngocampaignargs = array(
    'label' => __( 'NGO Campaigns' ),
    'labels' => array(
	    'name' => _x( 'NGO Campaigns', 'post type general name' ),
	    'singular_name' => _x( 'Campaign', 'post type singular name' ),
	    'add_new' => _x( 'Add New', 'Campaign' ),
	    'add_new_item' => __( 'Add New Campaign' ),
	    'edit_item' => __( 'Edit Campaign' ),
	    'new_item' => __( 'New Campaign' ),
	    'view_item' => __( 'View Campaign' ),
	    'search_items' => __( 'Search Campaign' ),
	    'not_found' =>  __( 'No campaigns found' ),
	    'not_found_in_trash' => __( 'No campaigns found in Trash' ),
	    'parent_item_colon' => ''
	),
    'public' => true,
    'can_export' => true,
    'show_ui' => true,
    '_builtin' => false,
    '_edit_link' => 'post.php?post=%d', // ?
    'capability_type' => 'post',
    'menu_icon' => get_bloginfo( 'template_url' ).'/template/transmit.png',
    'hierarchical' => false,
    'rewrite' => array( 'slug' => 'ngo-campaign' ,'with_front' => FALSE ),
    'supports'=> array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'trackbacks', 'comments', 'revisions', 'page-attributes', 'custom-fields' ),
    'show_in_nav_menus' => true,
    'taxonomies' => array( 'gp_ngocampaign_category', 'post_tag'),
	'has_archive' => true
);

$ngocampaigntaxonomy = array(
	'label' => __( 'Campaign Category' ),
	'labels' => array(
		'name' => _x( 'Categories', 'taxonomy general name' ),
		'singular_name' => _x( 'Category', 'taxonomy singular name' ),
		'search_items' =>  __( 'Search Categories' ),
		'popular_items' => __( 'Popular Categories' ),
		'all_items' => __( 'All Categories' ),
		'parent_item' => null,
		'parent_item_colon' => null,
		'edit_item' => __( 'Edit Category' ),
		'update_item' => __( 'Update Category' ),
		'add_new_item' => __( 'Add New Category' ),
		'new_item_name' => __( 'New Category Name' ),
		'separate_items_with_commas' => __( 'Separate categories with commas' ),
		'add_or_remove_items' => __( 'Add or remove categories' ),
		'choose_from_most_used' => __( 'Choose from the most used categories' )
	),
	'hierarchical' => true,
	'show_ui' => true,
	'query_var' => true,
	'rewrite' => array( 'slug' => 'ngocampaign-category' )
);


/* Green Gurus */
    
$greengurusargs = array(
    'label' => __( 'Green Gurus' ),
    'labels' => array(
	    'name' => _x( 'Green Gurus', 'post type general name' ),
	    'singular_name' => _x( 'Green Guru', 'post type singular name' ),
	    'add_new' => _x( 'Add New', 'Guru Story' ),
	    'add_new_item' => __( 'Add New Guru Story' ),
	    'edit_item' => __( 'Edit Guru Story' ),
	    'new_item' => __( 'New Guru Story' ),
	    'view_item' => __( 'View Guru Stories' ),
	    'search_items' => __( 'Search Guru Stories' ),
	    'not_found' =>  __( 'No guru stories found' ),
	    'not_found_in_trash' => __( 'No guru stories found in Trash' ),
	    'parent_item_colon' => ''
	),
    'public' => true,
    'can_export' => true,
    'show_ui' => true,
    '_builtin' => false,
    '_edit_link' => 'post.php?post=%d', // ?
    'capability_type' => 'post',
    'menu_icon' => get_bloginfo( 'template_url' ).'/template/icon-greenguru.png',
    'hierarchical' => false,
    'rewrite' => array( 'slug' => 'green-gurus' ,'with_front' => FALSE ),
    'supports'=> array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'trackbacks', 'comments', 'revisions', 'page-attributes', 'custom-fields' ),
    'show_in_nav_menus' => true,
    'taxonomies' => array( 'gp_greengurus_category', 'post_tag'),
	'has_archive' => true
);

$greengurustaxonomy = array(
	'label' => __( 'Green Gurus Category' ),
	'labels' => array(
		'name' => _x( 'Categories', 'taxonomy general name' ),
		'singular_name' => _x( 'Category', 'taxonomy singular name' ),
		'search_items' =>  __( 'Search Categories' ),
		'popular_items' => __( 'Popular Categories' ),
		'all_items' => __( 'All Categories' ),
		'parent_item' => null,
		'parent_item_colon' => null,
		'edit_item' => __( 'Edit Category' ),
		'update_item' => __( 'Update Category' ),
		'add_new_item' => __( 'Add New Category' ),
		'new_item_name' => __( 'New Category Name' ),
		'separate_items_with_commas' => __( 'Separate categories with commas' ),
		'add_or_remove_items' => __( 'Add or remove categories' ),
		'choose_from_most_used' => __( 'Choose from the most used categories' )
	),
	'hierarchical' => true,
	'show_ui' => true,
	'query_var' => true,
	'rewrite' => array( 'slug' => 'green-gurus-category' )
);

/* get new post types */
global $wp_role;

/* Modify widgets for 'add new' based on user role  */
if ( get_user_role( array('contributor') ) ) {
	unset($newsargs['supports']);
	unset($ngocampaignargs['supports']);
	unset($eventargs['supports']);
	$newsargs['supports'] = array( 'title', 'editor', 'thumbnail', 'excerpt', 'revisions' ) ;
	$ngocampaignargs['supports'] = array( 'title', 'editor', 'thumbnail', 'excerpt', 'revisions' );
	$eventargs['supports'] = array( 'title', 'editor', 'thumbnail', 'excerpt', 'revisions' );
} 

$sitemaptypes = array(
	array('id' => 'sitemap', 'name' => 'Sitemap'),
	array('id' => 'googlenews', 'name' => 'Google News')
);

$newposttypes = array(
	array('id' => 'gp_news', 'name' => 'News', 'plural' => false, 'addmeta' => false, 'args' => $newsargs, 'taxonomy' => $newstaxonomy, 'columns' => array('author', 'categories', 'tags', 'comments', 'date'), 'enabled' => true, 'priority' => '1', 'changefreq' => 'monthly', 'keywords' => 'science, environment'),
	array('id' => 'gp_events', 'name' => 'Event', 'plural' => true, 'addmeta' => true, 'args' => $eventargs, 'taxonomy' => $eventtaxonomy, 'columns' => array('author', 'categories', 'tags', 'comments', 'date', 'dates'), 'enabled' => true, 'priority' => '0.6', 'changefreq' => 'monthly', 'keywords' => 'science, environment'),
	array('id' => 'gp_jobs', 'name' => 'Job', 'plural' => true, 'addmeta' => false, 'args' => $jobargs, 'taxonomy' => $jobtaxonomy, 'columns' => array('author', 'categories', 'tags', 'comments', 'date'), 'enabled' => false, 'priority' => '0.6', 'changefreq' => 'monthly', 'keywords' => 'science, environment'),
	array('id' => 'gp_competitions', 'name' => 'Competition', 'plural' => true, 'addmeta' => true, 'args' => $competitionargs, 'taxonomy' => $competitiontaxonomy, 'columns' => array('author', 'categories', 'tags', 'comments', 'date', 'dates'), 'enabled' => true, 'priority' => '0.6', 'changefreq' => 'monthly', 'keywords' => 'science, environment'),
	array('id' => 'gp_people', 'name' => 'People', 'plural' => false, 'addmeta' => false, 'args' => $peopleargs, 'taxonomy' => $peopletaxonomy, 'columns' => array('author', 'categories', 'tags', 'comments', 'date'), 'enabled' => true, 'priority' => '0.6', 'changefreq' => 'monthly', 'keywords' => 'science, environment'),
	array('id' => 'gp_katiepatrick', 'name' => 'Katie Patrick', 'plural' => false, 'addmeta' => false, 'args' => $katiepatrickargs, 'taxonomy' => $katiepatricktaxonomy, 'columns' => array('author', 'categories', 'tags', 'comments', 'date'), 'enabled' => false, 'priority' => '0.6', 'changefreq' => 'monthly', 'keywords' => 'science, environment'),
	array('id' => 'gp_productreview', 'name' => 'Product Review', 'plural' => false, 'addmeta' => false, 'args' => $productreviewargs, 'taxonomy' => $productreviewtaxonomy, 'columns' => array('author', 'categories', 'tags', 'comments', 'date'), 'enabled' => false, 'priority' => '0.6', 'changefreq' => 'monthly', 'keywords' => 'science, environment'),
	array('id' => 'gp_advertorial', 'name' => 'Advertorial', 'plural' => true, 'addmeta' => false, 'args' => $advertorialargs, 'taxonomy' => $advertorialtaxonomy, 'columns' => array('author', 'categories', 'tags', 'comments', 'date'), 'enabled' => true, 'priority' => '0.6', 'changefreq' => 'monthly', 'keywords' => 'science, environment'),
	array('id' => 'gp_ngocampaign', 'name' => 'NGO Campaign', 'plural' => true, 'addmeta' => false, 'args' => $ngocampaignargs, 'taxonomy' => $ngocampaigntaxonomy, 'columns' => array('author', 'categories', 'tags', 'comments', 'date'), 'enabled' => true, 'priority' => '0.6', 'changefreq' => 'monthly', 'keywords' => 'science, environment'),
	array('id' => 'gp_greengurus', 'name' => 'Green Gurus', 'plural' => false, 'addmeta' => false, 'args' => $greengurusargs, 'taxonomy' => $greengurustaxonomy, 'columns' => array('author', 'categories', 'tags', 'comments', 'date'), 'enabled' => false, 'priority' => '0.6', 'changefreq' => 'monthly', 'keywords' => 'science, environment')
);

/** SET NEWS POST TYPE FOR DISPLAY ON THE HOME PAGE **/
add_filter( 'pre_get_posts', 'my_get_posts' );
function my_get_posts( $query ) {
	if ( is_home() ) {
		$query->set( 'post_type', array( 'gp_news' ) );
	}
	return $query;
}

/** ADD THUMBNAILS SUPPORT **/
# note: http://emrahgunduz.com/categories/development/wordpress/wordpress-how-to-show-the-featured-image-of-posts-in-social-sharing-sites/
# note: http://markjaquith.wordpress.com/2009/12/23/new-in-wordpress-2-9-post-thumbnail-images/
add_theme_support( 'post-thumbnails', array( 'post', 'gp_news', 'gp_events', 'gp_competitions', 'gp_jobs', 'gp_people', 'gp_advertorial', 'gp_ngocampaign', 'gp_katiepatrick', 'gp_productreview', 'gp_greengurus' ) );
add_image_size('homepage-thumbnail', 140, 105, true);
add_image_size('icon-thumbnail', 50, 50, true);
add_image_size('dash-thumbnail', 35, 35, true);
# add_image_size('homepage-featured', 240, 180, true);

/*** combined ***/
add_action( 'init', 'createPostOptions' );

for($index = 0; $index < count($newposttypes); $index++) {
	if ($newposttypes[$index]['enabled'] == true) {
		add_filter( 'manage_edit-' . $newposttypes[$index]['id'] . '_columns', 'editColumns' );
	}
	# add_action( 'manage_posts_custom_column', $newposttypes[$index]['id'] . '_custom_columns' );
}
add_action( 'manage_posts_custom_column', 'new_custom_columns' );
add_action( 'admin_init', 'createPostMeta' );
add_action( 'save_post', 'savePostType' );
add_filter( 'post_updated_messages', 'updated_messages' );

function createPostOptions () {
	global $newposttypes;
	for($index = 0; $index < count($newposttypes); $index++) {
		if ($newposttypes[$index]['enabled'] == true) {
			register_post_type( $newposttypes[$index]['id'] , $newposttypes[$index]['args'] );
			register_taxonomy( $newposttypes[$index]['id'] . '_category', $newposttypes[$index]['id'], $newposttypes[$index]['taxonomy'] );
		}
	}	
	flush_rewrite_rules();
}

function getPluralName ($newposttype) {
	if ($newposttype['plural'] == true) {
		return $newposttype['name'] . 's';
	} else {
		return $newposttype['name'];
	}
}

function createPostMeta () {
	global $newposttypes;
	for($index = 0; $index < count($newposttypes); $index++) {
		if ($newposttypes[$index]['enabled'] == true) {
			if ( $newposttypes[$index]['addmeta'] == true && function_exists($newposttypes[$index]['id'] . '_meta') ) {
				$getPostName = getPluralName( $newposttypes[$index] );
				add_meta_box( $newposttypes[$index]['id'] . '_meta', $getPostName , $newposttypes[$index]['id'] . '_meta', $newposttypes[$index]['id'] );
			}
			/* WORDBOOK PLUGIN SUPPORT FOR CUSTOM POST TYPES */
			if (get_option('wordbooker_settings')) { 
				if (current_user_can(WORDBOOKER_MINIMUM_ADMIN_LEVEL)) {
					add_meta_box( 'wordbook_sectionid', __('WordBooker Options'),'wordbooker_inner_custom_box', $newposttypes[$index]['id'] , 'advanced' );
				}
			}
		}
	}
}


function savePostType () {
	global $post, $newposttypes;
		$thisposttype = get_post_type();

	    if ( !wp_verify_nonce( $_POST[$thisposttype . '-nonce'], $thisposttype . '-nonce' )) {
	        return $post->ID;
	    }
	    
	    if ( !current_user_can( 'edit_post', $post->ID )) {
	        return $post->ID;
	    }
	    
	    /* set your custom fields */
	    if(isset($_POST[$thisposttype . '_startdate'])) {
	    	$updatestartd = strtotime ( $_POST[$thisposttype . '_startdate'] . $_POST[$thisposttype . '_starttime'] );
	    	update_post_meta($post->ID, $thisposttype . '_startdate', $updatestartd );
	    }
	
	    if(isset($_POST[$thisposttype . '_enddate'])) {
	    	$updateendd = strtotime ( $_POST[$thisposttype . '_enddate'] . $_POST[$thisposttype . '_endtime']);
	    	update_post_meta($post->ID, $thisposttype . '_enddate', $updateendd );
	    }
	    
		if(isset($_POST[$thisposttype . '_drawdate'])) {
	    	$updatedrawd = strtotime ( $_POST[$thisposttype . '_drawdate'] . $_POST[$thisposttype . '_drawtime']);
	    	update_post_meta($post->ID, $thisposttype . '_drawdate', $updatedrawd );
 	    }
	    
		if(isset($_POST[$thisposttype . '_loccountry'])) {
	    	update_post_meta($post->ID, $thisposttype . '_loccountry', $_POST[$thisposttype . '_loccountry'] );
	    }
	    
		if(isset($_POST[$thisposttype . '_locstate'])) {
	    	update_post_meta($post->ID, $thisposttype . '_locstate', $_POST[$thisposttype . '_locstate'] );
	    }
	    
		if(isset($_POST[$thisposttype . '_locsuburb'])) {
	    	update_post_meta($post->ID, $thisposttype . '_locsuburb', $_POST[$thisposttype . '_locsuburb'] );
	    }
	    
	    return $post;
}


function editColumns($columns) {
	global $newposttypes;
	for($index = 0; $index < count($newposttypes); $index++) {
		if ($newposttypes[$index]['enabled'] == true) {
			if ( substr(current_filter(), 12, -8) == $newposttypes[$index]['id'] ) {
				$mycolumns = $newposttypes[$index]['columns'];
				$myname = $newposttypes[$index]['id'];
			}
		}
	}
	
	$columns = array(
        'cb' => '<input type="checkbox" />',
    	'title' => 'Title'
	);
	
	for ($index = 0; $index < count($mycolumns); $index++) {
		$columns['col_'. $myname . '_' . $mycolumns[$index]] = $mycolumns[$index];
	}
    return $columns;
}

function new_custom_columns( $column ) {
	global $post, $newposttypes;
    $custom = get_post_custom();
    for($index = 0; $index < count($newposttypes); $index++) {
    	if ($newposttypes[$index]['enabled'] == true) {
		    switch ($column) {
		    	case 'col_' . $newposttypes[$index]['id'] . '_author':
		    		echo get_userdata($post->post_author)->display_name;
		    	break;
	            case 'col_' . $newposttypes[$index]['id'] . '_categories':
	                $categories = get_the_terms($post->ID, $newposttypes[$index]['id'] . '_category');
	                $categories_html = array();
		            if ( is_array($categories) && !array_key_exists( 'errors', $categories ) ) {
		            	foreach ($categories as $category) {
		            		array_push($categories_html, $category->name);
		            	}
		            	echo implode($categories_html, ", ");
		            } else {
		            	echo 'None';
		         	}
	            break;
	            case 'col_' . $newposttypes[$index]['id'] . '_tags':
	            	$tags = get_the_tags($post->ID, $newposttypes[$index]['id'] . '_tags');
	                $tags_html = array();
		            if ( is_array($tags) && !array_key_exists( 'errors', $tags ) ) {
		            	foreach ($tags as $tag) {
		            		array_push($tags_html, $tag->name);
		            	}
		            	echo implode($tags_html, ", ");
		            } else {
	                	echo 'No Tags';
	                }
	            break;
	            case 'col_' . $newposttypes[$index]['id'] . '_comments':
		    		echo $post->comment_count;
		    	break;
		    	case 'col_' . $newposttypes[$index]['id'] . '_date':
		    		echo mysql2date('Y/m/d', $post->post_date);
		    	break;
	            case 'col_' . $newposttypes[$index]['id'] . '_dates':
	                $startd = $custom[$newposttypes[$index]['id'] . '_startdate'][0];
	                $endd = $custom[$newposttypes[$index]['id'] . '_enddate'][0];
	                $startdate = date("F j, Y", $startd);
	                $enddate = date("F j, Y", $endd);
	                echo $startdate . '<br /><em>' . $enddate . '</em>';
	            break;
			}
    	}
    }
}

function updated_messages( $messages ) {
  global $post, $post_ID, $newposttypes;

  for($index = 0; $index < count($newposttypes); $index++) {
  	if ($newposttypes[$index]['enabled'] == true) {
	  	$messages[$newposttypes[$index]['id']] = array(
		    0 => '', // Unused. Messages start at index 1.
		    1 => sprintf( __($newposttypes[$index]['name'] . ' updated. <a href="%s">View post</a>'), esc_url( get_permalink($post_ID) ) ),
		    2 => __('Custom field updated.'),
		    3 => __('Custom field deleted.'),
		    4 => __($newposttypes[$index]['name'] . ' updated.'),
		    /* translators: %s: date and time of the revision */
		    5 => isset($_GET['revision']) ? sprintf( __($newposttypes[$index]['name'] . ' restored to revision from %s'), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
		    6 => sprintf( __($newposttypes[$index]['name'] . ' published. <a href="%s">View ' . $newposttypes[$index]['name'] . '</a>'), esc_url( get_permalink($post_ID) ) ),
		    7 => __($newposttypes[$index]['name'] . ' saved.'),
		    8 => sprintf( __($newposttypes[$index]['name'] . ' submitted. <a target="_blank" href="%s">Preview ' . $newposttypes[$index]['name'] . '</a>'), esc_url( add_query_arg( 'preview', 'true', get_permalink($post_ID) ) ) ),
		    9 => sprintf( __($newposttypes[$index]['name'] . ' scheduled for: <strong>%1$s</strong>. <a target="_blank" href="%2$s">Preview ' . $newposttypes[$index]['name'] . $newposttypes[$index]['name'] . '</a>'),
		      // translators: Publish box date format, see http://php.net/date
		      date_i18n( __( 'M j, Y @ G:i' ), strtotime( $post->post_date ) ), esc_url( get_permalink($post_ID) ) ),
		    10 => sprintf( __($newposttypes[$index]['name'] . ' draft updated. <a target="_blank" href="%s">Preview ' . $newposttypes[$index]['name'] . '</a>'), esc_url( add_query_arg( 'preview', 'true', get_permalink($post_ID) ) ) ),
	  	);
  	}
  }
  
  return $messages;
}

/***  meta ***/
function gp_events_meta () {
    global $post;
    $custom = get_post_custom($post->ID);

    $meta_sd = $custom["gp_events_startdate"][0];
    $meta_ed = $custom["gp_events_enddate"][0];
    $meta_st = $meta_sd;
    $meta_et = $meta_ed;
    
    $meta_loccountry = 'AU';
    $meta_locstate = $custom["gp_events_locstate"][0];
    $meta_locsuburb = $custom["gp_events_locsuburb"][0];

    $date_format = get_option('date_format');
    $time_format = get_option('time_format');

    if ($meta_sd == null) { $meta_sd = time(); $meta_ed = $meta_sd; $meta_st = 0; $meta_et = 0;}
    
    $clean_sd = date("D, M d, Y", $meta_sd);
    $clean_ed = date("D, M d, Y", $meta_ed);
    $clean_st = date($time_format, $meta_st);
    $clean_et = date($time_format, $meta_et);

    $states_au = array('NSW', 'QLD', 'VIC', 'WA', 'SA', 'NT', 'ACT', 'TAS');
    
    echo '<input type="hidden" name="gp_events-nonce" id="gp_events-nonce" value="' . wp_create_nonce( 'gp_events-nonce' ) . '" />';
    ?>
    <div class="tf-meta">
        <ul>
            <li><label>Start Date</label><input name="gp_events_startdate" class="tfdate" value="<?php echo $clean_sd; ?>" /></li>
            <li><label>Start Time</label><input name="gp_events_starttime" value="<?php echo $clean_st; ?>" /></li>
            <li><label>End Date</label><input name="gp_events_enddate" class="tfdate" value="<?php echo $clean_ed; ?>" /></li>
            <li><label>End Time</label><input name="gp_events_endtime" value="<?php echo $clean_et; ?>" /></li>
        </ul>
    </div>
    <div class="gp-meta">
        <ul>
            <li>
            	<label>State</label>
            		<select name="gp_events_locstate">
						<?php
						foreach ($states_au as $state) {
							if ($state == $meta_locstate) {$state_selected = ' selected';} else {$state_selected = '';}
		  					echo '<option value="' . $state . '"' . $state_selected . '>' . $state . '</option>';
						}
		  				?> 									
					</select>
            
            </li>
            <li><label>Suburb</label><input name="gp_events_locsuburb" value="<?php echo $meta_locsuburb; ?>" /></li>
        </ul>
        <input type="hidden" name="gp_events_loccountry" value="<?php echo $meta_loccountry; ?>" />
    </div>
    <?php
}

function gp_competitions_meta () {
    global $post;
    $custom = get_post_custom($post->ID);

    $meta_sd = $custom["gp_competitions_startdate"][0];
    $meta_ed = $custom["gp_competitions_enddate"][0];
    $meta_dd = $custom["gp_competitions_drawdate"][0];
    $meta_st = $meta_sd;
    $meta_et = $meta_ed;
    $meta_dt = $meta_dd;

    $date_format = get_option('date_format');
    $time_format = get_option('time_format');

    if ($meta_sd == null) { $meta_sd = time(); $meta_ed = $meta_sd; $meta_dd = $meta_sd; $meta_st = 0; $meta_et = 0; $meta_dt = 0;}
    
    $clean_sd = date("D, M d, Y", $meta_sd);
    $clean_ed = date("D, M d, Y", $meta_ed);
    $clean_dd = date("D, M d, Y", $meta_dd);
    $clean_st = date($time_format, $meta_st);
    $clean_et = date($time_format, $meta_et);
    $clean_dt = date($time_format, $meta_dt);

    echo '<input type="hidden" name="gp_competitions-nonce" id="gp_competitions-nonce" value="' . wp_create_nonce( 'gp_competitions-nonce' ) . '" />';
    ?>
    <div class="tf-meta">
        <ul>
            <li><label>Start Date</label><input name="gp_competitions_startdate" class="tfdate" value="<?php echo $clean_sd; ?>" /></li>
            <li><label>Start Time</label><input name="gp_competitions_starttime" value="<?php echo $clean_st; ?>" /></li>
            <li><label>Close Date</label><input name="gp_competitions_enddate" class="tfdate" value="<?php echo $clean_ed; ?>" /></li>
            <li><label>Close Time</label><input name="gp_competitions_endtime" value="<?php echo $clean_et; ?>" /></li>
            <li><label>Draw Date</label><input name="gp_competitions_drawdate" class="tfdate" value="<?php echo $clean_dd; ?>" /></li>
            <li><label>Draw Time</label><input name="gp_competitions_drawtime" value="<?php echo $clean_dt; ?>" /></li>
        </ul>
    </div>
    <?php
}

function hideUpdateNag() {
	if ( !get_user_role( array('administrator') ) ) {
    	remove_action( 'admin_notices', 'update_nag', 3 );
	}
}
add_action('admin_menu','hideUpdateNag');

/** MODIFY GENERIC POST TYPE VALUES **/
/* function change_post_menu_label() {
	global $menu;

	if ( get_user_role( array('subscriber') ) ) {
		unset($menu[2]); # dashboard
		unset($menu[4]); # seperator
		unset($menu[28]); # jobs
		unset($menu[30]); # people
		unset($menu[31]); # katie patrick
		unset($menu[32]); # product review
		unset($menu[35]); # green gurus
		unset($menu[34]); # ngo campaigns
		unset($menu[26]); # news
		unset($menu[10]); # media
	}
	
	if ( !get_user_role( array('administrator') ) ) {
		unset($menu[5]);
	}

	if ( get_user_role( array('contributor') ) ) {
		unset($menu[28]); # jobs
		unset($menu[29]); # competitions
		unset($menu[30]); # people
		unset($menu[31]); # katie patrick
		unset($menu[32]); # product review
		unset($menu[35]); # green gurus
	}
	
	if ( get_user_role( array('contributor', 'author', 'subscriber') ) ) {
		unset($menu[25]); # comments
		unset($menu[75]); # tools
		unset($menu[80]); # settings
	}
	
} */

function change_post_menu_label() {
        global $menu;

        if ( get_user_role( array('subscriber') ) ) {
                unset($menu[2]); # dashboard
                unset($menu[4]); # seperator
                unset($menu[29]); # people
                unset($menu[31]); # ngo campaigns
                unset($menu[26]); # news
                #unset($menu[27]); # events
                #unset($menu[30]); # advertorials
                unset($menu[10]); # media 
        }

        if ( !get_user_role( array('administrator') ) ) {
                unset($menu[5]); # posts
        }

        if ( get_user_role( array('contributor') ) ) {
                unset($menu[28]); # competitions
                unset($menu[29]); # people
        }
        
        if ( get_user_role( array('contributor', 'author', 'subscriber') ) ) {
                unset($menu[5]); # posts
                unset($menu[15]); # links
                unset($menu[20]); # pages
                unset($menu[25]); # comments
                unset($menu[60]); # appearance
                unset($menu[65]); # plugins
                #unset($menu[70]); # users
                unset($menu[75]); # tools
                unset($menu[80]); # settings
                unset($menu[100]); # gp-theme
        }

}

add_action( 'admin_menu', 'change_post_menu_label' );

/* This only works for labels array, wordpress ignores everything else. 

function change_post_object_label() {
	global $wp_post_types;

	$wp_post_types['post']->labels->name = 'News';
	$wp_post_types['post']->labels->singular_name = 'News';
	$wp_post_types['post']->labels->add_new = 'Add News';
	$wp_post_types['post']->labels->add_new_item = 'Add News';
	$wp_post_types['post']->labels->edit_item = 'Edit News';
	$wp_post_types['post']->labels->new_item = 'News';
	$wp_post_types['post']->labels->view_item = 'View News';
	$wp_post_types['post']->labels->search_items = 'Search News';
	$wp_post_types['post']->labels->not_found = 'No News found';
	$wp_post_types['post']->labels->not_found_in_trash = 'No News found in Trash';
	$wp_post_types['post']->labels->parent_item_colon = '';
	$wp_post_types['post']->labels->menu_name = 'News';
	$wp_post_types['post']->menu_icon = get_bloginfo( 'template_url' ).'/template/cup.png';
	$wp_post_types['post']->rewrite = array( 'slug'=>'news', 'with_front'=>false, 'pages'=>true, 'feeds'=>true );
	
	$newpost = array(
		'post' => array (
		    'labels' => array(
			    'name' => _x( 'News', 'post type general name' ),
			    'singular_name' => _x( 'News', 'post type singular name' ),
			    'add_new' => _x( 'Add New', 'News' ),
			    'add_new_item' => __( 'Add New News' ),
			    'edit_item' => __( 'Edit News' ),
			    'new_item' => __( 'New News' ),
			    'view_item' => __( 'View News' ),
			    'search_items' => __( 'Search News' ),
			    'not_found' =>  __( 'No news found' ),
			    'not_found_in_trash' => __( 'No news found in Trash' ),
			    'parent_item_colon' => '',
			    'menu_name' => 'News'
			),
		    'menu_icon' => get_bloginfo( 'template_url' ).'/template/cup.png',
		    'rewrite' => array( 'slug' => 'news' ,'with_front' => FALSE )
		)
	);
	$labels = array_merge($wp_post_types['post'], $newpost); 
}

add_action( 'init', 'change_post_object_label' );
 */


/** RE-ORDER ADMIN MENU **/
function menu_order_filter($menu) {
	$menu = array (
		0 => 'index.php',
		1 => 'separator1',
		2 => 'edit.php?post_type=gp_news',
		3 => 'edit.php?post_type=gp_events',
		4 => 'edit.php?post_type=gp_jobs',
		5 => 'edit.php?post_type=gp_competitions',
		6 => 'edit.php?post_type=gp_people',
		7 => 'edit.php?post_type=gp_advertorial',
		8 => 'edit.php?post_type=gp_productreview',
		9 => 'edit.php?post_type=gp_ngocampaign',
		10 => 'edit.php?post_type=gp_greengurus',
		11 => 'edit.php?post_type=gp_katiepatrick',
		12 => 'edit-comments.php',
		13 => 'separator2',
		14 => 'upload.php',
		15 => 'link-manager.php',
		16 => 'edit.php?post_type=page'
	);
	
	return $menu;
}
add_filter('custom_menu_order', create_function('', 'return true;'));
add_filter('menu_order', 'menu_order_filter');

/* function restrict_comment_editing( $caps, $cap ) {
	global $pagenow;
	
	if ( get_user_role( array('administrator', 'editor') ) ) {
		echo "test";
		if ( 'edit_post' == $cap && 'edit-comments.php' == $pagenow ) {
				$caps[] = 'moderate_comments';
		}
	}
 
	return $caps;
}
add_filter('map_meta_cap', 'restrict_comment_editing', 10, 3); */

/* ! undocumented functions */
function modify_capabilities () {
	$role = get_role('contributor');
	$role->add_cap('upload_files');	
	$role = get_role('subscriber');
	$role->add_cap('edit_posts');
	$role->add_cap('delete_posts');
	$role->add_cap('upload_files');
}
add_action( 'admin_init', 'modify_capabilities' );

/* note: http://codex.wordpress.org/Dashboard_Widgets_API */
function modify_dashboardwidgets () {
	global $wp_meta_boxes;
	if ( get_user_role( array('contributor', 'author', 'subscriber') ) ) {
		unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_quick_press']);
		unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_primary']);
		unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_secondary']);
		unset($wp_meta_boxes['dashboard']['normal']['core']['w3tc_latest']);
		unset($wp_meta_boxes['dashboard']['normal']['core']['w3tc_pagespeed']);
	}
}
add_action( 'wp_dashboard_setup', 'modify_dashboardwidgets' );

/** REDIRECT USER AFTER LOGIN **/
function redirect_user_to( $redirect_to, $user ) {
	if ( get_user_role( array('subscriber') ) ) {
		wp_safe_redirect('/wp-admin/profile.php');
	}
}
//add_filter( 'login_redirect', 'redirect_user_to', 10, 3 );

/** REMOVE FAVOURITE ACTIONS MENU **/
function remove_favorite_actions() {
    	return array();
}
add_filter( 'favorite_actions', 'remove_favorite_actions' );

/** SCREEN OPTIONS / HIDE WIDGETS FROM CUSTOM POST TYPES **/
# http://w-shadow.com/blog/2010/06/29/adding-stuff-to-wordpress-screen-options/
# http://w-shadow.com/blog/2010/06/30/add-new-buttons-alongside-screen-options-and-help/

function my_remove_meta_boxes(){
	global $wp_meta_boxes;

	if ( !get_user_role( array('administrator') ) ) {
		unset( $wp_meta_boxes['post'] );
		unset( $wp_meta_boxes['page'] );
	}
	
	if ( get_user_role( array('subscriber') ) ) {
		$disallowed_posttypes = array('gp_news', 'gp_jobs', 'gp_people', 'gp_katiepatrick', 'gp_productreview', 'gp_ngocampaign', 'gp_greengurus', 'gp_ngocampaign');
		$allowed_posttypes = array('gp_advertorial', 'gp_competitions', 'gp_events');
		$disallowed_metaboxes = array('wordbook_sectionid', 'tagsdiv-post_tag', 'gp_advertorial_categorydiv', 'gp_events_categorydiv', 'gp_competitions_categorydiv', 'pageparentdiv', 'postexcerpt', 'trackbacksdiv', 'postcustom', 'postexcerpt', 'commentstatusdiv', 'slugdiv', 'revisionsdiv');
		
		foreach ($disallowed_posttypes as $posttype) {
			unset( $wp_meta_boxes[$posttype] );
		}
		
		foreach ($allowed_posttypes as $posttype) {
			unset( $wp_meta_boxes[$posttype]['advanced']['default']['wordbook_sectionid'] );
			unset( $wp_meta_boxes[$posttype]['side']['core']['tagsdiv-post_tag'] );
			unset( $wp_meta_boxes[$posttype]['side']['core']['gp_advertorial_categorydiv'] );
			unset( $wp_meta_boxes[$posttype]['side']['core']['gp_events_categorydiv'] );
			unset( $wp_meta_boxes[$posttype]['side']['core']['gp_competitions_categorydiv'] );
			unset( $wp_meta_boxes[$posttype]['side']['core']['pageparentdiv'] );
			unset( $wp_meta_boxes[$posttype]['normal']['core']['postexcerpt'] );
			unset( $wp_meta_boxes[$posttype]['normal']['core']['trackbacksdiv'] );
			unset( $wp_meta_boxes[$posttype]['normal']['core']['postcustom'] );
			unset( $wp_meta_boxes[$posttype]['normal']['core']['postexcerpt'] );
			unset( $wp_meta_boxes[$posttype]['normal']['core']['commentstatusdiv'] );
			unset( $wp_meta_boxes[$posttype]['normal']['core']['slugdiv'] );
			unset( $wp_meta_boxes[$posttype]['normal']['core']['revisionsdiv'] );
		}
	}
	
	if ( get_user_role( array('contributor') ) ) {
		$disallowed_posttypes = array('gp_jobs', 'gp_people', 'gp_katiepatrick', 'gp_productreview', 'gp_greengurus');
		$allowed_posttypes = array('gp_news', 'gp_advertorial', 'gp_competitions', 'gp_events', 'gp_ngocampaign');
		$disallowed_metaboxes = array('wordbook_sectionid', 'tagsdiv-post_tag', 'gp_advertorial_categorydiv', 'gp_events_categorydiv', 'gp_competitions_categorydiv', 'pageparentdiv', 'postexcerpt', 'trackbacksdiv', 'postcustom', 'postexcerpt', 'commentstatusdiv', 'slugdiv', 'revisionsdiv');
		
		foreach ($disallowed_posttypes as $posttype) {
			unset( $wp_meta_boxes[$posttype] );
		}
		
		foreach ($allowed_posttypes as $posttype) {
			unset( $wp_meta_boxes[$posttype]['advanced']['default']['wordbook_sectionid'] );
			unset( $wp_meta_boxes[$posttype]['side']['core']['tagsdiv-post_tag'] );
			unset( $wp_meta_boxes[$posttype]['side']['core']['gp_advertorial_categorydiv'] );
			unset( $wp_meta_boxes[$posttype]['side']['core']['gp_events_categorydiv'] );
			unset( $wp_meta_boxes[$posttype]['side']['core']['gp_competitions_categorydiv'] );
			unset( $wp_meta_boxes[$posttype]['side']['core']['gp_ngocampaign_categorydiv'] );
			unset( $wp_meta_boxes[$posttype]['side']['core']['gp_news_categorydiv'] );
			unset( $wp_meta_boxes[$posttype]['side']['core']['pageparentdiv'] );
			unset( $wp_meta_boxes[$posttype]['normal']['core']['postexcerpt'] );
			unset( $wp_meta_boxes[$posttype]['normal']['core']['trackbacksdiv'] );
			unset( $wp_meta_boxes[$posttype]['normal']['core']['postcustom'] );
			unset( $wp_meta_boxes[$posttype]['normal']['core']['postexcerpt'] );
			unset( $wp_meta_boxes[$posttype]['normal']['core']['commentstatusdiv'] );
			unset( $wp_meta_boxes[$posttype]['normal']['core']['slugdiv'] );
			unset( $wp_meta_boxes[$posttype]['normal']['core']['revisionsdiv'] );
		}
	}
}
add_action( 'add_meta_boxes', 'my_remove_meta_boxes', 0 );


/** DISABLE FLASH UPLOADER **/
function disable_flash_uploader() {
	if ( !get_user_role( array('administrator') ) ) {
		return false;
	} else {
		return true;
	}
}
add_filter( 'flash_uploader', 'disable_flash_uploader', 1 );

/** ALLOWABLE FILE EXTENSION UPLOADS **/
function yoursite_wp_handle_upload_prefilter($file) {
	if ( get_user_role( array('subscriber') ) ) {
	  // This bit is for the flash uploader
	  if ($file['type']=='application/octet-stream' && isset($file['tmp_name'])) {
	    $file_size = getimagesize($file['tmp_name']);
	    if (isset($file_size['error']) && $file_size['error']!=0) {
	      $file['error'] = "Unexpected Error: {$file_size['error']}";
	      return $file;
	    } else {
	      $file['type'] = $file_size['mime'];
	    }
	  }
	  list($category,$type) = explode('/',$file['type']);
	  if ('image'!=$category || !in_array($type,array('jpg','jpeg','gif','png'))) {
	    $file['error'] = "Sorry, you can only upload a .GIF, a .JPG, or a .PNG image file.";
	  } else if ($post_id = (isset($_REQUEST['post_id']) ? $_REQUEST['post_id'] : false)) {
	    if (count(get_posts("post_type=attachment&post_parent={$post_id}"))>1)
	      $file['error'] = "Sorry, you cannot upload more than two (2) images.";
	  }
	}
	return $file;
}
add_filter('wp_handle_upload_prefilter', 'yoursite_wp_handle_upload_prefilter');

/** RESTRICT VIEWING OTHER USERS POSTS & MEDIA LIBRARY **/
function query_set_only_author( $wp_query ) {
	global $current_user;
	$the_admin_url = get_admin_url();
	$the_current_url = "http://" . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
	if ( substr( $the_current_url, 0, strlen($the_admin_url) ) == $the_admin_url ) {
		if ( get_user_role( array('subscriber', 'contributor') ) ) {
	        $wp_query->set( 'author', $current_user->ID );
	    }
	}  
}
add_action('pre_get_posts', 'query_set_only_author' );


/** REMOVE "QUICK EDIT" MENU FROM /WP-ADMIN/EDIT.PHP **/
function remove_quick_edit( $actions ) {
	if ( get_user_role( array('subscriber', 'contributor') ) ) {
		unset($actions['inline hide-if-no-js']);
	}
	return $actions;
}
add_filter('post_row_actions','remove_quick_edit',10,1);

function my_default_editor() {
    return 'tinymce';
}
add_filter( 'wp_default_editor', 'my_default_editor' );

/** ONLY ALLOW CERTAIN PAGES FOR SUBSCRIBERS (very hacky!) **/
function redirect_disallowed_pages () {
	if ( get_user_role( array('subscriber') ) ) {
		$admin_url = get_admin_url();
		$allowed_urls = array(
			$admin_url . 'profile.php', 
			$admin_url . 'post.php',
			$admin_url . 'admin-ajax.php',
			$admin_url . 'media-upload.php',
			$admin_url . 'wp-login.php',
			$admin_url . 'edit.php?post_type=gp_events', 
			$admin_url . 'post-new.php?post_type=gp_events', 
			$admin_url . 'edit.php?post_type=gp_competitions', 
			$admin_url . 'post-new.php?post_type=gp_competitions', 
			$admin_url . 'edit.php?post_type=gp_advertorial', 
			$admin_url . 'post-new.php?post_type=gp_advertorial'
		);
		
		$current_url = 'http://' . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];

		$redirect_this = true;
		foreach ($allowed_urls as $allowed_url) {
			if (substr($current_url, 0, strlen($allowed_url)) == $allowed_url) {
				$redirect_this = false;
			}
		}

		if ($redirect_this == true) {
			wp_safe_redirect('/wp-admin/profile.php');
		}
	}
}
add_action( 'admin_init', 'redirect_disallowed_pages' );

/** OVERRIDE 404 ON EMPTY ARCHIVE **/
function override_404() {
	global $wp_query;
	$args = array( 'public' => true, '_builtin' => false );
	$post_types = get_post_types($args);
	if ( in_array($wp_query->query_vars[post_type], $post_types) && $wp_query->is_404 == true ) {
		$wp_query->is_404 = false;
		$wp_query->is_archive = true;
		$wp_query->is_post_type_archive = true;
	}
}
#add_action('pre_get_posts', 'override_404');

function override_template() {
	global $wp_query;
	$args = array( 'public' => true, '_builtin' => false );
	$post_types = get_post_types($args);
	if ( in_array($wp_query->query_vars[post_type], $post_types) && $wp_query->is_404 == true ) {
		include(TEMPLATEPATH.'/index.php');
		exit;
	}
}
#add_action('template_redirect', 'override_template');

/** RECORD DATE/TIME OF LAST TIME USER LOGGED IN **/
function user_last_login($login) {
    global $user_ID;
    $user = get_userdatabylogin($login);
    update_usermeta($user->ID, 'last_login', $epochtime = strtotime('now'));
}
add_action('wp_login','user_last_login');

/** REDIRECT USER AFTER LOGIN/LOGOUT **/
function redirect_login() {
	wp_redirect($_SERVER['HTTP_REFERER']);
}
#add_action('wp_login','redirect_login');

function redirect_logout() {
	wp_redirect($_SERVER['HTTP_REFERER']);
}
#add_action('wp_logout ','redirect_logout');

/** CHANGE EXCERPT LENGTH **/
function new_excerpt_length($length) {
	return 20;
}
add_filter('excerpt_length', 'new_excerpt_length');

/** CHANGE END OF EXCERPT **/
function new_excerpt_more($more) {
	return '...';
}
add_filter('excerpt_more', 'new_excerpt_more');

/** GET RELATIVE DATE **/
function get_competitiondate($start, $end, $format = 2) {
	# A completely incomplete function

	if ( !is_numeric($start) && !is_numeric($end) ) {
		return false;
	}
	
	if (is_numeric($dateformat) && $dateformat > 1 ) {
		$dateformat = $dateformat - 1;
	} else {
		$dateformat == 1;
	}
	/*	
	$yearend = date('Y', $end);
	$yearnow = date('Y');
	
	$monthend = date('n', $end);
	$monthnow = date('n');
	
	$dayend = date('j', $end);
	$daynow = date('j');
	
	$hourend = (int)date('H', $end);
	$hournow = (int)date('H');
	
	$minuteend = (int)date('i', $end);
	$minutenow = (int)date('i');
	
	if ($yearstart == $yearnow && $yearend == $yearnow) {
		$displaydate = date('jS F', $end);
	} else {
		$displaydate = date('jS F Y', $end);
	}
	
	$displaydate = $displaydate . ' at ' . date('g:i a', $end);
	
	return $displaydate;
	*/
	
	$competitions_enddate_diff = _date_diff((int)$end, $start);

	if (date('Y', $start) - $competitions_enddate_diff['y'] != 1970) {
		if ($competitions_enddate_diff['y'] > 0) {
			$displaydate = 'Continue reading…';
		}
		 
		if ($competitions_enddate_diff['y'] <= 0 && $competitions_enddate_diff['m'] > 0) {
			if ($competitions_enddate_diff['m'] > 1) {$plural = 's';} else {$plural = '';}
			$displaydate = 'Competition closes in <span class="competition-close">' . $competitions_enddate_diff['m'] . ' month' . $plural . '</span>...';
		}
		
		if ($competitions_enddate_diff['m'] <= 0 && $competitions_enddate_diff['d'] > 0) {	
			if ($competitions_enddate_diff['d'] > 1) {$plural = 's';} else {$plural = '';}
			$displaydate = 'Competition closes in <span class="competition-close">' . $competitions_enddate_diff['d'] . ' day' . $plural . '</span>...';
		}
		
		if ($competitions_enddate_diff['d'] <= 0 && $competitions_enddate_diff['h'] > 0) {
			if ($competitions_enddate_diff['h'] > 1) {$plural = 's';} else {$plural = '';}
			$displaydate = 'Competition closes in <span class="competition-close">' . $competitions_enddate_diff['h'] . ' hour' . $plural . '</span>...';
		}
			
		if ($competitions_enddate_diff['h'] <= 0 && $competitions_enddate_diff['i'] > 0) {
			if ($competitions_enddate_diff['i'] > 1) {$plural = 's';} else {$plural = '';}
			$displaydate = 'Competition closes in <span class="competition-close">' . $competitions_enddate_diff['i'] . ' minute' . $plural . '</span>...';
		}
	
	}
	
	return $displaydate;
}

/** GET ABSOLUTE DATE **/
function get_absolutedate( $start, $end, $dateformat = 'jS F Y', $timeformat = 'g:i a', $abreviate = true, $dropyear = true, $join = array(' to ', ' at ', ' - ') ) {
	# A completely incomplete function
	
	$datetime_format = array(
		'year' => array('LoYy'),
		'month' => array('FmMnt'),
		'week' => array('W'),
		'day' => array('dDjlNSwz'),
		'time' => array('aAbgGhHisu'),
		'timezone' => array('eIOPTZ'),
		'full' => array('crU')
	);
	
	if ( !is_numeric($start) && !is_numeric($end) ) {
		return false;
	}
	
	if ( !is_string($dateformat) ) {
		$dateformat = 'jS F Y g:i a';
	}
	
	if ( !is_string($timeformat) ) {
		$timeformat = 'g:i a';
	}
	
	if ( empty($timeformat) ) {
		$showtime = false;
	} else {
		$showtime = true;
	}
	
	if ( !is_bool($abreviate) ) {
		$abreviate = true;
	}
	
	if ( !is_bool($dropyear) ) {
		$dropyear = true;
	}
	
	$yearstart = date('Y', $start);
	$yearend = date('Y', $end);
	$yearnow = date('Y');
	
	$monthstart = date('n', $start);
	$monthend = date('n', $end);
	$monthnow = date('n');
	
	$daystart = date('j', $start);
	$dayend = date('j', $end);
	$daynow = date('j');
	
	$hourstart = (int)date('H', $start);
	$hourend = (int)date('H', $end);
	$hournow = (int)date('H');
	
	$minutestart = (int)date('i', $start);
	$minuteend = (int)date('i', $end);
	$minutenow = (int)date('i');
	
	if ($yearstart == $yearnow && $yearend == $yearnow) {
		if ($daystart != $dayend && $monthstart != $monthend) {
			$displaydate = date('jS F', $start) . $join[0];
		}
		if ($daystart != $dayend && $monthstart == $monthend) {
			$displaydate = date('jS', $start) . $join[0];
		}
		$displaydate = $displaydate . date('jS F', $end);
	} else {
		if ($daystart != $dayend && $monthstart != $monthend && $yearstart != $yearend) {
			$displaydate = date('jS F Y', $start) . $join[0];
		}
		if ($daystart != $dayend && $monthstart != $monthend && $yearstart == $yearend) {
			$displaydate = date('jS F', $start) . $join[0];
		}
		if ($daystart != $dayend && $monthstart == $monthend && $yearstart == $yearend) {
			$displaydate = date('jS', $start) . $join[0];
		}
		$displaydate = $displaydate . date('jS F Y', $end);
	}
	
	if ( ( ( ( $hourstart * 60 ) + $minutestart ) < ( ( $hourend * 60 ) + $minuteend ) ) && $showtime == true) {
		$displaydate = $displaydate . $join[1] . date($timeformat, $start) . $join[2] . date($timeformat, $end);
	}
	
	return $displaydate;
	
}

function relevant_posts() {
	/*
	 * Todo: 
	 * 1. relevance by tag
	 * 2. relevance by category
	 * 3. add age to relevance score (you don't want posts that are too old? except maybe campaigns?)
	 * 4. find better way to get $posttype_title and $posttype_url (and then make sure this applies everywhere)
	 */
	if ( !is_single() ) {
		return false;
	}
	
	global $post, $wpdb;

	$post_id = $post->ID;
	$post_type = $post->post_type;
	$post_title = $post->post_title;
	$allowed_posttypes = array('gp_news', 'gp_ngocampaign', 'gp_advertorial', 'gp_people');
	
	if ( !in_array($post_type, $allowed_posttypes) ) {
		return false;
	}
	
	switch ($post_type) {
	    case 'gp_news':
	        $posttype_title = 'news';
	        $posttype_url = '/news';
	        break;
	    case 'gp_ngocampaign':
	      	$posttype_title = 'campaigns';
	      	$posttype_url = '/ngo-campaign';
	        break;
		case 'gp_advertorial':
	       	$posttype_title = 'stuff';
	       	$posttype_url = '/new-stuff';
	        break;
	    case 'gp_people':
	    	$posttype_title = 'people';
	    	$posttype_url = '/people';
	        break;
	}
	
	$querystr = $wpdb->prepare( "SELECT " . $wpdb->prefix . "posts.*, m0.meta_value as _thumbnail_id FROM " . $wpdb->prefix . "posts left join " . $wpdb->prefix . "postmeta as m0 on m0.post_id=" . $wpdb->prefix . "posts.ID and m0.meta_key='_thumbnail_id' WHERE post_status='publish' AND m0.meta_value >= 1 AND post_type = %s AND ID != %d AND MATCH (post_content) AGAINST (%s) LIMIT 5;", $post_type, $post_id, $post_title );
	$pageposts = $wpdb->get_results($querystr, OBJECT);
	$numPosts = $wpdb->num_rows-1;
	
	if ($pageposts && $numPosts != -1) {
		echo '<div id="relevant-posts"><span class="title">More <a href="' . $posttype_url . '">' . $posttype_title . '</a> you might like:</span>';
		foreach ($pageposts as $rpost) {
			setup_postdata($rpost);
			if ( has_post_thumbnail() ) {
				$imageArray = wp_get_attachment_image_src( get_post_thumbnail_id($rpost->ID), 'icon-thumbnail' );
				$imageURL = $imageArray[0];
				echo '<a href="' . get_permalink($rpost->ID) . '" class="hp_minithumb"><img src="' . $imageURL  . '" alt="' . get_the_title( get_post_thumbnail_id($rpost->ID) ) . '" /></a>';
			}
			?>
			<a href="<?php the_permalink(); ?>" title="Permalink to <?php esc_attr(the_title()); ?>" rel="bookmark" class="title"><?php the_title(); ?></a>
			<?php if ( $rpost->comment_status == 'open' ) { ?>
				<div class="comment-hp"><a href="<?php the_permalink(); ?>#comments"><span class="comment-mini"></span></a><a href="<?php the_permalink(); ?>#disqus_thread" class="comment-hp"><span class="comment-mini-number dsq-postid"><?php echo $rpost->comment_count; ?></span></a></div>
			<?php
			}
			echo '<div class="clear"></div></div>';
		}
		echo '</div>';
	}
}


/** SUBMIT POSTS AND REDIRECT TO CHARGIFY **/
add_filter('redirect_post_location', 'redirect_to_chargify');
function redirect_to_chargify() {
	global $current_user;
	
	$chargify_domain = "https://green-pages.chargify.com/";
	$chargify_prepop = "?first_name=" . $current_user->first_name . "&last_name=" . $current_user->last_name . "&email=" . $current_user->user_email . "&reference=" . $current_user->display_name;
	$chargify_advertorialurl = $chargify_domain . "h/33953/subscriptions/new" . $chargify_prepop;
	$chargify_competitionsurl = $chargify_domain . "h/33970/subscriptions/new" . $chargify_prepop;

	if ($_POST['publish'] == "Submit & Pay" && $_POST['post_status'] == "pending") {
		if (get_user_role( array('contributor') )) {
			if ($_POST['post_type'] == 'gp_advertorial') {
				wp_redirect($chargify_advertorialurl);
			}
		}
		if (get_user_role( array('subscriber') )) {
			if ($_POST['post_type'] == 'gp_advertorial') {
				wp_redirect($chargify_advertorialurl);
			}
			if ($_POST['post_type'] == 'gp_competitions') {
				wp_redirect($chargify_competitionsurl);
			}
		}
	} else {
		switch ($_POST['post_status']) {
			case 'publish':
				$msg = '6';
				break;
			case 'pending':
				$msg = '8';
				break;
			case 'draft':
				$msg = '10';
				break;
			case 'auto-draft':
				$msg = '10';
				break;
			case 'future':
				$msg = '9';
				break;
			case 'private':
				$msg = '7';
				break;
			case 'inherit':
				$msg = '5';
				break;
			case 'trash':
				$msg = '';
				break;
		}
		wp_redirect("/wp-admin/post-new.php?post_type=" . $_POST['post_type'] . "&message=" . $msg);
	}
}



/** EXTRA SPECIAL STUFF **/

/* Calculate years, months, days between dates.
 *  
 * See: http://stackoverflow.com/questions/676824/how-to-calculate-the-difference-between-two-dates-using-php
*/

function _date_range_limit($start, $end, $adj, $a, $b, $result)
{
    if ($result[$a] < $start) {
        $result[$b] -= intval(($start - $result[$a] - 1) / $adj) + 1;
        $result[$a] += $adj * intval(($start - $result[$a] - 1) / $adj + 1);
    }

    if ($result[$a] >= $end) {
        $result[$b] += intval($result[$a] / $adj);
        $result[$a] -= $adj * intval($result[$a] / $adj);
    }

    return $result;
}

function _date_range_limit_days($base, $result)
{
    $days_in_month_leap = array(31, 31, 29, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31);
    $days_in_month = array(31, 31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31);

    _date_range_limit(1, 13, 12, "m", "y", &$base);

    $year = $base["y"];
    $month = $base["m"];

    if (!$result["invert"]) {
        while ($result["d"] < 0) {
            $month--;
            if ($month < 1) {
                $month += 12;
                $year--;
            }

            $leapyear = $year % 400 == 0 || ($year % 100 != 0 && $year % 4 == 0);
            $days = $leapyear ? $days_in_month_leap[$month] : $days_in_month[$month];

            $result["d"] += $days;
            $result["m"]--;
        }
    } else {
        while ($result["d"] < 0) {
            $leapyear = $year % 400 == 0 || ($year % 100 != 0 && $year % 4 == 0);
            $days = $leapyear ? $days_in_month_leap[$month] : $days_in_month[$month];

            $result["d"] += $days;
            $result["m"]--;

            $month++;
            if ($month > 12) {
                $month -= 12;
                $year++;
            }
        }
    }

    return $result;
}

function _date_normalize($base, $result)
{
    $result = _date_range_limit(0, 60, 60, "s", "i", $result);
    $result = _date_range_limit(0, 60, 60, "i", "h", $result);
    $result = _date_range_limit(0, 24, 24, "h", "d", $result);
    $result = _date_range_limit(0, 12, 12, "m", "y", $result);

    $result = _date_range_limit_days(&$base, &$result);

    $result = _date_range_limit(0, 12, 12, "m", "y", $result);

    return $result;
}

/**
 * Accepts two unix timestamps.
 */
function _date_diff($one, $two)
{
    $invert = false;
    if ($one > $two) {
        list($one, $two) = array($two, $one);
        $invert = true;
    }

    $key = array("y", "m", "d", "h", "i", "s");
    $a = array_combine($key, array_map("intval", explode(" ", date("Y m d H i s", $one))));
    $b = array_combine($key, array_map("intval", explode(" ", date("Y m d H i s", $two))));

    $result = array();
    $result["y"] = $b["y"] - $a["y"];
    $result["m"] = $b["m"] - $a["m"];
    $result["d"] = $b["d"] - $a["d"];
    $result["h"] = $b["h"] - $a["h"];
    $result["i"] = $b["i"] - $a["i"];
    $result["s"] = $b["s"] - $a["s"];
    $result["invert"] = $invert ? 1 : 0;
    $result["days"] = intval(abs(($one - $two)/86400));

    if ($invert) {
        _date_normalize(&$a, &$result);
    } else {
        _date_normalize(&$b, &$result);
    }

    return $result;
}



function makeIso8601TimeStamp ($dateTime = '') {
    if (!$dateTime) {
        $dateTime = date('Y-m-d H:i:s');
    }
    
    if (is_numeric(substr($dateTime, 11, 1))) {
        $isoTS = substr($dateTime, 0, 10) . "T" . substr($dateTime, 11, 8) ."+10:00";
    } else {
        $isoTS = substr($dateTime, 0, 10);
    }
    
    return $isoTS;
}

?>