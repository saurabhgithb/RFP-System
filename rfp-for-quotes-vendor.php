<?php
require_once('includes/configuration.php');

$errors = []; // Initialize array to store errors
$formErrors = []; // Store all the form errors
$formSuccess = []; // Store all the form success

if (!isset($_SESSION['email']) && !isset($_SESSION["user_id"])) { // check if user is not logged in
    header("Location: index.php"); // redirect to login page.
    exit;
}

// check if vendor details exist and extract vendor id
/*RAW QUERY
    SELECT
        vendor_id
    FROM
        vendor_details
    WHERE
        user_id = 1;
*/
$get_vendor_id_query = " SELECT
                                    vendor_id
                                FROM
                                    vendor_details
                                WHERE
                                    user_id = " . (int)$_SESSION["user_id"];

try {
    $get_vendor_id_result = mysqli_query($db, $get_vendor_id_query);
    if ($get_vendor_id_result === FALSE) {
        throw new Exception("MySQL Error: " . mysqli_error($db));
    } else {
        if (mysqli_num_rows($get_vendor_id_result) > 0) {
            $get_vendor_row = mysqli_fetch_array($get_vendor_id_result);
            // this vendor id will be used throughout this page for fetching vendor's data of issued rfps and submitting quotes.
            $vendor_id = $get_vendor_row["vendor_id"];
        } else {
            $formErrors[] = "Vendor details not found.";
        }
    }
} catch (Exception $e) {
    $formErrors[] = $e->getMessage();
}

if (isset($_SESSION['rfp_id_not_found_error']) && !empty($_SESSION["rfp_id_not_found_error"])) {
    $formErrors[] = $_SESSION['rfp_id_not_found_error'];
    unset($_SESSION['rfp_id_not_found_error']);
}

if (!empty($_SESSION['rfp-status-success'])) {
    $formSuccess[] = $_SESSION['rfp-status-success'];
    unset($_SESSION["rfp-status-success"]);
}


if (!empty($vendor_id)) {
    // logic to count all rfp begin here
    $count_rfp_query = "SELECT
                            COUNT(*) as total_rfp_issued
                        FROM
                            `vendor_rfp` vr
                        JOIN rfp ON vr.rfp_id = rfp.rfp_id
                        WHERE
                            vr.vendor_id = " . (int)$vendor_id;

    try {
        $count_rfp_result = mysqli_query($db, $count_rfp_query);
        if ($count_rfp_result === FALSE) {
            throw new Exception("MySQL Error:" . mysqli_error($db));
        } else {
            if (mysqli_num_rows($count_rfp_result) > 0) {
                $row = mysqli_fetch_array($count_rfp_result);
                $total_rfp = $row['total_rfp_issued'];
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
            rfp.rfp_id,
            rfp.item_name,
            rfp.item_description,
            rfp.quantity,
            rfp.last_date,
            rfp.minimum_price,
            rfp.maximum_price,
            rfp.status
        FROM
            `vendor_rfp` vr
        JOIN rfp ON vr.rfp_id = rfp.rfp_id
        WHERE
            vr.vendor_id = 1
        ORDER BY
            rfp.`rfp_id`
        DESC
        LIMIT 0, 5
     */
    $rfp_list_query = "SELECT
                            rfp.rfp_id,
                            rfp.item_name,
                            rfp.item_description,
                            rfp.quantity,
                            rfp.last_date,
                            rfp.minimum_price,
                            rfp.maximum_price,
                            rfp.status
                        FROM
                            `vendor_rfp` vr
                        JOIN rfp ON vr.rfp_id = rfp.rfp_id
                        WHERE
                            vr.vendor_id = $vendor_id
                        ORDER BY
                            rfp.`rfp_id`
                        DESC
                        LIMIT " . $starting_row_number . ',' . $results_per_page;

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
} else {
    $formErrors[] = "Unable to fetch Issued RFPs.";
}

include("rfp-for-quotes-vendor.view.php");
