<?php
/*** TEMPLATE ROUTING FUNCTIONS ***/

function get_posttemplate($template='default_index') {
	global $wp_query;
    $current_page_id = $wp_query->get_queried_object_id();
	
	$templates = array('default_index', 'default_single', 'default_page', 'home_index', 'author_index', 'author_edit', 'author_settings', 'author_notifications', 'author_locale', 'author_subscriptions', 'author_privacy', 'search_index', 'events_index', 'competitions_index', 'news_index', 'people_index', 'jobs_index', 'advertorial_index', 'ngocampaign_index', 'katiepatrick_index', 'productreview_index', 'greengurus_index', 'attachment_single');
	
	$templateRoutes = array(
		'home' => 'home_index',								/* Home Page */
		'gp_search' => 'search_index',						/* Search */
		'author' => 'author_index',							/* Author */
		'gp_news' => 'news_index', 							/* News */
		'gp_events' => 'events_index',						/* Events */
		'gp_jobs' => 'jobs_index', 							/* Jobs */
		'gp_people' => 'people_index', 						/* People */
		'gp_competitions' => 'competitions_index',			/* Competitions */
		'gp_advertorial' => 'advertorial_index',			/* Advertorials */
		'gp_ngocampaign' => 'ngocampaign_index',			/* Campaigns */
		'gp_katiepatrick' => 'katiepatrick_index',			/* Katie Patrick Editorial */
		'gp_productreview' => 'productreview_index',		/* Product Reviews */
		'gp_greengurus' => 'greengurus_index',				/* Green Gurus */
		'attachment' => 'attachment_single'					/* Attachment */
	);

		#'author_edit' => 'author_edit',						/* Author Edit */
		#'author_settings' => 'author_settings',				/* Author Edit: Settings */
		#'author_notifications' => 'author_notifications',	/* Author Edit: Notifications */
		#'author_locale' => 'author_locale',					/* Author Edit: Locale */
		#'author_subscriptions' => 'author_subscriptions',	/* Author Edit: Subscriptions */
		#'author_privacy' => 'author_privacy',				/* Author Edit: Privacy */
	
	if ( $template != 'default_index' ) {
		if ( in_array($template, $templates) ) {
			return $template;
		}
	}	

	/* get_post_type() seems to be broken after Wordpress 3.1.3 update
	if ( isset($templateRoutes[get_post_type()]) ) {
		$template = $templateRoutes[get_post_type()];
	}
	*/
	
	$post_type = get_query_var('post_type');
	if (is_string($post_type)) {
		if ( isset($templateRoutes[$post_type]) ) {
			$template = $templateRoutes[$post_type];
		}
	}

	if ( ( (get_post_type() == 'gp_search') || is_search() ) && isset($templateRoutes['gp_search']) ) {
		$template = $templateRoutes['gp_search'];
	}

	if ( is_single($current_page_id) ) {
		if (isset($templateRoutes[$current_page_id]) ) {
			$template = $templateRoutes[$current_page_id];
		} else {
			$template = 'default_single';
		}
	}
	
	if ( is_page($current_page_id) ) {
		if (isset($templateRoutes[$current_page_id]) ) {
			$template = $templateRoutes[$current_page_id];
		} else {
			$template = 'default_page';
		}
	}
	
	if ( is_home() && isset($templateRoutes['home']) ) {
		$template = $templateRoutes['home'];
	}
	
	if ( is_attachment() && isset($templateRoutes['attachment']) ) {
		$template = $templateRoutes['attachment'];
	}
	
	if ( is_author() && isset($templateRoutes['author']) ) {
		if ( get_query_var( 'author_edit' ) ) {
			switch ( get_query_var( 'author_edit' ) ) {
				case 1:
					$template = $templateRoutes['author_edit'];
					break;
				case 2:
					$template = $templateRoutes['author_settings'];
					break;
				case 3:
					$template = $templateRoutes['author_locale'];
					break;
				case 4:
					$template = $templateRoutes['author_notifications'];
					break;
				case 5:
					$template = $templateRoutes['author_subscriptions'];
					break;
				case 6:
					$template = $templateRoutes['author_privacy'];
					break;
				default:
					$template = $templateRoutes['author_edit'];
					break;
			}
		} else {
			$template = $templateRoutes['author'];
		}
	}

	if ( is_category($current_page_id) && isset($templateRoutes[$current_page_id]) ) {
		$template = $templateRoutes[$current_page_id];
	}

	if ( in_array($template, $templates) ) {
		return $template;
	}	
}

$set_template = get_posttemplate();
if ( function_exists($set_template) ) {
	$set_template();
} else {
	echo "error! - couldn't find template";
	/* ERROR! */
}

/*** TEMPLATE COMPONENT FUNCTIONS ***/

/** BUTTONS TO LINK FRONT END TO CREATE NEW POST ADMIN PAGES **/

/*******************************************************************************/	

function theme_singlecreate_post() {
	switch (get_post_type()) {
		case 'gp_news':
			theme_newscreate_post();
			break;
		case 'gp_ngocampaign':
			theme_campaigncreate_post();
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

function theme_homecreate_post(){
	?><div id="post-filter"><span class="right"><?php theme_insert_homecreate_post(); ?></span><div class="clear"></div></div><?php
}

function theme_insert_homecreate_post(){
	echo '<a href="/wp-admin/index.php"><input type="button" value="Create a Post" /></a>';
	#theme_insert_newscreate_post();
	#theme_insert_eventcreate_post();
	#theme_insert_advertorialcreate_post();
	#theme_insert_competitioncreate_post();
	#theme_insert_campaigncreate_post();
}

function theme_newscreate_post(){
	?><div id="post-filter"><span class="right"><?php theme_insert_newscreate_post(); ?></span><div class="clear"></div></div><?php
}

function theme_insert_newscreate_post(){
	// if user is loggin in as a contributor links to create new news page, otherwise links to Content Partner info page
	if ( is_user_logged_in() && get_user_role( array('contributor'), $user->ID ) ) {
		echo '<a href="/wp-admin/post-new.php?post_type=gp_news"><input type="button" value="Post a News Story" /></a>';
	} else {
		echo '<a href="/get-involved/become-a-content-partner/"><input type="button" value="Post a News Story" /></a>';
	}
}

function theme_campaigncreate_post(){
	?><div id="post-filter"><span class="right"><?php theme_insert_campaigncreate_post(); ?></span><div class="clear"></div></div><?php
}

function theme_insert_campaigncreate_post(){
	echo '<a href="/wp-admin/post-new.php?post_type=gp_ngocampaign"><input type="button" value="Post a Campaign" /></a>';
}

function theme_advertorialcreate_post(){
	?><div id="post-filter"><span class="right"><?php theme_insert_advertorialcreate_post(); ?></span><div class="clear"></div></div><?php 
}

function theme_insert_advertorialcreate_post(){
	echo '<a href="/wp-admin/post-new.php?post_type=gp_advertorial"><input type="button" value="Post a Product Ad" /></a>';
}

function theme_competitioncreate_post(){
	?><div id="post-filter"><span class="right"><?php theme_insert_competitioncreate_post(); ?></span><div class="clear"></div></div><?php
}

function theme_insert_competitioncreate_post(){
	echo '<a href="/wp-admin/post-new.php?post_type=gp_competitions"><input type="button" value="Post a Competition" /></a>';
}

function theme_eventcreate_post(){
	?><div id="post-filter"><span class="right"><?php theme_insert_eventcreate_post(); ?></span><div class="clear"></div></div><?php
}

function theme_insert_eventcreate_post(){
	echo '<a href="/wp-admin/post-new.php?post_type=gp_events"><input type="button" value="Post an Event" /></a>';
}

function theme_profilecreate_post(){
	?><div id="post-filter"><span class="right"><?php theme_insert_profilecreate_post(); ?></span><div class="clear"></div></div><?php
}

function theme_insert_profilecreate_post(){
	// if user is logged in link to their own profile back end page, otherwise links to become a member page
	if ( is_user_logged_in() ) {
		echo '<a href="/wp-admin/profile.php"><input type="button" value="Edit My Profile" /></a>';
	} else {
		echo '<a href="/get-involved/become-a-member/"><input type="button" value="Create a Profile" /></a>';
	}	
}

/** PROFILE PAGE ADVERTISER PANEL BUTTONS **/

function theme_insert_advertise_advertorialcreate_post(){
	?><a href="/wp-admin/post-new.php?post_type=gp_advertorial"><input type="button" value="Post a Product $89" /></a><?php 
}

function theme_insert_advertise_competitioncreate_post(){
	?><a href="/wp-admin/post-new.php?post_type=gp_competitions"><input type="button" value="Post a Competition $250" /></a><?php 
}

function theme_insert_listingcreate_new(){
	?><a href="<?php echo get_permalink(472); ?>"><input type="button" value="Directory Page $39/m" /></a><?php 
}

function theme_insert_emailcreate_new(){
	?><a href="mailto:jesse.browne@thegreenpages.com.au?Subject=Exclusive%20Email%20Inquiry" ><input type="button" value="Exclusive Email $3500" /></a><?php 
}

/*******************************************************************************/	

function theme_singletitle() {
	global $wp_query;
	global $post;
	$titleClass = '';
	if ($wp_query->current_post == 0 || $wp_query->current_post == -1) {$titleClass = ' class="loop-title"';}
	echo '<h1' . $titleClass. '><a href="' . get_permalink($post->ID) . '" title="Permalink to ' . esc_attr(get_the_title($post->ID)) . '" rel="bookmark">' . get_the_title($post->ID) . '</a></h1>';
}

function theme_singledetails() {
	global $posts;
	$post_author = get_userdata($posts[0]->post_author);
	$post_author_url = get_author_posts_url($posts[0]->post_author);
	echo '<div class="post-details"><a href="' . $post_author_url . '">' . get_avatar( $post_author->ID, '18', '', $post_author->display_name ) . '</a>Posted by <a href="' . $post_author_url . '">' . $post_author->display_name . '</a> ' . time_ago(get_the_time('U'), 0) . ' ago</div>';
	theme_like();
	echo '<div class="clear"></div>';
}

function theme_singlecontributorstagline() {
	global $posts;
	$post_author = get_userdata($posts[0]->post_author);
	$post_author_url = get_author_posts_url($posts[0]->post_author);
	$post_author_tagline = get_the_author_meta( 'contributors_posttagline', $post_author->ID );
	if ( !empty($post_author_tagline) ) {
		echo '<div class="post-authorsdisclaimer"><a href="' . $post_author_url . '">' . get_avatar( $post_author->ID, '50', '', $post_author->display_name ) . '</a><div class="post-authorsdisclaimer-details">Posted by <a href="' . $post_author_url . '">' . $post_author->display_name . '</a> ' . time_ago(get_the_time('U'), 1) . ' ago</div><div class="post-authorsdisclaimer-content">' . $post_author_tagline . '</div><div class="clear"></div></div>';
	} else {
		theme_singledetails();
	}
}

function theme_singlepagination() {
	/* NOT USED YET! */
	/* wp_link_pages( array( 'before' => '<div class="page-link">' . __( 'Pages:', 'twentyten' ), 'after' => '</div>' ) ); */
}

function theme_singlesocialbar() {
	if (get_post_type() != "page") { 
		global $post;
		/*echo '
			<div class="post-socialnav">
				<div class="post-google">
					<g:plusone size="medium" href="' . urlencode(get_permalink($post->ID)) . '"></g:plusone>
				</div>
				<div class="post-twitter">
					<a href="http://twitter.com/share" class="twitter-share-button" data-url="' . get_permalink($post->ID) . '" data-text="' . esc_attr(get_the_title($post->ID)) . '" data-count="horizontal" data-via="GreenPagesAu">Tweet</a>
				</div>
				<script type="text/javascript" src="http://platform.twitter.com/widgets.js"></script>
				<div class="post-facebook">
					<iframe src="http://www.facebook.com/plugins/like.php?href=' . urlencode(get_permalink($post->ID)) . '&amp;layout=button_count&amp;show_faces=false&amp;action=like&amp;font=arial&amp;colorscheme=light" scrolling="no" frameborder="0" style="border:none; overflow:hidden;" allowTransparency="true"></iframe>
				</div>';
		if ( comments_open() && !is_attachment() ) {
			echo '
				<a href="' . get_permalink($post->ID) . '#comments" class="post-disqus">
					<div class="comment-background">
						<span class="comment-number dsq-postid"><fb:comments-count href="' . get_permalink($post->ID) . '"></fb:comments-count></span>
					</div>
				</a>
				<div class="comment-leftcap"></div>
				<div class="comment-rightcap"></div>';
		}
		
		echo '
				<div class="clear"></div>
			</div>';*/
		
		echo '
		<div id="gp_share">
		    <div id="gp_sharebar">
		    	<div id="gp_sharebox">
			    	<div class="wdt title">Share</div>
			        <div class="wdt twitter">
			            <a href="http://twitter.com/share" class="twitter-share-button" data-url="' . get_permalink($post->ID) . '" data-text="' . esc_attr(get_the_title($post->ID)) . '"  data-count="vertical" data-via="GreenPagesAu">Tweet</a>
			            <script type="text/javascript" src="http://platform.twitter.com/widgets.js"></script>
			        </div>
			        <div class="wdt google-plus">
			            <g:plusone size="tall" href="' . get_permalink($post->ID) . '"></g:plusone>
			            <script type="text/javascript">
			                (function() {
			                var po = document.createElement(\'script\'); po.type = \'text/javascript\'; po.async = true;
			                po.src = \'https://apis.google.com/js/plusone.js\';
			                var s = document.getElementsByTagName(\'script\')[0]; s.parentNode.insertBefore(po, s);
			                })();
			            </script>
			        </div>
			        <div class="wdt linkedin">
			        	<script src="http://platform.linkedin.com/in.js" type="text/javascript"></script>
						<script type="IN/Share" data-url="' . get_permalink($post->ID) . '" data-counter="top"></script>
			        </div>
			        <div class="wdt facebook">
			            <div class="fb-like" data-href="' . get_permalink($post->ID) . '" data-send="true" data-layout="box_count"></div>
			        </div>
			        <div class="clear"></div>
		        </div>';
				/*if ( comments_open() ) {
			        echo '<div id="gp_commentbox">
			    		<div class="wdt title">Comments</div>
			    		<div class="clear"></div>
			    		<div class="commentcount"><a href="#comments"><span class="comment-mini"></span><fb:comments-count href="' . get_permalink($post->ID) . '"></fb:comments-count></a></div>
			    		<div class="clear"></div>
			    	</div>';
				}*/
		    echo '</div>
		</div>
		';
	}
}

function theme_singlecomments() {
	if ( comments_open() ) {
		echo '<a name="comments"></a>';
		#comments_template( '', true );
		?>
		<div id="facebook-comments">
			<h3 id="reply-title">Leave a Reply</h3>
			<fb:comments href="<?php the_permalink(); ?>" num_posts="10" width="636"></fb:comments>
		</div>
		<?php
	}
}

function theme_indextitle() {
	theme_singletitle();
}

function theme_indexdetails($format='full') {
	global $post;
	$post_author = get_userdata($post->post_author);
	$post_author_url = get_author_posts_url($post->post_author);
	if ($format == 'full') {
		echo '<div class="post-details"><a href="' . $post_author_url . '">' . get_avatar( $post_author->ID, '18', '', $post_author->display_name ) . '</a>Posted by <a href="' . $post_author_url . '">' . $post_author->display_name . '</a> ' . time_ago(get_the_time('U'), 0) . ' ago</div>';
	}
	
	if ($format == 'author') {
		echo '<div class="post-details"><a href="' . $post_author_url . '">' . get_avatar( $post_author->ID, '18', '', $post_author->display_name ) . '</a>Posted by <a href="' . $post_author_url . '">' . $post_author->display_name . '</a> ' . time_ago(get_the_time('U'), 0) . ' ago</div>';
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
	global $post;
	global $current_user, $current_site;
	
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
	
	if ( comments_open($post->ID) ) {
		echo '<div class="comment-profile"><a href="#comments"><span class="comment-mini"></span><span class="comment-mini-number dsq-postid"><fb:comments-count href="' . get_permalink($post->ID) . '"></fb:comments-count></span></a></div>';
	}
	
	if ( is_single() ) {
		#echo '<div id="post-' . $post->ID . '" class="like-button"><a href="#" class="like_heart' . $likedclass . '">Favorite Me!</a></div>';
		if (is_user_logged_in()) {
			echo '<div id="post-' . $post->ID . '" class="favourite-profile"><a href="#/"><span class="star-mini' . $likedclass . '"></span><span class="star-mini-number"' . $showlikecount . '>' . $likecount . '</span><span class="star-mini-number-plus-one" style="display:none;">+1</span><span class="star-mini-number-minus-one" style="display:none;">-1</span></a></div>';
		} else {
			echo '<div id="post-' . $post->ID . '" class="favourite-profile"><a href="' . wp_login_url( "http://" . $_SERVER['HTTP_HOST']  . $_SERVER['REQUEST_URI'] ) . '" class="simplemodal-login"><span class="star-mini"></span><span class="star-mini-number"' . $showlikecount . '>' . $likecount . '</span><span class="star-login" style="display:none;">Login...</a></a></div>';
		}
	}
}

/** NEW INDEX FEED STYLE **/

function theme_index_feed_item() {
	global $post;
	
	echo '<div class="profile-postbox">';
			?><h1><a href="<?php the_permalink(); ?>"  title="Permalink to <?php esc_attr(the_title()); ?>" rel="bookmark"><?php the_title(); ?></a></h1><?php 		

			$post_author = get_userdata($post->post_author);
			$post_author_url = get_author_posts_url($post->post_author);
			
			/** CHECK POST TYPE AND ASSIGN APPROPRIATE TITLE AND URL **/
			switch (get_post_type()) {
			    case 'gp_news':
			        $post_title = 'News';
			        $post_url = '/news';
			        break;
			    case 'gp_ngocampaign':
			    	$post_title = 'Campaigns';
			    	$post_url = '/ngo-campaign';
			        break;
				case 'gp_advertorial':
					$post_title = 'Products';
					$post_url = '/eco-friendly-products';
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
			
			/** DISPLAY POST AUTHOR, CATEGORY AND TIME POSTED DETAILS **/
			echo '<span class="hp_miniauthor"><a href="' . $post_author_url . '">' . 
					get_avatar( $post_author->ID, '18', '', $post_author->display_name ) . 
					'</a>Posted by <a href="' . $post_author_url . '">' . $post_author->display_name . '</a> in <a href="' . $post_url . '">' . $post_title . '</a> ' . time_ago(get_the_time('U'), 0) . ' ago</span>';
			the_excerpt();			
			echo '<a href="' . get_permalink($post->ID) . '" class="profile_postlink">Continue Reading...</a>';
			
			if ( comments_open($post->ID) ) {
				echo '<div class="comment-profile"><a href="' . get_permalink($post->ID) . '#comments"><span class="comment-mini"></span><span class="comment-mini-number dsq-postid"><fb:comments-count href="' . get_permalink($post->ID) . '"></fb:comments-count></span></a></div>';
			}
			
			global $current_user, $current_site;
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
		echo '</div>';

	/** DISPLAY FEATURED IMAGE IF SET **/		
	if ( has_post_thumbnail() ) {
		$imageArray = wp_get_attachment_image_src( get_post_thumbnail_id($post->ID), 'homepage-thumbnail' );
		$imageURL = $imageArray[0];
		echo '<a href="' . get_permalink($post->ID) . '" class="profile_minithumb"><img src="' . $imageURL  . '" alt="' . get_the_title( get_post_thumbnail_id($post->ID) ) . '" /></a>';
	}

	echo '<div class="clear"></div>';
}

/*** TEMPLATE RENDERING ***/

function default_index() {
	if ( have_posts() ) {
		while ( have_posts() ) { 
			the_post();
			theme_index_feed_item();
			#theme_indextitle();
			#theme_indexdetails();
		    #the_content('Continue reading...');
		    #theme_indexsocialbar();    
	    }
	    theme_indexpagination();	
	} else {
		echo '<h1 class="loop-title">We couldn\'t find what you were look for!</h1>
			<p>No there\'s nothing wrong. It just means there\'s no posts for this section yet! Which is admittedly a little strange but if you\'d like to help and write for us (In a volunteer capacity at this stage.) send us a email to info[at]thegreenpages.com.au and we\'ll be in touch.</p>';
	}
}

function default_page() {
	if ( have_posts() ) { 
		the_post();
		echo '<article>';
			theme_singletitle();
			the_content();
			theme_singlepagination();
		echo '</article>';
	}
}

function default_single() {
	if ( have_posts() ) { 
		the_post();
		echo '<article>';
			theme_singlecreate_post();
			theme_singletitle();
			theme_singlesocialbar();
			if ( get_user_role( array('contributor'), $posts[0]->post_author) ) {
				theme_singlecontributorstagline();
			} else {
				theme_singledetails();
			}
			the_content();
			theme_singlepagination();
			theme_single_contributor_donate_join_bar();
			theme_single_product_button();
			theme_singlecomments();
		echo '</article>';
	}
}

function attachment_single() {
	if ( have_posts() ) { 
		the_post();
		echo '<article>';
			theme_singletitle();
			theme_singlesocialbar();
			theme_singledetails();
			the_content();
		echo '</article>';
	}
}

/** HOMEPAGE LIST VIEW OF 20 MOST RECENT POSTS **/
function home_index() {
	global $wpdb;
	global $post;
	
	$epochtime = strtotime('now');
	
	/** NEW SQL QUERIES SHOW LIST VIEW OF 20 MOST RECENT POSTS **/
	$qrystart = "SELECT " . $wpdb->prefix . "posts.*, m0.meta_value as _thumbnail_id,m1.meta_value as gp_enddate,m2.meta_value as gp_startdate 
				FROM " . $wpdb->prefix . "posts left join " . 
						 $wpdb->prefix . "postmeta as m0 on m0.post_id=" . 
						 $wpdb->prefix . "posts.ID and m0.meta_key='_thumbnail_id' left join " . 
						 $wpdb->prefix . "postmeta as m1 on m1.post_id=" . 
						 $wpdb->prefix . "posts.ID and (m1.meta_key='gp_events_enddate' or m1.meta_key='gp_competitions_enddate') left join " . 
						 $wpdb->prefix . "postmeta as m2 on m2.post_id=" . 
						 $wpdb->prefix . "posts.ID and (m2.meta_key='gp_events_startdate' or m2.meta_key='gp_competitions_startdate') 
				WHERE post_status='publish' AND m0.meta_value >= 1 AND ";
	$querystr = "(" . $qrystart ." post_type='gp_news' AND post_status='publish' OR 
						post_type='gp_advertorial' AND post_status='publish' OR 
						post_type='gp_competitions' AND post_status='publish' OR 
						post_type='gp_ngocampaign' AND post_status='publish' 
						ORDER BY post_date DESC LIMIT 20)";

	$pageposts = $wpdb->get_results($querystr, OBJECT);
	#$numPosts = $wpdb->num_rows-1;
	
	/** NEW LIST VIEW OF 20 MOST RECENT POSTS **/
	if ($pageposts) {
		
		theme_homecreate_post();							# DISPLAY CREATE NEW POST BUTTON

		foreach ($pageposts as $post) {						# DISPLAY MOST RECENT POSTS 
			setup_postdata($post);
			if (get_post_type() != 'gp_ngocampaign') {
				theme_index_feed_item();					# DISPLAY INDIVIDUAL POST TITLE, IMAGE, EXCERPT AND LINK 
			} 
			else {
				theme_index_feed_item();		 
				theme_index_contributor_donate_join_bar();	# CAMPAIGNS ALSO DISPLAY DONATE JOIN BUTTONS
			}
		}
	}														# THAT'S IT!
	
	/** OLD SQL QUERIES SHOW 3 MOST RECENT POSTS FROM EACH CATEGORY **/
	#$qrystart = "SELECT " . $wpdb->prefix . "posts.*, m0.meta_value as _thumbnail_id,m1.meta_value as gp_enddate,m2.meta_value as gp_startdate FROM " . $wpdb->prefix . "posts left join " . $wpdb->prefix . "postmeta as m0 on m0.post_id=" . $wpdb->prefix . "posts.ID and m0.meta_key='_thumbnail_id' left join " . $wpdb->prefix . "postmeta as m1 on m1.post_id=" . $wpdb->prefix . "posts.ID and (m1.meta_key='gp_events_enddate' or m1.meta_key='gp_competitions_enddate') left join " . $wpdb->prefix . "postmeta as m2 on m2.post_id=" . $wpdb->prefix . "posts.ID and (m2.meta_key='gp_events_startdate' or m2.meta_key='gp_competitions_startdate') WHERE post_status='publish' AND m0.meta_value >= 1 AND ";
	#$querystr = "(" . $qrystart ." post_type='gp_news' ORDER BY post_date DESC LIMIT 4)";
	#$querystr .= " union (" . $qrystart . " post_type='gp_events' and CAST(CAST(m1.meta_value AS UNSIGNED) AS SIGNED) >= " . $epochtime . " ORDER BY gp_startdate ASC LIMIT 3)";
	#$querystr .= " union (" . $qrystart . " post_type='gp_advertorial' ORDER BY post_date DESC LIMIT 3)";
	#$querystr .= " union (" . $qrystart . " post_type='gp_people' ORDER BY post_date DESC LIMIT 3)";
	#$querystr .= " union (" . $qrystart . " post_type='gp_competitions' and CAST(CAST(m2.meta_value AS UNSIGNED) AS SIGNED) <= " . $epochtime . " and CAST(CAST(m1.meta_value AS UNSIGNED) AS SIGNED) >= " . $epochtime . " ORDER BY gp_enddate ASC LIMIT 3)";
	#$querystr .= " union (" . $qrystart . " post_type='gp_ngocampaign' ORDER BY post_date DESC LIMIT 3)";
	
	
	/** OLD LIST VIEW OF 3 MOST RECENT POSTS FROM EACH CATEGORY **/
	#if ($pageposts) {
	#	$counterA = -1;
	#	$counterB = -2;
	#	$counterC = -1;
	#	foreach ($pageposts as $post) {
	#		setup_postdata($post);
			
	#		if ($counterA == -1) {
	#			$counterA++;
				#echo '<div class="hp_featured">';
				#theme_indextitle();
				#theme_indexdetails();
		    	#the_content('Continue reading...');
		    	#theme_indexsocialbar();
				#echo '<div class="clear"></div></div>';				
				
	#			theme_index_feed_item();
	#			theme_homecreate_post();
				
	#		} else {
				
				#if ($counterA == 0) {				
					#theme_index_feed_item();
				#}
				
	#			$counterC++;
	#			if($counterC % 3 == 0) {
	#				switch (get_post_type()) {
					    #case 'gp_news':
					    #   echo '<span class="hp_minitype"><a href="/news">News</a>:</span>';
					    #   break;
	#				    case 'gp_ngocampaign':
	#				        echo '<span class="hp_minitype"><a href="/ngo-campaign">Campaigns</a>:</span>';
	#				        break;
	#					case 'gp_advertorial':
	#				        echo '<span class="hp_minitype"><a href="/eco-friendly-products">Products</a>:</span>';
	#				        break;
						#case 'gp_competitions':
					    #   echo '<span class="hp_minitype"><a href="/competitions">Competitions</a>:</span>';
					    #   break;
	#				    case 'gp_events':
	#				        echo '<span class="hp_minitype"><a href="/events">Events</a>:</span>';
	#				        break;
	#				    case 'gp_people':
	#				        echo '<span class="hp_minitype"><a href="/people">People</a>:</span>';
	#				        break;
	#				}
	#			}
				
	#			$counterB++;				

	#			if ($counterA < 12) {       		 
	#				theme_index_feed_item();		# DISPLAY POSTS
	#			}
	#			else {
	#				theme_index_feed_item();		# CAMPAIGNS ALSO DISPLAY DONATE JOIN BUTTONS 
	#				theme_index_contributor_donate_join_bar();
	#			}
				
	#			$counterA++;						# Delete or comment out these four lines if code block below is ever uncommented otherwise will be duplicated 
	#			if($counterA % 3 == 0) {
	#				echo '<div class="clear"></div>';
	#			}

/**				COMPETITION POSTS - HAVE COMMENTED OUT AS WE DON'T ALWAYS HAVE 3 COMPETITIONS RUNNING AND OFTEN THROWS HOMEPAGE OUT OF WHACK		
				if ( has_post_thumbnail() ) { # we're doing this check twice and we don't have too?
					$imageArray = wp_get_attachment_image_src( get_post_thumbnail_id($post->ID), 'homepage-thumbnail' );
					$imageURL = $imageArray[0];
					echo '<a href="' . get_permalink($post->ID) . '" class="hp_minithumb"><img src="' . $imageURL  . '" alt="' . get_the_title( get_post_thumbnail_id($post->ID) ) . '" /></a>';
				}
				
				$post_author = get_userdata($post->post_author);
				$post_author_url = get_author_posts_url($post->post_author);
				echo '<span class="hp_miniauthor"><a href="' . $post_author_url . '">' . get_avatar( $post_author->ID, '18', '', $post_author->display_name ) . '</a>By <a href="' . $post_author_url . '">' . $post_author->display_name . '</a></span>';
				
				?>
				<!-- <span class="hp_miniauthor">By <?php #the_author_posts_link() ?></span> //-->
				<h1><a href="<?php the_permalink(); ?>" title="Permalink to <?php esc_attr(the_title()); ?>" rel="bookmark"><?php the_title(); ?></a></h1>
				
				<?php 

				
				if ( get_post_type() == 'gp_competitions' ) {
					$epochtime = strtotime('now');
					$competitions_enddate = $post->gp_enddate;
					$competitions_enddate_diff = _date_diff((int)$competitions_enddate, $epochtime);
					$hplinkclass = 'hp_link';
					if (date('Y', $epochtime) - $competitions_enddate_diff['y'] != 1970) {
					if ($competitions_enddate_diff['h'] <= 0 && $competitions_enddate_diff['i'] > 0) {
						if ($competitions_enddate_diff['i'] > 1) {$plural = 's';} else {$plural = '';}
							$competitions_enddate = $competitions_enddate_diff['i'] . ' minute' . $plural . ' to go...';
							$hplinkclass = 'hp_linkcompetitions';
						}
						
						if ($competitions_enddate_diff['d'] <= 0 && $competitions_enddate_diff['h'] > 0) {
							if ($competitions_enddate_diff['h'] > 1) {$plural = 's';} else {$plural = '';}
							$competitions_enddate = $competitions_enddate_diff['h'] . ' hour' . $plural . ' left...';
							$hplinkclass = 'hp_linkcompetitions';
						}
						
						if ($competitions_enddate_diff['m'] <= 0 && $competitions_enddate_diff['d'] > 0) {
							if ($competitions_enddate_diff['d'] > 1) {$plural = 's';} else {$plural = '';}
							$competitions_enddate = $competitions_enddate_diff['d'] . ' day' . $plural . ' left...';
							$hplinkclass = 'hp_linkcompetitions';
							
						}
						 
						if ($competitions_enddate_diff['y'] <= 0 && $competitions_enddate_diff['m'] > 0) {
							if ($competitions_enddate_diff['m'] > 1) {$plural = 's';} else {$plural = '';}
							$competitions_enddate = $competitions_enddate_diff['m'] . ' month' . $plural . ' left...';
							$hplinkclass = 'hp_linkcompetitions';
						}
						
						if ($competitions_enddate_diff['y'] > 0) {
							$competitions_enddate = 'Read more...';
						}
					} else {
						$competitions_enddate = 'Read more...';
					} 
				?>
					<a href="<?php the_permalink(); ?>" class="hp_linkcompetitions"><?php echo $competitions_enddate; ?></a>
				<?php } else {?>
					<a href="<?php the_permalink(); ?>" class="hp_link">Read more...</a>
				<?php } ?>
				
				
				<?php if ( comments_open() ) { ?>
					<div class="comment-hp"><a href="<?php the_permalink(); ?>#comments"><span class="comment-mini"></span></a><a href="<?php the_permalink(); ?>#disqus_thread" class="comment-hp"><span class="comment-mini-number dsq-postid"><fb:comments-count href="<?php the_permalink(); ?>"></fb:comments-count></span></a></div>
				<?php
				}
				echo '</div>';

				
				$counterA++;
				if($counterA % 3 == 0) {
					echo '<div class="clear"></div>';
				}

				#if ($counterA == $numPosts) {
				#	echo '</div>';
				#}				
**/				
	#		}
	#	}
	#}
}

function search_index() {
	default_index();
}

function news_index() {
	theme_newscreate_post();
	default_index();
}

function events_index() {
	global $wpdb, $post, $states_au;
	
	$epochtime = strtotime('now');
    if ( in_array(get_query_var( 'filterby_state' ), $states_au) ) {
		$filterby_state = "AND m3.meta_value='" . get_query_var( 'filterby_state' ) . "'";
    }

    $querytotal = "SELECT COUNT(*) as count FROM $wpdb->posts left join " . $wpdb->prefix . "postmeta as m0 on m0.post_id=" . $wpdb->prefix . "posts.ID and m0.meta_key='_thumbnail_id' left join " . $wpdb->prefix . "postmeta as m1 on m1.post_id=" . $wpdb->prefix . "posts.ID and m1.meta_key='gp_events_enddate' left join " . $wpdb->prefix . "postmeta as m2 on m2.post_id=" . $wpdb->prefix . "posts.ID and m2.meta_key='gp_events_startdate' left join " . $wpdb->prefix . "postmeta as m4 on m4.post_id=" . $wpdb->prefix . "posts.ID and m4.meta_key='gp_events_loccountry' left join " . $wpdb->prefix . "postmeta as m3 on m3.post_id=" . $wpdb->prefix . "posts.ID and m3.meta_key='gp_events_locstate' WHERE post_status='publish' AND post_type='gp_events' AND m4.meta_value='AU' " . $filterby_state . " AND CAST(CAST(m1.meta_value AS UNSIGNED) AS SIGNED) >= " . $epochtime . ";";
	$totalposts = $wpdb->get_results($querytotal, OBJECT);

	#$ppp = intval(get_query_var('posts_per_page'));
	$ppp = 20;
	
	$wp_query->found_posts = $totalposts[0]->count;
	$wp_query->max_num_pages = ceil($wp_query->found_posts / $ppp);	
	$on_page = intval(get_query_var('paged'));	

	if($on_page == 0){ $on_page = 1; }		
	$offset = ($on_page-1) * $ppp;
	
    $metas = array('_thumbnail_id', 'gp_events_enddate', 'gp_events_startdate', 'gp_events_locstate', 'gp_events_locsuburb', 'gp_events_loccountry');
	foreach ($metas as $i=>$meta_key) {
        $meta_fields[] = 'm' . $i . '.meta_value as ' . $meta_key;
        $meta_joins[] = ' left join ' . $wpdb->postmeta . ' as m' . $i . ' on m' . $i . '.post_id=' . $wpdb->posts . '.ID and m' . $i . '.meta_key="' . $meta_key . '"';
    }
    $querystr = "SELECT " . $wpdb->prefix . "posts.*, " .  join(',', $meta_fields) . " FROM $wpdb->posts ";
    $querystr .=  join(' ', $meta_joins);
    $querystr .= " WHERE post_status='publish' AND post_type='gp_events' AND m5.meta_value='AU' " . $filterby_state . " AND CAST(CAST(m1.meta_value AS UNSIGNED) AS SIGNED) >= " . $epochtime . " ORDER BY gp_events_startdate ASC LIMIT $ppp OFFSET $offset;";

	$pageposts = $wpdb->get_results($querystr, OBJECT);

	#please fix this and make it accessable to non js users
	?><div id="post-filter"><span class="right"> <?php theme_insert_eventcreate_post(); ?> </span><span class="right">Filter by State:&nbsp;&nbsp;<select name="filterby_state" id="filterby_state"><option value="/events">All States</option><?php 
	foreach ($states_au as $state) {
		if ($state == get_query_var( 'filterby_state' )) {$state_selected = ' selected';} else {$state_selected = '';}
  		echo '<option value="/events/AU/' . $state . '"' . $state_selected . '>' . $state . '</option>';
	}									
	?></select></span><div class="clear"></div></div><?php 

	if ($pageposts) {
		foreach ($pageposts as $post) {
			setup_postdata($post);
			
			$displayday = date('j', $post->gp_events_startdate);
			$displaymonth = date('M', $post->gp_events_startdate);
			$displayyear = date('y', $post->gp_events_startdate);
			echo '<div class="event-archive-item">';
			#$displaydate = get_absolutedate( $post->gp_events_startdate, $post->gp_events_enddate, 'jS F Y', '', true, true );
			#if ( $displayyear ) {
				#echo '<div class="post-mini-calendar"><img src="' . get_bloginfo('template_url') . '/template/famfamfam_silk_icons_v013/icons/calendar.png" />' . $displaydate . '<span>' . $post->gp_events_locsuburb . ' | <a href="/events/AU/' . $post->gp_events_locstate . '">' . $post->gp_events_locstate . '</a></span></div>';
				#echo '<img src="' . get_bloginfo('template_url') . '/template/events-calendar-icon.gif" />';
				if (date('Y', $post->gp_events_startdate) == date('Y')) {
					echo '<a href="' . get_permalink($post->ID) . '" class="post-events-calendar"><span class="post-month">' . $displaymonth . '</span><span class="post-day">' . $displayday . '</span></a>';
				} else {
					echo '<a href="' . get_permalink($post->ID) . '" class="post-events-calendar"><span class="post-day">' . $displayyear . '\'</span></a>';
				}
			#}
			#theme_indextitle();
			echo '<h1><a href="' . get_permalink($post->ID) . '" title="Permalink to ' . esc_attr(get_the_title($post->ID)) . '" rel="bookmark">' . get_the_title($post->ID) . '</a></h1>';
			echo '<a href="' . get_permalink($post->ID) . '" class="more-link">Continue reading...</a><div>';
			theme_indexdetails('author');
			echo '<div class="post-loc">' . $post->gp_events_locsuburb . ' | <a href="/events/AU/' . $post->gp_events_locstate . '">' . $post->gp_events_locstate . '</a></div><div class="clear"></div></div>';
			#the_content('Continue reading...');
			echo '</div><div class="clear"></div>';
		    #theme_indexsocialbar();
		}
		if (  $wp_query->max_num_pages > 1 ) { # We don't use theme_pagination() here - this is a fix  ?>
			<nav id="post-nav">
				<ul>
					<li class="post-previous"><?php next_posts_link('<div class="arrow-previous"></div>Later in Time', $wp_query->max_num_pages); ?></li>
					<li class="post-next"><?php previous_posts_link('Sooner in Time<div class="arrow-next"></div>', $wp_query->max_num_pages-1); ?></li>
				</ul>
			</nav>
		<?php
		}
	}   
}

function jobs_index() {
	default_index();
}

function competitions_index() {
	global $wpdb;
	global $post;
	
	theme_competitioncreate_post();
		
	$epochtime = strtotime('now');
    
    $querytotal = "SELECT COUNT(*) as count FROM $wpdb->posts left join " . $wpdb->prefix . "postmeta as m0 on m0.post_id=" . $wpdb->prefix . "posts.ID and m0.meta_key='_thumbnail_id' left join " . $wpdb->prefix . "postmeta as m1 on m1.post_id=" . $wpdb->prefix . "posts.ID and m1.meta_key='gp_competitions_enddate' left join " . $wpdb->prefix . "postmeta as m2 on m2.post_id=" . $wpdb->prefix . "posts.ID and m2.meta_key='gp_competitions_startdate' WHERE post_status='publish' AND post_type='gp_competitions' and CAST(CAST(m2.meta_value AS UNSIGNED) AS SIGNED) <= " . $epochtime . " and CAST(CAST(m1.meta_value AS UNSIGNED) AS SIGNED) >= " . $epochtime . ";";
	$totalposts = $wpdb->get_results($querytotal, OBJECT);

	$ppp = intval(get_query_var('posts_per_page'));

	$wp_query->found_posts = $totalposts[0]->count;
	$wp_query->max_num_pages = ceil($wp_query->found_posts / $ppp);	
	$on_page = intval(get_query_var('paged'));	

	if($on_page == 0){ $on_page = 1; }		
	$offset = ($on_page-1) * $ppp;
	
    $metas = array('_thumbnail_id', 'gp_competitions_enddate', 'gp_competitions_startdate');
	foreach ($metas as $i=>$meta_key) {
        $meta_fields[] = 'm' . $i . '.meta_value as ' . $meta_key;
        $meta_joins[] = ' left join ' . $wpdb->postmeta . ' as m' . $i . ' on m' . $i . '.post_id=' . $wpdb->posts . '.ID and m' . $i . '.meta_key="' . $meta_key . '"';
    }
    $querystr = "SELECT " . $wpdb->prefix . "posts.*, " .  join(',', $meta_fields) . " FROM $wpdb->posts ";
    $querystr .=  join(' ', $meta_joins);
    $querystr .= " WHERE post_status='publish' AND post_type='gp_competitions' and CAST(CAST(m2.meta_value AS UNSIGNED) AS SIGNED) <= " . $epochtime . " and CAST(CAST(m1.meta_value AS UNSIGNED) AS SIGNED) >= " . $epochtime . " ORDER BY gp_competitions_enddate ASC LIMIT $ppp OFFSET $offset;";

	$pageposts = $wpdb->get_results($querystr, OBJECT);

	if ($pageposts) {
		foreach ($pageposts as $post) {
			setup_postdata($post);
			$displaydate = get_competitiondate( strtotime('now'), $post->gp_competitions_enddate );
			theme_index_feed_item();
			echo $displaydate.'<div class="clear"></div>';
			#theme_indextitle();
			#theme_indexdetails();
			#if ( !$displaydate ) {
			#	the_content('Continue reading...');
			#} else {
			#	the_content($displaydate);
			#}
		    #theme_indexsocialbar();
		}
		if (  $wp_query->max_num_pages > 1 ) { # We don't use theme_pagination() here - this is a fix  ?>
			<nav id="post-nav">
				<ul>
					<li class="post-previous"><?php next_posts_link('<div class="arrow-previous"></div>Recent Posts', $wp_query->max_num_pages); ?></li>
					<li class="post-next"><?php previous_posts_link('Closing Soon<div class="arrow-next"></div>', $wp_query->max_num_pages-1); ?></li>
				</ul>
			</nav>
		<?php
		}
	}
}

function people_index() {
	theme_profilecreate_post();
	#default_index();
	
	# LIST VIEW OF MEMBERS WITH SUBSCRIBER STATUS SHOWING DISPLAY NAME, JOB TITLE, EMPLOYER AND FIRST FEW WORDS OF PROJECTS I NEED HELP WITH
	
	global $current_user, $wpdb, $wp_roles;

	$query = "SELECT wp_users.ID FROM wp_users LEFT JOIN wp_usermeta on wp_usermeta.user_id=wp_users.ID WHERE wp_users.user_status = 0 AND wp_usermeta.meta_key = 'wp_capabilities' AND wp_usermeta.meta_value RLIKE '[[:<:]]subscriber[[:>:]]' ORDER BY wp_users.user_registered DESC;";
  	$subscribers = $wpdb->get_results($query);
  	if ($subscribers) {
  		$member_string .= '<div class="memberslist">';
  		
    	foreach($subscribers as $subscriber) {
      		$thisuser = get_userdata($subscriber->ID);
      		$member_string .= '<a href="' . get_author_posts_url($thisuser->ID) . '" title="Posts by "' . esc_attr($thisuser->display_name) . '">'; 
      		$member_string .= get_avatar( $thisuser->ID, '100', '', $thisuser->display_name );
      		$member_string .= '<span><div><h1>' . $thisuser->display_name .'</h1></div>';
      		$member_string .= '<div>' . $thisuser->employment_jobtitle . '</div>';
      		$member_string .= '<div>' . $thisuser->employment_currentemployer . '</div>';
      		$member_string .= insert_memberslist_projects_excerpt($thisuser);
      		$member_string .= '</span></a>';
    	}
   	
    $member_string .= '</div><div class="clear"></div>';
   	echo $member_string;	
  	}
}

function advertorial_index() {
	theme_advertorialcreate_post();
	default_index();
}

function ngocampaign_index() {
	theme_campaigncreate_post();
	if ( have_posts() ) {
		while ( have_posts() ) { 
			the_post(); 
			theme_index_feed_item();
			theme_index_contributor_donate_join_bar();
	    }
	    theme_indexpagination();	
	} else {
		echo '<h1 class="loop-title">We couldn\'t find what you were look for!</h1>
			<p>No there\'s nothing wrong. It just means there\'s no posts for this section yet! Which is admittedly a little strange but if you\'d like to help and write for us (In a volunteer capacity at this stage.) send us a email to info[at]thegreenpages.com.au and we\'ll be in touch.</p>';
	}
}


/*** PROFILES ***/

function get_profile_author() {
	return (get_query_var('author_name')) ? get_user_by('slug', get_query_var('author_name')) : get_userdata(get_query_var('author'));
}

/* Route profiles by user role or profile type */
function author_index() {
	$profile_author = get_profile_author();
	
	/* list of allowed profile types - should place this globally somewhere! */
	$authorprofiles = array('administrator', 'editor', 'contributor', 'subscriber');
	
	/* Users selected 'profiletypes' will override users actual 'wp_capabilities'. */
	$profiletypes_user = get_the_author_meta( 'profiletypes', $profile_author->ID );

	if ( is_array( $profiletypes_user ) && in_array($profiletypes_user['profiletypes'], $authorprofiles) ) {
		if ($profiletypes_user['profiletypes'] == 'administrator') {administrator_index($profile_author);}
		if ($profiletypes_user['profiletypes'] == 'contributor') {contributor_index($profile_author);}
		if ($profiletypes_user['profiletypes'] == 'editor') {editor_index($profile_author);}
		if ($profiletypes_user['profiletypes'] == 'subscriber') {subscriber_index($profile_author);}
	} else {
		if ( get_user_role( array('administrator'), $profile_author->ID ) ) {
			administrator_index();
		} else if ( get_user_role( array('contributor', 'author', 'editor'), $profile_author->ID ) ) {
			editor_index($profile_author);
		} else {
			subscriber_index($profile_author);
		}	
	}
}

function author_edit() {
	global $current_user;
	$user_id = $current_user->data->ID;
	$user_roles = $current_user->roles;
	$user_role = array_shift($user_roles);
	$profiletypes_user = get_the_author_meta( 'profiletypes', $user->ID );
	$profiletypes_values = array('administrator', 'editor', 'contributor', 'subscriber');
	
	$profile_author = get_profile_author();
	
	#if ( !is_user_logged_in() || ( is_user_logged_in() && $user_id != $profile_author->ID) ) {
		#wp_safe_redirect('/profile/' . $profile_author->user_nicename);
		#return false;
	#}
	
	theme_authoreditnav();
	
	$rolesubscriber = 'subscriber';
	$roleauthor = 'author';
	$roleeditor = 'editor';
	$rolecontributor = 'contributor';
	
	if ( !get_user_role( array( $profiletypes_user['profiletypes'] ) ) && in_array( $profiletypes_user['profiletypes'], $profiletypes_values ) && in_array( $user_role, $profiletypes_values ) ) {
		${'role'. $profiletypes_user['profiletypes']} = $user_role;
		${'role'. $user_role} = $profiletypes_user['profiletypes'];
	}
	
?>
	<form action="post">

		<?php
		if ( get_user_role( array($rolesubscriber, 'administrator') ) ) {
			$bio_change = get_the_author_meta( 'bio_change', $user->ID );
			$bio_projects = get_the_author_meta( 'bio_projects', $user->ID );
			$bio_stuff = get_the_author_meta( 'bio_stuff', $user->ID ); 
		?>
		
		<Label for="bio_change">How I Would Change the World (in 50 words or less!)</Label>	
		<textarea value="" name="bio_change" id="bio_change" style="width:470px" rows="5"><?php echo $bio_change; ?></textarea>
	
		<Label for="bio_projects">Green Projects I Need Help With</Label>	
		<p>Are you working on something and need to find like minded people with complimentary skills? Explain your project and put the word out - the perfect helper/partner/collaborator might be just round the corner!</p>
		<textarea value="" name="member_projects" id="member_projects" style="width:470px" rows="5"><?php echo $bio_projects; ?></textarea>
		
		<Label for="bio_stuff">Green Stuff I'm Into</Label>	
		<p>Write a few brief words about the environmental, social or world changing issues that get you fired up.</p>
		<textarea value="" name="bio_stuff" id="bio_stuff" style="width:470px" rows="5"><?php echo $bio_stuff; ?></textarea>
		<?php
		}
		
		if ( get_user_role( array($roleeditor, $roleauthor, 'administrator') ) ) {
			$editors_blurb = get_the_author_meta( 'editors_blurb', $user->ID ); 
		?>
		<Label for="editors_blurb">Editors Blurb</Label>	
		<p></p>
		<textarea value="" name="editors_blurb" id="editors_blurb" style="width:470px" rows="5"><?php echo $editors_blurb; ?></textarea>
		<?php 
		}

		if ( get_user_role( array($rolecontributor, 'administrator') ) ) {
			$contributors_blurb = get_the_author_meta( 'contributors_blurb', $user->ID );
			$contributors_posttagline = get_the_author_meta( 'contributors_posttagline', $user->ID );
		?>
		<Label for="contributors_blurb">Contributors Blurb</Label>	
		<p>Tell visitors a little about your organisation. Make it fun! Visible on every Contributor profile.</p>
		<textarea name="contributors_blurb" id="contributors_blurb" style="width:470px" rows="5"><?php echo $contributors_blurb; ?></textarea>
		
		<label for="contributors_posttagline">Contributors Post Tagline</label>
		<p>In a couple sentences tell visitors a little about your organisation. Visible at end of each post you create.</p>
		<input type="text" value="<?php echo $contributors_posttagline; ?>" maxlength="255" name="contributors_posttagline" id="contributors_posttagline" style="width:470px" />
		<?php
		} 
		?>
	
		<Label>Website</Label>
		<input type="text" value="http://..." name="member_websiteurl" id="member_websiteurl" style="width:220px"/>
	
		<Label>Twitter ID</Label>
		<input type="text" value="@" name="member_twitterid" id="member_twitterid" style="width:220px"/>
		
		<Label>Facebook URL</Label>
		<p>Copy and paste the url from your facebook home page.</p>
		<input type="text" value="http://www.facebook.com/..." name="member_facebookid" id="member_facebookid" style="width:220px"/>
		
		<Label>Linkedin URL</Label>
		<p>Copy and paste the url from your linkedin profile page.</p>
		<input type="text" value="http://www.linkedin.com/..." name="member_linkedinid" id="member_linkedinid" style="width:220px"/>
		
		<Label>Skype ID</Label>
		<p>Copy and paste your Skype ID.</p>
		<input type="text" value="" name="member_linkedinid" id="member_linkedinid" style="width:220px"/>
		
		<?php 
		if ( get_user_role( array($rolesubscriber, 'administrator') ) ) {
			$employment_jobtitle = get_the_author_meta( 'employment_jobtitle', $user->ID );
			$employment_currentemployer = get_the_author_meta( 'employment_currentemployer', $user->ID );
		?>
		<Label for="employment_currentemployer">Current Employer</Label>
		<p>Who are you working for?</p>
		<input type="text" value="<?php echo $employment_currentemployer; ?>" name="employment_currentemployer" id="employment_currentemployer" style="width:220px" />
		
		<Label for="employment_jobtitle">Job Position</Label>
		<p>Work in the Green industry? We'd like to know what you do!</p>
		<input type="text" value="<?php echo $employment_jobtitle; ?>" name="employment_jobtitle" id="employment_jobtitle" style="width:220px"/>
		<?php
		} 
		?>
		<input type="button" name="Save Changes" />
	</form>
	<?php
}

function author_settings() {
	global $current_user;
	var_dump($current_user);
	$user_id = $current_user->data->ID;
	$user_first_name = $current_user->data->first_name;
	$user_last_name = $current_user->data->last_name;
	
	theme_authoreditnav();
	$profiletypes_values = array('administrator', 'editor', 'contributor', 'subscriber');
	$profiletypes_user = get_the_author_meta( 'profiletypes', $user->ID );
	?>
	<form action="post">
		<Label>First Name</Label>
		<input type="text" value="<?php echo $user_first_name; ?>" name="member_firstname" id="member_firstname" style="width:220px"/>
	
		<Label>Last Name</Label>
		<input type="text" value="<?php echo $user_last_name; ?>" name="member_lastname" id="member_lastname" style="width:220px"/>
		
		<Label>Reset Password</Label>
		
		<?php
		if ( get_user_role( array('administrator') ) ) {
			$profiletypes_items = array('profiletypes');
			# $profiletypes_users and $profiletypes_values defined top of function
	
			if ( is_array( $profiletypes_items ) ) {
			
				echo ('
				<label>Display profile as type...</label>
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
		
		<Label>Delete Account</Label>
		<p>This will permanently delete your account. All your settings will be removed and we will not be able to recover them at a later date.</p>
		<input type="button" name="Delete Now" value="Delete Now" />
		
	</form>
	<?php
}

function author_notifications() {
	theme_authoreditnav();
	?>
	<form action="post">
		<?php 
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
		<label for="' . esc_attr($key) . '">' . $value . '</label><p>How do you want notifications delivered to you?</p>
		<table><tr><th>Receive my notifitions in a weekly email update</th>
			<td><input type="radio" name="' . esc_attr($key) . '" id="' . esc_attr($key) . '" value="true" ');
		if ( $checked == true ) {echo "checked=\"checked\"";} 
		echo ('
		 /></td></tr><tr><th>Receive my notifications only on my dashboard</th>
	   	<td><input type="radio" name="' . esc_attr($key) . '" id="' . esc_attr($key) . '" value="false" ');
	   	if ( $checked == false ) {echo "checked=\"checked\"";}
	   	echo ('
	   	 /></td>
		</tr>
		</table>
		');
		
			}
		}
		?>
	</form>
	<?php
}

function author_locale() {
	theme_authoreditnav();
	$locale_postcode = get_the_author_meta( 'locale_postcode', $user->ID );
	?>
	<form action="post">
		<label>Country</label>
		<p>Australia</p>
		<input type="hidden" value="AU" name="member_country">
	
		<label for="locale_postcode">Postcode*</label>
		<p>Receive notifications green things happening close to you on your green toolbar</p>
		<input type="text" value="<?php echo $locale_postcode; ?>" name="locale_postcode" id="locale_postcode" maxlenght="4" style="width:74px"/>
	</form>
	<?php
}

function author_subscriptions() {
	theme_authoreditnav();
	?>
	<form action="post">
		<label>Your Subscriptions</label>
		<p></p>
		<table>
			<tr>
				<th></th>
				<th>Subscribed</th>
				<th>Not Subscribed</th>
			</tr>
			<?php
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
						<th>' . $value . '</th>
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
	</form>
	<?php
}

function author_privacy() {
	theme_authoreditnav();
	?>
	<p>Sorry, nothing here yet!</p>
	<?php
}

function theme_authoreditnav() {
	global $current_user;
	$authoredit_page = get_query_var( 'author_edit' );
	$post_author_url = get_author_posts_url($current_user->ID);
	?>
	<nav id="adv-tools">
		<ul>
			<li><a href="<?php echo $post_author_url; ?>edit"<?php if ( $authoredit_page == 1 ){echo ' class="active"';} ?>>Profile Info</a></li>
			<li><a href="<?php echo $post_author_url; ?>edit/settings"<?php if ( $authoredit_page == 2 ){echo ' class="active"';} ?>>Settings</a></li>
			<li><a href="<?php echo $post_author_url; ?>edit/locale"<?php if ( $authoredit_page == 3 ){echo ' class="active"';} ?>>Locale</a></li>
			<li><a href="<?php echo $post_author_url; ?>edit/notifications"<?php if ( $authoredit_page == 4 ){echo ' class="active"';} ?>>Notifications</a></li>
			<li><a href="<?php echo $post_author_url; ?>edit/subscriptions"<?php if ( $authoredit_page == 5 ){echo ' class="active"';} ?>>Subscriptions</a></li>
			<li><a href="<?php echo $post_author_url; ?>edit/privacy"<?php if ( $authoredit_page == 6 ){echo ' class="active"';} ?>>Privacy</a></li>
		</ul>
	</nav>
	<div class="clear"></div>
	<?php
}

function theme_authorphoto($profile_author) {
	echo '<div class="author-photo">' . get_avatar( $profile_author->ID, '160', '', $profile_author->display_name ) . '</div>';
}

function theme_authordisplayname($profile_author) {
	echo '<div class="author-name">' . $profile_author->display_name . '</div>';
}

function theme_authorposition($profile_author) {
	if ( !empty($profile_author->employment_jobtitle) ) {
		echo '<div class="author-position">Position: ' . $profile_author->employment_jobtitle . '</div>';
	}
}

function theme_authorlocation($profile_author) {
	echo '<div class="author-location">Location: ' . $profile_author->location . '</div>';
}

function theme_authoremail($profile_author) {
	if ( is_user_logged_in() && !empty($profile_author->user_email) ) {
		echo '<a href="mailto://' . str_replace('@', '[at]', $profile_author->user_email) . '" class="author-email"><img src="' . get_bloginfo('template_url') . '/template/socialmediaicons_v170/email-16x16.png" /></a>';
	}
}

function theme_authorfacebook($profile_author) {
if ( !empty($profile_author->facebook) ) {
		$profile_author_id = $profile_author->ID;
		$profile_author_facebook = $profile_author->facebook;
		$click_track_tag = '\'/outbound/profile-facebook/' . $profile_author_id . '/' . $profile_author_facebook .'/\'';
		echo '<a href="' . $profile_author->facebook . '" target="_new" onClick="_gaq.push([\'_trackPageview\', ' . $click_track_tag . ']);" class="author-facebook"><img src="' . get_bloginfo('template_url') . '/template/socialmediaicons_v170/facebook-16x16.png" /></a>';
	}
}

function theme_authorlinkedin($profile_author) {
if ( !empty($profile_author->linkedin) ) {
		$profile_author_id = $profile_author->ID;
		$profile_author_linkedin = $profile_author->linkedin;
		$click_track_tag = '\'/outbound/profile-linkedin/' . $profile_author_id . '/' . $profile_author_linkedin .'/\'';
		echo '<a href="' . $profile_author->linkedin . '" target="_new" onClick="_gaq.push([\'_trackPageview\', ' . $click_track_tag . ']);" class="author-linkedin"><img src="' . get_bloginfo('template_url') . '/template/socialmediaicons_v170/linkedin-16x16.png" /></a>';
	}
}

function theme_authortwitter($profile_author) {
	if ( !empty($profile_author->twitter) ) {
		$profile_author_id = $profile_author->ID;
		$profile_author_twitter = $profile_author->twitter;
		$click_track_tag = '\'/outbound/profile-twitter/' . $profile_author_id . '/' . $profile_author_twitter .'/\'';
		echo '<a href="http://www.twitter.com/' .$profile_author->twitter . '" target="_new" onClick="_gaq.push([\'_trackPageview\', ' . $click_track_tag . ']);" class="author-twitter"><img src="' . get_bloginfo('template_url') . '/template/socialmediaicons_v170/twitter-16x16.png" /></a>';
	}
}

function theme_authorskype($profile_author) {
	if ( is_user_logged_in() && !empty($profile_author->skype) ) {
		#$skype_viewers = array('administrator', 'contributor', 'author', 'editor');
		#if ( get_user_role($skype_viewers, $profile_author->ID) )  {
			$profile_author_id = $profile_author->ID;
			$profile_author_skype = $profile_author->skype;
			$click_track_tag = '\'/outbound/profile-skype/' . $profile_author_id . '/' . $profile_author_skype .'/\'';			
			echo '<a href="callto://' .$profile_author->skype . '" onClick="_gaq.push([\'_trackPageview\', ' . $click_track_tag . ']);" class="author-skype"><img src="' . get_bloginfo('template_url') . '/template/socialmediaicons_v170/skype-16x16.png" /></a>';
		#} 
	}
}

function theme_authorrss($profile_author) {
	echo '<a href="" class="author-rss"><img src="' . get_bloginfo('template_url') . '/template/socialmediaicons_v170/feed-16x16.png" /></a>';
}

function theme_authorwww($profile_author) {
	if ( !empty($profile_author->user_url) ) {
		$profile_author_id = $profile_author->ID;
		$profile_author_url = $profile_author->user_url;
		$click_track_tag = '\'/outbound/profile-user-url/' . $profile_author_id . '/' . $profile_author_url .'/\'';
		echo '<div class="author-www">Website: <a href="' . $profile_author->user_url . '" target="_new" onClick="_gaq.push([\'_trackPageview\', ' . $click_track_tag . ']);">' . $profile_author->user_url . '</a></div>';
	}	
}

function theme_authorbio($profile_author) {
	
}

function theme_contributorsblurb($profile_author) {
	if ( !empty($profile_author->contributors_blurb) ) {
		echo '<p>' . nl2br($profile_author->contributors_blurb) . '</p>';
	}
}

/** PRODUCT 'BUY IT!' BUTTON **/

function theme_single_product_button() {
	global $post;
	if (get_post_type() == "gp_advertorial") { 
		$custom = get_post_custom($post->ID);
	 	$product_url = $custom["gp_advertorial_product_url"][0];
	 	$post_author = get_userdata($post->post_author);
	 	$post_id = $post->ID;
	 	$post_author_id = $post_author->ID;
	 	
	 	if ( !empty($product_url) ) {
		?>
		<div id="post-product-button-bar">
			<?php
			$click_track_tag = '\'/outbound/product-button/' . $post_id . '/' . $post_author_id . '/' . $product_url .'/\'';
			echo '<span><a href="' . $product_url . '" target="_blank" onClick="_gaq.push([\'_trackPageview\', ' . $click_track_tag . ']);"><input type="button" id="product-button" value="Buy It!" /></a></span>';
			?>			
		</div>
		<div class="clear"></div>
		<?php
	 	}
	}
}

/********************************************************************************/

/** CONTRIBUTOR / CONTENT PARTNER DONATE | JOIN | SEND LETTER | SIGN PETITION | VOLUNTEER BARS **/

function theme_profile_contributor_donate_join_bar($profile_author){
	if (get_post_type() != "page") { 
		global $post;	
		$post_author = $profile_author;
		$post_author_id = $post_author->ID;
		$donate_url = $post_author->contributors_donate_url;
		$join_url = $post_author->contributors_join_url;
		$letter_url = $post_author->contributors_letter_url;
		$petition_url = $post_author->contributors_petition_url;
		$volunteer_url = $post_author->contributors_volunteer_url	
		
		?>
		<div id="post-donate-join-bar">
			<?php
			theme_contributors_donate($donate_url, $post_author_id);
			theme_contributors_join($join_url, $post_author_id);
			theme_contributors_letter($letter_url, $post_author_id);
			theme_contributors_petition($petition_url, $post_author_id);
			theme_contributors_volunteer($volunteer_url, $post_author_id);
			?>			
		</div>
		<div class="clear"></div>
		<?php				
	}
}

function theme_index_contributor_donate_join_bar() {
	if (get_post_type() != "page") { 
		global $post;
		$post_author = get_userdata($post->post_author);
		$post_author_url = get_author_posts_url($post->post_author);
		$post_author_id = $post_author->ID;
		$donate_url = $post_author->contributors_donate_url;
		$join_url = $post_author->contributors_join_url;
		$letter_url = $post_author->contributors_letter_url;
		$petition_url = $post_author->contributors_petition_url;
		$volunteer_url = $post_author->contributors_volunteer_url;
		
		?>
		<div id="index-donate-join-bar">
			<?php
			theme_contributors_donate($donate_url, $post_author_id);
			theme_contributors_join($join_url, $post_author_id);
			theme_contributors_letter($letter_url, $post_author_id);
			theme_contributors_petition($petition_url, $post_author_id);
			theme_contributors_volunteer($volunteer_url, $post_author_id);
			?>			
		</div>
		<div class="clear"></div>
		<?php		
	}
}

function theme_single_contributor_donate_join_bar() {
	if (get_post_type() != "page") { 
		global $post;
		$post_author = get_userdata($post->post_author);
		$post_author_url = get_author_posts_url($post->post_author);
		$post_author_id = $post_author->ID;
		$donate_url = $post_author->contributors_donate_url;
		$join_url = $post_author->contributors_join_url;
		$letter_url = $post_author->contributors_letter_url;
		$petition_url = $post_author->contributors_petition_url;
		$volunteer_url = $post_author->contributors_volunteer_url;
		
		?>
		<h3>Would you like to help <a href="<?php echo $post_author_url ?>"><?php echo $post_author->display_name ?></a> change the world?</h3>
		<div id="post-donate-join-bar">
			<?php
			theme_contributors_donate($donate_url, $post_author_id);
			theme_contributors_join($join_url, $post_author_id);
			theme_contributors_letter($letter_url, $post_author_id);
			theme_contributors_petition($petition_url, $post_author_id);
			theme_contributors_volunteer($volunteer_url, $post_author_id);
			?>			
		</div>
		<div class="clear"></div>
		<?php		
	}
}

/** CONTRIBUTOR / CONTENT PARTNER DONATE | JOIN | SEND LETTER | SIGN PETITION | VOLUNTEER BUTTONS **/

function theme_contributors_donate($donate_url, $post_author_id) {
	if ( !empty($donate_url) ) {
		$click_track_tag = '\'/outbound/activist-donate-button/' . $post_author_id . '/' . $donate_url .'/\'';
		echo '<span><a href="' . $donate_url . '" target="_blank" onClick="_gaq.push([\'_trackPageview\', ' . $click_track_tag . ']);"><input type="button" id="donate" value="Donate" /></a></span>';
	}
}

function theme_contributors_join($join_url, $post_author_id) {
	if ( !empty($join_url) ) {
		$click_track_tag = '\'/outbound/activist-join-button/' . $post_author_id . '/' . $join_url .'/\'';
		echo '<span><a href="' . $join_url . '" target="_blank" onClick="_gaq.push([\'_trackPageview\', ' . $click_track_tag . ']);"><input type="button" id="join" value="Join" /></a></span>';
	}
}

function theme_contributors_letter($letter_url, $post_author_id) {
	if ( !empty($letter_url) ) {
		$click_track_tag = '\'/outbound/activist-letter-button/' . $post_author_id . '/' . $letter_url .'/\'';
		echo '<span><a href="'. $letter_url .'" target="_blank" onClick="_gaq.push([\'_trackPageview\', ' . $click_track_tag . ']);"><input type="button" id="letter" value="Send Letter" /></a></span>';
	}
}

function theme_contributors_petition($petition_url, $post_author_id) {
	if ( !empty($petition_url) ) {
		$click_track_tag = '\'/outbound/activist-petition-button/' . $post_author_id . '/' . $petition_url .'/\'';
		echo '<span><a href="'. $petition_url .'" target="_blank" onClick="_gaq.push([\'_trackPageview\', ' . $click_track_tag . ']);"><input type="button" id="petition" value="Sign Petition" /></a></span>';
	}
}

function theme_contributors_volunteer($volunteer_url, $post_author_id) {
	if ( !empty($volunteer_url) ) {
		$click_track_tag = '\'/outbound/activist-volunteer-button/' . $post_author_id . '/' . $volunteer_url .'/\'';
		echo '<span><a href="'. $volunteer_url .'" target="_blank" onClick="_gaq.push([\'_trackPageview\', ' . $click_track_tag . ']);"><input type="button" id="volunteer" value="Volunteer" /></a></span>';
	}
}

/********************************************************************************/

function theme_editorsblurb($profile_author) {
	if ( !empty($profile_author->editors_blurb) ) {
		echo '<p>' . nl2br($profile_author->editors_blurb) . '</p>';
	}
}

function theme_authorgreenrazor($profile_author) {
	if ($profile_author->subscription["subscription-greenrazor"] == "true") {
		echo '<div class="author-greenrazor">Subscriptions: <a href="/about/green-razor-newsletter">Green Razor Newsletter</a></div>';
	}
}

function theme_authorjoined($profile_author) {
	$author_meta = get_userdata($profile_author->ID);
	$author_registered = $author_meta->user_registered;
	
	$author_registered_diff = _date_diff(strtotime($author_registered), time());
	if ($author_registered_diff['y'] > 1) {
		$author_registered = $author_registered_diff['y'] . ' year, ' .  $author_registered_diff['m'] . ' months';
	}
	 
	if ($author_registered_diff['y'] <= 0 && $author_registered_diff['d'] > 0) {
		$author_registered = $author_registered_diff['m'] . ' months, ' .  $author_registered_diff['d'] . ' days';
	}
	
	if ($author_registered_diff['d'] <= 0) {
		$author_registered = $author_registered_diff['h'] . ' hours, ' .  $author_registered_diff['i'] . ' minutes';
	}
	
	echo '<div class="author-joined">Joined: ' . $author_registered . '</div>';
}

function theme_authorseen($profile_author) {
	/*
	 * Note: This function isn't complete because it only updates on user login. You would need to set a cookie to tell the system to update periodically.
	 * See: http://meta.stackoverflow.com/questions/27234/account-last-activity-time-is-not-always-updated/33310#33310
	 * See: http://stackoverflow.com/questions/3027973/what-is-the-best-way-to-implement-a-last-seen-function-in-a-django-web-app
	 */
	$last_login = get_user_meta($profile_author->ID, 'last_login', true);
	$epochtime = strtotime('now');
	$last_login_diff = _date_diff((int)$last_login, $epochtime);
	if (date('Y', $epochtime) - $last_login_diff['y'] != 1970) {
		if ($last_login_diff['y'] > 1) {
			$last_login = mysql2date('jS F Y \a\t g:i A', $last_login, false);
		}
		 
		if ($last_login_diff['y'] <= 0 && $last_login_diff['d'] > 0) {
			$last_login = $last_login_diff['m'] . ' months, ' .  $last_login_diff['d'] . ' days ago';
		}
		
		if ($last_login_diff['d'] <= 0) {
			$last_login = $last_login_diff['h'] . ' hours, ' .  $last_login_diff['i'] . ' minutes ago';
		}
	} else {
		$last_login = ' - ';
	}
    
    echo '<div class="author-seen">Last Seen: <span>' . $last_login . '</span></div>';
}

function theme_authorschange($profile_author) {
	if (!empty($profile_author->bio_change)) {
		echo '<h1>How I Would Change the World</h1>';
		echo '<p>' . $profile_author->bio_change . '</p>';
	}
}

function theme_authorsprojects($profile_author) {
	if (!empty($profile_author->bio_projects)) {
		echo '<h1>Green Projects I Need Help With</h1>';
		echo '<p>' . $profile_author->bio_projects . '</p>';
	}	
}

function insert_memberslist_projects_excerpt($member) {
	if (!empty($member->bio_projects)) {	
		return '<div><p><strong>Needs Help With: </strong>' . substr($member->bio_projects, 0, 135) . ' <strong>... Learn More ...</strong></p></div>';
	}
}

function theme_authorsstuff($profile_author) {
	if (!empty($profile_author->bio_stuff)) {
		echo '<h1>Green Stuff I\'m Into</h1>';
		echo '<p>' . $profile_author->bio_stuff . '</p>';
	}	
}

/** SHOW MEMBERS FAVOURITE POSTS **/
function theme_author_favourites($profile_author) {
	global $wpdb;
	global $post;
	global $current_user, $current_site;
	
	$querystr = "SELECT " . $wpdb->prefix . "posts.*
					, m1.meta_value as _thumbnail_id 
				FROM " . $wpdb->prefix . "posts 
				LEFT JOIN " . $wpdb->prefix . "usermeta as m0 on REPLACE(m0.meta_key, 'likepost_" . $current_site->id . "_', '')=" . $wpdb->prefix . "posts.ID 
				LEFT JOIN " . $wpdb->prefix . "postmeta as m1 on m1.post_id=" . $wpdb->prefix . "posts.ID and m1.meta_key='_thumbnail_id' 
				WHERE post_status='publish' 
					AND m0.meta_value > 0 
					AND m0.user_id = $profile_author->ID 
					AND m0.meta_key LIKE 'likepost%' 
					AND m1.meta_value >= 1 
				ORDER BY m0.meta_value 
				DESC;";
					
	$pageposts = $wpdb->get_results($querystr, OBJECT);	
	?>
		<div id="my-favourites">
		<?php 
		
		foreach ($pageposts as $post) {
			setup_postdata($post);
			switch (get_post_type()) {
			    case 'gp_news':
			        $post_title = 'News';
			        $post_url = '/news';
			        break;
			    case 'gp_ngocampaign':
			    	$post_title = 'Campaigns';
			    	$post_url = '/ngo-campaign';
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
			echo '
			<div class="profile-postbox">
		    	<h1><a href="' . get_permalink($post->ID) . '" title="Permalink to ' . esc_attr(get_the_title($post->ID)) . '" rel="bookmark">' . get_the_title($post->ID) . '</a></h1>
		    	<div class="post-details">Posted in <a href="' . $post_url . '">' . $post_title . '</a> ' . time_ago(get_the_time('U'), 0) . ' ago</div>';
		    	the_excerpt();
				echo '<a href="' . get_permalink($post->ID) . '" class="profile_postlink">Read more...</a>';
				
			if ( comments_open() ) {
				echo '<div class="comment-profile"><a href="' . get_permalink($post->ID) . '#comments"><span class="comment-mini"></span><span class="comment-mini-number dsq-postid"><fb:comments-count href="' . get_permalink($post->ID) . '"></fb:comments-count></span></a></div>';
			}
			
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
				
	    	echo '</div>';
			if ( has_post_thumbnail() ) {
				$imageArray = wp_get_attachment_image_src( get_post_thumbnail_id($post->ID), 'homepage-thumbnail' );
				$imageURL = $imageArray[0];
				echo '<a href="' . get_permalink($post->ID) . '" class="profile_minithumb"><img src="' . $imageURL  . '" alt="' . get_the_title( get_post_thumbnail_id($post->ID) ) . '" /></a>';
			}
			echo '<div class="clear"></div>';
		}
		#theme_indexpagination();
		?>
		</div>
		<?php 
}

/** SHOW MEMBERS CREATE NEW AD OPTIONS **/
function theme_author_advertise($profile_author) {
	global $wpdb;
	global $post;
	global $current_user;
	
	?>
	<div id="my-advertise">
		<div id="advertorial">
			<span><?php theme_insert_advertise_advertorialcreate_post(); ?></span>
			<div class="clear"></div>			
			<span><a href="<?php bloginfo('template_url'); ?>/gp-rate-card-new-stuff-logged-in.html" rel="iframe-820-460"  class="pirobox_gall1">Learn more</a></span>
		</div>
		<div class="clear"></div>
		<div id="competition">
			<span><?php theme_insert_advertise_competitioncreate_post(); ?></span>	
			<div class="clear"></div>				
			<span><a href="<?php bloginfo('template_url'); ?>/gp-rate-card-competition-logged-in.html" rel="iframe-820-460"  class="pirobox_gall1">Learn more</a></span>
		</div>
		<div class="clear"></div>
		<div id="listing">
			<span><?php theme_insert_listingcreate_new(); ?></span>
			<div class="clear"></div>
			<span><a href="<?php bloginfo('template_url'); ?>/gp-rate-card-directory-page-logged-in.html" rel="iframe-820-460"  class="pirobox_gall1">Learn more</a></span>
		</div>
		<div class="clear"></div>
		<div id="email">
			<span><?php theme_insert_emailcreate_new(); ?></span>
			<div class="clear"></div>
			<span><a href="<?php bloginfo('template_url'); ?>/gp-rate-card-exclusive-email-logged-in.html" rel="iframe-820-460"  class="pirobox_gall1">Learn more</a></span>
		</div>
		<div class="clear"></div>
	</div>
	<?php 
}

/** SHOW MEMBERS GOOGLE ANALYTICS DATA FOR THEIR POSTS **/
function theme_author_analytics($profile_author, $pageposts) {
	require 'ga/analytics.class.php';
	global $wpdb;
	global $post;
	global $current_user;
	
	if (!$pageposts) {
		?>
		<div id="my-analytics"></div>
		<?php 
		return;
	}
	
	$profile_author_id = $profile_author->ID;
	
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
				<td class="author_analytics_page_impressions">Page Views</td>
				<td class="author_analytics_clicks">Clicks</td>
				<td class="author_analytics_category_impressions">Category Impressions</td>
			</tr>
	<?php	
				
	if ($pageposts) {		
	 	
		foreach ($pageposts as $post) {
			setup_postdata($post);
		
			$post_url_ext = $post->post_name; //Need to get post_name for URL. Gets ful URl, but we only need /url extention for Google API			
			$type = get_post_type($post->ID);
				
			$post_type_map = array( "gp_news" => "news", 
					                "gp_events" => "events", 
                         			"gp_advertorial" => "eco-friendly-products", 
                        			"gp_competitions" => "competitions", 
                         			"gp_people" => "people", 
                         			"gp_ngocampaign" => "ngo-campaign");
				
			$post_url_end = $post_type_map[$type] . '/' . $post_url_ext;
			#echo $post_url_end . '<br />$post_url_end<br />';
				
			$analytics = new analytics('greenpagesadserving@gmail.com', 'greenpages01'); //sign in and grab profile			
  			$analytics->setProfileById('ga:42443499'); 			//$analytics->setProfileByName('Stage 1 - Green Pages');
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
			
  			$pageViewType = ($analytics->getPageviewsURL($post_type_map[$type]));	//Page views for the section landing page, e.g. the news page
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
	 		$click_track_tag = 'yoast-ga/' . $post_id . '/' . $profile_author_id . '/outbound-article';
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
	 					$click_track_tag_product_button = 'outbound/product-button/' . $post_id . '/' . $profile_author_id . '/' . $product_url; 
  						$clickURL_product_button = ($analytics->getPageviewsURL($click_track_tag_product_button));
  						foreach ($clickURL_product_button as $data) {
    						$sumClick = $sumClick + $data;
  						}
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
		     	case 'gp_ngocampaign':
			    	$post_title = 'Campaigns';
			    	$post_url = '/ngo-campaign';
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
					<td class="author_analytics_page_impressions">' . $sumURL . '</td>	
					<td class="author_analytics_clicks">' . $sumClick . '</td>				
					<td class="author_analytics_category_impressions">' . $sumType . '</td>				
				</tr>';
		}
	}	
	?>
		</table>			
		<?php theme_homecreate_post(); ?>
		<p>Your posts have been viewed a total of</p> 
		<p><span class="big-number"><?php echo $total_sumURL;?></span> times!</p>	
		<p></p>
		
		
		<?php 	# FOR ADVERTISERS WHO HAVE (OR HAVE HAD) A DIRECTORY PAGE
		$old_crm_id = $profile_author->old_crm_id;
		if (!empty($old_crm_id)) {
		?>
			<h2>Directory Page Analytics</h2>
			<table class="author_analytics">
				<tr>
					<td class="author_analytics_title">Title</td>
					<td class="author_analytics_cost">Value</td>
					<!-- <td class="author_analytics_page_impressions">Page Impressions</td>  -->
					<td class="author_analytics_clicks">Clicks</td>
				</tr>	

				<?php 		
				# SET AND RESET SOME VARIABLES AND GET DIRECTORY PAGE DATA FROM GA
				$start_date = '2012-01-01'; 	// Click tracking of Directory Pages began just after this Date
				$today_date = date('Y-m-d'); 	// Todays Date
				
	  			$analytics->setDateRange($start_date, $today_date); //Set date in GA $analytics->setMonth(date('$post_date'), date('$new_date'));
				
				$click_track_tag = 'outbound/directory/' . $profile_author_id;
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
    				<td class="author_analytics_title">Directory page</td>
    				<td class="author_analytics_cost">$39 per month</td>
    				<!-- <td class="author_analytics_page_impressions">Coming Soon!</td> -->
    				<td class="author_analytics_clicks"><?php echo $sumClick; ?></td>
    			</tr>
    		</table>
    		<div id="post-filter"></div>
    		<div class="post-details">Page Views will be available soon!</div>
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
					<td class="author_analytics_activist">Donate</td>
					<td class="author_analytics_activist">Join</td>
					<td class="author_analytics_activist">Send Letter</td>
					<td class="author_analytics_clicks">Sign Petition</td>
					<td class="author_analytics_clicks">Volunteer</td>	
				</tr>
				<tr>
					<td class="author_analytics_title">Clicks</td>
					<?php #DISPLAY TABLE CELLS WITH CLICK DATA FOR ACTIVIST BAR BUTTONS
		  			foreach ($button_labels as $label => $label_url) {
  						$click_track_tag = 'outbound/activist-' . $label .'-button/' . $profile_author_id . '/' . $label_url;
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
  						echo '<td class="author_analytics_activist">' . $sumClick . '</td>';
  					}
		  			if ($activist_clicks_sum == 0) {			#IF NO CLICKS YET, DISPLAY 'Unavailable'
    					$activist_clicks_sum = 'Unavailable';
    				}
					?>		
				</tr>
			</table>
			<?php
			theme_profilecreate_post();
			if($activist_clicks_sum != 0) { 	#IF CLICKS DATA RETURNED, DISPLAY TOTAL
			?>
				<p>Your activist buttons have been clicked a total of</p> 
				<p><span class="big-number"><?php echo $activist_clicks_sum;?></span> times!</p>	
				<p></p>
			<?php
			}
			?>
			<div class="post-details">You can enter or update urls for Activist Bar buttons by clicking on Edit My Profile!</div>
			<?php 
		} 
		?>
		<div class="post-details">Why are Clicks showing as 'Unavailable'?</div>
		<div class="post-details">As it's a new feature, the clicks column is showing data from late 01/2012 onwards, all preceding click data is unavailable here.</div>
		<div class="post-details">Earlier clicks may be found by looking for thegreenpages.com.au under 'Traffic Source' in your own Google Analytics account.</div>	
	</div>
<?php 
}

function theme_subscriberposts($profile_author) {
	global $current_user;
	if ((is_user_logged_in()) && ($current_user->ID == $profile_author->ID) || get_user_role( array($rolecontributor, 'administrator') ) ) {
		?>
		<nav class="profile-tabs">
			<ul>
				<li id="favourites" style="background-color:#61c201;">Your Favourites</li>
			</ul>
		</nav>
		<?php
		theme_author_favourites($profile_author);	 #SHOW USER THEIR FAVOURITE POSTS IF LOGGED IN
	}
}

function theme_authorposts($profile_author) {
	global $wpdb;
	global $post;
	global $current_user;
			
	if ((is_user_logged_in()) && ($current_user->ID == $profile_author->ID) || get_user_role( array($rolecontributor, 'administrator') ) ) { # CHECK IF USER IS LOGGED IN AND VIEWING THEIR OWN PROFILE PAGE
		
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
						$wpdb->prefix . "posts.post_type = 'gp_ngocampaign' or " . 
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
							$wpdb->prefix . "posts.post_type = 'gp_ngocampaign' or " . 
							$wpdb->prefix . "posts.post_type = 'gp_competitions' or " . 
							$wpdb->prefix . "posts.post_type = 'gp_people') 
						and " . 
						$wpdb->prefix . "postmeta.meta_key = '_thumbnail_id' and " . 
						$wpdb->prefix . "postmeta.meta_value >= 1 and " . 
						$wpdb->prefix . "posts.post_author = '" . $profile_author->ID . "' 
					ORDER BY " . $wpdb->prefix . "posts.post_date DESC";
						
		$pageposts = $wpdb->get_results($querystr, OBJECT);
		
		if ($pageposts) {
			?><script type="text/javascript"><!-- 	
			function display_analytics(){		// JS DISPLAY ANAYTICS IF ANALYTICS TAB CLICKED ON
				document.getElementById("my-posts").style.display="none";
				document.getElementById("my-favourites").style.display="none";
				document.getElementById("my-analytics").style.display="inline";
				document.getElementById("my-advertise").style.display="none";
				document.getElementById("posts").style.backgroundColor="#afde7f";
				document.getElementById("favourites").style.backgroundColor="#afde7f";
				document.getElementById("analytics").style.backgroundColor="#61c201";
				document.getElementById("advertise").style.backgroundColor="#afde7f";
			}
	
			function display_posts(){			// JS DISPLAY POSTS IF POSTS TAB CLICKED ON
				document.getElementById("my-posts").style.display="inline";
				document.getElementById("my-favourites").style.display="none";
				document.getElementById("my-analytics").style.display="none";
				document.getElementById("my-advertise").style.display="none";
				document.getElementById("posts").style.backgroundColor="#61c201";
				document.getElementById("favourites").style.backgroundColor="#afde7f";
				document.getElementById("analytics").style.backgroundColor="#afde7f";
				document.getElementById("advertise").style.backgroundColor="#afde7f";
			}
	
			function display_advertise(){		// JS DISPLAY ADVERTISE PANEL IF ADVERTISE TAB CLICKED ON
				document.getElementById("my-posts").style.display="none";
				document.getElementById("my-favourites").style.display="none";
				document.getElementById("my-analytics").style.display="none";
				document.getElementById("my-advertise").style.display="inline";
				document.getElementById("posts").style.backgroundColor="#afde7f";
				document.getElementById("favourites").style.backgroundColor="#afde7f";
				document.getElementById("analytics").style.backgroundColor="#afde7f";
				document.getElementById("advertise").style.backgroundColor="#61c201";
			}
	
			function display_favourites(){		// JS DISPLAY FAVOURITES PANEL IF FAVOURITES TAB CLICKED ON
				document.getElementById("my-posts").style.display="none";
				document.getElementById("my-favourites").style.display="inline";
				document.getElementById("my-analytics").style.display="none";
				document.getElementById("my-advertise").style.display="none";
				document.getElementById("posts").style.backgroundColor="#afde7f";
				document.getElementById("favourites").style.backgroundColor="#61c201";
				document.getElementById("analytics").style.backgroundColor="#afde7f";
				document.getElementById("advertise").style.backgroundColor="#afde7f";
			}
			--></script> 
			<nav class="profile-tabs">
				<ul>
					<li id="posts" onclick="display_posts()">Your Posts</li>
					<li id="favourites" onclick="display_favourites()">Favourites</li>
					<li id="analytics" onclick="display_analytics()">Analytics</li>
					<li id="advertise" onclick="display_advertise()">Advertise</li>
					<!-- <li><span>Campaigns</span></li> -->
				</ul>
			</nav>
			<?php
			theme_author_analytics($profile_author, $pageposts);			 #SHOW USER THEIR AD DATA IF LOGGED IN AND ON THEIR OWN PAGE
			theme_author_advertise($profile_author);						 #SHOW USER AN ADVERTISE PANEL WHERE THEY CAN CREATE ADS OR LEARN ABOUT AD TYPES
			theme_author_favourites($profile_author);						 #SHOW USER THEIR FAVOURITE POSTS IF LOGGED IN
			?>
			<div id="my-posts">
			<?php 
			
			foreach ($pageposts as $post) {
				setup_postdata($post);
				switch (get_post_type()) {
				    case 'gp_news':
				        $post_title = 'News';
				        $post_url = '/news';
				        break;
				    case 'gp_ngocampaign':
				    	$post_title = 'Campaigns';
				    	$post_url = '/ngo-campaign';
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
					
		    	echo '</div>';
				if ( has_post_thumbnail() ) {
					$imageArray = wp_get_attachment_image_src( get_post_thumbnail_id($post->ID), 'homepage-thumbnail' );
					$imageURL = $imageArray[0];
					echo '<a href="' . get_permalink($post->ID) . '" class="profile_minithumb"><img src="' . $imageURL  . '" alt="' . get_the_title( get_post_thumbnail_id($post->ID) ) . '" /></a>';
				}
				echo '<div class="clear"></div>';
			}
			#theme_indexpagination();
			?>
			</div>
			<?php
		} else {
			?><script type="text/javascript"><!-- 	
			function display_advertise(){		// JS DISPLAY ADVERTISE PANEL IF ADVERTISE TAB CLICKED ON
				document.getElementById("my-favourites").style.display="none";
				document.getElementById("my-advertise").style.display="inline";
				document.getElementById("favourites").style.backgroundColor="#afde7f";
				document.getElementById("advertise").style.backgroundColor="#61c201";
			}
	
			function display_favourites(){		// JS DISPLAY FAVOURITES PANEL IF FAVOURITES TAB CLICKED ON
				document.getElementById("my-favourites").style.display="inline";
				document.getElementById("my-advertise").style.display="none";
				document.getElementById("favourites").style.backgroundColor="#61c201";
				document.getElementById("advertise").style.backgroundColor="#afde7f";
			}
			--></script> 
			<nav class="profile-tabs">
				<ul>
					<li id="favourites" onclick="display_favourites()">Favourites</li>
					<li id="advertise" onclick="display_advertise()">Advertise</li>
					<!-- <li><span>Campaigns</span></li> -->
				</ul>
			</nav>
			<?php
			theme_author_advertise($profile_author);						 #SHOW USER AN ADVERTISE PANEL WHERE THEY CAN CREATE ADS OR LEARN ABOUT AD TYPES
			theme_author_favourites($profile_author);						 #SHOW USER THEIR FAVOURITE POSTS IF LOGGED IN
		}
	}
}

/** Administrators Profile **/
function administrator_index() { 
	# Administrator profiles should never be shown - it's a security issue - never show your login id!!!
} 

/** Editors/Authors Profile **/
function editor_index($profile_author) {
	theme_profilecreate_post();
	theme_authorphoto($profile_author);
	echo '<div class="author-box">';
		theme_authordisplayname($profile_author);
		echo '<div class="author-connect">';
			#theme_authoremail($profile_author);
			theme_authorfacebook($profile_author);
			theme_authortwitter($profile_author);
			theme_authorlinkedin($profile_author);
			theme_authorskype($profile_author);
			#theme_authorrss($profile_author);
		echo '<div class="clear"></div></div>';
		#theme_authorlocation($profile_author);
		theme_authorposition($profile_author);
		theme_authorwww($profile_author);
		#theme_authorjoined($profile_author);
		#theme_authorseen($profile_author);
		#theme_authorgreenrazor($profile_author);
	echo '</div><div class="clear"></div>';
	theme_editorsblurb($profile_author);
	#echo '<div class="clear"></div>';
	theme_profile_contributor_donate_join_bar($profile_author);	
	theme_authorposts($profile_author); 
	echo '<div class="clear"></div>';
}

/** Subscribers Profile **/
function subscriber_index($profile_author) {
	theme_profilecreate_post();
	theme_authorphoto($profile_author);
	echo '<div class="author-box">';
		theme_authordisplayname($profile_author);
		echo '<div class="author-connect">';
			theme_authorfacebook($profile_author);
			theme_authortwitter($profile_author);
			theme_authorlinkedin($profile_author);
			theme_authorskype($profile_author);
		echo '<div class="clear"></div></div>';
		#theme_authorlocation($profile_author);
		theme_authorposition($profile_author);
		theme_authorwww($profile_author);
		#theme_authorjoined($profile_author);
		#theme_authorseen($profile_author);
		#theme_authorgreenrazor($profile_author);
	echo '</div><div class="clear"></div>';
	theme_authorschange($profile_author);
	theme_authorsprojects($profile_author);
	theme_authorsstuff($profile_author);
	echo '<div class="clear"></div>';
	theme_subscriberposts($profile_author); 
	echo '<div class="clear"></div>';
} 

/** Contributors Profile **/
function contributor_index($profile_author) {
	theme_profilecreate_post();
	theme_authorphoto($profile_author);
	echo '<div class="author-box">';
		theme_authordisplayname($profile_author);
		echo '<div class="author-connect">';
			#theme_authoremail($profile_author);
			theme_authorfacebook($profile_author);
			theme_authortwitter($profile_author);
			theme_authorlinkedin($profile_author);
			theme_authorskype($profile_author);
			#theme_authorrss($profile_author);
		echo '<div class="clear"></div></div>';
		theme_authorwww($profile_author);
		#theme_authorjoined($profile_author);
		#theme_authorseen($profile_author);
		#theme_authorgreenrazor($profile_author);
	echo '</div><div class="clear"></div>';
	theme_profile_contributor_donate_join_bar($profile_author);
	theme_contributorsblurb($profile_author);
	echo '<div class="clear"></div>';
	theme_authorposts($profile_author); 
	echo '<div class="clear"></div>';
} 

?>
 
