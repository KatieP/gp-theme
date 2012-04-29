<?php
require('../ga/analytics.class.php');

function days_diff($date_a, $date_b){
	$seconds_between = @strtotime(date_format($date_a, "Y-m-d")) - @strtotime(date_format($date_b, "Y-m-d"));
	return $seconds_between / (60*60*24);
}

//Mysql commands
mysql_connect("localhost", "s1-wordpress", "7BXmxPmwy4LJZNhR") or die(mysql_error());
mysql_select_db("s1-wordpress") or die(mysql_error());

$accounts = mysql_query(
	"SELECT DISTINCT 
		wp_users.display_name AS user_name,
		user_email as email
	FROM wp_posts 
	JOIN wp_users ON wp_posts.post_author = wp_users.ID
	WHERE post_type like 'gp_%'
		AND post_status = 'publish' order by email"
);

$total = mysql_num_rows($accounts);

$count = 0;
while ($row = mysql_fetch_assoc($accounts)) {
	$count = $count + 1;
	email_post($row, $count, $total);
}

exit;

//When querying the Google Analytics API for just a specific URL, add a filter for ga:pagePath set to an exact match for the URL you wish to query.
//This will be $post_url
//&filters=ga:pagePath$post_url
//http://www.electrictoolbox.com/google-analytics-api-single-page-data/

function email_post($row, $count, $total) {

	if (!filter_var($row['email'], FILTER_VALIDATE_EMAIL)) {
		return;
	}
	
	$posts = mysql_query(
		"SELECT
			wp_posts.post_name AS post_url,
			post_date AS date,
			post_title AS title,
			post_content AS content,
			post_type AS type
		FROM wp_posts 
		JOIN wp_users ON wp_posts.post_author = wp_users.ID
		WHERE post_type like 'gp_%'
			AND post_status = 'publish' 
			AND user_email = '" . $row['email'] . "'
		ORDER BY post_date DESC"
	);
	
	$post_type_map = array(
		"gp_news" => "news", 
		"gp_events" => "events", 
		"gp_advertorial" => "eco-friendly-products", 
		"gp_competitions" => "competitions", 
		"gp_people" => "people", 
		"gp_projects" => "projects"
	);

	//sign in and grab profile
	$analytics = new analytics('greenpagesadserving@gmail.com', 'greenpages01');
	$analytics->setProfileById('ga:42443499');
	
    $articles = "";
    $totalURL = 0;
    $numPosts = 0;
    $first_post_date = "";
    
  	while ($postrow = mysql_fetch_assoc($posts)) {
  	
  		extract($postrow);
  	
  		$mysqldate = @strtotime($date);
  
		$post_url_end = "/" . $post_type_map[$type] . "/" . $post_url . "/";
		$post_url = "http://www.thegreenpages.com.au/" . $post_type_map[$type] . "/" . $post_url;
		
		$post_date = @date('Y-m-d', $mysqldate);
		$posted = @date('j F Y', $mysqldate);
		$new_date = @date('Y-m-d');
  
		//$analytics->setMonth(date('$post_date'), date('$new_date'));
		$analytics->setDateRange($post_date, $new_date);
      
		//var_dump($analytics->getProfileList());
		//get array of visitors by day
		//print_r($analytics->getVisitors());

		//Page views for specific URL
		$pageViewURL = ($analytics->getPageviewsURL($post_url_end));
		$sumURL = 0;
		
		foreach ($pageViewURL as $data) {
			$sumURL = $sumURL + $data;
		}
		
		if ($sumURL > 10) {
			$totalURL = $totalURL + $sumURL;
			$numPosts = $numPosts + 1;
			$first_post_date = @date('F Y', $mysqldate);
			$articles .= "<tr><td align=\"left\" style=\"padding:8px;border:1px solid #ccc;\"><a href=\"" . $post_url . "\"><font color=\"#01aed8\">" . $title . "</font></a><br /><span style=\"font-size: 12px;\">Posted: " . $posted . "</span></td><td align=\"left\" style=\"padding:8px;border:1px solid #ccc;\">" . $sumURL . "</td></tr>\n";
		}

		// Get page views for section landing page, e.g., news, for up to 2 weeks 
		// after post was made

		//sign in and grab profile
		//$analytics = new analytics('greenpagesadserving@gmail.com', 'greenpages01');
		
		//$analytics->setProfileByName('Stage 1 - Green Pages');
		//$analytics->setProfileById('ga:42443499');
		
		//set the date range for which I want stats for (could also be $analytics->setDateRange('YYYY-MM-DD', 'YYYY-MM-DD'))
		//$later_date = @date_create($date);
		
		//date_add($later_date, date_interval_create_from_date_string('2 weeks'));
		
		//if ($later_date > @date_create(date('Y-m-d'))) {
			//$later_date = @date_create(date('Y-m-d'));
		//}
		
		//$numDaysDisplayedType = days_diff($later_date, @date_create($post_date));

		//$analytics->setMonth(date('$post_date'), date('$new_date'));
		//echo "\n\npost date should be: " . $post_date . "\n\n";
		//echo "2 weeks after post date should be: " . date_format($later_date,"Y-m-d") . "\n";
		//$analytics->setDateRange($post_date, date_format($later_date,"Y-m-d"));
      
		//var_dump($analytics->getProfileList());
		
		//get array of visitors by day
		//print_r($analytics->getVisitors());
		
		//Page views for the section landing page, e.g., the news page
		//$pageViewType = ($analytics->getPageviewsURL($post_type_map[$type]));
		//$sumType = 0;
		//foreach ($pageViewType as $data) {
		//$sumType = $sumType + $data;
		//}
		
		$keywords = $analytics->getData(
			array(
				'dimensions' => 'ga:keyword',
				'metrics' => 'ga:visits',
				'sort' => 'ga:keyword'
			)
		);
	}
	
	if ($numPosts > 0) {
		// Send the email
		
		$to = "eddy.respondek@gmail.com";
		//$to = $row['email'];
		$bcc = "";
		$subject = "Green Pages Analytics Report: Your posts have received " . $totalURL . " new visitors since " . $first_post_date . "!";
		$body = '<table width="600px" style="font-size: 15px; font-family: helvetica, arial, tahoma; margin: 5px; background-color: rgb(255,255,255);">';
		$body .= '	<tr><td align="center">';
		$body .= '	<table width="640">';
		$body .= '	<tr style="padding: 0 20px 5px 5px;">';
		$body .= '	<td style="font-size: 18px;text-transform:none;color:rgb(100,100,100);padding:0 0 0 5px;">';
		$body .= 'Hi ' . $row['user_name'] . ',<br /><br />';
		$body .= 'Since ' . $first_post_date . ' you have created <b>' . $numPosts . '</b> posts on <a href="http://www.thegreenpages.com.au/"><font color="#01aed8">Green Pages</font></a>. Collectively they have received <b>' . $totalURL . '</b> page views!<br /><br /><b>Here\'s a breakdown:</b><br /><br />';
		$body .= '<table style="font-size: 14px;">';
		$body .= '<tr><th align="left" style="padding:8px;border:1px solid #666;background-color:#fcfcfc;">Post</th><th align="left" style="padding:8px;border:1px solid #666;background-color:#fcfcfc;">Visits</th></tr>';
		$body .= $articles;
		$body .= '</table><br /><br />';
		$body .= 'You are awesome!<br /><br />The GP Team<br /><br />';
		$body .= '<a href="http://www.thegreenpages.com.au/wp-admin/">';
		$body .= '<font color="#01aed8">Upload another super-amazing post</font></a><br /><br />';
		$body .= '<hr /><br /><nr />';
		$body .= '<div style="font-size:14px;"><em>"Never doubt that a small group of ';
		$body .= 'thoughtful, committed, citizens can change the world. <br /> ';
		$body .= 'Indeed, it is the only thing that ever has." Margaret Mead</em></div>';
		$body .= '</td></tr></table></td></tr></table><br /><br /><br /><br />';
		
		$headers = 'Content-type: text/html' . "\r\n";
		$headers .= 'From: "Green Pages" <no-reply@thegreenpages.com.au>' . "\r\n";
	   	$headers .= 'Reply-To: "Green Pages" <no-reply@thegreenpages.com.au>' . "\r\n";
	   	$headers .= 'Bcc: ' . $bcc . "\r\n";
	   	
	   	if (mail($to, $subject, $body, $headers)) {
			echo("Message [" . $count . "/" . $total . "] SENT " . $row['email'] . "\n");
		} else {
			echo("Message [" . $count . "/" . $total . "] FAILED " . $row['email'] . "\n");
		}
	} else {
		echo("Message [" . $count . "/" . $total . "] SKIPPED " . $row['email'] . "\n");
	}
}

?>