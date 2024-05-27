<?php

require_once("includes/configuration.php");
require_once("includes/functions.inc.php");

// check if user is logged in and is of type admin
if (isset($_SESSION['email']) && $_SESSION['email'] != "" && strtolower($_SESSION['type']) == "admin") {
    $errors = []; // Initialize array to store errors
    $formErrors = []; // Store all the form errors
    $formSuccess = []; // Store all the form success

    $firstName = "";
    $lastName = "";
    $email = "";
    $password = "";
    $confirmPassword = "";

    if (isset($_POST) && !empty($_POST)) {
        // Retrieve form data
        $firstName = $_POST["firstName"];
        $lastName = $_POST["lastName"];
        $email = $_POST["email"];
        $password = $_POST["password"];
        $confirmPassword = $_POST["confirmPassword"];

        // Perform validation checks

        // Validate first name
        if (empty($firstName)) {
            $error_response = error_msg(101);
            if ($error_response['status']) {
                $errors[0] = $error_response['message'];
            } else {
                $formErrors[] = $error_response['message'];
            }
        }

        // Validate last name
        if (empty($lastName)) {
            $error_response = error_msg(201);
            if ($error_response['status']) {
                $errors[1] = $error_response['message'];
            } else {
                $formErrors[] = $error_response['message'];
            }
        }

        // Validate email
        if (empty($email)) {
            $error_response = error_msg(301);
            if ($error_response['status']) {
                $errors[2] = $error_response['message'];
            } else {
                $formErrors[] = $error_response['message'];
            }
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error_response = error_msg(302);
            if ($error_response['status']) {
                $errors[2] = $error_response['message'];
            } else {
                $formErrors[] = $error_response['message'];
            }
        } else { // check for unique email
            $response = checkUniqueEmail($email);
            if (isset($response['db-error'])) { // check if db error occured while finding unique email
                $formErrors[] = $response['db-error'];
            } else if ($response == false) { // check if it is not a unique email
                $error_response = error_msg(303);
                if ($error_response['status']) {
                    $errors[2] = $error_response['message'];
                } else {
                    $formErrors[] = $error_response['message'];
                }
            }
        }

        // Validate password
        if (empty($password)) {
            $error_response = error_msg(401);
            if ($error_response['status']) {
                $errors[3] = $error_response['message'];
            } else {
                $formErrors[] = $error_response['message'];
            }
        } elseif (strlen($password) < 6) {
            $error_response = error_msg(402);
            if ($error_response['status']) {
                $errors[3] = $error_response['message'];
            } else {
                $formErrors[] = $error_response['message'];
            }
        }

        // Validate confirm password
        if (empty($confirmPassword)) {
            $error_response = error_msg(403);
            if ($error_response['status']) {
                $errors[4] = $error_response['message'];
            } else {
                $formErrors[] = $error_response['message'];
            }
        } elseif ($password !== $confirmPassword) {
            $error_response = error_msg(404);
            if ($error_response['status']) {
                $errors[4] = $error_response['message'];
            } else {
                $formErrors[] = $error_response['message'];
            }
        }

        // If there are no errors, process the form
        if (empty($errors) && empty($formErrors)) {
            // prevent SQL Injection attacks
            $firstName = mysqli_real_escape_string($db, $firstName);
            $lastName = mysqli_real_escape_string($db, $lastName);
            $email = mysqli_real_escape_string($db, $email);
            $hashedPassword = md5($password); // hash password before storing in DB.
            $hashedPassword = mysqli_real_escape_string($db, $hashedPassword);

            // create reset token to be used to reset password
            $token = bin2hex(random_bytes(16));
            $resetTokenHash = hash("sha256", $token); // to store in db

            // generate expiry of this token to store them in db
            $resetTokenExpiry = date("Y-m-d H:i:s", time() + 60 * 30); // add 30 min to current time; 

            $resetTokenHash = mysqli_real_escape_string($db, $resetTokenHash);
            $resetTokenExpiry = mysqli_real_escape_string($db, $resetTokenExpiry);

            if (!empty($token) && !empty($resetTokenHash) && !empty($resetTokenExpiry)) {
                /*RAW QUERY
                    INSERT INTO `user_details`(
                        `first_name`,
                        `last_name`,
                        `email`,
                        `password`,
                        `type`,
                        `status`,
                        `reset_token_hash`,
                        `reset_token_expiry`,
                        `date_added`
                    )
                    VALUES(
                        'Saurabh',
                        'Singh',
                        'saurabh.singh@velsof.com',
                        '1b3231655cebb7a1f783eddf27d254ca',
                        'Admin',
                        'Rejected',
                        CURRENT_TIMESTAMP())
                 */
                $signup_user_query = "INSERT INTO `user_details`(
                        `first_name`,
                        `last_name`,
                        `email`,
                        `password`,
                        `type`,
                        `status`,
                        `reset_token_hash`,
                        `reset_token_expiry`,
                        `date_added`
                    )
                    VALUES(
                        '$firstName',
                        '$lastName',
                        '$email',
                        '$hashedPassword',
                        'Admin',
                        'Rejected',
                        '$resetTokenHash',
                        '$resetTokenExpiry',
                        CURRENT_TIMESTAMP())";

                try {
                    $signup_user_result = mysqli_query($db, $signup_user_query);
                    if ($signup_user_result === FALSE) {
                        throw new Exception("MySQL error: " . mysqli_error($db));
                    } else {
                        // Admin created 
                        $formSuccess[] = "Admin Account Created.";

                        // send mail with reset password link
                        $emailTo = $email;
                        $emailFrom = 'saurabh.singh@velsof.com';
                        $emailSubject = 'Welcome to The RFP System';
                        $emailBody = <<<END
    
                        Hi <strong>$firstName</strong>,<br>
                        <br>
                        Greetings!!<br>
                        <br>
                        We would like to onboard you onto our RFP(Request For Proposal) system as an admin.<br>
                        <br>
                        Click <a href="$GlobalApplication/signup-reset-password.php?token=$token">here</a> to reset your password to log in to our portal.<br>
                        <br>
                        Thanks
                        <br>
                        Velocity RFP System<br>
                        <br>
                        END;

                        $response_mail = sendMail($emailTo, $emailFrom, $emailSubject, $emailBody);

                        if ($response_mail['send-status']) { // email sent
                            $formSuccess[] = "Email sent to $email to reset the password.";
                        } else { // error while sending email
                            $formErrors[] = $response_mail['mail-error'];
                        }
                    }
                } catch (Exception $e) {
                    $formErrors[] = $e->getMessage();
                }
            } else {
                $formErrors[] = "Error occured while genereating reset token.";
            }
        }
    }

    include("register-admin.view.php");
} else { // if user is not of type admin then redirect them to login page
	header("Location: index.php"); // redirect to login
}
