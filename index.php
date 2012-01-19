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

if (isset($_POST['username']))
{
	require './misc/showErrors.inc.php';
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
		<h1>MyMirror</h1>
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
