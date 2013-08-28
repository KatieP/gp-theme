<?php
/*
Template Name: New Log In Page
*/
?>

<?php 
 /** 
   * Custom log in page for new users
   * who've just created their profiles
   * prompting them to log in with their 
   * newly created username and password 
   */
?>

<?php get_header(); ?>
	<div class="pos">
		<div id="col2" class="set3col">
			<div id="content">
            <?php $site_url = get_site_url(); 
            $referrer = $_SERVER['HTTP_REFERER'];
            $member_registration = $site_url . '/forms/member-registration-form/';
            if ( !is_user_logged_in() ) { 

                if ( isset($_GET['login']) && ( $_GET['login'] == 'failed' ) ) {
			        ?><h1 class=" validation_message">Incorrect username or password. Please try again.</h1><?php 
			    } else {?>
    			    <h1>Member Registration Successful</h1>
    			    <h1>Enter your new password and log in</h1><?php 
			    }
    				
    			$create_my_profile =  $site_url . '/forms/create-my-profile/';
    			$username =           ( isset($_GET['username']) ) ? $_GET['username'] : null ;
                $args =               array('redirect' => $create_my_profile, 'value_remember' => true, 'value_username' => $username);
                    
                wp_login_form( $args );

            } else { ?>

    		    <h1>You're already logged in.</h1> <?php 

            } ?>
			</div>
		</div>
		<?php get_sidebar('right'); ?>
	</div>
<?php get_footer(); ?>