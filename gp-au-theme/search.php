<?php
/* 
Template Name: Search
*/
?>

<?php get_header(); ?>

<div class="pos">

  <?php get_sidebar('left'); ?>

  <div id="col2" class="set3col">
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
    <link rel="stylesheet" href="//www.google.com/cse/style/look/default.css" type="text/css" />
    <style type="text/css">
      .gsc-control-cse {
        font-family: Arial, sans-serif;
        border-color: #FFFFFF;
        background-color: #FFFFFF;
      }
      .gsc-tabHeader.gsc-tabhInactive {
        border-color: #E9E9E9;
        background-color: #E9E9E9;
      }
      .gsc-tabHeader.gsc-tabhActive {
        border-top-color: #FF9900;
        border-left-color: #E9E9E9;
        border-right-color: #E9E9E9;
        background-color: #FFFFFF;
      }
      .gsc-tabsArea {
        border-color: #E9E9E9;
      }
      .gsc-webResult.gsc-result,
      .gsc-results .gsc-imageResult {
        border-color: #FFFFFF;
        background-color: #FFFFFF;
      }
      .gsc-webResult.gsc-result:hover,
      .gsc-imageResult:hover {
        border-color: #FFFFFF;
        background-color: #FFFFFF;
      }
      .gs-webResult.gs-result a.gs-title:link,
      .gs-webResult.gs-result a.gs-title:link b,
      .gs-imageResult a.gs-title:link,
      .gs-imageResult a.gs-title:link b {
        color: #01aed8;
      }
      .gs-webResult.gs-result a.gs-title:visited,
      .gs-webResult.gs-result a.gs-title:visited b,
      .gs-imageResult a.gs-title:visited,
      .gs-imageResult a.gs-title:visited b {
        color: #01aed8;
      }
      .gs-webResult.gs-result a.gs-title:hover,
      .gs-webResult.gs-result a.gs-title:hover b,
      .gs-imageResult a.gs-title:hover,
      .gs-imageResult a.gs-title:hover b {
        color: #01aed8;
      }
      .gs-webResult.gs-result a.gs-title:active,
      .gs-webResult.gs-result a.gs-title:active b,
      .gs-imageResult a.gs-title:active,
      .gs-imageResult a.gs-title:active b {
        color: #01aed8;
      }
      .gsc-cursor-page {
        color: #01aed8;
      }
      a.gsc-trailing-more-results:link {
        color: #01aed8;
      }
      .gs-webResult .gs-snippet,
      .gs-imageResult .gs-snippet {
        color: #666666;
      }
      .gs-webResult div.gs-visibleUrl,
      .gs-imageResult div.gs-visibleUrl {
        color: #61c201;
      }
      .gs-webResult div.gs-visibleUrl-short {
        color: #61c201;
      }
      .gs-webResult div.gs-visibleUrl-short {
        display: none;
      }
      .gs-webResult div.gs-visibleUrl-long {
        display: block;
      }
      .gsc-cursor-box {
        border-color: #FFFFFF;
      }
      .gsc-results .gsc-cursor-box .gsc-cursor-page {
        border-color: #E9E9E9;
        background-color: #FFFFFF;
        color: #01aed8;
      }
      .gsc-results .gsc-cursor-box .gsc-cursor-current-page {
        border-color: #FF9900;
        background-color: #FFFFFF;
        color: #01aed8;
      }
      .gs-promotion {
        border-color: #FFFFFF;
        background-color: #FFFFFF;
      }
      .gs-promotion a.gs-title:link,
      .gs-promotion a.gs-title:link *,
      .gs-promotion .gs-snippet a:link {
        color: #01aed8;
      }
      .gs-promotion a.gs-title:visited,
      .gs-promotion a.gs-title:visited *,
      .gs-promotion .gs-snippet a:visited {
        color: #01aed8;
      }
      .gs-promotion a.gs-title:hover,
      .gs-promotion a.gs-title:hover *,
      .gs-promotion .gs-snippet a:hover {
        color: #01aed8;
      }
      .gs-promotion a.gs-title:active,
      .gs-promotion a.gs-title:active *,
      .gs-promotion .gs-snippet a:active {
        color: #01aed8;
      }
      .gs-promotion .gs-snippet,
      .gs-promotion .gs-title .gs-promotion-title-right,
      .gs-promotion .gs-title .gs-promotion-title-right *  {
        color: #666666;
      }
      .gs-promotion .gs-visibleUrl,
      .gs-promotion .gs-visibleUrl-short {
        color: #61c201;
      }
      .gsc-input input.gsc-input {
        background: none repeat scroll 0% 0% white !important;
      }
    </style> 

    </div>
  </div>

  <?php get_sidebar('right'); ?>

</div>

<?php get_footer(); ?>

