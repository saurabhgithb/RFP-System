<?php
require_once('includes/configuration.php');

$errors = []; // Initialize array to store errors
$formErrors = []; // Store all the form errors
$formSuccess = []; // Store all the form success

if (!isset($_SESSION['email']) || strtolower($_SESSION['type']) != "admin") { // check if user is not logged in
    header("Location: index.php"); // redirect to login page and the login will redirect the user to vendor dashboard.
    exit;
}


// logic to count all admins begin here
/*RAW QUERY
        SELECT
            COUNT(*) as total_admins
        FROM
            `user_details`
        WHERE
            `type` = "Admin";
     */
$count_admins_query = "SELECT
                            COUNT(*) as total_admins
                        FROM
                            `user_details`
                        WHERE
                            `type` = 'Admin'";

try {
    $count_admins_result = mysqli_query($db, $count_admins_query);
    if ($count_admins_result === FALSE) {
        throw new Exception("MySQL Error:" . mysqli_error($db));
    } else {
        if (mysqli_num_rows($count_admins_result) > 0) {
            $row = mysqli_fetch_array($count_admins_result);
            $total_admins = $row['total_admins'];
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

$no_of_admins = 0;
$results_per_page = 5;

if (!empty($total_admins)) {
    $no_of_admins = $total_admins;
}
$no_of_pages = ceil($no_of_admins / $results_per_page);
$starting_row_number = ($current_page - 1) * $results_per_page;

/*RAW QUERY
        SELECT
            `user_id`,
            `first_name`,
            `last_name`,
            `email`,
            `status`
        FROM
            `user_details`
        WHERE
            `type` = "Admin"
        ORDER BY
            `user_id`
        DESC
        LIMIT 0, 5
     */
$admins_list_query = "SELECT
                        `user_id`,
                        `first_name`,
                        `last_name`,
                        `email`,
                        `status`
                    FROM
                        `user_details`
                    WHERE
                        `type` = 'Admin'
                    ORDER BY
                        `user_id`
                    DESC
                    LIMIT " . $starting_row_number . ',' . $results_per_page;

// results to be shown on page.
try {
    $admins_list_result = mysqli_query($db, $admins_list_query);
    if ($admins_list_result === FALSE) {
        throw new Exception("MySQL Error: " . mysqli_error($db));
    } else {
        // display list in view page
    }
} catch (Exception $e) {
    $formErrors[] = $e->getMessage();
}


include("list-admin.view.php");
