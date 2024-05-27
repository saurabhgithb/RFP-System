<?php
require_once('includes/configuration.php');

$errors = []; // Initialize array to store errors
$formErrors = []; // Store all the form errors
$formSuccess = []; // Store all the form success

if (!isset($_SESSION['email']) || strtolower($_SESSION['type']) != "admin") { // check if user is not logged in
    header("Location: index.php"); // redirect to login page and the login will redirect the user to vendor dashboard.
    exit;
}

// display category added successful message if set
if (!empty($_SESSION['category-add-success'])) {
    $formSuccess[] = $_SESSION['category-add-success'];
    unset($_SESSION["category-add-success"]);
}

// display category status changed message if set
if (!empty($_SESSION['category-status-changed-msg'])) {
    $formSuccess[] = $_SESSION['category-status-changed-msg'];
    unset($_SESSION["category-status-changed-msg"]);
}

if (!empty($_GET) && isset($_GET["category_id"])) {
    if (!empty($_GET["category_id"])) {
        if (!empty($_GET['action']) && strtolower($_GET["action"]) == "deactivate") {
            /*RAW QUERY
                UPDATE
                    `categories`
                SET
                    `status` = 'Inactive'
                WHERE
                    `categories`.`category_id` = 1;
            */
            $change_category_status_query = "UPDATE
                                                `categories`
                                            SET
                                                `status` = 'Inactive'
                                            WHERE
                                                `categories`.`category_id` = " . (int)$_GET["category_id"];
        } else if (!empty($_GET['action']) && strtolower($_GET["action"]) == "activate") {
            /*RAW QUERY
                UPDATE
                    `categories`
                SET
                    `status` = 'Active'
                WHERE
                    `categories`.`category_id` = 1;
            */
            $change_category_status_query = "UPDATE
                                                `categories`
                                            SET
                                                `status` = 'Active'
                                            WHERE
                                                `categories`.`category_id` = " . $_GET["category_id"];
        } else {
            $formErrors[] = "Invalid Action.";
        }

        if (isset($change_category_status_query)) {
            try {
                $change_category_status_result = mysqli_query($db, $change_category_status_query);
                if ($change_category_status_result === FALSE) {
                    throw new Exception("MySQL Error: " . mysqli_error($db));
                } else {
                    // status changed successfully
                    $_SESSION["category-status-changed-msg"] = "Category Status Updated.";
                    header("Location: categories.php");
                    exit;
                }
            } catch (Exception $e) {
                $formErrors[] = $e->getMessage();
            }
        }
    } else {
        $formErrors[] = "Category Id not found.";
    }
}

// logic to count all categories begin here
$count_categories_query = "SELECT count(*) as total_categories FROM `categories`";

try {
    $count_categories_result = mysqli_query($db, $count_categories_query);
    if ($count_categories_result === FALSE) {
        throw new Exception("MySQL Error:" . mysqli_error($db));
    } else {
        if (mysqli_num_rows($count_categories_result) > 0) {
            $row = mysqli_fetch_array($count_categories_result);
            $total_categories = $row['total_categories'];
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

$no_of_categories = 0;
$results_per_page = 5;

if (!empty($total_categories)) {
    $no_of_categories = $total_categories;
}
$no_of_pages = ceil($no_of_categories / $results_per_page);
$starting_row_number = ($current_page - 1) * $results_per_page;

/*RAW QUERY
    SELECT
        category_id,
        category_name,
        status
    FROM
        `categories`
    ORDER BY
        `category_id`
    DESC
    LIMIT 0, 5
 */
$category_list_query = "SELECT category_id, category_name, `status` FROM `categories` ORDER BY `category_id` DESC LIMIT " . $starting_row_number . ',' . $results_per_page;

// results to be shown on page.
try {
    $category_list_result = mysqli_query($db, $category_list_query);
    if ($category_list_result === FALSE) {
        throw new Exception("MySQL Error: " . mysqli_error($db));
    } else {
        // display list in view page
    }
} catch (Exception $e) {
    $formErrors[] = $e->getMessage();
}

include("categories.view.php");
