<?php

/*
Template Name: like
*/

global $current_site;

if ( is_user_logged_in() ) {
	global $current_user;
	$post_userid = $current_user->ID;
}

$post_id = str_replace('post-', '', $_POST['id']);
$post_action = $_POST['action'];
$post_what = $_POST['what'];
$epochtime = strtotime('now');

if ( !is_numeric($post_id) ) {
	echo '0';
	return false;
}

$post_action_types = array('add', 'remove');
if ( !in_array($post_action, $post_action_types) ) {
	echo '0';
	return false;
}

$post_what_types = array('likepost');
if ( !in_array($post_what, $post_what_types) ) {
	echo '0';
	return false;
}

if ( !isset($post_userid) ) {
	echo '0';
	return false;
}

if ( !isset($current_site->id) ) {
	echo '0';
	return false;
}

if ( $post_action == 'add' ) {
	update_usermeta($post_userid, $post_what . '-' . $current_site->id . '-' . $post_id, $epochtime);
}

if ( $post_action == 'remove' ) {
	delete_user_meta($post_userid, $post_what . '-' . $current_site->id . '-' . $post_id);
}
?>