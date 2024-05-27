<?php

require_once('includes/configuration.php');
require_once('includes/functions.inc.php');

// check if user is already logged in
if (isset($_SESSION['email']) && $_SESSION['email'] != "") {
	session_destroy(); // log out them
}

if (!empty($_GET['token'])) { // check if token is present
	$token = $_GET['token']; // extract token
	$token_hash = hash("sha256", $token); // convert to hash

	if (!empty($token_hash)) { // check if token hash is generated or not
		$token_hash = mysqli_real_escape_string($db, $token_hash);
		// query to match token_hash and check expiry of the token
		/*RAW QUERY 
			SELECT
				user_id,
				reset_token_expiry
			FROM
				user_details
			WHERE
				reset_token_hash = '15fd5ce9256dc8bdbe3f57b96040f38e';
		 */
		$fetch_token = "SELECT user_id, reset_token_expiry FROM user_details WHERE reset_token_hash = '$token_hash'";

		try {
			$fetch_token_result = mysqli_query($db, $fetch_token);
			if ($fetch_token_result === FALSE) {
				throw new Exception("MySQL error: ", mysqli_error($db));
			} else {
				if (mysqli_num_rows($fetch_token_result) > 0) { // check if token is found
					$resetUser = mysqli_fetch_array($fetch_token_result); // will be used to update token_hash and expiry
					if (strtotime($resetUser['reset_token_expiry']) <= time()) { // check if the token is expired or not
						die("Token has expired.");
					} else {
						// continue to reset password
					}
				} else {
					// no record found associated with the token
					die("token not found.");
				}
			}
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}
} else {
	die("token not specified.");
}


$errors = array();  //Array defines to hold error messages

$password = "";
$confirmPassword = "";

if (isset($_POST["resetPasswordSubmit"])) {

	$password = $_POST['password'];
	$confirmPassword = $_POST['confirmPassword'];

	// server side validation
	if (empty($password)) { // check for empty password field
		$error_response = error_msg(401);
		if ($error_response['status']) {
			$errors[0] = $error_response['message'];
		} else {
			$formErrors[] = $error_response['message'];
		}
	} elseif (strlen($password) < 6) {
        $error_response = error_msg(402);
        if ($error_response['status']) {
            $errors[0] = $error_response['message'];
        } else {
            $formErrors[] = $error_response['message'];
        }
    }

	if (empty($confirmPassword)) { // check for empty confirm password field
		$error_response = error_msg(403);
		if ($error_response['status']) {
			$errors[1] = $error_response['message'];
		} else {
			$formErrors[] = $error_response['message'];
		}
	} elseif ($password !== $confirmPassword) {
        $error_response = error_msg(404);
        if ($error_response['status']) {
            $errors[1] = $error_response['message'];
        } else {
            $formErrors[] = $error_response['message'];
        }
    }

	// db validation
	if (count($errors) == 0) { // no error occured form validated successfully.
		$hashedPassword = md5($password);
		$hashedPassword = mysqli_real_escape_string($db, $hashedPassword);

		if (isset($resetUser) && !empty($resetUser['user_id'])) { // user id should be retrieved after token is validated successfully
			/*RAW QUERY
				UPDATE
					user_details
				SET
					password = '1b3231655cebb7a1f783eddf27d254ca',
					reset_token_hash = NULL,
					reset_token_expiry = NULL
				WHERE
					user_id = 1;
			*/
			$reset_password_query = "UPDATE user_details SET `password` = '$hashedPassword', reset_token_hash = NULL, reset_token_expiry = NULL WHERE user_id = " . (int)$resetUser['user_id'];

			try {
				$reset_password_result = mysqli_query($db, $reset_password_query);
				if ($reset_password_result === FALSE) {
					throw new Exception("MySQL error: ", mysqli_error($db));
				} else {
					// password reset successful
					$formSuccess[] = 'Password reset successful. You can now login with new password.';
					$_SESSION["reset-password-success"] = "Password reset successful. You can now login with new password."; 
					header("Location: index.php");
				}
			} catch (Exception $e) {
				$formErrors[] = $e->getMessage();
			}
		} else {
			die("Failed to retreive User id.");
		}
	}
}

include('reset-password.view.php');
