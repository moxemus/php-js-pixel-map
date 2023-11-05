<?php

function getUserIpAddr()
{
    return match (true) {
        !empty($_SERVER['HTTP_CLIENT_IP']) => $_SERVER['HTTP_CLIENT_IP'],
        !empty($_SERVER['HTTP_X_FORWARDED_FOR']) => $_SERVER['HTTP_X_FORWARDED_FOR'],
        default => $_SERVER['REMOTE_ADDR']
    };
}

$userIp = getUserIpAddr();
$color = $_POST["color"] ?? null;
$index = $_POST["index"] ?? null;


$db = new PDO(
    'mysql:host=' . getenv('MYSQL_HOST') .
    ';dbname=' . getenv('MYSQL_DATABASE'),
    getenv('MYSQL_USER'),
    getenv('MYSQL_PASSWORD')
);

$result = $db->query("select updated from users where ipAddress = '$userIp'")->fetchAll();
print_r($result);
