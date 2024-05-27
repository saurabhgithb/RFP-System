<?php
require_once('includes/configuration.php');
require_once('includes/functions.inc.php');

if (!isset($_SESSION['email'])) { // check if user is not logged in
    header("Location: index.php"); // redirect to login page and the login will redirect the user to vendor dashboard.
    exit;
}

$errors = []; // Initialize array to store errors
$formErrors = []; // Store all the form errors
$formSuccess = []; // Store all the form success

// check if vendor details exist using user_id and extract vendor id

/*RAW QUERY
    SELECT
        vendor_id
    FROM
        vendor_details
    WHERE
        user_id = 1;
*/
$user_id = $_SESSION["user_id"];
$user_id = mysqli_real_escape_string($db, $user_id);
$get_vendor_id_query = " SELECT
                            vendor_id
                        FROM
                            vendor_details
                        WHERE
                            user_id = " . (int)$user_id;

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
            $formErrors[] = "Please add your vendor details to be able to create quote. Vendor details not found.";
        }
    }
} catch (Exception $e) {
    $formErrors[] = $e->getMessage();
}

// check if rfp id is sent or not for creating quote.
if (!empty($_GET) && !empty(trim($_GET["rfpId"]))) {
    $rfpId = $_GET["rfpId"];

    $rfpId = mysqli_real_escape_string($db, $rfpId);

    // check rfp details 

    /*RAW QUERY
        SELECT
            `rfp_id`,
            `last_date`,
            `status`
        FROM
            `rfp`
        WHERE
            rfp_id = 2;
     */
    $check_rfp_details_query = "SELECT
                                    `rfp_id`,
                                    `item_name`,
                                    `last_date`,
                                    `status`
                                FROM
                                    `rfp`
                                WHERE
                                    rfp_id = " . (int)$rfpId;

    try {
        $check_rfp_details_result = mysqli_query($db, $check_rfp_details_query);
        if ($check_rfp_details_result === FALSE) {
            throw new Exception("My SQL Error: " . mysqli_error($db));
        } else {
            if (mysqli_num_rows($check_rfp_details_result) > 0) {
                // rfp details found.
                $check_rfp_row = mysqli_fetch_array($check_rfp_details_result);
                $rfp_status = $check_rfp_row['status'];
                $rfp_last_date = $check_rfp_row['last_date'];
                $rfp_name = $check_rfp_row["item_name"];
            } else { // details not found.
                $formErrors[] = "RFP with given id is not found.";
            }
        }
    } catch (Exception $e) {
        $formErrors[] = $e->getMessage();
    }
} else {
    $_SESSION["rfp_id_not_found_error"] = "RFP Id not found. Please try again.";
    header("Location: rfp-for-quotes-vendor.php");
    exit;
}

// Initialize form data
$vendorPrice = "";
$itemDescription = "";
$quantity = "";
$totalCost = "";

if (isset($_POST) && !empty($_POST)) {
    // print "<pre>";
    // print_r($_POST);
    // print "</pre>";
    // Retrieve form data
    $vendorPrice = $_POST["vendorPrice"];
    $itemDescription = $_POST["itemDescription"];
    $quantity = $_POST["quantity"];
    $totalCost = $_POST["totalCost"];

    // Perform validation checks

    // Validate vendor price
    if (empty($vendorPrice)) {
        $error_response = error_msg(1901);
        if ($error_response['status']) {
            $errors[0] = $error_response['message'];
        } else {
            $formErrors[] = $error_response['message'];
        }
    } elseif (!is_numeric($vendorPrice)) {
        $error_response = error_msg(1902);
        if ($error_response['status']) {
            $errors[0] = $error_response['message'];
        } else {
            $formErrors[] = $error_response['message'];
        }
    } elseif ($vendorPrice <= 0) {
        $error_response = error_msg(1903);
        if ($error_response['status']) {
            $errors[0] = $error_response['message'];
        } else {
            $formErrors[] = $error_response['message'];
        }
    }

    // Validate item Description
    if (empty($itemDescription)) {
        $error_response = error_msg(1301);
        if ($error_response['status']) {
            $errors[1] = $error_response['message'];
        } else {
            $formErrors[] = $error_response['message'];
        }
    }

    // Validate quantity
    if (empty($quantity)) {
        $error_response = error_msg(1401);
        if ($error_response['status']) {
            $errors[2] = $error_response['message'];
        } else {
            $formErrors[] = $error_response['message'];
        }
    } else {
        if (!is_numeric($quantity) || $quantity <= 0 || floor($quantity) != $quantity) {
            $error_response = error_msg(1402);
            if ($error_response['status']) {
                $errors[2] = $error_response['message'];
            } else {
                $formErrors[] = $error_response['message'];
            }
        }
    }

    // Validate total cost
    if (empty($totalCost)) {
        $error_response = error_msg(2001);
        if ($error_response['status']) {
            $errors[3] = $error_response['message'];
        } else {
            $formErrors[] = $error_response['message'];
        }
    } elseif (!is_numeric($totalCost)) {
        $error_response = error_msg(2002);
        if ($error_response['status']) {
            $errors[3] = $error_response['message'];
        } else {
            $formErrors[] = $error_response['message'];
        }
    } elseif ($totalCost <= 0) {
        $error_response = error_msg(2003);
        if ($error_response['status']) {
            $errors[3] = $error_response['message'];
        } else {
            $formErrors[] = $error_response['message'];
        }
    }

    // check if rfp's last date have already passed.
    $currentTimestamp = time();
    if (isset($rfp_last_date) && strtotime($rfp_last_date) < $currentTimestamp) {
        $formError[] = "RFP's last date have passed.";
    }

    // check if rfp has been closed.
    if (isset($rfp_status) && strtolower($rfp_status) != "open") {
        $formErrors[] = "RFP has been closed. Try for another RFP.";
    }

    // check if quote has been already created by the vendor for the given rfp

    if ($vendor_id && $rfpId) {
        /*
        SELECT
            COUNT(*) AS quote_count
        FROM
            Quote
        WHERE
            vendor_id = 1 AND rfp_id = 1;
     */
        $check_quote_already_created_query = "SELECT
                                            COUNT(*) AS quote_count
                                        FROM
                                            Quote
                                        WHERE
                                            vendor_id = " . (int)$vendor_id . " AND rfp_id = " . (int)$rfpId;
        try {
            $check_quote_already_created_result = mysqli_query($db, $check_quote_already_created_query);
            if ($check_quote_already_created_result === FALSE) {
                throw new Exception("My SQL Error: " . mysqli_error($db));
            } else {
                $check_quote_row = mysqli_fetch_array($check_quote_already_created_result);
                if ($check_quote_row["quote_count"] > 0) {
                    $formErrors[] = "You have already created an quote for this RFP.";
                }
            }
        } catch (Exception $e) {
            //throw $th;
            $formErrors[] = $e->getMessage();
        }
    } else {
        $formErrors[] = "RFP Id or Vendor Id was not found.";
    }


    // If there are no errors, process the form
    if (empty($errors) && empty($formErrors)) {
        // Prevent SQL injection
        $vendorPrice = mysqli_real_escape_string($db, $vendorPrice);
        $itemDescription = mysqli_real_escape_string($db, $itemDescription);
        $quantity = mysqli_real_escape_string($db, $quantity);
        $totalCost = mysqli_real_escape_string($db, $totalCost);

        /*RAW QUERY
                INSERT INTO `quote`(
                    `vendor_id`,
                    `rfp_id`,
                    `vendor_price`,
                    `item_description`,
                    `quantity`,
                    `total_cost`
                )
                VALUES(
                    '1',
                    '1',
                    '1000',
                    '100 Laptops of high hp brand',
                    '100',
                    '500000')
        */
        $create_quote_query = "INSERT INTO `quote`(
            `vendor_id`,
            `rfp_id`,
            `vendor_price`,
            `item_description`,
            `quantity`,
            `total_cost`
        )
        VALUES(
            '$vendor_id',
            '$rfpId',
            '$vendorPrice',
            '$itemDescription',
            '$quantity',
            '$totalCost')";

        try {
            $create_quote_result = mysqli_query($db, $create_quote_query);
            if ($create_quote_result === FALSE) {
                throw new Exception("MySQL error: " . mysqli_error($db));
            } else {
                // quote created 
                $quote_id = mysqli_insert_id($db);
                $formSuccess[] = "Quote Created.";

                // send mail to all admins informing the quote has been created.

                // query to select all admins 
                $select_admin_query = "SELECT
                                            `user_id`,
                                            `email`,
                                            `type`,
                                            `status`
                                        FROM
                                            `user_details`
                                        WHERE 
                                            `type` 
                                                = 'Admin' AND
                                            `status`
                                                = 'Approved'";

                try {
                    $select_admin_result = mysqli_query($db, $select_admin_query);
                    if ($select_admin_result === FALSE) {
                        throw new Exception("MySQL Error: " . mysqli_error($db));
                    } else {
                        if (mysqli_num_rows($select_admin_result) > 0) {
                            while ($select_admin_row = mysqli_fetch_array($select_admin_result)) {
                                $adminEmail = $select_admin_row["email"];
                                $vendorName = $_SESSION["name"];
                                $todayDate = date("d-m-Y");

                                // send under review mail
                                $emailTo = $adminEmail;
                                $emailFrom = 'saurabh.singh@velsof.com';
                                $emailSubject = 'New Quotation in Velocity RFP System';
                                $emailBody = <<<END

                                Hi Admin,<br>
                                <br>
                                Greetings!!<br>
                                <br>
                                Vendor $vendorName has submitted the quote the RFP named $rfp_name. Please find below the details of the quote submitted by the Vendor.<br>
                                <br>
                                Quote Price: $vendorPrice<br>
                                Quote Date: $todayDate<br>
                                <br>
                                Thanks<br>
                                Velocity RFP System<br>
                                <br>
                                END;

                                $response_mail = sendMail($emailTo, $emailFrom, $emailSubject, $emailBody);

                                if ($response_mail['send-status']) { // email sent
                                    // $formSuccess[] = "Email sent to $email.";
                                } else { // error while sending email
                                    $formErrors[] = "Error occured while sending mail: " . $response_mail['mail-error'];
                                }
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
    }
}

?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <title>ASSAM FLOOD</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="Skote is a fully featured premium admin dashboard template built on top of awesome Bootstrap 4.4.1" name="description" />
    <meta content="Themesbrand" name="author" />
    <!-- App favicon -->
    <link rel="shortcut icon" href="assets/images/favicon.ico">

    <!-- DataTables -->
    <link href="assets/libs/datatables.net-bs4/css/dataTables.bootstrap4.min.css" rel="stylesheet" type="text/css" />
    <link href="assets/libs/datatables.net-buttons-bs4/css/buttons.bootstrap4.min.css" rel="stylesheet" type="text/css" />
    <!-- Responsive datatable examples -->
    <link href="assets/libs/datatables.net-responsive-bs4/css/responsive.bootstrap4.min.css" rel="stylesheet" type="text/css" />

    <!-- Bootstrap Css -->
    <link href="assets/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <!-- Icons Css -->
    <link href="assets/css/icons.min.css" rel="stylesheet" type="text/css" />
    <!-- App Css-->
    <link href="assets/css/app.min.css" rel="stylesheet" type="text/css" />
    <link href="assets/css/style.css" rel="stylesheet" type="text/css" />

</head>

<body data-sidebar="dark">

    <!-- Begin page -->
    <div id="layout-wrapper">

        <header id="page-topbar">
            <div class="navbar-header">
                <div class="d-flex">
                    <!-- LOGO -->
                    <div class="navbar-brand-box">

                        <a href="index.html" class="logo logo-light">
                            <span class="logo-sm">
                                <img src="assets/images/velocity_logo.png" alt="" height="40">
                            </span>
                            <span class="logo-lg">
                                <img src="assets/images/velocity_logo.png" alt="" height="">
                            </span>
                        </a>
                    </div>
                </div>

                <div class="d-flex pr-2">
                    <div class="dropdown d-inline-block">
                        <span class="d-none d-xl-inline-block ml-1" key="t-henry">Welcome <?php if (!empty($_SESSION['name'])) echo $_SESSION['name']; ?></span>&nbsp;&nbsp;
                        <a class="" href="logout.php">Logout</span></a>
                    </div>
                </div>
            </div>
        </header>

        <!-- ========== Left Sidebar Start ========== -->
        <div class="vertical-menu">

            <div data-simplebar class="h-100">

                <!--- Sidemenu -->
                <div id="sidebar-menu">
                    <!-- Left Menu Start -->
                    <ul class="metismenu list-unstyled" id="side-menu">
                        <li>
                            <a href="dashboard-vendor.php" class="waves-effect">
                                <i class="mdi mdi-file-document-box-outline"></i>
                                <span>Dashboard</span>
                            </a>
                        </li>
                        <li>
                            <a href="rfp-for-quotes-vendor.php" class="waves-effect">
                                <i class="mdi mdi-receipt"></i>
                                <span>RFP for Quotes</span>
                            </a>
                        </li>
                        <li>
                            <a href="edit-vendor.php" class="waves-effect">
                                <i class="mdi mdi-receipt"></i>
                                <span>Edit Vendor</span>
                            </a>
                        </li>

                    </ul>

                </div>
                <!-- Sidebar -->
            </div>
        </div>
        <!-- Left Sidebar End -->

        <!-- ============================================================== -->
        <!-- Start right Content here -->
        <!-- ============================================================== -->
        <div class="main-content">

            <div class="page-content">
                <div class="container-fluid">

                    <!-- start page title -->
                    <div class="row">
                        <div class="col-12">
                            <div class="page-title-box d-flex align-items-center justify-content-between">
                                <h4 class="mb-0 font-size-18">Quote Create</h4>

                                <div class="page-title-right">
                                    <ol class="breadcrumb m-0">
                                        <li class="breadcrumb-item"><a href="dashboard-vendor.php">Home</a></li>
                                        <li class="breadcrumb-item"><a href="rfp-for-quotes-vendor.php">RFP</li></a></li>
                                        <li class="breadcrumb-item active">Quote Create</li>
                                    </ol>
                                </div>

                            </div>
                        </div>
                    </div>
                    <!-- end page title -->

                    <!-- end row -->


                    <div class="row">
                        <div class="col-lg-12">
                            <div class="card">
                                <div class="card-body">
                                    <!-- display form error message  -->
                                    <?php
                                    if (!empty($formErrors) && count($formErrors) > 0) {
                                    ?>
                                        <div class="error_container" style="color: red;">
                                            <?php
                                            // Form Errors Display.				
                                            $numError = count($formErrors);
                                            for ($i = 0; $i < $numError; $i++) {
                                                echo ($i + 1) . ". " . $formErrors[$i] . "<br>";
                                            }
                                            ?>
                                        </div>
                                    <?php
                                    }
                                    ?>
                                    <!-- display form success message  -->
                                    <?php
                                    if (!empty($formSuccess) && count($formSuccess) > 0) {
                                    ?>
                                        <div class="error_container" style="color: green;">
                                            <?php
                                            // Form Success Display.				
                                            $numError = count($formSuccess);
                                            for ($i = 0; $i < $numError; $i++) {
                                                echo $formSuccess[$i] . "<br>";
                                            }
                                            ?>
                                        </div>
                                    <?php
                                    }
                                    ?>
                                    <form id="createQuoteForm" class="form-horizontal" action="" method="post">
                                        <div class="row">
                                            <!-- Vendor Price  -->
                                            <div class="col-md-12 col-lg-4 col-xl-4">
                                                <div class="form-group">
                                                    <label for="vendorprice">Vendor Price*</label>
                                                    <input type="text" class="form-control" id="vendorprice" placeholder="Enter Vendor Price" name="vendorPrice" value="<?php if (!empty($vendorPrice)) echo $vendorPrice; ?>">
                                                    <div>
                                                        <font color="#f00000" size="2px" id="vendorPriceError"><?php if (isset($errors[0]) && !empty($errors[0])) echo $errors[0]; ?></font>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- Item Description  -->
                                            <div class="col-md-12 col-lg-4 col-xl-4">
                                                <div class="form-group">
                                                    <label for="itemdescription">Item Description<em>*</em></label>
                                                    <input type="text" class="form-control" id="itemdescription" placeholder="Enter Item Description" name="itemDescription" value="<?php if (!empty($itemDescription)) echo $itemDescription; ?>">
                                                    <div>
                                                        <font color="#f00000" size="2px" id="itemDescriptionError"><?php if (isset($errors[1]) && !empty($errors[1])) echo $errors[1]; ?></font>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- Quantity  -->
                                            <div class="col-md-12 col-lg-4 col-xl-4">
                                                <div class="form-group">
                                                    <label for="quantity">Quantity*</label>
                                                    <input type="text" class="form-control" id="quantity" placeholder="Enter Quantity" name="quantity" value="<?php if (!empty($quantity)) echo $quantity; ?>">
                                                    <div>
                                                        <font color="#f00000" size="2px" id="quantityError"><?php if (isset($errors[2]) && !empty($errors[2])) echo $errors[2]; ?></font>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- Total Cost  -->
                                            <div class="col-md-12 col-lg-6 col-xl-6">
                                                <div class="form-group">
                                                    <label for="totalcost">Total Cost*</label>
                                                    <input type="text" class="form-control" id="totalcost" placeholder="Enter Total Cost" name="totalCost" value="<?php if (!empty($totalCost)) echo $totalCost; ?>">
                                                    <div>
                                                        <font color="#f00000" size="2px" id="totalCostError"><?php if (isset($errors[3]) && !empty($errors[3])) echo $errors[3]; ?></font>
                                                    </div>
                                                </div>
                                            </div>

                                        </div>

                                        <div class="p-2 mt-3">
                                            <button class="btn btn-primary btn-block waves-effect waves-light" type="submit" name="createQuoteSumbit">Create</button>
                                        </div>
                                </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>



                <!-- end row -->

            </div> <!-- container-fluid -->
        </div>
        <!-- End Page-content -->


        <footer class="footer">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-sm-6">
                        2022 &copy; Copyright.
                    </div>
                    <div class="col-sm-6">
                        <div class="text-sm-right d-none d-sm-block">
                            Support Email:<a href="#" target="_blank" class="text-muted"> support@velsof.com </a>
                        </div>
                    </div>

                </div>
            </div>
        </footer>
    </div>
    <!-- end main content-->

    </div>
    <!-- END layout-wrapper -->



</body>
<?php
// include the script
include("includes/script.php");
?>

</html>