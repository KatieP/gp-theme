<?php
/*** TEMPLATE ROUTING FUNCTIONS ***/

function get_posttemplate($template='default_index') {
	global $wp_query, $current_user;
    $current_page_id = $wp_query->get_queried_object_id();
	$profile_author = get_profile_author();

	$templates = array('default_index', 'default_single', 'default_page', 'home_index', 'author_index', 'author_edit', 'author_account', 'author_notifications', 'author_locale', 'author_newsletters', 'author_privacy', 'author_password', 'author_admin', 'search_index', 'events_index', 'competitions_index', 'news_index', 'people_index', 'jobs_index', 'advertorial_index', 'projects_index', 'katiepatrick_index', 'productreview_index', 'greengurus_index', 'attachment_single');
	
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
		'gp_projects' => 'projects_index',					/* Projects */
		'gp_katiepatrick' => 'katiepatrick_index',			/* Katie Patrick Editorial */
		'gp_productreview' => 'productreview_index',		/* Product Reviews */
		'gp_greengurus' => 'greengurus_index',				/* Green Gurus */
		'attachment' => 'attachment_single',				/* Attachment */
		'author_edit' => 'author_edit',						/* Author Edit */
		'author_account' => 'author_account',				/* Author Edit: Account */
		'author_notifications' => 'author_notifications',	/* Author Edit: Notifications */
		'author_locale' => 'author_locale',					/* Author Edit: Locale */
		'author_newsletters' => 'author_newsletters',		/* Author Edit: Newsletters */
		'author_privacy' => 'author_privacy',				/* Author Edit: Privacy */
		'author_password' => 'author_password',				/* Author Edit: Password */
		'author_admin' => 'author_admin'					/* Author Edit: Admin */
	);
	
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
	
	#if ( is_home() && isset($templateRoutes['home']) ) {
	if ( $_SERVER['REQUEST_URI'] == "/" && isset($templateRoutes['home']) ) {
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
					$template = $templateRoutes['author_account'];
					break;
				case 3:
					$template = $templateRoutes['author_locale'];
					break;
				case 4:
					$template = $templateRoutes['author_notifications'];
					break;
				case 5:
					$template = $templateRoutes['author_newsletters'];
					break;
				case 6:
					$template = $templateRoutes['author_privacy'];
					break;
				case 7:
					$template = $templateRoutes['author_password'];
					break;
				case 8:
					$template = $templateRoutes['author_admin'];
					break;
				default:
					$template = $templateRoutes['author_edit'];
					break;
			}
			if ( ( ( is_user_logged_in() ) && ( $current_user->ID == $profile_author->ID ) ) || get_user_role( array('administrator') ) ) {} else {$template = $templateRoutes['home'];}
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

function theme_singletitle() {
	global $wp_query;
	global $post;
	$titleClass = '';
	$title = esc_attr(get_the_title($post->ID));
	$link = get_permalink($post->ID);
	
	if ($wp_query->current_post == 0 || $wp_query->current_post == -1) {$titleClass = ' class="loop-title"';}
	echo '<h1' . $titleClass. '><a href="' . $link . '" title="' . $title . '" rel="bookmark">' . $title . '</a></h1>';
}

function theme_singledetails() {
	global $posts;
	$post_author = get_userdata($posts[0]->post_author);
	$post_author_url = get_author_posts_url($posts[0]->post_author);
	
	echo '<div class="post-details">
	          <a href="' . $post_author_url . '">' . get_avatar( $post_author->ID, '18', '', $post_author->display_name ) . '</a>
	          Posted by <a href="' . $post_author_url . '">' . $post_author->display_name . '</a> ' . time_ago(get_the_time('U'), 0) . ' ago
	      </div>';
	theme_like();
	echo '<div class="clear"></div>';
	$user_default_location = get_the_author_meta( 'user_default_location', $post_author->ID );
	#var_dump($user_default_location);
}

function theme_singlecontributorstagline() {
	global $posts;
	$post_author = get_userdata($posts[0]->post_author);
	$post_author_url = get_author_posts_url($posts[0]->post_author);
	$post_author_tagline = get_the_author_meta( 'contributors_posttagline', $post_author->ID );

	if ( !empty($post_author_tagline) ) {
		echo '<div class="post-authorsdisclaimer">
		          <a href="' . $post_author_url . '">' . get_avatar( $post_author->ID, '50', '', $post_author->display_name ) . '</a>
		          <div class="post-authorsdisclaimer-details">
		              Posted by <a href="' . $post_author_url . '">' . $post_author->display_name . '</a> ' . time_ago(get_the_time('U'), 1) . ' ago
		          </div>
		          <div class="post-authorsdisclaimer-content">' . $post_author_tagline . '</div>
		          <div class="clear"></div>
		      </div>';
	} else {
		theme_singledetails();
	}
}

function theme_single_event_details() {
    /**
     * Display event when and where data
     * at top of post.
     * 
     * Author: Jesse Browne
     *         jb@greenpag.es
     */
    
    if ( is_single() && ( get_post_type() == 'gp_events') ) { 
		
        global $post;
		
		$display_start_day =           date('l', $post->gp_events_startdate);
		$display_start_date =          date('j', $post->gp_events_startdate);
		$display_start_date_suffix =   date('S', $post->gp_events_startdate);
		$display_start_month =         date('M', $post->gp_events_startdate);
		$str_start_month =             date('m', $post->gp_events_startdate);
		$display_start_year =          date('Y', $post->gp_events_startdate);

		$start_time =                  $post->gp_events_starttime;
		$end_time =                    $post->gp_events_endtime;
		
		$display_end_day =             date('j', $post->gp_events_enddate);
		$display_end_date_suffix =     date('S', $post->gp_events_enddate);
		$display_end_month =           date('M', $post->gp_events_enddate);
		$str_end_month =               date('m', $post->gp_events_enddate);
		
		$location =                    $post->gp_google_geo_location;

    	if ($post->gp_events_enddate != $post->gp_events_startdate) {
    	    if ( ($display_end_month == $display_start_month) && ($display_end_day != $display_start_day) ) {
    	        $end_date_diff_month = '';
    	        $end_date_same_month = '- '. $display_end_day . $display_end_date_suffix;    
    	    } else {
    	        $end_date_diff_month = ' - '. $display_end_day . $display_end_date_suffix .' '. $display_end_month;
    	        $end_date_same_month = '';
    	    }		    
		}
		
		$times =   ( !empty($start_time) ) ? $start_time.', ' : '';
		$times =   ( !empty($start_time) && !empty($end_time) ) ? $start_time .' to '. $end_time.', ' : '';
		
		$when =    $times . $display_start_day .', '. $display_start_date.
                   $display_start_date_suffix .' '. $end_date_same_month .' '. $display_start_month .
                   $end_date_diff_month .', '. $display_start_year;                                   
		$where =   (!empty($location)) ? $location : '';
        
		?>
		<div class="post-details"><?php echo $when; ?></div>
		<div class="clear"></div>
		<div class="post-details"><?php echo $where; ?></div>
		<div class="clear"></div>
		<?php 
    }
}

function theme_singlepagination() {
	/* NOT USED YET! */
	/* wp_link_pages( array( 'before' => '<div class="page-link">' . __( 'Pages:', 'twentyten' ), 'after' => '</div>' ) ); */
}

function theme_singlesocialbar() {
	if (get_post_type() != "page") { 
		global $post;
		$link = get_permalink($post->ID);
		$title = esc_attr(get_the_title($post->ID));	 
		?>
		<div id="gp_share">
		    <div id="gp_sharebar">
		    	<div id="gp_sharebox">
			    	<div class="wdt title">Share</div>			    	
			        <div class="wdt twitter">
			            <a href="http://twitter.com/share" class="twitter-share-button" data-text="<?php echo $title; ?>"  data-count="vertical" data-via="GreenPagesAu">Tweet</a>
			        </div>
			        <div class="wdt google-plus">
						<g:plusone size="tall"></g:plusone>
					</div>
			        <div class="wdt linkedin">
			        	<script src="//platform.linkedin.com/in.js" type="text/javascript"> 
			        		lang: en_US
						</script>
						<script type="IN/Share" data-counter="top"></script>
			        </div>			        		        		        	
			        <div class="wdt facebook">
			            <div class="fb-like" data-href="<?php echo $link; ?>" data-send="true" data-layout="box_count"></div>
			        </div>			        
			        <div class="wdt stumbleupon">
						<su:badge layout="5"></su:badge>
			        </div>
			        <div class="clear"></div>
		        </div>
		    </div>
		</div>
		<?php 
	}
}

function theme_singlecomments() {
	if ( comments_open() ) {
		echo '<a name="comments"></a>';
		#comments_template( '', true );
		?>
		<div id="facebook-comments">
			<h3 id="reply-title">Leave a Reply</h3>
			<fb:comments href="<?php the_permalink(); ?>" num_posts="10" width="567px"></fb:comments>
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
		echo '<div class="post-details">
		          By <a href="' . $post_author_url . '">' . $post_author->display_name . '</a> ' . time_ago(get_the_time('U'), 0) . ' ago
		      </div>';
	}
	
	if ($format == 'author') {
		echo '<div class="post-details">
		          By <a href="' . $post_author_url . '">' . $post_author->display_name . '</a> ' . time_ago(get_the_time('U'), 0) . ' ago
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
                      <a href="#/">
                          <span class="af-icon-chevron-up' . $likedclass . '"></span>
                          <span class="af-icon-chevron-up-number"' . $showlikecount . '>' . $likecount . '</span>
                          <span class="af-icon-chevron-up-number-plus-one" style="display:none;">+1</span>
                          <span class="af-icon-chevron-up-number-minus-one" style="display:none;">-1</span>
                      </a>
                  </div>';
		} else {
			echo '<div id="post-' . $post->ID . '" class="favourite-profile">
			          <a href="' . wp_login_url( "http://" . $_SERVER['HTTP_HOST']  . $_SERVER['REQUEST_URI'] ) . '" 
			             class="simplemodal-login">
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

/** INDEX FEED STYLE **/

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
	
	/** DISPLAY FEATURED IMAGE IF SET **/           
    if ( has_post_thumbnail() ) {
		$imageArray = wp_get_attachment_image_src( get_post_thumbnail_id($post->ID), 'homepage-thumbnail' );
		$imageURL = $imageArray[0];
		echo '<a href="' . $link_location_uri . '" class="profile_minithumb">
		          <img src="' . $imageURL  . '" alt="' . get_the_title( get_post_thumbnail_id($post->ID) ) . '"/>
		      </a>';
    }
    else {	/** DISPLAY LOGO/PROFILE PICTURE INSTEAD **/
		echo '<span class="profile_minithumb">
		          <a href="' . $post_author_url . '">' . 
    		          get_avatar( $post_author->ID, '110', '', $post_author->display_name ) . '
    		      </a>
    		  </span>';
	}
	
	echo '<div class="profile-postbox">';			 		
	
    $site_posttypes = Site::getPostTypes();
    foreach ( $site_posttypes as $site_posttype ) {
	    if ( $site_posttype['id'] == get_post_type() ) {
	        $post_title = $site_posttype['title'];
	        $post_url = $site_posttype['slug'];
	    }   
	}

	?>
    <h1 class="profile-title">
        <a href="<?php echo $link_location_uri; ?>"  title="<?php esc_attr(the_title()); ?>" rel="bookmark"><?php the_title(); ?></a>
    </h1>
    <?php
	
	/** DISPLAY POST AUTHOR, CATEGORY AND TIME POSTED DETAILS **/
	echo '<span class="hp_miniauthor">
			 By <a href="' . $post_author_url . '">' . $post_author->display_name . '</a> 
			 in <a href="/' . $post_url . '">' . $post_title . '</a> ' . time_ago(get_the_time('U'), 0) . ' ago
          </span>';
			
	if ( get_user_meta($current_user->ID, 'likepost_' . $current_site->id . '_' . $post->ID , true) ) {
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
                  <a href="#/" title="Upvote">
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

/*** TEMPLATE RENDERING ***/

function default_index() {
    global $wpdb, $post, $gp;

    $user_lat =               $gp->location['latitude'];
    $user_long =              $gp->location['longitude'];
    $user_city =              $gp->location['city'];
    $user_country =           $gp->location['country_iso2'];
    
	$location_city =          ( !empty($_GET['locality_filter']) ) ? $_GET['locality_filter'] : $user_city;
	$location_latitude =      ( !empty($_GET['latitude_filter']) ) ? $_GET['latitude_filter'] : $user_lat;
    $location_longitude =     ( !empty($_GET['longitude_filter']) ) ? $_GET['longitude_filter'] : $user_long;
    $location_country_slug =  ( !empty($_GET['location_slug_filter']) ) ? $_GET['location_slug_filter'] : $user_country;
    $location_state_slug =    ( !empty($_GET['location_state_filter']) ) ? $_GET['location_state_filter'] : '';
	
	$querystring_country =    strtoupper( $location_country_slug );
	$querystring_state =      ( !empty($location_state_slug) ) ? strtoupper( $location_state_slug ) : '';
	$querystring_city =       $location_city ;
	
    $geo_currentlocation = $gp->location;

    $epochtime =         strtotime('now');
    
    $filterby_country =  (!empty($querystring_country)) ? $wpdb->prepare( " m1.meta_value=%s ", $querystring_country ) : '';
    $filterby_state =    (!empty($querystring_state)) ? $wpdb->prepare( " OR m2.meta_value=%s ", $querystring_state ) : '';
    $filterby_city =     (!empty($querystring_city)) ? $wpdb->prepare( " OR m4.meta_value=%s ", $querystring_city ) : '';
    
    $querytotal = $wpdb->prepare(
        "SELECT COUNT(*) AS count 
        FROM $wpdb->posts 
            LEFT JOIN " . $wpdb->prefix . "postmeta AS m0 ON m0.post_id=" . $wpdb->prefix . "posts.ID AND m0.meta_key='_thumbnail_id'
            LEFT JOIN " . $wpdb->prefix . "postmeta AS m1 ON m1.post_id=" . $wpdb->prefix . "posts.ID AND m1.meta_key='gp_google_geo_country'
            LEFT JOIN " . $wpdb->prefix . "postmeta AS m2 ON m2.post_id=" . $wpdb->prefix . "posts.ID AND m2.meta_key='gp_google_geo_administrative_area_level_1'
            LEFT JOIN " . $wpdb->prefix . "postmeta AS m3 ON m3.post_id=" . $wpdb->prefix . "posts.ID AND m3.meta_key='gp_google_geo_locality_slug'
            LEFT JOIN " . $wpdb->prefix . "postmeta AS m4 ON m4.post_id=" . $wpdb->prefix . "posts.ID AND m4.meta_key='gp_google_geo_locality'
        WHERE 
            post_status='publish'
            AND m0.meta_value >= 1
            AND post_type=%s 
            AND (
                " . $filterby_country . "
                " . $filterby_state . "
                " . $filterby_city . "
            );",
            get_query_var('post_type')
        );
              
	$totalposts = $wpdb->get_results($querytotal, OBJECT);

	$ppp = 20;

	$wp_query->found_posts = $totalposts[0]->count;
	$wp_query->max_num_pages = ceil($wp_query->found_posts / $ppp);	
	$on_page = intval($querystring_page);	
	
	if ($on_page == 0) { $on_page = 1; }		
	$offset = ($on_page-1) * $ppp;

    /** SQL QUERIES SHOW LIST VIEW OF 20 MOST RECENT POSTS **/
    $querystr = $wpdb->prepare(
        "SELECT DISTINCT
            " . $wpdb->prefix . "posts.*,
            m0.meta_value AS _thumbnail_id,
            m1.meta_value AS gp_google_geo_country,
            m2.meta_value AS gp_google_geo_administrative_area_level_1,
            m3.meta_value AS gp_google_geo_locality_slug,
            m4.meta_value AS gp_google_geo_locality
        FROM $wpdb->posts
            LEFT JOIN " . $wpdb->prefix . "postmeta AS m0 ON m0.post_id=" . $wpdb->prefix . "posts.ID AND m0.meta_key='_thumbnail_id'
            LEFT JOIN " . $wpdb->prefix . "postmeta AS m1 ON m1.post_id=" . $wpdb->prefix . "posts.ID AND m1.meta_key='gp_google_geo_country'
            LEFT JOIN " . $wpdb->prefix . "postmeta AS m2 ON m2.post_id=" . $wpdb->prefix . "posts.ID AND m2.meta_key='gp_google_geo_administrative_area_level_1'
            LEFT JOIN " . $wpdb->prefix . "postmeta AS m3 ON m3.post_id=" . $wpdb->prefix . "posts.ID AND m3.meta_key='gp_google_geo_locality_slug'
            LEFT JOIN " . $wpdb->prefix . "postmeta AS m4 ON m4.post_id=" . $wpdb->prefix . "posts.ID AND m4.meta_key='gp_google_geo_locality'
        WHERE
            post_status='publish'
            AND m0.meta_value >= 1
            AND post_type=%s 
            AND (
                " . $filterby_country . "
                " . $filterby_state . "
                " . $filterby_city . "
            )
        ORDER BY post_date DESC",
        get_query_var('post_type')
    );

	$pageposts = $wpdb->get_results($querystr, OBJECT);
	$posttype_slug = getPostTypeSlug( get_query_var('post_type') );

	if ( $pageposts ) {
	    
	    $sorted_posts = array();
	    
		foreach ( $pageposts as $post ) {
		    setup_postdata($post);
			$c = distance_to_post($post, $location_latitude, $location_longitude);
			$popularity_score_thisuser = page_rank($c, $post);
			$popularity_score_thisuser = $post->popularity_score_thisuser + $popularity_score_thisuser;
            $sorted_posts[$popularity_score_thisuser] = $post; 
	    }

        # Sort posts by popularity score and get top 20
	    krsort($sorted_posts);
        $display_posts = array_slice($sorted_posts, 0, $ppp, true);
	    
        # Display home page feed 	
	    foreach ( $display_posts as $post ) { 
	        setup_postdata($post);		
		    theme_index_feed_item();
	    }
	    
	    if (  $wp_query->max_num_pages > 1 ) {
            $page_url = "/" . $posttype_slug . "/";
            if ( !empty( $querystring_country ) ) { $page_url .= strtolower($querystring_country) . '/'; }
            if ( !empty( $querystring_state ) ) { $page_url .= strtolower($querystring_state) . '/'; }
            if ( !empty( $querystring_city ) ) { $page_url .= $querystring_city . '/'; }
            
            if ( $on_page != $wp_query->max_num_pages ) { $previous = "<a href=\"" . $page_url . "page/" . ($on_page + 1) . "\"><div class=\"arrow-previous\"></div>Later in Time</a>"; }
            if ( $on_page != 1 ) { $next = "<a href=\"" . $page_url . "page/" . ($on_page - 1) . "\">Sooner in Time<div class=\"arrow-next\"></div></a>"; }
            if ( ( $on_page - 1 ) == 1 ) { $next = "<a href=\"" . $page_url . "\">Sooner in Time<div class=\"arrow-next\"></div></a>"; }
            
            ?>
			<nav id="post-nav">
				<ul>
					<li class="post-previous"><?php echo $previous; ?></li>
					<li class="post-next"><?php echo $next; ?></li>
				</ul>
			</nav>
		    <?php
		}	
	} else {
		echo '<h1 class="loop-title">We couldn\'t find what you were look for!</h1>
			  <p>No there\'s nothing wrong. It just means there\'s no posts for this section yet!</p>';
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
	    global $post; 
		the_post();
		echo '<article>';
			theme_create_post();
			theme_update_delete_post();
			theme_singletitle();
			theme_singlesocialbar();
			if ( get_user_role( array('contributor'), $post->post_author) ) {
				theme_singlecontributorstagline();
			} else {
				theme_singledetails();
			}
			theme_single_event_details();
			the_content();
			theme_singlepagination();
			theme_single_tags();
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

/** HOMEPAGE LIST VIEW OF 20 MOST RECENT POSTS - EXCLUDING EVENTS **/
function home_index() {
	global $wpdb, $post, $gp;

    $user_lat =               $gp->location['latitude'];
    $user_long =              $gp->location['longitude'];
    $user_city =              $gp->location['city'];
    $user_country =           $gp->location['country_iso2'];
    
	$location_city =          ( !empty($_GET['locality_filter']) ) ? $_GET['locality_filter'] : $user_city;
	$location_latitude =      ( !empty($_GET['latitude_filter']) ) ? $_GET['latitude_filter'] : $user_lat;
    $location_longitude =     ( !empty($_GET['longitude_filter']) ) ? $_GET['longitude_filter'] : $user_long;
    $location_country_slug =  ( !empty($_GET['location_slug_filter']) ) ? $_GET['location_slug_filter'] : $user_country;
    $location_state_slug =    ( !empty($_GET['location_state_filter']) ) ? $_GET['location_state_filter'] : '';
	
	$querystring_country =    strtoupper( $location_country_slug );
	$querystring_state =      ( !empty($location_state_slug) ) ? strtoupper( $location_state_slug ) : '';
	$querystring_city =       $location_city ;
	
	$querystring_page =       get_query_var( 'page' );
	
	$geo_currentlocation =    $gp->location;
	
	$epochtime =              strtotime('now');
	
    $filterby_country =       (!empty($querystring_country)) ? $wpdb->prepare( " m3.meta_value=%s ", $querystring_country ) : '';
    $filterby_state =         (!empty($querystring_state)) ? $wpdb->prepare( " OR m4.meta_value=%s ", $querystring_state ) : '';
    $filterby_city =          (!empty($querystring_city)) ? $wpdb->prepare( " OR m6.meta_value=%s ", $querystring_city ) : '';
	
	$ppp = 20;
	
	/** SQL QUERIES GET ALL RECENT POSTS FROM PAST TWO WEEKS **/         
    $querystr = $wpdb->prepare(
        "SELECT DISTINCT
            " . $wpdb->prefix . "posts.*,
            m0.meta_value AS _thumbnail_id,
            m1.meta_value AS gp_enddate,
            m2.meta_value AS gp_startdate,
            m3.meta_value AS gp_google_geo_country,
            m4.meta_value AS gp_google_geo_administrative_area_level_1,
            m5.meta_value AS gp_google_geo_locality_slug,
            m6.meta_value AS gp_google_geo_locality
        FROM $wpdb->posts
            LEFT JOIN " . $wpdb->prefix . "postmeta AS m0 ON m0.post_id=" . $wpdb->prefix . "posts.ID AND m0.meta_key='_thumbnail_id'
            LEFT JOIN " . $wpdb->prefix . "postmeta AS m1 ON m1.post_id=" . $wpdb->prefix . "posts.ID AND (m1.meta_key='gp_events_enddate' OR m1.meta_key='gp_competitions_enddate') 
            LEFT JOIN " . $wpdb->prefix . "postmeta AS m2 ON m2.post_id=" . $wpdb->prefix . "posts.ID AND (m2.meta_key='gp_events_startdate' OR m2.meta_key='gp_competitions_startdate') 
            LEFT JOIN " . $wpdb->prefix . "postmeta AS m3 ON m3.post_id=" . $wpdb->prefix . "posts.ID AND m3.meta_key='gp_google_geo_country'
            LEFT JOIN " . $wpdb->prefix . "postmeta AS m4 ON m4.post_id=" . $wpdb->prefix . "posts.ID AND m4.meta_key='gp_google_geo_administrative_area_level_1'
            LEFT JOIN " . $wpdb->prefix . "postmeta AS m5 ON m5.post_id=" . $wpdb->prefix . "posts.ID AND m5.meta_key='gp_google_geo_locality_slug'
            LEFT JOIN " . $wpdb->prefix . "postmeta AS m6 ON m6.post_id=" . $wpdb->prefix . "posts.ID AND m6.meta_key='gp_google_geo_locality'
        WHERE
            popularity_score > DATE_SUB(CURDATE(), INTERVAL 2 WEEK) 
        	AND post_status='publish'
            AND m0.meta_value >= 1
            AND (
                post_type='gp_news' 
                OR post_type='gp_advertorial'
                OR post_type='gp_projects' 
                OR ( post_type='gp_events' AND CAST(CAST(m1.meta_value AS UNSIGNED) AS SIGNED) >= %d ) 
            )
            AND (
                " . $filterby_country . "
                " . $filterby_state . "
                " . $filterby_city . "
            )
        ORDER BY post_date DESC",
        $epochtime,
        $epochtime
    );
    
	$pageposts = $wpdb->get_results($querystr, OBJECT);
	
	/** NEW LIST VIEW OF 20 POSTS WITH HIGHEST POPULARITY SCORE **/
	if ( $pageposts ) {
	    # Display create new post button
		theme_create_post();
		$sorted_posts = array();
		
		# Assign popularity score for all posts in last two weeks and store in array for sorting
		foreach ( $pageposts as $post ) { 
		    setup_postdata($post);
			$c = distance_to_post($post, $location_latitude, $location_longitude);
			$popularity_score_thisuser = page_rank($c, $post);
			$post->popularity_score_thisuser = $popularity_score_thisuser;
            $sorted_posts[$popularity_score_thisuser] = $post;                       
		}

		# Sort posts by popularity score and get top 20
	    krsort($sorted_posts);
        $display_posts = array_slice($sorted_posts, 0, $ppp, true);
        
        # Display home page feed 	
	    foreach ( $display_posts as $post ) { 
	        setup_postdata($post);		
		    theme_index_feed_item();
	    }
	}
	
	?>
	<nav id="post-nav">										
		<ul>
			<li class="post-previous"><a href="/news/page/2/"><div class="arrow-previous"></div>More Posts</a></li>
		</ul>
	</nav>
	<?php	 
}

function search_index() {
	default_index();
}

function news_index() {
	theme_create_post();
	default_index();
}

function events_index() {
	/**
	 * Get events feed based on either user location or
	 * location filter set by user.
	 * 
	 * Show 20 events on events page with pagination 
	 * if over 20 events found.
	 */
    
    global $wpdb, $post, $gp;
	
	$querystring_page = get_query_var( 'page' );
	
    $user_lat =               $gp->location['latitude'];
    $user_long =              $gp->location['longitude'];
    $user_city =              $gp->location['city'];
    $user_country =           $gp->location['country_iso2'];
    
	$location_city =          ( !empty($_GET['locality_filter']) ) ? $_GET['locality_filter'] : $user_city;
	$location_latitude =      ( !empty($_GET['latitude_filter']) ) ? $_GET['latitude_filter'] : $user_lat;
    $location_longitude =     ( !empty($_GET['longitude_filter']) ) ? $_GET['longitude_filter'] : $user_long;
    $location_country_slug =  ( !empty($_GET['location_slug_filter']) ) ? $_GET['location_slug_filter'] : $user_country;
    $location_state_slug =    ( !empty($_GET['location_state_filter']) ) ? $_GET['location_state_filter'] : '';
	
	$querystring_country =    strtoupper( $location_country_slug );
	$querystring_state =      ( !empty($location_state_slug) ) ? strtoupper( $location_state_slug ) : '';
	$querystring_city =       $location_city;
	
	$geo_currentlocation =    $gp->location;

	$filterby_city =     "";
	$filterby_state =    "";
	$filterby_country =  "";

	$epochtime =         strtotime('now');

    $filterby_country =  (!empty($querystring_country)) ? $wpdb->prepare( " AND m3.meta_value=%s ", $querystring_country ) : '';
    $filterby_state =    (!empty($querystring_state)) ? $wpdb->prepare( " AND m4.meta_value=%s ", $querystring_state ) : '';
    $filterby_city =     (!empty($querystring_city)) ? $wpdb->prepare( " AND m5.meta_value=%s ", $querystring_city ) : '';
	
    $querytotal = $wpdb->prepare(
                    "SELECT COUNT(*) AS count 
                    FROM $wpdb->posts 
                        LEFT JOIN " . $wpdb->prefix . "postmeta AS m0 on m0.post_id=" . $wpdb->prefix . "posts.ID and m0.meta_key='_thumbnail_id' 
                        LEFT JOIN " . $wpdb->prefix . "postmeta AS m1 on m1.post_id=" . $wpdb->prefix . "posts.ID and m1.meta_key='gp_events_enddate' 
                        LEFT JOIN " . $wpdb->prefix . "postmeta AS m2 on m2.post_id=" . $wpdb->prefix . "posts.ID and m2.meta_key='gp_events_startdate' 
                        LEFT JOIN " . $wpdb->prefix . "postmeta AS m3 on m3.post_id=" . $wpdb->prefix . "posts.ID and m3.meta_key='gp_google_geo_country' 
                        LEFT JOIN " . $wpdb->prefix . "postmeta AS m4 on m4.post_id=" . $wpdb->prefix . "posts.ID and m4.meta_key='gp_google_geo_administrative_area_level_1' 
                        LEFT JOIN " . $wpdb->prefix . "postmeta AS m5 on m5.post_id=" . $wpdb->prefix . "posts.ID and m5.meta_key='gp_google_geo_locality_slug'
                    WHERE 
                        post_status='publish' 
                        AND post_type='gp_events' 
                        " . $filterby_country . "
                        " . $filterby_state . "
                        " . $filterby_city . "
                        AND CAST(CAST(m1.meta_value AS UNSIGNED) AS SIGNED) >= %d;",
                    $epochtime
            );
                
	$totalposts = $wpdb->get_results($querytotal, OBJECT);

	$ppp = 20;

	$wp_query->found_posts = $totalposts[0]->count;
	$wp_query->max_num_pages = ceil($wp_query->found_posts / $ppp);	
	$on_page = intval($querystring_page);	

	if ($on_page == 0) { $on_page = 1; }		
	$offset = ($on_page-1) * $ppp;
	
    $querystr = $wpdb->prepare(
                "SELECT 
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
                ORDER BY gp_events_startdate ASC 
                LIMIT %d 
                OFFSET %d;",
                $epochtime,
                $ppp,
                $offset
            );

	$pageposts = $wpdb->get_results($querystr, OBJECT);
	
	theme_create_post();

	if ($pageposts) {    
	    
		foreach ($pageposts as $post) {
			setup_postdata($post);
			
			if ( !isset($post->gp_google_geo_locality) || empty($post->gp_google_geo_locality) ) {
			    continue;
			}

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
			              <a href="/events/' . strtolower($post->gp_google_geo_country) . '/' 
			                                 . strtolower($post->gp_google_geo_administrative_area_level_1) . '/' 
			                                 . $post->gp_google_geo_locality_slug . '/">' 
			                  . $post->gp_google_geo_locality . '
			              </a> | 
			              <a href="/events/' . strtolower($post->gp_google_geo_country) . '/' 
			                                 . strtolower($post->gp_google_geo_administrative_area_level_1) . '/">' 
			                  . $post->gp_google_geo_administrative_area_level_1 . '
			              </a>
			          </div>
			          <div class="clear"></div>
			      </div>';

			echo '</div><div class="clear"></div>';
					
		}		
		
		if (  $wp_query->max_num_pages > 1 ) {
            $page_url = "/events/";
            if ( !empty( $querystring_country ) ) { $page_url .= strtolower($querystring_country) . '/'; }
            if ( !empty( $querystring_state ) ) { $page_url .= strtolower($querystring_state) . '/'; }
            if ( !empty( $querystring_city ) ) { $page_url .= $querystring_city . '/'; }
            
            if ( $on_page != $wp_query->max_num_pages ) { $previous = "<a href=\"" . $page_url . "page/" . ($on_page + 1) . "\"><div class=\"arrow-previous\"></div>Later in Time</a>"; }
            if ( $on_page != 1 ) { $next = "<a href=\"" . $page_url . "page/" . ($on_page - 1) . "\">Sooner in Time<div class=\"arrow-next\"></div></a>"; }
            if ( ( $on_page - 1 ) == 1 ) { $next = "<a href=\"" . $page_url . "\">Sooner in Time<div class=\"arrow-next\"></div></a>"; }
            
            ?>
			<nav id="post-nav">
				<ul>
					<li class="post-previous"><?php echo $previous; ?></li>
					<li class="post-next"><?php echo $next; ?></li>
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
	
	theme_create_post();
		
	$epochtime = strtotime('now');
    
    $querytotal = "SELECT COUNT(*) as count FROM $wpdb->posts left join " . $wpdb->prefix . "postmeta as m0 on m0.post_id=" . $wpdb->prefix . "posts.ID and m0.meta_key='_thumbnail_id' left join " . $wpdb->prefix . "postmeta as m1 on m1.post_id=" . $wpdb->prefix . "posts.ID and m1.meta_key='gp_competitions_enddate' left join " . $wpdb->prefix . "postmeta as m2 on m2.post_id=" . $wpdb->prefix . "posts.ID and m2.meta_key='gp_competitions_startdate' WHERE post_status='publish' AND post_type='gp_competitions' and CAST(CAST(m2.meta_value AS UNSIGNED) AS SIGNED) <= " . $epochtime . " and CAST(CAST(m1.meta_value AS UNSIGNED) AS SIGNED) >= " . $epochtime . ";";
	$totalposts = $wpdb->get_results($querytotal, OBJECT);

	$ppp = intval(get_query_var('posts_per_page'));

	$wp_query->found_posts = $totalposts[0]->count;
	$wp_query->max_num_pages = ceil($wp_query->found_posts / $ppp);	
	$on_page = intval(get_query_var('page'));	

	if($on_page == 0){ $on_page = 1; }		
	$offset = ($on_page-1) * $ppp;
	
    $metas = array('_thumbnail_id', 'gp_competitions_enddate', 'gp_competitions_startdate');
	foreach ($metas as $i=>$meta_key) {
        $meta_fields[] = 'm' . $i . '.meta_value as ' . $meta_key;
        $meta_joins[] = ' left join ' . $wpdb->postmeta . ' as m' . $i . ' on m' . $i . '.post_id=' . $wpdb->posts . '.ID and m' . $i . '.meta_key="' . $meta_key . '"';
    }
    $querystr = "SELECT " . $wpdb->prefix . "posts.*, " .  join(',', $meta_fields) . " 
                 FROM $wpdb->posts ";
    $querystr .=  join(' ', $meta_joins);
    $querystr .= " WHERE post_status='publish' 
                       AND post_type='gp_competitions' 
                       AND CAST(CAST(m2.meta_value AS UNSIGNED) AS SIGNED) <= " . $epochtime . " 
                       AND CAST(CAST(m1.meta_value AS UNSIGNED) AS SIGNED) >= " . $epochtime . " 
                   ORDER BY gp_competitions_enddate ASC LIMIT $ppp OFFSET $offset;";

	$pageposts = $wpdb->get_results($querystr, OBJECT);
	
	if ($pageposts) {    
	    
		foreach ($pageposts as $post) {
			setup_postdata($post);
			$displaydate = get_competitiondate( strtotime('now'), $post->gp_competitions_enddate );
			theme_index_feed_item();
			echo $displaydate.'<div class="clear"></div>';		
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
	
	# LIST VIEW OF MEMBERS WITH SUBSCRIBER STATUS SHOWING DISPLAY NAME, JOB TITLE, EMPLOYER AND FIRST FEW WORDS OF PROJECTS I NEED HELP WITH
	
	global $current_user, $wpdb, $wp_roles;

	$query = "SELECT DISTINCT wp_users.ID 
	          FROM wp_users 
	              LEFT JOIN wp_usermeta on wp_usermeta.user_id=wp_users.ID 
	          WHERE wp_users.user_status = 0 
	              AND wp_usermeta.meta_key = 'wp_capabilities' 
	              AND wp_usermeta.meta_value RLIKE '[[:<:]]subscriber[[:>:]]' 
	          ORDER BY wp_users.user_registered DESC;";
  	$subscribers = $wpdb->get_results($query);
  	if ($subscribers) {
  		$member_string = '<div class="memberslist">';
  		
    	foreach($subscribers as $subscriber) {
      		$thisuser = get_userdata($subscriber->ID);
      		/**
      		 * DISPLAY PROFILE ONLY IF AT LEAST ONE OF EITHER:
 			 * PROJECTS I NEED HELP WITH, GREEN STUFF I'M INTO OR HOW I'D CHANGE THE WORLD
 			 * HAVE BEEN FILLED IN 
 			**/
      		
      		# Set empty variables for projects, stuff and change meta fields	
      		$this_user_project = '';
      		$this_user_change = '';
      		$this_user_stuff = '';
      		
      		# Check profile builder and original meta fields for values     		
      		# Check to see if 'Projects I Need Help With' filled in
            if (!empty($thisuser->custom_field_projects) ) {
      		    $this_user_project = $thisuser->custom_field_projects;
      		} elseif (!empty($thisuser->bio_projects) ) {
      		    $this_user_project = $thisuser->bio_projects;
      		} 
      		
      		# Check to see if 'How I'd Change The World' filled in
      		if (!empty($thisuser->custom_field_change) ) {
      		    $this_user_change = $thisuser->custom_field_change;
      		} elseif (!empty($thisuser->bio_change) ) {
      		    $this_user_change = $thisuser->bio_change;
      		}
      		
    	    # Check to see if 'Green Stuff I'm Into' filled in
      		if (!empty($thisuser->custom_field_greenstuff_tags) ) {
      		    $this_user_stuff = $thisuser->custom_field_greenstuff_tags;
      		} elseif (!empty($thisuser->bio_stuff) ) {
      		    $this_user_stuff = $thisuser->bio_stuff;
      		}      		
      		
      		# If any of above fields hold a value (i.e. have been filled in) grab member snapshot and add to string
    	    if ( ($this_user_project != '' ) || ($this_user_change != '') || ($this_user_stuff != '') ) {

    	        # Set member data fields for display       
    	        # Job title
    	        $this_user_job_title = '';
    	        if ( !empty($thisuser->custom_field_job_title) ) {
		            $this_user_job_title = $thisuser->custom_field_job_title;
	            } elseif ( !empty($thisuser->employment_jobtitle) ) {
		            $this_user_job_title = $thisuser->employment_jobtitle;
	            }
	            
	            # Employer
	            $this_user_job_employer = '';
    	        if ( !empty($thisuser->custom_field_employer) ) {
		            $this_user_job_employer = $thisuser->custom_field_employer;
	            } elseif ( !empty($thisuser->employment_currentemployer) ) {
		            $this_user_job_employer = $thisuser->employment_currentemployer;
	            }
    	        
    	        # Add user snapshot data to string 
	      		$member_string .= '<a href="' . get_author_posts_url($thisuser->ID) . '" title="Posts by "' . esc_attr($thisuser->display_name) . '">'; 
    	  		$member_string .= get_avatar( $thisuser->ID, '100', '', $thisuser->display_name );
      			$member_string .= '<span><div><h1>' . $thisuser->display_name .'</h1></div>';
	      		$member_string .= '<div>' . $this_user_job_title . '</div>';
    	  		$member_string .= '<div>' . $this_user_job_employer . '</div>';
      			$member_string .= insert_memberslist_excerpt($thisuser);
      			$member_string .= '</span></a>';
      		}
      		
      		# Reset projects, change and stuff to avoid duplicate / incorrect display
      		$this_user_project = '';
      		$this_user_change = '';
      		$this_user_stuff = '';    		
    	}
   	
        # Complete string, print to screen and reset	
        $member_string .= '</div><div class="clear"></div>';
   	    echo $member_string;
   	    $member_string = '';
  	}
}

function advertorial_index() {
	theme_create_post();
	default_index();
}

function projects_index() {
	theme_create_post();
	default_index();
}


/*** PROFILES ***/

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
	
	echo "<div class=\"profile-edit-title\">My Settings</div>";
	
	theme_authoreditnav();
	
	$rolesubscriber = 'subscriber';
	$roleauthor = 'author';
	$roleeditor = 'editor';
	$rolecontributor = 'contributor';
	
	if ( !get_user_role( array( $profiletypes_user['profiletypes'] ) ) && in_array( $profciletypes_user['profiletypes'], $profiletypes_values ) && in_array( $user_role, $profiletypes_values ) ) {
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

function author_account() {
	global $current_user;
	#var_dump($current_user);
	$user_id = $current_user->data->ID;
	$user_first_name = $current_user->data->first_name;
	$user_last_name = $current_user->data->last_name;
	
	echo "<div class=\"profile-edit-title\">My Settings</div>";
	
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
	echo "<div class=\"profile-edit-title\">My Settings</div>";
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
	echo "<div class=\"profile-edit-title\">My Settings</div>";
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

function author_newsletters() {
	global $current_user, $current_site, $gp, $wpdb;
	
	echo "<div class=\"profile-edit-title\">My Settings</div>";
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
			$profile_author = get_profile_author();
			
			$subscription_user = get_the_author_meta( $wpdb->prefix . 'subscription', $profile_author->ID );

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
						<th>' . $value['profile_text'] . '</th>
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
	echo "<div class=\"profile-edit-title\">My Settings</div>";
	theme_authoreditnav();
	?>
	<p>Sorry, nothing here yet!</p>
	<?php
}

function author_password() {
	echo "<div class=\"profile-edit-title\">My Settings</div>";
	theme_authoreditnav();
	?>
	<p>Sorry, nothing here yet!</p>
	<?php
}

function author_admin() {
	echo "<div class=\"profile-edit-title\">My Settings</div>";
	theme_authoreditnav();
	?>
	<p>Sorry, nothing here yet!</p>
	<?php
}

function theme_authoreditnav() {
	global $current_user;
	$authoredit_page = get_query_var( 'author_edit' );
	
	$profile_author = get_profile_author();
	$profile_author_url = get_author_posts_url($profile_author->ID);
	
	?>
	<nav id="adv-tools">
		<ul>
			<li><a href="<?php echo $profile_author_url; ?>edit"<?php if ( $authoredit_page == 1 ){echo ' class="active"';} ?>>Profile</a></li>
			<li><a href="<?php echo $profile_author_url; ?>edit/account"<?php if ( $authoredit_page == 2 ){echo ' class="active"';} ?>>Account</a></li>
			<li><a href="<?php echo $profile_author_url; ?>edit/password"<?php if ( $authoredit_page == 7 ){echo ' class="active"';} ?>>Password</a></li>
			<li><a href="<?php echo $profile_author_url; ?>edit/locale"<?php if ( $authoredit_page == 3 ){echo ' class="active"';} ?>>Locale</a></li>
			<li><a href="<?php echo $profile_author_url; ?>edit/notifications"<?php if ( $authoredit_page == 4 ){echo ' class="active"';} ?>>Notifications</a></li>
			<li><a href="<?php echo $profile_author_url; ?>edit/newsletters"<?php if ( $authoredit_page == 5 ){echo ' class="active"';} ?>>Newsletters</a></li>
			<li><a href="<?php echo $profile_author_url; ?>edit/privacy"<?php if ( $authoredit_page == 6 ){echo ' class="active"';} ?>>Privacy</a></li>
			<?php
				if ( is_user_logged_in() && get_user_role( array('administrator') ) ) {
					?>
					<li class="adv-tools-admin"><a href="<?php echo $profile_author_url; ?>edit/admin"<?php if ( $authoredit_page == 8 ){echo ' class="active"';} ?>>Admin</a></li>
					<?php
				}
			?>
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
    /**
     * Display member job title on member profile page
     * from either profile builder field of original meta field   
     **/
	if ( !empty($profile_author->custom_field_job_title) ) {
		echo '<div class="author-position">Postition: ' . $profile_author->custom_field_job_title . '</div>';
	} elseif ( !empty($profile_author->employment_jobtitle) ) {
		echo '<div class="author-position">Position: ' . $profile_author->employment_jobtitle . '</div>';
	} 
}

function theme_author_employer($profile_author) {
    /**
     * Display member employer on member profile page
     * from either profile builder field of original meta field   
     **/
	if ( !empty($profile_author->custom_field_employer) ) {
		echo '<div class="author-employer">Employer: ' . $profile_author->custom_field_employer . '</div>';
	} elseif ( !empty($profile_author->employment_currentemployer) ) {
		echo '<div class="author-employer">Employer: ' . $profile_author->employment_currentemployer . '</div>';
	} 
}

function theme_authorlocation($profile_author) {
    /** Member location **/
	echo '<div class="author-location">Location: ' . $profile_author->location . '</div>';
}

function theme_authoremail($profile_author) {
    /** Display member email on profile - not in use **/
	if ( is_user_logged_in() && !empty($profile_author->user_email) ) {
		echo '<a href="mailto://' . str_replace('@', '[at]', $profile_author->user_email) . '" class="author-email">
		          <img src="' . get_bloginfo('template_url') . '/template/socialmediaicons_v170/email-16x16.png" />
		      </a>';
	}
}

function theme_authorfacebook($profile_author) {
    /**
     * Display facebook icon and link to member facebook page on member profile page
     * from either profile builder field of original meta field   
     **/    

    $profile_author_id = $profile_author->ID;
    
	if ( !empty($profile_author->custom_field_facebook) ) {
		$profile_author_facebook = $profile_author->custom_field_facebook;
		$click_track_tag = '\'/outbound/profile-facebook/' . $profile_author_id .'/\'';
		echo '<a href="' . $profile_author->custom_field_facebook . '" target="_new" onClick="_gaq.push([\'_trackPageview\', ' . $click_track_tag . ']);" class="author-facebook"><img src="' . get_bloginfo('template_url') . '/template/socialmediaicons_v170/facebook-16x16.png" /></a>';    
	} elseif ( !empty($profile_author->facebook) ) {
		$profile_author_facebook = $profile_author->facebook;
		$click_track_tag = '\'/outbound/profile-facebook/' . $profile_author_id .'/\'';
		echo '<a href="' . $profile_author->facebook . '" target="_new" onClick="_gaq.push([\'_trackPageview\', ' . $click_track_tag . ']);" class="author-facebook"><img src="' . get_bloginfo('template_url') . '/template/socialmediaicons_v170/facebook-16x16.png" /></a>';
	}
}

function theme_authorlinkedin($profile_author) {
    /**
     * Display Linkedin icon and link to member linkedin page on member profile page
     * from either profile builder field of original meta field   
     **/      
    $profile_author_id = $profile_author->ID;
    
    if ( !empty($profile_author->custom_field_linkedin) ) {	
		$profile_author_linkedin = $profile_author->custom_field_linkedin;
		$click_track_tag = '\'/outbound/profile-linkedin/' . $profile_author_id .'/\'';
		echo '<a href="' . $profile_author->custom_field_linkedin . '" target="_new" onClick="_gaq.push([\'_trackPageview\', ' . $click_track_tag . ']);" class="author-linkedin"><img src="' . get_bloginfo('template_url') . '/template/socialmediaicons_v170/linkedin-16x16.png" /></a>';
	} elseif ( !empty($profile_author->linkedin) ) {	
		$profile_author_linkedin = $profile_author->linkedin;
		$click_track_tag = '\'/outbound/profile-linkedin/' . $profile_author_id .'/\'';
		echo '<a href="' . $profile_author->linkedin . '" target="_new" onClick="_gaq.push([\'_trackPageview\', ' . $click_track_tag . ']);" class="author-linkedin"><img src="' . get_bloginfo('template_url') . '/template/socialmediaicons_v170/linkedin-16x16.png" /></a>';
	}
}

function theme_authortwitter($profile_author) {
    /**
     * Display Twitter icon and link to member Twitter account on member profile page
     * from either profile builder field of original meta field   
     **/     
    $profile_author_id = $profile_author->ID;

	if ( !empty($profile_author->custom_field_twitter) ) {	
		$profile_author_twitter = $profile_author->custom_field_twitter;
		$click_track_tag = '\'/outbound/profile-twitter/' . $profile_author_id .'/\'';
		echo '<a href="http://www.twitter.com/' .$profile_author->custom_field_twitter . '" target="_new" onClick="_gaq.push([\'_trackPageview\', ' . $click_track_tag . ']);" class="author-twitter"><img src="' . get_bloginfo('template_url') . '/template/socialmediaicons_v170/twitter-16x16.png" /></a>';
	} elseif ( !empty($profile_author->twitter) ) {
		$profile_author_twitter = $profile_author->twitter;
		$click_track_tag = '\'/outbound/profile-twitter/' . $profile_author_id .'/\'';
		echo '<a href="http://www.twitter.com/' .$profile_author->twitter . '" target="_new" onClick="_gaq.push([\'_trackPageview\', ' . $click_track_tag . ']);" class="author-twitter"><img src="' . get_bloginfo('template_url') . '/template/socialmediaicons_v170/twitter-16x16.png" /></a>';
	}
}

function theme_authorskype($profile_author) {
    /**
     * Display Skype icon and link to member Skype account on member profile page
     * from either profile builder field of original meta field   
     **/      
    $profile_author_id = $profile_author->ID;
    
	if ( is_user_logged_in() && !empty($profile_author->custom_field_skype) ) {
		#$skype_viewers = array('administrator', 'contributor', 'author', 'editor');
		#if ( get_user_role($skype_viewers, $profile_author->ID) )  {
			$profile_author_skype = $profile_author->custom_field_skype;
			$click_track_tag = '\'/outbound/profile-skype/' . $profile_author_id .'/\'';			
			echo '<a href="callto://' .$profile_author->custom_field_skype . '" onClick="_gaq.push([\'_trackPageview\', ' . $click_track_tag . ']);" class="author-skype"><img src="' . get_bloginfo('template_url') . '/template/socialmediaicons_v170/skype-16x16.png" /></a>';
		#} 
	} elseif ( is_user_logged_in() && !empty($profile_author->skype) ) {
		#$skype_viewers = array('administrator', 'contributor', 'author', 'editor');
		#if ( get_user_role($skype_viewers, $profile_author->ID) )  {
			$profile_author_skype = $profile_author->skype;
			$click_track_tag = '\'/outbound/profile-skype/' . $profile_author_id .'/\'';			
			echo '<a href="callto://' .$profile_author->skype . '" onClick="_gaq.push([\'_trackPageview\', ' . $click_track_tag . ']);" class="author-skype"><img src="' . get_bloginfo('template_url') . '/template/socialmediaicons_v170/skype-16x16.png" /></a>';
		#} 
	}
}

function theme_authorrss($profile_author) {
	echo '<a href="" class="author-rss">
	          <img src="' . get_bloginfo('template_url') . '/template/socialmediaicons_v170/feed-16x16.png" />
	      </a>';
}

function theme_authorwww($profile_author) {
    /** Display website link to member website on member profile page **/
	if ( !empty($profile_author->user_url) ) {
		$profile_author_id = $profile_author->ID;
		$profile_author_url = $profile_author->user_url;
		$click_track_tag = '\'/outbound/profile-website/' . $profile_author_id .'/\'';
		echo '<div class="author-www">Website: <a href="' . $profile_author->user_url . '" target="_new" onClick="_gaq.push([\'_trackPageview\', ' . $click_track_tag . ']);">' . $profile_author->user_url . '</a></div>';
	}
}

function theme_authorviews($profile_author) {
	$profile_author = get_profile_author();
	
	$profile_views = get_user_option( 'profile_views', $profile_author->ID );
	if ( !$profile_views || !is_numeric($profile_views) ) {$profile_views = 0;}
	
	#echo "<div class=\"author-views\">Profile Views: <span>{$profile_views}</span></div>";
}

function theme_authorbio($profile_author) {
    /** Display member bio on member profile page **/
    if ( !empty($profile_author->description) ) {
		echo '<p>' . nl2br($profile_author->description) . '</p>';
	}
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
		$product_call = $custom["gp_advertorial_call_to_action"][0];
	 	$product_url = $custom["gp_advertorial_product_url"][0];
	 	$post_author = get_userdata($post->post_author);
	 	$post_id = $post->ID;
	 	$post_author_id = $post_author->ID;
	 	
	 	if ( !empty($product_url) && ($product_url != 'http://')) {
		?>
		<div id="post-product-button-bar">
			<?php
			$click_track_tag = '\'/outbound/product-button/' . $post_id . '/' . $post_author_id . '/' . $product_url .'/\'';
			echo '<a href="' . $product_url . '" target="_blank" onClick="_gaq.push([\'_trackPageview\', ' . $click_track_tag . ']);"><span id="product-button">'. $product_call .'</span></a>';
			?>			
		</div>
		<div class="clear"></div>
		<?php
	 	}
	}
}

/** CONTRIBUTOR / CONTENT PARTNER DONATE | JOIN | SEND LETTER | SIGN PETITION | VOLUNTEER BARS **/

function theme_profile_contributor_donate_join_bar($profile_author){
	if (get_post_type() != "page") { 
		global $post;	
		$post_author = $profile_author;
		$post_author_id = $post_author->ID;
		$donate_url = $post_author->contributors_donate_url;
		$join_url = $post_author->contributors_join_url;
		$petition_url = $post_author->contributors_petition_url;
		$volunteer_url = $post_author->contributors_volunteer_url;
		
		?>
		<div id="post-donate-join-bar">
			<?php
			theme_contributors_donate($donate_url, $post_author_id);
			theme_contributors_join($join_url, $post_author_id);
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
		$petition_url = $post_author->contributors_petition_url;
		$volunteer_url = $post_author->contributors_volunteer_url;
		
		?>
		<div id="index-donate-join-bar">
			<?php
			theme_contributors_donate($donate_url, $post_author_id);
			theme_contributors_join($join_url, $post_author_id);
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
		$petition_url = $post_author->contributors_petition_url;
		$volunteer_url = $post_author->contributors_volunteer_url;
		
		?>
		<h4>Would you like to help <a href="<?php echo $post_author_url ?>"><?php echo $post_author->display_name ?></a> change the world?</h4>
		<div id="post-donate-join-bar">
			<?php
			theme_contributors_donate($donate_url, $post_author_id);
			theme_contributors_join($join_url, $post_author_id);
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
		echo '<a href="' . $donate_url . '" target="_blank" onClick="_gaq.push([\'_trackPageview\', ' . $click_track_tag . ']);"><span id="donate">Donate</span></a>';
	}
}

function theme_contributors_join($join_url, $post_author_id) {
	if ( !empty($join_url) ) {
		$click_track_tag = '\'/outbound/activist-join-button/' . $post_author_id . '/' . $join_url .'/\'';
		echo '<a href="' . $join_url . '" target="_blank" onClick="_gaq.push([\'_trackPageview\', ' . $click_track_tag . ']);"><span id="join">Join</span></a>';
	}
}

function theme_contributors_petition($petition_url, $post_author_id) {
	if ( !empty($petition_url) ) {
		$click_track_tag = '\'/outbound/activist-petition-button/' . $post_author_id . '/' . $petition_url .'/\'';
		echo '<a href="'. $petition_url .'" target="_blank" onClick="_gaq.push([\'_trackPageview\', ' . $click_track_tag . ']);"><span id="petition">Sign Petition</span></a>';
	}
}

function theme_contributors_volunteer($volunteer_url, $post_author_id) {
	if ( !empty($volunteer_url) ) {
		$click_track_tag = '\'/outbound/activist-volunteer-button/' . $post_author_id . '/' . $volunteer_url .'/\'';
		echo '<a href="'. $volunteer_url .'" target="_blank" onClick="_gaq.push([\'_trackPageview\', ' . $click_track_tag . ']);"><span id="volunteer">Volunteer</span></a>';
	}
}

/** ENDS - DONATE | JOIN | SEND LETTER | SIGN PETITION | VOLUNTEER BUTTONS **/

function theme_editorsblurb($profile_author) {
	if ( !empty($profile_author->editors_blurb) ) {
		echo '<p>' . nl2br($profile_author->editors_blurb) . '</p>';
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
    /**
     * Display member 'How I would Change The World' on member profile page
     * from either profile builder field of original meta field   
     **/
	if (!empty($profile_author->custom_field_change)) {
		echo '<h1>How I Would Change the World</h1>';
		echo '<p>' . $profile_author->custom_field_change . '</p>';
	} elseif (!empty($profile_author->bio_change)) {
		echo '<h1>How I Would Change the World</h1>';
		echo '<p>' . $profile_author->bio_change . '</p>';
	}
}

function theme_authorsprojects($profile_author) {
    /**
     * Display member 'Green Stuff I Need Help With' on member profile page
     * from either profile builder field of original meta field   
     **/
	if (!empty($profile_author->custom_field_projects)) {
		echo '<h1>Green Projects I Need Help With</h1>';
		echo '<p>' . $profile_author->custom_field_projects . '</p>';
	} elseif (!empty($profile_author->bio_projects)) {
		echo '<h1>Green Projects I Need Help With</h1>';
		echo '<p>' . $profile_author->bio_projects . '</p>';
	}	
}

function theme_authorsstuff($profile_author) {    
    /**
     * Display member 'Green Stuff I'm Into' on member profile page
     * from either profile builder field of original meta field   
     **/
	if (!empty($profile_author->custom_field_greenstuff_tags)) {
		echo '<h1>Green Stuff I\'m Into</h1>';
		echo '<p>' . $profile_author->custom_field_greenstuff_tags . '</p>';
	} elseif (!empty($profile_author->bio_stuff)) {
		echo '<h1>Green Stuff I\'m Into</h1>';
		echo '<p>' . $profile_author->bio_stuff . '</p>';
	}	
}

/** Short excerpt of member meta field for people_index() **/
function insert_memberslist_excerpt($member) {
    /**
     *  Prints to screen first 135 characters of either 'Projects I Need Help With',
     *  'How I'd Change The World' of 'Green Stuff I'm Into' - whichever is found
     *  to be not empty first - as part of member snapshot in people section index page.
     *  Called by people_index()
     */
    
    # Checks both profile builder field and original user meta field for each type
	if (!empty($member->custom_field_projects)) {	
		return '<div><p><strong>Needs Help With: </strong>' . substr($member->custom_field_projects, 0, 135) . ' <strong>... Learn More ...</strong></p></div>';
	} elseif (!empty($member->bio_projects)) {	
		return '<div><p><strong>Needs Help With: </strong>' . substr($member->bio_projects, 0, 135) . ' <strong>... Learn More ...</strong></p></div>';
	} elseif (!empty($member->custom_field_change)) {	
		return '<div><p><strong>Would Change World By: </strong>' . substr($member->custom_field_change, 0, 130) . ' <strong>... Learn More ...</strong></p></div>';
	} elseif (!empty($member->bio_change)) {	
		return '<div><p><strong>Would Change World By: </strong>' . substr($member->bio_change, 0, 130) . ' <strong>... Learn More ...</strong></p></div>';
	} elseif (!empty($member->custom_field_greenstuff_tags)) {	
		return '<div><p><strong>Is Into: </strong>' . substr($member->custom_field_greenstuff_tags, 0, 140) . ' <strong>... Learn More ...</strong></p></div>';
	} elseif (!empty($member->bio_stuff)) {	
		return '<div><p><strong>Is Into: </strong>' . substr($member->bio_stuff, 0, 140) . ' <strong>... Learn More ...</strong></p></div>';
	}
}

function theme_subscribertabs($profile_author) {
	global $current_user;
	
	$post_author_url = get_author_posts_url($profile_author->ID);
	$template_path = get_bloginfo('template_url') . "/template/";
	
	if (get_the_author_meta( 'directory_page_url', $profile_author->ID )) {
		$directory_page_url_redirect = get_the_author_meta( 'directory_page_url', $profile_author->ID );
		$directory_page_url_redirect = "<li><a href=\"{$directory_page_url_redirect}\">Directory</a></li>";
		$directory_page_url = "<li><a href=\"{$post_author_url}#tab:posts;post:directory;\">Directory</a></li>";
	}
	
	# User is logged in and IS viewing their own profile
	if ( ( is_user_logged_in() ) && ( $current_user->ID == $profile_author->ID ) || get_user_role( array('administrator') ) ) {
		echo "
			<nav class=\"profile-tabs\"><ul>
				<li><a href=\"{$post_author_url}#tab:posts\" class=\"profile-tab-active\">My Posts</a></li>
				<li><a href=\"{$post_author_url}#tab:favourites\">Favourites</a></li>
				<li><a href=\"{$post_author_url}#tab:topics\">Topics</a></li>
				<li><a href=\"{$post_author_url}#tab:following\">Following</a></li>
				<li class=\"profile-tab-man\"><a href=\"{$post_author_url}#tab:advertise\">Advertise</a></li>
	            <li class=\"profile-tab-man\"><a href=\"{$post_author_url}#tab:analytics\">Analytics</a></li>
			</ul></nav>
			<div class=\"clear\"></div>
			<nav class=\"profile-tab-posts\"><ul>
				<li><a href=\"{$post_author_url}#tab:posts;post:all;\" class=\"profile-tab-secondary-active\">All</a></li>
				{$directory_page_url}
				<li><a href=\"{$post_author_url}#tab:posts;post:news;\">News</a></li>
				<li><a href=\"{$post_author_url}#tab:posts;post:events;\">Events</a></li>
				<li><a href=\"{$post_author_url}#tab:posts;post:eco-friendly-products;\">Products</a></li>
				<li><a href=\"{$post_author_url}#tab:posts;post:competitions;\">Competitions</a></li>
				<li><a href=\"{$post_author_url}#tab:posts;post:projects;\">Projects</a></li>
			</ul></nav>
			 <nav class=\"profile-tab-favourites\"><ul>
				<li><a href=\"{$post_author_url}#tab:favourites;post:all;\" class=\"profile-tab-secondary-active\">All</a></li>
				<!--<li><a href=\"{$post_author_url}#tab:favourites;post:directory;\">Directory</a></li>//-->
				<li><a href=\"{$post_author_url}#tab:favourites;post:news;\">News</a></li>
				<li><a href=\"{$post_author_url}#tab:favourites;post:events;\">Events</a></li>
				<li><a href=\"{$post_author_url}#tab:favourites;post:eco-friendly-products;\">Products</a></li>
				<li><a href=\"{$post_author_url}#tab:favourites;post:competitions;\">Competitions</a></li>
				<li><a href=\"{$post_author_url}#tab:favourites;post:projects;\">Projects</a></li>
			</ul></nav>
	        <div class=\"clear\"></div>
			<div class=\"profile-timeout top\">ERROR: Timeout <a href=\"\">Try refreshing</a>.</div>
	        <div class=\"profile-loading top\">Loading...<img src=\"{$template_path}loading-16x16-lblue.gif\" alt=\"Loading\" /></div>
	        <div class=\"profile-container\"></div>
	        <div class=\"profile-timeout bottom\">ERROR: Timeout <a href=\"\">Try refreshing</a>.</div>
	        <div class=\"profile-loading bottom\">Loading...<img src=\"{$template_path}loading-16x16-lblue.gif\" alt=\"Loading\" /></div>
		";
	}
	
	# User is logged in and IS NOT viewing their own profile OR user is not logged in
	if ( ( ( is_user_logged_in() ) && ( $current_user->ID != $profile_author->ID ) && !get_user_role( array('administrator') ) ) || ( !is_user_logged_in() ) ) {
		echo "
			<nav class=\"profile-tabs\"><ul>
				<li><a href=\"{$post_author_url}#tab:posts\" class=\"profile-tab-active\">Posts</a></li>
				<li><a href=\"{$post_author_url}#tab:favourites\" class=\"profile-tab-active\">Favourites</a></li>
				<li><a href=\"{$post_author_url}#tab:topics\">Topics</a></li>
				<li><a href=\"{$post_author_url}#tab:following\">Following</a></li>
			</ul></nav>
			<div class=\"clear\"></div>
			<nav class=\"profile-tab-posts\"><ul>
				<li><a href=\"{$post_author_url}#tab:posts;post:all;\" class=\"profile-tab-secondary-active\">All</a></li>
				{$directory_page_url_redirect}
				<li><a href=\"{$post_author_url}#tab:posts;post:news;\">News</a></li>
				<li><a href=\"{$post_author_url}#tab:posts;post:events;\">Events</a></li>
				<li><a href=\"{$post_author_url}#tab:posts;post:eco-friendly-products;\">Products</a></li>
				<li><a href=\"{$post_author_url}#tab:posts;post:competitions;\">Competitions</a></li>
				<li><a href=\"{$post_author_url}#tab:posts;post:projects;\">Projects</a></li>
			</ul></nav>
			 <nav class=\"profile-tab-favourites\"><ul>
				<li><a href=\"{$post_author_url}#tab:favourites;post:all;\" class=\"profile-tab-secondary-active\">All</a></li>
				<!--<li><a href=\"{$post_author_url}#tab:favourites;post:directory;\">Directory</a></li>//-->
				<li><a href=\"{$post_author_url}#tab:favourites;post:news;\">News</a></li>
				<li><a href=\"{$post_author_url}#tab:favourites;post:events;\">Events</a></li>
				<li><a href=\"{$post_author_url}#tab:favourites;post:eco-friendly-products;\">Products</a></li>
				<li><a href=\"{$post_author_url}#tab:favourites;post:competitions;\">Competitions</a></li>
				<li><a href=\"{$post_author_url}#tab:favourites;post:projects;\">Projects</a></li>
			</ul></nav>
	        <div class=\"clear\"></div>
			<div class=\"profile-timeout top\">ERROR: Timeout <a href=\"\">Try refreshing</a>.</div>
	        <div class=\"profile-loading top\">Loading...<img src=\"{$template_path}loading-16x16-lblue.gif\" alt=\"Loading\" /></div>
	        <div class=\"profile-container\"></div>
	        <div class=\"profile-timeout bottom\">ERROR: Timeout <a href=\"\">Try refreshing</a>.</div>
	        <div class=\"profile-loading bottom\">Loading...<img src=\"{$template_path}loading-16x16-lblue.gif\" alt=\"Loading\" /></div>
		";
	}
}

function theme_editortabs($profile_author) {
	global $current_user;
	
	$post_author_url = get_author_posts_url($profile_author->ID);
	$template_path = get_bloginfo('template_url') . "/template/";
	
	if (get_the_author_meta( 'directory_page_url', $profile_author->ID )) {
		$directory_page_url_redirect = get_the_author_meta( 'directory_page_url', $profile_author->ID );
		$directory_page_url_redirect = "<li><a href=\"{$directory_page_url_redirect}\">Directory</a></li>";
		$directory_page_url = "<li><a href=\"{$post_author_url}#tab:posts;post:directory;\">Directory</a></li>";
	}
	unset($current_user, $post_author_url, $template_path, $directory_page_url_redirect);
	# User is logged in and IS viewing their own profile
	if ( ( is_user_logged_in() ) && ( $current_user->ID == $profile_author->ID ) || get_user_role( array('administrator') ) ) {
		echo "
	        <nav class=\"profile-tabs\">
	            <ul>
	                <li><a href=\"{$post_author_url}#tab:posts\" class=\"profile-tab-active\">My Posts</a></li>
	                <li><a href=\"{$post_author_url}#tab:favourites\">Favourites</a></li>
	                <li><a href=\"{$post_author_url}#tab:topics\">Topics</a></li>
	                <li><a href=\"{$post_author_url}#tab:following\">Following</a></li>
	                <li class=\"profile-tab-man\"><a href=\"{$post_author_url}#tab:advertise\">Advertise</a></li>
	                <li class=\"profile-tab-man\"><a href=\"{$post_author_url}#tab:analytics\">Analytics</a></li>
	            </ul>
	        </nav>
	        <div class=\"clear\"></div>
	        <nav class=\"profile-tab-posts\"><ul>
				<li><a href=\"{$post_author_url}#tab:posts;post:all;\" class=\"profile-tab-secondary-active\">All</a></li>
				{$directory_page_url}
				<li><a href=\"{$post_author_url}#tab:posts;post:news;\">News</a></li>
				<li><a href=\"{$post_author_url}#tab:posts;post:events;\">Events</a></li>
				<li><a href=\"{$post_author_url}#tab:posts;post:eco-friendly-products;\">Products</a></li>
				<li><a href=\"{$post_author_url}#tab:posts;post:competitions;\">Competitions</a></li>
				<li><a href=\"{$post_author_url}#tab:posts;post:projects;\">Projects</a></li>
			</ul></nav>
			 <nav class=\"profile-tab-favourites\"><ul>
				<li><a href=\"{$post_author_url}#tab:favourites;post:all;\" class=\"profile-tab-secondary-active\">All</a></li>
				<!--<li><a href=\"{$post_author_url}#tab:favourites;post:directory;\">Directory</a></li>//-->
				<li><a href=\"{$post_author_url}#tab:favourites;post:news;\">News</a></li>
				<li><a href=\"{$post_author_url}#tab:favourites;post:events;\">Events</a></li>
				<li><a href=\"{$post_author_url}#tab:favourites;post:eco-friendly-products;\">Products</a></li>
				<li><a href=\"{$post_author_url}#tab:favourites;post:competitions;\">Competitions</a></li>
				<li><a href=\"{$post_author_url}#tab:favourites;post:projects;\">Projects</a></li>
			</ul></nav>
	        <div class=\"clear\"></div>
	        <div class=\"profile-timeout top\">ERROR: Timeout <a href=\"\">Try refreshing</a>.</div>
	        <div class=\"profile-loading top\">Loading...<img src=\"{$template_path}loading-16x16-lblue.gif\" alt=\"Loading\" /></div>
	        <div class=\"profile-container\"></div>
	        <div class=\"profile-timeout bottom\">ERROR: Timeout <a href=\"\">Try refreshing</a>.</div>
	        <div class=\"profile-loading bottom\">Loading...<img src=\"{$template_path}loading-16x16-lblue.gif\" alt=\"Loading\" /></div>
		";
	}
	
	# User is logged in and IS NOT viewing their own profile OR user is not logged in
	if ( ( ( is_user_logged_in() ) && ( $current_user->ID != $profile_author->ID ) && !get_user_role( array('administrator') ) ) || ( !is_user_logged_in() ) ) {
		echo "
	        <nav class=\"profile-tabs\">
	            <ul>
	                <li><a href=\"{$post_author_url}#tab:posts\" class=\"profile-tab-active\">Posts</a></li>
	                <li><a href=\"{$post_author_url}#tab:favourites\">Favourites</a></li>
	                <li><a href=\"{$post_author_url}#tab:topics\">Topics</a></li>
	                <li><a href=\"{$post_author_url}#tab:following\">Following</a></li>
	            </ul>
	        </nav>
	        <div class=\"clear\"></div>
	        <nav class=\"profile-tab-posts\"><ul>
				<li><a href=\"{$post_author_url}#tab:posts;post:all;\" class=\"profile-tab-secondary-active\">All</a></li>
				{$directory_page_url_redirect}
				<li><a href=\"{$post_author_url}#tab:posts;post:news;\">News</a></li>
				<li><a href=\"{$post_author_url}#tab:posts;post:events;\">Events</a></li>
				<li><a href=\"{$post_author_url}#tab:posts;post:eco-friendly-products;\">Products</a></li>
				<li><a href=\"{$post_author_url}#tab:posts;post:competitions;\">Competitions</a></li>
				<li><a href=\"{$post_author_url}#tab:posts;post:projects;\">Projects</a></li>
			</ul></nav>
			 <nav class=\"profile-tab-favourites\"><ul>
				<li><a href=\"{$post_author_url}#tab:favourites;post:all;\" class=\"profile-tab-secondary-active\">All</a></li>
				<!--<li><a href=\"{$post_author_url}#tab:favourites;post:directory;\">Directory</a></li>//-->
				<li><a href=\"{$post_author_url}#tab:favourites;post:news;\">News</a></li>
				<li><a href=\"{$post_author_url}#tab:favourites;post:events;\">Events</a></li>
				<li><a href=\"{$post_author_url}#tab:favourites;post:eco-friendly-products;\">Products</a></li>
				<li><a href=\"{$post_author_url}#tab:favourites;post:competitions;\">Competitions</a></li>
				<li><a href=\"{$post_author_url}#tab:favourites;post:projects;\">Projects</a></li>
			</ul></nav>
	        <div class=\"clear\"></div>
	        <div class=\"profile-timeout top\">ERROR: Timeout <a href=\"\">Try refreshing</a>.</div>
	        <div class=\"profile-loading top\">Loading...<img src=\"{$template_path}loading-16x16-lblue.gif\" alt=\"Loading\" /></div>
	        <div class=\"profile-container\"></div>
	        <div class=\"profile-timeout bottom\">ERROR: Timeout <a href=\"\">Try refreshing</a>.</div>
	        <div class=\"profile-loading bottom\">Loading...<img src=\"{$template_path}loading-16x16-lblue.gif\" alt=\"Loading\" /></div>
		";
	}
}

function theme_contributortabs($profile_author) {
	global $current_user;
	
	$post_author_url = get_author_posts_url($profile_author->ID);
	$template_path = get_bloginfo('template_url') . "/template/";
	
	if (get_the_author_meta( 'directory_page_url', $profile_author->ID )) {
		$directory_page_url_redirect = get_the_author_meta( 'directory_page_url', $profile_author->ID );
		$directory_page_url_redirect = "<li><a href=\"{$directory_page_url_redirect}\">Directory</a></li>";
		$directory_page_url = "<li><a href=\"{$post_author_url}#tab:posts;post:directory;\">Directory</a></li>";
	}
	
	# User is logged in and IS viewing their own profile
	if ( ( is_user_logged_in() ) && ( $current_user->ID == $profile_author->ID ) || get_user_role( array('administrator') ) ) {
		echo "
	        <nav class=\"profile-tabs\">
	            <ul>
	                <li><a href=\"{$post_author_url}#tab:posts\" class=\"profile-tab-active\">My Posts</a></li>
	                <li><a href=\"{$post_author_url}#tab:favourites\">Favourites</a></li>
	                <li><a href=\"{$post_author_url}#tab:topics\">Topics</a></li>
	                <li><a href=\"{$post_author_url}#tab:following\">Following</a></li>
	                <li class=\"profile-tab-man\"><a href=\"{$post_author_url}#tab:advertise\">Advertise</a></li>
	                <li class=\"profile-tab-man\"><a href=\"{$post_author_url}#tab:analytics\">Analytics</a></li>
	            </ul>
	        </nav>
	        <div class=\"clear\"></div>
	        <nav class=\"profile-tab-posts\"><ul>
				<li><a href=\"{$post_author_url}#tab:posts;post:all;\" class=\"profile-tab-secondary-active\">All</a></li>
				{$directory_page_url}
				<li><a href=\"{$post_author_url}#tab:posts;post:news;\">News</a></li>
				<li><a href=\"{$post_author_url}#tab:posts;post:events;\">Events</a></li>
				<li><a href=\"{$post_author_url}#tab:posts;post:eco-friendly-products;\">Products</a></li>
				<li><a href=\"{$post_author_url}#tab:posts;post:competitions;\">Competitions</a></li>
				<li><a href=\"{$post_author_url}#tab:posts;post:projects;\">Projects</a></li>
			</ul></nav>
			 <nav class=\"profile-tab-favourites\"><ul>
				<li><a href=\"{$post_author_url}#tab:favourites;post:all;\" class=\"profile-tab-secondary-active\">All</a></li>
				<!--<li><a href=\"{$post_author_url}#tab:favourites;post:directory;\">Directory</a></li>//-->
				<li><a href=\"{$post_author_url}#tab:favourites;post:news;\">News</a></li>
				<li><a href=\"{$post_author_url}#tab:favourites;post:events;\">Events</a></li>
				<li><a href=\"{$post_author_url}#tab:favourites;post:eco-friendly-products;\">Products</a></li>
				<li><a href=\"{$post_author_url}#tab:favourites;post:competitions;\">Competitions</a></li>
				<li><a href=\"{$post_author_url}#tab:favourites;post:projects;\">Projects</a></li>
			</ul></nav>
	        <div class=\"clear\"></div>
	        <div class=\"profile-timeout top\">ERROR: Timeout <a href=\"\">Try refreshing</a>.</div>
	        <div class=\"profile-loading top\">Loading...<img src=\"{$template_path}loading-16x16-lblue.gif\" alt=\"Loading\" /></div>
	        <div class=\"profile-container\"></div>
	        <div class=\"profile-timeout bottom\">ERROR: Timeout <a href=\"\">Try refreshing</a>.</div>
	        <div class=\"profile-loading bottom\">Loading...<img src=\"{$template_path}loading-16x16-lblue.gif\" alt=\"Loading\" /></div>
		";
	}
	
	# User is logged in and IS NOT viewing their own profile OR user is not logged in
	if ( ( ( is_user_logged_in() ) && ( $current_user->ID != $profile_author->ID ) && !get_user_role( array('administrator') ) ) || ( !is_user_logged_in() ) ) {
		echo "
	        <nav class=\"profile-tabs\">
	            <ul>
	                <li><a href=\"{$post_author_url}#tab:posts\" class=\"profile-tab-active\">Posts</a></li>
	                <li><a href=\"{$post_author_url}#tab:favourites\">Favourites</a></li>
	                <li><a href=\"{$post_author_url}#tab:topics\">Topics</a></li>
	                <li><a href=\"{$post_author_url}#tab:following\">Following</a></li>
	            </ul>
	        </nav>
	        <div class=\"clear\"></div>
	        <nav class=\"profile-tab-posts\"><ul>
				<li><a href=\"{$post_author_url}#tab:posts;post:all;\" class=\"profile-tab-secondary-active\">All</a></li>
				{$directory_page_url_redirect}
				<li><a href=\"{$post_author_url}#tab:posts;post:news;\">News</a></li>
				<li><a href=\"{$post_author_url}#tab:posts;post:events;\">Events</a></li>
				<li><a href=\"{$post_author_url}#tab:posts;post:eco-friendly-products;\">Products</a></li>
				<li><a href=\"{$post_author_url}#tab:posts;post:competitions;\">Competitions</a></li>
				<li><a href=\"{$post_author_url}#tab:posts;post:projects;\">Projects</a></li>
			</ul></nav>
			 <nav class=\"profile-tab-favourites\"><ul>
				<li><a href=\"{$post_author_url}#tab:favourites;post:all;\" class=\"profile-tab-secondary-active\">All</a></li>
				<!--<li><a href=\"{$post_author_url}#tab:favourites;post:directory;\">Directory</a></li>//-->
				<li><a href=\"{$post_author_url}#tab:favourites;post:news;\">News</a></li>
				<li><a href=\"{$post_author_url}#tab:favourites;post:events;\">Events</a></li>
				<li><a href=\"{$post_author_url}#tab:favourites;post:eco-friendly-products;\">Products</a></li>
				<li><a href=\"{$post_author_url}#tab:favourites;post:competitions;\">Competitions</a></li>
				<li><a href=\"{$post_author_url}#tab:favourites;post:projects;\">Projects</a></li>
			</ul></nav>
	        <div class=\"clear\"></div>
	        <div class=\"profile-timeout top\">ERROR: Timeout <a href=\"\">Try refreshing</a>.</div>
	        <div class=\"profile-loading top\">Loading...<img src=\"{$template_path}loading-16x16-lblue.gif\" alt=\"Loading\" /></div>
	        <div class=\"profile-container\"></div>
	        <div class=\"profile-timeout bottom\">ERROR: Timeout <a href=\"\">Try refreshing</a>.</div>
	        <div class=\"profile-loading bottom\">Loading...<img src=\"{$template_path}loading-16x16-lblue.gif\" alt=\"Loading\" /></div>
		";
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
		theme_authorviews($profile_author);
		#theme_authorjoined($profile_author);
		#theme_authorseen($profile_author);
	echo '</div><div class="clear"></div>';
	theme_editorsblurb($profile_author);
	#echo '<div class="clear"></div>';
	theme_profile_contributor_donate_join_bar($profile_author);	
	theme_editortabs($profile_author); 
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
		theme_author_employer($profile_author);
		theme_authorwww($profile_author);
		theme_authorviews($profile_author);
		#theme_authorjoined($profile_author);
		#theme_authorseen($profile_author);
	echo '</div><div class="clear"></div>';
	theme_authorbio($profile_author);
	theme_authorschange($profile_author);
	theme_authorsprojects($profile_author);
	theme_authorsstuff($profile_author);	
	echo '<div class="clear"></div>';
	theme_subscribertabs($profile_author); 
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
		theme_authorviews($profile_author);
		#theme_authorjoined($profile_author);
		#theme_authorseen($profile_author);
	echo '</div><div class="clear"></div>';
	theme_profile_contributor_donate_join_bar($profile_author);
	theme_contributorsblurb($profile_author);
	echo '<div class="clear"></div>';
	theme_contributortabs($profile_author); 
	echo '<div class="clear"></div>';
} 

?>
