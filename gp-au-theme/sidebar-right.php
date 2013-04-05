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

				#relevant_posts();
				?>
				<!--  
				<table>
					<?php 
					# Display Directory links only if user in Australia 
                    # Get location from user ip address function	
                    #$user_country = $gp->location['country'];
	                #if ( $user_country == 'Australia' ) { ?> 
					<tr>
						<td>
							<nav id="lyb">
								<?php #$click_track_tag_lyb = '/internal/advertising/list-your-business/'; ?>
								<a href="<?php #echo get_permalink(472); ?>" onClick="_gaq.push(['_trackPageview', '<?php #echo $click_track_tag_lyb; ?>']);">
									<span class="title">List your business</span>
									<span class="content">Free 30 day trial</span>
								</a>
							</nav>
						</td>					
						<td>
							<nav id="renew-directory">
								<?php #$click_track_tag_renew_directory = '/chargify/renew-directory/'; ?>
								<a href="https://green-pages.chargify.com/h/51439/subscriptions/new" target="_blank" onClick="_gaq.push(['_trackPageview', '<?php #echo $click_track_tag_renew_directory; ?>']);">
									<span class="title">Renew My Listing</span>
									<span class="content">Directory Page Renewal</span>
								</a>
							</nav>
						</td>
					</tr>
					<?php 
					#;}  ?>
				</table>
				-->
			</div>
			