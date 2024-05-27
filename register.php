<?php

require_once("includes/configuration.php");
require_once("includes/functions.inc.php");

// check if user is already logged in.
if (!empty($_SESSION['email']) && !empty($_SESSION['type']) && strtolower($_SESSION['type']) == "admin") {
    header("Location: dashboard-admin.php");
} else if (!empty($_SESSION['email']) && !empty($_SESSION['type']) && strtolower($_SESSION['type']) == "vendor") {
    header("Location: dashboard-vendor.php");
}

$errors = []; // Initialize array to store errors
$formErrors = []; // Store all the form errors
$formSuccess = []; // Store all the form success

// Initialize form data
$firstName = "";
$lastName = "";
$email = "";
$password = "";
$confirmPassword = "";
$revenue = "";
$noOfEmployees = "";
$gstNo = "";
$panNo = "";
$phoneNo = "";
$categories = [];

// fetch countries before loading the form
$allCategoriesResponse = fetchAllCategories();
if (isset($allCategoriesResponse['db-error'])) {
    $formErrors[] = $allCategoriesResponse['db-error'];
}

if (isset($_POST) && !empty($_POST)) {
    // print "<pre>";
    // print_r($_POST);
    // print "</pre>";
    // Retrieve form data
    $firstName = $_POST["firstName"];
    $lastName = $_POST["lastName"];
    $email = $_POST["email"];
    $password = $_POST["password"];
    $confirmPassword = $_POST["confirmPassword"];
    $revenue = $_POST["revenue"];
    $noOfEmployees = $_POST["noOfEmployees"];
    $gstNo = $_POST["gstNo"];
    $panNo = $_POST["panNo"];
    $phoneNo = $_POST["phoneNo"];
    $categories = isset($_POST["categories"]) ? $_POST["categories"] : "";

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

    // Validate revenue
    if (empty($revenue)) {
        $error_response = error_msg(501);
        if ($error_response['status']) {
            $errors[5] = $error_response['message'];
        } else {
            $formErrors[] = $error_response['message'];
        }
    } else {
        if (!is_numeric($revenue)) {
            $error_response = error_msg(502);
            if ($error_response['status']) {
                $errors[5] = $error_response['message'];
            } else {
                $formErrors[] = $error_response['message'];
            }
        }
    }

    // Validate number of Employees
    if (empty($noOfEmployees)) {
        $error_response = error_msg(601);
        if ($error_response['status']) {
            $errors[6] = $error_response['message'];
        } else {
            $formErrors[] = $error_response['message'];
        }
    } else if (!is_numeric($noOfEmployees)) {
        $error_response = error_msg(602);
        if ($error_response['status']) {
            $errors[6] = $error_response['message'];
        } else {
            $formErrors[] = $error_response['message'];
        }
    }

    // Validate gst number
    if (empty($gstNo)) {
        $error_response = error_msg(701);
        if ($error_response['status']) {
            $errors[7] = $error_response['message'];
        } else {
            $formErrors[] = $error_response['message'];
        }
    } else if (!preg_match("/^[0-9]{2}[A-Z]{5}[0-9]{4}[A-Z]{1}[1-9A-Z]{1}Z[0-9A-Z]{1}$/", $gstNo)) {
        $error_response = error_msg(702);
        if ($error_response['status']) {
            $errors[7] = $error_response['message'];
        } else {
            $formErrors[] = $error_response['message'];
        }
    }

    // Validate pan number
    if (empty($panNo)) {
        $error_response = error_msg(801);
        if ($error_response['status']) {
            $errors[8] = $error_response['message'];
        } else {
            $formErrors[] = $error_response['message'];
        }
    } else if (!preg_match("/[A-Z]{5}[0-9]{4}[A-Z]{1}/", $panNo)) {
        $error_response = error_msg(802);
        if ($error_response['status']) {
            $errors[8] = $error_response['message'];
        } else {
            $formErrors[] = $error_response['message'];
        }
    }

    // Validate phone number
    if (empty($phoneNo)) {
        $error_response = error_msg(901);
        if ($error_response['status']) {
            $errors[9] = $error_response['message'];
        } else {
            $formErrors[] = $error_response['message'];
        }
    } else if (!preg_match("/^[\+]?[(]?[0-9]{3}[)]?[-\s\.]?[0-9]{3}[-\s\.]?[0-9]{4,6}$/", $phoneNo)) {
        $error_response = error_msg(902);
        if ($error_response['status']) {
            $errors[9] = $error_response['message'];
        } else {
            $formErrors[] = $error_response['message'];
        }
    }

    // Validate categories
    if (empty($categories)) {
        $error_response = error_msg(1001);
        if ($error_response['status']) {
            $errors[10] = $error_response['message'];
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
        $revenue = mysqli_real_escape_string($db, $revenue);
        $noOfEmployees = mysqli_real_escape_string($db, $noOfEmployees);
        $gstNo = mysqli_real_escape_string($db, $gstNo);
        $panNo = mysqli_real_escape_string($db, $panNo);
        $phoneNo = mysqli_real_escape_string($db, $phoneNo);

        /*RAW QUERY
                INSERT INTO `user_details`(
                    `first_name`,
                    `last_name`,
                    `email`,
                    `password`,
                    `type`,
                    `status`,
                    `date_added`
                )
                VALUES(
                    'Saurabh',
                    'Singh',
                    'saurabh.singh@velsof.com',
                    '1b3231655cebb7a1f783eddf27d254ca',
                    'Vendor',
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
                    `date_added`
                )
                VALUES(
                    '$firstName',
                    '$lastName',
                    '$email',
                    '$hashedPassword',
                    'Vendor',
                    'Rejected',
                    CURRENT_TIMESTAMP())";

        try {
            $signup_user_result = mysqli_query($db, $signup_user_query);
            if ($signup_user_result === FALSE) {
                throw new Exception("MySQL error: " . mysqli_error($db));
            } else {
                // Vendor created 
                $user_id = mysqli_insert_id($db);
                $formSuccess[] = "Vendor Account Created.";

                // Add vendor details to vendor_details table.
                /*RAW QUERY
                    INSERT INTO `vendor_details`(
                        `user_id`,
                        `revenue`,
                        `no_of_employees`,
                        `gst_number`,
                        `pan_number`,
                        `phone_number`
                    )
                    VALUES(
                        '1',
                        '1',
                        '55',
                        '06BZAHM6385P6Z2',
                        'BNZAA2318J',
                        '8382743927');
                */
                $vendor_detail_query = "INSERT INTO `vendor_details`(
                        `user_id`,
                        `revenue`,
                        `no_of_employees`,
                        `gst_number`,
                        `pan_number`,
                        `phone_number`
                    )
                    VALUES(
                        '$user_id',
                        '$revenue',
                        '$noOfEmployees',
                        '$gstNo',
                        '$panNo',
                        '$phoneNo')";

                try {
                    $vendor_detail_result = mysqli_query($db, $vendor_detail_query);
                    if ($vendor_detail_result === FALSE) {
                        throw new Exception("MySQL error: " . mysqli_error($db));
                    } else {
                        // Vendor details added
                        $vendor_id = mysqli_insert_id($db);
                        $formSuccess[] = "Added Vendor Details";

                        // Add selected categories of vendor into vendor_category table.
                        foreach ($categories as $category_id) {
                            $category_id = mysqli_real_escape_string($db, $category_id);
                            /*RAW QUERY
                                INSERT INTO `vendor_category`(`vendor_id`, `category_id`)
                                VALUES('1', '1');
                             */
                            $vendor_category_query = "INSERT INTO `vendor_category` (`vendor_id`, `category_id`) VALUES ('$vendor_id', '$category_id')";
                            try {
                                $vendor_category_result = mysqli_query($db, $vendor_category_query);
                                if ($vendor_category_result === FALSE) {
                                    throw new Exception("MySQL Error: " . mysqli_error($db));
                                } else {
                                    // insertion successful
                                }
                            } catch (Exception $e) {
                                $formErrors[] = $e->getMessage();
                            }
                        }
                    }
                } catch (Exception $e) {
                    $formErrors[] = $e->getMessage();
                }

                // send under review mail
                $emailTo = $email;
                $emailFrom = 'saurabh.singh@velsof.com';
                $emailSubject = 'Welcome to The RFP System';
                $emailBody = <<<END

                    Hi <strong>$firstName</strong>,<br>
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
                    $formSuccess[] = "Email sent to $email. Wait while Admin approves your account.";
                } else { // error while sending email
                    $formErrors[] = $response_mail['mail-error'];
                }
            }
        } catch (Exception $e) {
            $formErrors[] = $e->getMessage();
        }
    }
}

include("register.view.php");
