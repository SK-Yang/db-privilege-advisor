<?php
/*******************************************************
***********     DB Privilege Advisor     ***************
********************************************************
Author: SK Yang 
Website: https://skcave.wordpress.com/
File Version: 0.12

This little program will help you to identify that your db settings have flaw or not,
then it will give you suggestion if there is a way to improve its security.

Disclaimer: 
I'm not responsible for any damage to your system while using it, so use it at your own risk.

Licensed under Apache 2.0, you may use this program freely for non-commericial use;
a license is required for commericial use.
*/
error_reporting(E_ALL ^ E_NOTICE);

require_once(dirname(__FILE__) . '/func/func.php');

if($dbConf['type'] === 'mysql'){
	require_once(dirname(__FILE__) . '/func/mysql-func.php');
}
else if($dbConf['type'] === 'postgre'){
	require_once(dirname(__FILE__) . '/func/postgre-func.php');
}
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<title>DB Privilege Advisor</title>
</head>
<body>
	<h1>DB Privilege Advisor</h1><br/>
	<p>This program will help you to identify how secure your database is with current settings,<br/>
	then it will give you suggestion if there are anything you can do to improve its security.
	</p><br/>
	<table id='conn_info' width='400'>
		<tr>
			<center>Database Connection Infomation</center>
		</tr>
		<tr>
			<td width='25%'>Server type</td>
			<td width='25%'><?php echo $dbConf['type']; ?></td>
			<td></td>
		</tr>
		<tr>
			<td width='25%'>Server IP</td>
			<td width='25%'><?php echo $dbConf['address']; ?></td>
			<td><?php echo connIPChk(); ?></td>
		</tr>
		<tr>
			<td width='25%'>Port</td>
			<td width='25%'><?php echo $dbConf['port']; ?></td>
			<td><?php echo portChk(); ?></td>
		</tr>
		<tr>
			<td width='25%'>DB name</td>
			<td width='25%'><?php echo $dbConf['dbName']; ?></td>
			<td></td>
		</tr>
		<tr>
			<td width='25%'>User account</td>
			<td width='25%'><?php echo $dbConf['user']; ?></td>
			<td></td>
		</tr>
		<tr>
			<td width='25%'>Password</td>
			<td width='25%'><?php echo $dbConf['password']; ?></td>
			<td><?php echo pwdChk(); ?></td>
		</tr>
		<tr>
			<td width='25%'>Account role</td>
			<td width='25%'><?php echo $dbConf['rold']; ?></td>
			<td><?php echo roleChk(); ?></td>
		</tr>
	</table><br/>
	<?php 
		if($dbConf['type'] === 'mysql'){
			echo mysqlResult();
		}
		else if($dbConf['type'] === 'postgre'){
			echo postgreResult();
		}
	?>
	<br/>
</body>
</html>