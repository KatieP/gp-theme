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
                                'posts_per_page' => 10,
                                'orderby' => 'date',
                                'order' => 'DESC',
                                'post_type' => 'gp_news'));

# print posts in html or txt format
$format = htmlspecialchars($_GET["format"]);
if ($format == "html" || $format == "") {
  echo "<ol>";
  while( $the_query->have_posts()) {
    $the_query->the_post();
?>

<table width="100%" style="font-size: 11px; font-family: helvetica; margin: 5px; background-color: rgb(255,255,255);">
	<tr>
		<td align="center">
			<table cellpadding="0" cellspacing="0" border="0" width="640" bgcolor="#fff" style="background-color: #fff;">
				<tr style="padding: 0 5px 5px 5px;">
					<td style="font-size: 10px;text-transform:uppercase;color:rgb(205,205,205);padding:0 0 0 5px;">Your daily news from the Green Pages Community</td>
          <td style="font-size: 10px;text-transform:uppercase;color:rgb(205,205,205);padding:0 5px 0 0;" align="right"><?php date("l, j F Y"); ?></td>
          <td>Hello!</td>
          <td><img src="http://www.thegreenpages.com.au/wp-content/uploads/2011/12/GESS2012cover_doctored.jpg?39a4ff" /></td>
				</tr>
			</table>
    </td>
  </tr>
</table>


<?php

  }
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
