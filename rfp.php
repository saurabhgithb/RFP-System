<?php
require_once('includes/configuration.php');

$errors = []; // Initialize array to store errors
$formErrors = []; // Store all the form errors
$formSuccess = []; // Store all the form success

if (!isset($_SESSION['email']) || strtolower($_SESSION['type']) != "admin") { // check if user is not logged in
    header("Location: index.php"); // redirect to login page and the login will redirect the user to vendor dashboard.
    exit;
}

// display rfp status changed message if set
if (!empty($_SESSION['rfp-status-changed-msg'])) {
    $formSuccess[] = $_SESSION['rfp-status-changed-msg'];
    unset($_SESSION["rfp-status-changed-msg"]);
}

// display rfp status changed error if set
if (!empty($_SESSION['rfp-status-changed-err'])) {
    $formErrors[] = $_SESSION['rfp-status-changed-err'];
    unset($_SESSION["rfp-status-changed-err"]);
}

// check if rfp-status-success is set
if (!empty($_SESSION['rfp-status-success'])) {
    $formSuccess[] = $_SESSION['rfp-status-success'];
    unset($_SESSION["rfp-status-success"]);
}

if (!empty($_GET)) {
    if (!empty($_GET["rfp_id"])) {
        $rfpId = $_GET["rfp_id"];
        $rfpId = mysqli_real_escape_string($db, $rfpId);

        if (!empty($_GET['action']) && strtolower($_GET["action"]) == "open") {
            // check last date of the rfp before opening the rfp
            /*RAW QUERY
            SELECT
                last_date
            FROM
                rfp
            WHERE
                rfp_id = 1
         */
            $check_last_date_of_rfp_query = "SELECT last_date FROM rfp WHERE rfp_id = " . (int)$rfpId;
            try {
                $check_last_date_of_rfp_result = mysqli_query($db, $check_last_date_of_rfp_query);
                if ($check_last_date_of_rfp_result === FALSE) {
                    throw new Exception("My SQL Error: " . mysqli_error($db));
                } else {
                    if (mysqli_num_rows($check_last_date_of_rfp_result) > 0) {
                        $check_last_date_of_rfp_row = mysqli_fetch_array($check_last_date_of_rfp_result);
                        $currentTimestamp = time();
                        if (isset($check_last_date_of_rfp_row['last_date']) && strtotime($check_last_date_of_rfp_row['last_date']) < $currentTimestamp) {
                            $_SESSION["rfp-status-changed-err"] = "Unable to open RFP as RFP's last date have passed.";
                            header("Location: rfp.php");
                            exit;
                        } else {
                            // change status to open query
                            /*RAW QUERY
                                UPDATE
                                    `rfp`
                                SET
                                    `status` = 'Open'
                                WHERE
                                    `rfp`.`rfp_id` = 1;
                            */
                            $change_rfp_status_query = "UPDATE
                                                            `rfp`
                                                        SET
                                                            `status` = 'Open'
                                                        WHERE
                                                            `rfp`.`rfp_id` = " . (int)$rfpId;
                        }
                    } else { // last date of the rfp was not found.
                        $_SESSION["rfp-status-changed-err"] = "Unable to update status as last date not found for given RFP.";
                        header("Location: rfp.php");
                        exit;
                    }
                }
            } catch (Exception $e) {
                $_SESSION["rfp-status-changed-err"] = $e->getMessage();
                header("Location: rfp.php");
                exit;
            }
        } else if (!empty($_GET['action']) && strtolower($_GET["action"]) == "close") {
            /*RAW QUERY
                UPDATE
                    `rfp`
                SET
                    `status` = 'Closed'
                WHERE
                    `rfp`.`rfp_id` = 1;
            */
            $change_rfp_status_query = "UPDATE
                                                `rfp`
                                            SET
                                                `status` = 'Closed'
                                            WHERE
                                                `rfp`.`rfp_id` = " . (int)$rfpId;
        } else {
            $_SESSION["rfp-status-changed-err"] = "Invalid Action.";
            header("Location: rfp.php");
            exit;
        }

        if (isset($change_rfp_status_query)) {
            try {
                $change_rfp_status_result = mysqli_query($db, $change_rfp_status_query);
                if ($change_rfp_status_result === FALSE) {
                    throw new Exception("MySQL Error: " . mysqli_error($db));
                } else {
                    // status changed successfully
                    $_SESSION["rfp-status-changed-msg"] = "RFP status Updated.";
                    header("Location: rfp.php");
                    exit;
                }
            } catch (Exception $e) {
                // status changed successfully
                $_SESSION["rfp-status-changed-err"] = $e->getMessage();
                header("Location: rfp.php");
                exit;
            }
        }
    } else {
        // rfp Id not found
        $_SESSION["rfp-status-changed-err"] = "rfp Id not found.";
        header("Location: rfp.php");
        exit;
    }
}

// logic to count all rfp begin here
$count_rfp_query = "SELECT count(*) as total_rfp FROM `rfp`";

try {
    $count_rfp_result = mysqli_query($db, $count_rfp_query);
    if ($count_rfp_result === FALSE) {
        throw new Exception("MySQL Error:" . mysqli_error($db));
    } else {
        if (mysqli_num_rows($count_rfp_result) > 0) {
            $row = mysqli_fetch_array($count_rfp_result);
            $total_rfp = $row['total_rfp'];
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

$no_of_rfp = 0;
$results_per_page = 5;

if (!empty($total_rfp)) {
    $no_of_rfp = $total_rfp;
}
$no_of_pages = ceil($no_of_rfp / $results_per_page);
$starting_row_number = ($current_page - 1) * $results_per_page;

/*RAW QUERY
    SELECT
        rfp_id,
        item_name,
        item_description,
        quantity,
        last_date,
        minimum_price,
        maximum_price,
        status
    FROM
        `rfp`
    ORDER BY
        `rfp_id`
    DESC
    LIMIT 0, 5
 */
$rfp_list_query = "SELECT `rfp_id`, `item_name`, `item_description`, `quantity`, `last_date`, `minimum_price`, `maximum_price`, `status` FROM `rfp` ORDER BY `rfp_id` DESC LIMIT " . $starting_row_number . ',' . $results_per_page;

// results to be shown on page.
try {
    $rfp_list_result = mysqli_query($db, $rfp_list_query);
    if ($rfp_list_result === FALSE) {
        throw new Exception("MySQL Error: " . mysqli_error($db));
    } else {
        // display list in view page
    }
} catch (Exception $e) {
    $formErrors[] = $e->getMessage();
}

include("rfp.view.php");
