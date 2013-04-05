		<footer>
    		<div class="pos">
    		    <div class="template-left">
			        <nav id="footer-nav"> 
				        <ul>
				            <li><a href="/welcome/">Get Started</a></li>
					        <li><a href="/about/partners/">Content Partners</a></li>
					        <li><a href="/about/badges/">Badges</a></li>
					        <li><a href="/about/faq/">FAQ</a></li>
					        <li><a href="about/our-vision/">About</a></li>
					        <li><a href="/about/advertisers/">Advertisers</a></li>
					        <li><a href="/about/rate-card/">Rate Card</a></li>
					        <li><a href="/about/media-kit/">Media Kit</a></li>
				        </ul>	
			        </nav>
			    </div>
			    <div class="template-right">
			        <nav id="footer-nav">
			            <ul id="footer-social">
					        <li><a href="<?php show_facebook_by_location(); ?>" target="_blank"><i class="af-icon-facebook-sign"></i></a></li>
					        <li><a href="https://twitter.com/GreenPagesAu" target="_blank"><i class="af-icon-twitter-sign"></i></a></li>
					        <li><a href="/news/rss/" target="_blank"><i class="af-icon-rss"></i></a></li>
				        </ul>			            
			        </nav>
			        <nav id="footer-contact">
				        <div id="footer-contact-click-box">
					        <div class="click-contact-info" id="click-contact-address">
					            Postal Address:<pre><?php echo get_option('gp_postaladdress'); ?></pre>
					        </div>
					        <div class="click-contact-info" id="click-contact-phone">
					            Phone:<pre>Promoting an event? <br /><a href="/welcome/">Post it on Green Pages for free</a>.<br /><br /><?php echo get_option('gp_phone1'); ?></pre>
					        </div>
					        <div class="click-contact-info" id="click-contact-email">
					            Email Address:<pre>Promoting a product or company? <br /><a href="/welcome/">Post it on Green Pages for $89</a>.<br /><br /><?php echo str_replace('@', ' [at] ', get_option('gp_email1')); ?></pre>
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
		<script type="text/javascript" src="//assets.pinterest.com/js/pinit.js"></script>
	</body>
</html>