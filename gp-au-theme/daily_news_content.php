<?php
/* 
Template Name: Daily News Content
*/
?>

<?php 

global $post;
global $wpdb;
global $the_query;

$the_query = new WP_Query(array('post_status' => 'any', 
                                'posts_per_page' => 100,
                                'orderby' => 'date',
                                'order' => 'DESC',
                                'post_type' => 'gp_news'));

# print posts in html or txt format
$format = htmlspecialchars($_GET["format"]);
if ($format == "html" || $format == "") {
?>

<table width="100%" style="font-size: 11px; font-family: helvetica; margin: 5px; background-color: rgb(255,255,255);">
	<tr>
		<td align="center">
			<table cellpadding="0" cellspacing="0" border="0" width="640" bgcolor="#fff" style="background-color: #fff;">
				<tr style="padding: 0 5px 5px 5px;">
					<td style="font-size: 10px;text-transform:uppercase;color:rgb(205,205,205);padding:0 0 0 5px;">Your daily news from the Green Pages Community</td>
          <td style="font-size: 10px;text-transform:uppercase;color:rgb(205,205,205);padding:0 5px 0 0;" align="right"><?php echo date("l, j F Y"); ?></td>
				</tr>
			</table>

			<table cellpadding="0" cellspacing="0" border="0" width="640" bgcolor="#fff" style="background-color: #fff; margin: 0 3px 0 3px;">
				<tr>
          <td><a href="<?php echo site_url(); ?>"><img src="http://www.thegreenpages.com.au/wp-content/uploads/2012/01/header1.png" alt="header" width="554" height="80" border="0" /></a></td>
					<td><a href="http://twitter.com/GreenPagesAu"><img src="http://www.thegreenpages.com.au/wp-content/uploads/2012/01/headert.jpg" alt="header" width="41" height="70" border="0" /></a></td>
					<td><a href="http://www.facebook.com/pages/Green-Pages-Community/135951849770296?ref=ts"><img src="http://www.thegreenpages.com.au/wp-content/uploads/2012/01/headerf.jpg" alt="header" width="45" height="70" border="0" /></a></td>
        </tr>
				<tr>
<!--TOP NAV BAR STARTS-->
					<td colspan="3" style="background: rgb(1,174,216); url(http://www.thegreenpages.com.au/razor/menu_background.gif); background-repeat: repeat-x; height: 26px; padding: 1px 125px 1px 2px; overflow: hidden; float: left; -moz-border-radius: 3px;-webkit-border-radius: 3px; border-radius: 3px;">
						<table cellpadding="0" cellspacing="0" border="0" width="95%">
							<tr>
								<td align="center"><a href="http://directory.thegreenpages.com.au/" style="text-decoration:none; font-weight:; color:#fff; font-size: 13px;">Directory</a></td>
                <td align="center"><a href="<?php echo site_url(); ?>/news/" style="text-decoration:none;font-weight:; color:#fff;font-size: 13px;">News</a></td>
                <td align="center"><a href="<?php echo site_url(); ?>/events" style="text-decoration:none; font-weight:; color:#fff;font-size: 13px;">Events</a></td>
                <td align="center"><a href="<?php echo site_url(); ?>/new-stuff" style="text-decoration:none; font-weight:; color:#fff;font-size: 13px;">New Stuff</a></td>
                <td align="center"><a href="<?php echo site_url(); ?>/competitions" style="text-decoration:none; font-weight:; color:#fff;font-size: 13px;">Competitions</a></td>
                <td align="center"><a href="<?php echo site_url(); ?>/people/" style="text-decoration:none; font-weight:; color:#fff;font-size: 13px;">People</a></td>
                <td align="center"><a href="<?php echo site_url(); ?>/ngo-campaign" style="text-decoration:none; font-weight:; color:#fff;font-size: 13px;">Campaigns</a></td>
							</tr>
						</table>
					</td>
<!--TOP NAV BAR ENDS-->					
        </tr>
        <tr><td>&nbsp;</td></tr>
      </table>

			<table cellpadding="0" cellspacing="0" border="0" width="640" bgcolor="#fff" style="background-color: #fff;">
				<tr>
					<td width="490" style="padding: 0 0 0 5px;" valign="top">
						
<?php 
  while( $the_query->have_posts()) {
    $the_query->the_post();
?>
						<!-- Repeater1 for News Content Starts -->
            <table cellpadding="0" cellspacing="0" border="0" width="100%" style="margin: 0 0 10px 0;">
              <tr>
                <td>
                  <a href="<?php the_permalink(); ?>" 
                     style="font-size:18px; padding:0 0 0 0px;color:rgb(120,120,120);font-weight:bold;text-decoration:none;">
                    <?php the_title(); ?>
                  </a>
                </td>
                <td align="right">
                  <!--<img src="http://www.thegreenpages.com.au/razor/body_facebook.gif" alt="facebook" width="16" height="16"/>
                  <img src="http://www.thegreenpages.com.au/razor/body_twitter.gif" alt="twitter" width="16" height="16"/>
                  <img src="http://www.thegreenpages.com.au/razor/body_mail.gif" alt="email" width="16" height="16"/>-->
                </td>
              </tr>
              <tr>
                <td colspan="2"><hr style="padding:0px;margin:2px 0 10px 0;"></td>
              </tr>
              <tr>
                <td colspan="2" valign="top">
                  <table cellpadding="0" cellspacing="0" border="0">
                    <tr>
                      <td valign="top">
                        <?php if (has_post_thumbnail() ){
                                $imageArray = wp_get_attachment_image_src( get_post_thumbnail_id($post->ID), 'homepage-thumbnail');
                                $imageURL = $imageArray[0];
                        ?>
                          <a href="<?php the_permalink(); ?>">
                            <img label="Image" alt="story" width="100" 
                            style="padding:5px;border:1px solid rgb(205,205,205);margin:0 10px 0 0;"
                            src="<?php echo $imageURL; ?>" 
                            alt="<?php get_the_title( get_post_thumbnail_id($post->ID) ) ?>"
                            />
                          </a>
                        <?php } ?>
                      </td>
                      <td valign="top" style="font-size: 12px; color:rgb(120,120,120); line-height:1.25em;">

                      <?php the_excerpt(); ?>

                       <a href="<?php the_permalink(); ?>" style="text-decoration: none; color: #01AED8; font-weight: bold;">Continue reading...</a> 

<!--                        <p>Enter <a href="<?php echo site_url(); ?>" style="text-decoration:none"><span 
                        style="text-decoration:none;color:#01AED8">test link back to main 
                        website</span></a> body content here </p>
-->


                    </td>
                    </tr>
                  </table>
                </td>
              </tr>
            </table>
						<!-- Repeater1 for News Content Ends -->
<?php
  }
?>
          </td>
        </tr>
				<tr><td>&nbsp;</td></tr>
			</table>
			<table cellpadding="0" cellspacing="0" border="0" width="640">
				<tr>
          <td style="font-size: 10px; color: #fff; background-color: rgb(97,194,1); height: 40px; padding:5px 0 5px 5px;">THE GREEN RAZOR is delivered to you by Green Pages, The Hub of Sustainability<a href="<?php echo site_url();?>" alt="greenpages" width="198" height="20" align="absmiddle" border="0"></a></td>
					<td style="font-size: 10px; color: #fff; text-transform: uppercase; background-color: rgb(97,194,1); height: 40px; padding:10px 0 5px 10px;">FIND US ON <a href="http://www.facebook.com/pages/Green-Pages-Community/135951849770296?ref=ts"><img src="http://www.thegreenpages.com.au/wp-content/uploads/2012/01/footer_facebook_7.gif" alt="facebook" width="30" height="30" align="absmiddle" border="0" /></a>&nbsp;&nbsp;<a href="http://twitter.com/GreenPagesAu"><img src="http://www.thegreenpages.com.au/wp-content/uploads/2012/01/footer_twitter_8.gif" alt="twitter" width="30" height="30" align="absmiddle" border="0" /></a></td>
				</tr>
			</table>
			<table cellpadding="0" cellspacing="0" border="0" width="640" style="background: rgb(250,250,250) url(http://www.thegreenpages.com.au/razor/footer_background.jpg); height: 358px; background-repeat: repeat-x;">
				<tr>
					<td style="color:grey; padding: 10px;" valign="top"><span style="text-transform:; text-decoration: none; font-weight:bold; font-size: 14px;">Find Products in the Green Pages Directory</span></td>
				</tr>
				<tr>
					<td valign="top" style="padding: 10px;" class="directory">
						<table cellpadding="0" cellspacing="0" border="0" width="100%">
							<tr>
								<td valign="top"><a href="http://www.thegreenpages.com.au/Green-Eco-Directory/Home+Building" style="font-weight:bold; text-decoration: none; color:grey;font-size: 13px;">Home & Building</a><br>
									
									<ul style="list-style-type: none;margin: 0 10px 0 0; line-height:20px; padding: 0 20px 0 0;font-size: 12px;">
										<li><a href="http://directory.thegreenpages.com.au/" style="color:grey;text-decoration:none;">Appliances</a></li>
										
										<li><a href="http://directory.thegreenpages.com.au/" style="color:grey;text-decoration:none;">Building Products</a></li>
										
										<li><a href="http://directory.thegreenpages.com.au/" style="color:grey;text-decoration:none;">Building Services</a></li>
										
										<li><a href="http://directory.thegreenpages.com.au/" style="color:grey;text-decoration:none;">Cleaning</a></li>
										
										<li><a href="http://directory.thegreenpages.com.au/" style="color:grey;text-decoration:none;">Energy Efficiency</a></li>
										
										<li><a href="http://directory.thegreenpages.com.au/" style="color:grey;text-decoration:none;">Gardening</a></li>
										
										<li><a href="http://directory.thegreenpages.com.au/" style="color:grey;text-decoration:none;">Home Products & Furnishings</a></li>
										
										<li><a href="http://directory.thegreenpages.com.au/" style="color:grey;text-decoration:none;">Information & Tools</a></li>
										
										<li><a href="http://directory.thegreenpages.com.au/" style="color:grey;text-decoration:none;">Solar</a></li>
										
										<li><a href="http://directory.thegreenpages.com.au/" style="color:grey;text-decoration:none;">Water Saving</a></li>
									</ul>
								</td>
								<td valign="top"><a href="http://www.thegreenpages.com.au/Green-Eco-Directory/Business" style="text-transform:; text-decoration:none; font-weight:bold; color:grey; font-size: 13px;">Business</a><br>
									<ul style="list-style-type: none;margin: 0 10px 0 0; line-height:20px; padding: 0 20px 0 0;font-size: 12px;">
										<li><a href="http://directory.thegreenpages.com.au/" style="color:grey;text-decoration:none;">Associations & Certifications</a></li>
										<li><a href="http://directory.thegreenpages.com.au/" style="color:grey;text-decoration:none;">Carbon Trading & Offsets</a></li>
										<li><a href="http://directory.thegreenpages.com.au/" style="color:grey;text-decoration:none;">Design & Marketing</a></li>
										
										<li><a href="http://directory.thegreenpages.com.au/" style="color:grey;text-decoration:none;">Environmental Services &<br>Consultants</a></li>
										<li><a href="http://directory.thegreenpages.com.au/" style="color:grey;text-decoration:none;">Finance</a></li>
										
										<li><a href="http://directory.thegreenpages.com.au/" style="color:grey;text-decoration:none;">Industrial Water Products</a></li>
										<li><a href="http://directory.thegreenpages.com.au/" style="color:grey;text-decoration:none;">Landscape</a></li>
										
										<li><a href="http://directory.thegreenpages.com.au/" style="color:grey;text-decoration:none;">Office Products & Fitout</a></li>
										<li><a href="http://directory.thegreenpages.com.au/" style="color:grey;text-decoration:none;">Printing & Paper</a></li>
										
										<li><a href="http://directory.thegreenpages.com.au/" style="color:grey;text-decoration:none;">Professional Training</a></li>
										<li><a href="http://directory.thegreenpages.com.au/" style="color:grey;text-decoration:none;">Recycling & Waste</a></li>
										
										<li><a href="http://directory.thegreenpages.com.au/" style="color:grey;text-decoration:none;">Technology</a></li>
									</ul>
								</td>
								<td valign="top"><a href="http://www.thegreenpages.com.au/Green-Eco-Directory/Life+Style" style="text-transform:; text-decoration:none; font-weight:bold; color:grey;font-size: 13px;">Life & Style</a><br>
									<ul style="list-style-type: none;margin: 0 10px 0 0; line-height:20px; padding: 0 20px 0 0;font-size: 12px;">
										<li><a href="http://directory.thegreenpages.com.au/" style="color:grey;text-decoration:none;">Beauty & Health</a></li>
										<li><a href="http://directory.thegreenpages.com.au/" style="color:grey;text-decoration:none;">Books, Mags & DVD</a></li>
										<li><a href="http://directory.thegreenpages.com.au/" style="color:grey;text-decoration:none;">Cars, Bikes & Scooters</a></li>
										<li><a href="http://directory.thegreenpages.com.au/" style="color:grey;text-decoration:none;">Children & Baby</a></li>
										<li><a href="http://directory.thegreenpages.com.au/" style="color:grey;text-decoration:none;">Consumer Electronics</a></li>
										<li><a href="http://directory.thegreenpages.com.au/" style="color:grey;text-decoration:none;">Eco Retail Stores</a></li>
										<li><a href="http://directory.thegreenpages.com.au/" style="color:grey;text-decoration:none;">Eco Travel</a></li>
										<li><a href="http://directory.thegreenpages.com.au/" style="color:grey;text-decoration:none;">Education & Short Courses</a></li>
										<li><a href="http://directory.thegreenpages.com.au/" style="color:grey;text-decoration:none;">Fashion & Clothing</a></li>
										<li><a href="http://directory.thegreenpages.com.au/" style="color:grey;text-decoration:none;">Green Organisations</a></li>
										<li><a href="http://directory.thegreenpages.com.au/" style="color:grey;text-decoration:none;">Money</a></li>
										<li><a href="http://directory.thegreenpages.com.au/" style="color:grey;text-decoration:none;">Organic Food & Beverages<br>Products</a></li>
										<li><a href="http://directory.thegreenpages.com.au/" style="color:grey;text-decoration:none;">Organic Stores, Cafes & Markets</a></li>
										<li><a href="http://directory.thegreenpages.com.au/" style="color:grey;text-decoration:none;">Pet Care</a></li>
										<li><a href="http://directory.thegreenpages.com.au/" style="color:grey;text-decoration:none;">Weddings & Events</a></li>
									</ul>
								</td>
							</tr>
						</table>
					</td>
				</tr>
				<tr>
					<td style="border-top:1px solid #fff; border-bottom: 1px solid #fff; height: 30px; text-align: center; color: grey;font-size:10px;">
						<a href="http://www.thegreenpages.com.au/index.asp?page_id=23" style="color:grey;text-transform:uppercase;text-decoration:underline;font-size:10px;">feedback</a> |
						<forwardtoafriend><span style="color:grey;text-transform:uppercase;text-decoration:underline;font-size:10px;">subscribe a friend</span></forwardtoafriend> |
						<a href="mailto:editor@thegreenpages.com.au" style="color:grey;text-transform:uppercase;text-decoration:underline;font-size:10px;">send a story idea</a> |
						<a href="http://www.thegreenpages.com.au/index.asp?page_id=290" style="color:grey;text-transform:uppercase;text-decoration:underline;font-size:10px;">add a gp link to your site</a> |
						<a href="http://www.thegreenpages.com.au/index.asp?page_id=17" style="color:grey;text-transform:uppercase;text-decoration:underline;font-size:10px;">advertise</a> |
						<a href="http://www.thegreenpages.com.au/index.asp?page_id=23" style="color:grey;text-transform:uppercase;text-decoration:underline;font-size:10px;">contact us</a> 
					</td>
				</tr>
				<tr>
					<td style="background-color: rgb(100,100,100); height: 50px; padding: 0 0 0 10px;color:#fff;font-size:10px; text-align:center;">
						&copy; 2011 Green Pages Pty Ltd functions under Creative Commons Copyright.<br /> No vibes were harmed making this website. <br /><br /><unsubscribe style="color:#fff;font-size:10px;">Unsubscribe</unsubscribe>
					</td>
				</tr>
			</table>
		</td>
	</tr>
</table>

    </td>
  </tr>
</table>

<?php

    #echo '<li><a href="';
    #the_permalink();
    #echo '">';
    #the_title();
    #echo '</a>';
    #echo "<br /><br />";
    #the_time('l, F, j, Y');
    #echo "<br /><br />";
    #the_author();
    #echo "<br /><br />";
    #the_content();
    #echo '</li>';
  #}
  #echo "</ol>";

} else if ($format == "txt") {

  while( $the_query->have_posts()) {
    $the_query->the_post();
    the_title();
    echo "\n\n";
    the_permalink();
    echo "\n\n";
    the_time('l, F, j, Y');
    echo "\n\n";
    the_author();
    echo "\n\n";
    the_content();
    echo "        ----------------------------------------          ";
    echo "\n\n";
  }
} else {
  echo 'ERROR: invalid format "' . $format . '".';
}

?>
