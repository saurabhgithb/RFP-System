<?php
require_once('includes/configuration.php');
require_once('includes/functions.inc.php');

$errors = []; // Initialize array to store errors
$formErrors = []; // Store all the form errors
$formSuccess = []; // Store all the form success

if (!isset($_SESSION['email']) || strtolower($_SESSION['type']) != "admin") { // check if user is not logged in
    header("Location: index.php"); // redirect to login page and the login will redirect the user to vendor dashboard.
    exit;
}

if (!empty($_GET)) {
    if (!empty($_GET["user_id"])) {
        if (!empty($_GET['action']) && strtolower($_GET["action"]) == "approve") {
            /*RAW QUERY
                UPDATE
                    `user_details`
                SET
                    `status` = 'Approved'
                WHERE
                    `user_details`.`user_id` = 1;
            */
            $change_vendor_status_query = "UPDATE
                                                `user_details`
                                            SET
                                                `status` = 'Approved'
                                            WHERE
                                                `user_details`.`user_id` = " . $_GET["user_id"];
        } else {
            $formErrors[] = "Invalid Action.";
        }

        if (isset($change_vendor_status_query)) {
            try {
                $change_vendor_status_result = mysqli_query($db, $change_vendor_status_query);
                if ($change_vendor_status_result === FALSE) {
                    throw new Exception("MySQL Error: " . mysqli_error($db));
                } else {
                    // status changed successfully
                    $formSuccess[] = "Account Approved";
                    if (mysqli_affected_rows($db) > 0) { // send mail if actually something changes in db.
                        /*RAW QUERY
                            SELECT
                                user_id,
                                first_name,
                                last_name,
                                email
                            FROM
                                user_details
                            WHERE
                                user_id = 1
                         */
                        $fetch_updated_user_query = "SELECT user_id, first_name, last_name, email FROM user_details WHERE user_id = " . (int)$_GET['user_id'];

                        try {
                            $fetch_updated_user_result = mysqli_query($db, $fetch_updated_user_query);
                            if ($fetch_updated_user_result === FALSE) {
                                throw new Exception("MySQL Error: " . mysqli_error($db));
                            } else {
                                if (mysqli_num_rows($fetch_updated_user_result) > 0) {
                                    $fetchedUserRow = mysqli_fetch_array($fetch_updated_user_result);
                                    $fetchedUserEmail = $fetchedUserRow["email"];
                                    $fetchedUserFirstName = $fetchedUserRow["first_name"];
                                    $fetchedUserLastName = $fetchedUserRow["last_name"];

                                    // send account approval confirmation mail
                                    $emailTo = $fetchedUserEmail;
                                    $emailFrom = 'saurabh.singh@velsof.com';
                                    $emailSubject = 'Account Approved for Velocity RFP System';
                                    $emailBody = <<<END

                                        Hi <strong>$fetchedUserFirstName $fetchedUserLastName</strong>,<br>
                                        <br>
                                        Greetings!!<br>
                                        <br>
                                        Congratulations, your account have been approved. You can now login to our portal.<br>
                                        <br>
                                        Thanks<br>
                                        <br>
                                        Velocity RFP System<br>
                                        <br>
                                        END;

                                    $response_mail = sendMail($emailTo, $emailFrom, $emailSubject, $emailBody);

                                    if ($response_mail['send-status']) { // email sent
                                        $formSuccess[] = "Account Approval Mail Sent.";
                                    } else { // error while sending email
                                        $formErrors[] = $response_mail['mail-error'];
                                    }
                                } else {
                                    $formErrors[] = "Unable to fetch user.";
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
    } else {
        $formErrors[] = "User Id not found.";
    }
}

// logic to count all vendors begin here
$count_vendors_query = "SELECT count(*) as total_vendors FROM `user_details` WHERE `type` = 'Vendor'";

try {
    $count_vendors_result = mysqli_query($db, $count_vendors_query);
    if ($count_vendors_result === FALSE) {
        throw new Exception("MySQL Error:" . mysqli_error($db));
    } else {
        if (mysqli_num_rows($count_vendors_result) > 0) {
            $row = mysqli_fetch_array($count_vendors_result);
            $total_vendors = $row['total_vendors'];
        }
    }
} catch (Exception $e) {
    $formErrors[] = $e->getMessage();
}

if (!isset($_GET['page'])) { // check if no page number is set
    $current_page = 1; // set current page to 1 as default
} else {
    $current_page = $_GET['page']; // set current page to page number requested
}

$no_of_vendors = 0;
$results_per_page = 5;

if (!empty($total_vendors)) {
    $no_of_vendors = $total_vendors;
}
$no_of_pages = ceil($no_of_vendors / $results_per_page);
$starting_row_number = ($current_page - 1) * $results_per_page;

/*RAW QUERY
    SELECT
        ud.user_id,
        ud.first_name,
        ud.last_name,
        ud.email,
        ud.type,
        ud.status,
        vd.phone_number
    FROM
        `user_details` ud
    LEFT JOIN vendor_details vd ON
        ud.user_id = vd.user_id
    WHERE
        ud.type = 'Vendor'
    ORDER BY
        `user_id`
    DESC
    LIMIT 0, 5
 */
$vendors_list_query = "SELECT ud.user_id, ud.first_name, ud.last_name, ud.email, ud.type, ud.status, vd.phone_number FROM `user_details` ud LEFT JOIN vendor_details vd ON ud.user_id = vd.user_id WHERE ud.type = 'Vendor' ORDER BY `user_id` DESC LIMIT " . $starting_row_number . ',' . $results_per_page;

// results to be shown on page.
try {
    $vendors_list_result = mysqli_query($db, $vendors_list_query);
    if ($vendors_list_result === FALSE) {
        throw new Exception("MySQL Error: " . mysqli_error($db));
    } else {
        // display list in view page
    }
} catch (Exception $e) {
    $formErrors[] = $e->getMessage();
}

include("vendors.view.php");
