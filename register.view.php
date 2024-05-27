<!doctype html>
<html lang="en">

<head>

    <meta charset="utf-8" />
    <title>Register as Vendor | RFP System</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="Premium Multipurpose Admin & Dashboard Template" name="description" />
    <meta content="Themesbrand" name="author" />
    <!-- App favicon -->
    <link rel="shortcut icon" href="assets/images/favicon.ico">

    <!-- Bootstrap Css -->
    <link href="assets/css/bootstrap.min.css" id="bootstrap-style" rel="stylesheet" type="text/css" />
    <!-- Icons Css -->
    <link href="assets/css/icons.min.css" rel="stylesheet" type="text/css" />
    <!-- App Css-->
    <link href="assets/css/app.min.css" id="app-style" rel="stylesheet" type="text/css" />

</head>

<body>
    <div class="home-btn d-none d-sm-block">
        <a href="index.html" class="text-dark"><i class="fas fa-home h2"></i></a>
    </div>
    <div class="account-pages my-5 pt-sm-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-8 col-lg-6 col-xl-8">
                    <div class="card overflow-hidden">
                        <div class="bg-soft-primary">
                            <div class="row">
                                <div class="col-12">
                                    <div class="text-primary p-4">
                                        <h5 class="text-primary">Welcome to RFP System!</h5>
                                        <p>Regsiter as Vendor</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body pt-0">
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
                            <div class="p-4">
                                <form id="signupVendorForm" class="form-horizontal" action="" method="post">
                                    <div class="row">
                                        <div class="col-md-12 col-lg-6 col-xl-6">
                                            <div class="form-group">
                                                <label for="firstname">First name*</label>
                                                <input type="text" class="form-control" id="firstname" placeholder="Enter Firstname" name="firstName" value="<?php if (!empty($firstName)) echo $firstName; ?>">
                                                <div>
                                                    <font color="#f00000" size="2px" id="firstNameError"><?php if (isset($errors[0]) && !empty($errors[0])) echo $errors[0]; ?></font>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-12 col-lg-6 col-xl-6">
                                            <div class="form-group">
                                                <label for="lastname">Last Name<em>*</em></label>
                                                <input type="text" class="form-control" id="lastname" placeholder="Enter Lastname" name="lastName" value="<?php if (!empty($lastName)) echo $lastName; ?>">
                                                <div>
                                                    <font color="#f00000" size="2px" id="lastNameError"><?php if (isset($errors[1]) && !empty($errors[1])) echo $errors[1]; ?></font>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
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
                                                <label for="password">Password*</label>
                                                <input type="password" class="form-control" id="password" placeholder="Enter Password" name="password" value="<?php if (!empty($password)) echo $password; ?>">
                                                <div>
                                                    <font color="#f00000" size="2px" id="passwordError"><?php if (isset($errors[3]) && !empty($errors[3])) echo $errors[3]; ?></font>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-12 col-lg-6 col-xl-6">
                                            <div class="form-group">
                                                <label for="confirmPassword">Confirm Password*</label>
                                                <input type="password" class="form-control" id="confirmpassword" placeholder="Enter Confirm Password" name="confirmPassword" value="<?php if (!empty($confirmPassword)) echo $confirmPassword; ?>">
                                                <div>
                                                    <font color="#f00000" size="2px" id="confirmPasswordError"><?php if (isset($errors[4]) && !empty($errors[4])) echo $errors[4]; ?></font>
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

                                        <div class="p-2 mt-3">
                                            <button class="btn btn-primary btn-block waves-effect waves-light" type="submit" name="signupVendorSubmit">Register</button>
                                        </div>
                                    </div>
                                    <div class="mt-4 text-center">
                                        Already Registered?
                                        <a href="index.php" class="text-primary">Login</a>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="mt-5 text-center">
                        <div>
                            <p>&copy; Copyright <i class="mdi mdi-heart text-danger"></i> RFP System</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
<?php
// include the script
include("includes/script.php");
?>

</html>