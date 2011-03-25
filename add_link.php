<?php
if (!isset($_POST['url'])) { exit; }
session_start();

require '../.dbcreds';
$pdo = new PDO(sprintf('mysql:host=%s;dbname=%s', DBConfig::$host, DBConfig::$db), DBConfig::$user, DBConfig::$pass);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

set_time_limit(180);

$title = filter_input(INPUT_POST, 'title', FILTER_SANITIZE_STRING);
$url = filter_input(INPUT_POST, 'url', FILTER_SANITIZE_URL);

$matches = array();
preg_match('|https?://([^/])+|', $url, $matches);
$domain = $matches[1];
$title = !empty($title) ? $title : $domain;
print_r($_SESSION);
$userID = $_SESSION['userID'];
$userID = $_SESSION['userID'];

$status = exec('/usr/local/bin/mirror_page.php ' . $url . ' ' . $title);
if ($status == false) { exit; }

try
{
	$pdo->beginTransaction();

	$stmt = $pdo->prepare('INSERT INTO CachedDomains (name, firstGrabbed, isAlive) VALUES (?, NOW(), 1)');
	$stmt->execute(array($domain));
	$domainID = $pdo->lastInsertId('id');
	unset($stmt);

	$stmt = $pdo->prepare('INSERT INTO GrabbedURLs_v2 (url, title, last_fetched, domainID) VALUES (?, ?, NOW(), ?)');
	$stmt->execute(array($url, $title, $domainID));
	$urlID = $pdo->lastInsertId('id');
	unset($stmt);

	$stmt = $pdo->prepare('INSERT INTO UserURLs (userID, urlID) VALUES (?, ?)');
	$stmt->execute(array($userID, $urlID));

	$pdo->commit();
header('Location: http://' . $_SERVER['HTTP_HOST'] . '/users/profile.php?secret=' . $_SESSION['secret']);
}
catch(PDOException $e)
{
	echo 'PDO Exception: ' . $e->getMessage();
	$pdo->rollback();
}

