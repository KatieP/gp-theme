<?php
/**
 * The loop that displays posts.
 *
 * The loop displays the posts and the post content.  See
 * http://codex.wordpress.org/The_Loop to understand it and
 * http://codex.wordpress.org/Template_Tags to understand
 * the tags used in it.
 *
 * This can be overridden in child themes with loop.php or
 * loop-template.php, where 'template' is the loop context
 * requested by a template. For example, loop-index.php would
 * be used if it exists and we ask for the loop with:
 * <code>get_template_part( 'loop', 'index' );</code>
 *
 * @package WordPress
 * @subpackage Twenty_Ten
 * @since Twenty Ten 1.0
 */
?>

<?php /* If there are no posts to display, such as an empty archive page */ ?>
<?php if ( ! have_posts() ) : ?>
        <div id="post-0" class="post error404 not-found">
                <h1 class="entry-title"><?php _e( 'Not Found', 'twentyten' ); ?></h1>
                <div class="entry-content">
                        <p><?php _e( 'Apologies, but no results were found for the requested archive. Perhaps searching will help find a related post.', 'twentyten' ); ?></p>
                        <?php get_search_form(); ?>
                </div><!-- .entry-content -->
        </div><!-- #post-0 -->
<?php endif; ?>

<?php
        /* Start the Loop.
         *
         * In Twenty Ten we use the same loop in multiple contexts.
         * It is broken into three main parts: when we're displaying
         * posts that are in the gallery category, when we're displaying
         * posts in the asides category, and finally all other posts.
         *
         * Additionally, we sometimes check for whether we are on an
         * archive page, a search page, etc., allowing for small differences
         * in the loop on each template without actually duplicating
         * the rest of the loop that is shared.
         *
         * Without further ado, the loop:
         */ ?>
<?php while ( have_posts() ) : the_post(); ?>

<?php /* How to display posts in the Gallery category. */ ?>

        <?php if ( in_category( _x('gallery', 'gallery category slug', 'twentyten') ) ) : ?>
                <div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                        <h2><?php the_title(); ?></h2>
                                                                               
                        <div class="entry-meta">
                                <?php twentyten_posted_on(); ?>
                        </div><!-- .entry-meta -->

                        <div class="entry-content">
<?php if ( post_password_required() ) : ?>
                                <?php the_content(); ?>
<?php else : ?>
                                <?php
                                        $images = get_children( array( 'post_parent' => $post->ID, 'post_type' => 'attachment', 'post_mime_type' => 'image', 'orderby' => 'menu_order', 'order' => 'ASC', 'numberposts' => 999 ) );
                                        if ( $images ) :
                                                $total_images = count( $images );
                                                $image = array_shift( $images );
                                                $image_img_tag = wp_get_attachment_image( $image->ID, 'thumbnail' );
                                ?>
                                                <div class="gallery-thumb">
                                                        <a class="size-thumbnail" href="<?php the_permalink(); ?>"><?php echo $image_img_tag; ?></a>
                                                </div><!-- .gallery-thumb -->
                                                <p><em><?php printf( __( 'This gallery contains <a %1$s>%2$s photos</a>.', 'twentyten' ),
                                                                'href="' . get_permalink() . '" title="' . sprintf( esc_attr__( 'Permalink to %s', 'twentyten' ), the_title_attribute( 'echo=0' ) ) . '" rel="bookmark"',
                                                                $total_images
                                                        ); ?></em></p>
                                <?php endif; ?>
                                                <?php the_excerpt(); ?>
<?php endif; ?>
                        </div><!-- .entry-content -->


                </div><!-- #post-## -->

<?php /* How to display posts in the asides category */ ?>

        <?php elseif ( in_category( _x('asides', 'asides category slug', 'twentyten') ) ) : ?>
                <div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

                <?php if ( is_archive() || is_search() ) : // Display excerpts for archives and search. ?>
                        <div class="entry-summary">
                                <?php the_excerpt(); ?>
                        </div><!-- .entry-summary -->
                <?php else : ?>
                        <div class="entry-content">
                                <?php the_content( __( 'Continue reading <span class="meta-nav">&rarr;</span>', 'twentyten' ) ); ?>
                        </div><!-- .entry-content -->
                <?php endif; ?>

                </div><!-- #post-## -->

<?php /* How to display all other posts. */ ?>

        <?php else : ?>
                        <h2><a href="<?php the_permalink(); ?>" title="<?php printf( esc_attr__( 'Permalink to %s', 'twentyten' ), the_title_attribute( 'echo=0' ) ); ?>" rel="bookmark"><?php the_title(); ?></a></h2>

        <?php if ( is_search() ) : // Only display excerpts for archives and search. ?>
                        <div class="entry-summary">
                                <?php the_excerpt(); ?>
                        </div><!-- .entry-summary -->
        <?php else : ?>
                        <div class="entry-content">
                                <?php the_content( __( 'Continue reading <span class="meta-nav">&rarr;</span>', 'twentyten' ) ); ?>
                                <?php wp_link_pages( array( 'before' => '<div class="page-link">' . __( 'Pages:', 'twentyten' ), 'after' => '</div>' ) ); ?>
                                <div class="post-socialnav">
                                	<?php if ( comments_open() ) : ?>
                                		<div class="post-twitter"><a href="http://twitter.com/share" class="twitter-share-button" data-url="<?php echo get_permalink($post->ID); ?>" data-text="<?php echo get_the_title($post->ID); ?>" data-count="horizontal" data-via="GreenPagesAu">Tweet</a></div><script type="text/javascript" src="http://platform.twitter.com/widgets.js"></script>
	                                	<div class="post-facebook"><iframe src="http://www.facebook.com/plugins/like.php?href=<?php echo urlencode(get_permalink($post->ID)); ?>&amp;layout=button_count&amp;show_faces=false&amp;action=recommend&amp;font=arial&amp;colorscheme=light" scrolling="no" frameborder="0" style="border:none; overflow:hidden;" allowTransparency="true"></iframe></div>
	                                	<a href="<?php echo get_permalink($post->ID); ?>#disqus_thread" class="post-disqus">
	                                		<div class="comment-leftcap"></div>
	                                		<div class="comment-background">
	                                			<?php echo $post->comment_count; ?>
	                                		</div>
	                                	</a>
	                                	<div class="comment-rightcap"></div>
	                                <?php endif; ?>
	                                <div class="clear"></div>
                                </div>
                                
                        </div><!-- .entry-content -->
        <?php endif; ?>

           
                <!-- #post-## -->

                <?php comments_template( '', true ); ?>

        <?php endif; // This was the if statement that broke the loop into three parts based on categories. ?>

<?php endwhile; // End the loop. Whew. ?>

<?php 
/* Display navigation to next/previous pages when applicable */
if (  $wp_query->max_num_pages > 1 ) : ?>
    <nav id="post-nav">
    	<ul>
    		<li class="post-previous"><?php next_posts_link('<div class="arrow-previous"></div>Older Posts', 0); ?></li>
    		<li class="post-next"><?php previous_posts_link('Newer Posts<div class="arrow-next"></div>', 0); ?></li>
    	</ul>
    </nav>
<?php endif; ?>