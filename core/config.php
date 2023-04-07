<?php
require_once __DIR__.'/../vendor/autoload.php';
$sitename = "testTube";
$pagename = ucwords(str_replace("_", " ", str_replace(".php", "", str_replace("/", " ", $_SERVER['DOCUMENT_URI'] ?? $_SERVER['PHP_SELF']))));
$devmode = true;

//version, precisely started being set before the day of public testing.
$version = "1.0.1";

if(isset($_SERVER["HTTP_CF_CONNECTING_IP"])) {
    $_SERVER["REMOTE_ADDR"] = $_SERVER["HTTP_CF_CONNECTING_IP"];
}

if(!$devmode) {
error_reporting(1);
$whoops = new \Whoops\Run;
$whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler);
$whoops->register();
}

$sql = [
    "dbname" => "testtube",
    "host" => "localhost",
    "port" => 3306,
    "user" => "root",
    "pass" => "plsseturown"
];

try {
    $con = new PDO("mysql:dbname=".$sql["dbname"].";host=".$sql["host"].";port=".$sql["port"], $sql["user"], $sql["pass"]);
    $con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
    unset($sql);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
