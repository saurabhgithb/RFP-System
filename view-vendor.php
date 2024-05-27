<?php
require_once('includes/configuration.php');
require_once('includes/functions.inc.php');

if (!isset($_SESSION['email']) || strtolower($_SESSION['type']) != "admin") { // check if user is not logged in
    header("Location: index.php"); // redirect to login page and the login will redirect the user to vendor dashboard.
    exit;
}

$errors = []; // Initialize array to store errors
$formErrors = []; // Store all the form errors
$formSuccess = []; // Store all the form success

// Initialize view data
$firstName = "";
$lastName = "";
$email = "";
$revenue = "";
$noOfEmployees = "";
$gstNo = "";
$panNo = "";
$phoneNo = "";
$categories = [];

// fetch countries before loading the form
$allCategoriesResponse = fetchAllCategories();
if (isset($allCategoriesResponse['db-error'])) {
    $formErrors[] = $allCategoriesResponse['db-error'];
}

if (!empty($_GET) && !empty(trim($_GET["userId"]))) {
    $userId = $_GET["userId"];
    $userId = mysqli_real_escape_string($db, $userId);
    /*RAW QUERY
        SELECT
            ud.user_id,
            vd.vendor_id,
            ud.first_name,
            ud.last_name,
            ud.email,
            vd.revenue,
            vd.no_of_employees,
            vd.gst_number,
            vd.pan_number,
            vd.phone_number
        FROM
            `user_details` ud
        JOIN `vendor_details` vd ON
            ud.user_id = vd.user_id
        WHERE
            ud.user_id = 1
    */
    $get_vendor_details_query = "SELECT
                                    ud.user_id,
                                    vd.vendor_id,
                                    ud.first_name,
                                    ud.last_name,
                                    ud.email,
                                    vd.revenue,
                                    vd.no_of_employees,
                                    vd.gst_number,
                                    vd.pan_number,
                                    vd.phone_number
                                FROM
                                    `user_details` ud
                                LEFT JOIN `vendor_details` vd ON
                                    ud.user_id = vd.user_id
                                WHERE
                                    ud.user_id = " . (int)$_GET["userId"];
    try {
        $get_vendor_details_result = mysqli_query($db, $get_vendor_details_query);
        if ($get_vendor_details_result === FALSE) {
            throw new Exception("My SQL Error: " . mysqli_error($db));
        } else {
            if (mysqli_num_rows($get_vendor_details_result) > 0) {
                $get_vendor_row = mysqli_fetch_array($get_vendor_details_result);
                $firstName = $get_vendor_row["first_name"];
                $lastName = $get_vendor_row["last_name"];
                $email = $get_vendor_row["email"];
                $revenue = $get_vendor_row["revenue"];
                $noOfEmployees = $get_vendor_row["no_of_employees"];
                $gstNo = $get_vendor_row["gst_number"];
                $panNo = $get_vendor_row["pan_number"];
                $phoneNo = $get_vendor_row["phone_number"];
                $vendor_id = $get_vendor_row["vendor_id"];

                // get vendor's category in which he has registered
                if ($vendor_id) { // check if vendor id found
                    // get vendor's categories
                    $fetchVendorCategoriesResponse = fetchVendorsCategory($vendor_id);
                    if ($fetchVendorCategoriesResponse["status"]) { // check if vendor categories found.
                        $categories = $fetchVendorCategoriesResponse["categories-data"];
                    } else {
                        $formErrors[] = $fetchVendorCategoriesResponse["error-msg"];
                    }
                }
            } else {
                $formErrors[] = "Vendor details with given user id not found.";
            }
        }
    } catch (Exception $e) {
        //throw $th;
        $formErrors[] = $e->getMessage();
    }
} else {
    $formErrors[] = "User Id not found.";
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
                            <a href="users.html" class="waves-effect">
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
                                <h4 class="mb-0 font-size-18">View Vendor</h4>

                                <div class="page-title-right">
                                    <ol class="breadcrumb m-0">
                                        <li class="breadcrumb-item"><a href="dashboard-admin.php">Home</a></li>
                                        <li class="breadcrumb-item"><a href="vendors.php">Vendors</li></a></li>
                                        <li class="breadcrumb-item active">View Vendor</li>
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
                                            <div class="col-md-12 col-lg-4 col-xl-4">
                                                <div class="form-group">
                                                    <label>First Name</label>
                                                    <div class="form-control"><?php if (!empty($firstName)) echo $firstName; ?></div>
                                                </div>
                                            </div>
                                            <div class="col-md-12 col-lg-4 col-xl-4">
                                                <div class="form-group">
                                                    <label>Last Name</label>
                                                    <div class="form-control"><?php if (!empty($lastName)) echo $lastName; ?></div>

                                                </div>
                                            </div>
                                            <div class="col-md-12 col-lg-4 col-xl-4">
                                                <div class="form-group">
                                                    <label>Email</label>
                                                    <div class="form-control"><?php if (!empty($email)) echo $email; ?></div>
                                                </div>
                                            </div>
                                            <div class="col-md-12 col-lg-4 col-xl-4">
                                                <div class="form-group">
                                                    <label>Revenue</label>
                                                    <div class="form-control"><?php if (!empty($revenue)) echo $revenue; ?></div>
                                                </div>
                                            </div>
                                            <div class="col-md-12 col-lg-4 col-xl-4">
                                                <div class="form-group">
                                                    <label>No of Employees</label>
                                                    <div class="form-control"><?php if (!empty($noOfEmployees)) echo $noOfEmployees; ?></div>
                                                </div>
                                            </div>
                                            <div class="col-md-12 col-lg-4 col-xl-4">
                                                <div class="form-group">
                                                    <label>GST No</label>
                                                    <div class="form-control"><?php if (!empty($gstNo)) echo $gstNo; ?></div>
                                                </div>
                                            </div>
                                            <div class="col-md-12 col-lg-4 col-xl-4">
                                                <div class="form-group">
                                                    <label>PAN No</label>
                                                    <div class="form-control"><?php if (!empty($panNo)) echo $panNo; ?></div>
                                                </div>
                                            </div>
                                            <div class="col-md-12 col-lg-4 col-xl-4">
                                                <div class="form-group">
                                                    <label>Phone No</label>
                                                    <div class="form-control"><?php if (!empty($phoneNo)) echo $phoneNo; ?></div>
                                                </div>
                                            </div>
                                            <div class="col-md-12 col-lg-6 col-xl-6">
                                                <div class="form-group">
                                                    <label for="Categories">Categories Registered</label>
                                                    <?php
                                                    if (!isset($allCategoriesResponse['db-error']) && !empty($allCategoriesResponse)) {
                                                        foreach ($allCategoriesResponse as $category) {
                                                            $category_id = $category['category_id'];
                                                            $category_name = $category['category_name'];

                                                            // Check if the category is selected
                                                            $selected = (!empty($categories) && in_array($category_id, $categories)) ? 'selected' : '';

                                                            if (!empty($categories) && in_array($category_id, $categories)) {
                                                                echo "<div>" . $category_name . "</div>";
                                                            }
                                                        }
                                                    }
                                                    ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
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