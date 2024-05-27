<?php

require_once("includes/configuration.php");
require_once("includes/functions.inc.php");
require_once 'vendor/autoload.php';

// load env variables
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$errors = []; // Initialize array to store errors
$formErrors = []; // Store all the form errors
$formSuccess = []; // Store all the form success

$response = []; // Initialize response array 

if (isset($_POST) && !empty($_POST)) {
    // Retrieve form data
    $email = $_POST["email"];
    $password = $_POST["password"];

    // Perform validation checks

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
    }

    // Validate password
    if (empty($password)) {
        $error_response = error_msg(401);
        if ($error_response['status']) {
            $errors[1] = $error_response['message'];
        } else {
            $formErrors[] = $error_response['message'];
        }
    } elseif (strlen($password) < 6) {
        $error_response = error_msg(402);
        if ($error_response['status']) {
            $errors[1] = $error_response['message'];
        } else {
            $formErrors[] = $error_response['message'];
        }
    }

    // If there are no errors, process the form
    if (empty($errors) && empty($formErrors)) {
        // prevent SQL Injection attacks
        $email = mysqli_real_escape_string($db, $email);
        $hashedPassword = md5($password); // hash password before checking in DB.
        $hashedPassword = mysqli_real_escape_string($db, $hashedPassword);

        /*RAW QUERY
			SELECT
				`user_id`,
				`first_name`,
				`last_name`,
				`email`,
				`password`,
                `type`,
                `status`
			FROM
				`user_details`
			WHERE
				`email` = 'saurabh.singh@velsof.com' AND `password` = 'f3ed11bbdb94fd9ebdefbaf646ab94d3'
		 */
        $login_user_query = "SELECT `user_id`, `first_name`, `last_name`, `email`, `type`, `status` FROM `user_details` WHERE `email` = '$email' AND `password` = '$hashedPassword'";

        try {
            $login_user_result = mysqli_query($db, $login_user_query);
            if ($login_user_result === FALSE) {
                throw new Exception("MySQL error: " . mysqli_error($db));
            } else {
                // check if user account found
                if (mysqli_num_rows($login_user_result) > 0) {
                    $row = mysqli_fetch_array($login_user_result);

                    if (strtolower($row['status']) == "rejected") { // check if user status is rejected.
                        $formErrors[] = "Your account has not been approved yet. Please contact admin.";
                    } else if (strtolower($row['status']) == "approved") {
                        // save user details in session
                        $_SESSION['user_id'] = $row['user_id'];
                        $_SESSION['name'] = trim($row['first_name']) . " " . trim($row['last_name']);
                        $_SESSION['email'] = $row['email'];
                        $_SESSION['type'] = $row['type'];

                        // redirect based on user type
                        if (strtolower($row['type']) == 'admin') {
                            $response = ['type' => 'success', 'redirectUrl' => 'dashboard-admin.php'];
                        } else if (strtolower($row['type']) == 'vendor') {
                            $response = ['type' => 'success', "redirectUrl" => 'dashboard-vendor.php'];
                        }
                    }
                } else {
                    $formErrors[] = "Username/Password not found.";
                }
            }
        } catch (Exception $e) {
            $formErrors[] = $e->getMessage();
        }
    } else {
        // response if validation or any form error occurs
        if (isset($errors) && !empty($errors[0])) {
            $response['validationError']['emailError'] = $errors[0];
        }
        if (isset($errors) && !empty($errors[1])) {
            $response['validationError']['passwordError'] = $errors[1];
        }
        if (isset($formErrors) && !empty($formErrors)) {
            $response['formErrors'] = $formErrors;
        }
        $response['type'] = 'failed';
        echo json_encode($response);
        exit;
    }

    // response if any form error occurs after validation
    if (isset($formErrors) && !empty($formErrors)) {
        $response['formErrors'] = $formErrors;
        $response['type'] = 'failed';
        echo json_encode($response);
        exit;
    }

    // response if no error occurs and success response is set
    echo json_encode($response);
    exit;
} else {
    // create Client Request to access Google API
    $client = new Google_Client();
    $client->setClientId($_ENV["CLIENT_ID"]);
    $client->setClientSecret($_ENV["CLIENT_SECRET"]);
    $redirectURL = $_ENV["GLOBAL_APPLICATION"] . "login.php";
    $client->setRedirectUri($redirectURL);
    $client->addScope("email");
    $client->addScope("profile");

    // authenticate code from Google OAuth Flow
    if (isset($_GET['code'])) {
        $google_login_error = array();
        $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
        $client->setAccessToken($token['access_token']);

        // get profile info
        $google_oauth = new Google_Service_Oauth2($client);
        $google_account_info = $google_oauth->userinfo->get();
        $email =  $google_account_info->email;
        $name =  $google_account_info->name;

        if (!empty($email) && !empty($name)) {
            // check if the email received from google exist in student table or not
            $email_exist_response = emailExist($email);

            if ($email_exist_response['email_found']) {
                $user_data = $email_exist_response['user_data'];
                // check if user is approved or not.
                if (strtolower($user_data['status']) == 'rejected') {
                    $formErrors[] = "Your account has not been approved yet. Please contact admin.";
                } else if (strtolower($user_data['status']) == "approved") { // incase the account is approved redirect to index.php
                    // save user details in session
                    $_SESSION['user_id'] = $user_data['user_id'];
                    $_SESSION['name'] = trim($user_data['first_name']) . " " . trim($user_data['last_name']);
                    $_SESSION['email'] = $user_data['email'];
                    $_SESSION['type'] = $user_data['type'];

                    // redirect based on user type
                    if (strtolower($user_data['type']) == 'admin') {
                        $response = ['type' => 'success', 'redirectUrl' => 'dashboard-admin.php'];
                        header("Location: dashboard-admin.php");
                        exit;
                    } else if (strtolower($user_data['type']) == 'vendor') {
                        $response = ['type' => 'success', "redirectUrl" => 'dashboard-vendor.php'];
                        header("Location: dashboard-vendor.php");
                        exit;
                    }
                }
            } else { //email not registered
                // check if db-error occured while checking the email
                if (isset($email_exist_response['db-error']) && $email_exist_response['db-error']) {
                    $formErrors[] = $email_exist_response['db-error'];
                } else { // only vendor can register via google
                    // query to register the vendor via google email
                    /*RAW QUERY
                        INSERT INTO `user_details`(
                            `first_name`,
                            `email`,
                            `type`,
                            `status`,
                            `date_added`
                        )
                        VALUES(
                            'Saurabh',
                            'saurabh.singh@velsof.com',
                            '1b3231655cebb7a1f783eddf27d254ca',
                            'Vendor',
                            'Rejected',
                            CURRENT_TIMESTAMP())
                    */
                    $email = mysqli_real_escape_string($db, $email);
                    $name = mysqli_real_escape_string($db, $name);
                    $insert_google_email_query = "INSERT INTO `user_details`(
                        `first_name`,
                        `email`,
                        `type`,
                        `status`,
                        `date_added`
                    )
                    VALUES(
                        '$name',
                        '$email',
                        'Vendor',
                        'Rejected',
                        CURRENT_TIMESTAMP())";

                    try {
                        $insert_google_email_result = mysqli_query($db, $insert_google_email_query);
                        if ($insert_google_email_result === FALSE) {
                            throw new Exception('MySQL Error: ' . mysqli_error($db));
                        } else {
                            // user details inserted
                            $insert_id = mysqli_insert_id($db);
                            $formSuccess[] = "Vendor Account Created.";

                            // send under review mail
                            $emailTo = $email;
                            $emailFrom = 'saurabh.singh@velsof.com';
                            $emailSubject = 'Welcome to The RFP System';
                            $emailBody = <<<END
                                Hi <strong>$name</strong>,<br>
                                <br>
                                Greetings!!<br>
                                <br>
                                Thanks for showing registration on our RFP System. We will review the details & approve the account shortly.<br>
                                <br>
                                Thanks<br>
                                <br>
                                Velocity RFP System<br>
                                <br>
                                END;

                            $response_mail = sendMail($emailTo, $emailFrom, $emailSubject, $emailBody);

                            if ($response_mail['send-status']) { // email sent
                                $formSuccess[] = "Email sent to $email. Wait while admin approves your account.";
                                // store success info and redirect the user to login page
                                $_SESSION['google-login-success'] = $formSuccess;
                                header("Location: index.php");
                                exit;
                            } else { // error while sending email
                                $formErrors[] = $response_mail['mail-error'];
                            }

                        }
                    } catch (Exception $e) {
                        $formErrors[] = $e->getMessage();
                    }
                }
            }
        }

        // response if any form error occured
        if (isset($formErrors) && !empty($formErrors)) {
            $response['formErrors'] = $formErrors;
            $_SESSION['google-login-error'] = $formErrors;
            if(isset($formSuccess) && !empty($formSuccess)){ // this case will happen if any error occurs after a form success like vendor account created and error occured while sending mail
                $_SESSION['google-login-success'] = $formSuccess;
            }
            // $response['type'] = 'failed';
            echo json_encode($response);
            header("Location: index.php");
            exit;
        }

        

        // if niether form error occurs nor success happens control will reach here.
        echo json_encode($response);
        exit;
    }
}
