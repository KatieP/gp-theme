<!DOCTYPE HTML>

<?php
if ( is_user_logged_in() ) {
	global $current_user;
	#$current_user = wp_get_current_user();
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
?>
<html xmlns="http://www.w3.org/1999/xhtml" lang="EN" xml:lang="EN" dir="ltr"<?php echo $schematype; ?> xmlns:og="http://ogp.me/ns#" xmlns:fb="http://www.facebook.com/2008/fbml">

	<head>
	
		<?php
		if (isset($_GET['noscript'])) { 
			if ($_GET['noscript'] != '1') { ?>
		<noscript>
			<meta http-equiv=refresh content="0; URL=/?noscript=1" />
		</noscript>
		<?php }} ?>

		<noscript>
			<meta http-equiv="X-Frame-Options" content="deny" />
		</noscript>
	
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
		
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

        ?></title>
		
		<?php
			if ( is_single() || is_page() ) {
				if ( have_posts() ) : while ( have_posts() ) : the_post();
						$out_excerpt = str_replace(array('\r\n', '\r', '\n'), '', strip_tags(get_the_excerpt()));
						$out_excerpt = apply_filters('the_excerpt_rss', $out_excerpt);
					endwhile;
				endif;
			}
		
			// Show required info for Google Plus button
			if (get_post_type() != "page") {
				echo '<meta itemprop="name" content="' . esc_attr(get_bloginfo('name')) . esc_attr(wp_title('|', false, '')) . '">';
				echo '<meta itemprop="description" content="' . esc_attr($out_excerpt) . '">';
				
				if ( is_single() && function_exists('has_post_thumbnail') && has_post_thumbnail($post->ID) ) {
					$socialThumb = wp_get_attachment_image_src(get_post_thumbnail_id($post->ID), 'thumbnail');
					echo '<meta itemprop="image" content="' . esc_url($socialThumb[0]) . '">';
				}
			}
		
			// Show required info for Facebook attach link functionality and open graph protocol
			echo '<meta property="fb:app_id" content="305009166210437" />';
            echo '<meta property="og:locale" content="en_US" />';
			echo '<meta property="og:site_name" content="Green Pages" />';
			echo '<meta property="og:url" content="' . urlencode(get_permalink($post->ID)) . '"/>';
			echo '<meta property="fb:admins" content="100000564996856,katiepatrickgp,eddy.respondek"/>';
	
			if ( is_single() && function_exists('has_post_thumbnail') && has_post_thumbnail($post->ID) ) {
				$socialThumb = wp_get_attachment_image_src(get_post_thumbnail_id($post->ID), 'thumbnail');
				echo '<link rel="image_src" href="' . esc_url($socialThumb[0]) . '" />';
				echo '<meta property="og:image" content="' . esc_url($socialThumb[0]) . '"/>';
			}
			
			if ( is_single() || is_page() ) {
				echo '<meta property="og:type" content="article"/>';
				echo '<meta name="title" property="og:title" content="' . esc_attr(get_bloginfo('name')) . esc_attr(wp_title('|', false, '')) . '" />';
				echo '<meta name="description" property="og:description" content="' . esc_attr($out_excerpt) . '" />';
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
        if ( is_singular() && get_option( 'thread_comments' ) )
                wp_enqueue_script( 'comment-reply' );

        /* Always have wp_head() just before the closing </head>
         * tag of your theme, or you will break many plugins, which
         * generally use this hook to add elements to <head> such
         * as styles, scripts, and meta tags.
         */
        wp_head();
		?>
		
		<script type='text/javascript' src='http://partner.googleadservices.com/gampad/google_service.js'>
		</script>
		<script type='text/javascript'>
			GS_googleAddAdSenseService("ca-pub-5276108711751681");
			GS_googleEnableAllServices();
		</script>
		<script type='text/javascript'>
			GA_googleAddSlot("ca-pub-5276108711751681", "stg1_medrec");
			GA_googleAddSlot("ca-pub-5276108711751681", "stg1_medrec2");
			GA_googleAddSlot("ca-pub-5276108711751681", "stg1_ldrbrd");
		</script>
		<script type='text/javascript'>
			GA_googleFetchAds();
		</script>
		
		<script type="text/javascript" src="http://apis.google.com/js/plusone.js"></script>
				
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
	
		<header class="pos">
			<nav id="header-mast">
				<a href="<?php echo get_option('home'); ?>">
					<span class="header-slogan"><?php echo str_replace(':', ':<br />', get_option('gp_slogan')); ?></span>
					<img src="<?php echo $template_url; ?>/template/mast.gif" width="234" height="38" />
				</a>
			</nav>
      <!-- Google CSE Search Box -->
        <form id="cref_iframe" method="get" action="<?php echo get_site_url();?>/search/">
				<div id="search-tag"><span>Search For:</span> products, news, people, events, tips, forums.</div>
				<div id="search-field"><input type="text" maxlength="255" size="40" name="q"/></div>
				<div id="search-button"><input type="submit" name="sa" value="Search" /></div>
			</form>
      <!-- Google CSE Search Box Ends -->
			<nav id="header-nav">
				<ul>
					<?php # wp_list_pages('show_count=0&title_li=&hide_empty=0&use_desc_for_title=0&child_of=43&exclude=64')
					$post_type = get_post_type($post->ID);
					?>
					<li><a href="http://directory.thegreenpages.com.au/">Directory</a></li>
					<li><a href="/news"<?php if ( $post_type == 'gp_news' && !is_home() ){echo ' class="active"';} ?>>News</a></li>
					<li><a href="/events"<?php if ( $post_type == 'gp_events' ) {echo ' class="active"';} ?>>Events</a></li>
					<li><a href="/new-stuff"<?php if ( $post_type == 'gp_advertorial' ) {echo ' class="active"';} ?>>Products&nbsp;</a></li>
					<li><a href="/competitions"<?php if ( $post_type == 'gp_competitions' ) {echo ' class="active"';} ?>>Competitions</a></li>
					<li><a href="/people"<?php if ( $post_type == 'gp_people' ) {echo ' class="active"';} ?>>People</a></li>
					<li><a href="/ngo-campaign"<?php if ( $post_type == 'gp_ngocampaign' ) {echo ' class="active"';} ?>>Campaigns</a></li>
				</ul>
			</nav>
			
			<?php 
			#if ( !($current_user instanceof WP_User) || $current_user->ID == 0 ) { 
			if ( !is_user_logged_in() ) {
			?>
			<nav id="header-auth">
				<?php if (!isset($_GET['noscript'])) { ?>
				<ul id="auth-tools">
					<li id="auth-youraccount"> 
						<a href="<?php echo wp_login_url( "http://" . $_SERVER['HTTP_HOST']  . $_SERVER['REQUEST_URI'] ); ?>" class="simplemodal-login">
							<span class="bullet2"></span>Sign in
						</a>
					</li>
				</ul>
				<div class="clear"></div>
				<div id="auth-forgot">
					Don't have an account? <a href="/wp-login.php?action=register" class="simplemodal-register">Sign Up!</a>
				</div>
				<?php } ?>
			</nav>
			<?php 
			} else { 
			?>
			<nav id="header-auth">
				<ul id="auth-tools">
					<li id="auth-yourfavourites" class="no-js">
						<a href="" class="auth-yourfavourites-start" title="Your Favourites">
							<span class="icon-favourites">Your Favourites</span>
						</a>
						<ul id="auth-dash-favourites" class="auth-dash">
							
							<?php
							global $wpdb;
							global $current_site;
							
							$post_author_url = get_author_posts_url($current_user->ID);
							#$querystr = "SELECT REPLACE(meta_key, 'likepost', '') as post_id FROM wp_usermeta WHERE meta_value > 0 and user_id = 5 and meta_key LIKE 'likepost%' order by meta_value DESC limit 5;";
							$querystr = "SELECT " . $wpdb->prefix . "posts.*, m1.meta_value as _thumbnail_id FROM " . $wpdb->prefix . "posts LEFT JOIN " . $wpdb->prefix . "usermeta as m0 on REPLACE(m0.meta_key, 'likepost_'" . $current_site->id . "'_', '')=" . $wpdb->prefix . "posts.ID left join " . $wpdb->prefix . "postmeta as m1 on m1.post_id=" . $wpdb->prefix . "posts.ID and m1.meta_key='_thumbnail_id' WHERE post_status='publish' AND m0.meta_value > 0 AND m0.user_id = $current_user->ID AND m0.meta_key LIKE 'likepost%' AND m1.meta_value >= 1 ORDER BY m0.meta_value DESC LIMIT 5;";
							$pageposts = $wpdb->get_results($querystr, OBJECT);
							$numPosts = $wpdb->num_rows-1;
							
							if ($pageposts && $numPosts != -1) {
								echo '<li class="auth-dash-title">Your Favourites<div class="clear"></div></li>';
								foreach ($pageposts as $post) {
									setup_postdata($post);
									echo '<li>';
									if ( has_post_thumbnail() ) {
										$imageArray = wp_get_attachment_image_src( get_post_thumbnail_id($post->ID), 'dash-thumbnail' );
										$imageURL = $imageArray[0];
										echo '<a href="' . get_permalink($post->ID) . '" title="Permalink to ' . esc_attr(get_the_title()) . '" rel="bookmark"><img src="' . $imageURL  . '" alt="' . get_the_title( get_post_thumbnail_id($post->ID) ) . '" /><span class="what">' . get_the_title() . '</span></a>';
									}
									echo '<div class="clear"></div></li>';
								}
								echo '<li class="auth-dash-seeall"><a href="' . $post_author_url . '#favourites">See all your favourites</a></li>';
							} else {
								echo '<li><div class="account-heart">Love it!</div></li>';
							}
							?>
						</ul>
					</li>
					<li id="auth-yournotifications" class="no-js">
						<a href="" class="auth-yournotifications-start" title="Your Notifications">
							<span class="icon-notifications">Your Notifications</span>
						</a>
						<ul id="auth-dash-notifications" class="auth-dash">
							<li class="auth-dash-title">This feature will be available soon.</li>
						</ul>
					</li>
					<li id="auth-youraccount" class="no-js">
						<a href="" class="auth-youraccount-start">
							<span class="bullet2"></span>
							Your Account
						</a>
						<ul id="auth-dash-account" class="auth-dash">
							<li class="auth-dash-title">Account Options</li>
							<li class="auth-dash-avatar"><a href="<?php echo $post_author_url; ?>"><?php echo get_avatar( $current_user->ID, '50', '', $current_user->display_name ); ?></a></li>
							<li class="auth-account-options">	
								<a href="<?php echo $post_author_url; ?>" title="Your profile">Your Profile</a> 
								<a href="/wp-admin" title="Edit account">Edit Account</a>
								<a href="/about/help" title="Help">Help</a>
								<a href="<?php echo wp_logout_url( "http://" . $_SERVER['HTTP_HOST']  . $_SERVER['REQUEST_URI'] ); ?>" title="Logout">Logout</a>
							</li>
							<!-- 
							<li class="auth-dash-title">Your Toolbox</li>
							<li class="auth-toolbox">
								<a href="">
									<span class="icon-listcheck"></span>
									List your business
									<span class="moreinfo">More info</span>
								</a>
								
								<?php
								# $story_contributors = array('administrator', 'contributor', 'author', 'editor');
								# if ( get_user_role($story_contributors) )  {
								?>
								<a href="">
									<span class="icon-book"></span>
									Submit a story
									<span class="moreinfo">More info</span>
								</a>
								<?php # } else { ?>
								<a href="">
									<span class="icon-book"></span>
									Become a content partner
									<span class="moreinfo">More info</span>
								</a>
								<?php # } ?>
								
								<a href="">
									<span class="icon-star"></span>
									Put GP on my site
									<span class="moreinfo">More info</span>
								</a>
							</li>
							//-->
						</ul>
					</li>
				</ul>
				<div class="clear"></div>
				<div id="auth-forgot">
					<?php echo "Signed in as <a href=\"" . $post_author_url . "\">" . $current_user->display_name ."</a>"; ?>
				</div>
			</nav>
			<?php } ?>
			<?php #theme_location_tag_line(); ?>	
		</header>
