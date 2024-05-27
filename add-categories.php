<?php
require_once('includes/configuration.php');
require_once('includes/functions.inc.php');

if (!isset($_SESSION['email']) || strtolower($_SESSION['type']) != "admin") { // check if user is not logged in
    header("Location: index.php"); // redirect to login page and the login will redirect the user to vendor dashboard if the user is logged in.
    exit;
}

$errors = []; // Initialize array to store errors
$formErrors = []; // Store all the form errors
$formSuccess = []; // Store all the form success

// Initialize form data
$categoryName = "";
$status = "";

if (isset($_POST) && !empty($_POST)) {
    // Retrieve form data
    $categoryName = $_POST['categoryName'];
    $status = $_POST['status'];

    // Perform validation checks

    // Validate category name
    if (empty($categoryName)) {
        $error_response = error_msg(1101);
        if ($error_response['status']) {
            $errors[0] = $error_response['message'];
        } else {
            $formErrors[] = $error_response['message'];
        }
    }

    // Validate category status
    if (empty($status)) {
        $error_response = error_msg(1102);
        if ($error_response['status']) {
            $errors[1] = $error_response['message'];
        } else {
            $formErrors[] = $error_response['message'];
        }
    }

    // If there are no errors, process the form
    if (empty($errors) && empty($formErrors)) {
        $categoryName = mysqli_real_escape_string($db, $categoryName);
        $status = mysqli_real_escape_string($db, $status);

        if (strtolower($status === "active")) {
            $db_category_status = 'Active';
        } else if (strtolower($status === "inactive")) {
            $db_category_status = "Inactive";
        }

        /*
            INSERT INTO `categories`(
                `category_name`,
                `status`
            )
            VALUES('Furniture', 'Active');
        */
        $add_category_query = "INSERT INTO `categories` (`category_name`, `status`) VALUES ('$categoryName', '$db_category_status');";

        try {
            $add_category_result = mysqli_query($db, $add_category_query);
            if ($add_category_result === FALSE) {
                throw new Exception("MySQL Error: " . mysqli_error($db));
            } else {
                // category added
                $_SESSION['category-add-success'] = "Category added successfully";
                header("Location: categories.php");
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
                        </li>F

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
                                <h4 class="mb-0 font-size-18">Add Categories</h4>

                                <div class="page-title-right">
                                    <ol class="breadcrumb m-0">
                                        <li class="breadcrumb-item"><a href="dashboard-admin.php">Home</a></li>
                                        <li class="breadcrumb-item"><a href="categories.php">Categories</li></a></li>
                                        <li class="breadcrumb-item active">Add Categories</li>
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
                                    <form class="validateJs" id="addCategoryForm" method="post">
                                        <div class="row">
                                            <div class="col-lg-4 col-md-4">
                                                <div class="form-group">
                                                    <label for="categoryName">Category Name<em>*</em></label>
                                                    <input type="text" class="form-control" id="categoryName" name="categoryName" placeholder="Enter Category Name" data-rule-mandatory="true" value="<?php if (!empty($categoryName)) echo $categoryName; ?>">
                                                    <div>
                                                        <font color="#f00000" size="2px" id="categoryNameError"><?php if (isset($errors[0]) && !empty($errors[0])) echo $errors[0]; ?></font>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-lg-6 col-md-6">
                                                <div class="form-group">
                                                    <label for="">Status<em>*</em></label>
                                                    <select class="form-control" id="status" name="status">
                                                        <option value="">(Select Status)</option>
                                                        <option value="active" <?php if (!empty($status) && $status == 'active') echo "selected"; ?>>Active</option>
                                                        <option value="inactive" <?php if (!empty($status) && $status == 'inactive') echo "selected"; ?>>Inactive</option>
                                                    </select>
                                                    <div>
                                                        <font color="#f00000" size="2px" id="statusError"><?php if (isset($errors[1]) && !empty($errors[1])) echo $errors[1]; ?></font>
                                                    </div>
                                                </div>
                                            </div>

                                        </div>

                                        <div class="row">
                                            <div class="col-lg-12 col-md-12 mt-4 text-right">
                                                <button type="submit" class="btn btn-primary mb-2 mt-1" name="addCategoryButton">Add</button>
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