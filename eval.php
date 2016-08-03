<?php

	require_once('functions.php');
	include('dbinfo.php');
	connectdb();
	$query = "SELECT * FROM prefs";
        $result = mysqli_query($db,$query);
	echo mysqli_errno($db);
        echo '<br />';
        echo mysqli_error($db);
        $accept = mysqli_fetch_array($result);
        $query = "SELECT status FROM users WHERE username='".$_SESSION['username']."'";
        $result = mysqli_query($db,$query);
	echo mysqli_errno($db);
        echo '<br />';
        echo mysqli_error($db);
        $status = mysqli_fetch_array($result);
	if (!preg_match("/^[^\\/?* :;{}\\\\]+\\.[^\\/?*: ;{}\\\\]{1,4}$/", $_POST['filename']))
		header("Location: solve.php?ferror=1&id=".$_POST['id']); // invalid filename
        // check if the user is banned or allowed to submit and SQL Injection checks
        else if($accept['accept'] == 1 and $status['status'] == 1 and is_numeric($_POST['id'])) {
        	$soln = mysqli_real_escape_string($_POST['soln']);
        	$filename = mysqli_real_escape_string($_POST['filename']);
        	$lang = mysqli_real_escape_string($_POST['lang']);
        	//check if entries are empty
        	if(trim($soln) == "" or trim($filename) == "" or trim($lang) == "")
        		header("Location: solve.php?derror=1&id=".$_POST['id']);
        	else {
			if($_POST['ctype']=='new')
				// add to database if it is a new submission
				$query = "INSERT INTO `solve` ( `problem_id` , `username`, `soln`, `filename`, `lang`) VALUES ('".$_POST['id']."', '".$_SESSION['username']."', '".$soln."', '".$filename."', '".$lang."')";
			else {
				// update database if it is a re-submission
				$tmp = "SELECT attempts FROM solve WHERE (problem_id='".$_POST['id']."' AND username='".$_SESSION['username']."')";
				$result = mysqli_query($db,$tmp);
				echo mysqli_errno($db);
        			echo '<br />';
	        		echo mysqli_error($db);
				$fields = mysqli_fetch_array($result);
				$query = "UPDATE solve SET lang='".$lang."', attempts='".($fields['attempts']+1)."', soln='".$soln."', filename='".$filename."' WHERE (username='".$_SESSION['username']."' AND problem_id='".$_POST['id']."')";
			}
			mysqli_query($db,$query);
			echo mysqli_errno($db);
        		echo '<br />';
        		echo mysqli_error($db);
			// connect to the java compiler server to compile the file and fetch the results
			$socket = fsockopen($compilerhost, $compilerport);
			if(!$socket) {
				echo "ERROR: ".$errorno.": ".$errorstr;
			}
			if($socket) {
				fwrite($socket, $_POST['filename']."\n");
				$query = "SELECT time, input, output FROM problems WHERE sl='".$_POST['id']."'";
				$result = mysqli_query($db,$query);
				echo mysqli_errno($db);
        			echo '<br />';
        			echo mysqli_error($db);
				$fields = mysqli_fetch_array($result);
				fwrite($socket, $fields['time']."\n");
				$soln = str_replace("\n", '$_n_$', treat($_POST['soln']));
				fwrite($socket, $soln."\n");
				$input = str_replace("\n", '$_n_$', treat($fields['input']));
				fwrite($socket, $input."\n");
				fwrite($socket, $lang."\n");
				$status = fgets($socket);
				$contents = "";
				while(!feof($socket))
					$contents = $contents.fgets($socket);
				if($status == 0) {
					// oops! compile error
					$query = "UPDATE solve SET status=1 WHERE (username='".$_SESSION['username']."' AND problem_id='".$_POST['id']."')";
					mysqli_query($db,$query);
					echo mysqli_errno($db);
        				echo '<br />';
        				echo mysqli_error($db);
					$_SESSION['cerror'] = trim($contents);
					header("Location: solve.php?cerror=1&id=".$_POST['id']);
				} else if($status == 1) {
					if(trim($contents) == trim(treat($fields['output']))) {
						// Congratulations! problem solved
						$query = "UPDATE solve SET status=2 WHERE (username='".$_SESSION['username']."' AND problem_id='".$_POST['id']."')";
						mysqli_query($db,$query);
						echo mysqli_errno($db);
        					echo '<br />';
        					echo mysqli_error($db);
						header("Location: index.php?success=1");
					} else {
						// wrong output bro
						$query = "UPDATE solve SET status=1 WHERE (username='".$_SESSION['username']."' AND problem_id='".$_POST['id']."')";
						mysqli_query($db,$query);
						echo mysqli_errno($db);
        					echo '<br />';
        					echo mysqli_error($db);
						header("Location: solve.php?oerror=1&id=".$_POST['id']);
					}
				} else if($status == 2) {
					$query = "UPDATE solve SET status=1 WHERE (username='".$_SESSION['username']."' AND problem_id='".$_POST['id']."')";
					mysqli_query($db,$query);
					echo mysqli_errno($db);
        				echo '<br />';
        				echo mysqli_error($db);
					header("Location: solve.php?terror=1&id=".$_POST['id']);
				}
			} else
				header("Location: solve.php?serror=1&id=".$_POST['id']); // compiler server not running
		}
	}
?>
