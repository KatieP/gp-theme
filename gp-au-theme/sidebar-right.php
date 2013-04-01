			<div id="col3"  class="set3col">
				 
				<!-- Event Calendar -->
				<div id="eventCalendar"></div>
				<div id="event-dialog" title="Event Details" class="hidden"></div> 
				<!-- BookAd -->
				<!--  
				<div id='div-gpt-ad-1344935271926-0' style='width:300px; height:150px;'>
					<script type='text/javascript'>
						googletag.cmd.push(function() { googletag.display('div-gpt-ad-1344935271926-0'); });
					</script>
				</div>
				-->
				<?php 
				/** SHOW VIDEO IF CURRENT CONTENT AVAILABLE **/
				$video_news_id = get_the_author_meta( 'video_news_id', '2' );
				if ( !empty($video_news_id) ) {
				?>
					<div id="video">
						<?php 
						/** CREATE VIDEO URL FROM VARIABLE IN KATIES PROFILE USERMETA, DISPLAY VIDEO NEWS **/
						$video_news_url = 'http://player.vimeo.com/video/' . $video_news_id;
						?>
						<iframe src="<?php echo $video_news_url; ?>" width="300" height="177" frameborder="0" webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe>				
					</div>
				<?php
				}
				
				/**SHOWS THE NEXT 5 UP AND COMING EVENTS UNDER THE EVENT CALENDAR**/ 				
				coming_events();
				
				cm_update_current_user(); # checks a users Campaign Monitor newsletter subscription first and modify's that user if necessary.

				global $current_user, $wpdb, $gp;
				if ( ( is_page() || is_home() ) && ( $current_user->{$wpdb->prefix . 'subscription'}["subscription-greenrazor"] !== "true" || !is_user_logged_in() ) ) {
				?>
				<div id="subscribe">
					<span class="title">Subscribe to the Green Razor</span>
					<span id="subscribe-tag">The latest green news straight to you! <a href="/about/green-razor-newsletter">Read more...</a></span>
					<form>
						<div class="icon-subscribe"></div>
						<div id="subscribe-email">Email address: <span>(e.g. citizen@green.com)</span></div>
						<div id="subscribe-field"><input type="text" name="subscriber-email" id="subscriber-email" maxlength="255" /></div>
						<div id="subscribe-button">Subscribe<input type="button" name="subscribe" title="subscribe" onclick='return create();' /></div>
						<div class="clear"></div>
					</form>
				</div>
				<?php
				}
				
				?>
				<!--  
				<div id="twitter">
					<a href="https://twitter.com/GreenPagesAu" class="twitter-follow-button" data-show-count="true" data-size="large">Follow @GreenPagesAu</a>
					<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>
				</div>
				<div id="facebook">
				    <?php #show_facebook_by_location(); ?>
			    </div>
			    -->
				<?php
				#relevant_posts();
				?>
				<!--  
				<table>
					<tr>
						<td>
							<nav id="advertisewus">
								<?php $click_track_tag_awu = '/internal/advertising/advertise-with-us/'; ?>
								<a href="<?php bloginfo('template_url'); ?>/about/rate-card/" onClick="_gaq.push(['_trackPageview', '<?php echo $click_track_tag_awu; ?>']);">
									<span class="title">Advertise</span>
									<span class="content">Explore the options</span>
								</a>
							</nav>
						</td>
						<td>
							<nav id="media-kit">
								<?php $click_track_tag_media_kit = '/internal/media-kit/'; ?>
								<a href="<?php bloginfo('template_url'); ?>/about/media-kit/" target="_blank" onClick="_gaq.push(['_trackPageview', '<?php echo $click_track_tag_media_kit; ?>']);">
									<span class="title">Media Kit</span>
									<span class="content">About the members</span>
								</a>
							</nav>						
						</td>
					</tr>
					<?php 
					# Display Directory links only if user in Australia 
                    # Get location from user ip address function	
                    $user_country = $gp->location['country'];
	                if ( $user_country == 'Australia' ) { ?> 
					<tr>
						<td>
							<nav id="lyb">
								<?php $click_track_tag_lyb = '/internal/advertising/list-your-business/'; ?>
								<a href="<?php echo get_permalink(472); ?>" onClick="_gaq.push(['_trackPageview', '<?php echo $click_track_tag_lyb; ?>']);">
									<span class="title">List your business</span>
									<span class="content">Free 30 day trial</span>
								</a>
							</nav>
						</td>					
						<td>
							<nav id="renew-directory">
								<?php $click_track_tag_renew_directory = '/chargify/renew-directory/'; ?>
								<a href="https://green-pages.chargify.com/h/51439/subscriptions/new" target="_blank" onClick="_gaq.push(['_trackPageview', '<?php echo $click_track_tag_renew_directory; ?>']);">
									<span class="title">Renew My Listing</span>
									<span class="content">Directory Page Renewal</span>
								</a>
							</nav>
						</td>
					</tr>
					<?php 
					;}  ?>
				</table>
				-->
			</div>
			