<?php
/*
Plugin Name: WP Digg This
Version: 0.1
Desciption:Automatically adds the Digg this button to your posts.
Author: Nilutpal Borgohain
Author URI:
Plugin URI:
*/

/*Version Check*/
global $wp_version;
$exit_msg='WP Digg This requires WordPress 2.5 or newer.
<a href="http://codex.wordpress.org/Upgrading_WordPress">Please
update!</a>';
if(version_compare($wp_version, "2.5","<"))
{
	exit($exit_msg);
}
/* Show a Digg This link */
function WPDiggThis_Link()
{
global $post;
// get the URL to the post
$link=urlencode(get_permalink($post->ID));
// get the post title
$title=urlencode($post->post_title);
// get first 350 characters of post and strip it off
// HTML tags
$text=urlencode(substr(strip_tags($post->post_content),
0, 350));
// create a Digg link and return it
return '<a href="http://digg.com/submit?url='.$link.'&amp;
title='.$title.'&amp;bodytext='.$text.'">Digg This</a>';
}


?>
