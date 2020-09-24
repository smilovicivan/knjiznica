<?php


error_reporting(E_ALL);
ini_set('display_errors', 1);

// pokrenut session za spreamnje podataka logiranog korisnika
session_start();

//putanja do root-a projekta
$path = '/';


// konekcija na bazu podataka
$connect = new PDO("mysql:host=localhost;dbname=","","");
$connect->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
$connect->exec("set names utf8;");