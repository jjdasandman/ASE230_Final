<?php
// DB

//Configure credentials
$host='localhost';
$name='clothing_db_final';
$user='root'; //go to phpmyadmin > user accounts tab at the top to locate username/password to access DB
$pass='';

//Specify options
$opt = [
	PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
	PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
	PDO::ATTR_EMULATE_PREPARES => false
];

//Establish a connection to the db
$db=new PDO('mysql:host='.$host.';dbname='.$name.';charset=utf8mb4',$user,$pass,$opt);