<?php
//get the class
require('../ga/analytics.class.php');
//Google Analytics Script

//hack: this should be included from gp-core.php in the greenpages theme plugin
//but I'm not sure how to init this file using wordpress properly, so we're
//just copying here until it becomes high enough priority to fix

$post_type_to_url_part = array("gp_news" => "news",
                               "gp_events" => "events",
                               "gp_advertorial" => "new-stuff",
                               "gp_competitions" => "competitions",
                               "gp_people" => "people",
                               "gp_ngocampaign" => "ngo-campaign");

//Mysql commands
mysql_connect("localhost", "s1-wordpress", "7BXmxPmwy4LJZNhR") or die(mysql_error());
mysql_select_db("s1-wordpress") or die(mysql_error());
$result = mysql_query("SELECT wp_posts.post_name AS post_url
, date(post_date) AS date
, wp_users.display_name AS user_name
, user_email as email
, post_title AS title
, post_content AS content
, post_type AS type
FROM wp_posts 
JOIN wp_users ON wp_posts.post_author = wp_users.ID
WHERE date(post_date) = date(date_sub(now(), INTERVAL 2 WEEK))
AND post_type like 'gp_%'
AND post_status = 'publish'");

while ($row = mysql_fetch_assoc($result)) {
/* 	print_r($row); */
	email_post($row);
}
exit;

//When querying the Google Analytics API for just a specific URL, add a filter for ga:pagePath set to an exact match for the URL you wish to query.
//This will be $post_url
//&filters=ga:pagePath$post_url
//http://www.electrictoolbox.com/google-analytics-api-single-page-data/


function email_post($row) {
	
	extract($row); // take the array from the database and put it into the variables

  global $post_type_to_url_part;
	
	$post_url_end = $post_type_to_url_part[$type] . "/" . $post_url;
 	$post_url = "http://www.thegreenpages.com.au/" . $post_url_end;


  //Google Analytics API
  //UA-2619469-9

  //sign in and grab profile
  $analytics = new analytics('greenpagesadserving@gmail.com', 'greenpages01');
  //$analytics->setProfileByName('Stage 1 - Green Pages');
  $analytics->setProfileById('ga:42443499');
  //set the date range for which I want stats for 
  $post_date = $date;
  $new_date = date('Y-m-d');
  $analytics->setDateRange($post_date, $new_date);
      
  //Page views for specific URL
  $pageViewURL = ($analytics->getPageviewsURL($post_url_end));
  $sumURL = 0;
  foreach ($pageViewURL as $data) {
    $sumURL = $sumURL + $data;
  }

  //Page views for the section landing page, e.g., the news page
  $pageViewType = ($analytics->getPageviewsURL($post_type_to_url_part[$type]));
  $sumType = 0;
  foreach ($pageViewType as $data) {
      $sumType = $sumType + $data;
  }

  // Send the email
	
 	$to = "katiepatrickgp@gmail.com";
	//$to = $email;
  $bcc = "katiepatrickgp@gmail.com, scmelton@gmail.com, eddy.respondek@gmail.com, jessebrowne78@gmail.com";
	$subject="Report: Your post from " . $date . " has gotten a bunch of visitors!";
	$body = '<table width="600px" style="font-size: 15px; font-family: helvetica, arial, tahoma; margin: 5px; background-color: rgb(255,255,255);">';
	$body .= '	<tr><td align="center">';
	$body .= '	<table width="640">';
	$body .= '	<tr style="padding: 0 20px 5px 5px;">';
	$body .= '	<td style="font-size: 18px;text-transform:none;color:rgb(100,100,100);padding:0 0 0 5px;">';
	$body .= "Hi " . $user_name . ",<br /><br /> 
	The " . $post_type_to_url_part[$type] . " section of Green Pages where you article is displayed  has received " .$sumType  . " page views in the last 2 weeks!<br /><br /> 
	Your article from " . $date . ' <a href="' . $post_url . '"><font color="#01aed8">' . $post_url . "</font></a> has individually received " . $sumURL . " page views!<br /><br />" .
	'You are awesome!<br /><br />The GP Team<br /><br /><a href="http://www.thegreenpages.com.au/wp-admin/"><font color="#01aed8">Upload another super-amazing post</font></a><br /><br />';
	$body .= '<hr /><br /><nr />';
	$body .= '<div style="font-size:14px;"><em>"Never doubt that a small group of thoughtful, committed, citizens can change the world. <br /> Indeed, it is the only thing that ever has." Margaret Mead</em></div>';
	$body .= '</td></tr></table></td></tr></table><br /><br /><br /><br />';

  $headers = 'Content-type: text/html' . "\r\n";
  $headers .= 'Bcc: ' . $bcc . "\r\n";
	
	if (mail($to, $subject, $body, $headers)) {
	echo("<p>Message successfully sent</p>");
	} else {
	echo("<p>Message delivery failed</p>");
	}
	
	echo "<br><br>====================<br><br>To $email:<br><br>$body";
}
