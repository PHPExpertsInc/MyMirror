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

require '.misc/showErrors.inc.php';
require '../.dbcreds';

session_start();

class GrabbedURL
{
	protected $id;

	public $domain;
	public $url;
	public $title;
	public $last_fetched;
	public $first_added;
}

try
{
	$pdo = new PDO(sprintf('mysql:host=%s;dbname=%s', DBConfig::$host, DBConfig::$db), DBConfig::$user, DBConfig::$pass);

	ensureHasAccess();
	$userURLs = getUserURLs($pdo);
}
catch (RuntimeException $e)
{
	showErrorMessage($e);
}

function ensureHasAccess()
{
	$secret = filter_input(INPUT_GET, 'secret', FILTER_SANITIZE_NUMBER_INT);

	if (!isset($_SESSION['secret']) || $_SESSION['secret'] != $secret)
	{
		throw new RuntimeException('INVALID SECURITY TOKEN. Please <a href="/users/index.php">login again</a> to continue.');
	}
}

function getUserURLs(PDO $pdo)
{
	$stmt = $pdo->prepare('SELECT g.url, g.title, g.last_fetched, d.name domain ' .
			              'FROM UserURLs uu ' .
						  'JOIN GrabbedURLs_v2 g ON g.id=uu.urlID ' . 
						  'JOIN CachedDomains d ON d.id=g.domainID ' .
						  'WHERE uu.userID =? ' .
						  'ORDER BY g.last_fetched');
	$stmt->execute(array($_SESSION['userID']));

	$grabbedURLs = array();
	while (($url = $stmt->fetchObject('GrabbedURL')))
	{
		$grabbedURLs[] = $url;
	}

	return $grabbedURLs;
}
?>
<html>
	<body>
	    <h1>MyMirror</h1>
		<h2>Welcome, <?php echo $_SESSION['username']; ?>.</h2>
		<div id="links">
			<h3>My Links</h3>
			<ul id="links_list">
<?php
	foreach ($userURLs as /**@var GrabbedURL**/$url)
	{
?>
				<li><?php echo $url->last_fetched; ?> &mdash; <a href="<?php echo $url->url; ?>"><?php echo $url->title; ?></a> [<?php echo $url->domain; ?>]</li>
<?php
	}
?>
		</div>
		<div id="add_link">
			<h3>Mirror a new URL</h3>
			<form method="post" action="add_link.php">
				<div>Title: <input type="text" name="title" /></div>
				<div>URL: <input type="text" name="url" /></div>
				<div><input type="submit" value="add url"/></div>
			</form>
		</div>
	</body>
</html>
