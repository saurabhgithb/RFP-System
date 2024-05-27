<?php

require_once('includes/configuration.php');
require_once('includes/functions.inc.php');

if (!isset($_SESSION['email']) || !isset($_SESSION["type"])) { // check if user is not logged in
    header("Location: index.php"); // redirect to login page and the login will redirect the user to vendor dashboard.
    exit;
} else if (isset($_SESSION["type"]) && strtolower($_SESSION["type"]) == "admin") { // check if user is logged in as admin
    $_SESSION["edit-vendor-error"] = "Please login as vendor to access edit vendor page.";
    header("Location: dashboard-admin.php"); // redirect to login page and the login will redirect the user to vendor dashboard.
    exit;
}

$errors = []; // Initialize array to store errors
$formErrors = []; // Store all the form errors
$formSuccess = []; // Store all the form success

// Initialize form data
$firstName = "";
$lastName = "";
$email = "";
$revenue = "";
$noOfEmployees = "";
$gstNo = "";
$panNo = "";
$phoneNo = "";
$categories = [];

// check if vendor details exist using user_id and extract vendor_details and fill the details in the form

/*RAW QUERY
    SELECT
        ud.user_id,
        ud.first_name,
        ud.last_name,
        ud.email,
        vd.revenue,
        vd.no_of_employees,
        vd.gst_number,
        vd.pan_number,
        vd.phone_number
    FROM
        `user_details` ud
    LEFT JOIN `vendor_details` vd ON
        ud.user_id = vd.user_id
    WHERE
        ud.user_id = 1
*/
$get_vendor_details_query = "SELECT
                                ud.user_id,
                                ud.first_name,
                                ud.last_name,
                                ud.email,
                                vd.vendor_id,
                                vd.revenue,
                                vd.no_of_employees,
                                vd.gst_number,
                                vd.pan_number,
                                vd.phone_number
                            FROM
                                `user_details` ud
                            LEFT JOIN `vendor_details` vd ON
                                ud.user_id = vd.user_id
                            WHERE
                                ud.user_id = " . (int)$_SESSION["user_id"];

try {
    $get_vendor_details_result = mysqli_query($db, $get_vendor_details_query);
    if ($get_vendor_details_result === FALSE) {
        throw new Exception("MySQL Error: " . mysqli_error($db));
    } else {
        if (mysqli_num_rows($get_vendor_details_result) > 0) { // check if vendor_details found.
            $vendor_row = mysqli_fetch_array($get_vendor_details_result);
            // fetch vendor details and fill in the form.
            $firstName = $vendor_row["first_name"];
            $lastName = $vendor_row["last_name"];
            $email = $vendor_row["email"];
            $revenue = $vendor_row["revenue"];
            $noOfEmployees = $vendor_row["no_of_employees"];
            $gstNo = $vendor_row["gst_number"];
            $panNo = $vendor_row["pan_number"];
            $phoneNo = $vendor_row["phone_number"];

            $vendor_id = $vendor_row["vendor_id"];

            if ($vendor_id) { // check if vendor id found
                // get vendor's categories
                $fetchVendorCategoriesResponse = fetchVendorsCategory($vendor_id);
                if ($fetchVendorCategoriesResponse["status"]) { // check if vendor categories found.
                    $categories = $fetchVendorCategoriesResponse["categories-data"];
                } else {
                    $formErrors[] = $fetchVendorCategoriesResponse["error-msg"];
                }
            }
        } else { // vendor_details not found.
            $formErrors[] = "User not found. Please try again later.";
        }
    }
} catch (Exception $e) {
    $formErrors[] = $e->getMessage();
}

// fetch countries before loading the form
$allCategoriesResponse = fetchAllCategories();
if (isset($allCategoriesResponse['db-error'])) {
    $formErrors[] = $allCategoriesResponse['db-error'];
}

// submit edited details
if (isset($_POST) && !empty($_POST)) {
    // print "<pre>";
    // print_r($_POST);
    // print "</pre>";
    // Retrieve form data
    $firstName = $_POST["firstName"];
    $lastName = $_POST["lastName"];
    $email = $_POST["email"];
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
        $response = checkUniqueEmail($email, $_SESSION["user_id"]);
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
        $revenue = mysqli_real_escape_string($db, $revenue);
        $noOfEmployees = mysqli_real_escape_string($db, $noOfEmployees);
        $gstNo = mysqli_real_escape_string($db, $gstNo);
        $panNo = mysqli_real_escape_string($db, $panNo);
        $phoneNo = mysqli_real_escape_string($db, $phoneNo);

        /*RAW QUERY
                UPDATE
                    `user_details`
                SET
                    `first_name` = 'Saurabh',
                    `last_name` = 'Singh',
                    `email` = 'saurav999bsr@gmail.com'
                WHERE
                    `user_details`.`user_id` = 1
        */
        $update_vendor_query = "UPDATE
                                `user_details`
                            SET
                                `first_name` = '$firstName',
                                `last_name` = '$lastName',
                                `email` = '$email'
                            WHERE
                                `user_details`.`user_id` = " . (int)$_SESSION["user_id"];

        try {
            $update_vendor_result = mysqli_query($db, $update_vendor_query);
            if ($update_vendor_result === FALSE) {
                throw new Exception("MySQL error: " . mysqli_error($db));
            } else {
                // Vendor updated, now update vendor details if exist otherwise create vendor details
                if ($vendor_id) { // check if vendor_id was found
                    /*RAW QUERY
                        UPDATE
                            `vendor_details`
                        SET
                            `revenue` = '1.00',
                            `no_of_employees` = '55',
                            `gst_number` = '06BZAHM6385P6Z2',
                            `pan_number` = 'BNZAA2318J',
                            `phone_number` = '8382743927'
                        WHERE
                            `vendor_details`.`vendor_id` = 1
                     */
                    $update_vendor_details_query = "UPDATE
                                                        `vendor_details`
                                                    SET
                                                        `revenue` = '$revenue',
                                                        `no_of_employees` = '$noOfEmployees',
                                                        `gst_number` = '$gstNo',
                                                        `pan_number` = '$panNo',
                                                        `phone_number` = '$phoneNo'
                                                    WHERE
                                                        `vendor_details`.`vendor_id` = " . (int)$vendor_id;
                    try {
                        $update_vendor_details_result = mysqli_query($db, $update_vendor_details_query);
                        if ($update_vendor_details_result === FALSE) {
                            throw new Exception("My SQL Error: " . mysqli_error($db));
                        } else {
                            // vendor details updated
                            $formSuccess[] = "Updated Vendor Details Successfully.";

                            // Handle Category Update
                            // Remove existing categories for the vendor
                            $delete_vendor_categories_query = "DELETE FROM `vendor_category` WHERE `vendor_id` = '$vendor_id'";
                            try {
                                $delete_vendor_categories_result = mysqli_query($db, $delete_vendor_categories_query);
                                if ($delete_vendor_categories_result === FALSE) {
                                    throw new Exception("MySQL error: " . mysqli_error($db));
                                } else {
                                    // Insert selected categories for the vendor
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
                                            }
                                        } catch (Exception $e) {
                                            $formErrors[] = $e->getMessage();
                                        }
                                    }
                                }
                            } catch (Exception $e) {
                                $formErrors[] = $e->getMessage();
                            }
                        }
                    } catch (Exception $e) {
                        $formErrors[] = $e->getMessage();
                    }
                } else { // if vendor_id not found, create vendor details for the user.
                    // Add vendor details to vendor_details table.
                    $user_id = $_SESSION["user_id"];
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
                            $formSuccess[] = "Updated vendor details successfully";

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
                }
            }
        } catch (Exception $e) {
            $formErrors[] = $e->getMessage();
        }
    }
}

include("edit-vendor.view.php");
