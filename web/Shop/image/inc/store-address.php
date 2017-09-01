<?php
/*///////////////////////////////////////////////////////////////////////
Part of the code from the book
Building Findable Websites: Web Standards, SEO, and Beyond
by Aarron Walter (aarron@buildingfindablewebsites.com)
http://buildingfindablewebsites.com

Distrbuted under Creative Commons license
http://creativecommons.org/licenses/by-sa/3.0/us/
///////////////////////////////////////////////////////////////////////*/

//global $session;

function storeAddress($apikey, $listid){

//    $your_apikey = '78a6118343c6bf1cdade80bb4162e0b3-us9';
//    $my_list_unique_id = "e5cd1cb09f";
    $your_apikey = $apikey;
    $my_list_unique_id = $listid;

    // Validation
	if(!$_GET['email']){ return "No email address provided"; }

	if(!preg_match("/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*$/i", $_GET['email'])) {
		return "Email address is invalid!";
	}

	require_once('MCAPI.class.php');
	// grab an API Key from http://admin.mailchimp.com/account/api/
	$api = new MCAPI($your_apikey);
	
	// grab your List's Unique Id by going to http://admin.mailchimp.com/lists/
	// Click the "settings" link for the list - the Unique Id is at the bottom of that page.
	$list_id = $my_list_unique_id;

	if($api->listSubscribe($list_id, $_GET['email'], '') === true) {
		// It worked!
		return 'Success! Check your email to confirm.';
	}else{
		// An error ocurred, return error message
		return 'Error: ' . $api->errorMessage;
	}
	
}

// If being called via ajax, autorun the function
if(isset($_GET['ajax'])){
    echo storeAddress($_GET['apikey'], $_GET['listid']);
}