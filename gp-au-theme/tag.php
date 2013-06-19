<?php get_header(); ?>
	<div class="pos">
		<div id="col2" class="set3col">
			<div id="content">					
			    <?php 
			    $tag =             single_tag_title("", false); 
			    $post_type_map =   get_post_type_map();
			    $post_type =       ( !empty($_GET['post_type']) ) ? $_GET['post_type'] : 'gp_news';
			    $post_type_name =  ucfirst($post_type_map[$post_type]);
			    ?>
                <p><strong>Recent posts tagged with #<?php echo $tag; ?> in <?php echo $post_type_name; ?></strong></p>
				
				<?php
				/** Display posts with relevant tag **/
				$args =       array ( 'posts_per_page' => 20,
				                      'post_type' => $post_type,
				                      'tag' => $tag );
				$pageposts =  get_posts( $args );
				
				if ($pageposts) {
				    foreach ($pageposts as $post) {
				        theme_index_feed_item();
				    }
				}
				?>		
			</div>
		</div>
		<?php get_sidebar('right'); ?>
	</div>
<?php get_footer(); ?>




