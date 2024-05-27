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

// Initialize view data
$vendorPrice = "";
$itemDescription = "";
$quantity = "";
$totalCost = "";

if (!empty($_GET) && !empty(trim($_GET["quoteId"]))) {
    $quoteId = $_GET["quoteId"];
    $quoteId = mysqli_real_escape_string($db, $quoteId);

    /*RAW QUERY
        SELECT
            `quote_id`,
            `vendor_price`,
            `item_description`,
            `quantity`,
            `total_cost`
        FROM
            `quote`
        WHERE
            `quote_id` = 1;
     */
    $get_quote_details_query = "SELECT
                                    `quote_id`,
                                    `vendor_price`,
                                    `item_description`,
                                    `quantity`,
                                    `total_cost`
                                FROM
                                    `quote`
                                WHERE
                                    `quote_id` = " . (int)$_GET["quoteId"];
    try {
        $get_quote_details_result = mysqli_query($db, $get_quote_details_query);
        if ($get_quote_details_result === FALSE) {
            throw new Exception("My SQL Error: " . mysqli_error($db));
        } else {
            if (mysqli_num_rows($get_quote_details_result) > 0) {
                $get_quote_row = mysqli_fetch_array($get_quote_details_result);
                $vendorPrice = $get_quote_row["vendor_price"];
                $itemDescription = $get_quote_row["item_description"];
                $quantity = $get_quote_row["quantity"];
                $totalCost = $get_quote_row["total_cost"];
            } else {
                $formErrors[] = "Quote with given id not found.";
            }
        }
    } catch (Exception $e) {
        //throw $th;
        $formErrors[] = $e->getMessage();
    }
} else {
    $formErrors[] = "Unable to get quote id.";
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
                                <h4 class="mb-0 font-size-18">View Quote</h4>

                                <div class="page-title-right">
                                    <ol class="breadcrumb m-0">
                                        <li class="breadcrumb-item"><a href="dashboard-vendor.php">Home</a></li>
                                        <li class="breadcrumb-item"><a href="rfp-for-quotes-vendor.php">RFP</li></a></li>
                                        <li class="breadcrumb-item active">View Quote</li>
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
                                    <div class="form-horizontal">
                                        <div class="row">
                                            <!-- Vendor Price  -->
                                            <div class="col-md-12 col-lg-4 col-xl-4">
                                                <div class="form-group">
                                                    <label for="vendorprice">Vendor Price*</label>
                                                    <div class="form-control"><?php if (!empty($vendorPrice)) echo $vendorPrice; ?></div>
                                                </div>
                                            </div>
                                            <!-- Item Description  -->
                                            <div class="col-md-12 col-lg-4 col-xl-4">
                                                <div class="form-group">
                                                    <label for="itemdescription">Item Description<em>*</em></label>
                                                    <div class="form-control"><?php if (!empty($itemDescription)) echo $itemDescription; ?></div>

                                                </div>
                                            </div>
                                            <!-- Quantity  -->
                                            <div class="col-md-12 col-lg-4 col-xl-4">
                                                <div class="form-group">
                                                    <label for="quantity">Quantity*</label>
                                                    <div class="form-control"><?php if (!empty($quantity)) echo $quantity; ?></div>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- Total Cost  -->
                                        <div class="col-md-12 col-lg-6 col-xl-6">
                                            <div class="form-group">
                                                <label for="totalcost">Total Cost*</label>
                                                <div class="form-control"><?php if (!empty($totalCost)) echo $totalCost; ?></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
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

</html>