<?php
// This file is a part of the MyMirror Project, a PHP University Project.
//
// Copyright (c) 2012 Theodore R.Smith (theodore@phpexperts.pro)
// DSA-1024 Fingerprint: 10A0 6372 9092 85A2 BB7F  907B CB8B 654B E33B F1ED
// Provided by the PHP University (www.phpu.cc)
//
// This file is dually licensed under the terms of the following licenses:
// * Primary License: OSSAL - Open Source Software Alliance License
//   * Key points:
//       5.Redistributions of source code in any non-textual form (i.e.
//          binary or object form, etc.) must not be linked to software that is
//          released with a license that requires disclosure of source code
//          (ex: the GPL).
//       6.Redistributions of source code must be licensed under more than one
//          license and must not have the terms of the OSSAL removed.
//   * See http://repo.phpexperts.pro/license-ossal.html for complete details.
//
// * Secondary License: Creative Commons Attribution License v3.0
//   * Key Points:
//       * You are free:
//           * to copy, distribute, display, and perform the work
//           * to make non-commercial or commercial use of the work in its original form
//       * Under the following conditions:
//           * Attribution. You must give the original author credit. You must retain all
//             Copyright notices and you must include the sentence, "Based upon work from the
//             the PHP University (www.phpu.cc).", wherever you list contributors and authors.
//   * See http://creativecommons.org/licenses/by/3.0/ for complete details.

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

