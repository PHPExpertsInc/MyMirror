<?php

if (isset($_POST['username']))
{
	require './.misc/showErrors.inc.php';
	require '../.dbcreds';

	$pdo = new PDO(sprintf('mysql:host=%s;dbname=%s', DBConfig::$host, DBConfig::$db), DBConfig::$user, DBConfig::$pass);
	try
	{
		$input = getUserInput();
		validateUser($pdo, $input['username'], $input['password']);
		registerUserAsLoggedIn($input['username']);
		takeToUserPage();
	}
	catch(RuntimeException $e)
	{
		showErrorMessage($e);
	}
}

function getUserInput()
{
	$username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
	$password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING);

	return array('username' => $username,
			     'password' => $password);
}

function validateUser(PDO $pdo, $username, $password)
{
	$stmt = $pdo->prepare('SELECT id FROM Users ' .
			              'WHERE username=? AND ' .
						  '      password=PASSWORD(?)');
	$stmt->execute(array($username, $password));
	$row = $stmt->fetch(PDO::FETCH_ASSOC);

	if (is_null($row))
	{
		throw new RuntimeException('Invalid username or password.');
	}

	session_start();
	$_SESSION['userID'] = $row['id'];
}

function registerUserAsLoggedIn($username)
{
	define('MIN_RAND', 1000);
	define('MAX_RAND', 1000000);

	$_SESSION['username'] = $username;
	$_SESSION['secret'] = rand(MIN_RAND, MAX_RAND);
}

function takeToUserPage()
{
	header('Location: http://' . $_SERVER['HTTP_HOST'] . '/users/profile.php?secret=' . $_SESSION['secret']);
}
?>
<html>
	<body>
		<h1>MyUser v1.0</h1>
		<div id="login_box">
			<form method="post">
				<h4>Log in: </h4>
				<div>Username: <input type="text" name="username" /></div>
				<div>Password: <input type="password" name="password" /></div>
				<div><input type="submit" value="log in"/></div>
			</form>
		</div>
	</body>
</html>
