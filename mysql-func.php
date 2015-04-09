<?php
/*******************************************************
***********     DB Privilege Advisor     ***************
********************************************************
Author: SK Yang 
Website: https://skcave.wordpress.com/
Version: 0.11

This file contained functions to examine the MySQL settings.
*/
require_once(dirname(__FILE__) . '/config.php');


// Determine which role that this account actually is
function chkGrant($str = NULL){
	$retStr = '';
	$patterns = array('#SELECT#', '#INSERT#', '#UPDATE#', '#DELETE#', '#FILE#', '#EXECUTE#', '#LOCK TABLES#',
				'#CREATE#', '#ALTER#', '#INDEX#', '#DROP#', '#EVENT#', '#CREATE VIEW#', '#CREATE TEMPORARY TABLES#', '#SHOW VIEW#', '#CREATE ROUTINE#', '#ALTER ROUTINE#', '#TRIGGER#', 
				'#REPLICATION CLIENT#', '#REPLICATION SLAVE#',
				'#RELOAD#', '#PROCESS#', '#SHUTDOWN#', '#SHOW DATABASES#', '#CREATE USER#'); 
	
	if(!isset($str)){
		return 'Error occured: No data.';
	}
	else{
		// Check if this is the message of global privilege
		if(preg_match('/\*\.\*/', $str, $matches)){
			$retStr = 'Global ';
		} 
		else $retStr = 'Local ';
		
		// Determine user privilege level
		if(preg_match('#ALL PRIVILEGES#', $str, $matches) || preg_match('#SUPER#', $str, $matches)){
			$retStr .= 'SUPERUSER';
		}
		else if(preg_match('#USAGE#', $str, $matches)){
			$retStr .= 'no privilege';
		}
		else {
			$lvl = 0;
			for($i = 0; $i < 25; $i++){
				if(preg_match($patterns[$i], $str, $matches)){
					$lvl = $i;
				}
			}
			
			if($lvl < 7){ // application user level
				$retStr .= 'application user';
			}
			else if($lvl < 18){// development staff level
				$retStr .= 'development staff';
			}
			else if($lvl < 20){// replication user level
				$retStr .= 'replication user';
			}
			else{// server admin level
				$retStr .= 'SUPERUSER';
			}
		}
		
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
		else if(($role == 'replic') && (preg_match('#SUPERUSER#', $grantRep, $matches))){
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
		if(($role == 'project') && ((preg_match('#SUPERUSER#', $grantRep, $matches)) || (preg_match('#development staff#', $grantRep, $matches)))){
			$advice = '<font color="green">Local privilege settings look good.</font>';
		}
		// Local application user
		else if(($role == 'app') && (preg_match('#application user#', $grantRep, $matches))){
			$advice = '<font color="green">Local privilege settings look good.</font>';
		}
		else if(preg_match('#no privilege#', $grantRep, $matches)){
			$advice = '<font color="#FFAA33">Looks like this account do not have any privilege to this database.</font>';
		}
		else $advice = '<font color="green">Local privilege settings look good.</font>';
	}
	
	return $advice;
}

// Perform test and generate result form
function mysqlResult(){
	global $dbConf;
	$report = '<table id="mysql_report" width="600"><tr><center>User Privilege Report</center></tr>';
	
	try{
		$conn = new PDO('mysql:host=' . $dbConf['address'] . ';charset=utf8', $dbConf['user'], $dbConf['password']);
		$rs = $conn->query('SHOW GRANTS');
		
		// Fetch grant results
		while($row = $rs->fetch()){
			$report .= '<tr><td>Database Privilege</td><td>' . $row[0] . '</td><td>';
			
			$grantRep = chkGrant($row[0]);
			$advice = getAdvice($dbConf['role'], $grantRep);
			
			$report .= $grantRep . '</td><td>' . $advice . '</td></tr>';
		}
		
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