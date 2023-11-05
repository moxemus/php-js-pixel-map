<?php

const TIME_LIMIT = 5 * 60;

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

if (!is_null($color) || !is_null(!$index)) {
    http_response_code(400);
    exit;
}

$color = (int)$color;
$index = (int)$index;

$time = time();;

$db = new PDO(
    'mysql:host=' . getenv('MYSQL_HOST') .
    ';dbname=' . getenv('MYSQL_DATABASE'),
    getenv('MYSQL_USER'),
    getenv('MYSQL_PASSWORD')
);

$result = $db->query("select updated from users where ipAddress = '$userIp'")->fetchColumn();
$updated = $result ?? null;

# If user appears at first time
$sql = '';
if (!$updated) {
    $sql = "insert into users (ipAddress, updated) values('$userIp', $time); " .
        "update map set color = $color where id = $index;";
} elseif (($updated + TIME_LIMIT) <= $time) {
    $sql = "update users set updated = $time where ipAddress = '$userIp';" .
        "update map set color = $color where id = $index;";
} else {
    http_response_code(400);
    exit;
}

$db->prepare($sql);
$db->exec($sql);

