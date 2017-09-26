<?php session_start();?>

<!DOCTYPE html>
<html>
<head>
	<title>Simpletown Sign Up Page</title>
</head>
<style type="text/css">
	*{padding: 0px;margin: 0px}
	form{
		border: 1px solid black;
		padding: 10px;margin: 10px;
		float: left;
	}
</style>
<?php 
function signUserIn($user_id,$username,$userdate){
	$_SESSION['user_id'] = $user_id;
	$_SESSION['username'] = $username;
	$_SESSION['userdate'] = $userdate;
}

if(isset($_POST['signup']) || isset($_POST['register'])){
	require_once($_SERVER["DOCUMENT_ROOT"] . "/dbConnect.php");

	$user = $_POST['user'];
	$pass = $_POST['pass'];

	try{
	  $results = $db->prepare("SELECT * FROM users WHERE username = ?");
	  $results->bindParam(1,$user);
	  $results->execute();
	  $hold = $results->fetchAll(PDO::FETCH_ASSOC);
	} catch (Exception $e) {
	  echo "Data could not be retrieved from the database.";
	  exit();
	}

	if(isset($_POST['register'])){

		if($hold[0]['user_id'] != ""){
			echo 'username already exists';
		}else{
			$options = ['cost' => 11,];
		  $hashedpass = password_hash($pass, PASSWORD_BCRYPT, $options);
		  date_default_timezone_set('America/Chicago');
			$timenow = strtotime('now');

			try{
			  $results = $db->prepare("INSERT INTO users (username,userpass,userdate)VALUES(?,?,?)");
			  $results->bindParam(1,$user);
			  $results->bindParam(2,$hashedpass);
			  $results->bindParam(3,$timenow);
			  $results->execute();
			  $lastInsert_id = $db->lastInsertId();
			} catch (Exception $e) {
			  echo "Data could not be retrieved from the database.";
			  exit();
			}
			echo 'Registered, signing you in.';
			signUserIn($lastInsert_id,$user,$timenow);
		}

		

	}else{//signup

		
		if($hold[0]['user_id'] != ""){
			$pw_check = $hold[0]['userpass'];
	    if(!password_verify($pass, $pw_check)){ 
	      echo 'User/Password combo was incorrect.';
	    }else{
	    	signUserIn($hold[0]['user_id'],$hold[0]['username'],$hold[0]['userdate']);
	    }
		}else{
			echo 'User/Password combo was incorrect.';
		}
	}
}


if(isset($_SESSION['user_id'])){
	echo 'Currently signed in as '.$_SESSION['username'];
}

?>


<body>
	<h4>SimpleTown Form</h4>

	<form action="" method="POST">
		<input type="text" name="user" placeholder="username">
		<input type="text" name="pass" placeholder="password">
		<input type="submit" name="signup" value="Sign Up">
	</form>

	<form action="" method="POST">
		<input type="text" name="user" placeholder="username">
		<input type="text" name="pass" placeholder="password">
		<input type="submit" name="register" value="Register">
	</form>

	<a href="/users.php">Show All Users</a>


</body>
</html>