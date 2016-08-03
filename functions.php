      <?php

      session_start();

      // checks if any user is logged in
      function loggedin() {
        return isset($_SESSION['username']);
      }

      // connects to the database
      function connectdb() {
        include('dbinfo.php');
        $db = mysqli_connect($host, $user, $password, $database);
        if (mysqli_connect_errno()) {
            echo mysqli_error($db);
            exit;
        }
      
          mysqli_select_db($db,$database) or die('Error connecting to database.');
      }

      // generates a random alpha numeric sequence. Used to generate salt
      function randomAlphaNum($length){
        $rangeMin = pow(36, $length-1);
        $rangeMax = pow(36, $length)-1;
        $base10Rand = mt_rand($rangeMin, $rangeMax);
        $newRand = base_convert($base10Rand, 10, 36);
        return $newRand;
      }

      // gets the name of the event
      function getName(){
        connectdb();
        $query="SELECT name FROM prefs";
        $result = mysqli_query($db,$query);
        echo mysqli_errno($db);
        echo '<br />';
        echo mysqli_error($db);
        $row = mysql_fetch_array($result);
        return $row['name'];
      }

      // converts text to an uniform only '\n' newline break
      function treat($text) {
      	$s1 = str_replace("\n\r", "\n", $text);
      	return str_replace("\r", "", $s1);
      }
