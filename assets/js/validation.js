// This function checks if the given email is valid or not.
function validateEmail(email) {
  var regex =
    /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
  return regex.test(email);
}

$(document).ready(function () {
  // Perform validation for sign up form.
  $("#signupForm").submit(function (e) {
    // Prevent default form submission
    e.preventDefault();

    // Reset error messages
    $("#firstNameError").text("");
    $("#lastNameError").text("");
    $("#emailError").text("");
    $("#passwordError").text("");
    $("#confirmPasswordError").text("");

    // Extract input values
    const firstName = $("#firstname").val().trim();
    const lastName = $("#lastname").val().trim();
    const email = $("#email").val().trim();
    const password = $("#userpassword").val().trim();
    const confirmPassword = $("#confirmpassword").val().trim();

    let isValid = true;

    // Check each field and display error messages if validation fails
    if (firstName === "") {
      $("#firstNameError").text("First name is required.");
      isValid = false;
    }
    if (lastName === "") {
      $("#lastNameError").text("Last name is required.");
      isValid = false;
    }
    if (email === "") {
      $("#emailError").text("Email is required.");
      isValid = false;
    } else if (!validateEmail(email)) {
      $("#emailError").text("Invalid email address.");
      isValid = false;
    }
    if (password === "") {
      $("#passwordError").text("Password is required.");
      isValid = false;
    } else if (password.length < 6) {
      $("#passwordError").text("Password must be at least 6 characters.");
      isValid = false;
    }
    if (confirmPassword === "") {
      $("#confirmPasswordError").text("Confirm password is required.");
      isValid = false;
    } else if (password !== confirmPassword) {
      $("#confirmPasswordError").text("Passwords do not match.");
      isValid = false;
    }

    // Submit the form if all validation checks pass
    if (isValid) {
      this.submit();
    }
  });

  // Perform validation for sign in form.
  $("#signinForm").submit(function (e) {
    // Prevent default form submission
    e.preventDefault();

    // Reset error messages
    $("#emailError").text("");
    $("#passwordError").text("");
    $(".error_container").text("");

    // Extract input values
    const email = $("#email").val().trim();
    const password = $("#userpassword").val().trim();

    let isValid = true;

    // Check each field and display error messages if validation fails
    if (email === "") {
      $("#emailError").text("Email is required.");
      isValid = false;
    } else if (!validateEmail(email)) {
      $("#emailError").text("Invalid email address.");
      isValid = false;
    }
    if (password === "") {
      $("#passwordError").text("Password is required.");
      isValid = false;
    } else if (password.length < 6) {
      $("#passwordError").text("Password must be at least 6 characters.");
      isValid = false;
    }

    // Submit the form if all validation checks pass
    if (isValid) {
      $.ajax({
        url: "login.php",
        method: "POST",
        dataType: "json",
        data: {
          email: email,
          password: password,
        },
        success: function (data) {
          console.log(data);
          if (data.type == "success") {
            //Redirect
            // console.log("login successful");
            window.location.href = data.redirectUrl;
          } else if (data.type == "failed") {
            $("#form_error").html(data.message);
            if (data.validationError) {
              if (data.validationError.emailError) {
                $("#emailError").html(data.validationError.emailError);
              }
              if (data.validationError.passwordError) {
                $("#passwordError").html(data.validationError.passwordError);
              }
            }
            if (data.formErrors) {
              $("#formErrors").empty();
              data.formErrors.forEach((error) => {
                $("#formErrors").append(`<div>${error}</div>`);
              });
            }
          }
        },
      });
    }
  });

  // Perform validation for vendor sign up form.
  $("#signupVendorForm").submit(function (e) {
    // Prevent default form submission
    e.preventDefault();

    // Reset error messages
    $("#firstNameError").text("");
    $("#lastNameError").text("");
    $("#emailError").text("");
    $("#passwordError").text("");
    $("#confirmPasswordError").text("");
    $("#revenueError").text("");
    $("#noOfEmployeesError").text("");
    $("#gstNoError").text("");
    $("#panNoError").text("");
    $("#phoneNoError").text("");
    $("#categoriesError").text("");

    // Extract input values
    const firstName = $("#firstname").val().trim();
    const lastName = $("#lastname").val().trim();
    const email = $("#email").val().trim();
    const password = $("#password").val().trim();
    const confirmPassword = $("#confirmpassword").val().trim();
    const revenue = $("#revenue").val().trim();
    const noofemployees = $("#noofemployees").val().trim();
    const gstno = $("#gstno").val().trim();
    const panno = $("#panno").val().trim();
    const phoneno = $("#phoneno").val().trim();
    const categories = $("#categories").val();
    console.log(categories);

    let isValid = true;

    // Check each field and display error messages if validation fails
    if (firstName === "") {
      $("#firstNameError").text("First name is required.");
      isValid = false;
    }
    if (lastName === "") {
      $("#lastNameError").text("Last name is required.");
      isValid = false;
    }
    if (email === "") {
      $("#emailError").text("Email is required.");
      isValid = false;
    } else if (!validateEmail(email)) {
      $("#emailError").text("Invalid email address.");
      isValid = false;
    }
    if (password === "") {
      $("#passwordError").text("Password is required.");
      isValid = false;
    } else if (password.length < 6) {
      $("#passwordError").text("Password must be at least 6 characters.");
      isValid = false;
    }
    if (confirmPassword === "") {
      $("#confirmPasswordError").text("Confirm password is required.");
      isValid = false;
    } else if (password !== confirmPassword) {
      $("#confirmPasswordError").text("Passwords do not match.");
      isValid = false;
    }
    if (revenue === "") {
      $("#revenueError").text("Revenue is required.");
      isValid = false;
    } else if (isNaN(revenue)) {
      $("#revenueError").text("Revenue must be a number.");
      isValid = false;
    }
    if (!noofemployees) {
      $("#noOfEmployeesError").text("Number of employees is required.");
      isValid = false;
    } else if (isNaN(noofemployees)) {
      $("#noOfEmployeesError").text("Number of employees must be a number.");
      isValid = false;
    } else if (Number.isInteger(noofemployees) || noofemployees < 0) {
      $("#noOfEmployeesError").text(
        "Number of employees must be a non-negative whole number."
      );
      isValid = false;
    }
    if (!gstno) {
      $("#gstNoError").text("GST Number is required.");
      isValid = false;
    } else if (
      !/^[0-9]{2}[A-Z]{5}[0-9]{4}[A-Z]{1}[1-9A-Z]{1}Z[0-9A-Z]{1}$/.test(gstno)
    ) {
      $("#gstNoError").text("Invalid GST number format.");
      isValid = false;
    }
    if (!panno) {
      $("#panNoError").text("PAN Number is required.");
      isValid = false;
    } else if (!/[A-Z]{5}[0-9]{4}[A-Z]{1}/.test(panno)) {
      $("#panNoError").text("Invalid PAN number format.");
      isValid = false;
    }
    if (!phoneno) {
      $("#phoneNoError").text("Phone Number is required.");
      isValid = false;
    } else if (
      !/^[\+]?[(]?[0-9]{3}[)]?[-\s\.]?[0-9]{3}[-\s\.]?[0-9]{4,6}$/.test(phoneno)
    ) {
      $("#phoneNoError").text("Invalid Phone number format.");
      isValid = false;
    }
    if (categories.length == 0) {
      $("#categoriesError").text("Categories are required.");
      isValid = false;
    }

    // Submit the form if all validation checks pass
    if (isValid) {
      this.submit();
    }
  });

  // Perform validation for forgotPassword form.
  $("#forgotPasswordForm").submit(function (e) {
    // Prevent default form submission
    e.preventDefault();

    // Reset error messages
    $("#emailError").text("");

    // Extract input values
    const email = $("#email").val().trim();

    let isValid = true;

    // Check each field and display error messages if validation fails
    if (email === "") {
      $("#emailError").text("Email is required.");
      isValid = false;
    } else if (!validateEmail(email)) {
      $("#emailError").text("Invalid email address.");
      isValid = false;
    }

    // Submit the form if all validation checks pass
    if (isValid) {
      this.submit();
    }
  });

  // Perform validation for add Category form.
  $("#addCategoryForm").submit(function (e) {
    // Prevent default form submission
    e.preventDefault();

    // Reset error messages
    $("#categoryNameError").text("");
    $("#statusError").text("");

    // Extract input values
    const categoryName = $("#categoryName").val().trim();
    const status = $("#status").val();

    let isValid = true;

    // Check each field and display error messages if validation fails
    if (categoryName === "") {
      $("#categoryNameError").text("Category Name is required.");
      isValid = false;
    }

    if (status === "") {
      $("#statusError").text("Status is required.");
      isValid = false;
    }

    // Submit the form if all validation checks pass
    if (isValid) {
      this.submit();
    }
  });

  // Perform validation for select rfp Category form.
  $("#selectRfpCategoryForm").submit(function (e) {
    // Prevent default form submission
    e.preventDefault();

    // Reset error messages
    $("#categoriesError").text("");

    // Extract input values
    const categories = $("#categories").val().trim();

    let isValid = true;

    // Check each field and display error messages if validation fails
    if (categories === "") {
      $("#categoriesError").text("Category is required.");
      isValid = false;
    }

    // Submit the form if all validation checks pass
    if (isValid) {
      this.submit();
    }
  });

  // Perform validation for add rfp form.
  $("#addRFPForm").submit(function (e) {
    // Prevent default form submission
    e.preventDefault();

    // Reset error messages
    $("#itemNameError").text("");
    $("#itemDescriptionError").text("");
    $("#quantityError").text("");
    $("#lastDateError").text("");
    $("#minimumPriceError").text("");
    $("#vendorsError").text("");

    // Extract input values
    const itemName = $("#itemname").val().trim();
    const itemDescription = $("#itemdescription").val().trim();
    const quantity = $("#quantity").val().trim();
    const lastDate = $("#lastdate").val().trim();
    const minimumPrice = $("#minimumprice").val().trim();
    const maximumPrice = $("#maximumprice").val().trim();
    const vendors = $("#vendors").val();

    let isValid = true;

    // Check each field and display error messages if validation fails
    // Validate item name
    if (itemName === "") {
      $("#itemNameError").text("Item name is required.");
      isValid = false;
    }

    // Validate item description
    if (itemDescription === "") {
      $("#itemDescriptionError").text("Item description is required.");
      isValid = false;
    }

    // Validate quantity
    if (quantity === "") {
      $("#quantityError").text("Quantity is required.");
      isValid = false;
    } else if (
      isNaN(quantity) ||
      quantity <= 0 ||
      !Number.isInteger(parseFloat(quantity))
    ) {
      $("#quantityError").text("Quantity must be a positive integer.");
      isValid = false;
    }

    // Validate last date
    if (lastDate === "") {
      $("#lastDateError").text("Last date is required.");
      isValid = false;
    } else if (new Date(lastDate) < new Date()) {
      $("#lastDateError").text("Last date must be in the future.");
      isValid = false;
    }

    // Validate minimum price
    if (minimumPrice === "") {
      $("#minimumPriceError").text("Minimum price is required.");
      isValid = false;
    } else if (isNaN(minimumPrice) || minimumPrice <= 0) {
      $("#minimumPriceError").text("Minimum price must be a positive number.");
      isValid = false;
    }

    // Validate maximum price
    if (maximumPrice === "") {
      $("#maximumPriceError").text("Maximum price is required.");
      isValid = false;
    } else if (isNaN(maximumPrice) || maximumPrice <= 0) {
      $("#maximumPriceError").text("Maximum price must be a positive number.");
      isValid = false;
    }

    // Validate vendors
    if (vendors === null || vendors.length === 0) {
      $("#vendorsError").text("At least one vendor must be selected.");
      isValid = false;
    }

    // Submit the form if all validation checks pass
    if (isValid) {
      this.submit();
    }
  });

  // Perform validation for create quote form.
  $("#createQuoteForm").submit(function (e) {
    // Prevent default form submission
    e.preventDefault();

    // Reset error messages
    $("#vendorPriceError").text("");
    $("#itemDescriptionError").text("");
    $("#quantityError").text("");
    $("#totalCostError").text("");

    // Extract input values
    const vendorPrice = $("#vendorprice").val().trim();
    const itemDescription = $("#itemdescription").val().trim();
    const quantity = $("#quantity").val().trim();
    const totalcost = $("#totalcost").val().trim();

    let isValid = true;

    // Check each field and display error messages if validation fails
    // Validate vendor price
    if (vendorPrice === "") {
      $("#vendorPriceError").text("Vendor price is required.");
      isValid = false;
    } else if (isNaN(vendorPrice) || vendorPrice <= 0) {
      $("#vendorPriceError").text("Vendor price must be a positive number.");
      isValid = false;
    }

    // Validate item description
    if (itemDescription === "") {
      $("#itemDescriptionError").text("Item description is required.");
      isValid = false;
    }

    // Validate quantity
    if (quantity === "") {
      $("#quantityError").text("Quantity is required.");
      isValid = false;
    } else if (
      isNaN(quantity) ||
      quantity <= 0 ||
      !Number.isInteger(parseFloat(quantity))
    ) {
      $("#quantityError").text("Quantity must be a positive integer.");
      isValid = false;
    }

    // Validate total cost
    if (totalCost === "") {
      $("#totalCostError").text("Total cost is required.");
      isValid = false;
    } else if (isNaN(totalCost) || totalCost <= 0) {
      $("#totalCostError").text("Total cost must be a positive number.");
      isValid = false;
    }

    // Submit the form if all validation checks pass
    if (isValid) {
      this.submit();
    }
  });

  // Perform validation for edit vendor form.
  $("#editVendorForm").submit(function (e) {
    // Prevent default form submission
    e.preventDefault();

    // Reset error messages
    $("#firstNameError").text("");
    $("#lastNameError").text("");
    $("#emailError").text("");
    $("#revenueError").text("");
    $("#noOfEmployeesError").text("");
    $("#gstNoError").text("");
    $("#panNoError").text("");
    $("#phoneNoError").text("");
    $("#categoriesError").text("");

    // Extract input values
    const firstName = $("#firstname").val().trim();
    const lastName = $("#lastname").val().trim();
    const email = $("#email").val().trim();
    const revenue = $("#revenue").val().trim();
    const noofemployees = $("#noofemployees").val().trim();
    const gstno = $("#gstno").val().trim();
    const panno = $("#panno").val().trim();
    const phoneno = $("#phoneno").val().trim();
    const categories = $("#categories").val();

    let isValid = true;

    // Check each field and display error messages if validation fails
    if (firstName === "") {
      $("#firstNameError").text("First name is required.");
      isValid = false;
    }
    if (lastName === "") {
      $("#lastNameError").text("Last name is required.");
      isValid = false;
    }
    if (email === "") {
      $("#emailError").text("Email is required.");
      isValid = false;
    } else if (!validateEmail(email)) {
      $("#emailError").text("Invalid email address.");
      isValid = false;
    }

    if (revenue === "") {
      $("#revenueError").text("Revenue is required.");
      isValid = false;
    } else if (isNaN(revenue)) {
      $("#revenueError").text("Revenue must be a number.");
      isValid = false;
    }
    if (!noofemployees) {
      $("#noOfEmployeesError").text("Number of employees is required.");
      isValid = false;
    } else if (isNaN(noofemployees)) {
      $("#noOfEmployeesError").text("Number of employees must be a number.");
      isValid = false;
    } else if (Number.isInteger(noofemployees) || noofemployees < 0) {
      $("#noOfEmployeesError").text(
        "Number of employees must be a non-negative whole number."
      );
      isValid = false;
    }
    if (!gstno) {
      $("#gstNoError").text("GST Number is required.");
      isValid = false;
    } else if (
      !/^[0-9]{2}[A-Z]{5}[0-9]{4}[A-Z]{1}[1-9A-Z]{1}Z[0-9A-Z]{1}$/.test(gstno)
    ) {
      $("#gstNoError").text("Invalid GST number format.");
      isValid = false;
    }
    if (!panno) {
      $("#panNoError").text("PAN Number is required.");
      isValid = false;
    } else if (!/[A-Z]{5}[0-9]{4}[A-Z]{1}/.test(panno)) {
      $("#panNoError").text("Invalid PAN number format.");
      isValid = false;
    }
    if (!phoneno) {
      $("#phoneNoError").text("Phone Number is required.");
      isValid = false;
    } else if (
      !/^[\+]?[(]?[0-9]{3}[)]?[-\s\.]?[0-9]{3}[-\s\.]?[0-9]{4,6}$/.test(phoneno)
    ) {
      $("#phoneNoError").text("Invalid Phone number format.");
      isValid = false;
    }
    if (categories.length == 0) {
      $("#categoriesError").text("Categories are required.");
      isValid = false;
    }

    // Submit the form if all validation checks pass
    if (isValid) {
      this.submit();
    }
  });
});
