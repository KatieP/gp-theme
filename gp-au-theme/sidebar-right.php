			<div id="col3"  class="set3col">
				 
				<!-- Event Calendar -->
				<div id="eventCalendar"></div>
				<div id="event-dialog" title="Event Details" class="hidden"></div> 
				
				<?php
				
				global $post;
				$post_type = ( isset($post) ? get_post_type($post->ID) : "" );
				
                if ( is_home() || is_front_page() || ($post_type == 'gp_events') ) {
                    /**SHOWS THE THE EVENT CALENDAR AND THE NEXT 3 UP AND COMING EVENTS **/ 				
				    coming_events();
                }
				

				?>
			</div>
			