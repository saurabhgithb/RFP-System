<?php
require_once('includes/configuration.php');
require_once('includes/functions.inc.php');

if (!isset($_SESSION['email']) || strtolower($_SESSION['type']) != "admin") { // check if user is not logged in
    header("Location: index.php"); // redirect to login page and the login will redirect the user to vendor dashboard.
    exit;
}

if (!empty($_GET) && !empty(trim($_GET["categoryId"]))) {
    $categoryId = $_GET["categoryId"];
} else {
    $_SESSION["fetch_vendor_category_error"] = "Category Id not found. Please select category before adding RFP.";
    header("Location: rfp-select-category.php");
    exit;
}

$errors = []; // Initialize array to store errors
$formErrors = []; // Store all the form errors
$formSuccess = []; // Store all the form success

// Initialize form data
$itemName = "";
$itemDescription = "";
$quantity = "";
$lastDate = "";
$minimumPrice = "";
$maximumPrice = "";
$vendors = [];

// fetch countries before loading the form
$fetchVendorsResponse = fetchCategoryVendors($categoryId);
if (!empty($fetchVendorsResponse) && isset($fetchVendorsResponse["status"]) && $fetchVendorsResponse["status"] == false) {
    if (isset($fetchVendorsResponse["error-msg"])) {
        $_SESSION["fetch_vendor_category_error"] = $fetchVendorsResponse["error-msg"];
    } else {
        $_SESSION["fetch_vendor_category_error"] = "Error occured while fetching vendors. Please try again.";
    }
    header("Location: rfp-select-category.php");
    exit;
}

if (isset($_POST) && !empty($_POST)) {
    // print "<pre>";
    // print_r($_POST);
    // print "</pre>";
    // Retrieve form data
    $itemName = $_POST["itemName"];
    $itemDescription = $_POST["itemDescription"];
    $quantity = $_POST["quantity"];
    $lastDate = $_POST["lastDate"];
    $minimumPrice = $_POST["minimumPrice"];
    $maximumPrice = $_POST["maximumPrice"];
    $vendors = isset($_POST['vendors']) ? $_POST["vendors"] : "";

    // Perform validation checks

    // Validate item name
    if (empty($itemName)) {
        $error_response = error_msg(1201);
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

    // Validate lastDate
    $currentTimestamp = time();
    if (empty($lastDate)) {
        $error_response = error_msg(1501);
        if ($error_response['status']) {
            $errors[3] = $error_response['message'];
        } else {
            $formErrors[] = $error_response['message'];
        }
    } else if (strtotime($lastDate) < $currentTimestamp) {
        $error_response = error_msg(1502);
        if ($error_response['status']) {
            $errors[3] = $error_response['message'];
        } else {
            $formErrors[] = $error_response['message'];
        }
    }

    // Validate maximum price
    if (empty($minimumPrice)) {
        $error_response = error_msg(1801);
        if ($error_response['status']) {
            $errors[4] = $error_response['message'];
        } else {
            $formErrors[] = $error_response['message'];
        }
    } elseif (!is_numeric($minimumPrice)) {
        $error_response = error_msg(1802);
        if ($error_response['status']) {
            $errors[4] = $error_response['message'];
        } else {
            $formErrors[] = $error_response['message'];
        }
    } elseif ($minimumPrice <= 0) {
        $error_response = error_msg(1803);
        if ($error_response['status']) {
            $errors[4] = $error_response['message'];
        } else {
            $formErrors[] = $error_response['message'];
        }
    }
    // Validate maximum price
    if (empty($maximumPrice)) {
        $error_response = error_msg(1601);
        if ($error_response['status']) {
            $errors[5] = $error_response['message'];
        } else {
            $formErrors[] = $error_response['message'];
        }
    } elseif (!is_numeric($maximumPrice)) {
        $error_response = error_msg(1602);
        if ($error_response['status']) {
            $errors[5] = $error_response['message'];
        } else {
            $formErrors[] = $error_response['message'];
        }
    } elseif ($maximumPrice <= 0) {
        $error_response = error_msg(1603);
        if ($error_response['status']) {
            $errors[5] = $error_response['message'];
        } else {
            $formErrors[] = $error_response['message'];
        }
    }

    // Validate vendors
    if (empty($vendors)) {
        $error_response = error_msg(1701);
        if ($error_response['status']) {
            $errors[6] = $error_response['message'];
        } else {
            $formErrors[] = $error_response['message'];
        }
    }

    // If there are no errors, process the form
    if (empty($errors) && empty($formErrors)) {
        // Prevent SQL injection
        $itemName = mysqli_real_escape_string($db, $itemName);
        $itemDescription = mysqli_real_escape_string($db, $itemDescription);
        $quantity = mysqli_real_escape_string($db, $quantity);
        $quantity = mysqli_real_escape_string($db, $quantity);
        $dbLastDate = date("Y-m-d", strtotime($lastDate));
        $dbLastDate = mysqli_real_escape_string($db, $dbLastDate);
        $minimumPrice = mysqli_real_escape_string($db, $minimumPrice);
        $maximumPrice = mysqli_real_escape_string($db, $maximumPrice);

        /*RAW QUERY
                INSERT INTO `rfp`(
                    `item_name`,
                    `item_description`,
                    `quantity`,
                    `last_date`,
                    `minimum_price`,
                    `maximum_price`,
                    `category_id`
                )
                VALUES(
                    'Laptops',
                    'Purchase of 100 Laptops',
                    '100',
                    '2024-05-30',
                    '26000',
                    '50000',
                    '1')
        */
        $create_rfp_query = "INSERT INTO `rfp`(
            `item_name`,
            `item_description`,
            `quantity`,
            `last_date`,
            `minimum_price`,
            `maximum_price`,
            `category_id`
        )
        VALUES(
            '$itemName',
            '$itemDescription',
            '$quantity',
            '$dbLastDate',
            '$minimumPrice',
            '$maximumPrice',
            '$categoryId')";

        try {
            $create_rfp_result = mysqli_query($db, $create_rfp_query);
            if ($create_rfp_result === FALSE) {
                throw new Exception("MySQL error: " . mysqli_error($db));
            } else {
                // rfp created 
                $rfp_id = mysqli_insert_id($db);
                $formSuccess[] = "RFP Created.";

                // Add selected vendors for rfp into vendor_rfp table.
                foreach ($vendors as $vendor_id) {
                    $vendor_id = mysqli_real_escape_string($db, $vendor_id);
                    /*RAW QUERY
                        INSERT INTO `vendor_rfp`(`vendor_id`, `rfp_id`)
                        VALUES('1', '1');
                    */
                    $vendor_rfp_query = "INSERT INTO `vendor_rfp` (`vendor_id`, `rfp_id`) VALUES ('$vendor_id', '$rfp_id')";
                    try {
                        $vendor_rfp_result = mysqli_query($db, $vendor_rfp_query);
                        if ($vendor_rfp_result === FALSE) {
                            throw new Exception("MySQL Error: " . mysqli_error($db));
                        } else {
                            // insertion successful send mail
                            $fetchVendorDetailResponse = fetchVendorDetails($vendor_id);
                            if (!empty($fetchVendorDetailResponse) && isset($fetchVendorDetailResponse["status"]) && $fetchVendorDetailResponse["status"] == false) {
                                if (isset($fetchVendorsResponse["error-msg"])) {
                                    $formErrors[] = "Error fetching vendor#$vendor_id: " . $fetchVendorsResponse["error-msg"];
                                } else {
                                    $formErrors[] = "Error occured while fetching vendor detail with Id: $vendor_id. Please try again.";
                                }
                            } else if (!empty($fetchVendorDetailResponse) && isset($fetchVendorDetailResponse["status"]) && $fetchVendorDetailResponse["status"] == true) {
                                // send mail to that vendor informing about the rfp
                                $fetched_vendor_detail = $fetchVendorDetailResponse["vendor-data"];
                                $vendorName = $fetched_vendor_detail["first_name"] . " " . $fetched_vendor_detail["last_name"];
                                $vendorEmail = $fetched_vendor_detail["email"];

                                // send under review mail
                                $emailTo = $vendorEmail;
                                $emailFrom = 'saurabh.singh@velsof.com';
                                $emailSubject = 'Velocity has open an RFP for Quotation';
                                $emailBody = <<<END

                                Dear <strong>$vendorName</strong>,<br>
                                <br>
                                Greetings!!<br>
                                <br>
                                We want to inform that Velocity has opened an RFP & requesting the quotation on the same. Please find below the RFP details.<br>
                                <br>
                                RFP Name: $itemName<br>
                                RFP Description: $itemDescription<br>
                                End Date: $lastDate<br>
                                Kindly login into the RFP system on the link below & submit your quote.<br>
                                Click <a href="{$GlobalApplication}">here</a> to login.<br>
                                <br>
                                Thanks<br>
                                Velocity RFP System<br>
                                <br>
                                END;

                                $response_mail = sendMail($emailTo, $emailFrom, $emailSubject, $emailBody);

                                if ($response_mail['send-status']) { // email sent
                                    // $formSuccess[] = "Email sent to $email.";
                                } else { // error while sending email
                                    $formErrors[] = "Error occured while sending mail to vendor#$vendor_id" . $response_mail['mail-error'];
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
                            <a href="dashboard-admin.php" class="waves-effect">
                                <i class="mdi mdi-file-document-box-outline"></i>
                                <span>Dashboard</span>
                            </a>
                        </li>
                        <li>
                            <a href="categories.php" class="waves-effect">
                                <i class="mdi mdi-receipt"></i>
                                <span>Categories</span>
                            </a>
                        </li>
                        <li>
                            <a href="vendors.php" class="waves-effect">
                                <i class="mdi mdi-flip-vertical"></i>
                                <span>Vendors</span>
                            </a>
                        </li>
                        <li>
                            <a href="rfp.php" class="waves-effect">
                                <i class="mdi mdi-apps"></i>
                                <span>RFP</span>
                            </a>
                        </li>
                        <li>
                            <a href="rfp-quotes.php" class="waves-effect">
                                <i class="mdi mdi-apps"></i>
                                <span>RFP Quotes</span>
                            </a>
                        </li>

                        <li>
                            <a href="list-admin.php" class="waves-effect">
                                <i class="mdi mdi-weather-night"></i>
                                <span>Users Management</span>
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
                                <h4 class="mb-0 font-size-18">RFP Create</h4>

                                <div class="page-title-right">
                                    <ol class="breadcrumb m-0">
                                        <li class="breadcrumb-item"><a href="dashboard-admin.php">Home</a></li>
                                        <li class="breadcrumb-item"><a href="rfp.php">RFP</li></a></li>
                                        <li class="breadcrumb-item active">RFP Create</li>
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
                                    <form id="addRFPForm" class="form-horizontal" action="" method="post">
                                        <div class="row">
                                            <!-- Item Name  -->
                                            <div class="col-md-12 col-lg-4 col-xl-4">
                                                <div class="form-group">
                                                    <label for="itemname">Item name*</label>
                                                    <input type="text" class="form-control" id="itemname" placeholder="Enter Item Name" name="itemName" value="<?php if (!empty($itemName)) echo $itemName; ?>">
                                                    <div>
                                                        <font color="#f00000" size="2px" id="itemNameError"><?php if (isset($errors[0]) && !empty($errors[0])) echo $errors[0]; ?></font>
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
                                            <!-- Last Date  -->
                                            <div class="col-md-12 col-lg-6 col-xl-6">
                                                <div class="form-group">
                                                    <label for="lastdate">Last Date*</label>
                                                    <input type="date" class="form-control" id="lastdate" placeholder="Enter lastDate" name="lastDate" value="<?php if (!empty($lastDate)) echo $lastDate; ?>">
                                                    <div>
                                                        <font color="#f00000" size="2px" id="lastDateError"><?php if (isset($errors[3]) && !empty($errors[3])) echo $errors[3]; ?></font>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- Minimum Price  -->
                                            <div class="col-md-12 col-lg-6 col-xl-6">
                                                <div class="form-group">
                                                    <label for="minimumprice">Minimum Price*</label>
                                                    <input type="text" class="form-control" id="minimumprice" placeholder="Enter Minimum Price" name="minimumPrice" value="<?php if (!empty($minimumPrice)) echo $minimumPrice; ?>">
                                                    <div>
                                                        <font color="#f00000" size="2px" id="minimumPriceError"><?php if (isset($errors[4]) && !empty($errors[4])) echo $errors[4]; ?></font>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- Maximum Price  -->
                                            <div class="col-md-12 col-lg-6 col-xl-6">
                                                <div class="form-group">
                                                    <label for="maximumprice">Maximum Price*</label>
                                                    <input type="text" class="form-control" id="maximumprice" placeholder="Enter Maximum Price" name="maximumPrice" value="<?php if (!empty($maximumPrice)) echo $maximumPrice; ?>">
                                                    <div>
                                                        <font color="#f00000" size="2px" id="maximumPriceError"><?php if (isset($errors[5]) && !empty($errors[5])) echo $errors[5]; ?></font>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-12 col-lg-6 col-xl-6">
                                                <div class="form-group">
                                                    <label for="vendors">Vendors*</label>
                                                    <select class="form-control" multiple id="vendors" name="vendors[]">
                                                        <?php
                                                        if (isset($fetchVendorsResponse['status']) && $fetchVendorsResponse['status'] == true && !empty($fetchVendorsResponse["vendors-data"])) {
                                                            $vendors_data = $fetchVendorsResponse["vendors-data"];
                                                            foreach ($vendors_data as $vendor) {
                                                                $vendor_id = $vendor['vendor_id'];
                                                                $first_name = $vendor['first_name'];
                                                                $last_name = $vendor['last_name'];

                                                                // Check if the vendor is selected
                                                                $selected = (!empty($vendors) && in_array($vendor_id, $vendors)) ? 'selected' : '';

                                                                echo "<option value='" . $vendor_id . "' $selected>" . $first_name . " " . $last_name . "</option>";
                                                            }
                                                        }
                                                        ?>
                                                    </select>
                                                    <div>
                                                        <font color="#f00000" size="2px" id="vendorsError"><?php if (isset($errors[6]) && !empty($errors[6])) echo $errors[6]; ?></font>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="p-2 mt-3">
                                                <button class="btn btn-primary btn-block waves-effect waves-light" type="submit" name="createRfpSubmit">Create RFP</button>
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