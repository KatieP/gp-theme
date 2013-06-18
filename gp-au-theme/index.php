<?php get_header(); ?>
	<div class="pos">
		<div id="col2" class="set3col">
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
		<?php get_sidebar('right'); ?>
	</div>
<?php get_footer(); ?>
