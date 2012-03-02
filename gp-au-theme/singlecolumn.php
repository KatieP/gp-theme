<?php

/*
Template Name: Single Column Layout
*/

?>

<?php get_header(); ?>

	<div class="pos">

			<div id="col" class="setcol">
				<div id="content">
				
					<?php
                        /* Run the loop to output the posts.
                         * If you want to overload this in a child theme then include a file
                         * called loop-index.php and that will be used instead.
                         */
                         get_template_part( 'loop', 'index' );
					?>
					
				</div>
			</div>
			
	</div>

<?php get_footer(); ?>