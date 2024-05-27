<?php
require_once('includes/configuration.php');
require_once("includes/functions.inc.php");

if (!isset($_SESSION['email']) || strtolower($_SESSION['type']) != "admin") { // check if user is not logged in
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
                                <h4 class="mb-0 font-size-18">RFP Quotes</h4>

                                <div class="page-title-right">
                                    <ol class="breadcrumb m-0">
                                        <li class="breadcrumb-item"><a href="dashboard-admin.php">Home</a></li>
                                        <li class="breadcrumb-item active">RFP Quotes</li>
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

                                    <div class="TableHeader">
                                        <div class="row">
                                            <div class="col-lg-3">
                                                <h4 class="card-title">RFP Quotes</h4>
                                            </div>

                                            <!-- display form error message  -->
                                            <?php
                                            if (count($formErrors) > 0) {
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
                                        </div>
                                    </div>
                                    <div class="table-responsive">
                                        <table class="table mb-0 listingData dt-responsive" id="datatable">
                                            <thead>
                                                <tr>
                                                    <th>Quote No.</th>
                                                    <th>Vendor Id</th>
                                                    <th>Vendor Price</th>
                                                    <th>Item Description</th>
                                                    <th>Quantity</th>
                                                    <th>Total Cost</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                if (isset($rfpId) && mysqli_num_rows($quotes_list_result) > 0) {
                                                    $counter = $starting_row_number + 1;

                                                    while ($quoteRow = mysqli_fetch_array($quotes_list_result)) {

                                                ?>
                                                        <tr>
                                                            <th scope="row"><?php echo $quoteRow["quote_id"]; ?></th>
                                                            <td><?php echo $quoteRow["vendor_id"]; ?></td>
                                                            <td><?php echo $quoteRow["vendor_price"]; ?></td>
                                                            <td><?php echo $quoteRow["item_description"]; ?></td>
                                                            <td><?php echo $quoteRow["quantity"]; ?></td>
                                                            <td><?php echo $quoteRow["total_cost"]; ?></td>
                                                        </tr>
                                                    <?php
                                                    }
                                                } else { ?>
                                                    <tr>
                                                        <th scope="row">No Quotes found for given RFP.</th>
                                                    </tr>

                                                <?php }
                                                ?>
                                            </tbody>
                                        </table>
                                    </div>

                                    <?php
                                    if (isset($rfpId)) { ?>
                                        <div class="row pt-3">
                                            <div class="col-sm-12 col-md-5">
                                                <?php
                                                // Calculate first column number
                                                $firstColumnNumber = (($current_page - 1) * $results_per_page) + 1;
                                                // Calculate last column number
                                                $lastColumnNumber = min($firstColumnNumber + $results_per_page - 1, $no_of_quotes_for_rfp);
                                                ?>
                                                <div class="dataTables_info" id="datatable_info" role="status" aria-live="polite">Showing <?php echo ($no_of_quotes_for_rfp == 0) ? "0" : $firstColumnNumber; ?> to <?php echo $lastColumnNumber; ?> of <?php echo $no_of_quotes_for_rfp; ?> entries</div>
                                            </div>
                                            <div class="col-sm-12 col-md-7 dataTables_wrapper ">
                                                <div class="dataTables_paginate paging_simple_numbers" id="datatable_paginate">
                                                    <ul class="pagination">
                                                        <li class="paginate_button page-item previous <?php if ($current_page <= 1) echo 'disabled'; ?>" id="datatable_previous">
                                                            <a href="view-quotes-for-rfp.php?page=<?php if ($current_page <= 1) {
                                                                                                        echo $no_of_pages;
                                                                                                    } else {
                                                                                                        echo $current_page - 1;
                                                                                                    }
                                                                                                    ?>" aria-controls="datatable" data-dt-idx="0" tabindex="0" class="page-link">Previous</a>
                                                        </li>
                                                        <?php
                                                        // loop for displaying page numbers
                                                        for ($page = 1; $page <= $no_of_pages; $page++) {

                                                        ?>
                                                            <li class="paginate_button page-item <?php if ($page == $current_page) echo 'active'; ?>">
                                                                <a href="view-quotes-for-rfp.php?page=<?php echo $page; ?>" aria-controls="datatable" data-dt-idx="1" tabindex="0" class="page-link"><?php echo $page; ?></a>
                                                            </li>
                                                        <?php } ?>
                                                        <li class="paginate_button page-item next <?php if (($current_page + 1) > $no_of_pages) echo 'disabled'; ?>" id="datatable_next"><a href="view-quotes-for-rfp.php?page=<?php if ($current_page >= $no_of_pages) {
                                                                                                                                                                                                                                    echo 1;
                                                                                                                                                                                                                                } else {
                                                                                                                                                                                                                                    echo $current_page + 1;
                                                                                                                                                                                                                                } ?>" aria-controls="datatable" data-dt-idx="2" tabindex="0" class="page-link">Next</a>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    <?php }
                                    ?>

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