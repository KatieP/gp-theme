<?php
#error_reporting(E_ERROR | E_PARSE);


set_include_path(get_include_path() . PATH_SEPARATOR . '/usr/share/google-api-php-client/src');
require_once 'apiClient.php';
require_once 'contrib/apiAnalyticsService.php';

session_start();

$client = new apiClient();
$client->setScopes('https://www.googleapis.com/auth/analytics.readonly');
$client->setApplicationName("Green Pages Analytics");
$client->setAccessType('offline');
$client->setApprovalPrompt('force'); # or 'force'
$client->setClientId('565608134305.apps.googleusercontent.com');
$client->setClientSecret('C_BIVs9mUJ_eCIVynsevmLfQ');
$client->setRedirectUri('http://www.thegreenpages.com.au/wp-content/themes/gp-au-theme/cron/ga_profile_views.php');
// $client->setAccessToken($config['token']);
// $client->refreshToken('1\/ZUtUTGd7uvuiBw_-QnVirEcfGEzkepP5_5ANtDkuwks');
// $client->setDeveloperKey('insert_your_developer_key');
$service = new apiAnalyticsService($client);

mysql_connect("localhost", "s1-wordpress", "7BXmxPmwy4LJZNhR") or die(mysql_error());
mysql_select_db("s1-wordpress") or die(mysql_error());



//$client->authenticate();
//var_dump($client->getAccessToken());

/*
if (isset($_GET['logout'])) {
	unset($_SESSION['token']);
}

var_dump($_SESSION['token']);
if (isset($_GET['code'])) {
	$client->authenticate();
	$_SESSION['token'] = $client->getAccessToken();
	header('Location: http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF']);
}

if (isset($_SESSION['token'])) {
	$client->setAccessToken($_SESSION['token']);
}

if ($client->getAccessToken()) {
	$props = $service->management_webproperties->listManagementWebproperties("~all");
	print "<h1>Web Properties</h1><pre>" . print_r($props, true) . "</pre>";

	$accounts = $service->management_accounts->listManagementAccounts();
	print "<h1>Accounts</h1><pre>" . print_r($accounts, true) . "</pre>";

	$segments = $service->management_segments->listManagementSegments();
	print "<h1>Segments</h1><pre>" . print_r($segments, true) . "</pre>";

	$goals = $service->management_goals->listManagementGoals("~all", "~all", "~all");
	print "<h1>Segments</h1><pre>" . print_r($goals, true) . "</pre>";

	$_SESSION['token'] = $client->getAccessToken();
} else {
	$authUrl = $client->createAuthUrl();
	print "<a class='login' href='$authUrl'>Connect Me!</a>";
}
*/

/*
define('ga_email','greenpagesadserving@gmail.com');
define('ga_password','greenpages01');
define('ga_profile_id','42443499');

require('../lib/gapi-1.3/gapi.class.php');

$ga = new gapi(ga_email,ga_password);

mysql_connect("localhost", "s1-wordpress", "7BXmxPmwy4LJZNhR") or die(mysql_error());
mysql_select_db("s1-wordpress") or die(mysql_error());

$subscribers = mysql_query("SELECT wp_users.ID, wp_users.user_nicename FROM wp_users LEFT JOIN wp_usermeta on wp_usermeta.user_id=wp_users.ID;");

$filter = "";
$counter= 0;

if (ob_get_level() == 0) ob_start();

while ($subscriber = mysql_fetch_assoc($subscribers)) {
	$filter .= "pagePath == /profile/" . $subscriber['user_nicename'] . "/ || ";
	
	$counter = $counter+1;
	if ($counter % 20 == 0) {
	
		$ga->requestReportData(ga_profile_id, array('pagePath'), array('pageviews'), null, $filter);

		foreach($ga->getResults() as $result) {
			echo $result->getPageviews() . " " . $result->getPagepath() . "<br />";
			if ( $result->getPagepath() == "" ) {
				#replace into
			}
		}
		echo "<br />";
		ob_flush();
    	flush();
		
		sleep(2);
		
		$filter = "";
	}
}

ob_end_flush();
*/
?>