<?php
require( dirname(__FILE__) . '/wp-load.php' );

function send_user_impressions_reminder_email ($post_id) {
  //do database queries
  $subject = "2 weeks ago...";
  $body = "you totally made a post 2 weeks ago!\n";
  if(mail($to, $subject, $body)) {
    echo("<p>Message successfully sent.</p>");
  } else {
    echo("<p>Message delivery failed!</p>");
  }
}

//echo("<p>Hello! You submitted your post two weeks ago on ");


//$targetDate = strtotime("2 weeks ago");
//echo(date("m-d-Y",$targetDate));

//echo(". Just wanted to let you know; maybe you want to post some more!</p>"\
//);

//Find all posts in db that match this date

//$key = $wpdb->get_var($wpdb->prepare("SELECT user_activation_key FROM $wpdb->users WHERE user_login = %s", $user_login));
//mysql_query("use `s1-wordpress`") or die(mysql_error());

//$post_date = mysql_query("SELECT * FROM wp_posts") or die(mysql_error());

//$post_date_row = mysql_fetch_array($post_date);

//var_dump($post_date_row);

?>
