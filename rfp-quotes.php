<?php
require_once('includes/configuration.php');

$errors = []; // Initialize array to store errors
$formErrors = []; // Store all the form errors
$formSuccess = []; // Store all the form success

if (!isset($_SESSION['email']) || strtolower($_SESSION['type']) != "admin") { // check if user is not logged in
    header("Location: index.php"); // redirect to login page and the login will redirect the user to vendor dashboard.
    exit;
}


// logic to count all quotes begin here
/*RAW QUERY
        SELECT
            COUNT(*) as total_quotes
        FROM
            `quote`;
     */
$count_quotes_query = "SELECT
                                COUNT(*) as total_quotes
                            FROM
                                `quote`";

try {
    $count_quotes_result = mysqli_query($db, $count_quotes_query);
    if ($count_quotes_result === FALSE) {
        throw new Exception("MySQL Error:" . mysqli_error($db));
    } else {
        if (mysqli_num_rows($count_quotes_result) > 0) {
            $row = mysqli_fetch_array($count_quotes_result);
            $total_quotes = $row['total_quotes'];
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

$no_of_quotes = 0;
$results_per_page = 5;

if (!empty($total_quotes)) {
    $no_of_quotes = $total_quotes;
}
$no_of_pages = ceil($no_of_quotes / $results_per_page);
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
        ORDER BY
            `quote_id`
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
                        ORDER BY
                            `quote_id`
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


include("rfp-quotes.view.php");
