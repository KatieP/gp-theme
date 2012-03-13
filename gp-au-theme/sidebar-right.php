			<div id="col3"  class="set3col">
				<div id="col3-ad">
						<!-- <iframe src="<?php bloginfo('template_url'); ?>/template/google-medrec1.html" class="medrec"></iframe> -->
						<!-- stg1_medrec -->
						<script type='text/javascript'>
							GA_googleFillSlot("stg1_medrec");
						</script>
						<span class="icon-advertisement">Advertisement</span>
				</div>
				
				<?php
				/**SHOWS THE NEXT 5 UP AND COMING EVENTS UNDER THE EVENT CALENDAR**/ 				
				coming_events();
				
				cm_update_current_user(); # checks a users Campaign Monitor newsletter subscription first and modify's that user if necessary.
				#$current_user = wp_get_current_user();
				global $current_user;

				if ( ( is_page() || is_home() ) && ( $current_user->subscription["subscription-greenrazor"] != "true" || !is_user_logged_in() ) ) {
				?>
				<div id="subscribe">
					<span class="title">Subscribe to the Green Razor</span>
					<span id="subscribe-tag">The latest green news straight to you! <a href="/about/green-razor-newsletter">Read more...</a></span>
					<form>
						<div class="icon-subscribe"></div>
						<div id="subscribe-email">Email address: <span>(e.g. citizen@green.com)</span></div>
						<div id="subscribe-field"><input type="text" name="subscriber-email" id="subscriber-email" maxlength="255" /></div>
						<div id="subscribe-button">Subscribe<input type="button" name="subscribe" title="subscribe" onclick='return create();' /></div>
						<div class="clear"></div>
					</form>
				</div>
				<?php
				}
				
				#if ( is_home() ) {
				?>		
				<div id="facebook">
					<!-- <span class="title">Find us on Facebook</span>  -->
					<iframe src="http://www.facebook.com/plugins/likebox.php?id=135951849770296&amp;width=300&amp;connections=10&amp;stream=false&amp;header=false&amp;height=274" frameborder="0" scrolling="no" id="facebook-frame" allowTransparency="true"></iframe>
					<a href="http://www.facebook.com/pages/Green-Pages-Community/135951849770296" target="_new" class="moreinfo">Click here to visit our Facebook page</a>
				</div>
				<?php
				#}
				
				if ( get_post_type() == 'gp_news' && !is_author() && !is_single() && !wp_title("",0)==" Search") {
				?> 
				<div id="twitter">
					<span class="title">Live: Environmental News</span>
					<script src="http://widgets.twimg.com/j/2/widget.js"></script>
					<script>
					new TWTR.Widget({
					  version: 2,
					  type: 'search',
					  search: 'environmental news',
					  interval: 6000,
					  title: '',
					  subject: '',
					  width: 300,
					  height: 640,
					  theme: {
					    shell: {
					      background: '#ffffff',
					      color: '#666666'
					    },
					    tweets: {
					      background: '#ffffff',
					      color: '#666666',
					      links: '#01add8'
					    }
					  },
					  features: {
					    scrollbar: false,
					    loop: true,
					    live: true,
					    hashtags: false,
					    timestamp: false,
					    avatars: true,
					    toptweets: true,
					    behavior: 'default'
					  }
					}).render().start();
					</script>
					<a href="http://twitter.com/GreenPagesAu" target="_new" class="moreinfo">Click here to visit our Twitter account</a>
				</div>
				<?php
				}
				relevant_posts();
				?>
				<table>
					<tr>
						<td>
							<nav id="lyb">
								<?php $click_track_tag_lyb = '/internal/advertising/list-your-business/'; ?>
								<a href="<?php echo get_permalink(472); ?>" onClick="_gaq.push(['_trackPageview', '<?php echo $click_track_tag_lyb; ?>']);">
									<span class="title">List your business</span>
									<span class="content">Free 30 day trial</span>
								</a>
							</nav>
						</td>
						<td>
							<nav id="advertisewus">
								<?php $click_track_tag_awu = '/internal/advertising/advertise-with-us/'; ?>
								<a href="<?php bloginfo('template_url'); ?>/gp-rate-card-cover.html" rel="iframe-820-460" class="pirobox_gall1" onClick="_gaq.push(['_trackPageview', '<?php echo $click_track_tag_awu; ?>']);">
									<span class="title">Advertise</span>
									<span class="content">Explore the options</span>
								</a>
							</nav>
							<div class="hidden">
								<?php if ( !is_user_logged_in() ) { ?>
									<a href="<?php bloginfo('template_url'); ?>/gp-rate-card-directory-page.html" rel="iframe-820-460"  class="pirobox_gall1"></a>
									<a href="<?php bloginfo('template_url'); ?>/gp-rate-card-new-stuff.html" rel="iframe-820-460"  class="pirobox_gall1"></a>
									<a href="<?php bloginfo('template_url'); ?>/gp-rate-card-display-ad.html" rel="iframe-820-460"  class="pirobox_gall1"></a>
									<a href="<?php bloginfo('template_url'); ?>/gp-rate-card-competition.html" rel="iframe-820-460"  class="pirobox_gall1"></a>
									<a href="<?php bloginfo('template_url'); ?>/gp-rate-card-exclusive-email.html" rel="iframe-820-460"  class="pirobox_gall1"></a>
								<?php } 
								else { ?>
									<a href="<?php bloginfo('template_url'); ?>/gp-rate-card-directory-page-logged-in.html" rel="iframe-820-460"  class="pirobox_gall1"></a>
									<a href="<?php bloginfo('template_url'); ?>/gp-rate-card-new-stuff-logged-in.html" rel="iframe-820-460"  class="pirobox_gall1"></a>
									<a href="<?php bloginfo('template_url'); ?>/gp-rate-card-display-ad-logged-in.html" rel="iframe-820-460"  class="pirobox_gall1"></a>
									<a href="<?php bloginfo('template_url'); ?>/gp-rate-card-competition-logged-in.html" rel="iframe-820-460"  class="pirobox_gall1"></a>
									<a href="<?php bloginfo('template_url'); ?>/gp-rate-card-exclusive-email-logged-in.html" rel="iframe-820-460"  class="pirobox_gall1"></a>								
								<?php }?>								
							</div>
						</td>
					</tr>
					<tr>
						<td>
							<nav id="renew-directory">
								<?php $click_track_tag_renew_directory = '/chargify/renew-directory/'; ?>
								<a href="https://green-pages.chargify.com/h/51439/subscriptions/new" target="_blank" onClick="_gaq.push(['_trackPageview', '<?php echo $click_track_tag_renew_directory; ?>']);">
									<span class="title">Renew My Listing</span>
									<span class="content">Directory Page Renewal</span>
								</a>
							</nav>
						</td>							
					</tr>
				</table>
				<div id="toolbox">
					<script type="text/javascript">
					<!--
						google_ad_client = "ca-pub-5276108711751681";
						/* Col3 text ads */
						google_ad_slot = "3620435405";
						google_ad_width = 300;
						google_ad_height = 250;
					//-->
					</script>
					<script type="text/javascript" src="http://pagead2.googlesyndication.com/pagead/show_ads.js"></script>
				</div>
				<div id="col3-ad" style="margin-top:20px;">
					<div>
						<!-- <iframe src="<?php bloginfo('template_url'); ?>/template/google-medrec2.html" class="medrec"></iframe> -->
						<!-- stg1_medrec -->
						<script type='text/javascript'>
							GA_googleFillSlot("stg1_medrec2");
						</script>
						<span class="icon-advertisement">Advertisement</span>
					</div>
				</div>
			</div>
