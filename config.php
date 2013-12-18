<?php

session_start();
$config = array(
    "DB" => array(
        "db_host" => "localhost",
        "db_user" => "root",
        "db_pass" => "astalavista",
        "db_name" => "mysite"
    ),
    "absolutePath" => "/home/serge/web/mysite/",
    "rootUrl" => "http://mysite.com/",
    "siteTitle" => "My first site",
    "charset" => "UTF-8"
);

define('SNIPPETS_PATH', $config['absolutePath'] . "template/snippets/");
define('MODULES_PATH', $config['absolutePath'] . "modules/");
define('TEMPLATES_PATH', $config['absolutePath'] . "template/");
define('ABSOLUTE_PATH', $config['absolutePath']);

error_reporting(E_ALL | E_STRICT);

mb_internal_encoding($config['charset']);

$db = new PDO("mysql:host={$config['DB']['db_host']};dbname={$config['DB']['db_name']}", "{$config['DB']['db_user']}", "{$config['DB']['db_pass']}");
$db->exec("	SET NAMES UTF8;
		SET CHARACTER SET UTF8;");
?>