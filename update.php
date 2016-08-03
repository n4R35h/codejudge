<?php

	include('functions.php');
	connectdb();
	if($_POST['action']=='email') {
		// change the email id of the user
		if(trim($_POST['email']) == "")
			header("Location: account.php?derror=1");
		else {
			$result = mysqli_query($db,"UPDATE users SET email='".mysql_real_escape_string($_POST['email'])."' WHERE username='".$_SESSION['username']."'");if (!$result) {   die('Invalid query: ' . mysqli_error($result));}
			header("Location: account.php?changed=1");
		}
	} else if($_POST['action']=='password') {
		// change the password of the user
		if(trim($_POST['oldpass']) == "" or trim($_POST['newpass']) == "")
			header("Location: account.php?derror=1");
		else {
			$query = "SELECT salt,hash FROM users WHERE username='".$_SESSION['username']."'";
			$result = mysqli_query($db,$query);
			if (!$result) {   die('Invalid query: ' . mysqli_error($result));}
			$fields = mysqli_fetch_array($result);
			$currhash = crypt($_POST['oldpass'], $fields['salt']);
			if($currhash == $fields['hash']) {
				$salt = randomAlphaNum(5);
				$newhash = crypt($_POST['newpass'], $salt);
				$result = mysqli_query($db,"UPDATE users SET hash='$newhash', salt='$salt' WHERE username='".$_SESSION['username']."'");
				if (!$result) {   die('Invalid query: ' . mysqli_error($result));}
				header("Location: account.php?changed=1");
			} else
				header("Location: account.php?passerror=1");
		}
	}
?>
