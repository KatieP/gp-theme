			<div id="col3"  class="set3col">
				
				<?php show_google_map(); ?>
				
				<!-- Event Calendar -->
				<div id="eventCalendar"></div>
				<div id="event-dialog" title="Event Details" class="hidden"></div>
				
				<?php
                if ( is_home() || is_front_page() || ($post_type == 'gp_events') ) {
                    coming_events(); 
                } 
				?>

			</div>
			