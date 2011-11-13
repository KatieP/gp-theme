<?php get_header(); ?>

	<div class="pos">
		<?php get_sidebar('left'); ?>

			<div id="col2" class="set3col">
				<div id="content">

					<!-- <nav class="breadcrumbs">
						<span>You are here:</span>&nbsp;
						<a href="">Bread</a>&nbsp;>&nbsp;
						<a href="">crumb</a>&nbsp;>&nbsp;
						<a href="">trail</a>
					</nav> //-->
					
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
