		<?php 
		if (is_home() || is_page()) { $post_type = "gp_news"; }
		$post_type_map = array( "gp_news" => "news",
		                        "gp_events" => "events", 
                                "gp_advertorial" => "eco-friendly-products", 
                                "gp_projects" => "projects" );
		?>
		<footer>
    		<div class="pos">
    		    <div class="template-left">
			        <nav id="footer-nav"> 
				        <ul>
					        <li><a href="<?php echo $site_url; ?>/about/partners/">Content Partners</a></li>
					        <li><a href="<?php echo $site_url; ?>/about/badges/">Badges</a></li>
					        <li><a href="<?php echo $site_url; ?>/about/">About</a></li>
					        <li><a href="http://greenpages.myshopify.com" target="_blank">Shop</a></li>
					        <li><a href="<?php echo $site_url; ?>/about/terms-and-conditions/">Terms</a></li>
					        <li><a href="<?php echo $site_url; ?>/about/green-pages-privacy-policy/">Privacy</a></li>
					        <li><a href="<?php echo $site_url; ?>/advertisers/">Advertisers</a></li>
					        <li><a href="<?php echo $site_url; ?>/world-map/">World Map</a></li>
				        </ul>	
			        </nav>
			    </div>
			    <div class="template-right">
			        <nav id="footer-nav">
			            <ul id="footer-social">
					        <li><a href="<?php echo 'http://www.facebook.com/'. show_facebook_by_location(); ?>" target="_blank"><i class="af-icon-facebook-sign"></i></a></li>
					        <li><a href="https://twitter.com/GreenPagesAu" target="_blank"><i class="af-icon-twitter-sign"></i></a></li>
					        <li><a href="<?php echo $site_url . '/' . $post_type_map[$post_type]; ?>/feed/" target="_blank"><i class="af-icon-rss"></i></a></li>
				        </ul>			            
			        </nav>
			        <nav id="footer-contact">
				        <div id="footer-contact-click-box">
					        <div class="click-contact-info" id="click-contact-address">
					            <span>Postal Address:</span>
					            <pre><?php echo '599 Fairchild Dr <br />Mountain View <br />CA <br />USA'; ?></pre>
					        </div>
					        <div class="click-contact-info" id="click-contact-phone">
					            <span>Phone:</span>
					            <pre>Promoting an event? <br /><a href="<?php echo $site_url; ?>/welcome/">Post it on Green Pages for free</a>.
					            <?php echo 'US: 650-283-8142 <br />AU: 02 8003 5915'; ?></pre>
					        </div>
					        <div class="click-contact-info" id="click-contact-email">
					            <span>Email Address:</span>
					            <pre>Promoting a product or company? <br /><a href="<?php echo $site_url; ?>/welcome/">Post it on Green Pages for $89</a>.
					            <br />hello[at]greenpag.es</pre>
					        </div>
				        </div>
				        <ul>
					        <li class="title">Contact us</li>
					        <li class="contact-icon" id="contact-address"></li>
					        <li class="contact-icon" id="contact-phone"></li>
					        <li class="contact-icon" id="contact-email"></li>
				        </ul>
			        </nav>
			    </div>
            </div>
		</footer>
		<?php 
        /* Always have wp_footer() just before the closing </body>
         * tag of your theme, or you will break many plugins, which
         * generally use this hook to reference JavaScript files.
         */
        wp_footer();
        ?>
        <!-- JS for social media sharebar: Twitter, Google+ and Stumbleupon -->
        <script type="text/javascript" src="http://platform.twitter.com/widgets.js"></script>
        <script type="text/javascript">
			(function() {
				var po = document.createElement('script'); 
				po.type = 'text/javascript'; 
				po.async = true;
				po.src = 'https://apis.google.com/js/plusone.js';
				var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(po, s);
			})();
			
			(function() {
    			var li = document.createElement('script'); li.type = 'text/javascript'; li.async = true;
    			li.src = ('https:' == document.location.protocol ? 'https:' : 'http:') + '//platform.stumbleupon.com/1/widgets.js';
    			var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(li, s);
  			})();
		</script>
	</body>
</html>