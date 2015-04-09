<?php
/*******************************************************
***********     DB Privilege Advisor     ***************
********************************************************
Author: SK Yang 
Website: https://skcave.wordpress.com/
Version: 0.11

This file contains common functions for DB connection info examine.
*/
require_once(dirname(__FILE__) . '/config.php');

// Grade your password strength
function pwdChk(){
	global $dbConf;
	$grade = 0;
	$str = '';
	$patterns = array('#[a-z]#','#[A-Z]#','#[0-9]#','/[?!"¢G$%^&*()`{}\[\]:@~;\'#<>?,.\/\\-=_+\|]/'); 
	$length = strlen($dbConf['password']);
	
	// More than 7 digit is considered as a line to cross weak grade	
	if($length >= 7){	
		if($length >= 10) $grade+=2;
		
		foreach($patterns as $pattern)
		{
			if(preg_match($pattern, $dbConf['password'], $matches))
			{
				$grade++;
			}
		} 
	}
	
	if($grade > 4) 		$str = '<font color="green">STRONG</font>';
	else if($grade > 2) $str = '<font color="#FFAA33">FAIR</font>';
	else $str = '<font color="red">WEAK</font>';	
	
	return $str;
}

// Check the IP that you given is inside the LAN or not
function connIPChk($ip = NULL){	
	if(!isset($ip)){
		global $dbConf;
		$ip = $dbConf['address'];
	}

	// Don't ask me why I don't check out if the ip is less or equal 255:255:255:255. 
	// It is a basic knowledge if you are going to use this tool!
	if( ($ip === 'localhost') ||
		($ip === '127.0.0.1') || 
		($ip === '::1') || 
		(preg_match('/10\./', $ip)) || 
		(preg_match('/172\.(1[6-9]|2[0-9]|3[0-1])\./', $ip)) || 
		(preg_match('/192\.168\./', $ip)))
	{
		return '<font color="green">You\'re using this tool inside the private network.</font>';
	}
	else
	{
		return '<font color="red">You\'re using this tool to test DB via public area network!'
		.' YOUR CONNECTION INFO MIGHT LEAK!</font>';
	}
}

// Check you port setting is the default or not
function portChk(){
	global $dbConf;
	
	if(($dbConf['type'] == 'mysql') && ($dbConf['port'] == '3306')){
		if( preg_match('/public/', connIPChk()) ){
			return '<font color="red">Your DB might suffer direct attack like brute force or DoS with this setting</font>';
		}
		else {
			return '<font color="#FFAA33">Default port is ok if you are in the LAN, but changing port is recommended</font>';
		}
	}
	
	return '<font color="green">Look good</font>';
}

// Check the role that you mentioned to use
function roleChk(){
	global $dbConf;
	$str = '';
	
	// Access from public
	if( preg_match('/public/', connIPChk()) ){
		if(($dbConf['role'] != 'app') || ($dbConf['role'] != 'unknown')){
			$str = '<font color="red">This account should not be used from public network!</font>';
		}
		else $str = '<font color="orange">Not recommend if there is no special purpose to accessing from public network</font>';
	}
	else{
		if(($dbConf['role'] != 'app')){
			$str = '<font color="orange">Use with caution</font>';
		}
		else $str = '<font color="green">Look good</font>';
	}
	
	return $str;
}



?>
