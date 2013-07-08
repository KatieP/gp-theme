<?php

/*
Template Name: AJAX Profile Data
*/

if (!$_POST) {
	echo '0';
	return false;
}

global $current_site, $current_user;

$post_tab = $_POST['tab'];
$post_type = $_POST['post'];
$post_page = $_POST['page'];
$post_pid = $_POST['pid'];

if (!isset($post_pid)) {
	echo '0';
	return false;
}

$post_tab_types = array('posts', 'favourites', 'analytics', 'advertise', 'following', 'topics', 'billing');

if ( !in_array(strtolower($post_tab), $post_tab_types) ) {
	$post_tab = "posts";
}

if ( !checkPostTypeSlug( strtolower($post_type) ) ) {
	if ( strtolower($post_type) != "directory" ) {
		$post_type = "all";
	}
}

if (!$post_page || !is_numeric($post_page)) {$post_page = 1;}

if ($post_tab == "posts") {
	theme_profile_posts($post_pid, $post_page, $post_tab, $post_type);
}

if ($post_tab == "topics") {
	theme_profile_topics($post_pid);
}

if ($post_tab == "following") {
	theme_profile_following($post_pid);
}

if ($post_tab == "favourites") {
	theme_profile_favourites($post_pid, $post_page, $post_tab, $post_type);
}

if ($post_tab == "analytics") {
	theme_profile_analytics($post_pid);
}

if ($post_tab == "advertise") {
	theme_profile_advertise($post_pid);
}

if ($post_tab == "billing") {
	theme_profile_billing($post_pid);
}

?>