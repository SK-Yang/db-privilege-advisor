<?php
/***************************************************
*************** DB Connection Config ***************
****************************************************
Fill your database connection info into those fields.
*/

$dbConf['user']		= 'test';
$dbConf['password'] = 'test';
$dbConf['dbName'] 	= 'test'; // Database Name
$dbConf['address'] 	= 'localhost'; // Server IP
$dbConf['port']		= '5432'; // Server Port

// The group name that this account member of(for Postgresql)
$dbConf['group']	= 'reader';

/*** DB Type ***
mysql: the test subject is MySQL.
postgre: the test subject is PostgreSQL.

(support mysql 5+, postgreSQL 9, didn't test the other version.)
*/
$dbConf['type']		= 'postgre'; 


/*** DB Role ***
super: super user of DB server.
replic: DB replication user.
project: individual DB project staff.
app: application user.
unknown: this program will just list the privliege it have. 
*/ 
$dbConf['role']		= 'unknown';

?>