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
    <script src="//www.google.com/jsapi" type="text/javascript"></script>
    <script type="text/javascript"> 
      function parseQueryFromUrl () {
        var queryParamName = "q";
        var search = window.location.search.substr(1);
        var parts = search.split('&');
        for (var i = 0; i < parts.length; i++) {
          var keyvaluepair = parts[i].split('=');
          if (decodeURIComponent(keyvaluepair[0]) == queryParamName) {
            return decodeURIComponent(keyvaluepair[1].replace(/\+/g, ' '));
          }
        }
        return '';
      }

      var _gaq = _gaq || [];
      _gaq.push(["_setAccount", "UA-2619469-9"]);
      function _trackQuery(control, searcher, query) {
        var loc = document.location;
        var url = [
          loc.pathname,
          loc.search,
          loc.search ? '&' : '?',
          encodeURIComponent('\x22\x22\x22term\x22'),
          '=',
          encodeURIComponent(query)
        ];
        _gaq.push(["_trackPageview", url.join('')]);
      }

      google.load('search', '1', {language : 'en'});
      google.setOnLoadCallback(function() {
        var customSearchControl = new google.search.CustomSearchControl('016746372392197706967:0_ruhdktmek');
        customSearchControl.setResultSetSize(google.search.Search.FILTERED_CSE_RESULTSET);
        customSearchControl.setSearchStartingCallback(null, _trackQuery);
        var options = new google.search.DrawOptions();
        options.setAutoComplete(true);
        options.enableSearchResultsOnly(); 
        customSearchControl.draw('cse', options);
        var queryFromUrl = parseQueryFromUrl();
        if (queryFromUrl) {
          customSearchControl.execute(queryFromUrl);
        }
      }, true);
    </script>

    </div>
  </div>

</div>

<?php get_footer(); ?>

