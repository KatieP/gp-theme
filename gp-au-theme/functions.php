<?php

$states_au = array('NSW', 'QLD', 'VIC', 'WA', 'SA', 'NT', 'ACT', 'TAS');

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

/*
Remove action from plugin - wp-email-login.
Javascript generates errors if element isn't found. Element is also different for plugin - simple modal-login.
*/
remove_action( 'login_form', 'username_or_email_login' );

function new_username_or_email_login() { ?>
	<script type="text/javascript">
		var regtitle = document.getElementById('loginform');
		
		if (regtitle != undefined) {
			if (regtitle.childNodes[1] != undefined && regtitle.childNodes[1].childNodes[1] != undefined && regtitle.childNodes[1].childNodes[1].childNodes[0] != undefined && regtitle.childNodes[1].childNodes[1].childNodes[0].length > 0) {
				regtitle.childNodes[1].childNodes[1].childNodes[0].nodeValue = '<?php echo esc_js( __( 'Username or Email Address', 'email-login' ) ); ?>';
			}
		}
		
		// Error Messages
		if ( document.getElementById('login_error') )
			document.getElementById('login_error').innerHTML = document.getElementById('login_error').innerHTML.replace( '<?php echo esc_js( __( 'username' ) ); ?>', '<?php echo esc_js( __( 'Username or Email' , 'email-login' ) ); ?>' );
	</script>
<?php } 

add_action( 'login_form', 'new_username_or_email_login' );

add_filter( 'admin_footer_text', 'gp_add_admin_footer' );
function gp_add_admin_footer() {
	echo 'Welcome to the Green Pages backend editor! Go back to <a href="http://www.thegreenpages.com.au/">front end</a>';
}

add_filter( 'update_footer', 'gp_remove_version_footer', 9999);
function gp_remove_version_footer() {return '&nbsp;';}

add_filter('wp_mail_from','yoursite_wp_mail_from');
function yoursite_wp_mail_from($content_type) {
	return 'no-reply@thegreenpages.com.au';
}

add_filter('wp_mail_from_name','yoursite_wp_mail_from_name');
function yoursite_wp_mail_from_name($name) {
	return 'Green Pages';
}

/*** MANUALLY SETS WORD LENGTH OF EXCERPT FROM POST SHOWN IN INDEX AND PROFILE PAGES ***/
add_filter( 'excerpt_length', 'custom_excerpt_length', 999 );
function custom_excerpt_length( $length ) {
	return 25;
}

function add_jquery_data() { 
	global $current_user, $post;
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
			//$("table.form-table:eq(1) tr:eq(3)").hide(); // nickname
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
			    buttonImage: '<?php echo get_bloginfo('template_url'); ?>/template/icon-datepicker.png',
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
	if ((get_post_type() == 'gp_advertorial' || get_post_type() == 'gp_competitions') && (get_post_status( $post->ID ) == 'auto-draft' || get_post_status( $post->ID ) == 'draft')) {
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
		
		<!--[if lte IE 6]>
			<link type="text/css" rel="stylesheet" media="all" href="<?php echo get_bloginfo('template_url'); ?>/template/ie6.css" />
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

/** ADD CUSTOM JQUERY THEME FOR DATEPICKER / CALENDAR AND DIALOG  **/
function gp_theme_scripts() {
	if(!is_admin()){
		wp_deregister_script('jquery-ui-core');
		wp_register_script('jquery-ui-core', get_bloginfo('template_url') . '/template/jquery.ui.core.js');
	    wp_enqueue_script('jquery-ui-core');
	    
	    wp_deregister_script('jquery-ui-widget');
	    wp_register_script('jquery-ui-widget', get_bloginfo('template_url') . '/template/jquery.ui.widget.js');
	    wp_enqueue_script('jquery-ui-widget');
		
		wp_register_script('jquery-ui-datepicker', get_bloginfo('template_url') . '/template/jquery.ui.datepicker.js');
	    wp_enqueue_script('jquery-ui-datepicker');

		wp_register_script('jquery-ui-dialog', get_bloginfo('template_url') . '/template/jquery.ui.dialog.js');
	    wp_enqueue_script('jquery-ui-dialog');	    
	    
		wp_register_style('jquery-ui-custom-css', get_bloginfo('template_url') . '/template/custom-theme/jquery-ui-1.8.22.custom.css');
    	wp_enqueue_style('jquery-ui-custom-css');  	
	}
}
add_action('init', 'gp_theme_scripts');

/** ADD WEB FONT FONTFACES **/
function gp_theme_load_fonts() {
    wp_register_style('gp_web_fonts', get_bloginfo('template_url') . '/template/styles/fontfaces.css');
    wp_enqueue_style( 'gp_web_fonts');
}
add_action('init', 'gp_theme_load_fonts');

/** ADD REWRITE RULES **/
function change_author_permalinks() {
    global $wp_rewrite;
   	$wp_rewrite->author_base = 'profile'; # see this for changing slug by role - http://wordpress.stackexchange.com/questions/17106/change-author-base-slug-for-different-roles
    #$wp_rewrite->flush_rules();
}
add_action('init','change_author_permalinks');

/** AUTHOR EDIT REWRITE RULE **/
add_action( 'author_rewrite_rules', 'edit_author_slug' ); #new-edit
function edit_author_slug( $author_rules )
{
    $author_rules['profile/([^/]+)/edit/?$'] = 'index.php?author_name=$matches[1]&author_edit=1';
    $author_rules['profile/([^/]+)/edit/account?$'] = 'index.php?author_name=$matches[1]&author_edit=2';
    $author_rules['profile/([^/]+)/edit/locale?$'] = 'index.php?author_name=$matches[1]&author_edit=3';
    $author_rules['profile/([^/]+)/edit/notifications?$'] = 'index.php?author_name=$matches[1]&author_edit=4';
    $author_rules['profile/([^/]+)/edit/newsletters?$'] = 'index.php?author_name=$matches[1]&author_edit=5';
    $author_rules['profile/([^/]+)/edit/privacy?$'] = 'index.php?author_name=$matches[1]&author_edit=6';
    $author_rules['profile/([^/]+)/edit/password?$'] = 'index.php?author_name=$matches[1]&author_edit=7';
    $author_rules['profile/([^/]+)/edit/admin?$'] = 'index.php?author_name=$matches[1]&author_edit=8';
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
  global $states_au;

	$newrules = array();
	
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
		$template = TEMPLATEPATH . '/singlecolumn.php';
		if ( file_exists($template) ) {
			include($template);
			exit;
		}
	}
}
add_action('template_redirect', 'twocolumn_template'); #new-edit


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
	
	
	global $current_user, $current_site, $gp, $wpdb;
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
	
/**	
	
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

**/	
	
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
		$contributors_donate_url = get_the_author_meta( 'contributors_donate_url', $user->ID );
		$contributors_join_url = get_the_author_meta( 'contributors_join_url', $user->ID );
		$contributors_letter_url = get_the_author_meta( 'contributors_letter_url', $user->ID );
		$contributors_petition_url = get_the_author_meta( 'contributors_petition_url', $user->ID );
		$contributors_volunteer_url = get_the_author_meta( 'contributors_volunteer_url', $user->ID );
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
		
		<h3>Activist Bar Buttons</h3>
		
		<table class="form-table">
			<tr>
				<th><label for="contributors_donate_url">Donate</label></th>
				<td><input type="text" maxlength="255" name="contributors_donate_url" id="contributors_donate_url" class="regular-text" value="' . $contributors_donate_url . '" /><br />
				<span class="description">Enter the url you use to accept donations and a \'Donate\' button will be visible on each 
				post you create and your profile page!</span></td>
			</tr>
			<tr>
				<th><label for="contributors_join_url">Join</label></th>
				<td><input type="text" maxlength="255" name="contributors_join_url" id="contributors_join_url" class="regular-text" value="' . $contributors_join_url . '" /><br />
				<span class="description">Enter the url you use to sign up new members and a \'Join\' button 
				will be visible on each post you create and your profile page!</span></td>
			</tr>
			<tr>
				<th><label for="contributors_letter_url">Send Letter</label></th>
				<td><input type="text" maxlength="255" name="contributors_letter_url" id="contributors_letter_url" class="regular-text" value="' . $contributors_letter_url . '" /><br />
				<span class="description">Enter the url you use to encourage sending a letter to a 
				decision maker and a \'Send Letter\' button will be visible on each post you create and your profile page!</span></td>
			</tr>
			<tr>
				<th><label for="contributors_petition_url">Sign Petition</label></th>
				<td><input type="text" maxlength="255" name="contributors_petition_url" id="contributors_petition_url" class="regular-text" value="' . $contributors_petition_url . '" /><br />
				<span class="description">Enter the url you use to encourage signing a petition 
				and a \'Sign a petition\' button will be visible on each post you create and your profile page!</span></td>
			</tr>
			<tr>
				<th><label for="contributors_volunteer_url">Volunteer</label></th>
				<td><input type="text" maxlength="255" name="contributors_volunteer_url" id="contributors_volunteer_url" class="regular-text" value="' . $contributors_volunteer_url . '" /><br />
				<span class="description">Enter the url you use to sign up volunteers and a \'Volunteer\' button 
				will be visible on each post you create and your profile page!</span></td>
			</tr>		
		</table>		');
	}

/**		
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
**/
	?>
	
	<!-- </table> -->
	
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
		
		$subscription_user = get_the_author_meta( $wpdb->prefix . 'subscription', $user->ID );

		$cm_lists = $gp->campaignmonitor[$current_site->id]['lists'];
		if ( is_array( $cm_lists ) ) {
			foreach ( $cm_lists as $key => $value ) {
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
					<th><label for="' . esc_attr($key) . '">' . $value['profile_text'] . '</label></th>
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


    /** HIDE THE FOLLOWING CODE BLOCK WITH MISC META DATA FROM NON ADMINS, CODE STILL NEEDS TO RUN THOUGH 
     ** OTHERWISE EVERYTIME A NON ADMIN UPDATES THEIR PROFILE PAGE THE META DATA IS LOST **/
	if ( !get_user_role( array('administrator') ) ) {
		echo '<div class="hidden">';
	}
	
	if ( get_the_author_meta( 'reg_advertiser', $user->ID ) == true ) {$checkthis = ' checked="checked"';} else {$checkthis = '';}
	echo ('
    	<h3>Accounts Types</h3>
		<table class="form-table">
			<tr><th>Advertiser</th><td><input type="checkbox" name="reg_advertiser" id="reg_advertiser" value="reg_advertiser"' . $checkthis . ' /></td></tr>
		</table>
		');
		/** SET AND DISPLAY DIRECTORY ID AND URL STRINGS AND YOUTUBE ID FOR VIDEO NEWS IFRAME**/
		$old_crm_id = get_the_author_meta( 'old_crm_id', $user->ID );
		$wp_id = $user->ID;
		$directory_page_url = get_the_author_meta( 'directory_page_url', $user->ID );
		$chargify_self_service_page_url = get_the_author_meta( 'chargify_self_service_page_url', $user->ID );
		$video_news_id = get_the_author_meta( 'video_news_id', $user->ID );		
		echo ('
		<h3>Miscellaneous</h3>
		
		<table class="form-table">
			<tr>
				<th><label for="old_crm_id">Old CRM ID</label></th>
				<td><input type="text" 	name="old_crm_id" id="old_crm_id" class="regular-text" maxlength="6" value="' . esc_attr($old_crm_id) . '" /><br />
				<span class="description">This is used to map business ID\'s used in our old CRM to the new ID\'s in Wordpress.</span></td>
			</tr>
			<tr>
				<th><label for="wp_id">Wordpress ID</label></th>
				<td><span>' . esc_attr($wp_id) . '</span><br />
				<span class="description">This is used to map the new ID\'s in Wordpress to the business ID\'s used in our old CRM.</span></td>
			</tr>
			<tr>
				<th><label for="directory_page_url">Directory Page URL</label></th>
				<td><input type="text" 	name="directory_page_url" id="directory_page_url" class="regular-text" maxlength="255" value="' . esc_attr($directory_page_url) . '" /><br />
				<span class="description">This is used to provide a link to the members Directory Page from their profile page</span></td>
			</tr>
			<tr>
				<th><label for="chargify_self_service_page_url">Chargify Self-Service Page Url</label></th>
				<td><input type="text" 	name="chargify_self_service_page_url" id="chargify_self_service_page_url" class="regular-text" maxlength="255" value="' . esc_attr($chargify_self_service_page_url) . '" /><br />
				<span class="description">This is used to provide a link to the members Chargify self service from their profile page</span></td>
			</tr>			
			<tr>
				<th><label for="video_news_id">Video News ID</label></th>
				<td><input type="text" 	name="video_news_id" id="video_news_id" class="regular-text" maxlength="255" value="' . esc_attr($video_news_id) . '" /><br />
				<span class="description">This is used to insert the ID into the iframe that displays the video news on right sidebar</span></td>
			</tr>			
		</table>
	');

	if ( !get_user_role( array('administrator') ) ) {	
		echo '</div>';
	}
	?>

<?php

} 

add_action( 'personal_options_update', 'my_save_extra_profile_fields' );
add_action( 'edit_user_profile_update', 'my_save_extra_profile_fields' );

function my_save_extra_profile_fields( $user_id ) {
	global $current_site, $gp, $wpdb;
	
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
	
	$reg_advertiser = isset( $_POST[ 'reg_advertiser' ] ) ? true : false;
	
	update_usermeta($user_id, 'bio_change', $_POST['bio_change'] );
	update_usermeta($user_id, 'bio_projects', $_POST['bio_projects'] );
	update_usermeta($user_id, 'bio_stuff', $_POST['bio_stuff'] );
	update_usermeta($user_id, 'locale_postcode', $_POST['locale_postcode'] );
	update_usermeta($user_id, 'employment_jobtitle', $_POST['employment_jobtitle'] );
	update_usermeta($user_id, 'employment_currentemployer', $_POST['employment_currentemployer'] );
	update_usermeta($user_id, 'editors_blurb', $_POST['editors_blurb'] );
	update_usermeta($user_id, 'contributors_blurb', $_POST['contributors_blurb'] );
	update_usermeta($user_id, 'contributors_posttagline', $_POST['contributors_posttagline'] );
	update_usermeta($user_id, 'contributors_donate_url', $_POST['contributors_donate_url'] );
	update_usermeta($user_id, 'contributors_join_url', $_POST['contributors_join_url'] );
	update_usermeta($user_id, 'contributors_letter_url', $_POST['contributors_letter_url'] );
	update_usermeta($user_id, 'contributors_petition_url', $_POST['contributors_petition_url'] );
	update_usermeta($user_id, 'contributors_volunteer_url', $_POST['contributors_volunteer_url'] );	
	update_usermeta($user_id, 'notification', $notification_post );
	update_usermeta($user_id, 'reg_advertiser', $reg_advertiser );
	update_usermeta($user_id, 'old_crm_id', $_POST['old_crm_id'] );
	update_usermeta($user_id, 'directory_page_url', $_POST['directory_page_url'] );
	update_usermeta($user_id, 'chargify_self_service_page_url', $_POST['chargify_self_service_page_url'] );
	update_usermeta($user_id, 'video_news_id', $_POST['video_news_id'] );
	
	/*** UPDATE CAMPAIGN MONITOR - USER GREENRAZOR SUBSCRIPTION ***/
	$subscription_post = array();
	if ( is_array( $gp->campaignmonitor ) ) {
		foreach ( $gp->campaignmonitor as $key => $value ) {
			if ($key == $current_site->id) {
				foreach ( $value['lists'] as $list_key => $list_value ) {
					if (!cm_subscribe($list_key, $_POST[$list_key], $user_id)) {
						$_POST[$list_key] = false;
					}
					$subscription_post = $subscription_post + array($list_key => $_POST[$list_key]);
				}
				update_usermeta($user_id, $wpdb->prefix . 'subscription', $subscription_post);
			}
		}
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
    'label' => __( 'Products' ),
    'labels' => array(
	    'name' => _x( 'Products', 'post type general name' ),
	    'singular_name' => _x( 'Product', 'post type singular name' ),
	    'add_new' => _x( 'Add New ($89)', 'Product' ),
	    'add_new_item' => __( 'Post your eco friendly product here for $89! &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; All posts are approved within 24 hours.' ),
	    'edit_item' => __( 'Edit Product' ),
	    'new_item' => __( 'New Product' ),
	    'view_item' => __( 'View Product' ),
	    'search_items' => __( 'Search Products' ),
	    'not_found' =>  __( 'No Products Found' ),
	    'not_found_in_trash' => __( 'No Products Found in Trash' ),
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
    'rewrite' => array( 'slug' => 'eco-friendly-products' ,'with_front' => FALSE ),
    'supports'=> array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'trackbacks', 'comments', 'revisions', 'page-attributes', 'custom-fields' ),
    'show_in_nav_menus' => true,
    'taxonomies' => array( 'gp_advertorial_category', 'post_tag'),
	'has_archive' => true
);

$advertorialtaxonomy = array(
	'label' => __( 'Product Category' ),
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


/* Projects */
    
$projectsargs = array(
    'label' => __( 'Projects' ),
    'labels' => array(
	    'name' => _x( 'Projects', 'post type general name' ),
	    'singular_name' => _x( 'Project', 'post type singular name' ),
	    'add_new' => _x( 'Add New', 'Project' ),
	    'add_new_item' => __( 'Add New Project' ),
	    'edit_item' => __( 'Edit Project' ),
	    'new_item' => __( 'New Project' ),
	    'view_item' => __( 'View Project' ),
	    'search_items' => __( 'Search Projects' ),
	    'not_found' =>  __( 'No projects found' ),
	    'not_found_in_trash' => __( 'No projects found in Trash' ),
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
    'rewrite' => array( 'slug' => 'projects' ,'with_front' => FALSE ),
    'supports'=> array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'trackbacks', 'comments', 'revisions', 'page-attributes', 'custom-fields' ),
    'show_in_nav_menus' => true,
    'taxonomies' => array( 'gp_projects_category', 'post_tag'),
	'has_archive' => true
);

$projectstaxonomy = array(
	'label' => __( 'Project Category' ),
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
	'rewrite' => array( 'slug' => 'projects-category' )
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
	unset($projectsargs['supports']);
	unset($eventargs['supports']);
	$newsargs['supports'] = array( 'title', 'editor', 'thumbnail', 'excerpt', 'revisions' ) ;
	$projectsargs['supports'] = array( 'title', 'editor', 'thumbnail', 'excerpt', 'revisions' );
	$eventargs['supports'] = array( 'title', 'editor', 'thumbnail', 'excerpt', 'revisions' );
} 

$sitemaptypes = array(
	array('id' => 'sitemap', 'name' => 'Sitemap'),
	array('id' => 'googlenews', 'name' => 'Google News')
);

$newposttypes = array(
	array('id' => 'gp_news', 'name' => 'News', 'plural' => false, 'GPmeta' => array(array('id' => 'postGeoLoc', 'title' => 'Post Location')), 'args' => $newsargs, 'taxonomy' => $newstaxonomy, 'columns' => array('author', 'categories', 'tags', 'comments', 'date'), 'enabled' => true, 'priority' => '1', 'changefreq' => 'monthly', 'keywords' => 'science, environment'),
	array('id' => 'gp_events', 'name' => 'Event', 'plural' => true, 'GPmeta' => array(array('id' => 'postGeoLoc', 'title' => 'Event Location'), array('id' => 'postEventDate', 'title' => 'Event Date')), 'args' => $eventargs, 'taxonomy' => $eventtaxonomy, 'columns' => array('author', 'categories', 'tags', 'comments', 'date', 'dates'), 'enabled' => true, 'priority' => '0.6', 'changefreq' => 'monthly', 'keywords' => 'science, environment'),
	array('id' => 'gp_jobs', 'name' => 'Job', 'plural' => true, 'GPmeta' => array(array('id' => 'postGeoLoc', 'title' => 'Post Location')), 'args' => $jobargs, 'taxonomy' => $jobtaxonomy, 'columns' => array('author', 'categories', 'tags', 'comments', 'date'), 'enabled' => false, 'priority' => '0.6', 'changefreq' => 'monthly', 'keywords' => 'science, environment'),
	array('id' => 'gp_competitions', 'name' => 'Competition', 'plural' => true, 'GPmeta' => array(array('id' => 'postGeoLoc', 'title' => 'Post Location'), array('id' => 'postCompetitionDate', 'title' => 'Competition Date')), 'args' => $competitionargs, 'taxonomy' => $competitiontaxonomy, 'columns' => array('author', 'categories', 'tags', 'comments', 'date', 'dates'), 'enabled' => true, 'priority' => '0.6', 'changefreq' => 'monthly', 'keywords' => 'science, environment'),
	array('id' => 'gp_people', 'name' => 'People', 'plural' => false, 'GPmeta' => array(array('id' => 'postGeoLoc', 'title' => 'Post Location')), 'args' => $peopleargs, 'taxonomy' => $peopletaxonomy, 'columns' => array('author', 'categories', 'tags', 'comments', 'date'), 'enabled' => true, 'priority' => '0.6', 'changefreq' => 'monthly', 'keywords' => 'science, environment'),
	array('id' => 'gp_katiepatrick', 'name' => 'Katie Patrick', 'plural' => false, 'GPmeta' => array(array('id' => 'postGeoLoc', 'title' => 'Post Location')), 'args' => $katiepatrickargs, 'taxonomy' => $katiepatricktaxonomy, 'columns' => array('author', 'categories', 'tags', 'comments', 'date'), 'enabled' => false, 'priority' => '0.6', 'changefreq' => 'monthly', 'keywords' => 'science, environment'),
	array('id' => 'gp_productreview', 'name' => 'Product Review', 'plural' => false, 'GPmeta' => array(array('id' => 'postGeoLoc', 'title' => 'Post Location')), 'args' => $productreviewargs, 'taxonomy' => $productreviewtaxonomy, 'columns' => array('author', 'categories', 'tags', 'comments', 'date'), 'enabled' => false, 'priority' => '0.6', 'changefreq' => 'monthly', 'keywords' => 'science, environment'),
	array('id' => 'gp_advertorial', 'name' => 'Product', 'plural' => true, 'GPmeta' => array(array('id' => 'postGeoLoc', 'title' => 'Post Location'), array('id' => 'postProductURL', 'title' => 'Purchase URL')), 'args' => $advertorialargs, 'taxonomy' => $advertorialtaxonomy, 'columns' => array('author', 'categories', 'tags', 'comments', 'date'), 'enabled' => true, 'priority' => '0.6', 'changefreq' => 'monthly', 'keywords' => 'science, environment'),
	array('id' => 'gp_projects', 'name' => 'Project', 'plural' => true, 'GPmeta' => array(array('id' => 'postGeoLoc', 'title' => 'Post Location')), 'args' => $projectsargs, 'taxonomy' => $projectstaxonomy, 'columns' => array('author', 'categories', 'tags', 'comments', 'date'), 'enabled' => true, 'priority' => '0.6', 'changefreq' => 'monthly', 'keywords' => 'science, environment'),
	array('id' => 'gp_greengurus', 'name' => 'Green Gurus', 'plural' => false, 'GPmeta' => array(array('id' => 'postGeoLoc', 'title' => 'Post Location')), 'args' => $greengurusargs, 'taxonomy' => $greengurustaxonomy, 'columns' => array('author', 'categories', 'tags', 'comments', 'date'), 'enabled' => false, 'priority' => '0.6', 'changefreq' => 'monthly', 'keywords' => 'science, environment')
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
add_theme_support( 'post-thumbnails', array( 'post', 'gp_news', 'gp_events', 'gp_competitions', 'gp_jobs', 'gp_people', 'gp_advertorial', 'gp_projects', 'gp_katiepatrick', 'gp_productreview', 'gp_greengurus' ) );
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
	                $startd = isset($custom[$newposttypes[$index]['id'] . '_startdate'][0]) ? date("F j, Y", $custom[$newposttypes[$index]['id'] . '_startdate'][0]) : "";
	                $endd = isset($custom[$newposttypes[$index]['id'] . '_enddate'][0]) ? date("F j, Y", $custom[$newposttypes[$index]['id'] . '_enddate'][0]) : "";
	                echo $startd . '<br /><em>' . $endd . '</em>';
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
                #unset($menu[31]); # projects
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
                unset($menu[101]); # gp-directory
                unset($menu[102]); # syndication
				unset($menu[103]); # performance
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
		9 => 'edit.php?post_type=gp_projects',
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
		$disallowed_posttypes = array('gp_news', 'gp_jobs', 'gp_people', 'gp_katiepatrick', 'gp_productreview', 'gp_greengurus');
		$allowed_posttypes = array('gp_advertorial', 'gp_competitions', 'gp_events', 'gp_projects');
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
		$allowed_posttypes = array('gp_news', 'gp_advertorial', 'gp_competitions', 'gp_events', 'gp_projects');
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
			unset( $wp_meta_boxes[$posttype]['side']['core']['gp_projects_categorydiv'] );
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
			$admin_url . 'post-new.php?post_type=gp_advertorial',
			$admin_url . 'edit.php?post_type=gp_projects', 
			$admin_url . 'post-new.php?post_type=gp_projects'
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
	
	return '<div class="competition-enddate">'.$displaydate.'</div>';
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
	 * 3. add age to relevance score (you don't want posts that are too old? except maybe Projects?)
	 * 4. find better way to get $posttype_title and $posttype_url (and then make sure this applies everywhere)
	 */
	if ( !is_single() ) {
		return false;
	}
	
	global $post, $wpdb;

	$post_id = $post->ID;
	$post_type = $post->post_type;
	$post_title = $post->post_title;
	$allowed_posttypes = array('gp_news', 'gp_projects', 'gp_advertorial', 'gp_people');
	
	if ( !in_array($post_type, $allowed_posttypes) ) {
		return false;
	}
	
	switch ($post_type) {
	    case 'gp_news':
	        $posttype_title = 'news';
	        $posttype_url = '/news';
	        break;
	    case 'gp_projects':
	      	$posttype_title = 'projects';
	      	$posttype_url = '/projects';
	        break;
		case 'gp_advertorial':
	       	$posttype_title = 'stuff';
	       	$posttype_url = '/eco-friendly-products';
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
			echo '<div class="relevant-item">';
			if ( has_post_thumbnail() ) {
				$imageArray = wp_get_attachment_image_src( get_post_thumbnail_id($rpost->ID), 'icon-thumbnail' );
				$imageURL = $imageArray[0];
				echo '<a href="' . get_permalink($rpost->ID) . '" class="hp_minithumb"><img src="' . $imageURL  . '" alt="' . get_the_title( get_post_thumbnail_id($rpost->ID) ) . '" /></a>';
			}
			?>
			<a href="<?php echo get_permalink($rpost->ID); ?>" title="Permalink to <?php esc_attr($rpost->post_title); ?>" rel="bookmark" class="title"><?php echo $rpost->post_title; ?></a>
			<?php if ( $rpost->comment_status == 'open' ) { ?>
				<div class="clear"></div><div class="comment-hp"><a href="<?php echo get_permalink($rpost->ID); ?>#comments"><span class="comment-mini"></span></a><a href="<?php echo get_permalink($rpost->ID); ?>#disqus_thread" class="comment-hp"><span class="comment-mini-number dsq-postid"><?php echo $rpost->comment_count; ?></span></a></div>
			<?php
			}
			echo '<div class="clear"></div></div>';
		}
		echo '</div>';
	}
}

/** SHOWS THE NEXT 5 UP COMING EVENTS UNDER THE EVENT CALENDAR IN SIDEBAR-RIGHT **/ 
function coming_events() {
					
	global $wpdb;
	global $post;
	global $states_au;
	
	$epochtime = strtotime('now');
	
	if ( in_array(get_query_var( 'filterby_state' ), $states_au) ) {
		$filterby_state = "AND m3.meta_value='" . get_query_var( 'filterby_state' ) . "'";
    } else {
    	$filterby_state = "";
    }
    
	/** SQL QUERY FOR COMING EVENTS **/
	$metas = array('_thumbnail_id', 'gp_events_enddate', 'gp_events_startdate', 'gp_events_locstate', 'gp_events_locsuburb', 'gp_events_loccountry');
	foreach ($metas as $i=>$meta_key) {
        $meta_fields[] = 'm' . $i . '.meta_value as ' . $meta_key;
        $meta_joins[] = ' left join ' . $wpdb->postmeta . ' as m' . $i . ' on m' . $i . '.post_id=' . $wpdb->posts . '.ID and m' . $i . '.meta_key="' . $meta_key . '"';
    }
    
    $querystr = "SELECT " . $wpdb->prefix . "posts.*, " .  join(',', $meta_fields) . " 
    			 FROM $wpdb->posts ";
    $querystr .=  join(' ', $meta_joins);
    $querystr .= "WHERE post_status='publish' 
    					AND post_type='gp_events' 
    					AND m5.meta_value='AU' " . $filterby_state . " 
    					AND CAST(CAST(m1.meta_value AS UNSIGNED) AS SIGNED) >= " . $epochtime . " 
    			  ORDER BY gp_events_startdate;";
					
	$pageposts = $wpdb->get_results($querystr, OBJECT);
	$numPosts = $wpdb->num_rows-1;
					
	if ($pageposts && $numPosts != -1) {
		echo '<div id="relevant-posts"><span class="title"><a href="/events">Upcoming Events</a> - <a href="/wp-admin/post-new.php?post_type=gp_events">Post Your Event</a></span>'; 
			
		?><div id="post-filter"><span class="left">Filter by State:&nbsp;&nbsp;<select name="filterby_state" id="filterby_state"><option value="/events">All States</option><?php 
		foreach ($states_us as $key => $value) {
			if ($key == get_query_var( 'filterby_state' )) {$state_selected = ' selected';} else {$state_selected = '';}
  			echo '<option value="/events/US/' . $key . '"' . $state_selected . '>' . $value . '</option>';
		}									
		?></select></span><div class="clear"></div></div><?php
		
		$i = 0;
		# Format event data and store in a string for use with jquery datepicker EVENT CALENDAR 
		$event_str = '[';
		
		foreach ($pageposts as $post) {
			setup_postdata($post);
			
			$event_title =  get_the_title($post->ID);
			
			$displayday = date('j', $post->gp_events_startdate);
			$displaymonth = date('M', $post->gp_events_startdate);
			$str_month = date('m', $post->gp_events_startdate);
			$displayyear = date('y', $post->gp_events_startdate);
			
			$displayendday = date('j', $post->gp_events_enddate);
			$displayendmonth = date('M', $post->gp_events_enddate);
			$str_endmonth = date('m', $post->gp_events_enddate);
			
			$displaysuburb = $post->gp_events_locsuburb;
			$displaystate = $post->gp_events_locstate;
			
			$event_link_url = get_permalink($post->ID);
			$post_id = $post->ID;
			
			$displaytitle = '<a href=\"'. $event_link_url . '\" title=\"Permalink to '. $event_title .'\">'. $event_title .'</a>';
						
			$event_date_string = 'new Date("'. $str_month .'/'. $displayday .'/'. $displayyear .'")';
			$event_str .= '{ Title: "'. $displaytitle .'", Date: new Date("'. $str_month .'/'. $displayday .'/'. $displayyear .'") },';
			
			/** DISPLAY NEXT 3 EVENTS BELOW CALENDAR  **/
			if ($i < 3) {
				echo '<div class="relevant-item">';
				if ( has_post_thumbnail() ) {	# DISPLAY EVENTS FEATURED IMAGE 
					$imageArray = wp_get_attachment_image_src( get_post_thumbnail_id($post->ID), 'icon-thumbnail' );
					$imageURL = $imageArray[0];
					echo '<a href="' . get_permalink($post->ID) . '" class="hp_minithumb"><img src="' . $imageURL . '" alt="' . get_the_title( get_post_thumbnail_id($post->ID) ) . '" /></a>';
				} else {						# DISPLAY DEFAULT EVENT IMAGE 
					$imageArray = wp_get_attachment_image_src( get_post_thumbnail_id(322), 'icon-thumbnail' ); 	# DEFAULT IMAGE STORED IN POST WHERE ID = 322
					$imageURL = $imageArray[0];
					echo '<a href="' . get_permalink($post->ID) . '" class="hp_minithumb"><img src="' . $imageURL . '" alt="' . get_the_title( get_post_thumbnail_id($post->ID) ) . '" /></a>';
				}
				?>
				<a href="<?php the_permalink(); ?>" title="Permalink to <?php esc_attr(the_title()); ?>" rel="bookmark" class="title"><?php the_title(); ?></a>
				<?php echo '<div class="post-details">' . $post->gp_events_locsuburb . ' | <a href="/events/US/' . $post->gp_events_locstate . '">' . $post->gp_events_locstate . ', </a>';
				if ($displayday == $displayendday) {
					echo $displayday . ' ' . $displaymonth;
				} else {
					echo $displayday . ' ' . $displaymonth . ' - ' . $displayendday . ' ' . $displayendmonth;
				}	
				echo '</div><div class="clear"></div></div>';	
				$i++;
			}		
		}
		echo '</div>';
	}
	$event_str .= ']';
	#echo $event_str;
	
	/** RUN JAVASCRIPT THAT DISPLAYS EVENT CALENDAR AND HIGHLIGHTS DATES WITH EVENTS 
	 *  USING JQUERY DATEPICKER 
	 *  CLICKING ON A HIGHLIGHTED DATE WILL DISPLAY LINKS TO EVENT PAGES IN JQUERY DIALOG BOX
	 *  **/
	
	echo '<script type="text/javascript">
			<!--//--><![CDATA[//><!--
				var events = '. $event_str .';
				console.log(events);
				$("#eventCalendar").datepicker({
    				beforeShowDay: function(date) {
    					var result = [true, \'\', null];
    					var matching = $.grep(events, function(event) {
       						return event.Date.valueOf() === date.valueOf();
   						});
       
   						if (matching.length) {
       						result = [true, \'ui-datepicker-cell-over ui-state-active\', null];
   						}
   						return result;
   					},
   					onSelect: function(dateText) {
   						var date,
       					selectedDate = new Date(dateText),
       					i = 0,
       					j = 0;
       					event = [];
       					event[j] = null;
      
	        			while (i < events.length && !event[j]) {
       						date = events[i].Date;
       						
	            			if (selectedDate.valueOf() === date.valueOf()) {
       			    			event[j] = events[i];
       			    			j++;
       						}
       						i++;
   						}
   						if (event[0]) {
   							k = 0;
   							l = event.length;
   							
   							dialog_str = \'\';
   							for (k = 0; k < l; k++) {
   								next_str = \'<p>\'+event[k].Title+\'</p><br />\';
   								dialog_str = dialog_str.concat(next_str);
   							}
   							

							$(function() {
								$( "#event-dialog" ).html(dialog_str);
								$( "#event-dialog" ).dialog({ position: [848,200], minHeight: 142, width: 288 });
							});   							
       						//alert(event.Title);
   						}
					}
				});
				//--><!]]>
			</script>';	
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

function email_after_post_approved($post_ID) {

  global $post_type_to_url_part;

  $bcc = "katiepatrickgp@gmail.com, jesse.browne@thegreenpages.com.au";

  $post = get_post($post_ID);
  $user = get_userdata($post->post_author);
  $post_url = site_url() . '/' . $post_type_to_url_part[$post->post_type] . '/' . $post->post_name;

  $headers  = 'Content-type: text/html' . "\r\n";
  $headers .= 'Bcc: ' . $bcc . "\r\n";

  $body  = '<table width="600px" style="font-size: 15px; font-family: helvetica, arial, tahoma; margin: 5px; background-color: rgb(255,255,255);">';
  $body .= '<tr><td align="center">';
  $body .= '<table width="640">';
  $body .= '<tr style="padding: 0 20px 5px 5px;">';
  $body .= '<td style="font-size: 18px; text-transform:non; color:rgb(100,100,100);padding:0 0 0 5px;">';
  $body .= 'Hi ' . $user->user_nicename . "!<br /><br />";
  $body .= 'Your Green Pages post has been approved.  Thanks for posting!<br /><br />';
  $body .= 'You can see your new post at:<br />';
  $body .= '<a href="'. $post_url . '" >' . $post_url."</a><br /><br />";
  $body .= "Keep on making an amazing world.<br /><br />";
  $body .= "The Green Pages Team<br />";

  $body .= '<div style="color: rgb(0, 154, 194);font=size:13px; ">';
  $body .= 'Green Pages Australia &nbsp;p 02 8003 5915&nbsp;<br />';
  $body .= '<a href="mailto:info@thegreenpages.com.au">info@thegreenpages.com.au</a>&nbsp;';
  $body .= '<a href="http://www.thegreenpages.com.au">www.thegreenpages.com.au</a>';
  $body .= '<br />';
  $body .= '</div>';

  $body .= '</td></tr></table></td></tr></table><br /><br />';

  wp_mail($user->user_email, 'Your Green Pages post has been approved!', $body, $headers);

}
add_action('pending_to_publish', 'email_after_post_approved');

/** GOOGLE MAPS TO SHOW ALL POSTS ON WORLD MAP, CENTERED BY USER IP LOCATION **/

function display_google_map_posts($json) {
    
    /** 
    * Accepts json structured string holding post title link, lat and long data on each relevant post
    * Construcs google map and places marker on each post location, 
    * Each marker shows a lightbox with a link to post on click
    * TODO: add excerpt in future
    **/

	//Grabs user's IP address and gets lat and long via geoplugin free website.
	//TO DO: Script is loading the map really slowly, not sure how to fix, but needs to be fixed!
	
	$ip_addr = $_SERVER['REMOTE_ADDR'];
	$geoplugin = unserialize( file_get_contents('http://www.geoplugin.net/php.gp?ip='.$ip_addr) );

	if ( is_numeric($geoplugin['geoplugin_latitude']) && is_numeric($geoplugin['geoplugin_longitude']) ) {

		$user_lat = $geoplugin['geoplugin_latitude'];
		$user_long = $geoplugin['geoplugin_longitude'];
	}
	
	//For testing
	//echo $ip_addr.';'.$user_lat.';'.$user_long;  
	//note $_SERVER['REMOTE_ADDR']; doesn't work on local host dev environment so I explicitly declare the IP for various use locations who have sign up, and it works well.


    //Syndey's default lat and long in case remote IP does not load.
    //$default_lat = -32;
    //$default_long = 134;   
    
	?>   
    <script type="text/javascript">
        //Event Objects to make surrounding markers
        var json = <?php echo $json; ?>;
      
        //Function that calls map, centres map around post location, styles map
        function initialize() {
            var myLatlng = new google.maps.LatLng(<?php echo $user_lat; ?>, <?php echo $user_long; ?> );
            var mapOptions = {
                zoom: 4,
                center: myLatlng,          
                mapTypeId: google.maps.MapTypeId.ROADMAP
            }
        
            //Adds map to map_canvas div in DOM to it is visible
            var map = new google.maps.Map(document.getElementById("post_google_map_canvas"),
                      mapOptions);
            
            // Creating a global infoWindow object that will be reused by all markers
		    var infoWindow = new google.maps.InfoWindow();
		
		        		
    		//Loop through the json surrounding event objects
	    	for (var i = 0, length = json.length; i < length; i++) {
		        var data = json[i],
		                   eventlatlong = new google.maps.LatLng(data.Event_lat, data.Event_long);
		    		    
                //Adds surrounding markers from the json object and loop for surrounding events
                var marker2 = new google.maps.Marker({
            	    position: eventlatlong,
            	    map: map,
        	        title: data.Title
                });		    
		
		 	    // Creating a closure to retain the correct data, notice how I pass the current 
            	// data in the loop into the closure (marker, data)
	    		(function(marker2, data) {

		        	// Attaching a click event to the current marker
		        	google.maps.event.addListener(marker2, "click", function(e) {
			        	infoWindow.setContent(data.Title);
			    	    infoWindow.open(map, marker2);
    				});
	    		})(marker2, data);       	
		    } 
        }
     
        function loadScript() {
  	        var script = document.createElement("script");
      	    script.type = "text/javascript";
  		    script.src = "http://maps.googleapis.com/maps/api/js?key=AIzaSyC1Lcch07tMW7iauorGtTY3BPe-csJhvCg&sensor=false&callback=initialize";
      		document.body.appendChild(script);
	    }
	    window.onload = loadScript;
   </script>
   <?php
   echo '<div onload="initialize()"></div>
         <div id="post_google_map_canvas"></div>'; 
	
}	

/** GET USER IP ADDRESS FROM SIMPLEGEO API **/
#Simple GEO has gone out of business! This code will need to be replaced.
#https://github.com/simplegeo/php-simplegeo
#https://github.com/simplegeo/php-simplegeo/blob/master/README.md

function simplegeo_ip_user_location() {
	
	require_once 'php-simplegeo/SimpleGeo.php';

	$client = new SimpleGeo('bmZFj3mruK3MZ8B3aZnxAvYYzTdS7Dp8', 'F6hR5h2s3Ed9YTgsqeT4kSc3DeWpSjjs');
				
	$ip = $_SERVER["REMOTE_ADDR"];							#Gets IP from user
															#RESULTS ARE NOT VERY ACCURATE BUT AT LEAST GET THE STATE AND COUNTRY
	$result = $client->ContextIP($ip);

	$i = 0;
	$user_location = '';
	
	foreach($result['features'] as $item) {					#DISPLAY ONLY SUBURB AND CITY
		
		switch ($i) {
			case '0':
				#$user_location .= $item['name'] .', '; 	#DISPLAY SUBURB
				$i++;
				break;
			case '1':
				#$user_location .= $item['name'] .', ';		#DISPLAY CITY
				$i++;
				break;
			case '2':
				#$user_location .= $item['name'] .', ';		#DISPLAY STATE/COUNTRY
				$i++;
				break;
			case '3':
				$user_location .= $item['name'] .', ';		#DISPLAY STATE
				$i++;
				break;								
			case '4':
				$user_location .= $item['name'];			#DISPLAY COUNTRY
				$i++;
				break;
		}		
	}	
	
	if($user_location == ''){
		$user_location = 'your part of the world';
	}
	
	return $user_location;									#SUBURB AND CITY IN STRING
}

/** LOCATION TAG LINE STYLE **/

function theme_location_tag_line() {						#DISPLAY TAGLINE ENDING WITH USERS STATE AND COUNTRY
	$location = simplegeo_ip_user_location();
	?>
	<div class="pos"><div class="post-details" id="header-tagline">Everything environmental happening in <?php echo $location; ?></div></div>  
	<?php
}


/** SHOW MEMBERS POSTS **/
function theme_profile_posts($profile_pid, $post_page, $post_tab, $post_type) {
	// note: Favourites are viewable by everyone!
	
	$profile_author = get_user_by('slug', $profile_pid);
	
	global $wpdb, $post, $current_user, $post_type_to_url_part, $newposttypes;
	
	if ( strtolower($post_type) == "directory" ) {
		theme_profile_directory($profile_pid);
		return;	
	}
	
	$post_type_filter = "";
	$post_type_key = array_search($post_type, $post_type_to_url_part);
	if ( $post_type_key ) {
		$post_type_filter = "" . $wpdb->prefix . "posts.post_type = '{$post_type_key}'";
	} else {
		foreach ($post_type_to_url_part as $key => $value) {
			$post_type_filter .= $wpdb->prefix . "posts.post_type = '{$key}' or ";
		}
		$post_type_filter = substr($post_type_filter, 0, -4);
	}
	
		
	#if ((is_user_logged_in()) && ($current_user->ID == $profile_author->ID) || get_user_role( array($rolecontributor, 'administrator') ) ) { # CHECK IF USER IS LOGGED IN AND VIEWING THEIR OWN PROFILE PAGE
		
		$total = "SELECT COUNT(*) as count 
				FROM $wpdb->posts " . 
					$wpdb->prefix . "posts, 
					$wpdb->postmeta " . 
					$wpdb->prefix . "postmeta 
				WHERE " . $wpdb->prefix . "posts.ID = " . 
					$wpdb->prefix . "postmeta.post_id and " . 
					$wpdb->prefix . "posts.post_status = 'publish' and 
					(" . $post_type_filter . ")	and " . 
					$wpdb->prefix . "postmeta.meta_key = '_thumbnail_id' and " . 
					$wpdb->prefix . "postmeta.meta_value >= 1 and " . 
					$wpdb->prefix . "posts.post_author = '" . $profile_author->ID . "'";
					
		$totalposts = $wpdb->get_results($total, OBJECT);
		#$ppp = intval(get_query_var('posts_per_page'));
		$ppp = 10;
		$wp_query->found_posts = $totalposts[0]->count;
		$wp_query->max_num_pages = ceil($wp_query->found_posts / $ppp);		
		#$on_page = intval(get_query_var('paged'));	
		$on_page = $post_page;

		if($on_page == 0){ $on_page = 1; }		
		$offset = ($on_page-1) * $ppp;
		
		$querystr = "SELECT " . $wpdb->prefix . "posts.* 
					FROM $wpdb->posts " . 
						$wpdb->prefix . "posts, 
						$wpdb->postmeta " . 
						$wpdb->prefix . "postmeta 
					WHERE " . $wpdb->prefix . "posts.ID = " . 
						$wpdb->prefix . "postmeta.post_id and " . 
						$wpdb->prefix . "posts.post_status = 'publish' and 
						(" . $post_type_filter . ") and " . 
						$wpdb->prefix . "postmeta.meta_key = '_thumbnail_id' and " . 
						$wpdb->prefix . "postmeta.meta_value >= 1 and " . 
						$wpdb->prefix . "posts.post_author = '" . $profile_author->ID . "' 
					ORDER BY " . $wpdb->prefix . "posts.post_date DESC 
					LIMIT " . $ppp . " 
					OFFSET " . $offset;
						
		$pageposts = $wpdb->get_results($querystr, OBJECT);
		
		if ( $post_type_key ) {
			foreach ($newposttypes as $newposttype) {
				if ($newposttype['id'] == $post_type_key) {$post_type_name = " " . $newposttype['name'];}
			}
		}
		
		if ( ( is_user_logged_in() ) && ( $current_user->ID == $profile_author->ID ) || get_user_role( array('administrator') ) ) {
			echo "<div class=\"total-posts\"><span>{$wp_query->found_posts}</span>{$post_type_name} Posts";
			gp_select_createpost();
			echo "</div>";
		} else {
			echo "<div class=\"total-posts\"><span>{$wp_query->found_posts}</span>{$post_type_name} Posts</div>";
		}
		
		if ($pageposts) {
			$post_author_url = get_author_posts_url($profile_author->ID);
			
			foreach ($pageposts as $post) {
			
				setup_postdata($post);
				
				switch (get_post_type()) {
				    case 'gp_news':
				        $post_title = 'News';
				        $post_url = '/news';
				        break;
				    case 'gp_projects':
				    	$post_title = 'Projects';
				    	$post_url = '/projects';
				        break;
					case 'gp_advertorial':
						$post_title = 'Products';
						$post_url = '/news-stuff';
				        break;
					case 'gp_competitions':
						$post_title = 'Competitions';
						$post_url = '/competitions';
				        break;
				    case 'gp_events':
				    	$post_title = 'Events';
				    	$post_url = '/events';
				        break;
				    case 'gp_people':
				    	$post_title = 'People';
				    	$post_url = '/people';
				        break;
				}

				if ( has_post_thumbnail() ) {
					$imageArray = wp_get_attachment_image_src( get_post_thumbnail_id($post->ID), 'homepage-thumbnail' );
					$imageURL = $imageArray[0];
					echo '<a href="' . get_permalink($post->ID) . '" class="profile_minithumb"><img src="' . $imageURL  . '" alt="' . get_the_title( get_post_thumbnail_id($post->ID) ) . '" /></a>';
				}

				echo '
				<div class="profile-postbox">
			    	<h1><a href="' . get_permalink($post->ID) . '" title="Permalink to ' . esc_attr(get_the_title($post->ID)) . '" rel="bookmark">' . get_the_title($post->ID) . '</a></h1>
			    	<div class="post-details">Posted in <a href="' . $post_url . '">' . $post_title . '</a> ' . time_ago(get_the_time('U'), 0) . ' ago</div>';
			    	the_excerpt();
					echo '<a href="' . get_permalink($post->ID) . '" class="profile_postlink">Read more...</a>';
					
				if ( comments_open() ) {
					echo '<div class="comment-profile"><a href="' . get_permalink($post->ID) . '#comments"><span class="comment-mini"></span><span class="comment-mini-number dsq-postid"><fb:comments-count href="' . get_permalink($post->ID) . '"></fb:comments-count></span></a></div>';
				}
				
				global $current_user, $current_site;
				
				$likedclass = '';
				if ( get_user_meta( $current_user->ID, 'likepost_' . $current_site->id . '_' . $post->ID , true ) ) {
					$likedclass = ' favorited';
				}
				
				$likecount = get_post_meta( $post->ID, 'likecount', true );
				if ($likecount > 0) {
					$showlikecount = '';
				} else {
					$likecount = 0;
					$showlikecount = ' style="display:none;"';
				}
				
				$likecount = abbr_number( $likecount );
				
				if ( is_user_logged_in() ) {
					echo '<div id="post-' . $post->ID . '" class="favourite-profile"><a href="#/"><span class="star-mini' . $likedclass . '"></span><span class="star-mini-number"' . $showlikecount . '>' . $likecount . '</span><span class="star-mini-number-plus-one" style="display:none;">+1</span><span class="star-mini-number-minus-one" style="display:none;">-1</span></a></div>';
				} else {
					echo '<div id="post-' . $post->ID . '" class="favourite-profile"><a href="' . wp_login_url( "http://" . $_SERVER['HTTP_HOST']  . $_SERVER['REQUEST_URI'] ) . '" class="simplemodal-login"><span class="star-mini"></span><span class="star-mini-number"' . $showlikecount . '>' . $likecount . '</span><span class="star-login" style="display:none;">Login...</a></a></div>';
				}
					
		    	echo '
		    		</div>
		    		<div class="topic-container">
		    			<div class="topic-content">
		    				<a href="#/" class="topic-bookmark">test topic</a>
		    			</div>
		    		</div>
		    		<div class="clear"></div>
		    	';
			}
			
			if ( $wp_query->max_num_pages > 1 ) {
				theme_tagnumpagination( $on_page, $wp_query->max_num_pages, $post_tab, $post_type );
			}

		}
	#}	
}

function theme_tagnumpagination ($on_page, $post_pages, $post_tab, $post_type) {
	echo "<div class=\"post-number-paging\"><div>";
	for ($i = 1; $i <= $post_pages; $i++) {
		if ($i == 1 && $on_page != $i) {
			$previous = $on_page - 1;
			#echo "<a href=\"#tag:{$post_tab};page:{$i};\">First<a>";
			echo "<a href=\"#tag:{$post_tab};post:{$post_type};page:{$previous};\" class=\"post-number-arrow\">&larr;</a>";
		}
		if ($on_page == $i) {
			echo "<a href=\"#tag:{$post_tab};post:{$post_type};page:{$i};\" class=\"active-page\">{$i}</a>";
		} else {
			echo "<a href=\"#tag:{$post_tab};post:{$post_type};page:{$i};\">{$i}</a>";
		}
		
		if ($i != $post_pages) {
			echo " &middot; ";
		}
		
		if ($i == $post_pages && $on_page != $post_pages) {
			$next = $on_page + 1;
			echo "<a href=\"#tag:{$post_tab};type:{$post_type};page:{$next};\" class=\"post-number-arrow\">&rarr;</a>";
			#echo "<a href=\"#tag:{$post_tab};page:{$i};\">Last<a>";
		}
	}
	echo "</div></div>";
}

/** SHOW MEMBERS FOLLOWING MEMBERSHIP **/
function theme_profile_following($profile_pid) {
	// note: Favourites are viewable by everyone!
	
	echo "
	<div class=\"total-posts\">
		<span>0</span> Following
	</div>
	";
}

/** SHOW MEMBERS TOPIC MEMBERSHIP **/
function theme_profile_topics($profile_pid) {
	// note: Favourites are viewable by everyone!
	
	echo "
	<div class=\"total-posts\">
		<span>0</span> Topics
	</div>
	";	
}

/** SHOW MEMBERS FAVOURITE POSTS **/
function theme_profile_favourites($profile_pid, $post_page, $post_tab, $post_type) {

	// note: Favourites are viewable by everyone!
	
	$profile_author = get_user_by('slug', $profile_pid);

	global $wpdb, $post, $current_user, $current_site, $post_type_to_url_part, $newposttypes;
	
	$post_type_filter = "";
	$post_type_key = array_search($post_type, $post_type_to_url_part);
	if ( $post_type_key ) {
		$post_type_filter = "" . $wpdb->prefix . "posts.post_type = '{$post_type_key}'";
	} else {
		foreach ($post_type_to_url_part as $key => $value) {
			$post_type_filter .= $wpdb->prefix . "posts.post_type = '{$key}' or ";
		}
		$post_type_filter = substr($post_type_filter, 0, -4);
	}

	$total = "SELECT COUNT(*) as count
				FROM " . $wpdb->prefix . "posts 
				LEFT JOIN " . $wpdb->prefix . "usermeta as m0 on REPLACE(m0.meta_key, 'likepost_" . $current_site->id . "_', '')=" . $wpdb->prefix . "posts.ID 
				LEFT JOIN " . $wpdb->prefix . "postmeta as m1 on m1.post_id=" . $wpdb->prefix . "posts.ID and m1.meta_key='_thumbnail_id' 
				WHERE post_status='publish' 
					AND (" . $post_type_filter . ")
					AND m0.meta_value > 0 
					AND m0.user_id = $profile_author->ID 
					AND m0.meta_key LIKE 'likepost%' 
					AND m1.meta_value >= 1;";
					
	$totalposts = $wpdb->get_results($total, OBJECT);
	#$ppp = intval(get_query_var('posts_per_page'));
	$ppp = 10;
	$wp_query->found_posts = $totalposts[0]->count;
	$wp_query->max_num_pages = ceil($wp_query->found_posts / $ppp);		
	#$on_page = intval(get_query_var('paged'));	
	$on_page = $post_page;

	if($on_page == 0){ $on_page = 1; }		
	$offset = ($on_page-1) * $ppp;

	$querystr = "SELECT " . $wpdb->prefix . "posts.*
					, m1.meta_value as _thumbnail_id 
				FROM " . $wpdb->prefix . "posts 
				LEFT JOIN " . $wpdb->prefix . "usermeta as m0 on REPLACE(m0.meta_key, 'likepost_" . $current_site->id . "_', '')=" . $wpdb->prefix . "posts.ID 
				LEFT JOIN " . $wpdb->prefix . "postmeta as m1 on m1.post_id=" . $wpdb->prefix . "posts.ID and m1.meta_key='_thumbnail_id' 
				WHERE post_status='publish'
					AND (" . $post_type_filter . ")   
					AND m0.meta_value > 0 
					AND m0.user_id = $profile_author->ID 
					AND m0.meta_key LIKE 'likepost%' 
					AND m1.meta_value >= 1 
				ORDER BY m0.meta_value DESC
				LIMIT " . $ppp . " 
				OFFSET " . $offset;
					
	$pageposts = $wpdb->get_results($querystr, OBJECT);	
			
		if ( $post_type_key ) {
			foreach ($newposttypes as $newposttype) {
				if ($newposttype['id'] == $post_type_key) {
					if ($newposttype['plural'] == true) {
						$post_type_name = " " . $newposttype['name'] . "s";
					} else {
						$post_type_name = " " . $newposttype['name'];
					}
				}
			}
		}
		
		if ($post_type_name) {
			echo "<div class=\"total-posts\"><span>{$wp_query->found_posts}</span> Favourite{$post_type_name}</div>";
		} else {
			echo "<div class=\"total-posts\"><span>{$wp_query->found_posts}</span> Favourites</div>";
		}
		
		foreach ($pageposts as $post) {
		
			setup_postdata($post);
			switch (get_post_type()) {
			    case 'gp_news':
			        $post_title = 'News';
			        $post_url = '/news';
			        break;
			    case 'gp_projects':
			    	$post_title = 'Projects';
			    	$post_url = '/projects';
			        break;
				case 'gp_advertorial':
					$post_title = 'Products';
					$post_url = '/news-stuff';
			        break;
				case 'gp_competitions':
					$post_title = 'Competitions';
					$post_url = '/competitions';
			        break;
			    case 'gp_events':
			    	$post_title = 'Events';
			    	$post_url = '/events';
			        break;
			    case 'gp_people':
			    	$post_title = 'People';
			    	$post_url = '/people';
			        break;
			}

			if ( has_post_thumbnail() ) {
                                $imageArray = wp_get_attachment_image_src( get_post_thumbnail_id($post->ID), 'homepage-thumbnail' );
                                $imageURL = $imageArray[0];
                                echo '<a href="' . get_permalink($post->ID) . '" class="profile_minithumb"><img src="' . $imageURL  . '" alt="' . get_the_title( get_post_thumbnail_id($post->ID) ) . '" /></a>';
                        }

			echo '
			<div class="profile-postbox">
		    	<h1><a href="' . get_permalink($post->ID) . '" title="Permalink to ' . esc_attr(get_the_title($post->ID)) . '" rel="bookmark">' . get_the_title($post->ID) . '</a></h1>
		    	<div class="post-details">Posted in <a href="' . $post_url . '">' . $post_title . '</a> ' . time_ago(get_the_time('U'), 0) . ' ago</div>';
		    	the_excerpt();
				echo '<a href="' . get_permalink($post->ID) . '" class="profile_postlink">Read more...</a>';
				
			if ( comments_open() ) {
				echo '<div class="comment-profile"><a href="' . get_permalink($post->ID) . '#comments"><span class="comment-mini"></span><span class="comment-mini-number dsq-postid"><fb:comments-count href="' . get_permalink($post->ID) . '"></fb:comments-count></span></a></div>';
			}
			
			$likedclass = '';
			if ( get_user_meta($current_user->ID, 'likepost_' . $current_site->id . '_' . $post->ID , true) ) {
				$likedclass = ' favorited';
			}
			
			$likecount = get_post_meta($post->ID, 'likecount', true);
			if ($likecount > 0) {
				$showlikecount = '';
			} else {
				$likecount = 0;
				$showlikecount = ' style="display:none;"';
			}
			
			$likecount = abbr_number($likecount);
			
			if (is_user_logged_in()) {
				echo '<div id="post-' . $post->ID . '" class="favourite-profile"><a href="#/"><span class="star-mini' . $likedclass . '"></span><span class="star-mini-number"' . $showlikecount . '>' . $likecount . '</span><span class="star-mini-number-plus-one" style="display:none;">+1</span><span class="star-mini-number-minus-one" style="display:none;">-1</span></a></div>';
			} else {
				echo '<div id="post-' . $post->ID . '" class="favourite-profile"><a href="' . wp_login_url( "http://" . $_SERVER['HTTP_HOST']  . $_SERVER['REQUEST_URI'] ) . '" class="simplemodal-login"><span class="star-mini"></span><span class="star-mini-number"' . $showlikecount . '>' . $likecount . '</span><span class="star-login" style="display:none;">Login...</a></a></div>';
			}
				
	    	echo '</div><div class="topic-container"><div class="topic-content"><a href="#/" class="topic-bookmark">test topic</a></div></div>';
/*			if ( has_post_thumbnail() ) {
				$imageArray = wp_get_attachment_image_src( get_post_thumbnail_id($post->ID), 'homepage-thumbnail' );
				$imageURL = $imageArray[0];
				echo '<a href="' . get_permalink($post->ID) . '" class="profile_minithumb"><img src="' . $imageURL  . '" alt="' . get_the_title( get_post_thumbnail_id($post->ID) ) . '" /></a>';
			}*/
			echo '<div class="clear"></div>';
		}
		
		if ( $wp_query->max_num_pages > 1 ) {
			theme_tagnumpagination( $on_page, $wp_query->max_num_pages, $post_tab, $post_type );
		}
}

/** SHOW MEMBERS ADVERTISING OPTIONS **/
function theme_profile_advertise($profile_pid) {
	global $current_user;

	$profile_author = get_user_by('slug', $profile_pid);
	
	if ( ( ( is_user_logged_in() ) && ( $current_user->ID == $profile_author->ID ) ) || get_user_role( array('administrator') ) ) {} else {return;}

	echo "
	<div id=\"my-advertise\">
		<div id=\"advertorial\">
			<span><a href=\"/forms/create-product-post/\" target=\"_blank\"><input type=\"button\" value=\"Post a Product $89\" /></a></span>
			<div class=\"clear\"></div>			
			<span><a href=\"" . get_bloginfo('template_url') . "/about/rate-card/#product\" target=\"_blank\">Learn more</a></span>
		</div>
		<div class=\"clear\"></div>
		<div id=\"competition\">
			<span><a href=\"/forms/create-competition-post/\" target=\"_blank\"><input type=\"button\" value=\"Post a Competition $250\" /></a></span>	
			<div class=\"clear\"></div>				
			<span><a href=\"" . get_bloginfo('template_url') . "/about/rate-card/#competition\" target=\"_blank\">Learn more</a></span>
		</div>
		<div class=\"clear\"></div>
		<div id=\"listing\">
			<span><a href=\"" . get_permalink(472) . "\" target=\"_blank\"><input type=\"button\" value=\"Directory Page $39/m\" /></a></span>
			<div class=\"clear\"></div>
			<span><a href=\"" . get_bloginfo('template_url') . "/about/rate-card/#directory\" target=\"_blank\">Learn more</a></span>
		</div>
		<div class=\"clear\"></div>
		<div id=\"email\">
			<span><a href=\"mailto:jesse.browne@thegreenpages.com.au?Subject=Exclusive%20Email%20Inquiry\" ><input type=\"button\" value=\"Exclusive Email $3500\" /></a></span>
			<div class=\"clear\"></div>
			<span><a href=\"" . get_bloginfo('template_url') . "/about/rate-card/#email\" target=\"_blank\">Learn more</a></span>
		</div>
		<div class=\"clear\"></div>
	</div>
	";
}

/** SHOW MEMBERS DIRECTORY OPTIONS **/
function theme_profile_directory($profile_pid) {
	$profile_author = get_user_by('slug', $profile_pid);
	$profile_author_id = $profile_author->ID;
	$directory_page_url = $profile_author->directory_page_url;
    $chargify_self_service_page_url = $profile_author->chargify_self_service_page_url;
	$chargify_self_service_page_link = '';
	
    if (!empty($chargify_self_service_page_url)) {
        $chargify_self_service_page_link = "<a href=\"" . $chargify_self_service_page_url . "\" target=\"_blank\"><h3>Update my credit card payment details here</h3></a>";
    }
   
	echo "
	<div id=\"my-directory\">
	    <br />
		<a href=\"" . $directory_page_url . "\" target=\"_blank\"><h3>View My Directory Page</h3></a>
		<a href=\"/forms/update-my-directory-page/\">
		    <h3>Update my Directory Page details here</h3>
		</a>
		" . $chargify_self_service_page_link . "
	</div>
	";
}

/** SHOW MEMBERS POST ANALYTICS **/
function theme_profile_analytics($profile_pid) {
	global $wpdb, $post, $current_user, $post_type_to_url_part;

	$profile_author = get_user_by('slug', $profile_pid);
	$profile_author_id = $profile_author->ID;

	if ( ( ( is_user_logged_in() ) && ( $current_user->ID == $profile_author->ID ) ) || get_user_role( array('administrator') ) ) {} else {return;}

	require 'ga/analytics.class.php';
	
	$total = "SELECT COUNT(*) as count 
			FROM $wpdb->posts " . 
				$wpdb->prefix . "posts, 
				$wpdb->postmeta " . 
				$wpdb->prefix . "postmeta 
			WHERE " . $wpdb->prefix . "posts.ID = " . 
				$wpdb->prefix . "postmeta.post_id and " . 
				$wpdb->prefix . "posts.post_status = 'publish' and (" . 
					$wpdb->prefix . "posts.post_type = 'gp_news' or " . 
					$wpdb->prefix . "posts.post_type = 'gp_events' or " . 
					$wpdb->prefix . "posts.post_type = 'gp_advertorial' or " . 
					$wpdb->prefix . "posts.post_type = 'gp_projects' or " . 
					$wpdb->prefix . "posts.post_type = 'gp_competitions' or " . 
					$wpdb->prefix . "posts.post_type = 'gp_people') 
				and " . 
				$wpdb->prefix . "postmeta.meta_key = '_thumbnail_id' and " . 
				$wpdb->prefix . "postmeta.meta_value >= 1 and " . 
				$wpdb->prefix . "posts.post_author = '" . $profile_author->ID . "'";
				
	$totalposts = $wpdb->get_results($total, OBJECT);

	$ppp = intval(get_query_var('posts_per_page'));
	$wp_query->found_posts = $totalposts[0]->count;
	$wp_query->max_num_pages = ceil($wp_query->found_posts / $ppp);		
	$on_page = intval(get_query_var('paged'));	

	if($on_page == 0){ $on_page = 1; }		
	$offset = ($on_page-1) * $ppp;
	
	$querystr = "SELECT " . $wpdb->prefix . "posts.* 
				FROM $wpdb->posts " . 
					$wpdb->prefix . "posts, 
					$wpdb->postmeta " . 
					$wpdb->prefix . "postmeta 
				WHERE " . $wpdb->prefix . "posts.ID = " . 
					$wpdb->prefix . "postmeta.post_id and " . 
					$wpdb->prefix . "posts.post_status = 'publish' and (" . 
						$wpdb->prefix . "posts.post_type = 'gp_news' or " . 
						$wpdb->prefix . "posts.post_type = 'gp_events' or " . 
						$wpdb->prefix . "posts.post_type = 'gp_advertorial' or " . 
						$wpdb->prefix . "posts.post_type = 'gp_projects' or " . 
						$wpdb->prefix . "posts.post_type = 'gp_competitions' or " . 
						$wpdb->prefix . "posts.post_type = 'gp_people') 
					and " . 
					$wpdb->prefix . "postmeta.meta_key = '_thumbnail_id' and " . 
					$wpdb->prefix . "postmeta.meta_value >= 1 and " . 
					$wpdb->prefix . "posts.post_author = '" . $profile_author->ID . "' 
				ORDER BY " . $wpdb->prefix . "posts.post_date DESC";
					
	$pageposts = $wpdb->get_results($querystr, OBJECT);
	
	# Profile meta variables for getting specific analytics data
	$old_crm_id = $profile_author->old_crm_id;
	$directory_page_url = $profile_author->directory_page_url;
	$facebook = $profile_author->facebook;
	$linkedin = $profile_author->linkedin;
	$twitter = $profile_author->twitter;
	$skype = $profile_author->skype;
	$url = $profile_author->user_url;
	
	if (!$pageposts && !empty($old_crm_id) ) {
		?>
		<div id="my-analytics">
		    <br />
			<?php theme_advertorialcreate_post(); ?>
			<p>Create your complementary Product of the Week Advertorial to unlock your Analytics.</p>
		</div>
		<?php 
		return;
	}
	
	if (!$pageposts) {
		?>
		<div id="my-analytics"></div>
		<?php 
		return;
	}
	
	# TABLE HEADINGS FOR POST ANALYTICS
	?>
	<div id="my-analytics">
		<?php gp_select_createpost(); ?>
	
		<h2>Post Analytics</h2>		
		
		<table class="author_analytics">
			<tr>
				<td class="author_analytics_title">Title</td>		
				<td class="author_analytics_type">Post Type</td>
				<td class="author_analytics_cost">Value</td>
				<td class="author_analytics_date">Date Posted</td> 
				<td class="author_analytics_category_impressions">Category Impressions</td>
				<td class="author_analytics_page_impressions">Page Views</td>
				<td class="author_analytics_clicks">Clicks</td>
			</tr>
	<?php	
	
	$analytics = new analytics('greenpagesadserving@gmail.com', 'greenpages01'); //sign in and grab profile			
  	$analytics->setProfileById('ga:42443499'); 			//$analytics->setProfileByName('Stage 1 - Green Pages');
				
	if ($pageposts) {		
	 	
		foreach ($pageposts as $post) {
			setup_postdata($post);
		
			$post_url_ext = $post->post_name; //Need to get post_name for URL. Gets ful URl, but we only need /url extention for Google API			
			$type = get_post_type($post->ID);
				
			$post_type_map = $post_type_to_url_part;
				
			$post_url_end = '/' . $post_type_map[$type] . '/' . $post_url_ext . '/';
			#echo $post_url_end . '<br />$post_url_end<br />';
				
			
			$post_date = get_the_time('Y-m-d'); 				//Post Date
			#echo $post_date . ' ';
			$today_date = date('Y-m-d'); 						//Todays Date
			#echo $today_date . ' ';
				
  			$analytics->setDateRange($post_date, $today_date); 	//Set date in GA $analytics->setMonth(date('$post_date'), date('$new_date'));
				
  			#print_r($analytics->getVisitors()); 				//get array of visitors by day
  	
  			$pageViewURL = ($analytics->getPageviewsURL($post_url_end));	//Page views for specific URL
  			#echo $pageViewURL . ' $pageViewURL';
  			#var_dump ($pageViewURL);
  			$sumURL = 0;
  			foreach ($pageViewURL as $data) {
    			$sumURL = $sumURL + $data;
    			$total_sumURL = $total_sumURL + $data;
  			}
  			#echo ' <br />*** ' . $sumURL . ' ***<br /> ';			
			
  			$pageViewType = ($analytics->getPageviewsURL('/' . $post_type_map[$type] . '/'));	//Page views for the section landing page, e.g. the news page
  			$sumType = 0;
  			foreach ($pageViewType as $data) {
      			$sumType = $sumType + $data;
  			}
  				
  			$keywords = $analytics->getData(array(
            	'dimensions' => 'ga:keyword',
           	 	'metrics' => 'ga:visits',
            	'sort' => 'ga:keyword'
            	)
          	);	
          	
          	#SET UP POST ID AND AUTHOR ID DATA, POST DATE, GET LINK CLICKS DATA FROM GA 
          	$post_date_au = get_the_time('j-m-y');
	 		$post_id = $post->ID;
	 		$click_track_tag = '/yoast-ga/' . $post_id . '/' . $profile_author_id . '/outbound-article/';
			$clickURL = ($analytics->getPageviewsURL($click_track_tag));
  			$sumClick = 0;
			foreach ($clickURL as $data) {
    			$sumClick = $sumClick + $data;
  			}
			
			switch (get_post_type()) {		# CHECK POST TYPE AND ASSIGN APPROPRIATE TITLE, URL, COST AND GET BUTTON CLICKS DATA
			   
				case 'gp_advertorial':
					$post_title = 'Products';
					$post_url = '/eco-friendly-products';
					$post_price = '$89.00';
			  		$custom = get_post_custom($post->ID);
	 				$product_url = $custom["gp_advertorial_product_url"][0];	
	 				if ( !empty($product_url) ) {		# IF 'BUY IT' BUTTON ACTIVATED, GET CLICKS
	 					$click_track_tag_product_button = '/outbound/product-button/' . $post_id . '/' . $profile_author_id . '/' . $product_url . '/'; 
  						$clickURL_product_button = ($analytics->getPageviewsURL($click_track_tag_product_button));
  						foreach ($clickURL_product_button as $data) {
    						$sumClick = $sumClick + $data;
  						}
	 				}
	 				# GET PAGE IMPRESSIONS FOR OLD PRODUCT POSTS FROM BEFORE WE CHANGED URL AND ADD TO TOTAL
				 	$old_post_url_end = '/new-stuff/' . $post_url_ext . '/';
	 				$old_PageViewURL = ($analytics->getPageviewsURL($old_post_url_end));	//Page views for specific old URL
  					foreach ($old_PageViewURL as $data) {
    					$sumURL = $sumURL + $data;
    					$total_sumURL = $total_sumURL + $data;
  					}
		       		break;
				case 'gp_competitions':
					$post_title = 'Competitions';
					$post_url = '/competitions';
					$post_price = '$250.00';
		       		break;
		   		case 'gp_events':
		   			$post_title = 'Events';
		   			$post_url = '/events';
		   			$post_price = 'N/A';
		     		break;
		     	case 'gp_news':
				   	$post_title = 'News';
		   			$post_url = '/news';
		   			$post_price = 'N/A';		   			
		     		break;
		     	case 'gp_projects':
			    	$post_title = 'Projects';
			    	$post_url = '/projects';
			    	$post_price = 'N/A';
			        break;
			}
			
		  	if ($sumClick == 0) {			#IF NO CLICKS YET, DISPLAY 'Unavailable'
    			$sumClick = 'Unavailable';
    		}
			
											# DISPLAY ROW OF ANALYTICS DATA FOR EACH POST BY THIS AUTHOR (PAGE IMPRESSIONS ETC)
			echo '<tr>				
					<td class="author_analytics_title"><a href="' . get_permalink($post->ID) . '" title="Permalink to ' . 
					esc_attr(get_the_title($post->ID)) . '" rel="bookmark">' . get_the_title($post->ID) . '</a></td>				
					<td class="author_analytics_type"><a href="' . $post_url . '">' . $post_title . '</a></td>					
					<td class="author_analytics_cost">' . $post_price . '</td>				
					<td class="author_analytics_date">' . $post_date_au . '</td>
					<td class="author_analytics_category_impressions">' . $sumType . '</td>
					<td class="author_analytics_page_impressions">' . $sumURL . '</td>	
					<td class="author_analytics_clicks">' . $sumClick . '</td>								
				</tr>';
		}
	}	
	?>
		</table>			

		<p>Your posts have been viewed a total of</p> 
		<p><span class="big-number"><?php echo $total_sumURL;?></span> times!</p>	
		<p></p>
		
		
		<?php 	# DIRECTORY PAGE ANALYTICS FOR ADVERTISERS WHO HAVE (OR HAVE HAD) A DIRECTORY PAGE
		if (!empty($old_crm_id)) {
		?>
			<h2>Directory Page Analytics</h2>
			<table class="author_analytics">
				<tr>
					<td class="author_analytics_title">Title</td>
					<td class="author_analytics_cost">Value</td>
					<td class="author_analytics_category_impressions">Category Impressions</td>
					<td class="author_analytics_page_impressions">Page views</td>
					<td class="author_analytics_clicks">Clicks</td>
				</tr>	

				<?php 		
				# SET AND RESET SOME VARIABLES AND GET DIRECTORY PAGE DATA FROM GA
				$start_date = '2012-01-01'; 	// Click tracking of Directory Pages began just after this Date
				$today_date = date('Y-m-d'); 	// Todays Date
				
	  			$analytics->setDateRange($start_date, $today_date); //Set date in GA $analytics->setMonth(date('$post_date'), date('$new_date'));
	  			
				$gp_legacy = new gp_legacy();
				$results = $gp_legacy->getDirectoryPages($old_crm_id);

				$dir_sumURL = 0;
				$list_sumURL = 0;
				$totaldir_sumURL = 0;
				$totallist_sumURL = 0;
				$directory_trail = '';
				$directory_trails = '';
				
				if (array_key_exists('listing_title', $results)) {
					$listing_title = $results['listing_title'];
				}
			
				if (array_key_exists('listing_path_example', $results)) {
					$listing_path = '<a href="http://directory.thegreenpages.com.au' . $results["listing_path_example"] . '">' . $listing_title . '</a>';
				}
				
				if (array_key_exists('listing_expired', $results)) {
					if (!$results['listing_expired']) {
						$listing_expired = ' <span class="listing_status active">(Active)</span>';
					} else {
						$listing_expired = ' <span class="listing_status expired">(Expired on ' . date("d/m/Y", $results['listing_expired']) . ')</span>';
					}
				}
				
				if (array_key_exists('directory', $results)) {
					$i = 0;
					foreach ($results["directory"] as $value) {
						if ($value["directory_path"]) {
							$dir_pageViewURL = ($analytics->getPageviewsURL(urlencode($value["directory_path"])));
							$dir_sumURL = 0;
							foreach ($dir_pageViewURL as $data) {
								$dir_sumURL = $dir_sumURL + (int)$data;
							}
							$totaldir_sumURL = $totaldir_sumURL + $dir_sumURL;
	 					}
	 					
						if ($value["listing_path"]) {
							$list_pageViewURL = ($analytics->getPageviewsURL(urlencode($value["listing_path"])));
							$list_sumURL = 0;
							foreach ($list_pageViewURL as $data) {
								$list_sumURL = $list_sumURL + (int)$data;
							}
							$totallist_sumURL = $totallist_sumURL + $list_sumURL;
	 					}
	
						if (is_array($value["directory_trail"])) {
							$j = 0;
							foreach ($value["directory_trail"] as $crumb) {
								if (is_array($crumb)) {
									if (++$j > 1) {
										$directory_trail = $directory_trail . $crumb['title'] . " &gt; ";
									}
								}
							}
							if ($directory_trail != '') {
								$directory_trails = $directory_trails . '<br />&nbsp;&nbsp;<a href="http://directory.thegreenpages.com.au' . $value["directory_path"] . '">' . substr($directory_trail, 0, -6) . '</a>';
							}
							$directory_trail = '';
						}
					}
				}		
	  			
	  			# GET CLICK DATA	  			
				$click_track_tag = '/outbound/directory/' . $profile_author_id . '/';
  				$clickURL = ($analytics->getPageviewsURL($click_track_tag));
  				$sumClick = 0;
				foreach ($clickURL as $data) {
    				$sumClick = $sumClick + $data;		// Clicks for that button from all posts
	  			}
	  			
  				if ($sumClick == 0) {					#IF NO CLICKS YET, DISPLAY 'Unavailable'
    				$sumClick = 'Unavailable';
    			}
    			?>
    			<tr>
    				<td class="author_analytics_title"><?php echo $listing_path . $listing_expired . "<br /><span class=\"author-analytics-featuredin\">Featured in:</span>" . $directory_trails; ?></td>
    				<td class="author_analytics_cost">$39 per month</td>
    				<td class="author_analytics_category_impressions"><?php echo $totaldir_sumURL; ?></td>
    				<td class="author_analytics_page_impressions"><?php echo $totallist_sumURL; ?></td>
    				<td class="author_analytics_clicks"><?php echo $sumClick; ?></td>
    			</tr>
    		</table>
    		<div id="post-filter">The ability to edit your Directory Page details yourself will be ready soon! In the meantime:</div>
    		<div id="post-filter">
    			<a href="mailto:jesse.browne@thegreenpages.com.au?Subject=Please%20Update%20My%20Directory%20Page%20Details" >Update my Directory Page details here</a>
    		</div>
    		<div id="post-filter"></div>
    		<?php 		
		}	
		?>
		
		<?php   # FOR CONTRIBUTORS / CONTENT PARTNERS - DISPLAY ACTIVIST BAR / DONATE JOIN BUTTON ANALYTICS DATA
		if ( get_user_role( array('contributor') ) || get_user_role( array($rolecontributor, 'administrator') ) ) {
			
			# SET AND RESET SOME VARIABLES AND GET ACTIVIST BAR DATA FROM GA
			$start_date = '2012-01-01'; 	// Click tracking of activist buttons began just after this Date
			$today_date = date('Y-m-d'); 	// Todays Date
				
  			$analytics->setDateRange($start_date, $today_date); //Set date in GA $analytics->setMonth(date('$post_date'), date('$new_date'));

  			$donate_url = $profile_author->contributors_donate_url;
			$join_url = $profile_author->contributors_join_url;
			$letter_url = $profile_author->contributors_letter_url;
			$petition_url = $profile_author->contributors_petition_url;
			$volunteer_url = $profile_author->contributors_volunteer_url;
			
  			$button_labels = array('donate' => $donate_url, 
  									'join' =>  $join_url, 
  									'letter' =>  $letter_url, 
  									'petition' =>  $petition_url, 
  									'volunteer' =>  $volunteer_url);
  			$activist_clicks_sum = 0;
  			  			
			?>
			<h2>Activist Bar Analytics</h2>
			<table class="author_analytics">
				<tr>
					<td class="author_analytics_title">Activist Buttons</td>
					<td class="author_analytics_activist">Clicks</td>	
				</tr>

				<?php #DISPLAY TABLE ROWS WITH CLICK DATA FOR ACTIVIST BAR BUTTONS
		  		foreach ($button_labels as $label => $label_url) {
		  			if (!empty($label_url)) {
  					    $click_track_tag = '/outbound/activist-' . $label .'-button/' . $profile_author_id . '/' . $label_url . '/';
					    #var_dump($click_track_tag);
  					    $clickURL = ($analytics->getPageviewsURL($click_track_tag));
  					    $sumClick = 0;
					    foreach ($clickURL as $data) {
    					    $sumClick = $sumClick + $data;							// Clicks for that button from all posts
    					    $activist_clicks_sum = $activist_clicks_sum + $data;	// Total clicks for all activist bar buttons
  					    }
  					    if ($sumClick == 0) {			#IF NO CLICKS YET, DISPLAY 'Unavailable'
    					    $sumClick = 'Unavailable';
    				    }
  					    echo '<tr>
  					   	         <td class="author_analytics_title">' . $label . '</td>
  					       	     <td class="author_analytics_activist">' . $sumClick . '</td>
  					   	      </tr>';
		  			}
  				}
		  		if ($activist_clicks_sum == 0) {			#IF NO CLICKS YET, DISPLAY 'Unavailable'
    				$activist_clicks_sum = 'Unavailable';
    			}
				?>
						
			</table>
			<?php
			theme_profilecreate_post();
			if($activist_clicks_sum != 0) { 	#IF CLICKS DATA RETURNED, DISPLAY TOTAL
			?>
				<p>Your activist buttons have been clicked a total of</p> 
				<p><span class="big-number"><?php echo $activist_clicks_sum;?></span> times!</p>	
				<br />
			<?php
			}
			?>
			<div class="post-details"><a href="/wp-admin/profile.php">Enter or update urls for Activist Bar buttons</a></div>
			<br />
			<div id="post-details"></div>
			<?php 
		} 
		?>
		
		<?php # Profile page analytics if profile contact fields data present
				
		if (!empty($facebook) || !empty($linkedin) || !empty($twitter) || !empty($skype) || !empty($url)) {

		# SET AND RESET SOME VARIABLES AND GET PROFILE PAGE DATA FROM GA
		$start_date = '2012-07-01'; 	// Click tracking of profile page contact fields began just after this Date
		$today_date = date('Y-m-d'); 	// Todays Dat		
  		$analytics->setDateRange($start_date, $today_date); //Set date in GA $analytics->setMonth(date('$post_date'), date('$new_date'));			
		?>
			<h2>Profile Page Analytics</h2>
			<table class="author_analytics">
				<tr>
					<td class="author_analytics_title">Profile Page </td>
					<td class="author_analytics_activist">Clicks</td>	
				</tr>

				<?php 				  			
  				$profile_labels = array('facebook' => $facebook, 
  										'linkedin' =>  $linkedin, 
  										'twitter' =>  $twitter, 
  										'skype' =>  $skype, 
  										'website' =>  $url);
  				$profile_clicks_sum = 0;
		     
				foreach ($profile_labels as $label => $label_url) {
					if (!empty($label_url)) {
					    $click_track_tag = '/outbound/profile-' . $label .'/' . $profile_author_id .'/';
					    #var_dump($click_track_tag);
  					    $clickURL = ($analytics->getPageviewsURL($click_track_tag));
  					    $sumClick = 0;
					    foreach ($clickURL as $data) {
    					    $sumClick = $sumClick + $data;							// Clicks for that button from all posts
    					    $profile_clicks_sum = $profile_clicks_sum + $data;	// Total clicks for all activist bar buttons
  					    }
  					    if ($sumClick == 0) {			#IF NO CLICKS YET, DISPLAY 'Unavailable'
    					    $sumClick = 'Unavailable';
    				    }
    					echo '<tr>
  					    	     <td class="author_analytics_title">' . $label . '</td>
  					        	 <td class="author_analytics_activist">' . $sumClick . '</td>
  					      	  </tr>';
					}
  				}
		  		if ($profile_clicks_sum == 0) {			#IF NO CLICKS YET, DISPLAY 'Unavailable'
    				$profile_clicks_sum = 'Unavailable';
    			}
  			 ?>           
            </table>
            <?php 
            if($profile_clicks_sum != 0) { 	#IF CLICKS DATA RETURNED, DISPLAY TOTAL
				?>
				<p>Your Profile Page contact buttons have been clicked a total of</p> 
				<p><span class="big-number"><?php echo $profile_clicks_sum;?></span> times!</p>	
				<br />
				<?php
			}
			?>
			<div class="post-details"><a href="/wp-admin/profile.php">Enter or update urls/ids for Profile Page contact buttons</a></div>
			<br />
			<div id="post-details"></div>   
        <?php     
		}
    	?>
		
		<div class="post-details">Why are Clicks showing as 'Unavailable'?</div>
		<div class="post-details">As it's a new feature, the clicks column is showing data from late 01/2012 onwards, all preceding click data is unavailable here.</div>
		<div class="post-details">Earlier clicks may be found by looking for thegreenpages.com.au under 'Traffic Source' in your own Google Analytics account.</div>	
	</div>
<?php 
}

/** BUTTONS TO LINK FRONT END TO CREATE NEW POST ADMIN PAGES **/

function theme_homecreate_post(){
	?><div class="new-action"><span class="right"><?php theme_insert_homecreate_post(); ?></span><div class="clear"></div></div><?php
}

function theme_insert_homecreate_post(){
	echo '<a href="/wp-admin/index.php" class="new-post-action">Create a Post</a>';
	#theme_insert_newscreate_post();
	#theme_insert_eventcreate_post();
	#theme_insert_advertorialcreate_post();
	#theme_insert_competitioncreate_post();
	#theme_insert_projectcreate_post();
}

function theme_profilecreate_post(){
	#echo "<div id=\"post-filter\"><span class=\"right\">" . theme_insert_profilecreate_post() . "</span><div class=\"clear\"></div></div>";
	theme_insert_profilecreate_post();
}

function theme_insert_profilecreate_post(){
	// if user is logged in link to their own profile back end page, otherwise links to become a member page
	if ( is_user_logged_in() ) {
		#echo '<a href="/wp-admin/profile.php"><input type="button" value="Edit My Profile" /></a>';
	} else {
		#echo '<a href="/get-involved/become-a-member/"><input type="button" value="Sign up & create your own profile now!" /></a>';
		echo "
			<div class=\"profile-join-dialogue\">
				<a href=\"/wp-login.php?action=register\" class=\"simplemodal-register profile-action-join\">Sign up & create your profile now!</a>
				<br />
				<a href=\"/get-involved/become-a-member/\" class=\"profile-action-why\">Why join? Find out more about us.</a>
			</div>
		";
	}	
}	

function theme_singlecreate_post() {
	switch (get_post_type()) {
		case 'gp_news':
			theme_newscreate_post();
			break;
		case 'gp_projects':
			theme_projectcreate_post();
			break;
		case 'gp_advertorial':
			theme_advertorialcreate_post();
			break;
		case 'gp_competitions':
			theme_competitioncreate_post();
			break;
		case 'gp_events':
			theme_eventcreate_post();
			break;
		case 'gp_people':
			theme_profilecreate_post();
			break;
	}
}

function theme_newscreate_post(){
	?><div class="new-action"><span class="right"><?php theme_insert_newscreate_post(); ?></span><div class="clear"></div></div><?php
}

function theme_insert_newscreate_post(){
	global $user;
	
	// if user is loggin in as a contributor links to create new news page, otherwise links to Content Partner info page
	if ( is_user_logged_in() && get_user_role( array('contributor'), $user->ID ) ) {
		echo '<a href="/forms/create-news-post/" class="new-post-action">Post a News Story</a>';
	} else {
		echo '<a href="/get-involved/become-a-content-partner/" class="new-post-action">Post a News Story</a>';
	}
}

function theme_projectcreate_post(){
	?><div class="new-action"><span class="right"><?php theme_insert_projectcreate_post(); ?></span><div class="clear"></div></div><?php
}

function theme_insert_projectcreate_post(){
	echo '<a href="/forms/create-project-post/" class="new-post-action">Post a Project</a>';
}

function theme_advertorialcreate_post(){
	?><div class="new-action"><span class="right"><?php theme_insert_advertorialcreate_post(); ?></span><div class="clear"></div></div><?php 
}

function theme_insert_advertorialcreate_post(){
	echo '<a href="/forms/create-product-post/" class="new-post-action">Post a Product Ad</a>';
}

function theme_competitioncreate_post(){
	?><div class="new-action"><span class="right"><?php theme_insert_competitioncreate_post(); ?></span><div class="clear"></div></div><?php
}

function theme_insert_competitioncreate_post(){
	echo '<a href="/forms/create-competition-post/" class="new-post-action">Post a Competition</a>';
}

function theme_eventcreate_post(){
	?><div class="new-action"><span class="right"><?php theme_insert_eventcreate_post(); ?></span><div class="clear"></div></div><?php
}

function theme_insert_eventcreate_post(){
	echo '<a href="/forms/create-event-post/" class="new-post-action">Post an Event</a>';
}

function gp_select_createpost() {
	echo "
	<div class=\"profile-action-container no-js\">
		<a href=\"/wp-admin\" class=\"profile-action\">Create post<span class=\"bullet5\"></span></a>
		<ul class=\"profile-action-items\">
			<li><a href=\"/forms/create-news-post/\">News (Free)</a></li>
			<li><a href=\"/forms/create-event-post/\">Event (Free)</a></li>
			<li><a href=\"/forms/create-product-post/\">Product Feature ($89)</a></li>
			<li><a href=\"/forms/create-competition-post/\">Competition ($250)</a></li>
			<li><a href=\"/forms/create-project-post/\">Project (Free)</a></li>
		</ul>
	</div>
	";
}

?>