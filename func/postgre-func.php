<?php
/*******************************************************
***********     DB Privilege Advisor     ***************
********************************************************
Author: SK Yang 
Website: https://skcave.wordpress.com/
File Version: 0.1
Date: 05/05/2015

This file contained functions to examine the PostgreSQL settings.
*/
require_once(dirname(dirname(__FILE__)) . '/config/config.php');

// Determine that this user have the DDL or superuser privilege or not
function chkPriv($str){
	$retStr = 'Global ';
	
	if($str['rolsuper'] == true){
		$retStr .= 'SUPERUSER, ';
	}
	
	if($str['rolcreaterole'] == true){
		$retStr .= 'CREATE USER, ';
	}
	
	if($str['rolcreatedb'] == true){
		$retStr .= 'CREATE DATABASE, ';
	}
	
	if($str['rolcatupdate'] == true){
		$retStr .= 'CATALOGS MODIFY, ';
	}
	
	if($str['rolreplication'] == true){
		$retStr .= 'REPLICATION, ';
	}
	
	if($retStr == ''){
		$retStr .= 'no privilege';
	}
	
	return $retStr;
}

// Generate advice report for account current privileges
function getAdvice($role, $grantRep){
	$advice = '';

	if(preg_match('#Global#', $grantRep, $matches)){
		// Global superuser advice
		if(($role == 'super') && (preg_match('#SUPERUSER#', $grantRep, $matches))){
			$advice = '<font color="green">Global privilege settings look good.</font>';
		}
		// Global replication user advice
		else if(($role == 'replic') && (preg_match('#REPLICATION#', $grantRep, $matches))){
			$advice = '<font color="green">Global privilege settings look good.</font>';
		}
		// Other level user is not supposed to have any privilege on global
		else if(!preg_match('#no privilege#', $grantRep, $matches)){
			$advice = '<font color="red">User which is not a Admin or for Replication purposes are not suppose to have any global privilege.</font>';
		}
		// No privilege at global
		else{
			if($role == 'super'){
				$advice = '<font color="#FFAA33">Looks like its privilege is not set properly.</font> Grant all priveileges on global to this account.';				
			}
			else if($role == 'replic'){
				$advice = '<font color="#FFAA33">Looks like its privilege is not set properly.</font> Grant replication and CRUD privileges on global to this account.';
			}
			else $advice = '<font color="green">Global privilege settings look good.</font>';
		}
	}
	else {
		// Local development staff, got partial or full control of own database
		if(($role == 'project') && (preg_match('#SUPERUSER#', $grantRep, $matches) || preg_match('#development staff#', $grantRep, $matches))){
			$advice = '<font color="green">Local privilege settings look good.</font>';
		}
		// Local application user
		else if(($role == 'app') && (preg_match('#application user#', $grantRep, $matches))){
			$advice = '<font color="green">Local privilege settings look good.</font>';
		}
		else if(!preg_match('#no privilege#', $grantRep, $matches)){
			$advice = '<font color="#FFAA33">Looks like this account do not have any privilege to this database.</font>';
		}
		else $advice = '<font color="green">Local privilege settings look good.</font>';
	}
	
	return $advice;
}

// Perform test and generate result form
function postgreResult(){
	global $dbConf;
	$report = '<table id="postgre_report" width="600">';
	
	try{
		$conn = new PDO('pgsql:host=' . $dbConf['address'] . ';port=' . $dbConf['port'] . ';user=' . $dbConf['user'] . ';password="' . $dbConf['password'] .';dbname=' . $dbConf['dbName']);
		
		$rs = $conn->query('SELECT rolsuper, rolcreaterole, rolcreatedb, rolcatupdate, rolreplication FROM pg_roles WHERE rolname="' . $dbConf['user'] . '"');
		
		// Fetch DDL grant results
		$row = $rs->fetch();
		$grantRep = chkPriv($row);
		$report .= '<tr><td>Database Privilege</td><td>';
			
		$advice = getAdvice($dbConf['role'], $grantRep);
		
		$report .= $grantRep . '</td><td>' . $advice . '</td></tr>';
		
		$conn = null;
		$report .= '</table>';
		return $report;
	}
	catch(PDOException $e) {
		$str  = '<tr><td>Error: ' . $e->getMessage() . '</td></tr></table>';
		$conn = null;
		return $str;
	}
}

?>