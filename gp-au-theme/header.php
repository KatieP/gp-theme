<!DOCTYPE html>

<?php
global $gp;
$site_url = get_site_url();

if ( is_user_logged_in() ) {
	global $current_user;
}

$template_url = get_bloginfo('template_url');

$schematype = ' itemscope itemtype="http://schema.org/';
if ( is_single() ) {
	switch (get_post_type()) {
	    case 'gp_events':
	        $schematype .= 'Event';
	        break;
	    default:
	       $schematype .= 'Article';
	}
	$schematype .= '"';
} else {
	$schematype = '';
}

$htmlattr = 'xmlns="http://www.w3.org/1999/xhtml" lang="EN" xml:lang="EN" dir="ltr"' . $schematype . ' xmlns:og="http://ogp.me/ns#" xmlns:fb="http://www.facebook.com/2008/fbml"';
?>

<!--[if lt IE 7]>      <html <?php echo $htmlattr; ?> class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html <?php echo $htmlattr; ?> class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html <?php echo $htmlattr; ?> class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!--> <html <?php echo $htmlattr; ?> class="no-js"> <!--<![endif]-->

    <head>	
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
        <meta name="viewport" content="width=device-width">
                
		<script type="text/javascript">
			window.google_analytics_uacct = "UA-2619469-9";
		</script>
		
		<title><?php
        /*
         * Print the <title> tag based on what is being viewed.
         */
        global $page, $paged, $post;

        wp_title( '|', true, 'right' );

        // Add the blog name.
        bloginfo( 'name' );

        // Add the blog description for the home/front page.
        $site_description = get_bloginfo( 'description', 'display' );
        if ( $site_description && ( is_home() || is_front_page() ) ) {
                echo " | $site_description";
        }

        // Add a page number if necessary:
        if ( $paged >= 2 || $page >= 2 )
                echo ' | ' . sprintf( __( 'Page %s', 'greenpages' ), max( $paged, $page ) );
        ?>
        </title>
		
		<?php
			$out_excerpt = "";
			if ( is_single() || is_page() ) {
				if ( have_posts() ) {
					while ( have_posts() ) {
						the_post();
						$out_excerpt = str_replace(array('\r\n', '\r', '\n'), '', strip_tags(get_the_excerpt()));
						$out_excerpt = apply_filters('the_excerpt_rss', $out_excerpt);
					}
				}
			}
		
			// Show required info for Google Plus button
			if ( is_single() || is_page() ) {
				echo '<meta itemprop="name" content="' . esc_attr(get_bloginfo('name')) . esc_attr(wp_title('|', false, '')) . '"/>';
				echo '<meta itemprop="description" content="' . esc_attr(sanitize_text_field($out_excerpt)) . '"/>';
				
				if ( is_single() && function_exists('has_post_thumbnail') && has_post_thumbnail($post->ID) ) {
					$socialThumb = wp_get_attachment_image_src(get_post_thumbnail_id($post->ID), 'thumbnail');
					echo '<meta itemprop="image" content="' . esc_url($socialThumb[0]) . '"/>';
				}
			}
		
			// Show required info for Facebook attach link functionality and open graph protocol
			$permalink = ( isset($post) ? get_permalink($post->ID) : "" );
			$facebook_app_id = show_facebook_by_location();
			?>
			<meta property="fb:app_id" content="<?php echo $facebook_app_id; ?>" />
            <meta property="og:locale" content="en_US" />
			<meta property="og:site_name" content="Green Pages" />
			<meta property="og:url" content="<?php echo $permalink; ?>"/>
			<meta property="fb:admins" content="100000564996856,katiepatrickgp,eddy.respondek"/>
	        <?php 
			if ( is_single() && function_exists('has_post_thumbnail') && has_post_thumbnail($post->ID) ) {
				$socialThumb = wp_get_attachment_image_src(get_post_thumbnail_id($post->ID), 'thumbnail');
				echo '<link rel="image_src" href="' . esc_url($socialThumb[0]) . '" />
				      <meta property="og:image" content="' . esc_url($socialThumb[0]) . '"/>';
			}
			
			if ( is_single() || is_page() ) {
				echo '<meta property="og:type" content="article"/>
				      <meta name="title" property="og:title" content="' . esc_attr(wp_title('', false, '')) . '" />
				      <meta name="description" property="og:description" content="' . esc_attr(sanitize_text_field($out_excerpt)) . '" />';
			}
			
			if ( is_home() || is_front_page() ) {
                echo '<meta property="og:type" content="blog"/>';
        	}
		    ?>

		<link rel="shortcut icon" href="<?php echo $template_url; ?>/template/gpicon.ico" />
		<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />
		
		<?php
        /* We add some JavaScript to pages with the comment form
         * to support sites with threaded comments (when in use).
         */
        if ( is_singular() && get_option( 'thread_comments' ) ) {
            wp_enqueue_script( 'comment-reply' );
        }
        
        /* Add google geo location javascript to pages so location autocomplete 
         * works on Gravity forms and select location function in header
         */

        if ( is_page() ) { 
            if ( strpos($_SERVER['REQUEST_URI'], '/world-map/' ) === 0 ) {
                ;
            } else {
                add_action('wp_head', 'gp_js_postGeoLoc_meta');
            }
        }
        
        /* Always have wp_head() just before the closing </head>
         * tag of your theme, or you will break many plugins, which
         * generally use this hook to add elements to <head> such
         * as styles, scripts, and meta tags.
         */
        wp_head();
		?>
		<!-- Remove unnecessary call to jquery css, ugly solution I know! -->
		<script type="text/javascript">
		    rogue_element = document.getElementById("jquery-ui-css");
		    rogue_element.parentNode.removeChild(rogue_element);
		</script>	
	</head>

	<body>
		<!-- Facebook JavaScript SDK -->
		<div id="fb-root"></div>
		<script>
			window.fbAsyncInit = function() {
				FB.init({appId: '305009166210437', status: true, cookie: true, xfbml: true});
			};
			(function() {
				var e = document.createElement('script'); e.async = true;
				e.src = document.location.protocol + '//connect.facebook.net/en_US/all.js';
				document.getElementById('fb-root').appendChild(e);
			}());
		</script>
		<!-- End Facebook JavaScript SDK -->
		
		<?php
        $location_filter_uri =  get_location_filter_uri();
        $user_country =         $gp->location['country_iso2'];
        ?>
		<header>
    		<div class="pos">
    		    <div class="template-left">
                    <a id="header-logo" href="<?php echo $site_url; ?>">greenpag.es</a>
			        <nav id="header-nav">
				        <ul>
					        <?php $post_type = ( isset($post) ? get_post_type($post->ID) : "" ); ?>
					        <li><a href="<?php echo $site_url; ?>/news/<?php echo $location_filter_uri; ?>"<?php if ( $post_type == 'gp_news' && !is_home() ){echo ' class="active"';} ?>>News</a></li>
					        <li><a href="<?php echo $site_url; ?>/events/<?php echo $location_filter_uri; ?>"<?php if ( $post_type == 'gp_events' ) {echo ' class="active"';} ?>>Events</a></li>
					        <li><a href="<?php echo $site_url; ?>/eco-friendly-products/<?php echo $location_filter_uri; ?>"<?php if ( $post_type == 'gp_advertorial' ) {echo ' class="active"';} ?>>Products</a></li>
					        <li><a href="<?php echo $site_url; ?>/projects/<?php echo $location_filter_uri; ?>"<?php if ( $post_type == 'gp_projects' ) {echo ' class="active"';} ?>>Projects</a></li>
	                        <?php 
	                        // Display Directory link only if user in Australia
	                        if ( $user_country == 'AU' ) { ?> <li><a href="http://directory.thegreenpages.com.au/">Directory</a></li><?php ;} ?>					    
				        </ul>
			        </nav>
			    </div>
			    <div class="template-right">
				    <?php 
		    	    if ( !is_user_logged_in() ) {
			        ?>
			    	<nav id="header-auth">
					    <?php 
					    if (!isset($_GET['noscript'])) { 
					    ?>
					    <ul id="auth-tools">
						    <li id="auth-youraccount">
                            	<a href="<?php echo wp_login_url( "http://" . $_SERVER['HTTP_HOST']  . $_SERVER['REQUEST_URI'] ); ?>" class="lower">
	    	                        Log In
    	                        </a>
            	                <span class="breaker"> | </span>
                	            <a href="<?php echo $site_url ;?>/welcome" class="lower">Join</a>
						    </li>
				    	</ul>
				    	<div class="clear"></div>
				        <?php 
					    } 
					    ?>
			    	</nav>
			        <?php 
    			    } else { 
    			        global $wpdb;
	    		        global $current_site;    	
		    	        $post_author_url = ( isset($current_user) ? get_author_posts_url($current_user->ID) : "" );
			        ?>
			    	<nav id="header-auth">
			  	    	<div id="auth-forgot">
						    <?php echo "<a href=\"" . $post_author_url . "\">" . $current_user->display_name ."</a>"; ?>
					    </div>
					    <ul id="auth-tools">
						    <li id="auth-yourfavourites" class="no-js">
							    <a href="<?php echo $post_author_url; ?>#tab:favourites" title="My Upvoted Posts">
	    						    <span class="af-icon-chevron-up"></span>
	    						    
							    </a>
							</li>
					    	<li id="auth-yournotifications" class="no-js">
							    <a href="#/" class="auth-yournotifications-start" title="My Notifications">
								    <span class="icon-notifications">My Notifications</span>
							    </a>
							    
							    <ul id="auth-dash-notifications" class="auth-dash">
								    <li class="auth-dash-title">You have no notifications yet.</li>
							    </ul>
					    	</li>
					    	<li id="auth-youraccount" class="no-js">
						    	<a href="#" class="auth-youraccount-start">
							    	<span>My Account</span>
						    	</a>
						    	<ul id="auth-dash-account" class="auth-dash">
								    <li class="auth-dash-title">Account Options</li>
							    	<li class="auth-dash-avatar"><a href="<?php echo $post_author_url; ?>"><?php echo get_avatar( $current_user->ID, '50', '', $current_user->display_name ); ?></a></li>
								    <li class="auth-account-options">	
									    <a href="<?php echo $post_author_url; ?>" title="My profile">View Profile</a>
									    <a href="<?php echo $site_url; ?>/forms/edit-profile-details/" title="Edit Profile">Edit Profile</a>
									    <a href="<?php echo $site_url; ?>/forms/profile-picture-editor/" title="Profile Picture">Profile Picture</a>
									    <a href="<?php echo $site_url; ?>/forms/profile-email-editor/" title="Email">Email</a>
									    <a href="<?php echo $site_url; ?>/forms/profile-notifications/" title="Notifications">Notifications</a>
									    <a href="<?php echo wp_logout_url( $site_url ); ?>" title="Logout">Logout</a>
								    </li>
							    </ul>
						    </li>
					    </ul>
					    <div class="clear"></div>
				    </nav>
			        <?php } ?>
			    	<!-- Google CSE Search Box -->
                	<div id="header-search">
	                    <form id="cref_iframe" method="get" action="<?php echo $site_url; ?>/search/">
				            <div id="search-field"><input type="text" maxlength="255" size="40" name="q" placeholder="Search" /></div>
				            <div id="search-button"><input type="submit" value=""/></div>
				        </form>
			    	</div>
			    	<div class="clear"></div>
                	<!-- Google CSE Search Box Ends -->
            	</div>			
                <?php
                if (!is_page()) {
       	            # Display location tag line and location filter option
                    $posttype_slug =               getPostTypeSlug($post_type);
                    $location_filter =             get_location_filter();
                    $location_filter_url_prefix =  $site_url . '/' . $posttype_slug;
	                ?>
            		<script type="text/javascript">
                		function show_location_field() {
                    		document.getElementById("header_location_field").className = "";
                    		document.getElementById("header_user_location").className = "hidden";
                		}

                		function hide_location_field() {
                    		document.getElementById("header_location_field").className = "hidden";
                    		document.getElementById("header_user_location").className = "";
                		}
            		</script>
    				<div class="post-details" id="header-tagline">
	            		Everything environmental happening around <span id="header_user_location" class=""><a href="#" onclick="show_location_field();"><?php echo $location_filter; ?></a>.</span>
	            		<span id="header_location_field" class="hidden">
	            		   <!-- Location filter data is set here, processed by display_google_map_posts_and_places_autocomplete() -->
	            		   <form action="#" method="get">
	            		    	<input name="location_filter" id="location_filter" type="text" />
	            		    	<input name="latitude_filter" id="latitude_filter" type="hidden" value="" readonly="readonly" />
								<input name="longitude_filter" id="longitude_filter" type="hidden" value="" readonly="readonly" />
								<input name="location_slug_filter" id="location_slug_filter" type="hidden" value="" readonly="readonly" />
								<input name="admin_area_level_1_filter" id="admin_area_level_1_filter" type="hidden" value="" readonly="readonly" />
								<input name="admin_area_level_2_filter" id="admin_area_level_2_filter" type="hidden" value="" readonly="readonly" />
								<input name="admin_area_level_3_filter" id="admin_area_level_3_filter" type="hidden" value="" readonly="readonly" />
								<input name="locality_filter" id="locality_filter" type="hidden" value="" readonly="readonly" />
								<input name="location_filter_url_prefix" id="location_filter_url_prefix" type="hidden" value="<?php echo $location_filter_url_prefix; ?>" readonly="readonly" />
	            		    	<a id="location_filter_go" href="#"><input type="button" value="Go" /></a>
	            		    	<input type="button" value="Cancel" onclick="hide_location_field();" />
	            		   </form>
	            		</span>
	            		<div id="dummy_map_canvas"></div>
            		</div>
            	<?php
            	}
            	?>
        	</header>
        	        	