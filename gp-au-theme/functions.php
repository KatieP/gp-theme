<?php
/**
 * Green Pages Theme functions and definitions
 * Green Pages theme is dependant on the Green Pages plugin to run.
 * 
 * @package WordPress
 * @subpackage gp-au-theme
 * @since Green Pages Theme 1.0
 * 
 * @global array $wp_roles
 * @var array $sitemaptypes
 *
 * TIPS:
 * http://wordpress.stackexchange.com/questions/1567/best-collection-of-code-for-your-functions-php-file
 *
 * @TODO Continue moving functions related groups of functions
 *       into their own files in gp-wp-core plugin repo
 *       
 */

// Hide wordpress toolbar from front end
add_filter( 'show_admin_bar', '__return_false' );

/* Thumbnail support and custom image size for posts */
add_theme_support( 'post-thumbnails' );
add_image_size( 'gp_custom', 567, 425 );

global $wp_role;

$sitemaptypes = array(
	array('id' => 'sitemap', 'name' => 'Sitemap'),
	array('id' => 'googlenews', 'name' => 'Google News')
);

function redirect_to_users_own_profile_page() {
	/**
	 * Redirects logged in user to their own profile page
	 * Handy for redirecting from other plugins that can't access user variables 
 	 * 
	 * Author: Jesse Browne
	 *         jb@greenpag.es
	 **/

    if ( is_user_logged_in() ) {
        
        global $current_user;
        $post_author_url = ( isset($current_user) ? get_author_posts_url($current_user->ID) : "" );
        wp_redirect($post_author_url);
    
    }    
}

remove_action( 'login_form', 'username_or_email_login' );

function new_username_or_email_login() { ?>
	<script type="text/javascript" async>
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
	echo 'Welcome to the Green Pages backend editor! Go back to <a href="'. get_site_url() .'">front end</a>';
}

add_filter( 'update_footer', 'gp_remove_version_footer', 9999 );
function gp_remove_version_footer() {return '&nbsp;';}

add_filter('wp_mail_from','yoursite_wp_mail_from');
function yoursite_wp_mail_from($content_type) {
	return 'no-reply@thegreenpages.com.au';
}

add_filter('wp_mail_from_name','yoursite_wp_mail_from_name');
function yoursite_wp_mail_from_name($name) {
	return 'Green Pages';
}

function remove_toolbar_items() {
    global $wp_admin_bar;
    
    if ( !get_user_role( array('administrator') ) ) { 
        $wp_admin_bar->remove_menu('new-content');
        $wp_admin_bar->remove_menu('comments');
        $wp_admin_bar->remove_menu('updates');
        $wp_admin_bar->remove_menu('w3tc');
        $wp_admin_bar->remove_menu('my-sites');
        $wp_admin_bar->remove_menu('wp-logo');
        $wp_admin_bar->remove_menu('cloudflare');
    }
}
add_action('wp_before_admin_bar_render', 'remove_toolbar_items');

/* MANUALLY SETS WORD LENGTH OF EXCERPT FROM POST SHOWN IN INDEX AND PROFILE PAGES */
add_filter( 'excerpt_length', 'custom_excerpt_length', 999 );
function custom_excerpt_length( $length ) {
	return 25;
}

function add_jquery_data() { 
	global $current_user, $post;
	if ( parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH) == "/wp-admin/profile.php" || parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH) == "/wp-admin/user-edit.php" ) {
		if ( parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH) == "/wp-admin/profile.php" ) { ?>
			<script type="text/javascript" async>
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
	<script type="text/javascript" async>
		$(document).ready(function(){
			$("form#your-profile > h3:first").hide(); // profile options
			$("form#your-profile > table:first").hide(); // profile options
			//$("table.form-table:eq(1) tr:eq(3)").hide(); // nickname
		});
	</script>
		<?php } ?>
	<script type="text/javascript" async>
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
	<script type="text/javascript" async>
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
	<script type="text/javascript" async>
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
		
		<script type="text/javascript" async>
			/*$(document).ready(function() {
				$('#auth-youraccount').renderDash("#auth-dash-account");
				$('#auth-yourfavourites').renderDash("#auth-dash-favourites");
				$('#auth-yournotifications').renderDash("#auth-dash-notifications");
			 });*/
		</script>
		
		<!--[if lte IE 6]>
			<link type="text/css" rel="stylesheet" media="all" href="<?php echo get_bloginfo('template_url'); ?>/template/css/ie6.css" />
		<![endif]-->
		<?php
	}
}

/* REGISTER CUSTOM QUERY VARS */
# I'm assuming that query vars are registered in order to ensure rewrite rules work properly - in other words, don't change the order of the query vars.
add_filter( 'query_vars', 'register_query_vars' );
function register_query_vars( $query_vars )
{
    $query_vars[] = 'author_name';
    $query_vars[] = 'author_edit';
    $query_vars[] = 'post_type';
    $query_vars[] = 'country';
    $query_vars[] = 'state';
    $query_vars[] = 'city';
    $query_vars[] = 'page';
    return $query_vars;
}

/* ADD REWRITE RULES */
function change_author_permalinks() {
	/**
	 * See this for changing slug by role:
	 * http://wordpress.stackexchange.com/questions/17106/change-author-base-slug-for-different-roles
	 */
    global $wp_rewrite;
   	$wp_rewrite->author_base = 'profile';
}
add_action('init','change_author_permalinks');

/* AUTHOR EDIT REWRITE RULE */
add_action( 'author_rewrite_rules', 'edit_author_slug' ); #new-edit
function edit_author_slug( $author_rules ) {
    $author_rules['profile/([^/]+)/edit/account/?$'] = 'index.php?author_name=$matches[1]&author_edit=2';
    $author_rules['profile/([^/]+)/edit/locale/?$'] = 'index.php?author_name=$matches[1]&author_edit=3';
    $author_rules['profile/([^/]+)/edit/notifications/?$'] = 'index.php?author_name=$matches[1]&author_edit=4';
    $author_rules['profile/([^/]+)/edit/newsletters/?$'] = 'index.php?author_name=$matches[1]&author_edit=5';
    $author_rules['profile/([^/]+)/edit/privacy/?$'] = 'index.php?author_name=$matches[1]&author_edit=6';
    $author_rules['profile/([^/]+)/edit/password/?$'] = 'index.php?author_name=$matches[1]&author_edit=7';
    $author_rules['profile/([^/]+)/edit/admin/?$'] = 'index.php?author_name=$matches[1]&author_edit=8';
    $author_rules['profile/([^/]+)/edit/?$'] = 'index.php?author_name=$matches[1]&author_edit=1';
    return $author_rules;
}

function gp_rewrite_rules( $wp_rewrite ) {
	/**
	 * ADD CUSTOM REWRITE RULES
	 * see: http://wordpress.stackexchange.com/questions/4127/custom-taxonomy-and-pages-rewrite-slug-conflict-gives-404
	 */
	
    $newrules = array();
    $site_posttypes = Site::getPostTypes();
    foreach ( $site_posttypes as $site_posttype ) {
        $newrules[$site_posttype['slug'] . '/([a-z0-9]{1,2})/([a-z\-]+)/([a-z\-]+)/page/([0-9]{1,})/?$'] = 'index.php?post_type=' . $site_posttype['id'] . '&country=$matches[1]&state=$matches[2]&city=$matches[3]&page=$matches[4]';
        $newrules[$site_posttype['slug'] . '/([a-z0-9]{1,2})/([a-z\-]+)/page/([0-9]{1,})/?$'] = 'index.php?post_type=' . $site_posttype['id'] . '&country=$matches[1]&state=$matches[2]&page=$matches[3]';
        $newrules[$site_posttype['slug'] . '/([a-z0-9]{1,2})/([a-z\-]+)/([a-z\-]+)/?$'] = 'index.php?post_type=' . $site_posttype['id'] . '&country=$matches[1]&state=$matches[2]&city=$matches[3]';
        $newrules[$site_posttype['slug'] . '/([a-z0-9]{1,2})/([a-z\-]+)/?$'] = '/index.php?post_type=' . $site_posttype['id'] . '&country=$matches[1]&state=$matches[2]';
        $newrules[$site_posttype['slug'] . '/([a-z0-9]{1,2})/page/([0-9]{1,})/?$'] = '/index.php?post_type=' . $site_posttype['id'] . '&country=$matches[1]&page=$matches[2]';
        $newrules[$site_posttype['slug'] . '/([a-z0-9]{1,2})/?$'] = '/index.php?post_type=' . $site_posttype['id'] . '&country=$matches[1]';
    }
    
    $wp_rewrite->rules = $newrules + $wp_rewrite->rules;
    return $wp_rewrite->rules;
}
add_filter('generate_rewrite_rules','gp_rewrite_rules');

/* SWITCH TEMPLATES */
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

/* CHECK USER ROLES */
function get_user_role($roles_to_check = array('subscriber'), $user_id = 0) {
	global $wp_roles, $current_user;
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

function restrict_admin_area() {
	if ( !current_user_can('manage_options') ) {
        wp_redirect(site_url());
	}
}
add_action( 'admin_init', 'restrict_admin_area', 1 );

function pu_login_failed($user) {
  	// check what page the login attempt is coming from
  	$referrer = $_SERVER['HTTP_REFERER'];

  	// check that were not on the default login page
	if ( !empty($referrer) && !strstr($referrer,'wp-login') && !strstr($referrer,'wp-admin') && $user != null ) {
		// make sure we don't already have a failed login attempt
		if ( !strstr($referrer, '?login=failed') ) {
			// Redirect to the login page and append a querystring of login failed
	    	wp_redirect( $referrer . '?login=failed');
	    } else {
	      	wp_redirect( $referrer );
	    }
	    exit;
	}
}
add_action( 'wp_login_failed', 'pu_login_failed' ); // hook failed login

add_action( 'show_user_profile', 'my_show_extra_profile_fields' );
add_action( 'edit_user_profile', 'my_show_extra_profile_fields' );

function my_show_extra_profile_fields( $user ) {
	/**
	 * Exposes custom user meta in edit profile
	 * page in backend i.e. /wp-admin
	 * Only admins can access this page 
	 */
	
    global $current_user, $current_site, $gp, $wpdb;
    
	$profiletypes_user =    get_the_author_meta( 'profiletypes', $user->ID );
	$profiletypes_values =  array('administrator', 'editor', 'contributor', 'subscriber');
	
	$rolesubscriber =       'subscriber';
	$roleauthor =           'author';
	$roleeditor =           'editor';
	$rolecontributor =      'contributor';

	$user_roles =           $current_user->roles;
	$user_role =            array_shift($user_roles);
	
	if ( !get_user_role( array( $profiletypes_user['profiletypes'] ) ) && in_array( $profiletypes_user['profiletypes'], $profiletypes_values ) && in_array( $user_role, $profiletypes_values ) ) {
		${'role'. $profiletypes_user['profiletypes']} = $user_role;
		${'role'. $user_role} = $profiletypes_user['profiletypes'];
	}

	if ( get_user_role( array($rolesubscriber, 'administrator') ) ) {
		$bio_change = get_the_author_meta( 'bio_change', $user->ID );
		$bio_projects = get_the_author_meta( 'bio_projects', $user->ID );
		$bio_stuff = get_the_author_meta( 'bio_stuff', $user->ID );
		$user_default_location = get_the_author_meta( 'user_default_location', $user->ID );
		$user_default_tags = get_the_author_meta( 'user_default_tags', $user->ID );
		$gp_google_geo_location = get_the_author_meta( 'gp_google_geo_location', $user->ID );
		$gp_google_geo_latitude = get_the_author_meta( 'gp_google_geo_latitude', $user->ID );
		$gp_google_geo_longitude = get_the_author_meta( 'gp_google_geo_longitude', $user->ID );
		$gp_google_geo_country = get_the_author_meta( 'gp_google_geo_country', $user->ID );
		$gp_google_geo_administrative_area_level_1 = get_the_author_meta( 'gp_google_geo_administrative_area_level_1', $user->ID );
		$gp_google_geo_administrative_area_level_2 = get_the_author_meta( 'gp_google_geo_administrative_area_level_2', $user->ID );
		$gp_google_geo_administrative_area_level_3 = get_the_author_meta( 'gp_google_geo_administrative_area_level_3', $user->ID );
		$gp_google_geo_locality = get_the_author_meta( 'gp_google_geo_locality', $user->ID );
		$gp_google_geo_locality_slug = get_the_author_meta( 'gp_google_geo_locality_slug', $user->ID );
		$gp_user_tags = get_the_author_meta( 'gp_user_tags', $user->ID );
		
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
			<tr>
				<th><label for="gp_google_geo_location">My Location</label></th>
				<td>
					<input type="text" name="gp_google_geo_location" id="gp_google_geo_location" value="' . $gp_google_geo_location . '"/>
					<br /><span class="description">Location!</span>
					<div class="gp-meta">
					    <input name="gp_google_geo_latitude" id="gp_google_geo_latitude" type="text" value="' . $gp_google_geo_latitude . '" readonly="readonly">
					    <input name="gp_google_geo_longitude" id="gp_google_geo_longitude" type="text" value="' . $gp_google_geo_longitude . '" readonly="readonly">
					    <input name="gp_google_geo_country" id="gp_google_geo_country" type="text" value="' . $gp_google_geo_country . '" readonly="readonly">
					    <input name="gp_google_geo_administrative_area_level_1" id="gp_google_geo_administrative_area_level_1" type="text" value="' . $gp_google_geo_administrative_area_level_1 . '" readonly="readonly">
					    <input name="gp_google_geo_administrative_area_level_2" id="gp_google_geo_administrative_area_level_2" type="text" value="' . $gp_google_geo_administrative_area_level_2 . '" readonly="readonly">
					    <input name="gp_google_geo_administrative_area_level_3" id="gp_google_geo_administrative_area_level_3" type="text" value="' . $gp_google_geo_administrative_area_level_3 . '" readonly="readonly">
					    <input name="gp_google_geo_locality" id="gp_google_geo_locality" type="text" value="' . $gp_google_geo_locality . '" readonly="readonly">
					    <input name="gp_google_geo_locality_slug" id="gp_google_geo_locality_slug" type="text" value="' . $gp_google_geo_locality_slug . '" readonly="readonly">
					</div>
				</td>
			</tr>
			<tr>
				<th><label for="gp_user_tags">My Tags</label></th>
				<td>
					<input type="text" name="gp_user_tags" id="gp_user_tags" value="' . $gp_user_tags . '"/>
					<br /><span class="description">Tags!</span>
				</td>
			</tr>			
		</table>
		');
	}
	
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
		$contributors_blurb =          get_the_author_meta( 'contributors_blurb', $user->ID );
		$contributors_posttagline =    get_the_author_meta( 'contributors_posttagline', $user->ID );
		$contributors_donate_url =     get_the_author_meta( 'contributors_donate_url', $user->ID );
		$contributors_join_url =       get_the_author_meta( 'contributors_join_url', $user->ID );
		$contributors_petition_url =   get_the_author_meta( 'contributors_petition_url', $user->ID );
		$contributors_volunteer_url =  get_the_author_meta( 'contributors_volunteer_url', $user->ID );
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
		</table> ');
	} ?>
	
	<!-- </table> -->

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
	
	$notification_setting = get_user_meta($user->ID, 'notification_setting', true);
	
	switch ($notification_setting) {
	    case 'weekly_email':
            $check_weekly_email = ' checked="checked"';
            $check_system_email = '';
            break;
	    case 'system_email':
            $check_weekly_email = '';
            $check_system_email = ' checked="checked"';
            break;
	} ?>
	      <h3>Notification Settings</h3>
		  <table class="form-table">
		      <tr>
		          <th>Weekly Email - Green Razor</th>
		          <td><input type="radio" name="notification_setting" id="weekly_email" value="weekly_email" <?php echo $check_weekly_email; ?> /></td>
		      </tr>
		      <tr>
		          <th>System Emails Only - Rare</th>
		          <td><input type="radio" name="notification_setting" id="system_email" value="system_email" <?php echo $check_system_email; ?> /></td>
		      </tr>
		  </table> <?php
	
    /**
     *  HIDE THE FOLLOWING CODE BLOCK WITH MISC META DATA FROM NON ADMINS, CODE STILL NEEDS TO RUN THOUGH 
     *  OTHERWISE EVERYTIME A NON ADMIN UPDATES THEIR PROFILE PAGE THE META DATA IS LOST 
     **/
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
		/* SET AND DISPLAY DIRECTORY ID AND URL STRINGS AND YOUTUBE ID FOR VIDEO NEWS IFRAME*/
		$old_crm_id =          get_the_author_meta( 'old_crm_id', $user->ID );
		$wp_id =               $user->ID;
		$directory_page_url =  get_the_author_meta( 'directory_page_url', $user->ID );
		$video_news_id =       get_the_author_meta( 'video_news_id', $user->ID );		
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
				<th><label for="video_news_id">Video News ID</label></th>
				<td><input type="text" 	name="video_news_id" id="video_news_id" class="regular-text" maxlength="255" value="' . esc_attr($video_news_id) . '" /><br />
				<span class="description">This is used to insert the ID into the iframe that displays the video news on right sidebar</span></td>
			</tr>			
		</table>
	');

	if ( !get_user_role( array('administrator') ) ) {	
		echo '</div>';
	}
} 

add_action( 'personal_options_update', 'my_save_extra_profile_fields' );
add_action( 'edit_user_profile_update', 'my_save_extra_profile_fields' );

function my_save_extra_profile_fields( $user_id ) {
	/**
	 * Stores custom user meta data in wp_usermeta table
	 */
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
	update_usermeta($user_id, 'contributors_petition_url', $_POST['contributors_petition_url'] );
	update_usermeta($user_id, 'contributors_volunteer_url', $_POST['contributors_volunteer_url'] );	
	update_usermeta($user_id, 'notification_setting', $_POST['notification_setting'] );
	update_usermeta($user_id, 'notification', $notification_post );
	update_usermeta($user_id, 'reg_advertiser', $reg_advertiser );
	update_usermeta($user_id, 'old_crm_id', $_POST['old_crm_id'] );
	update_usermeta($user_id, 'directory_page_url', $_POST['directory_page_url'] );
	update_usermeta($user_id, 'chargify_self_service_page_url', $_POST['chargify_self_service_page_url'] );
	update_usermeta($user_id, 'video_news_id', $_POST['video_news_id'] );
	update_usermeta($user_id, 'user_default_location', $_POST['user_default_location'] );
	update_usermeta($user_id, 'user_default_tags', $_POST['user_default_tags'] );
	update_usermeta($user_id, 'gp_google_geo_location', $_POST['gp_google_geo_location'] );
	update_usermeta($user_id, 'gp_google_geo_latitude', $_POST['gp_google_geo_latitude'] );
	update_usermeta($user_id, 'gp_google_geo_longitude', $_POST['gp_google_geo_longitude'] );
	update_usermeta($user_id, 'gp_google_geo_country', $_POST['gp_google_geo_country'] );
	update_usermeta($user_id, 'gp_google_geo_administrative_area_level_1', $_POST['gp_google_geo_administrative_area_level_1'] );
	update_usermeta($user_id, 'gp_google_geo_administrative_area_level_2', $_POST['gp_google_geo_administrative_area_level_2'] );
	update_usermeta($user_id, 'gp_google_geo_administrative_area_level_3', $_POST['gp_google_geo_administrative_area_level_3'] );
	update_usermeta($user_id, 'gp_google_geo_locality', $_POST['gp_google_geo_locality'] );
	update_usermeta($user_id, 'gp_google_geo_locality_slug', $_POST['gp_google_geo_locality_slug'] );
	update_usermeta($user_id, 'gp_user_tags', $_POST['gp_user_tags'] );
	
	/* UPDATE CAMPAIGN MONITOR - USER GREENRAZOR SUBSCRIPTION */
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

/* ADD THUMBNAILS SUPPORT */
# note: http://emrahgunduz.com/categories/development/wordpress/wordpress-how-to-show-the-featured-image-of-posts-in-social-sharing-sites/
# note: http://markjaquith.wordpress.com/2009/12/23/new-in-wordpress-2-9-post-thumbnail-images/
add_theme_support( 'post-thumbnails', array( 'post', 'gp_news', 'gp_events', 'gp_competitions', 'gp_jobs', 'gp_people', 'gp_advertorial', 'gp_projects', 'gp_katiepatrick', 'gp_productreview', 'gp_greengurus' ) );
add_image_size('homepage-thumbnail', 110, 110, true);
add_image_size('icon-thumbnail', 50, 50, true);
add_image_size('dash-thumbnail', 35, 35, true);
# add_image_size('homepage-featured', 240, 180, true);

/* combined */
add_action( 'init', 'createPostOptions' );
global $gp;

$ns_loc = $gp->location['country_iso2'] . '\\Edition';
$edition_posttypes = $ns_loc::getPostTypes();

for($index = 0; $index < count($edition_posttypes); $index++) {
	if ($edition_posttypes[$index]['enabled'] == true) {
		add_filter( 'manage_edit-' . $edition_posttypes[$index]['id'] . '_columns', 'editColumns' );
	}
	# add_action( 'manage_posts_custom_column', $posttypes[$index]['id'] . '_custom_columns' );
}
add_action( 'manage_posts_custom_column', 'new_custom_columns' );
add_filter( 'post_updated_messages', 'updated_messages' );

function createPostOptions () {
    global $gp;
    $ns_loc = $gp->location['country_iso2'] . '\\Edition';
    $edition_posttypes = $ns_loc::getPostTypes();
    
	for($index = 0; $index < count($edition_posttypes); $index++) {
		if ($edition_posttypes[$index]['enabled'] == true) {
			register_post_type( $edition_posttypes[$index]['id'] , $edition_posttypes[$index]['args'] );
			register_taxonomy( $edition_posttypes[$index]['id'] . '_category', $edition_posttypes[$index]['id'], $edition_posttypes[$index]['taxonomy'] );
		}
	}	
	//flush_rewrite_rules();
}

function editColumns($columns) {
    global $gp;
    $ns_loc = $gp->location['country_iso2'] . '\\Edition';
    $edition_posttypes = $ns_loc::getPostTypes();
    
	for($index = 0; $index < count($edition_posttypes); $index++) {
		if ($edition_posttypes[$index]['enabled'] == true) {
			if ( substr(current_filter(), 12, -8) == $edition_posttypes[$index]['id'] ) {
				$mycolumns = $edition_posttypes[$index]['columns'];
				$myname = $edition_posttypes[$index]['id'];
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
	global $post, $gp;
	$ns_loc = $gp->location['country_iso2'] . '\\Edition';
    $edition_posttypes = $ns_loc::getPostTypes();

    $custom = get_post_custom();
    for($index = 0; $index < count($edition_posttypes); $index++) {
    	if ($edition_posttypes[$index]['enabled'] == true) {
		    switch ($column) {
		    	case 'col_' . $edition_posttypes[$index]['id'] . '_author':
		    		echo get_userdata($post->post_author)->display_name;
		    	break;
	            case 'col_' . $edition_posttypes[$index]['id'] . '_categories':
	                $categories = get_the_terms($post->ID, $edition_posttypes[$index]['id'] . '_category');
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
	            case 'col_' . $edition_posttypes[$index]['id'] . '_tags':
	            	$tags = get_the_tags($post->ID, $edition_posttypes[$index]['id'] . '_tags');
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
	            case 'col_' . $edition_posttypes[$index]['id'] . '_comments':
		    		echo $post->comment_count;
		    	break;
		    	case 'col_' . $edition_posttypes[$index]['id'] . '_date':
		    		echo mysql2date('Y/m/d', $post->post_date);
		    	break;
	            case 'col_' . $edition_posttypes[$index]['id'] . '_dates':
	                $startd = isset($custom[$edition_posttypes[$index]['id'] . '_startdate'][0]) ? date("F j, Y", $custom[$edition_posttypes[$index]['id'] . '_startdate'][0]) : "";
	                $endd = isset($custom[$edition_posttypes[$index]['id'] . '_enddate'][0]) ? date("F j, Y", $custom[$edition_posttypes[$index]['id'] . '_enddate'][0]) : "";
	                echo $startd . '<br /><em>' . $endd . '</em>';
	            break;
			}
    	}
    }
}

function updated_messages( $messages ) {
  global $post, $post_ID, $gp;
  $ns_loc = $gp->location['country_iso2'] . '\\Edition';
  $edition_posttypes = $ns_loc::getPostTypes();
    
  for($index = 0; $index < count($edition_posttypes); $index++) {
  	if ($edition_posttypes[$index]['enabled'] == true) {
	  	$messages[$edition_posttypes[$index]['id']] = array(
		    0 => '', // Unused. Messages start at index 1.
		    1 => sprintf( __($edition_posttypes[$index]['name'] . ' updated. <a href="%s">View post</a>'), esc_url( get_permalink($post_ID) ) ),
		    2 => __('Custom field updated.'),
		    3 => __('Custom field deleted.'),
		    4 => __($edition_posttypes[$index]['name'] . ' updated.'),
		    /* translators: %s: date and time of the revision */
		    5 => isset($_GET['revision']) ? sprintf( __($edition_posttypes[$index]['name'] . ' restored to revision from %s'), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
		    6 => sprintf( __($edition_posttypes[$index]['name'] . ' published. <a href="%s">View ' . $edition_posttypes[$index]['name'] . '</a>'), esc_url( get_permalink($post_ID) ) ),
		    7 => __($edition_posttypes[$index]['name'] . ' saved.'),
		    8 => sprintf( __($edition_posttypes[$index]['name'] . ' submitted. <a target="_blank" href="%s">Preview ' . $edition_posttypes[$index]['name'] . '</a>'), esc_url( add_query_arg( 'preview', 'true', get_permalink($post_ID) ) ) ),
		    9 => sprintf( __($edition_posttypes[$index]['name'] . ' scheduled for: <strong>%1$s</strong>. <a target="_blank" href="%2$s">Preview ' . $edition_posttypes[$index]['name'] . $edition_posttypes[$index]['name'] . '</a>'),
		      // translators: Publish box date format, see http://php.net/date
		      date_i18n( __( 'M j, Y @ G:i' ), strtotime( $post->post_date ) ), esc_url( get_permalink($post_ID) ) ),
		    10 => sprintf( __($edition_posttypes[$index]['name'] . ' draft updated. <a target="_blank" href="%s">Preview ' . $edition_posttypes[$index]['name'] . '</a>'), esc_url( add_query_arg( 'preview', 'true', get_permalink($post_ID) ) ) ),
	  	);
  	}
  }
  
  return $messages;
}

/* OVERRIDE 404 ON EMPTY ARCHIVE */
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


/* GET RELATIVE DATE */
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
			$displaydate = 'Continue readingÔøΩ';
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
	$pageposts = $wpdb->get_row($querystr);
	$numPosts = $wpdb->num_rows-1;
	
	if ($pageposts && $numPosts != -1) {
		echo '<div id="relevant-posts"><span class="title">More <a href="' . $posttype_url . '">' . $posttype_title . '</a> you might like:</span>';
		foreach ($pageposts as $rpost) {
			setup_postdata($rpost);
			echo '<div class="relevant-item">';
			if ( has_post_thumbnail() ) {
				$imageArray = wp_get_attachment_image_src( get_post_thumbnail_id($rpost->ID), 'icon-thumbnail' );
				$imageURL = $imageArray[0];
				$link = get_permalink($rpost->ID);
				echo '<a href="' . $link . '" class="hp_minithumb"><img src="' . $imageURL  . '" alt="' . get_the_title( get_post_thumbnail_id($rpost->ID) ) . '" width="50" height="50" /></a>';
				unset($imageArray, $imageURL);
			}
			?>
			<a href="<?php echo $link; ?>" title="<?php esc_attr($rpost->post_title); ?>" rel="bookmark" class="title"><?php echo $rpost->post_title; ?></a>
			<?php if ( $rpost->comment_status == 'open' ) { ?>
				<div class="clear"></div><div class="comment-hp"><a href="<?php echo $link; ?>#comments"><span class="comment-mini"></span></a><a href="<?php echo $link; ?>#disqus_thread" class="comment-hp"><span class="comment-mini-number dsq-postid"><?php echo $rpost->comment_count; ?></span></a></div>
			<?php
			}
			echo '<div class="clear"></div></div>';
			unset($link, $rpost);
		}
		echo '</div>';
	}
}

function get_events($filterby_country = '', $filterby_state = '', $filterby_city = '', $max_results = '', $offset_num = '') {
	/**
	 * Returns set of events from database.
	 * Called by events_index() and get_calendar_and_upcoming_events()
	 * 
	 * Author: Jesse Browne
	 *         jb@greenpag.es
	 **/
    
    global $wpdb, $gp;    
	$epochtime =  strtotime('now'); 
    $limit_by =   (!empty($max_results)) ? 'LIMIT %d' : '';
    $offset =     (!empty($offset_num)) ? 'OFFSET %d' : '';
    
	/* SQL QUERY FOR COMING EVENTS */
	$querystr = $wpdb->prepare(
	        "SELECT DISTINCT
                " . $wpdb->prefix . "posts.*,
	            m0.meta_value AS _thumbnail_id,
	            m1.meta_value AS gp_events_enddate,
	            m2.meta_value AS gp_events_startdate,
	            m3.meta_value AS gp_google_geo_country,
	            m4.meta_value AS gp_google_geo_administrative_area_level_1,
	            m5.meta_value AS gp_google_geo_locality_slug,
	            m6.meta_value AS gp_google_geo_locality
	        FROM $wpdb->posts
	            LEFT JOIN " . $wpdb->prefix . "postmeta AS m0 on m0.post_id=" . $wpdb->prefix . "posts.ID and m0.meta_key='_thumbnail_id'
                LEFT JOIN " . $wpdb->prefix . "postmeta AS m1 on m1.post_id=" . $wpdb->prefix . "posts.ID and m1.meta_key='gp_events_enddate'
                LEFT JOIN " . $wpdb->prefix . "postmeta AS m2 on m2.post_id=" . $wpdb->prefix . "posts.ID and m2.meta_key='gp_events_startdate'
                LEFT JOIN " . $wpdb->prefix . "postmeta AS m3 on m3.post_id=" . $wpdb->prefix . "posts.ID and m3.meta_key='gp_google_geo_country'
                LEFT JOIN " . $wpdb->prefix . "postmeta AS m4 on m4.post_id=" . $wpdb->prefix . "posts.ID and m4.meta_key='gp_google_geo_administrative_area_level_1'
                LEFT JOIN " . $wpdb->prefix . "postmeta AS m5 on m5.post_id=" . $wpdb->prefix . "posts.ID and m5.meta_key='gp_google_geo_locality_slug'
                LEFT JOIN " . $wpdb->prefix . "postmeta AS m6 on m6.post_id=" . $wpdb->prefix . "posts.ID and m6.meta_key='gp_google_geo_locality'
            WHERE
	            post_status='publish'
                AND post_type='gp_events'
                    " . $filterby_country . "
                    " . $filterby_state . "
                    " . $filterby_city . "
	            AND CAST(CAST(m1.meta_value AS UNSIGNED) AS SIGNED) >= %d
            ORDER BY gp_events_startdate ASC ".
	        $limit_by .
	        $max_results . 
	        $offset .";",
            $epochtime,
            $max_results,
            $offset_num
	);
	
	$pageposts = $wpdb->get_results($querystr, OBJECT);

	return $pageposts;
}

/* SHOWS THE NEXT 3 UP COMING EVENTS UNDER THE EVENT CALENDAR IN SIDEBAR-RIGHT */ 
function get_calendar_and_upcoming_events() {
    
	global $wpdb, $post, $gp;
	
	$user_country =           $gp->location['country_iso2'];
	$location_country_slug =  ( !empty($_GET['location_slug_filter']) ) ? $_GET['location_slug_filter'] : $user_country;
	$querystring_country =    strtoupper( $location_country_slug );
	$filterby_country =       (!empty($querystring_country)) ? $wpdb->prepare( " AND m3.meta_value=%s ", $querystring_country ) : '';
    
	$pageposts = get_events($filterby_country);
    $num_posts = count($pageposts);
    
    if ($num_posts == 0) {
        $pageposts = get_events();
    }
    
	$numPosts = $wpdb->num_rows-1;
	$location_filter_uri =  get_location_filter_uri();				
	
	if ($pageposts && $numPosts != -1) {
		echo '<div id="relevant-posts">
				<span class="title">
					Upcoming Events
				</span>'; 
		$i = 0;
		# Format event data and store in a string for use with jquery datepicker EVENT CALENDAR
		$event_str = '[';
		
		foreach ($pageposts as $post) {
			setup_postdata($post);
			
			$event_title = get_the_title($post->ID);
			
			$displayday =         date('j', $post->gp_events_startdate);
			$displaymonth =       date('M', $post->gp_events_startdate);
			$str_month =          date('m', $post->gp_events_startdate);
			$displayyear =        date('y', $post->gp_events_startdate);
			
			$displayendday =      date('j', $post->gp_events_enddate);
			$displayendmonth =    date('M', $post->gp_events_enddate);
			$str_endmonth =       date('m', $post->gp_events_enddate);
			
			$event_link_url =     get_permalink($post->ID) . $location_filter_uri;
			$post_id =            $post->ID;
			
			$displaytitle =       '<a href=\"'. $event_link_url . '\" title=\"'. $event_title .'\">'. $event_title .'</a>';
			$event_date_string =  'new Date('. $post->gp_events_startdate .'000)';
			
			$event_str .=         '{ Title: "'. $displaytitle .'", Date: '. $event_date_string .' },';
			
			/** DISPLAY NEXT 3 EVENTS BELOW CALENDAR  **/
			if ($i < 3) {
				echo '<div class="relevant-item">';
				if ( has_post_thumbnail() ) {	# DISPLAY EVENTS FEATURED IMAGE
					$imageArray = wp_get_attachment_image_src( get_post_thumbnail_id($post->ID), 'icon-thumbnail' );
					$imageURL = $imageArray[0];
					echo '<a href="' . $event_link_url . '" class="hp_minithumb"><img src="' . $imageURL . '" alt="' . get_the_title( get_post_thumbnail_id($post->ID) ) . '"  /></a>';
				} else {
					$imageArray = wp_get_attachment_image_src( get_post_thumbnail_id(322), 'icon-thumbnail' ); 	# DEFAULT IMAGE STORED IN POST WHERE ID = 322
					$imageURL = $imageArray[0];
					echo '<a href="' . $event_link_url . '" class="hp_minithumb"><img src="' . $imageURL . '" alt="' . get_the_title( get_post_thumbnail_id($post->ID) ) . '"  /></a>';
				}
				?>
				<a href="<?php echo $event_link_url; ?>" title="<?php echo $event_title; ?>" rel="bookmark" class="title"><?php echo $event_title; ?></a>
				<?php 
				echo '<div class="post-details">' . $post->gp_google_geo_locality . ', ' . $post->gp_google_geo_administrative_area_level_1 . '<br />';
				if ($displayday == $displayendday) {
					echo $displayday . ' ' . $displaymonth;
				} else {
					echo $displayday . ' ' . $displaymonth . ' - ' . $displayendday . ' ' . $displayendmonth;
				}
				echo '    </div>
				      <div class="clear"></div>
				      </div>';
				$i++;
			}
		}
		
		$event_str .= ']';
		echo '</div>';
	}
	
	/** 
	 *  RUN JAVASCRIPT THAT DISPLAYS EVENT CALENDAR AND HIGHLIGHTS DATES WITH EVENTS
	 *  USING JQUERY DATEPICKER
	 *  CLICKING ON A HIGHLIGHTED DATE WILL DISPLAY LINKS TO EVENT PAGES IN JQUERY DIALOG BOX
	 **/
	
	if ( isset($event_str) ) {
	echo '<script type="text/javascript">
				var events = '. $event_str .';
				console.log(events);
				$("#eventCalendar").datepicker({
    				beforeShowDay: function(date) {
    					var result = [true, \'\', null];
    					var matching = $.grep(events, function(event) {
       						return event.Date.toDateString() === date.toDateString();
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
       						
	            			if (selectedDate.toDateString() === date.toDateString()) {
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
   							
   							var x = $(this).offset().left - $(document).scrollLeft() - $(this).outerWidth() - 30;
                            var y = $(this).offset().top - $(document).scrollTop();
   							
							$(function() {
								$( "#event-dialog" ).html(dialog_str);
								$( "#event-dialog" ).dialog({ 
								    position: [x,y], 
								    minHeight: 170, 
								    width: 270, 
								    dialogClass: "event-dialog-wrap",
								    open: function(e, ui) { 
								        $(document).bind(\'click\', function(e) {
								            if ($(e.target).parents(".event-dialog-wrap").length == 0) {
								                $("#event-dialog").dialog(\'close\');
                                            }
                                        }); 
                                    } 
                                }).delay(20000).hide(function() { $( "#event-dialog" ).dialog(\'close\') });
							});   							
       						//alert(event.Title);
   						}
					}
				});
				
				$(window).resize(function() {
                    if( $("#event-dialog").is(\':visible\') ) {
    				    var x = $("#eventCalendar").offset().left - $(document).scrollLeft() - $("#eventCalendar").outerWidth() - 4;
                        var y = $("#eventCalendar").offset().top - $(document).scrollTop();
                        $("#event-dialog").dialog({ position: [x,y] });
                    }
                });
			</script>';
	}
}

/* SUBMIT POSTS AND REDIRECT TO CHARGIFY */
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



/* EXTRA SPECIAL STUFF */

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

function get_post_location_json_data($current_post = false) {
    /**
     * Returns a single JSON structured string holding post data:
     * title, link, lat, long and custom marker of an indivdual post.
     * 
     * Authors: Jesse Browne
     * 			jb@greenpag.es
     **/

    global $post;
    $template_url = get_bloginfo('template_url');
    
    # Set post data
    $post_type =            get_post_type();	
    $post_id =              $post->ID;
    $post_title =           get_the_title($post->ID);
    $post_link_url =        get_permalink($post->ID);
    $location_filter_uri =  get_location_filter_uri();
    $title_link =           '<a href=\"'. $post_link_url . $location_filter_uri .'\" title=\"'. $post_title . '\">'. $post_title .'</a>';
    
    # Set location keys
    $lat_post_key =        'gp_google_geo_latitude';
    $long_post_key =       'gp_google_geo_longitude';
    
	# Assign icon for custom marker depending on post type
	$post_icon = $template_url . '/template/icons/post-type-icons/';
	switch ($post_type) {
	    case 'gp_news':
            $post_icon .= ($current_post == false) ? 'news-icon.png' : 'news-icon-large.png';
	        break;
	    case 'gp_projects':
	    	$post_icon .= ($current_post == false) ? 'project-icon.png' : 'project-icon-large.png';
	        break;
		case 'gp_advertorial':
			$post_icon .= ($current_post == false) ? 'product-icon.png' : 'product-icon-large.png';
	        break;
	    case 'gp_events':
	    	$post_icon .= ($current_post == false) ? 'event-icon.png' : 'event-icon-large.png';
	        break;
	}
    
	# Get post location meta (post id, key, true/false)
    $lat_post =  get_post_meta($post_id, $lat_post_key, true);
    $long_post = get_post_meta($post_id, $long_post_key, true);

    if ( empty($lat_post) || empty($long_post) ) { return ''; }
    
	# Construct json data and return for google map marker display
	$json = '{ 
	             Title: "'.      $post_title .'",
	             Title_link: "'. $title_link .'",
	             Post_lat: "'.   $lat_post   .'", 
	             Post_long: "'.  $long_post  .'", 
	             Post_icon: "'.  $post_icon  .'" 
             },';
	
	return $json;
}

/** GOOGLE MAP SHOWS SURROUNDING POSTS ON MAP **/

function display_google_map_posts_and_places_autocomplete($json, $map_canvas, $center_map_lat, $center_map_long, $zoom) {
    
    /**
     * Accepts json structured string holding post title link, lat and long data on each relevant post;
     * name of map container element; lat and long of map center; and map zoom 
     * 
     * Constructs google map and places custom marker on each post location,
     * Each marker shows a lightbox with a link to post on click
     * 
     * Google maps api places library called from footer.php, this enables text input autocomplete
     * for the location filter in the header tagline.
     * 
     * Additional work to set location filtering data i.e. lat/long, country,
     * city name, state etc also done here
     * 
     * Called by get_google_map() & get_google_world_map()
     * 
     * @todo Map display and location filtering / autocomplete should 
     *       probably be decoupled in the future
     * 
     * Authors: Katie Patrick & Jesse Browne
     * 			katie.patrick@greenpag.es
     * 			jb@greenpag.es
     **/

    ?>
    <script type="text/javascript" async>
        //Event Objects to make surrounding markers 
        var json = <?php echo $json; ?>;
      
        //Function that calls map, centres map around post location, styles map 
        function initialize() {
            var myLatlng = new google.maps.LatLng(
                               <?php echo $center_map_lat .','. $center_map_long; ?>
                           );

            var styles = <?php custom_google_map_styles(); ?>;
            
            var mapOptions = {
                zoom: <?php echo $zoom; ?>,
                center: myLatlng, 
                styles: styles,          
                mapTypeId: google.maps.MapTypeId.ROADMAP
            };
            
            //Adds map to map_canvas div in DOM so it is visible 
            var map = new google.maps.Map(document.getElementById('<?php echo $map_canvas; ?>'),
                      mapOptions);

			// Do additional work to enable location filter autocomplete field 
            var dummy_map =    new google.maps.Map(document.getElementById('dummy_map_canvas'), mapOptions);
            var dummy_marker = new google.maps.Marker({map: dummy_map});
            
            // Creating a global infoWindow object that will be reused by all markers 
		    var infoWindow = new google.maps.InfoWindow();
            
	        var options = {
	                types: ['(cities)']
	            };
	            
	        var input = document.getElementById('location_filter');
	        var autocomplete = new google.maps.places.Autocomplete(input, options);

	        autocomplete.bindTo('bounds', dummy_map);
	        
	        google.maps.event.addListener(autocomplete, 'place_changed', function() {

	        	var place = autocomplete.getPlace();

	            if (place.geometry.viewport) {
	                dummy_map.fitBounds(place.geometry.viewport);
	            } else {
	                dummy_map.setCenter(place.geometry.location);
	            }
	        		      
            	var latitude_filter =           place.geometry.location.lat();
            	var longitude_filter =          place.geometry.location.lng();
            	var location_slug_filter =      '';
            	var admin_area_level_1_filter = '';
                var admin_area_level_2_filter = '';
                var admin_area_level_3_filter = '';
                var locality_filter =           '';
            	
                for (var i = 0; i < place.address_components.length; i++) {
                    var addr = place.address_components[i];
                    if (addr.types[0] == 'country')                     {location_slug_filter = addr.short_name;}
                    if (addr.types[0] == 'administrative_area_level_1') {admin_area_level_1_filter = addr.short_name;}
                    if (addr.types[0] == 'administrative_area_level_2') {admin_area_level_2_filter = addr.short_name;}
                    if (addr.types[0] == 'administrative_area_level_3') {admin_area_level_3_filter = addr.short_name;}
                    if (addr.types[0] == 'locality')                    {locality_filter = addr.short_name;}
                }

                var url_prefix =           document.getElementById('location_filter_url_prefix').value;
                var location =             document.getElementById('location_filter').value;
				var slug =                 location_slug_filter.toLowerCase();
                var admin_1 =              admin_area_level_1_filter.toLowerCase();
                var admin_2 =              admin_area_level_2_filter.toLowerCase();
                var admin_3 =              admin_area_level_3_filter.toLowerCase();
                var raw_locality =         locality_filter.toLowerCase();
                var locality =             raw_locality.replace(' ','-');
                var location_filter_url =  url_prefix + '/?' + 'location_filter=' + location + '&' + 
                                           'latitude_filter=' + latitude_filter + '&' + 'longitude_filter=' + longitude_filter +
                                           '&' + 'location_slug_filter=' + slug + '&' + 'location_state_filter=' + admin_1 + 
                                           '&' + 'locality_filter=' + raw_locality;
                
                document.getElementById('latitude_filter').value =            latitude_filter;
                document.getElementById('longitude_filter').value =           longitude_filter;
                document.getElementById('location_slug_filter').value =       slug;
                document.getElementById('admin_area_level_1_filter').value =  admin_1;
                document.getElementById('admin_area_level_2_filter').value =  admin_2;
                document.getElementById('admin_area_level_3_filter').value =  admin_3;
                document.getElementById('locality_filter').value =            locality;
                document.getElementById('location_filter_go').href =          location_filter_url;

	        });

	        google.maps.event.addDomListener(input, 'keydown', function(e) {
	            if (e.keyCode == 13) {
	                if (e.preventDefault) {
	                    e.preventDefault();
	                } else {
	                    // Since the google event handler framework does not handle early IE versions, we have to do it by our self. :-( 
	                    e.cancelBubble = true;
	                    e.returnValue = false;
	                }
	            }
	        });
	        
    		//Loop through the json surrounding event objects 
	    	for (var i = 0, length = json.length; i < length; i++) {
		        var data = json[i],
		                   postlatlong = new google.maps.LatLng(data.Post_lat, data.Post_long);
		    		    
                //Adds surrounding markers from the json object and loop for surrounding events 
                var custom_marker = new google.maps.Marker({
            	    position: postlatlong,
            	    map: map,
        	        title: data.Title,
        	        icon: data.Post_icon
                });		    
		
		 	    // Creating a closure to retain the correct data, notice how I pass the current 
            	// data in the loop into the closure (marker, data) 
	    		(function(custom_marker, data) {
		        	// Attaching a click event to the current marker 
		        	google.maps.event.addListener(custom_marker, "click", function(e) {
			        	infoWindow.setContent(data.Title_link);
			    	    infoWindow.open(map, custom_marker);
    				});
	    		})(custom_marker, data);       	
		    } 
        }

   </script>
   
   <div onload="initialize(); return false;"></div>
   <div id="<?php echo $map_canvas; ?>"></div>   
   
   <?php 
   if ( !is_page() ) {
   ?>
   <div class="right">
       <a href="<?php echo $site_url; ?>/world-map/">See World Map</a>
   </div>
   <?php  
   }
} 

function get_google_map() {
    /**
     * Sets lat and long to centre map on from post if single post
     * or on users location if home page or feed. 
     * Grabs data from posts surrounding map center and formats in json.
     * Then calls display_google_map_posts_and_places_autocomplete($json,[etc])
     * 
     * Called from sidebar-right.php
     * 
     * Authors: Katie Patrick & Jesse Browne
     * 			katie.patrick@greenpag.es
     * 			jb@greenpag.es
     */
    
    $post_type = get_post_type();
    
    if ($post_type != "page" && $post_type != false) { 
        
        global $post, $gp, $wpdb;
        $current_post_id =  $post->ID;
        $lat_key =          'gp_google_geo_latitude';
        $long_key =         'gp_google_geo_longitude';
        
        if ( is_single() ) {
            $center_map_lat =     get_post_meta($current_post_id, $lat_key, true);
            $center_map_long =    get_post_meta($current_post_id, $long_key, true);
            $current_post_json =  get_post_location_json_data(true);
        } else {
            # Set user location lat and long here
            $user_lat =           ( !empty($_GET['latitude_filter']) ) ? $_GET['latitude_filter'] : $gp->location['latitude'];
            $user_long =          ( !empty($_GET['longitude_filter']) ) ? $_GET['longitude_filter'] :$gp->location['longitude'];
            $center_map_lat =     ( !empty($user_lat) )  ? $user_lat  : '-33.9060263' ;
            $center_map_long =    ( !empty($user_long) ) ? $user_long : '151.26363019999997' ;
        }
        
        # set up data for surrounding posts query and google map if proper location data found
        if (!empty($center_map_lat) && !empty($center_map_long)) {
            
            $lat_min =     $center_map_lat - 1;
            $lat_max =     $center_map_lat + 1;
            $long_min =    $center_map_long - 1;
            $long_max =    $center_map_long + 1;
            $post_limit =  20;
            $pageposts =   get_surrounding_posts($lat_min, $lat_max, $long_min, $long_max, $post_limit);
                        
            # Set zoom level for map depending on number of surrounding posts
            $num_posts =   count($pageposts);
            
            # If number of posts is less that 20, expand location bounding box to 3 degrees of post
            if ($num_posts < 20) {
            	
            	$lat_min =    $center_map_lat - 3;
            	$lat_max =    $center_map_lat + 3;
           		$long_min =   $center_map_long - 3;
            	$long_max =   $center_map_long + 3;            	
            	$pageposts =  get_surrounding_posts($lat_min, $lat_max, $long_min, $long_max, $post_limit);
			    
            }
            
            $num_posts = count($pageposts);
            $zoom = ($num_posts > 5) ? 11 : 7;
            
            if ($num_posts < 5) {
            	
            	$lat_min =    -90;
            	$lat_max =    90;
           		$long_min =   -180;
            	$long_max =   180;            	
            	$pageposts =  get_surrounding_posts($lat_min, $lat_max, $long_min, $long_max, $post_limit);
			    $zoom =       1;
            }
            
            $num_posts = count($pageposts);
            
            # Construct location data in JSON for google map display
            if ($num_posts > 0) {
                
                $json = '[';
                foreach ($pageposts as $post) {                    
                    $json .= ($current_post_id != $post->ID) ? get_post_location_json_data() : '';	
                }
                $json .= (isset($current_post_json)) ? $current_post_json : '' ;
                $json .= ']';
                
                # Define map canvas div id and show map
                $map_canvas = 'post_google_map_canvas';
    	        display_google_map_posts_and_places_autocomplete($json, $map_canvas, $center_map_lat, $center_map_long, $zoom);
    	        	
            }
	    }
	}
}

function get_surrounding_posts($lat_min, $lat_max, $long_min, $long_max, $post_limit) {
    /*
     * Get posts surrounding map center sql query
     * Called by get_google_map()
     * 
     * Authors: Katie Patrick & Jesse Browne
     * 			katie.patrick@greenpag.es
     * 			jb@greenpag.es
     */
    
    global $wpdb;
    $epochtime = strtotime('now');

	/** SQL TO GET 20 MOST RECENT POSTS AROUND MAP CENTER **/
    $querystr = $wpdb->prepare(
        		    "SELECT DISTINCT
            			" . $wpdb->prefix . "posts.*,
            		 	m0.meta_value AS _thumbnail_id,
                     	m1.meta_value AS gp_enddate,
            			m2.meta_value AS gp_startdate
        			FROM $wpdb->posts
            			LEFT JOIN " . $wpdb->prefix . "postmeta AS m0 ON m0.post_id=" . $wpdb->prefix . "posts.ID AND m0.meta_key='_thumbnail_id'
            			LEFT JOIN " . $wpdb->prefix . "postmeta AS m1 ON m1.post_id=" . $wpdb->prefix . "posts.ID AND m1.meta_key='gp_events_enddate' 
            			LEFT JOIN " . $wpdb->prefix . "postmeta AS m2 ON m2.post_id=" . $wpdb->prefix . "posts.ID AND m2.meta_key='gp_events_startdate'
       				WHERE
            			post_status='publish'
            			AND ( ( post_latitude > " . $lat_min . " ) AND ( post_latitude < " . $lat_max . " ) )
            			AND ( ( post_longitude > " . $long_min . " ) AND ( post_longitude < " . $long_max . " ) )
            			AND m0.meta_value >= 1
            			AND (
                				post_type='gp_news' 
                				OR post_type='gp_advertorial' 
                				OR post_type='gp_projects' 
                				OR ( post_type='gp_events' AND CAST(CAST(m1.meta_value AS UNSIGNED) AS SIGNED) >= %d ) 
            				)
        			ORDER BY post_date DESC
        			LIMIT %d;",
                    $epochtime,
                    $post_limit
                );
           
    $pageposts = $wpdb->get_results($querystr, OBJECT);

    return $pageposts;

}

/** WORLD MAP OF ALL POSTS - LINK ON FOOTER **/

function get_google_world_map() {
    /**
     * Shows all posts from the last 4 weeks on a world map
     * TO DO - centre map on user's location
     * Found on worldpress page www.greenpag.es/world-map using shortcode gp-world-map
     * Called from gp-word-map.php
     * 
     * Authors: Katie Patrick & Jesse Browne
     * 			katie.patrick@greenpag.es
     * 			jb@greenpag.es
     */    
       
    global $wpdb, $post;
   
    $center_map_lat = '0.0';
    $center_map_long = '20.0';
    $zoom = 2;
    $post_limit = 100;
    $epochtime = strtotime('now');

	/** SQL TO GET 20 MOST RECENT POSTS AROUND MAP CENTER **/
    $querystr = $wpdb->prepare(
        		    "SELECT DISTINCT
            			" . $wpdb->prefix . "posts.*,
            			m1.meta_value AS gp_enddate,
            			m2.meta_value AS gp_startdate
        			FROM $wpdb->posts
        			    LEFT JOIN " . $wpdb->prefix . "postmeta AS m1 ON m1.post_id=" . $wpdb->prefix . "posts.ID AND m1.meta_key='gp_events_enddate' 
            			LEFT JOIN " . $wpdb->prefix . "postmeta AS m2 ON m2.post_id=" . $wpdb->prefix . "posts.ID AND m2.meta_key='gp_events_startdate'
       				WHERE
       				    post_status='publish'
            			AND (
            			    ( post_type='gp_events' AND CAST(CAST(m1.meta_value AS UNSIGNED) AS SIGNED) >= %d ) 
                			OR post_type='gp_projects' 
                			OR post_type='gp_advertorial' 
                			OR post_type='gp_news' 
            			)
        			ORDER BY post_date DESC
        			LIMIT %d;",
                    $epochtime,
                    $post_limit
                );
           
    $pageposts = $wpdb->get_results($querystr, OBJECT);
            
    # Construct location data in JSON for google map display
    if ($pageposts) {            
        $json = '[';	                
        foreach ($pageposts as $post) {                    
            $json .= get_post_location_json_data();	
        }
        $json .= ']'; 
        # Define map canvas div id
        $map_canvas = 'world_google_map_canvas';
    	display_google_map_posts_and_places_autocomplete($json, $map_canvas, $center_map_lat, $center_map_long, $zoom);        	
    }
}

/** CUSTOM STYLES FOR GOOGLE MAPS  **/
function custom_google_map_styles() {
    echo  '[ 
                          { 
                              "featureType": "water", 
                              "elementType": "geometry.fill", 
                              "stylers": [ 
                                             { "saturation": 1 }, 
                                             { "lightness": 1 }, 
                                             { "gamma": 1 }, 
                                             { "hue": "#00ffff" } 
                                         ] 
                          },{ 
                              "featureType": "poi.park", 
                              "elementType": "geometry.fill", 
                              "stylers": [ 
                                             { "visibility": "on" }, 
                                             { "color": "#47c92c" }, 
                                             { "invert_lightness": true } 
                                         ] 
                          },{   "featureType": "road.highway", 
                              "elementType": "geometry.fill", 
                              "stylers": [ 
                                             { "color": "#808080" } 
                                         ] 
                          },{ 
                              "featureType": "road.highway", 
                              "elementType": "geometry.stroke", 
                              "stylers": [ 
                                             { "color": "#808080" } 
                                         ] 
                          },{   "elementType": "labels.text.stroke", 
                              "stylers": [ 
                                             { "color": "#808080" } 
                                         ] 
                          },{ 
                              "elementType": "labels.text.fill", 
                              "stylers": [ 
                                             { "color": "#ffffff" } 
                                         ] 
                          },{ 
                              "featureType": "road.arterial", 
                              "elementType": "geometry.fill", 
                              "stylers": [ 
                                             { "color": "#dcdcdc" } 
                                         ] 
                          },{ 
                              "elementType": "geometry.stroke", 
                              "stylers": [ 
                                             { "weight": 0.1 } 
                                         ] 
                          },{ 
                              "featureType": "road.arterial", 
                              "elementType": "labels.text.stroke", 
                              "stylers": [ 
                                             { "color": "#ffffff" } 
                                         ] 
                          },{ 
                              "elementType": "labels.text.fill", 
                              "stylers": [ 
                                             { "color": "#646464" } 
                                         ] 
                          },{ 
                              "featureType": "road.local", 
                              "elementType": "labels.text.stroke", 
                              "stylers": [ 
                                             { "color": "#ffffff" } 
                                         ] 
                          },{ 
                              "featureType": "poi.attraction", 
                              "elementType": "labels.text.stroke", 
                              "stylers": [ 
                                             { "color": "#ffffff" } 
                                         ] 
                          },{ 
                              "featureType": "poi", 
                              "elementType": "labels.text.stroke", 
                              "stylers": [ 
                                             { "color": "#ffffff" } 
                                         ] 
                          },{ 
                              "featureType": "landscape", 
                              "elementType": "geometry.fill", 
                              "stylers": [ 
                                             { "color": "#e8e7df" } 
                                         ] 
                          },{ 
                              "featureType": "poi.park", 
                              "elementType": "geometry.fill", 
                              "stylers": [ 
                                             { "color": "#aed199" } 
                                         ] 
                          },{ 
                              "featureType": "administrative", 
                              "elementType": "labels.text.stroke", 
                              "stylers": [ 
                                             { "color": "#ffffff" } 
                                         ] 
                          },{ 
                              "elementType": "labels.text.stroke", 
                              "stylers": [ 
                                             { "color": "#ffffff" } 
                                         ] 
                          },{ 
                              "featureType": "road.highway", 
                              "elementType": "geometry.fill", 
                              "stylers": [ 
                                             { "color": "#bebebe" } 
                                         ] 
                          },{ 
                              "featureType": "water", 
                              "elementType": "geometry.fill", 
                              "stylers": [ 
                                             { "color": "#badce1" } 
                                         ] 
                          },{ 
                              "featureType": "road.local", 
                              "elementType": "geometry.fill", 
                              "stylers": [ 
                                             { "color": "#fcfcfc" } 
                                         ] 
                          },{ 
                              "featureType": "landscape", 
                              "stylers": [ 
                                             { "color": "#f0f0f0" } 
                                         ] 
                          },{ 
                              "featureType": "poi.school", 
                              "elementType": "geometry.fill", 
                              "stylers": [ 
                                             { "color": "#e6e6e6" } 
                                         ] 
                          },{ 
                              "featureType": "poi.sports_complex", 
                              "elementType": "geometry.fill", 
                              "stylers": [ 
                                             { "color": "#e6e6e6" } 
                                         ] 
                          },{ 
                              "featureType": "poi.medical", 
                              "elementType": "geometry.fill", 
                              "stylers": [ 
                                             { "color": "#e6e6e6" } 
                                         ] 
                          },{ 
                              "featureType": "transit", 
                              "elementType": "geometry.fill", 
                              "stylers": [ 
                                             { "color": "#dcdcdc" } 
                                         ] 
                          },{ 
                              "featureType": "landscape", 
                              "elementType": "geometry.fill", 
                              "stylers": [ 
                                             { "color": "#f8f8f5" } 
                                         ] 
                          },{ } 
                      ]';
}

/** SHOWS DIFFERENT COUNTRY FACEBOOK PAGES BASED ON USER'S LOCATION BY IP**/
function show_facebook_by_location() {
    global $post, $wpdb, $gp;
    $ns_loc = $gp->location['country_iso2'] . '\\Edition';
    
    $edition_meta = $ns_loc::getMeta();
    
    if ( isset($edition_meta['facebook_id']) && !empty($edition_meta['facebook_id']) ) {
    
    	// Echo facebook iframe with country based facebook page ID inserted into iframe
    	/**
        echo '<iframe src="http://www.facebook.com/plugins/likebox.php?id=' . $edition_meta['facebook_id'] .
    	      '&amp;width=270&amp;connections=4&amp;stream=false&amp;header=false&amp;height=212" 
    	      frameborder="0" scrolling="no" id="facebook-frame" allowTransparency="true"></iframe>';
        **/
        
        // Link to appropriate facebook page - TODO replace this link with facebox above in dialog box
        return $edition_meta['facebook_id'];
    }
}	

function theme_indexdetails($format='full') {
    /**
     * By line including author name, posted in and posted ago
     **/
    
    global $post;
	$post_author = get_userdata($post->post_author);
	$post_author_url = get_author_posts_url($post->post_author);
    
	$site_posttypes = Site::getPostTypes();
    foreach ( $site_posttypes as $site_posttype ) {
	    if ( $site_posttype['id'] == get_post_type() ) {
	        $post_type_title = $site_posttype['title'];
	        $post_type_url = $site_posttype['slug'];
	    }   
	}
	
	$in = ( is_home() ) ? 'in <a href="/' . $post_type_url . '">' . $post_type_title . '</a>' : '';
	
	if ($format == 'full') {
		echo '<div class="post-details">
		          By <a href="' . $post_author_url . '">' . $post_author->display_name . '</a> ' . $in .' ' . time_ago(get_the_time('U'), 0) . ' ago 
		      </div>';
	}
	
	if ($format == 'author') {
		echo '<div class="post-details">
		          By <a href="' . $post_author_url . '">' . $post_author->display_name . '</a> ' . $in .' ' . time_ago(get_the_time('U'), 0) . ' ago 
		      </div>';
	}
}

function theme_indexsocialbar() {
	theme_singlesocialbar();
}

function theme_indexpagination() {
	global $wp_query;
	
	if (  $wp_query->max_num_pages > 1 ) { ?>
		<nav id="post-nav">
			<ul>
				<li class="post-previous"><?php next_posts_link('<div class="arrow-previous"></div>More Posts', $wp_query->max_num_pages); ?></li>
				<li class="post-next"><?php previous_posts_link('Recent Posts<div class="arrow-next"></div>', $wp_query->max_num_pages); ?></li>
			</ul>
		</nav>
	<?php
	}
}

function theme_like_comments() {
    /**
     * Shows upvote icon and count
     * Dependant on some js functions in gp-theme/js/gp.js
     **/
    
    global $post, $current_user, $current_site;
    $site_url = get_site_url();
    $link = get_permalink($post->ID);
	
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
	
	if (is_user_logged_in()) {
		echo '<div id="post-' . $post->ID . '" class="favourite-profile">
                  <a href="#/" title="Upvote this post and save to favourites">
                      <span class="af-icon-chevron-up' . $likedclass . '"></span>
                      <span class="af-icon-chevron-up-number"' . $showlikecount . '>' . $likecount . '</span>
                      <span class="af-icon-chevron-up-number-plus-one" style="display:none;">+1</span>
                      <span class="af-icon-chevron-up-number-minus-one" style="display:none;">-1</span> 
                  </a>
              </div>';
	} else {
		echo '<a href="' . $site_url  . '/welcome/" 
		         id="login-to-upvote" title ="Log in to upvote" 
		         onmouseover="show_login_to_upvote(this); return false;"
			     onmouseout="hide_login_to_upvote(this); return false;" >
		          <div id="post-' . $post->ID . '" class="favourite-profile">
		              <span id="upvote-icon"   class="af-icon-chevron-up"></span>
		              <span id="upvote-number" class="af-icon-chevron-up-number"' . $showlikecount . '>' . $likecount . '</span>
		              <span id="upvote-login"  class="upvote-login" style="display:none;">Log in to upvote...</span>
		          </div>
		      </a>';
	}

	if ( comments_open($post->ID) ) {
		echo '<div class="comment-profile">
                  <a href="'. $link .'#comments">
                      <span class="comment-mini"></span>
                      <span class="comment-mini-number dsq-postid">
                          <fb:comments-count href="' . $link . '"></fb:comments-count>
                      </span>
                  </a>
              </div>';
	}	
}

function theme_index_feed_item() {
    /**
     * Shows title of post as link for dislay in index pages,
     * If featured image present then thumbnail displayed
     * Otherwise thumbnail of user profile picture shown instead 
     */
	
    global $post, $current_user, $current_site;
    
	$post_author =            get_userdata($post->post_author);
	$post_author_url =        get_author_posts_url($post_author);
    $location_filter_uri =    get_location_filter_uri();
	$link =                   get_permalink($post->ID);
	$link_location_uri =      $link . $location_filter_uri;
	$likedclass =             '';
	$site_url = get_site_url();
	
	/** DISPLAY FEATURED IMAGE IF SET **/           
    if ( has_post_thumbnail() ) {
		$imageArray = wp_get_attachment_image_src( get_post_thumbnail_id($post->ID), 'homepage-thumbnail' );
		$imageURL = $imageArray[0];
		echo '<a href="' . $link_location_uri . '" class="profile_minithumb">
		          <img src="' . $imageURL  . '" alt="' . get_the_title( get_post_thumbnail_id($post->ID) ) . '"/>
		      </a>';
    } else {	/** DISPLAY RANDOM IMAGE IF NO IMAGE IN HTML**/

        $random_images = array();
	    $random_images = get_random_images();

		$rand_keys = array_rand($random_images, 2);
		$image_url_img = $random_images[$rand_keys[0]];
	
		echo '<span class="profile_minithumb">
		          <a href="' . $link_location_uri . '"> 
    		          <img src="'. $image_url_img  . '"  alt="green"/>
    		      </a>
    		  </span>';
	}
	
	echo '<div class="profile-postbox">';

	?>
    <h1 class="profile-title">
        <a href="<?php echo $link_location_uri; ?>"  title="<?php esc_attr(the_title()); ?>" rel="bookmark"><?php the_title(); ?></a>
    </h1>
    <?php
	
	
	/** DISPLAY POST AUTHOR, CATEGORY AND TIME POSTED DETAILS **/
	$location = get_post_meta($post->ID, 'gp_google_geo_location');
	$where =   (!empty($location)) ? $location : '';
	
	theme_indexdetails('author');

    echo '<div class="post-details"> '. $location[0] .' </div>';
			
    theme_like_comments();
	
	echo '</div>
		  <div class="topic-container">
              <div class="topic-content">
                  <div class="topic-bookmark">
                      <a href="#/" class="topic-bookmark">
                          <span class="topic-bookmark-false"></span>
                          <span class="topic-bookmark-box">test topic</span>
                      </a>
                      <div class="topic-bookmark-options">
                          <a href="#/">Subscribe
                              <span class="topic-subscribe-count">0</span>
                              <span class="topic-subscribe-count-plus-one">+1</span>
                              <span class="topic-subscribe-count-minus-one">-1</span>
                          </a>
						  <a href="#/">RSS</a>
                      </div>
                  </div>
                  <div class="clear"></div>
              </div>
		  </div>';
	echo '<div class="clear"></div>';
}

/* SHOW MEMBERS POSTS */
function theme_profile_posts($profile_pid, $post_page, $post_tab, $post_type) {
	// note: Favourites are viewable by everyone!
	
	$profile_author = get_user_by('slug', $profile_pid);
	
	global $wpdb, $post, $current_user, $gp;
	$geo_currentlocation = $gp->location;
	$ns_loc = $gp->location['country_iso2'] . '\\Edition';
	$edition_posttypes = $ns_loc::getPostTypes();
	
	if ( strtolower($post_type) == "directory" ) {
		theme_profile_directory($profile_pid);
		return;	
	}
	
	$post_type_filter = "";
	$post_type_key = getPostTypeID_by_Slug($post_type);
	if ( $post_type_key ) {
		$post_type_filter = "" . $wpdb->prefix . "posts.post_type = '{$post_type_key}'";
	} else {
		foreach ($edition_posttypes as $value) {
		    if ( $value['enabled'] === true ) {
			    $post_type_filter .= $wpdb->prefix . "posts.post_type = '{$value['id']}' or ";
		    }
		}
		$post_type_filter = substr($post_type_filter, 0, -4);
	}
		
		$total = "SELECT DISTINCT COUNT(*) as count 
				FROM $wpdb->posts 
				WHERE 
		            post_status = 'publish' and 
					(" . $post_type_filter . ")	and " . 
					$wpdb->prefix . "posts.post_author = '" . $profile_author->ID . "'";			
					
		$totalposts = $wpdb->get_results($total, OBJECT);
		$ppp = 10;
		$wp_query->found_posts = $totalposts[0]->count;
		$wp_query->max_num_pages = ceil($wp_query->found_posts / $ppp);	
		$on_page = $post_page;

		if($on_page == 0){ $on_page = 1; }		
		$offset = ($on_page-1) * $ppp;
		
		$querystr = "SELECT DISTINCT " . $wpdb->prefix . "posts.* 
					FROM $wpdb->posts
				    WHERE 
		            	post_status = 'publish' and 
						(" . $post_type_filter . ")	and " . 
					    $wpdb->prefix . "posts.post_author = '" . $profile_author->ID . "' 
					ORDER BY " . $wpdb->prefix . "posts.post_date DESC 
					LIMIT " . $ppp . " 
					OFFSET " . $offset .";";				

		$pageposts = $wpdb->get_results($querystr, OBJECT);
		
		if ( $post_type_key ) {
			foreach ($edition_posttypes as $newposttype) {
			    if ( $newposttype['enabled'] === true ) {
				    if ($newposttype['id'] == $post_type_key) {$post_type_name = " " . $newposttype['name'];}
			    }
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
				theme_index_feed_item();
				
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

/* SHOW MEMBERS FOLLOWING MEMBERSHIP */
function theme_profile_following($profile_pid) {
	// note: Favourites are viewable by everyone!
	
	echo "
	<div class=\"total-posts\">
		<span>0</span> Following
	</div>
	";
}

/* SHOW MEMBERS TOPIC MEMBERSHIP */
function theme_profile_topics($profile_pid) {
	// note: Favourites are viewable by everyone!
	
	echo "
	<div class=\"total-posts\">
		<span>0</span> Topics
	</div>
	";	
}

/* BUTTONS TO LINK FRONT END TO CREATE NEW POST ADMIN PAGES */

function theme_profilecreate_post(){
	if ( !is_user_logged_in() ) { 
	    $site_url = get_site_url(); ?>
	    <div class="wide-button-wrapper">
            <div class="icon-container-row">
    		    <div id="post-product-button-bar">
	    		    <a href="<?php echo $site_url; ?>/welcome/">
		    	        <span id="product-button">Join greenpag.es!</span>
		    	    </a>
		    	    <a  href="<?php echo $site_url; ?>/welcome/">
    		    		<div id="inner-padding" >Use greenpag.es to get involved with or promote environmental issues in your local area</div>
    				</a>
    		    </div>    		    
    		</div>
    		<div class="clear"></div>
        </div> <?php 
	}
}	

function theme_mobile_log_in() {
    /**
     * Log in button for mobile users
     * 
     * Author: Jesse Browne
     * 	       jb@greenpag.es
     */
    
    $site_url = get_site_url(); ?>
    
    <span class="mobile-only">   
        <a href="<?php echo $site_url . '/wp-login.php'; ?>" class="new-post-action">Log In</a>
    </span>
    <span class="mobile-only">
        <a href="<?php echo $site_url; ?>/welcome/" class="new-post-action">Join</a>
    </span> <?php 
}

function theme_create_post_and_mobile_log_in() {
    /**
     * Route to appropriate create post button
     * depending on post type / page being viewed
     * 
     * Author: Jesse Browne
     * 	       jb@greenpag.es
     */
    
    global $current_user;
    
    switch (get_post_type()) {
		case 'gp_news':
            if ( !is_user_logged_in() ) {
                $link = '/get-involved/become-a-content-partner/';
            } else if ( is_user_logged_in() && get_user_role( array('subscriber') ) ) {
                $link = '/get-involved/become-a-content-partner/';
            } else if ( is_user_logged_in() && get_user_role( array('contributor') ) ) {
                $link = '/forms/create-news-post/';                         
            } else if ( is_user_logged_in() && get_user_role( array('administrator') ) ) {
                $link = '/forms/create-news-post/';
            }
            $message = 'Post a news story';
			break;
		case 'gp_projects':
			$link =    '/forms/create-project-post/';
			$message = 'Post a project';
			break;
		case 'gp_advertorial':
			$link =    ( is_user_logged_in()  && $current_user->reg_advertiser == 1 ) ? '/forms/create-product-post-subscriber/' : '/advertisers/';
		    $message = 'Post a product ad';
			break;
		case 'gp_events':
			$link =    '/forms/create-event-post/';
			$message = 'Post an event';
			break;
	} 
	
	if ( is_home() ) {
	    $link    = '/welcome/';
	    $message = 'Create a post';
	}
	
	?>	
	
	<div class="new-action">
	    <?php theme_mobile_log_in(); ?>
	    <span class="right">           
	        <a href="<?php echo get_site_url() . $link; ?>" class="new-post-action"><?php echo $message; ?></a>
	    </span>
	</div>    
	<div class="clear"></div> <?php
}

function gp_select_createpost() {
    /**
     * Display a drop down menu in profile page with links to create post forms,
     * news posts are only available to Content Partners (contributors) and
     * links to product and competition forms are determined by regular advertiser status
     */
    
    global $current_user;
    
    # Set link to create news post for Content Partners
    $post_my_news_link = ( get_user_role( array('contributor') ) ) ? "<li><a href=\"". get_site_url() ."/forms/create-news-post/\">News</a></li>" : "";
    
    # Set links to forms for monthly advertisers and non monthly advertisers
    $post_my_product_form = ( is_user_logged_in() && $current_user->reg_advertiser == 1 ) ? '/forms/create-product-post-subscriber/' : '/advertisers/';
    $post_my_product_link = ( is_user_logged_in() && $current_user->reg_advertiser == 1 ) ? "<a href=\"". get_site_url() . $post_my_product_form ."\">Product Post</a>" : "<a href=\"". $post_my_product_form ."\">Product Post ($1.90 / click)</a>"; 
    
	echo "
	<div class=\"profile-action-container no-js\">
		<a href=\"". get_site_url() ."/wp-admin\" class=\"profile-action\">Create post<span class=\"bullet5\"></span></a>
		<ul class=\"profile-action-items\">
		    ". $post_my_news_link ."
            <li><a href=\"". get_site_url() ."/forms/create-event-post/\">Event</a></li>
            <li>". $post_my_product_link ."</li>
            <li><a href=\"". get_site_url() ."/forms/create-project-post/\">Project</a></li>
		</ul>
	</div>
	";
}

function theme_update_delete_post() {
    /**
     * Allow users to edit and delete their own posts.
     * Work is done by passing the post id
     * to an appropriate gravity form embedded in a page to edit
     * or by passing the post id to /delete-post-handler/.
     * 
     * Author: Jesse Browne
     *         jb@greenpag.es
     */
    
    global $post, $current_user;
    
    # Check if this is users own post or admin and show update button if appropriate
	if ( is_user_logged_in()  && ( $current_user->ID == $post->post_author ) ) {} else {return;}

	# Set url components for post types
	switch (get_post_type()) {
		case 'gp_news':
			$update_delete_post_page = '/forms/update-news';
			break;
		case 'gp_projects':
			$update_delete_post_page = '/forms/update-project';
			break;
		case 'gp_advertorial':
			$update_delete_post_page = '/forms/update-product-post';
			break;
		case 'gp_events';
		    $update_delete_post_page = '/forms/update-event/';
			break;
	}
	
	# Construct link to appropriate form passing post id via gform_post_id
    $gform_prefix =      '?gform_post_id=';
    $post_id =           $post->ID;
    $update_post_link =  $update_delete_post_page . $gform_prefix . $post_id;
    $site_url = get_site_url();
    
    ?>
    <script type="text/javascript" async>
		<!--		
		function delete_post_dialog() {
			site_url =     '<?php echo $site_url; ?>';
			action_url =   site_url + '/delete-post-handler';
			post_id =      '<?php echo $post_id; ?>';
			confirm_str =  '<p>Are you sure you want to delete this post?</p>';
			confirm_str += '<p>This action cannot be undone.</p>';
			confirm_str += '<form action="' + action_url + '" method="post">';
			confirm_str += '    <span>';
			confirm_str += '        <input type="hidden" name="delete_this_post" id="delete_this_post" value="' + post_id + '">';
			confirm_str += '    </span>';
			confirm_str += '    <span>';
			confirm_str += '        <input onclick="cancel_delete_post();" type="button" value="Cancel" onclick="cancel_delete_post();">';
			confirm_str += '    </span>';
			confirm_str += '    <span>';
			confirm_str += '        <input onclick="delete_the_post();" type="submit" value="Delete" onclick="delete_the_post();">';
			confirm_str += '    </span>';
			confirm_str += '</form>';
				
			$(function() {
				$( '#delete-post-dialog' ).dialog({ modal: true }).html(confirm_str).delay(20000).hide(function() { 
					$( '#delete-post-dialog' ).dialog('close'); 
				}); 
			});
		}

		function cancel_delete_post() {
			$(function() {
				$( '#delete-post-dialog' ).dialog('close');
			});	
		}

		//-->
	</script>
	<div id="delete-post-dialog" title="Delete this post"></div>
	<div class="new-action">
	    <span class="right">
	        <a href="javascript:void(0);" onclick="delete_post_dialog(); return false;" class="new-post-action">Delete this post</a>
	    </span>
	</div>
	<div class="clear"></div>  
    <div class="new-action">
	    <span class="right">
	        <a href="<?php echo $update_post_link; ?>" class="new-post-action">Edit this post</a>
	    </span>
	</div>
	<?php
}

function theme_single_tags() {
    ?>
    <div class="post-tags">
        <?php 
        #$location_filter_uri =    get_location_filter_uri();
        global $post;
        $post_type = ( isset($post) ? get_post_type($post->ID) : '' );
        $site_url = get_site_url();
        $posttags = get_the_tags();
        if ($posttags) {
            echo 'Tags: ';
            foreach($posttags as $tag) {
                $tag_link = $site_url .'/tag/'. $tag->slug .'/?post_type='. $post_type;
                echo '<a href="'. $tag_link .'" rel="tag">'. $tag->name .'</a> '; 
            }
        }
        ?>
    </div>
    <?php
}

function add_suggest_script() {
    wp_enqueue_script( 'suggest', get_bloginfo('wpurl').'/wp-includes/js/jquery/suggest.js', array(), '', true );
}
add_action( 'wp_enqueue_scripts', 'add_suggest_script' );



/** POPULARITY SCORE RELATED CALCULATIONS **/

function distance_to_post($post, $location_latitude, $location_longitude) {
    /**
	 *  Calulates distance between user location, or location filter 
	 *  and post location using Pythagoras Theorem 
     *  
     *  Author: Katie Patrick
     *  		katie.patrick@greenpag.es
     */
    
    global $post, $gp;  
	$post_latitude =  (float) $post->post_latitude;
	$post_longitude = (float) $post->post_longitude;
	$user_latitude =  (float) $location_latitude;
    $user_longitude = (float) $location_longitude;
    
    #var_dump($post_latitude);  echo '<br />';
    #var_dump($post_longitude); echo '<br />';
    #var_dump($user_latitude);  echo '<br />';
    #var_dump($user_longitude); echo '<br />';
    
    if ( ($post_latitude == $user_latitude) && ($post_longitude == $post_longitude) ) {
        $c = (float) 0.1;
        #echo 'if $c :'; var_dump($c);  echo '<br />';
    } else {
	    $a = $post_latitude - $user_latitude;
	    $b = $post_longitude - $user_longitude;		    
	    $c = sqrt(pow($a,2) + pow($b,2));
	    #echo 'else $a :'; var_dump($a);  echo '<br />';
	    #echo 'else $b :'; var_dump($b);  echo '<br />';
	    #echo 'else $c :'; var_dump($c);  echo '<br />';
	}
	
	return $c;
}

function page_rank($c, $post) {
    /**
	 *  Adjusts popularity score depending on distance
	 *  between user location and post location
     *  
     *  Author: Katie Patrick
     *  		katie.patrick@greenpag.es
     */

    global $post; 
	$popularity_score = (int) $post->popularity_score;
	
	if ($c > 2) {
    	$location_as_unix = pow(($c*2000), 1.05);
    	$location_as_unix = (int) $location_as_unix;
    	$popularity_score_thisuser = $popularity_score - $location_as_unix;
	} elseif ($c < 1) {
		$popularity_score_thisuser = $popularity_score + pow(((1/$c)*3600), 1.05);
		$popularity_score_thisuser = (int) $popularity_score_thisuser;	
	} 

	#var_dump($popularity_score_thisuser); echo '<br />';echo '____________________<br />';
	
	return $popularity_score_thisuser;
}

function get_location_filter() {
    /**
     * Returns pretty location string to display in 
     * header tag line
     * 
     * Author: Jesse Browne
     *         jb@greenpag.es
     */
    
    global $gp;
    $user_city =       $gp->location['city'];
    $location_filter = ( !empty($_GET['location_filter']) ) ? $_GET['location_filter'] : $user_city;
    
    return $location_filter;
}

function get_location_filter_uri_prefix() {
    /**
     * Set location filter data and construct location filter uri's 
     * for nav bar and links to posts from index pages / feeds
     * 
     * Author: Jesse Browne
     *         jb@greenpag.es
     **/
    
    global $gp;

    $location_country_slug =    ( !empty($_GET['location_slug_filter']) )  ? $_GET['location_slug_filter'] : '';
    $location_state_slug =      ( !empty($_GET['location_state_filter']) ) ? '/' .$_GET['location_state_filter'] : '';
    $locality_slug =            ( !empty($_GET['locality_filter']) )       ? '/' .$_GET['locality_filter'] : '';

    if ( !empty($location_country_slug) && !empty($location_state_slug) ) {
        $location_filter_uri_prefix =  $location_country_slug . $location_state_slug . $locality_slug;
    } else {
        $location_filter_uri_prefix = '';
    }
    
    return $location_filter_uri_prefix;
}

function get_location_filter_uri() {
    /**
     * Set location filter data and construct location filter uri's 
     * for nav bar and links to posts from index pages / feeds
     * 
     * Author: Jesse Browne
     *         jb@greenpag.es
     */
    
    global $gp;    
    $location_filter =          get_location_filter();
    $location_latitude =        ( !empty($_GET['latitude_filter']) ) ? $_GET['latitude_filter'] : '';;
    $location_longitude =       ( !empty($_GET['longitude_filter']) ) ? $_GET['longitude_filter'] : '';;
    $location_country_slug =    ( !empty($_GET['location_slug_filter']) ) ? $_GET['location_slug_filter'] : '';
    $location_state_slug =      ( !empty($_GET['location_state_filter']) ) ? $_GET['location_state_filter'] : '';
    $locality_slug =            ( !empty($_GET['locality_filter']) ) ? $_GET['locality_filter'] : '';
    $append_location =          ( !empty($location_filter) ) ? '?location_filter=' . $location_filter : '';
    $append_latitude =          ( !empty($location_latitude) ) ? '&latitude_filter=' . $location_latitude : '';
    $append_longitude =         ( !empty($location_longitude) ) ? '&longitude_filter=' . $location_longitude : '';
    $append_location_slug =     ( !empty($location_country_slug) ) ? '&location_slug_filter=' . $location_country_slug : '';
    $append_state_slug =        ( !empty($location_state_slug) ) ? '&location_state_filter=' . $location_state_slug : '';
    $append_locality_slug =     ( !empty($locality_slug) ) ? '&locality_filter=' . $locality_slug : '';
    
    if ( !empty($append_location) && !empty($append_latitude) && !empty($append_longitude) ) {
        $location_filter_uri =  $append_location . $append_latitude . $append_longitude . 
                                $append_location_slug . $append_state_slug . $append_locality_slug;
    } else {
        $location_filter_uri =  '';
    }
    
    return $location_filter_uri;
}

function theme_index_event_item() {
    /**
     * Display event title, date and location in events feed
     **/
    
    global $post;
    setup_postdata($post);
			    
	$displayday =             date('j', $post->gp_events_startdate);
	$displaymonth =           date('M', $post->gp_events_startdate);
	$displayyear =            date('y', $post->gp_events_startdate);
		
	$location_filter_uri =    get_location_filter_uri();
	$link =                   get_permalink($post->ID);
	$link_location_uri =      $link . $location_filter_uri;
			
	echo '<div class="event-archive-item">';

	if (date('Y', $post->gp_events_startdate) == date('Y')) {
		echo '<a href="' . $link_location_uri . '" class="post-events-calendar"><span class="post-month">' . $displaymonth . '</span><span class="post-day">' . $displayday . '</span></a>';
    } else {
	    echo '<a href="' . $link_location_uri . '" class="post-events-calendar"><span class="post-day">' . $displayyear . '\'</span></a>';
	}

	echo '<h1><a href="' . $link_location_uri . '" title="' . esc_attr(get_the_title($post->ID)) . '" rel="bookmark">' . get_the_title($post->ID) . '</a></h1>';
    echo '<div>';
	theme_indexdetails('author');
	echo '    <div class="post-loc">
	               '. $post->gp_google_geo_locality .' | '. $post->gp_google_geo_administrative_area_level_1 . '
		      </div>
		      <div class="clear"></div>
	      </div>
	  </div>
	  <div class="clear"></div>';
}

//** CUSTOM IMAGE LOGIN SCREEN **//

add_filter('login_headertitle', create_function(false, 'return "'. get_site_url() .'";'));

function my_login_head() {
	echo "
	<style>
	body.login #login h1 a {
		background: url('".get_bloginfo('template_url')."/template/images/greenpages-logo.png') no-repeat scroll center top transparent;
		height: 80px;
		width: 360px;
	}
	</style>
	";
}
add_action("login_head", "my_login_head");

function get_post_type_map() {
    /**
     * Useful for creating pretty post type names
     */
    
    $post_type_map = array( "gp_news"        => "news", 
    						"gp_events"      => "events", 
                            "gp_advertorial" => "products", 
                            "gp_projects"    => "projects" );
    
    return $post_type_map;
}

?>
