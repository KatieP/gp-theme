<?php
/* 
Template Name: Search
*/
?>

<?php get_header(); ?>

<div class="pos">

  <?php get_sidebar('left'); ?>

  	<div id="col2" class="set2col">
    	<div id="content">
    		<div id="cse" style="width: 100%;">Loading Search Results...</div>
    		
    		<script>
  				(function() {
    			var cx = '016746372392197706967:ucybeydkkyc';
    			var gcse = document.createElement('script');
    			gcse.type = 'text/javascript';
    			gcse.async = true;
    			gcse.src = (document.location.protocol == 'https:' ? 'https:' : 'http:') +
        		'//www.google.com/cse/cse.js?cx=' + cx;
    			var s = document.getElementsByTagName('script')[0];
    			s.parentNode.insertBefore(gcse, s);
  				})();
			</script>
			<gcse:searchresults-only></gcse:searchresults-only>

    	</div>
  	</div>

</div>

<?php get_footer(); ?>

