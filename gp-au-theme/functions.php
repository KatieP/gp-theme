<?php
/**
 * Green Pages Theme functions and definitions
 * 
 * Green Pages theme or themes are dependant on the Green Pages plugin to run. A theme 
 * contains all necessary functions and definitions that are unique to a single 
 * Green Pages site e.g., greenpages.com.au. In other words, there is one theme per
 * site. Any functions and definitions that are not unique to the theme and are reusable 
 * belong in the Green Pages plugin, not here.
 * 
 * @package WordPress
 * @subpackage gp-au-theme
 * @since Green Pages Theme 1.0
 * 
 * @global array $wp_roles
 * @var array $sitemaptypes
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

# Set Minimum year for Gravity form year selector drop down menus
add_filter("gform_date_min_year", "set_min_year");
function set_min_year($min_year){
    $current_year = date("Y");
    return $current_year;
}

add_filter( 'admin_footer_text', 'gp_add_admin_footer' );
function gp_add_admin_footer() {
	echo 'Welcome to the Green Pages backend editor! Go back to <a href="'. get_site_url() .'">front end</a>';
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

#add_filter( 'author_template', 'edit_author_template' );
#function edit_author_template( $author_template )
#{
    #if ( get_query_var( 'author_edit' ) ) {
        #locate_template( array( 'edit-author.php', $author_template ), true );
    #}
    #return $author_template;
#}


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
    global $wp_rewrite;
   	$wp_rewrite->author_base = 'profile'; # see this for changing slug by role - http://wordpress.stackexchange.com/questions/17106/change-author-base-slug-for-different-roles
    #$wp_rewrite->flush_rules();
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

/* ADD CUSTOM REWRITE RULES */
# see: http://wordpress.stackexchange.com/questions/4127/custom-taxonomy-and-pages-rewrite-slug-conflict-gives-404

function gp_rewrite_rules( $wp_rewrite ) {
    $newrules = array();

    $site_posttypes = Site::getPostTypes();
    foreach ( $site_posttypes as $site_posttype ) {
        $newrules[$site_posttype['slug'] . '/([a-z0-9]{1,2})/([a-z\-]+)/([a-z\-]+)/page/([0-9]{1,})/?$'] = 'index.php?post_type=' . $site_posttype['id'] . '&country=$matches[1]&state=$matches[2]&city=$matches[3]&page=$matches[4]';
        $newrules[$site_posttype['slug'] . '/([a-z0-9]{1,2})/([a-z\-]+)/page/([0-9]{1,})/?$'] = 'index.php?post_type=' . $site_posttype['id'] . '&country=$matches[1]&state=$matches[2]&page=$matches[3]';
        $newrules[$site_posttype['slug'] . '/([a-z0-9]{1,2})/([a-z\-]+)/([a-z\-]+)/?$'] = 'index.php?post_type=' . $site_posttype['id'] . '&country=$matches[1]&state=$matches[2]&city=$matches[3]';
        $newrules[$site_posttype['slug'] . '/([a-z0-9]{1,2})/([a-z\-]+)/?$'] = '/index.php?post_type=' . $site_posttype['id'] . '&country=$matches[1]&state=$matches[2]';
        $newrules[$site_posttype['slug'] . '/([a-z0-9]{1,2})/page/([0-9]{1,})/?$'] = '/index.php?post_type=' . $site_posttype['id'] . '&country=$matches[1]&page=$matches[2]';
        $newrules[$site_posttype['slug'] . '/([a-z0-9]{1,2})/?$'] = '/index.php?post_type=' . $site_posttype['id'] . '&country=$matches[1]';
        //$newrules[$site_posttype['slug'] . '/?$'] = '/index.php?post_type=' . $site_posttype['id'];
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

/* GET PROFILE USER */
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
	
/*
	
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

*/	
	
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

/*		
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
*/
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
    
    if ( get_the_author_meta( 'weekly_email', $user->ID ) == true ) {
        $check_weekly_email = ' checked="checked"';
        $check_system_email = '';
    } else {
        $check_weekly_email = '';
    }
    
    if ( get_the_author_meta( 'system_email', $user->ID ) == true ) {
        $check_weekly_email = '';
        $check_system_email = ' checked="checked"';
    } else {
        $check_system_email = '';
    }
    ?>
	      <h3>Notification Settings</h3>
		  <table class="form-table">
		      <tr>
		          <th>weekly_email</th>
		          <td><input type="radio" name="notification_setting" id="weekly_email" value="weekly_email" <?php echo $check_weekly_email; ?> /></td>
		      </tr>
		      <tr>
		          <th>system_email</th>
		          <td><input type="radio" name="notification_setting" id="" value="system_email" <?php echo $check_system_email; ?> /></td>
		      </tr>
		  </table>
    <?php
	
    /* HIDE THE FOLLOWING CODE BLOCK WITH MISC META DATA FROM NON ADMINS, CODE STILL NEEDS TO RUN THOUGH 
     * OTHERWISE EVERYTIME A NON ADMIN UPDATES THEIR PROFILE PAGE THE META DATA IS LOST 
     */
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

/* SET NEWS POST TYPE FOR DISPLAY ON THE HOME PAGE */
add_filter( 'pre_get_posts', 'my_get_posts' );
function my_get_posts( $query ) {
	if ( is_home() ) {
		$query->set( 'post_type', array( 'gp_news' ) );
	}
	return $query;
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
			
			$displayday = date('j', $post->gp_events_startdate);
			$displaymonth = date('M', $post->gp_events_startdate);
			$str_month = date('m', $post->gp_events_startdate);
			$displayyear = date('y', $post->gp_events_startdate);
			
			$displayendday = date('j', $post->gp_events_enddate);
			$displayendmonth = date('M', $post->gp_events_enddate);
			$str_endmonth = date('m', $post->gp_events_enddate);
			
			$event_link_url = get_permalink($post->ID) . $location_filter_uri;
			$post_id = $post->ID;
			
			$displaytitle = '<a href=\"'. $event_link_url . '\" title=\"'. $event_title .'\">'. $event_title .'</a>';
			
			$event_date_string = 'new Date('. $post->gp_events_startdate .'000)';
			
			$event_str .= '{ Title: "'. $displaytitle .'", Date: '. $event_date_string .' },';
			
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

/** ACTIONS THAT OCCUR WHEN POSTS ARE PUBLISHED **/

function email_after_post_approved($post_ID) {
    
  $type = get_post_type($post_ID);
  $posttypeslug = getPostTypeSlug($type);

  $bcc = "katiepatrickgp@gmail.com, jesse.browne@thegreenpages.com.au";

  $post = get_post($post_ID);
  $user = get_userdata($post->post_author);
  $post_url = site_url() . '/' . $posttypeslug . '/' . $post->post_name;

  $headers  = 'Content-type: text/html' . "\r\n";
  $headers .= 'Bcc: ' . $bcc . "\r\n";

  $body  = '<table width="600px" style="font-size: 15px; font-family: helvetica, arial, tahoma; margin: 5px; background-color: rgb(255,255,255);">';
  $body .= '<tr><td align="center">';
  $body .= '<table width="640">';
  $body .= '<tr style="padding: 0 20px 5px 5px;">';
  $body .= '<td style="font-size: 18px; text-transform:non; color:rgb(100,100,100);padding:0 0 0 5px;">';
  $body .= 'Hi ' . $user->display_name . "!<br /><br />";
  $body .= 'Your Green Pages post has been approved.  Thanks for posting!<br /><br />';
  $body .= 'You can see your new post at:<br />';
  $body .= '<a href="'. $post_url . '" >' . $post_url."</a><br /><br />";
  $body .= "Keep on making an amazing world.<br /><br />";
  $body .= "The Green Pages Team<br />";

  $body .= '<div style="color: rgb(0, 154, 194);font=size:13px; ">';
  $body .= 'Green Pages Australia &nbsp;p 02 8003 5915&nbsp;<br />';
  $body .= '<a href="mailto:info@thegreenpages.com.au">info@thegreenpages.com.au</a>&nbsp;';
  $body .= '<a href="'. get_site_url() .'">'. get_site_url() .'</a>';
  $body .= '<br />';
  $body .= '</div>';

  $body .= '</td></tr></table></td></tr></table><br /><br />';

  wp_mail($user->user_email, 'Your Green Pages post has been approved!', $body, $headers);

}
add_action('pending_to_publish', 'email_after_post_approved');

function force_comments_open($post_id) {
    /**
     * 
     **/

    global $wpdb;
    $post = get_post($post_id);
    $post_score = strtotime($post->post_date_gmt);
    
	// Avoid an infinite loop by the following
	if ( ! wp_is_post_revision( $post_id ) ){
	
		// unhook this function so it doesn't loop infinitely
		remove_action('publish_gp_news', 'force_comments_open');
		remove_action('publish_gp_events', 'force_comments_open');
		remove_action('publish_gp_advertorial', 'force_comments_open');
		remove_action('publish_gp_projects', 'force_comments_open');
    
		// update the post, which calls publish_gp_news again
		$comment_status =  'open';
		$table =           'wp_posts';
		
		$data =            array( 'comment_status' => $comment_status );
		$where =           array( 'ID' => $post_id );
		$format =          array( '%s' );

        $wpdb->update($table, $data, $where, $format);
		
		// re-hook this function
		add_action('publish_gp_news', 'force_comments_open');
		add_action('publish_gp_events', 'force_comments_open');
		add_action('publish_gp_advertorial', 'force_comments_open');
		add_action('publish_gp_projects', 'force_comments_open');
	}
}
add_action('publish_gp_news', 'force_comments_open');
add_action('publish_gp_events', 'force_comments_open');
add_action('publish_gp_advertorial', 'force_comments_open');
add_action('publish_gp_projects', 'force_comments_open');

function create_popularity_timestamp ($post_id) {	
    /** 
     *  Sets date published in unix time as popularity score.
     *  Executed when post is published, including those coming
     *  from rss feeds set to auto publish.
     *    
     *  Author: Katie Patrick
     *  		katie.patrick@greenpag.es
     */
    
	global $wpdb;
    $post = get_post($post_id);
    $post_score = strtotime($post->post_date_gmt);
    
	// Avoid an infinite loop by the following
	if ( ! wp_is_post_revision( $post_id ) ){
	
		// unhook this function so it doesn't loop infinitely
		remove_action('publish_gp_news', 'create_popularity_timestamp');
		remove_action('publish_gp_events', 'create_popularity_timestamp');
		remove_action('publish_gp_advertorial', 'create_popularity_timestamp');
		remove_action('publish_gp_projects', 'create_popularity_timestamp');
	    
		// update the post, which calls publish_gp_news again
		$table = 'wp_posts';
		$data = array(
		            'popularity_score' => $post_score
		        );
		$where = array(
		             'ID' => $post_id
		         );
		$format = array(
				      '%s'
				  );

        $wpdb->update($table, $data, $where, $format);
		
		// re-hook this function
		add_action('publish_gp_news', 'create_popularity_timestamp');
		add_action('publish_gp_events', 'create_popularity_timestamp');
		add_action('publish_gp_advertorial', 'create_popularity_timestamp');
		add_action('publish_gp_projects', 'create_popularity_timestamp');
	}
}
add_action('publish_gp_news', 'create_popularity_timestamp');
add_action('publish_gp_events', 'create_popularity_timestamp');
add_action('publish_gp_advertorial', 'create_popularity_timestamp');
add_action('publish_gp_projects', 'create_popularity_timestamp');

function set_post_location_data_as_decimal($post_id) {
    /** 
     * Store post latitude and longitude as decimal in wp_posts so
     * we can do math on the values when getting surrounding posts of 
     * map centre co-ordinates for users and posts in show_google_maps().
     * 
     * We have to do this as wp_post_meta only stores lat and long as 
     * a string which is useless for querying surrounding posts later on.
     * 
     * Also if no post location defined sets post author location as 
     * post location.
     *   
     * Executed when post is published, including those coming
     * from rss feeds set to auto publish. 
     *    
     * Author: Jesse Browne
     * 	       jb@greenpag.es
     **/
    
    global $wpdb, $post;
    $post = get_post($post_id);
    
    // Avoid an infinite loop by the following
	if ( !wp_is_post_revision( $post_id ) ){
	
		// unhook this function so it doesn't loop infinitely
		remove_action('publish_gp_news', 'set_post_location_data_as_decimal');
	    
		$post_author = get_userdata($post->post_author);
        $post_author_id = $post_author->ID;
    
        $location_meta_key = 	'gp_google_geo_location';
        $lat_meta_key = 		'gp_google_geo_latitude';
        $long_meta_key = 		'gp_google_geo_longitude';
        $country_meta_key = 	'gp_google_geo_country';
        $admin_lvl_one_key = 	'gp_google_geo_administrative_area_level_1';
        $admin_lvl_two_key = 	'gp_google_geo_administrative_area_level_2';
        $admin_lvl_three_key = 	'gp_google_geo_administrative_area_level_3';
        $locality_key = 		'gp_google_geo_locality';
        $locality_slug_key = 	'gp_google_geo_locality_slug';
        
        $post_location = get_post_meta($post_id, $location_meta_key, true);
        
        if ( empty($post_location) ) {
            $author_location =         get_user_meta($post_author_id, $location_meta_key, true);
            $author_lat =              get_user_meta($post_author_id, $lat_meta_key, true);
            $author_long =             get_user_meta($post_author_id, $long_meta_key, true);
            $author_country =          get_user_meta($post_author_id, $country_meta_key, true);
            $author_admin_lvl_one =    get_user_meta($post_author_id, $admin_lvl_one_key, true);
            $author_admin_lvl_two =    get_user_meta($post_author_id, $admin_lvl_two_key, true);
            $author_admin_lvl_three =  get_user_meta($post_author_id, $admin_lvl_three_key, true);
            $author_locality =         get_user_meta($post_author_id, $locality_key, true);
            $author_location_slug =    get_user_meta($post_author_id, $locality_slug_key, true);
        
            update_post_meta($post_id, $location_meta_key, $author_location); 
            update_post_meta($post_id, $lat_meta_key, $author_lat);
            update_post_meta($post_id, $long_meta_key, $author_long);
            update_post_meta($post_id, $country_meta_key, $author_country); 
            update_post_meta($post_id, $admin_lvl_one_key, $author_admin_lvl_one); 
            update_post_meta($post_id, $admin_lvl_two_key, $author_admin_lvl_two); 
            update_post_meta($post_id, $admin_lvl_three_key, $author_admin_lvl_three); 
            update_post_meta($post_id, $locality_key, $author_locality);
            update_post_meta($post_id, $locality_slug_key, $author_location_slug);
        }
    
        $post_lat  = (float) get_post_meta($post_id, $lat_meta_key, true);
        $post_long = (float) get_post_meta($post_id, $long_meta_key, true);
		
		// update the post, which calls publish_gp_news again
		$table = 'wp_posts';
		$data = array(
		            'post_latitude' => $post_lat,
		            'post_longitude' => $post_long
		        );
		$where = array(
		             'ID' => $post_id
		         );
		$format = array(
				      '%s',
		              '%s'
				  );

        $wpdb->update($table, $data, $where, $format);
		
		// re-hook this function
		add_action('publish_gp_news', 'set_post_location_data_as_decimal');
	}
}
add_action('publish_gp_news', 'set_post_location_data_as_decimal');

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

function theme_like() {
    /**
     * Shows upvote icon and count
     * Dependant on some js functions in gp-theme/js/gp.js
     **/
    
    global $post, $current_user, $current_site;
	
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
	
	if ( is_single() ) {
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
			echo '<div id="post-' . $post->ID . '" class="favourite-profile">
			          <a href="' . wp_login_url( "http://" . $_SERVER['HTTP_HOST']  . $_SERVER['REQUEST_URI'] ) . '" 
			             title ="Login to upvote" >
			              <span class="af-icon-chevron-up"></span>
			              <span class="af-icon-chevron-up-number"' . $showlikecount . '>' . $likecount . '</span>
			              <span class="upvote-login" style="display:none;">Login...</span>
			          </a>
			      </div>';
		}
	}

	if ( comments_open($post->ID) ) {
		echo '<div class="comment-profile">
                  <a href="#comments">
                      <span class="comment-mini"></span>
                      <span class="comment-mini-number dsq-postid">
                          <fb:comments-count href="' . get_permalink($post->ID) . '"></fb:comments-count>
                      </span>
                  </a>
              </div>';
	}	
}

function get_random_images() {
    /**
     * Returns array 100+ random images to be used as 
     * thumbnails if no featured image set for posts
     * 
     */
    $site_url = get_site_url();
    $random_images = array(
		
			             $site_url .'/wp-content/uploads/2013/06/random116.jpg',
			             $site_url .'/wp-content/uploads/2013/06/random115.jpg',
			             $site_url .'/wp-content/uploads/2013/06/random114.jpg',
			             $site_url .'/wp-content/uploads/2013/06/random113.jpg',
			             $site_url .'/wp-content/uploads/2013/06/random112.jpg',
			             $site_url .'/wp-content/uploads/2013/06/random111.jpg',
			             $site_url .'/wp-content/uploads/2013/06/random110.jpg',
			             $site_url .'/wp-content/uploads/2013/06/random109.jpg',
			             $site_url .'/wp-content/uploads/2013/06/random108.jpg',
			             $site_url .'/wp-content/uploads/2013/06/random107.jpg',
			             $site_url .'/wp-content/uploads/2013/06/random106.jpg',
			             $site_url .'/wp-content/uploads/2013/06/random105.jpg',
			             $site_url .'/wp-content/uploads/2013/06/random104.jpg',
			             $site_url .'/wp-content/uploads/2013/06/random103.jpg',
			             $site_url .'/wp-content/uploads/2013/06/random102.jpg',
			             $site_url .'/wp-content/uploads/2013/06/random99.jpg',
			             $site_url .'/wp-content/uploads/2013/06/random98.jpg',
			             $site_url .'/wp-content/uploads/2013/06/random97.jpg',
			             $site_url .'/wp-content/uploads/2013/06/random96.jpg',
			             $site_url .'/wp-content/uploads/2013/06/random95.jpg',
			             $site_url .'/wp-content/uploads/2013/06/random94.jpg',
			             $site_url .'/wp-content/uploads/2013/06/random92.jpg',
			             $site_url .'/wp-content/uploads/2013/06/random91.jpg',
			             $site_url .'/wp-content/uploads/2013/06/random90.jpg',
			             $site_url .'/wp-content/uploads/2013/06/random89.jpg',
			             $site_url .'/wp-content/uploads/2013/06/random88.jpg',
			             $site_url .'/wp-content/uploads/2013/06/random87.jpg',
			             $site_url .'/wp-content/uploads/2013/06/random86.jpg',
			             $site_url .'/wp-content/uploads/2013/06/random85.jpg',
			             $site_url .'/wp-content/uploads/2013/06/random84.jpg',
			             $site_url .'/wp-content/uploads/2013/06/random83.jpg',
			             $site_url .'/wp-content/uploads/2013/06/random81.jpg',
			             $site_url .'/wp-content/uploads/2013/06/random80.jpg',
			             $site_url .'/wp-content/uploads/2013/06/random79.jpg',
			             $site_url .'/wp-content/uploads/2013/06/random78.jpg',
			             $site_url .'/wp-content/uploads/2013/06/random77.jpg',
			             $site_url .'/wp-content/uploads/2013/06/random76.jpg',
			             $site_url .'/wp-content/uploads/2013/06/random75.jpg',
			             $site_url .'/wp-content/uploads/2013/06/random74.jpg',
			             $site_url .'/wp-content/uploads/2013/06/random73.jpg',
			             $site_url .'/wp-content/uploads/2013/06/random72.jpg',
			             $site_url .'/wp-content/uploads/2013/06/random71.jpg',
			             $site_url .'/wp-content/uploads/2013/06/random70.jpg',
			             $site_url .'/wp-content/uploads/2013/06/random69.jpg',
			             $site_url .'/wp-content/uploads/2013/06/random68.jpg',
			             $site_url .'/wp-content/uploads/2013/06/random67.jpg',
			             $site_url .'/wp-content/uploads/2013/06/random66.jpg',
			             $site_url .'/wp-content/uploads/2013/06/random65.jpg',
			             $site_url .'/wp-content/uploads/2013/06/random64.jpg',
			             $site_url .'/wp-content/uploads/2013/06/random63.jpg',
			             $site_url .'/wp-content/uploads/2013/06/random62.jpg',
			             $site_url .'/wp-content/uploads/2013/06/random61.jpg',
			             $site_url .'/wp-content/uploads/2013/06/random60.jpg',
			             $site_url .'/wp-content/uploads/2013/06/random59.jpg',
			             $site_url .'/wp-content/uploads/2013/06/random58.jpg',
			             $site_url .'/wp-content/uploads/2013/06/random57.jpg',
			             $site_url .'/wp-content/uploads/2013/06/random56.jpg',
			             $site_url .'/wp-content/uploads/2013/06/random55.jpg',
			             $site_url .'/wp-content/uploads/2013/06/random54.jpg',
			             $site_url .'/wp-content/uploads/2013/06/random53.jpg',
			             $site_url .'/wp-content/uploads/2013/06/random52.jpg',
			             $site_url .'/wp-content/uploads/2013/06/random51.jpg',
			             $site_url .'/wp-content/uploads/2013/06/random50.jpg',
			             $site_url .'/wp-content/uploads/2013/06/random49.jpg',
			             $site_url .'/wp-content/uploads/2013/06/random48.jpg',
			             $site_url .'/wp-content/uploads/2013/06/random47.jpg',
			             $site_url .'/wp-content/uploads/2013/06/random46.jpg',
			             $site_url .'/wp-content/uploads/2013/06/random45.jpg',
			             $site_url .'/wp-content/uploads/2013/06/random44.jpg',
			             $site_url .'/wp-content/uploads/2013/06/random43.jpg',
			             $site_url .'/wp-content/uploads/2013/06/random41.jpg',
			             $site_url .'/wp-content/uploads/2013/06/random40.jpg',
			             $site_url .'/wp-content/uploads/2013/06/random39.jpg',
			             $site_url .'/wp-content/uploads/2013/06/random38.jpg',
			             $site_url .'/wp-content/uploads/2013/06/random37.jpg',
			             $site_url .'/wp-content/uploads/2013/06/random36.jpg',
			             $site_url .'/wp-content/uploads/2013/06/random35.jpg',
			             $site_url .'/wp-content/uploads/2013/06/random34.jpg',
			             $site_url .'/wp-content/uploads/2013/06/random33.jpg',
			             $site_url .'/wp-content/uploads/2013/06/random31.jpg',
			             $site_url .'/wp-content/uploads/2013/06/random30.jpg',
			             $site_url .'/wp-content/uploads/2013/06/random29.jpg',
			             $site_url .'/wp-content/uploads/2013/06/random28.jpg',
			             $site_url .'/wp-content/uploads/2013/06/random27.jpg',
			             $site_url .'/wp-content/uploads/2013/06/random26.jpg',
			             $site_url .'/wp-content/uploads/2013/06/random25.jpg',
			             $site_url .'/wp-content/uploads/2013/06/random23.jpg',
			             $site_url .'/wp-content/uploads/2013/06/random22.jpg',
			             $site_url .'/wp-content/uploads/2013/06/random21.jpg',
			             $site_url .'/wp-content/uploads/2013/06/random20.jpg',
			             $site_url .'/wp-content/uploads/2013/06/random19.jpg',
			             $site_url .'/wp-content/uploads/2013/06/random18.jpg',
			             $site_url .'/wp-content/uploads/2013/06/random17.jpg',
			             $site_url .'/wp-content/uploads/2013/06/random16.jpg',
			             $site_url .'/wp-content/uploads/2013/06/random15.jpg',
			             $site_url .'/wp-content/uploads/2013/06/random14.jpg',
			             $site_url .'/wp-content/uploads/2013/06/random13.jpg',
			             $site_url .'/wp-content/uploads/2013/06/random12.jpg',
			             $site_url .'/wp-content/uploads/2013/06/random11.jpg',
			             $site_url .'/wp-content/uploads/2013/06/random10.jpg',
			             $site_url .'/wp-content/uploads/2013/06/random9.jpg',
			             $site_url .'/wp-content/uploads/2013/06/random8.jpg',
			             $site_url .'/wp-content/uploads/2013/06/random7.jpg',
			             $site_url .'/wp-content/uploads/2013/06/random6.jpg',
			             $site_url .'/wp-content/uploads/2013/06/random5.jpg',
			             $site_url .'/wp-content/uploads/2013/06/random4.jpg',
			             $site_url .'/wp-content/uploads/2013/06/random3.jpg',
			             $site_url .'/wp-content/uploads/2013/06/random2.jpg',
			             $site_url .'/wp-content/uploads/2013/06/random1.jpg'
			
                     );
    return $random_images;
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
			
	if ( get_user_meta($current_user->ID, 'likepost_' . $post->ID , true) ) {
		$likedclass = ' favorited';
	}
			
	echo '<a href="#/" class="topic-select">Topics<span class="topic-select-down"></span></a>';

	$likecount = get_post_meta($post->ID, 'likecount', true);
	if ($likecount > 0) {
		$showlikecount = '';
	} else {
		$likecount = 0;
		$showlikecount = ' style="display:none;"';
	}
      
	$likecount = abbr_number($likecount);
			
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
        echo '<div id="post-' . $post->ID . '" class="favourite-profile">
		          <a href="' . wp_login_url( "http://" . $_SERVER['HTTP_HOST']  . $_SERVER['REQUEST_URI'] ) . '" 
		             title ="Login to upvote">
		              <span class="af-icon-chevron-up"></span>
		              <span class="af-icon-chevron-up-number"' . $showlikecount . '>' . $likecount . '</span>
		              <span class="upvote-login" style="display:none;">Login...</span>
		          </a>
		      </div>';
	}

	if ( comments_open($post->ID) ) {
		echo '<div class="comment-profile">
		          <a href="' . $link . '#comments">
		              <span class="comment-mini"></span>
		              <span class="comment-mini-number dsq-postid">
		                  <fb:comments-count href="' . $link . '"></fb:comments-count>
		              </span>
		          </a>
		      </div>';
	}	
	
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

/* SHOW MEMBERS FAVOURITE POSTS */
function theme_profile_favourites($profile_pid, $post_page, $post_tab, $post_type) {

	// note: Favourites are viewable by everyone!
	
	$profile_author = get_user_by('slug', $profile_pid);

	global $wpdb, $post, $current_user, $current_site, $gp;
	$geo_currentlocation = $gp->location;
	$ns_loc = $gp->location['country_iso2'] . '\\Edition';
	$edition_posttypes = $ns_loc::getPostTypes();
	
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

	$total = "SELECT COUNT(*) as count
				FROM " . $wpdb->prefix . "posts 
				LEFT JOIN " . $wpdb->prefix . "usermeta as m0 on REPLACE(m0.meta_key, 'likepost_', '')=" . $wpdb->prefix . "posts.ID 
				LEFT JOIN " . $wpdb->prefix . "postmeta as m1 on m1.post_id=" . $wpdb->prefix . "posts.ID 
				WHERE post_status='publish' 
					AND (" . $post_type_filter . ")
					AND m0.meta_value > 0 
					AND m0.user_id = $profile_author->ID 
					AND m0.meta_key LIKE 'likepost%' 
					AND m1.meta_value >= 1;";
				
	$totalposts = $wpdb->get_results($total, OBJECT);
	
	#$ppp = intval(get_query_var('posts_per_page'));
	$ppp = 20;
	$wp_query->found_posts = $totalposts[0]->count;
	$wp_query->max_num_pages = ceil($wp_query->found_posts / $ppp);		
	#$on_page = intval(get_query_var('paged'));	
	$on_page = $post_page;

	if($on_page == 0){ $on_page = 1; }		
	$offset = ($on_page-1) * $ppp;

	$querystr = "SELECT DISTINCT " . $wpdb->prefix . "posts.*
					, m1.meta_value as _thumbnail_id 
				FROM " . $wpdb->prefix . "posts 
				LEFT JOIN " . $wpdb->prefix . "usermeta as m0 on REPLACE(m0.meta_key, 'likepost_', '')=" . $wpdb->prefix . "posts.ID 
				LEFT JOIN " . $wpdb->prefix . "postmeta as m1 on m1.post_id=" . $wpdb->prefix . "posts.ID
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
            foreach ($edition_posttypes as $newposttype) {
                if ( $newposttype['enabled'] === true ) {
                    if ($newposttype['id'] == $post_type_key) {
                        if ($newposttype['plural'] === true) {
                            $post_type_name = " " . $newposttype['name'] . "s";
                        } else {
                            $post_type_name = " " . $newposttype['name'];
                        }
                    }
                }
            }
        }
                
        if ($post_type_name) {
            echo "<div class=\"total-posts\">Favourite{$post_type_name}</div>";
        } else {
            echo "<div class=\"total-posts\">Favourites</div>";
        }	    
	    
		$previous_post_title = '';
		foreach ($pageposts as $post) {
		
			setup_postdata($post);
			if ($post->post_title != $previous_post_title) {
			    theme_index_feed_item();
			    $previous_post_title = $post->post_title;
			}
		}
		
		if ( $wp_query->max_num_pages > 1 ) {
			theme_tagnumpagination( $on_page, $wp_query->max_num_pages, $post_tab, $post_type );
		}
}

/* CHARGIFY API COMMUNICATION ---------------------------------------------------------------------------------*/

function get_billing_history($subscription_id,  $component_id) {

    $chargify_key =       '3FAaEvUO_ksasbblajon';
	$chargify_auth =      $chargify_key .':x';
	$chargify_auth_url =  'https://'. $chargify_auth .'green-pages.chargify.com/subscriptions/';
    $chargify_url =       'https://green-pages.chargify.com/subscriptions/' . $subscription_id . '/components/' . $component_id . '/usages.json';
    
    // Chargify api key: 3FAaEvUO_ksasbblajon
    // http://docs.chargify.com/api-authentication

    $ch = curl_init($chargify_auth_url);

    $array = array();
 
    array_push($array, 'Content-Type: application/json;', 'Accept: application/json;', 'charset=utf-8;');

    curl_setopt($ch, CURLOPT_HTTPHEADER, $array);
    curl_setopt($ch, CURLOPT_URL, $chargify_url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
    #curl_setopt($ch, CURLOPT_POSTFIELDS, $usage);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FRESH_CONNECT, true);
    curl_setopt($ch, CURLOPT_VERBOSE, true);
    curl_setopt($ch, CURLOPT_USERPWD, $chargify_auth);

    $json_result = curl_exec($ch);
    $result = json_decode($json_result);
    
    curl_close($ch);
    return $result;    

}

/* SHOW MEMBERS BILLING OPTIONS ---------------------------------------------------------------------------------*/
		
function upgrade_plan($product_id, $budget_status) {
    /**
     * Show appropriate list of advertising plans for user to upgrade to
     * Called by theme_profile_billing()
     * 
     **/
    	
    if ($product_id != '3313297') {} else { return; }
    $site_url = get_site_url();

    if ( $budget_status != 'cancelled' ) {
        ?><h3>Upgrade</h3><?php
        $name = 'upgrade'; 
    } else {
        ?><h3>Reactivate</h3><?php
        $name = 'reactivate';   
    }
    
    ?><form action="<?php echo $site_url; ?>/chargify-upgrade-downgrade-handler/" method="post"><?php

    if ( $name == 'reactivate' ) {
        switch ($product_id) {
            case '3313297':	//$499/wk
    			echo '<select name="'. $name .'">
    			 		<option value="3313297"> &nbsp&nbsp&nbsp $449/week plan &nbsp&nbsp&nbsp </option>
    		  		</select>';			
    		  	break;	
    		case '3313296':	//$249/wk
    			echo '<select name="'. $name .'">
    			 		<option value="3313296">  &nbsp&nbsp&nbsp $249/week plan &nbsp&nbsp&nbsp </option>
    		  		</select>';			
    		  	break;	
    		case '27028': //$99/wk
    			echo '<select name="'. $name .'">
    			 		<option value="27028">  &nbsp&nbsp&nbsp $99/week plan &nbsp&nbsp&nbsp </option>
    		  		</select>';			
    		  	break;	
    		case '27029': //$39/wk
    			echo '<select name="'. $name .'">
    		     		<option value="27029"> &nbsp&nbsp&nbsp $39/week plan &nbsp&nbsp&nbsp </option>
    		  		</select>';			
    		  	break;		
    		case '3313295': //$12/wk
    			echo '<select name="'. $name .'">
    			 		<option value="3313295"> &nbsp&nbsp&nbsp $12/week plan &nbsp&nbsp&nbsp </option>
    		  		</select>';			
    		  	break;
    	}
		?><input type="submit" value="Confirm Reactivation">
    	</form>
    	<div class="clear"></div><?php 
		return;
    }
    
    switch ($product_id) {
		case '3313296':	//$249/wk
			echo '<select name="'. $name .'">
			 		<option value="3313297"> &nbsp&nbsp&nbsp $499/week plan &nbsp&nbsp&nbsp </option>
		  		</select>';			
		  	break;	
		case '27028': //$99/wk
			echo '<select name="'. $name .'">
			 		<option value="3313297">  &nbsp&nbsp&nbsp $499/week plan &nbsp&nbsp&nbsp </option>
		  	 		<option value="3313296">  &nbsp&nbsp&nbsp $249/week plan &nbsp&nbsp&nbsp </option>
		  		</select>';			
		  	break;	
		case '27029': //$39/wk
			echo '<select name="'. $name .'">
			 		<option value="3313297"> &nbsp&nbsp&nbsp $499/week plan &nbsp&nbsp&nbsp </option>
		  	 		<option value="3313296"> &nbsp&nbsp&nbsp $249/week plan &nbsp&nbsp&nbsp  </option>
		     		<option value="27028"> &nbsp&nbsp&nbsp $99/week plan &nbsp&nbsp&nbsp </option>
		  		</select>';			
		  	break;		
		case '3313295': //$12/wk
			echo '<select name="'. $name .'">
			 		<option value="3313297"> &nbsp&nbsp&nbsp $499/week plan &nbsp&nbsp&nbsp </option>
		  	 		<option value="3313296"> &nbsp&nbsp&nbsp $249/week plan &nbsp&nbsp&nbsp </option>
		     		<option value="27028"> &nbsp&nbsp&nbsp $99/week plan &nbsp&nbsp&nbsp </option>
			 		<option value="27029"> &nbsp&nbsp&nbsp $39/week plan &nbsp&nbsp&nbsp </option>
		  		</select>';			
		  	break;
		case '27023': //Directory $39 / month
			echo '<select name="'. $name .'">
			 		<option value="3313297"> &nbsp&nbsp&nbsp $499/week plan &nbsp&nbsp&nbsp </option>
		  	 		<option value="3313296"> &nbsp&nbsp&nbsp $249/week plan &nbsp&nbsp&nbsp </option>
		     		<option value="27028"> &nbsp&nbsp&nbsp $99/week plan &nbsp&nbsp&nbsp </option>
			 		<option value="27029"> &nbsp&nbsp&nbsp $39/week plan &nbsp&nbsp&nbsp </option>
			 		<option value="3313295"> &nbsp&nbsp&nbsp $12/week plan &nbsp&nbsp&nbsp </option>
		  		</select>';			
		  	break;	
	}
	
	?><input type="submit" value="Confirm Upgrade">
	</form>
	<div class="clear"></div><?php 
}


function downgrade_plan($product_id, $budget_status) {
    /**
     * Show appropriate list of advertising plans for user to downgrade to
     * Called by theme_profile_billing()
     * 
     **/

    if ($budget_status == 'cancelled') { return; }	
    $site_url = get_site_url();
    
    ?><h3>Downgrade</h3>
    <form action="<?php echo $site_url; ?>/chargify-upgrade-downgrade-handler/" method="post">
    <?php
    
    switch ($product_id) {
		case '3313297':	//$499/wk
			echo '<select name="downgrade">
			 		<option value="3313296"> &nbsp&nbsp&nbsp $249/week plan &nbsp&nbsp&nbsp </option>
		     		<option value="27028"> &nbsp&nbsp&nbsp $99/week plan &nbsp&nbsp&nbsp </option>
			 		<option value="27029"> &nbsp&nbsp&nbsp $39/week plan &nbsp&nbsp&nbsp </option>
			 		<option value="3313295"> &nbsp&nbsp&nbsp $12/week plan &nbsp&nbsp&nbsp </option>
			 		<option value="cancel"> &nbsp&nbsp&nbsp Cancel Advertising &nbsp&nbsp&nbsp </option>
		  		</select>';			
		  	break;	
		case '3313296': //$249/wk
			echo '<select name="downgrade">
			 		<option value="27028"> &nbsp&nbsp&nbsp $99/week plan &nbsp&nbsp&nbsp </option>
			 		<option value="27029"> &nbsp&nbsp&nbsp $39/week plan &nbsp&nbsp&nbsp </option>
			 		<option value="3313295"> &nbsp&nbsp&nbsp $12/week plan &nbsp&nbsp&nbsp </option>
			 		<option value="cancel"> &nbsp&nbsp&nbsp Cancel Advertising &nbsp&nbsp&nbsp </option>
		  		</select>';			
		  	break;
		 case '27028': //$99/wk
			echo '<select name="downgrade">
			 		<option value="27029"> &nbsp&nbsp&nbsp $39/week plan &nbsp&nbsp&nbsp </option>
			 		<option value="3313295"> &nbsp&nbsp&nbsp $12/week plan &nbsp&nbsp&nbsp </option>
			 		<option value="cancel"> &nbsp&nbsp&nbsp Cancel Advertising &nbsp&nbsp&nbsp </option>
		  		</select>';			
		  	break;	
		case '27029': //$39/wk
			echo '<select name="downgrade">
			 		<option value="3313295"> &nbsp&nbsp&nbsp $12/week plan &nbsp&nbsp&nbsp </option>
			 		<option value="cancel"> &nbsp&nbsp&nbsp Cancel Advertising &nbsp&nbsp&nbsp </option>
		  		</select>';			
		  	break;		
		case '3313295': //$12/wk
			echo '<select name="downgrade">
			 		<option value="cancel"> &nbsp&nbsp&nbsp Cancel Advertising &nbsp&nbsp&nbsp </option>
		  		</select>';			
		  	break;			
		case '27023': //Directory $39 / month
			echo '<select name="downgrade">
					<option value="3313295"> &nbsp&nbsp&nbsp $12/week plan &nbsp&nbsp&nbsp </option>
			 		<option value="cancel"> &nbsp&nbsp&nbsp Cancel Advertising &nbsp&nbsp&nbsp </option>
		  		</select>';			
		  	break;	
	}
	?><input type="submit" value="Confirm Downgrade">
	</form>
	<div class="clear"></div><?php 
}

function create_update_payment_url($profile_author) {
    /**
     *  This creates the SHA1 token at the end of the url for the update_payment, requires first 10 digits 
     *  $token = sha1 ("update_payment--3364787--OG6AQ4YsCTh2lRfEP6p3");
     *
     *  Author: Katie Patrick 
     *          kp@greenpag.es
     **/
	
	$site_key =            'OG6AQ4YsCTh2lRfEP6p3'; // Site Shared Key:
	$subscription_id =     $profile_author->subscription_id;
	$sha_input =           'update_payment--'. $subscription_id .'--'. $site_key;
	$token =               sha1($sha_input);
	$token_10 =            substr($token, 0, 10);  // returns first 10 digits of sha

	$update_payment_url =  'https://green-pages.chargify.com/update_payment/' . $subscription_id.'/'. $token_10;

	return $update_payment_url;
}


function theme_profile_billing($profile_pid) {
    /**
     * Billing panel on profile page
     * 
     * Allows user to upgrade or downgrade cost per click advertising plans
     * and update credit card details
     * 
     * Shows some advertiser history
     * 
     * Authors: Katie Patrick & Jesse Browne
     *          kp@greenpag.es
     *          jb@greenpag.es
     **/
	
	global $current_user;

	if ( ( $current_user->reg_advertiser == '1' ) || ( get_user_role( array('administrator') ) ) ) {} else { return; }
	
	$profile_author =                  get_user_by('slug', $profile_pid);
	$profile_author_id =               $profile_author->ID;
    $site_url =                        get_site_url();
    $user_ID =                         $current_user->ID;
    $product_id =                      $profile_author->product_id;
    $subscription_id =                 $profile_author->subscription_id;
    $budget_status =                   $profile_author->budget_status;
    $advertiser_signup_time =          $profile_author->adv_signup_time;
    $chargify_self_service_page_url =  ( !empty($subscription_id) ) ? create_update_payment_url($profile_author) : '';
    $component_id = 				   get_component_id($product_id);
    
    if ( ( ( is_user_logged_in() ) && ( $current_user->ID == $profile_author->ID ) ) || get_user_role( array('administrator') ) ) {} else {return;}

    if (!empty($chargify_self_service_page_url)) {
        ?><a href="<?php echo $chargify_self_service_page_url; ?>" target="_blank"><h3>Update my credit card details</h3></a><?php    
    } else {
        ?>
        <h3>You currently aren't signed up to a plan with us.</h3>
        <h3><a href="<?php echo $site_url; ?>/advertisers/">Choose a plan.</a></h3>
        <p>Doesn't sound right?</p>
        <p>Send us an email at hello[at]greenpag.es and we'll get to the bottom of it.</p>
        <?php 
    }    
    
    $plan = get_product_name($product_id);

    if ( !empty($product_id) && !empty($plan) ) {
	    
        if ( $budget_status != 'cancelled' ) {
            ?><h3>You are on the <?php echo $plan; ?></h3><?php
        } else {
            ?><h3>You were on the <?php echo $plan; ?>, however your subscription is currently cancelled.</h3><?php   
        }
        upgrade_plan($product_id, $budget_status);
	
		if (!empty($component_id)) {
		
			$history = get_billing_history($subscription_id,  $component_id); 
			
			?><h3>Current Subscription History</h3><?php 
			
			$i = 0; 
			foreach ($history as $usage) {
			    $date =             substr( $usage->usage->created_at, 0, 10 );
			    $clicks =           $usage->usage->quantity;
			    $plan_cpc =         get_cost_per_click($product_id);
			    $cpc =              ( $plan_cpc != NULL ) ? (float) $plan_cpc : (float) 0.0;
			    $billable =         ( $plan_cpc != NULL ) ? ( (int) $clicks ) * $cpc : (float) 0.0;
			    $pretty_cpc =       number_format($cpc, 2);
			    $pretty_billable =  number_format($billable, 2);
			    $total_billed +=    $billable; 
    				
			    if ( $i == 2 ) { ?>
				    
				    <table class="author_analytics">
				        <tr>
					        <td>Activity Date</td>
					        <td>Clicks</td>
					        <td>Cost Per Click</td>
					        <td>Billing Amount</td>
				        </tr> <?php 
			    }
			        
    			if ($date == $prev_date) { 
    			    $sum_clicks +=          $clicks;
    			    $sum_billable =         ( (int) $sum_clicks ) * $cpc;
    			    $sum_pretty_billable =  number_format($sum_billable, 2);
    			} else { 
                    if ( !empty($sum_clicks) ) { ?>
            			<tr>
            				<td><?php echo $prev_date; ?></td>
            				<td><?php echo $sum_clicks; ?></td>
            				<td><?php echo '$'. $pretty_cpc; ?></td>
            				<td><?php echo '$'. $sum_pretty_billable; ?></td>
            			</tr><?php
        			    $sum_clicks =           '';
        			    $sum_billable =         '';
        			    $sum_pretty_billable =  '';
                    } elseif ( !empty( $prev_date ) ) { ?>
            			<tr>
            				<td><?php echo $date; ?></td>
            				<td><?php echo $clicks; ?></td>
            				<td><?php echo '$'. $pretty_cpc; ?></td>
            				<td><?php echo '$'. $pretty_billable; ?></td>
            			</tr><?php
                    }
                }
                    
                $prev_date = $date;
                $i++;
		    }
			
		    if ($i >= 2) { ?></table><?php }
			 	
            $total_billed = number_format($total_billed, 2); ?>
            <table class="author_analytics">
                <tr>
        	        <td><strong>Total billed:</strong></td>
        			<td><?php echo '$'.$total_billed; ?></td>
        		</tr>
    		</table>
    		<?php

		} elseif ( $product_id == '27023') {
		
			?><h3>
			    <p>Why don't you change your subscription to a cost per click plan? 
		    	You'll be able to create unlimited product posts only pay for the clicks you receive. 
				Simply choose a plan from the 'upgrade' menu above.</p>
		    <h3><?php    
		}	
		downgrade_plan($product_id, $budget_status);
	}
}

/* ADVERTISER POST HISTORY LIST */
#shows title, url, post-date and pause/active button so advertisers can manage posts

function list_posts_advertiser($profile_pid) {
	
	global $current_user, $wpdb, $post;
	$site_url = get_site_url();
	$profile_author = get_user_by('slug', $profile_pid);
	
	if ( ( ( is_user_logged_in() ) && ( $current_user->ID == $profile_author->ID ) ) || get_user_role( array('administrator') ) ) {} else {return;}
	
	$querystr = "SELECT DISTINCT " . $wpdb->prefix . "posts.*
                 FROM $wpdb->posts
                 WHERE 
                    post_status = 'publish' and 
                    post_type = 'gp_advertorial' and 
                    ".$wpdb->prefix . "posts.post_author = '" . $profile_author->ID . "' 
                 ORDER BY " . $wpdb->prefix . "posts.post_date DESC";              

	$pageposts = $wpdb->get_results($querystr, OBJECT);
	
	echo '<table class="advertiser_table">';
	
	foreach ($pageposts as $post) {

    	setup_postdata($post);
    	if ($post->post_title != $previous_post_title) {
    	
    		$post_id = $post->ID;
    		$post_status = get_post_status($post_id);
        	
			echo '<tr>
						<td class="advertiser_table">';
        					$link = get_permalink($post->ID);               
        					echo '<a href="'. $link .'">'. $post->post_title .'</a>
        				</td>
        				
        				<!--
        				<td class="author_analytics_date">';
        				#	Active/Paused 'post_hidden', 'post_shown'
        				
        				if ($post_status == 'publish') {
        					echo 'Hide_Post';
        					#$post = array();
        					#$post = $post_id;
        					#$post['post_status'] = 'post_hidden'; //Hides post
        					#wp_update_post($post);
        					
        					
        					} elseif ($post_status == 'post_hidden') {
        					
        						echo 'Show_Post';
        						#$post = array();
        						#$post = $post_id;
        						#$post['post_status'] = 'publish'; //Shows post
        						#wp_update_post($post);
        				}
        					
        				echo '</td>
        				-->
        				
        	 	  <tr>';
    	}
	}       

	echo '</table>';
}



/* SHOW MEMBERS ADVERTISING OPTIONS */
function theme_profile_advertise($profile_pid) {

	global $current_user;
	$site_url = get_site_url();
	$profile_author = get_user_by('slug', $profile_pid);

	if ( ( ( is_user_logged_in() ) && ( $current_user->ID == $profile_author->ID ) ) || get_user_role( array('administrator') ) ) {} else {return;}
		
	# if user IS and advertiser
	if ($profile_author->reg_advertiser == true) {
		
		$product_id = $profile_author->product_id;
	
		$product_name = get_product_name($product_id);
		
		switch ($profile_author->budget_status) {
    		case 'active': //Active client with budget remaining
    			?><h3>You are on the <?php echo $product_name; ?></h3><?php
        		
        		echo '<p>You still have some budget left this week</p>
				<p>Want more clicks? There\'s no limit on how many posts you can make, so go for it! <br />
				Create another post now.</p>
				<a href="'. $site_url .'/forms/create-product-post/"><input type="button" value="Create Another Product Post"></a>
				<div class="clear"></div><br /><br />';
				
				echo '<h3>My Product Posts</h3>';
				
				list_posts_advertiser($profile_pid);
        		break;
        		
    		case 'used_up': //Active client with budget used up for the week
    			?><h3>You are on the <?php echo $product_name; ?></h3><?php
        		
        		echo '<p>Wow, you\'re posts are so popular that your weekly budget is already used up!<br /> 
        		This means that your product posts will not show again until your next billing cycle commences.</p>
        		
        		<p>Want more clicks? Upgrade your weekly budget now.</p>';
			
				echo '<form action="'. $site_url .'/chargify-upgrade-downgrade-handler/" method="post">
        		         '. upgrade_dropdown($product_id) . '
        		    	 <input type="submit" value="Save plan">
        		      </form>
        			  <div class="clear"></div>';
				
				echo '<h3>My Product Posts</h3>';
				list_posts_advertiser($profile_pid);
        		break;
        		
    		case 'cancelled': //Previous active client who has cancelled
    			?><h3>You were on the <?php echo $product_name; ?></h3>
    			<p>Reactivate your account now</p><br /><?php
    			echo '<h3>My Product Posts</h3>';
        		break;
		}	
	
	} else { 
		# if user IS NOT an adverters and never has been
		# Set form urls for creating ad posts for regular monthly subscription advertisers and non regular advertisers
		$post_my_product_form = ($profile_author->reg_advertiser == 1) ? '/forms/create-product-post-subscriber/' : '/forms/create-product-post/';
    	$template_url = get_bloginfo('template_url');
    
    
		echo "
		<div id=\"my-advertise\">
			<div id=\"email\">
				<span><a href=\"" . $site_url . "/advertisers\" ><input type=\"button\" value=\"Create Your First Product Promotion!\" /></a></span>
				<div class=\"clear\"></div>
				<br />
				
				<p> Greenpages offers an extremely effective kind of online advertising: You get to create your own editorials!</p>
				<p>You create the editorial post, then we send it out to the Greenpages members. You only pay for the clicks you receive in 
				cost-per-click model. No click, no payment! You can upgrade, downgrade or pause your advertiser plan at any time.</p>

				
				
				<span><a href=\"" . $site_url . "/advertisers\" target=\"_blank\">Learn more</a></span>
			</div>
		</div>
		<div class=\"clear\"></div>
		";
	}
}

/* SHOW MEMBERS DIRECTORY OPTIONS */
function theme_profile_directory($profile_pid) {
	$profile_author = get_user_by('slug', $profile_pid);
	$profile_author_id = $profile_author->ID;
	$directory_page_url = $profile_author->directory_page_url;
	
	echo "
	<div id=\"my-directory\">
	    <br />
		<a href=\"" . $directory_page_url . "\" target=\"_blank\"><h3>View My Directory Page</h3></a>
		<a href=\"/forms/update-my-directory-page/\">
		    <h3>Update my Directory Page details here</h3>
		</a>
	</div>
	";
}

/* SHOW MEMBERS POST ANALYTICS */
function theme_profile_analytics($profile_pid) {
	global $wpdb, $post, $current_user;

	$profile_author = get_user_by('slug', $profile_pid);
	$profile_author_id = $profile_author->ID;
    $site_url = get_site_url();
	
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
	$old_crm_id =          $profile_author->old_crm_id;
	$directory_page_url =  $profile_author->directory_page_url;
	$facebook =            $profile_author->facebook;
	$linkedin =            $profile_author->linkedin;
	$twitter =             $profile_author->twitter;
	$skype =               $profile_author->skype;
	$url =                 $profile_author->user_url;
	
	if (!$pageposts && !empty($old_crm_id) ) {
		?>
		<div id="my-analytics">
		    <br />
			<?php theme_advertorialcreate_post(); ?>
			<p>Create your first Product of the Week Advertorial to unlock your Analytics.</p>
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
	 	
	    $total_sumURL = 0;
	    
		foreach ($pageposts as $post) {
			setup_postdata($post);
		
			$post_url_ext = $post->post_name; //Need to get post_name for URL. Gets ful URl, but we only need /url extention for Google API			
			$type = get_post_type($post->ID);
				
			$post_type_map = getPostTypeSlug($type);
				
			$post_url_end = '/' . $post_type_map . '/' . $post_url_ext . '/';
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
			
  			$pageViewType = ($analytics->getPageviewsURL('/' . $post_type_map . '/'));	//Page views for the section landing page, e.g. the news page
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
					<td class="author_analytics_title"><a href="' . get_permalink($post->ID) . '" title="' . 
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
			$petition_url = $profile_author->contributors_petition_url;
			$volunteer_url = $profile_author->contributors_volunteer_url;
			
  			$button_labels = array('donate' => $donate_url, 
  									'join' =>  $join_url,
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
			<div class="post-details"><a href="<?php echo $site_url; ?>/wp-admin/profile.php">Enter or update urls for Activist Bar buttons</a></div>
			<br />
			<div class="clear"></div>
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
			<div class="post-details"><a href="<?php echo $site_url; ?>/wp-admin/profile.php">Enter or update urls/ids for Profile Page contact buttons</a></div>
			<br />
			<div id="post-details"></div>   
        <?php     
		}
    	?>		
	</div>
<?php 
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

function theme_create_post() {
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
	    $link    = '/welcome';
	    $message = 'Create a post';
	}
	
	?>	
	
	<div class="new-action">
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
    $post_my_product_form = ( is_user_logged_in()  && $current_user->reg_advertiser == 1 ) ? '/forms/create-product-post-subscriber/' : '/advertisers/';
    $post_my_product_link = ( is_user_logged_in()  && $current_user->reg_advertiser == 1 ) ? "<a href=\"". get_site_url() . $post_my_product_form ."\">Product Post</a>" : "<a href=\"". $post_my_product_form ."\">Product Post ($1.90 / click)</a>"; 
    
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

/** SHOW UPDATE-DELETE BUTTON TO SIGNED IN AUTHORS TO EDIT THEIR PUBLISHED POSTS **/
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

function add_location_and_tag_fields($input, $field, $value, $lead_id, $form_id) {
    /**
     *	Uses Gravity Form filter to assign appropriate id values to specific location 
     *  input fields of Gravity Forms. Location input fields are identified by a 
     *  css class name assigned to the field's wrapper during form creation.
     *  
     *  This enables Google Places autocomplete to work and forms to capture 
     *  location data i.e. lat, long etc for posts and member registration.
     *  
     *  Also captures and stores members tags /key words of interest in their profile. 
     *  http://www.gravityhelp.com/documentation/page/Gform_field_input
     *  
     *  Author: Jesse Browne
     *  		jb@greenpag.es 
     *
     **/
    
    $field_css_class =  $field['cssClass'];
    $input_name_id =    $field['id'];
    $location = 		'gp_google_geo_location';
    $latitude = 		'gp_google_geo_latitude';
    $longitude = 		'gp_google_geo_longitude';
    $country = 			'gp_google_geo_country';
    $admin_lvl_one = 	'gp_google_geo_administrative_area_level_1';
    $admin_lvl_two = 	'gp_google_geo_administrative_area_level_2';
    $admin_lvl_three = 	'gp_google_geo_administrative_area_level_3';
    $locality = 		'gp_google_geo_locality';
    $locality_slug = 	'gp_google_geo_locality_slug';
    $user_tags =        'gp_user_tags';
    $notification_set = 'notification_setting';
    $weekly_email =     'weekly_email';
    $monthly_email =    'monthly_email';
    $type =             'type="hidden"';
    $read_only = 		'readonly="readonly"';    
    
    # Check css class name for match with location class names above and define id on match
    switch ($field_css_class) {
        case $location:
            $type = 'type="text"';
            $read_only = '';
            $input_id = $location;
            break;
        case $latitude:
            $input_id = $latitude;
            break;
        case $longitude:
            $input_id = $longitude;
            break;
        case $country:
            $input_id = $country;
            break;
        case $admin_lvl_one:
            $input_id = $admin_lvl_one;
            break;
        case $admin_lvl_two:
            $input_id = $admin_lvl_two;
            break;
        case $admin_lvl_three:
            $input_id = $admin_lvl_three;
            break;
        case $locality:
            $input_id = $locality;
            break;
        case $locality_slug:
            $input_id = $locality_slug;
            break;
        case $user_tags:
            $type = 'type="text"'; 
            $read_only = '';
            $input_id = $user_tags;
            break;
        case $notification_set:
            $type = 'type="radio"';
            $read_only = '';
            $input_id = $notification_set;
            break;
    }

    switch ($type) {
        case 'type="radio"':
            $input = (isset($input_id)) ? get_correct_radio_buttons($input_name_id, $input_id, $type, $read_only) : '';
            break;
        default:
            $input = (isset($input_id)) ? get_correct_input_field($input_name_id, $input_id, $type, $read_only) : '';
    }
           
    return $input;
}

/* Gravity Form filter of all input fields to assign id's to all location related fields */
add_filter("gform_field_input", "add_location_and_tag_fields", 10, 5);

function get_correct_input_field ($input_name_id, $input_id, $type, $read_only) {
    /**
	 *  Returns location input field for Gravity Forms with appropriate id 
	 *  value to work with Google places autocomplete, location data 
	 *  and user tags / topics of interest. 
	 *  Called by add_location_and_tag_fields($input, $field, $value, $lead_id, $form_id)
     *  
     *  Author: Jesse Browne
     *  		jb@greenpag.es
     */
    
    global $current_user;
    
        $current_data = (isset($current_user->$input_id)) ? $current_user->$input_id : '';
    
    $correct_input = '<div class="ginput_container">
                          <input name="input_'. $input_name_id .'" id="'. $input_id .'" '. $type .' 
                                 value="'. $current_data .'" '. $read_only .' class="medium" tabindex="5">
                      </div>';    

    return $correct_input;
}

function get_correct_radio_buttons($input_name_id, $input_id, $type, $read_only) {
    /**
	 *  Returns notification setting radio button for Gravity Forms with appropriate id  
	 *  Called by add_location_and_tag_fields($input, $field, $value, $lead_id, $form_id)
     *  
     *  Author: Jesse Browne
     *  		jb@greenpag.es
     **/
    
    global $current_user;
    $notification_setting =  $current_user->notification_setting;
    $daily =                 'daily_email';
    $weekly =                'weekly_email';
    $monthly =               'monthly_email';
    $system =                'system_email';
    $daily_decription =      '<span class="slightly-larger-font">
                                  <strong>Daily: \'The Green Laser\'</strong> Get notified each day of news, events and projects happening near you
                              </span>';
    $weekly_decription =     '<span class="slightly-larger-font">
                                  <strong>Weekly: \'The Green Razor\'</strong> The best of your environmental movement in a weekly email
                              </span>';
    $monthly_decription =    '<span class="slightly-larger-font">
                                  <strong>Monthly: \'The Green Phaser\'</strong> The best of the Green Pages Community of the month
                              </span>';
    $system_decription =     '<span class="slightly-larger-font">
                                  <strong>Rare: \'System Messages Only\'</strong>
                              </span>';
    
    switch ($notification_setting) {
        case 'daily_email':
            $check_daily_email =    ' checked="checked"';
            $check_weekly_email =   '';
            $check_monthly_email =  '';
            $check_system_email =   '';
            break;
        case 'weekly_email':
            $check_daily_email =    '';
            $check_weekly_email =   ' checked="checked"';
            $check_monthly_email =  '';
            $check_system_email =   '';
            break;
        case 'monthly_email':
            $check_daily_email =    '';
            $check_weekly_email =   ''; 
            $check_monthly_email =  ' checked="checked"';
            $check_system_email =   '';
            break;
        case 'system_email':
            $check_daily_email =    '';
            $check_weekly_email =   ''; 
            $check_monthly_email =  '';
            $check_system_email =   ' checked="checked"';
            break;
    }
    
    // Currently only offering weekly and rare system only options
    $correct_input = '<div class="ginput_container">
                          <input name="input_'. $input_name_id .'" id="'. $weekly .'" '. $type .' 
                                 value="'. $weekly .'" '. $check_weekly_email .' tabindex="5"> 
                          '. $weekly_decription .'  
                      </div>
                      <div class="ginput_container">
                          <input name="input_'. $input_name_id .'" id="'. $system .'" '. $type .' 
                                 value="'. $system .'" '. $check_system_email .' tabindex="5"> 
                          '. $system_decription .'
                      </div>';

    return $correct_input;
}

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

add_action("login_head", "my_login_head");
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

function fix_broken_images ($post_id) {	
    /** 
     *  Remove The Conversation attribution image
     *  
     *  Author: Jesse Browne
     *  		jb@greenpag.es
     **/

	$post = get_post($post_id);
    $post_author = get_userdata($post->post_author);
    
	// Avoid an infinite loop by the following
	if ( ! wp_is_post_revision( $post_id ) ){
	
		// unhook this function so it doesn't loop infinitely
		remove_action('publish_gp_news', 'fix_broken_images'); 
	    
		$content = $post->post_content;
		$content = str_replace('feedproxy.google.com', 'www.greenpeace.org', $content);
		$content = str_replace('<div id="the_conversation_attribution" style="float:right;">
        <a href="http://theconversation.com/"><br />
          <img src="http://theconversation.com/assets/logos/theconversation_vertical_100px-ab58f56b4507a90ced4077004eb0692e.png" alt="The Conversation"><br />
        </a>
      </div>', '', $content);
		
		$update_post =                  array();
        $update_post['ID'] =            $post_id;
        $update_post['post_content'] =  $content;
        wp_update_post($update_post);
		
		// re-hook this function
		add_action('publish_gp_news', 'fix_broken_images');
	}
}
add_action('publish_gp_news', 'fix_broken_images');

//** MEMBER PERMISSIONS - MEMBERS CAN POST WITHOUT APPROVAL AFTER THEIR THIRD APPROVED POST **//

function member_permission_upgrade ($post_id) {	
    /** 
     *  Checks number of approved posts a user has, then if is greater than 3,  
     *  Adds user meta called 'subscriber_approved' 'true' when subscribed publishes their third post
     *  Another function will check this user_meta value for true or false when setting posts to publish or pending
     *  The first 3 posts a user makes will require approval from GP staff
     *  
     *  Author: Katie Patrick
     *  		katie.patrick@greenpag.es
     */
    
	global $wpdb;
    $post = get_post($post_id);    
    $post_author = get_userdata($post->post_author);
    $post_author_ID = $post_author->ID;
    
	$post_count = (int) $wpdb->get_var("SELECT COUNT(*) FROM $wpdb->posts WHERE post_author = '" . $post_author_ID . "' AND post_status = 'publish'");
    
	// Avoid an infinite loop by the following
	if ( ! wp_is_post_revision( $post_id ) ){
	
		// unhook this function so it doesn't loop infinitely
		remove_action('publish_gp_news', 'member_permission_upgrade');
		remove_action('publish_gp_events', 'member_permission_upgrade');
		remove_action('publish_gp_advertorial', 'member_permission_upgrade');
		remove_action('publish_gp_projects', 'member_permission_upgrade');
	    
	    if ($post_count > 3) {
		    // if user_post_count is greater than 3, change user meta
				
		    $meta_key =  'subscriber_approved';
		    $meta_value = true;
		
		    add_user_meta( $post_author_ID, $meta_key, $meta_value, true );
		    
		}
		
		// re-hook this function
		add_action('publish_gp_news', 'member_permission_upgrade');
		add_action('publish_gp_events', 'member_permission_upgrade');
		add_action('publish_gp_advertorial', 'member_permission_upgrade');
		add_action('publish_gp_projects', 'member_permission_upgrade');
		
	}
}
add_action('publish_gp_news', 'member_permission_upgrade');
add_action('publish_gp_events', 'member_permission_upgradep');
add_action('publish_gp_advertorial', 'member_permission_upgrade');
add_action('publish_gp_projects', 'member_permission_upgrade');

function set_post_to_pending_if_subscriber_not_approved ($post_id) {	
    /** 
     *  If subscriber has published less than required amount of posts
     *  set post status to pending
     *  
     *  Author: Katie Patrick
     *  		katie.patrick@greenpag.es
     **/

	$post = get_post($post_id);
    $post_author = get_userdata($post->post_author);
    
	// Avoid an infinite loop by the following
	if ( ! wp_is_post_revision( $post_id ) ){
	
		// unhook this function so it doesn't loop infinitely
		remove_action('publish_gp_news', 'set_post_to_pending_if_subscriber_not_approved');
		remove_action('publish_gp_events', 'set_post_to_pending_if_subscriber_not_approved');
		remove_action('publish_gp_advertorial', 'set_post_to_pending_if_subscriber_not_approved');
		remove_action('publish_gp_projects', 'set_post_to_pending_if_subscriber_not_approved');	    
	    
		if ( !get_user_role( array('administrator') ) ) { 
		    if ( !get_user_role( array('contributor') ) ) { 
	            if ( $post_author->subscriber_approved != true ) {
                    $update_post =                 array();
                    $update_post['ID'] =           $post_id;
                    $update_post['post_status'] =  'pending';
                    wp_update_post($update_post);
		        }
		    }
		}
		
		// re-hook this function
		add_action('publish_gp_news', 'set_post_to_pending_if_subscriber_not_approved');
		add_action('publish_gp_events', 'set_post_to_pending_if_subscriber_not_approved');
		add_action('publish_gp_advertorial', 'set_post_to_pending_if_subscriber_not_approved');
		add_action('publish_gp_projects', 'set_post_to_pending_if_subscriber_not_approved');
		
	}
}
add_action('publish_gp_news', 'set_post_to_pending_if_subscriber_not_approved');
add_action('publish_gp_events', 'set_post_to_pending_if_subscriber_not_approved');
add_action('publish_gp_advertorial', 'set_post_to_pending_if_subscriber_not_approved');
add_action('publish_gp_projects', 'set_post_to_pending_if_subscriber_not_approved');

function set_event_dates_lat_and_long($entry, $form) {
    /**
     * Converts event date data from gravity form into timestamp
     * so that event can be sorted by start date in event_index()
     * Also sets post lat and long as decimal for events in post table
     * Triggered on gravity form submission
     * 
     * Author: Jesse Browne
     *         jb@greenpag.es
     **/
    
    global $wpdb, $post;
    $post    = get_post($entry["post_id"]);
    setup_postdata( $post ); 
    $post_id = $post->ID;

	// Avoid an infinite loop by the following
	if ( !wp_is_post_revision( $post_id ) ){
	
		// unhook this function so it doesn't loop infinitely
        remove_action("gform_after_submission", "set_event_dates_lat_and_long", 10, 2);
	    
        $start_key    = 'gp_events_startdate';
        $end_key      = 'gp_events_enddate';
        $start_date   = get_post_meta($post_id, $start_key, true);
        $end_date     = get_post_meta($post_id, $end_key, true);
        
        if ( !empty($start_date) && !empty($end_date) ) {

            $start_ts = strtotime ($start_date);
            $end_ts   = strtotime ($end_date);
            update_post_meta($post_id, $start_key, $start_ts);
            update_post_meta($post_id, $end_key, $end_ts);     
        
        }    

        if ($post->post_type == 'gp_news') {

            $location_meta_key = 	   'gp_google_geo_location';
            $lat_meta_key = 		   'gp_google_geo_latitude';
            $long_meta_key = 		   'gp_google_geo_longitude';
            $country_meta_key = 	   'gp_google_geo_country';
            $admin_lvl_one_key = 	   'gp_google_geo_administrative_area_level_1';
            $admin_lvl_two_key = 	   'gp_google_geo_administrative_area_level_2';
            $admin_lvl_three_key = 	   'gp_google_geo_administrative_area_level_3';
            $locality_key = 		   'gp_google_geo_locality';
            $locality_slug_key = 	   'gp_google_geo_locality_slug';
            
            $edit_news_uri =           '/forms/update-news/?gform_post_id='. $post->ID;
            
            $location_entry = 	       ( strpos($_SERVER['REQUEST_URI'], $edit_news_uri ) === 0 ) ? '9'  : '8' ;
            $lat_entry = 		       ( strpos($_SERVER['REQUEST_URI'], $edit_news_uri ) === 0 ) ? '10' : '9' ;
            $long_entry = 		       ( strpos($_SERVER['REQUEST_URI'], $edit_news_uri ) === 0 ) ? '11' : '10';
            $country_entry = 	       ( strpos($_SERVER['REQUEST_URI'], $edit_news_uri ) === 0 ) ? '12' : '11';
            $admin_lvl_one_entry = 	   ( strpos($_SERVER['REQUEST_URI'], $edit_news_uri ) === 0 ) ? '13' : '12';
            $admin_lvl_two_entry = 	   ( strpos($_SERVER['REQUEST_URI'], $edit_news_uri ) === 0 ) ? '14' : '13';
            $admin_lvl_three_entry =   ( strpos($_SERVER['REQUEST_URI'], $edit_news_uri ) === 0 ) ? '15' : '14';
            $locality_entry = 		   ( strpos($_SERVER['REQUEST_URI'], $edit_news_uri ) === 0 ) ? '16' : '15';
            $locality_slug_entry = 	   ( strpos($_SERVER['REQUEST_URI'], $edit_news_uri ) === 0 ) ? '17' : '16';              
            
            $original_location =       $entry[$location_entry];
            $original_lat =            $entry[$lat_entry];
            $original_long =           $entry[$long_entry];
            $original_country =        $entry[$country_entry];
            $original_admin_1 =        $entry[$admin_lvl_one_entry];
            $original_admin_2 =        $entry[$admin_lvl_two_entry];
            $original_admin_3 =        $entry[$admin_lvl_three_entry];
            $original_locality =       $entry[$locality_entry];
            $original_locality_slug =  $entry[$locality_slug_entry];
        
            update_post_meta($post_id, $location_meta_key, $original_location); 
            update_post_meta($post_id, $lat_meta_key, $original_lat);
            update_post_meta($post_id, $long_meta_key, $original_long);
            update_post_meta($post_id, $country_meta_key, $original_country); 
            update_post_meta($post_id, $admin_lvl_one_key, $original_admin_1); 
            update_post_meta($post_id, $admin_lvl_two_key, $original_admin_2); 
            update_post_meta($post_id, $admin_lvl_three_key, $original_admin_3); 
            update_post_meta($post_id, $locality_key, $original_locality);
            update_post_meta($post_id, $locality_slug_key, $original_locality_slug);            
        }
            
        $post_lat  =       (float) get_post_meta($post_id, $lat_meta_key, true);
        $post_long =       (float) get_post_meta($post_id, $long_meta_key, true);
            
    	// update the post, with lat and long as decimal
		$table =           'wp_posts';
		$data =            array( 'post_latitude' => $post_lat, 'post_longitude' => $post_long );
		$where =           array( 'ID' => $post_id );
		$format =          array( '%s', '%s' );
   
        $wpdb->update($table, $data, $where, $format);     

        add_action("gform_after_submission", "set_event_dates_lat_and_long", 10, 2);    
	}
}
add_action("gform_after_submission", "set_event_dates_lat_and_long", 10, 2);

function get_post_type_map() {
    /**
     * Useful for creating pretty post type names
     **/
    
    $post_type_map = array( "gp_news"     => "news", 
    						"gp_events"   => "events", 
                            "gp_products" => "products", 
                            "gp_projects" => "projects" );
    
    return $post_type_map;
}

function get_product_name($product_id) {
    
    // Map of product_id to names of plans
    $plan_type_map = array( "3313295"  => "$12 / week plan",
							"27029"    => "$39 / week plan",
							"27028"    => "$99 / week plan",
							"3313296"  => "$249 / week plan",
							"3313297"  => "$499 / week plan",
                            "3325582"  => "Free CPC Plan",
							"27023"    => "Directory page $39 / month plan" );
                        
    $product_name = $plan_type_map[$product_id];
    
    return $product_name;
    
}

function get_component_id($product_id) {
    /**
	 * Return component id mapped to product id
	 * for Chargify metered billing components
	 **/
    
    $component_map = array( '3313295'  => '3207',
							'27029'    => '3207',
							'27028'    => '3207',
							'3313296'  => '20016',
							'3313297'  => '20017',
                            '3325582'  => '21135',
							'27023'    => '' );
                        
    $component_id = $component_map[$product_id];

    return $component_id;
}

function get_cost_per_click($product_id) {
    /**
     * As it sounds, returns cost per click depending 
     * on which chargify subscription user is on
     */
    switch ($product_id)   {
        case '3313295':
            // $12 per week plan
            $cpc = 1.9;
            break;
        case '27029':
            // $39 per week plan
            $cpc = 1.9;
            break;
        case '27028':
            // $99 per week plan
            $cpc = 1.9;
            break; 
        case '3313296':
            // $249 per week plan
            $cpc = 1.8;
            break; 
        case '3313297':
            // $499 per week plan
            $cpc = 1.7;
            break;
        case '3325582':
            // Free CPC plan
            $cpc = NULL;
            break;                                   
    }
    return $cpc;   
}

function get_click_cap($product_id) {
    /**
     * Calculate maximum clicks available per week
     * based on which plan advertiser is signed up to.
     * 
     * Called by metered-billing-cron.php
     * 
     * Author: Jesse Browne
     *         jb@greenpag.es
     *  
     **/
    
    switch ($product_id)   {
        case '3313295':
            // $12 per week plan
            $cap = (int) (12.00 / 1.9);
            break;
        case '27029':
            // $39 per week plan
            $cap = (int) (39.00 / 1.9);
            break;
        case '27028':
            // $99 per week plan
            $cap = (int) (99.00 / 1.9);
            break; 
        case '3313296':
            // $249 per week plan
            $cap = (int) (249.00 / 1.8);
            break; 
        case '3313297':
            // $499 per week plan
            $cap = (int) (449.00 / 1.7);
            break;
        case '3325582':
            // Free cpc plan
            $cap = (int) 1000;
            break;
        case '27023':
            // $39 per month old plan
            $cap = (int) 1000;
            break;                                              
    }
    
    return $cap;
}

function get_clicks_for_post($post_row, $user_id, $analytics, $start_range, $end_range) {
    /**
     * Returns total outbound clicks from a post from Google Analytics,
     * gets product button clicks also. 
     * 
     * Called by metered-billing-cron.php and weekly-advertiser-email.php
     * 
     * @TODO Refactor the shit out of the analytics function for profile page, 
     *       this function could be called from there. 
     *       Maybe move analytics functions to separate file?
     *       
     * Authors: Initial work on analytics done by 
     *          Katie Patrick, Stephanie 'Cord' Melton &
     *          Jesse Browne
     *          jb@greenpag.es
     * 
     */
    
	$analytics->setDateRange($start_range, $end_range);	        //Set date in GA $analytics->setMonth(date('$post_date'), date('$new_date'));

   	#SET UP POST ID AND AUTHOR ID DATA, POST DATE, GET LINK CLICKS DATA FROM GA 
	$profile_author_id =  $user_id;
	$post_id =            $post_row->ID;
	$click_track_tag =    '/yoast-ga/' . $post_id . '/' . $profile_author_id . '/outbound-article/';

	$clickURL = ($analytics->getPageviewsURL($click_track_tag));
	$sumClick = 0;

	foreach ($clickURL as $data) {
   		$sumClick = $sumClick + $data;
	}

    // Get url product button is linked to
    $sql_product_url = 'SELECT meta_value 
                        FROM wp_postmeta 
                        WHERE post_id = "'. $post_id .'"
                            AND meta_key = "gp_advertorial_product_url";';

    $product_url_results =  mysql_query($sql_product_url);
    mysql_data_seek($product_url_results, 0);
    $product_url_row =      mysql_fetch_object($product_url_results);	
	$product_url =          $product_url_row->meta_value;

	if ( !empty($product_url) ) {		# IF 'BUY IT' BUTTON ACTIVATED, GET CLICKS
	    $click_track_tag_product_button = '/outbound/product-button/' . $post_id . '/' . $profile_author_id . '/' . $product_url . '/'; 	         
		$clickURL_product_button = ($analytics->getPageviewsURL($click_track_tag_product_button));
            
		foreach ($clickURL_product_button as $data) {
   			$sumClick = $sumClick + $data;
		}
	}
        
    return $sumClick;
}

?>
