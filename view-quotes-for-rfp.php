<?php
require_once('includes/configuration.php');

$errors = []; // Initialize array to store errors
$formErrors = []; // Store all the form errors
$formSuccess = []; // Store all the form success

if (!isset($_SESSION['email']) || strtolower($_SESSION['type']) != "admin") { // check if user is not logged in
    header("Location: index.php"); // redirect to login page and the login will redirect the user to vendor dashboard.
    exit;
}

if (!empty($_GET) && !empty(trim($_GET["rfpId"]))) {
    $rfpId = (int)$_GET["rfpId"];
    $rfpId = mysqli_real_escape_string($db, $rfpId);

    // logic to count all rfp begin here
    /*RAW QUERY
        SELECT
            COUNT(*) as total_quotes_for_rfp
        FROM
            `quote`
        WHERE
            rfp_id = " . (int)$rfpId;
     */
    $count_quotes_for_rfp_query = "SELECT
                            COUNT(*) as total_quotes_for_rfp
                        FROM
                            `quote`
                        WHERE
                            rfp_id = " . (int)$rfpId;

    try {
        $count_quotes_for_rfp_result = mysqli_query($db, $count_quotes_for_rfp_query);
        if ($count_quotes_for_rfp_result === FALSE) {
            throw new Exception("MySQL Error:" . mysqli_error($db));
        } else {
            if (mysqli_num_rows($count_quotes_for_rfp_result) > 0) {
                $row = mysqli_fetch_array($count_quotes_for_rfp_result);
                $total_quotes_for_rfp = $row['total_quotes_for_rfp'];
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

    $no_of_quotes_for_rfp = 0;
    $results_per_page = 5;

    if (!empty($total_quotes_for_rfp)) {
        $no_of_quotes_for_rfp = $total_quotes_for_rfp;
    }
    $no_of_pages = ceil($no_of_quotes_for_rfp / $results_per_page);
    $starting_row_number = ($current_page - 1) * $results_per_page;

    /*RAW QUERY
        SELECT
            `quote_id`,
            `vendor_id`,
            `vendor_price`,
            `item_description`,
            `quantity`,
            `total_cost`
        FROM
            `quote`
        WHERE
            rfp_id = 2;
        ORDER BY
            `rfp_id`
        DESC
        LIMIT 0, 5
     */
    $quotes_list_query = "SELECT
                            `quote_id`,
                            `vendor_id`,
                            `vendor_price`,
                            `item_description`,
                            `quantity`,
                            `total_cost`
                        FROM
                            `quote`
                        WHERE
                            rfp_id = $rfpId
                        ORDER BY
                            `rfp_id`
                        DESC
                        LIMIT " . $starting_row_number . ',' . $results_per_page;

    // results to be shown on page.
    try {
        $quotes_list_result = mysqli_query($db, $quotes_list_query);
        if ($quotes_list_result === FALSE) {
            throw new Exception("MySQL Error: " . mysqli_error($db));
        } else {
            // display list in view page
        }
    } catch (Exception $e) {
        $formErrors[] = $e->getMessage();
    }
} else {
    $formErrors[] = "RFP id not found.";
}

include("view-quotes-for-rfp.view.php");
