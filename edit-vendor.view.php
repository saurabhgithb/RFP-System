<?php
require_once('includes/configuration.php');
require_once("includes/functions.inc.php");

if (!isset($_SESSION['email'])) { // check if user is not logged in
    header("Location: index.php"); // redirect to login page and the login will redirect the user to vendor dashboard.
    exit;
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
                                <h4 class="mb-0 font-size-18">Edit Vendor</h4>

                                <div class="page-title-right">
                                    <ol class="breadcrumb m-0">
                                        <li class="breadcrumb-item"><a href="javascript: void(0);">Home</a></li>
                                        <li class="breadcrumb-item active">Edit Vendor</li>
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
                                    <form class="validateJs" id="editVendorForm" name="editVendorForm" method="post">
                                        <div class="row">
                                            <div class="col-md-12 col-lg-4 col-xl-4">
                                                <div class="form-group">
                                                    <label for="firstname">First name*</label>
                                                    <input type="text" class="form-control" id="firstname" placeholder="Enter Firstname" name="firstName" value="<?php if (!empty($firstName)) echo $firstName; ?>">
                                                    <div>
                                                        <font color="#f00000" size="2px" id="firstNameError"><?php if (isset($errors[0]) && !empty($errors[0])) echo $errors[0]; ?></font>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-12 col-lg-4 col-xl-4">
                                                <div class="form-group">
                                                    <label for="lastname">Last Name<em>*</em></label>
                                                    <input type="text" class="form-control" id="lastname" placeholder="Enter Lastname" name="lastName" value="<?php if (!empty($lastName)) echo $lastName; ?>">
                                                    <div>
                                                        <font color="#f00000" size="2px" id="lastNameError"><?php if (isset($errors[1]) && !empty($errors[1])) echo $errors[1]; ?></font>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-12 col-lg-4 col-xl-4">
                                                <div class="form-group">
                                                    <label for="email">Email*</label>
                                                    <input type="text" class="form-control" id="email" placeholder="Enter Email" name="email" value="<?php if (!empty($email)) echo $email; ?>">
                                                    <div>
                                                        <font color="#f00000" size="2px" id="emailError"><?php if (isset($errors[2]) && !empty($errors[2])) echo $errors[2]; ?></font>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-12 col-lg-6 col-xl-6">
                                                <div class="form-group">
                                                    <label for="revenue">Revenue (Last 3 Years in Lakhs)*</label>
                                                    <input type="text" class="form-control" id="revenue" placeholder="Enter Revenue" name="revenue" value="<?php if (!empty($revenue)) echo $revenue; ?>">
                                                    <div>
                                                        <font color="#f00000" size="2px" id="revenueError"><?php if (isset($errors[5]) && !empty($errors[5])) echo $errors[5]; ?></font>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-12 col-lg-6 col-xl-6">
                                                <div class="form-group">
                                                    <label for="noofemployees">No of Employees*</label>
                                                    <input type="text" class="form-control" id="noofemployees" placeholder="No of Employees" name="noOfEmployees" value="<?php if (!empty($noOfEmployees)) echo $noOfEmployees; ?>">
                                                    <div>
                                                        <font color="#f00000" size="2px" id="noOfEmployeesError"><?php if (isset($errors[6]) && !empty($errors[6])) echo $errors[6]; ?></font>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-12 col-lg-6 col-xl-6">
                                                <div class="form-group">
                                                    <label for="gstno">GST No*</label>
                                                    <input type="text" class="form-control" id="gstno" placeholder="Enter GST No" name="gstNo" value="<?php if (!empty($gstNo)) echo $gstNo; ?>">
                                                    <div>
                                                        <font color="#f00000" size="2px" id="gstNoError"><?php if (isset($errors[7]) && !empty($errors[7])) echo $errors[7]; ?></font>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-12 col-lg-6 col-xl-6">
                                                <div class="form-group">
                                                    <label for="panno">PAN No*</label>
                                                    <input type="text" class="form-control" id="panno" placeholder="Enter PAN No" name="panNo" value="<?php if (!empty($panNo)) echo $panNo; ?>">
                                                    <div>
                                                        <font color="#f00000" size="2px" id="panNoError"><?php if (isset($errors[8]) && !empty($errors[8])) echo $errors[8]; ?></font>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-12 col-lg-6 col-xl-6">
                                                <div class="form-group">
                                                    <label for="phoneno">Phone No*</label>
                                                    <input type="text" class="form-control" id="phoneno" placeholder="Enter Phone No" name="phoneNo" value="<?php if (!empty($phoneNo)) echo $phoneNo; ?>">
                                                    <div>
                                                        <font color="#f00000" size="2px" id="phoneNoError"><?php if (isset($errors[9]) && !empty($errors[9])) echo $errors[9]; ?></font>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-12 col-lg-6 col-xl-6">
                                                <div class="form-group">
                                                    <label for="Categories">Categories*</label>
                                                    <select class="form-control" multiple id="categories" name="categories[]">
                                                        <?php
                                                        if (!isset($allCategoriesResponse['db-error']) && !empty($allCategoriesResponse)) {
                                                            foreach ($allCategoriesResponse as $category) {
                                                                $category_id = $category['category_id'];
                                                                $category_name = $category['category_name'];

                                                                // Check if the category is selected
                                                                $selected = (!empty($categories) && in_array($category_id, $categories)) ? 'selected' : '';

                                                                echo "<option value='" . $category_id . "' $selected>" . $category_name . "</option>";
                                                            }
                                                        }
                                                        ?>
                                                    </select>
                                                    <div>
                                                        <font color="#f00000" size="2px" id="categoriesError"><?php if (isset($errors[10]) && !empty($errors[10])) echo $errors[10]; ?></font>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>



                                        <div class="row">
                                            <div class="col-lg-12 col-md-12 mt-4 text-right">
                                                <button type="submit" class="btn btn-primary mb-2 mt-1 waves-effect waves-light" name="editVendorSubmit">Edit</button>
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