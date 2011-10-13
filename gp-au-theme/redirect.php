<?php

/*
Template Name: Redirect

Why are we redirecting so many Pages to Posts? Because I think it's easier 
to keep Pages maintained in a heirachy by admins only. I think it would 
also look cleaner for post editors if they only had the main categories to 
choose from. Plus a lot of these pages aren't built yet but we can fill
them with post data for now.
*/

$redirect2IntPage301 = 	array(
						2  => 9 	/* About Us (Page) => Our Vision (Page) */
					);

$redirect2IntCategory301 = 	array(
					);

if (isset($redirect2IntPage301[$post->ID])) {
	$redirectPageId = $redirect2IntPage301[$post->ID];
}

if (is_numeric($redirectPageId)) {
	$permalink = get_permalink($redirectPageId);
}

if (isset($redirect2IntCategory301[$post->ID])) {
	$redirectCategoryId = $redirect2IntCategory301[$post->ID];
}

if (is_numeric($redirectCategoryId)) {
	$permalink = get_category_link($redirectCategoryId);
}

if (!empty($permalink)) {
	/* redirect 301 */
	header("HTTP/1.1 301 Moved Permanently");
	header("Status: 301 Moved Permanently");
	header('Location: '.$permalink);
} else {
	/* redirect 404 */
	header("HTTP/1.1 404 Not Found");
	header("Status: 404 Not Found");
}

?>