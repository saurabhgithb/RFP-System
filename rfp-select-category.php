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

if (isset($_SESSION['fetch_vendor_category_error']) && !empty($_SESSION["fetch_vendor_category_error"])) {
    $formErrors[] = $_SESSION['fetch_vendor_category_error'];
    unset($_SESSION['fetch_vendor_category_error']);
}


$postCategory = "";

// fetch countries before loading the form
$allCategoriesResponse = fetchAllCategories();
if (isset($allCategoriesResponse['db-error'])) {
    $formErrors[] = $allCategoriesResponse['db-error'];
}

if (isset($_POST) && !empty($_POST)) {
    $postCategory = $_POST["postCategory"];

    // Perform validation checks

    // Validate category
    if (empty($postCategory)) {
        $error_response = error_msg(1001);
        if ($error_response['status']) {
            $errors[0] = $error_response['message'];
        } else {
            $formErrors[] = $error_response['message'];
        }
    }

    // If there are no errors, process the form
    if (empty($errors) && empty($formErrors)) {
        // redirect the user to add-rfp page
        header("Location: add-rfp.php?categoryId=$postCategory");
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
                            <a href="rfp.php" class="waves-effect">
                                <i class="mdi mdi-flip-vertical"></i>
                                <span>RFP Lists</span>
                            </a>
                        </li>
                        <li>
                            <a href="users.html" class="waves-effect">
                                <i class="mdi mdi-apps"></i>
                                <span>User Management</span>
                            </a>
                        </li>

                        <li>
                            <a href="#" class="waves-effect">
                                <i class="mdi mdi-weather-night"></i>
                                <span>Vendors</span>
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
                                <h4 class="mb-0 font-size-18">Add rfp</h4>

                                <div class="page-title-right">
                                    <ol class="breadcrumb m-0">
                                        <li class="breadcrumb-item"><a href="dashboard-admin.php">Home</a></li>
                                        <li class="breadcrumb-item"><a href="rfp.php">RFP</li></a></li>
                                        <li class="breadcrumb-item active">RFP Select Category</li>
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
                                    <form class="validateJs" id="selectRfpCategoryForm" method="post">
                                        <div class="row">
                                            <div class="col-md-12 col-lg-6 col-xl-6">
                                                <div class="form-group">
                                                    <label for="Categories">Categories*</label>
                                                    <select class="form-control" id="categories" name="postCategory">
                                                        <option value="">(Select Category)</option>
                                                        <?php
                                                        if (!isset($allCategoriesResponse['db-error']) && !empty($allCategoriesResponse)) {
                                                            foreach ($allCategoriesResponse as $category) {
                                                                $category_id = $category['category_id'];
                                                                $category_name = $category['category_name'];

                                                                // Check if the category is selected
                                                                $selected = (!empty($postCategory) && ($category_id == $postCategory)) ? 'selected' : '';

                                                                echo "<option value='" . $category_id . "' $selected>" . $category_name . "</option>";
                                                            }
                                                        }
                                                        ?>
                                                    </select>
                                                    <div>
                                                        <font color="#f00000" size="2px" id="categoriesError"><?php if (isset($errors[0]) && !empty($errors[0])) echo $errors[0]; ?></font>
                                                    </div>
                                                </div>
                                            </div>

                                        </div>

                                        <div class="row">
                                            <div class="col-lg-12 col-md-12 mt-4 text-right">
                                                <button type="submit" class="btn btn-primary mb-2 mt-1" name="selectRfpCategoryButton">Select</button>
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