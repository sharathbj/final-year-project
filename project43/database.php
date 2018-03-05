<?php

ini_set("display_startup_errors", 1);
ini_set("display_errors", 1);
error_reporting(E_ALL);
session_start();
require_once __DIR__ . '/src/Facebook/autoload.php';

$db=new mysqli('localhost','root','123456','project43db');
if ($db->connect_error) {
	die('error:' .$db->connect_error);
}

$productName=$_REQUEST['pname'];

$fb = new Facebook\Facebook([
  'app_id' => '455670057959693',
  'app_secret' => 'b00b1d05b79ff53d5b0901518596b9b7',
  'default_graph_version' => 'v2.5',
]);

$helper = $fb->getRedirectLoginHelper();
$permissions = ['email']; 

try {
	if (isset($_SESSION['facebook_access_token'])) {
	$accessToken = $_SESSION['facebook_access_token'];
	} else {
  		$accessToken = $helper->getAccessToken();
	}
} catch(Facebook\Exceptions\FacebookResponseException $e) {
 
 	echo 'Graph returned an error: ' . $e->getMessage();
  	exit;
} catch(Facebook\Exceptions\FacebookSDKException $e) {
 	echo 'Facebook SDK returned an error: ' . $e->getMessage();
  	exit;
 }

if (isset($accessToken)) {

	if (isset($_SESSION['facebook_access_token'])) {
		$fb->setDefaultAccessToken($_SESSION['facebook_access_token']);
	} else {
		$_SESSION['facebook_access_token'] = (string) $accessToken;
		$oAuth2Client = $fb->getOAuth2Client();
		$longLivedAccessToken = $oAuth2Client->getLongLivedAccessToken($_SESSION['facebook_access_token']);

		$_SESSION['facebook_access_token'] = (string) $longLivedAccessToken;

		$fb->setDefaultAccessToken($_SESSION['facebook_access_token']);
	}

	try {
		$request = $fb->get('/me');
	} catch(Facebook\Exceptions\FacebookResponseException $e) {
		// When Graph returns an error
		if ($e->getCode() == 190) {
			unset($_SESSION['facebook_access_token']);
			$helper = $fb->getRedirectLoginHelper();
			$loginUrl = $helper->getLoginUrl('http://localhost/project43/database.php', $permissions);
			echo "<script>window.top.location.href='".$loginUrl."'</script>";
			exit;
		}
	} catch(Facebook\Exceptions\FacebookSDKException $e) {
		// When validation fails or other local issues
		echo 'Facebook SDK returned an error: ' . $e->getMessage();
		exit;
	}

	
	$getPostsComments = $fb->get("/$productName/posts?fields=id,comments{message,from}&limit=12");
	$Posts = $getPostsComments->getGraphEdge()->asArray();

	$sn=1;
	foreach ($Posts as $key) {
		$postid=$key['id'];
		if (isset($key['comments'])) {
			foreach ($key['comments'] as $key) {
				$comments=$key['message'];
				if (isset($key['from'])) {
					$userName = $key['from']['name'];
					$fbid= $key['from']['id'];	
				
			$sql = "INSERT INTO commentsTable (ID,postID,fbID,userName,comments)
	VALUES ( '{$sn}','{$postid}','{$fbid}', '{$userName}','{$comments}')";

		if ($db->query($sql) === TRUE) {
    			echo "New comment added to database successfuly ".$sn."<br>";
			$sn++;
		}else {
    			//echo "Error: " . $sql . "<br>" . $db->error;
	      	}
	   }
	}
   }
}
	$db->close();
} else {
	$helper = $fb->getRedirectLoginHelper();
	$loginUrl = $helper->getLoginUrl('http://localhost/project43/database.php', $permissions);
	echo "<script>window.top.location.href='".$loginUrl."'</script>";
}