<!doctype html>
<html lang="en">

<head>

    <meta charset="utf-8" />
    <title>Sign up | RFP System</title>
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
                <div class="col-md-8 col-lg-6 col-xl-5">
                    <div class="card overflow-hidden">
                        <div class="bg-soft-primary">
                            <div class="row">
                                <div class="col-12">
                                    <div class="text-primary p-4">
                                        <h5 class="text-primary">Welcome to RFP System!</h5>
                                        <p>Sign up to continue</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body pt-0">
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
                            if (count($formSuccess) > 0) {
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
                            <div class="p-2">
                                <form id="signupForm" class="form-horizontal" method="post" action="register-admin.php">
                                    <!-- first name  -->
                                    <div class="form-group">
                                        <label for="firstname">First name*</label>
                                        <input type="text" class="form-control" id="firstname" placeholder="Enter Firstname" name="firstName" value="<?php if (!empty($firstName)) echo $firstName; ?>">
                                        <div>
                                            <font color="#f00000" size="2px" id="firstNameError"><?php if (isset($errors[0]) && !empty($errors[0])) echo $errors[0]; ?></font>
                                        </div>
                                    </div>
                                    <!-- last name  -->
                                    <div class="form-group">
                                        <label for="lastname">Last Name<em>*</em></label>
                                        <input type="text" class="form-control" id="lastname" placeholder="Enter Lastname" name="lastName" value="<?php if (!empty($lastName)) echo $lastName; ?>">
                                        <div>
                                            <font color="#f00000" size="2px" id="lastNameError"><?php if (isset($errors[1]) && !empty($errors[1])) echo $errors[1]; ?></font>
                                        </div>
                                    </div>
                                    <!-- email  -->
                                    <div class="form-group">
                                        <label for="username">Email</label>
                                        <input type="text" class="form-control" id="email" placeholder="Enter Email" name="email" value="<?php if (!empty($email)) echo $email; ?>">
                                        <div>
                                            <font color="#f00000" size="2px" id="emailError"><?php if (isset($errors[2]) && !empty($errors[2])) echo $errors[2]; ?></font>
                                        </div>
                                    </div>
                                    <!-- password  -->
                                    <div class="form-group">
                                        <label for="userpassword">Password</label>
                                        <input type="password" class="form-control" id="userpassword" placeholder="Enter password" name="password" value="<?php if (!empty($password)) echo $password; ?>">
                                        <div>
                                            <font color="#f00000" size="2px" id="passwordError"><?php if (isset($errors[3]) && !empty($errors[3])) echo $errors[3]; ?></font>
                                        </div>
                                    </div>
                                    <!-- confirm password  -->
                                    <div class="form-group">
                                        <label for="password">Confirm Password*</label>
                                        <input type="password" class="form-control" id="confirmpassword" placeholder="Enter Confirm Password" name="confirmPassword" value="<?php if (!empty($confirmPassword)) echo $confirmPassword; ?>">
                                        <div>
                                            <font color="#f00000" size="2px" id="confirmPasswordError"><?php if (isset($errors[4]) && !empty($errors[4])) echo $errors[4]; ?></font>
                                        </div>
                                    </div>
                                    <!-- sign up submit  -->
                                    <div class="mt-3">
                                        <button class="btn btn-primary btn-block waves-effect waves-light" type="submit" name="signupSubmit">Sign Up</button>
                                    </div>

                                    <div class="mt-4 text-center">
                                        <a href="register.php" class="text-muted"><i class="mdi mdi-lock mr-1"></i> Register as Vendor</a>
                                    </div>
                                    <div class="mt-4 text-center">
                                        <a href="index.php" class="text-muted"><i class="mdi mdi-lock mr-1"></i> Already Registered?</a>
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