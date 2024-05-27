<?php

require_once('includes/configuration.php');
require_once('includes/functions.inc.php');

$errors = []; // Initialize array to store errors
$formErrors = []; // Store all the form errors
$formSuccess = []; // Store all the form success

// check if user is already logged in logout the existing user
if (isset($_SESSION['email']) && $_SESSION['email'] != "") {
    session_destroy(); // logout user
}

$email = "";

if (isset($_POST) && !empty($_POST)) {
    
    $email = $_POST['email'];
    $email = trim($email); // remove whitespaces

    // server side validation
    // Validate email
    if (empty($email)) {
        $error_response = error_msg(301);
        if ($error_response['status']) {
            $errors[0] = $error_response['message'];
        } else {
            $formErrors[] = $error_response['message'];
        }
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_response = error_msg(302);
        if ($error_response['status']) {
            $errors[0] = $error_response['message'];
        } else {
            $formErrors[] = $error_response['message'];
        }
    } else {
        // validate if the email ID exists in system
        $email = mysqli_real_escape_string($db, $email);
        $emailExistResponse = emailExist($email);

        if ($emailExistResponse['email_found']) { // email found
            $user_data = $emailExistResponse['user_data'];
            $name = $user_data['first_name'] . " " . $user_data['last_name']; // will be used while sending email
        } else {
            if (isset($emailExistResponse['db-error'])) { // db error occured while checking email
                $formErrors[] = $emailExistResponse['db-error'];
            } else { // email not found in db
                $formErrors[] = "Email is not registered in our system.";
            }
        }
    }

    // validation successful
    if (count($errors) == 0) {
        // generate token
        $token = bin2hex(random_bytes(16));
        $token_hash = hash("sha256", $token); // to store in db

        // generate expiry of this token to store them in db
        $expiry = date("Y-m-d H:i:s", time() + 60 * 30); // add 30 min to current time; 

        // store token hash in db.
        $token_hash = mysqli_real_escape_string($db, $token_hash);
        $expiry = mysqli_real_escape_string($db, $expiry);

        if (!empty($token) && !empty($token_hash) && !empty($expiry) && !empty($name) && !empty($email)) {
            // query to update reset token hash and expiry in db
            /*RAW QUERY
                UPDATE
                    `user_details`
                SET
                    reset_token_hash = '15fd5ce9256dc8bdbe3f57b96040f38e',
                    reset_token_expiry = '2024-05-03 11:34:33'
                WHERE
                    email = 'saurav999bsr@gmail.com'
            */
            $store_token_query = "UPDATE `user_details` SET reset_token_hash = '$token_hash', reset_token_expiry = '$expiry' WHERE email = '$email'";

            try {
                $store_token_result = mysqli_query($db, $store_token_query);
                if ($store_token_result === FALSE) {
                    throw new Exception("MySQL error while updating reset token hash: " . $mysqli_error($db));
                } else {
                    if (mysqli_affected_rows($db) > 0) { // token stored successfully
                        // send mail with token in link

                        $emailTo = $email;
                        $emailFrom = 'saurabh.singh@velsof.com';
                        $emailSubject = 'Password Reset for RFP System';
                        $emailBody = <<<END
    
                        Hi $name,<br>
                        <br>
                        You recently requested to reset the password for your RFP System account. Click the link below to proceed.<br>
                        <br>
                        Click <a href="{$GlobalApplication}reset-password.php?token=$token">here</a> to reset your password.<br>
                        <br>
                        If you did not request a password reset, please ignore this email or reply to let us know. <strong>This password reset link is only valid for the next 30 minutes.</strong><br>
                        <br>
                        Thanks, the Student Management team<br>
                        <br>
                        END;

                        $response_mail = sendMail($emailTo, $emailFrom, $emailSubject, $emailBody);

                        if ($response_mail['send-status']) { // email sent
                            $formSuccess[] = "An email with password reset link has been sent to $email.";
                        } else { // error while sending email
                            $formErrors[] = $response_mail['mail-error'];
                        }
                    }
                }
            } catch (Exception $e) { // error while updating token hash in db
                $formErrors[] = $e->getMessage();
            }
        } else {
            // this error will occur if there is some error in generating token
            $formErrors[] = "Something went wrong.";
        }
    }
}

require('forgot-password.view.php');
