<?php
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
