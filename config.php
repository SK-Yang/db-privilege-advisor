<?php
/***************************************************
*************** DB Connection Config ***************
****************************************************
Fill your database connection info into those fields.
*/

$dbConf['user']		= 'test';
$dbConf['password'] = 'password';
$dbConf['dbName'] 	= 'test'; // Database Name
$dbConf['address'] 	= 'localhost'; // Server IP
$dbConf['port']		= '1234'; // Server Port
$dbConf['type']		= 'mysql'; // what kind of db is it(support mysql 5+ now)
/*** DB Role ***
super: super user of DB server.
replic: DB replication user.
project: individual DB project staff.
app: application user.
unknown: this program will just list the privliege it have. 
*/ 
$dbConf['role']		= 'unknown';

?>