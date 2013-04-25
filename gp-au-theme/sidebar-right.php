			<div id="col3"  class="set3col">
				 
				<!-- Event Calendar -->
				<div id="eventCalendar"></div>
				<div id="event-dialog" title="Event Details" class="hidden"></div> 
				
				<?php
				
				/**SHOWS THE NEXT 5 UP AND COMING EVENTS UNDER THE EVENT CALENDAR**/ 				
				coming_events();

				# Display Directory links only if user in Australia 
                # Get location from user ip address function	
                global $gp;
                $user_country = $gp->location['country_iso2'];
	            if ( $user_country == 'AU' ) {
	                $click_track_tag_lyb = '/internal/advertising/list-your-business/';
	                $lyb_link = get_permalink(472);
	                $click_track_tag_renew_directory = '/chargify/renew-directory/';
	                echo '
	            	<table>
						<tr>
							<td>
								<nav id="lyb">
									<a href="'. $lyb_link .'" onClick="_gaq.push(["_trackPageview", "'. $click_track_tag_lyb .'"]);">
										<span class="title">List your business</span>
										<span class="content">Free 30 day trial</span>
									</a>
								</nav>
							</td>					
							<td>
								<nav id="renew-directory">
									<a href="https://green-pages.chargify.com/h/51439/subscriptions/new" target="_blank" onClick="_gaq.push(["_trackPageview", "'. $click_track_tag_renew_directory .'"]);">
										<span class="title">Renew My Listing</span>
										<span class="content">Directory Page Renewal</span>
									</a>
								</nav>
							</td>
						</tr>
					</table>';
				}  
				?>
			</div>
			