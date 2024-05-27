<?php
require_once("includes/configuration.php");
require_once 'vendor/autoload.php';

// load env variables
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

// check if user is already logged in.
if (!empty($_SESSION['email']) && !empty($_SESSION['type']) && strtolower($_SESSION['type']) == "admin") {
    header("Location: dashboard-admin.php");
} else if (!empty($_SESSION['email']) && !empty($_SESSION['type']) && strtolower($_SESSION['type']) == "vendor") {
    header("Location: dashboard-vendor.php");
}

// check if reset password success is set
if (!empty($_SESSION['reset-password-success'])) {
    $formSuccess[] = $_SESSION['reset-password-success'];
    unset($_SESSION['reset-password-success']);
}

// check if google login errror is set. It will be set if any error occurs while login with google.
if (!empty($_SESSION['google-login-error'])) {
    $formErrors = $_SESSION['google-login-error'];
    unset($_SESSION['google-login-error']);
}

// check if google login success is set. It will be set if new vendor try to sign up with google.
if (!empty($_SESSION['google-login-success'])) {
    $formSuccess = $_SESSION['google-login-success'];
    unset($_SESSION['google-login-success']);
}

// set google credentials
$client = new Google_Client();
$client->setClientId($_ENV["CLIENT_ID"]);
$client->setClientSecret($_ENV["CLIENT_SECRET"]);
$redirectURL = $_ENV["GLOBAL_APPLICATION"] . "login.php";

$client->setRedirectUri($redirectURL);
$client->addScope("email");
$client->addScope("profile");

?>
<!doctype html>
<html lang="en">

<head>

    <meta charset="utf-8" />
    <title>Login | RFP System</title>
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
                                        <p>Sign in to continue</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body pt-0">
                            <div class="error_container" id="formErrors" style="color: red;">
                            </div>
                            <div class="p-2">
                                <!-- display form error message  -->
                                <?php
                                if (!empty($formErrors) && count($formErrors) > 0) {
                                ?>
                                    <div class="error_container" style="color: red;">
                                        <?php
                                        // Form Errors Display.				
                                        $numError = count($formErrors);
                                        for ($i = 0; $i < $numError; $i++) {
                                            echo $formErrors[$i] . "<br>";
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
                                <form class="form-horizontal" id="signinForm" action="">

                                    <div class="form-group">
                                        <label for="username">Email</label>
                                        <input type="text" class="form-control" id="email" placeholder="Enter Email" name="email" value="<?php if (!empty($email)) echo $email; ?>">
                                        <div>
                                            <font color="#f00000" size="2px" id="emailError"><?php if (isset($errors[0]) && !empty($errors[0])) echo $errors[0]; ?></font>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="userpassword">Password</label>
                                        <input type="password" class="form-control" id="userpassword" placeholder="Enter password" name="password" value="<?php if (!empty($password)) echo $password; ?>">
                                        <div>
                                            <font color="#f00000" size="2px" id="passwordError"><?php if (isset($errors[1]) && !empty($errors[1])) echo $errors[1]; ?></font>
                                        </div>
                                    </div>

                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" id="customControlInline">
                                        <label class="custom-control-label" for="customControlInline">Remember me</label>
                                    </div>

                                    <div class="mt-3">
                                        <button class="btn btn-primary btn-block waves-effect waves-light" type="submit" name="signinSubmit">Log In</button>
                                    </div>


                                    <div class="mt-4 text-center">
                                        <h5 class="font-size-14 mb-3">Sign in with</h5>

                                        <ul class="list-inline">
                                            <li class="list-inline-item">
                                                <a href="<?php echo $client->createAuthUrl() ?>" class="social-list-item bg-danger text-white border-danger">
                                                    <i class="mdi mdi-google"></i>
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                    <div class="mt-4 text-center">
                                        <a href="register.php" class="text-muted"><i class="mdi mdi-lock mr-1"></i> Register as Vendor</a>
                                    </div>
                                    <div class="mt-4 text-center">
                                        <a href="forgot-password.php" class="text-muted"><i class="mdi mdi-lock mr-1"></i> Forgot your password?</a>
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