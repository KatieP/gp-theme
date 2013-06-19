<?php get_header(); ?>
	<div class="pos">
		<div id="col2" class="set3col">
			<div id="content">					
			    <?php $tag = single_tag_title("", false); ?>
                <p><strong>Topic: <?php echo $tag; ?></strong></p>
				
				<?php
				/** Display posts with relevant tag **/
				$tag_args =   array ( 'posts_per_page' => 20,
				                      'post_type' => 'gp_news',
				                      'tag' => $tag );
				$pageposts =  get_posts( $tag_args );
				
				foreach ($pageposts as $post) {
				    theme_index_feed_item();
				}
				?>		
			</div>
		</div>
		<?php get_sidebar('right'); ?>
	</div>
<?php get_footer(); ?>




