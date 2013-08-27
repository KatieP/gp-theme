<?php
/*
Template Name: New Log In Page
*/
?>

<?php get_header(); ?>
	<div class="pos">
		<div id="col2" class="set3col">
			<div id="content">
			    <h1>Member Registration Successful</h1>
			    <h1>Log in with the username and password you just created</h1>				
				<?php
                /** 
                 * Custom log in page for new users
                 * who've just created their profiles
                 * prompting them to log in with their 
                 * newly created username and password 
                 */
				
				$site_url =           get_site_url();
				$create_my_profile =  $site_url . '/forms/create-my-profile/';
				
                $args = array('redirect' => $create_my_profile, 'value_remember' => true);
                wp_login_form( $args );
				
				?>
			</div>
		</div>
		<?php get_sidebar('right'); ?>
	</div>
<?php get_footer(); ?>