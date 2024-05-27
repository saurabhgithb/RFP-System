
<?php

/*BOC: 
    FUNCTION NAME:- fetchAllCategories()
    PURPOSE:- This function is for fetching categories names from db.
    PARAMETER:- none
    
    RETURN VALUES- 
    1. $categories: an array consisting of country names and ids
    2. ['db-error'=>$e->getMessage()]: an array with key db-error and value error message
    --------------------
    NAME : SAURABH SINGH
    DATE : 22/May/2024
    --------------------
*/
function fetchAllCategories()
{
    global $db;
    $fetch_categories = "SELECT `category_id`, `category_name` FROM `categories` WHERE `status` = 'Active' ORDER BY `category_name` ASC";
    try {
        $fetch_categories_result = mysqli_query($db, $fetch_categories);
        if ($fetch_categories_result === false) {
            throw new Exception("MySQL error while fetching countries: " . mysqli_error($db));
        } else {
            $categories = [];
            while ($category = mysqli_fetch_array($fetch_categories_result)) {
                $categories[] = ['category_id' => $category['category_id'], 'category_name' => $category['category_name']];
            }
            return $categories;
        }
    } catch (Exception $e) {
        // echo $e->getMessage();
        return ['db-error' => $e->getMessage()];
    }
}

/*BOC: 
    FUNCTION NAME:- fetchCategoryVendors()
    PURPOSE:- This function is for fetching vendors enrolled in a particular category.
    PARAMETER:- none
    
    RETURN VALUES- 
    1. ['status'=> true, 'vendors-data'=> $vendors]: an array consisting of vendor names and ids and status
    2. ['status'=> false, 'error-msg'=> $e->getMessage()]: an array with key db-error and value error message and status
    --------------------
    NAME : SAURABH SINGH
    DATE : 22/May/2024
    --------------------
*/
function fetchCategoryVendors($categoryId)
{
    global $db;
    $categoryId = trim($categoryId);
    // validate category_id

    // Check if the category ID is not empty
    if (empty($categoryId)) {
        return ['status' => false, 'error-msg' => "Category Id not found."]; // Category ID cannot be empty
    }

    // Check if the category ID is an integer
    if (!is_numeric($categoryId)) {
        return ['status' => false, 'error-msg' => "Category Id must be a number."]; // Category ID must be a number
    }

    $categoryId = mysqli_real_escape_string($db, $categoryId);
    /*RAW QUERY
        SELECT
            vc.`category_id`,
            vc.`vendor_id`,
            vd.user_id,
            ud.first_name,
            ud.last_name
        FROM
            `vendor_category` vc
        JOIN `vendor_details` vd ON
            vc.vendor_id = vd.vendor_id
        JOIN `user_details` ud ON
            ud.user_id = vd.user_id
        WHERE
            vc.category_id = 1
        ORDER BY
            ud.first_name ASC
     */
    $fetch_vendor_categories_query = "SELECT vc.`category_id`, vc.`vendor_id`, vd.user_id, ud.first_name, ud.last_name FROM `vendor_category` vc JOIN `vendor_details` vd on vc.vendor_id = vd.vendor_id JOIN `user_details` ud on ud.user_id = vd.user_id WHERE vc.category_id = '$categoryId' ORDER BY ud.first_name ASC;";
    try {
        $fetch_vendor_categories_result = mysqli_query($db, $fetch_vendor_categories_query);
        if ($fetch_vendor_categories_result === false) {
            throw new Exception("MySQL error while fetching vendors: " . mysqli_error($db));
        } else {
            $vendors = [];
            while ($vendor = mysqli_fetch_array($fetch_vendor_categories_result)) {
                $vendors[] = ['vendor_id' => $vendor['vendor_id'], 'user_id' => $vendor['user_id'], 'first_name' => $vendor['first_name'], 'last_name' => $vendor['last_name']];
            }
            return ['status' => true, 'vendors-data' => $vendors];
        }
    } catch (Exception $e) {
        // echo $e->getMessage();
        return ['status' => false, 'error-msg' => $e->getMessage()];
    }
}

/*BOC: 
    FUNCTION NAME:- fetchVendorsCategory()
    PURPOSE:- This function is for fetching categories of the vendors in which vendor have registered.
    PARAMETER:- none
    
    RETURN VALUES- 
    1. ['status'=> true, 'categories-data'=> $]: an array consisting of vendor names and ids and status
    2. ['status'=> false, 'error-msg'=> $e->getMessage()]: an array with key db-error and value error message and status
    --------------------
    NAME : SAURABH SINGH
    DATE : 22/May/2024
    --------------------
*/
function fetchVendorsCategory($vendorId)
{
    global $db;
    $vendorId = trim($vendorId);
    // validate vendor_id

    // Check if the vendor ID is not empty
    if (empty($vendorId)) {
        return ['status' => false, 'error-msg' => "vendor Id not found."]; // Vendor ID cannot be empty
    }

    // Check if the vendor ID is an integer
    if (!is_numeric($vendorId)) {
        return ['status' => false, 'error-msg' => "vendor Id must be a number."]; // Vendor ID must be a number
    }

    $vendorId = mysqli_real_escape_string($db, $vendorId);
    /*RAW QUERY
        SELECT
            vc.`category_id`
        FROM
            `vendor_category` vc
        JOIN `vendor_details` vd ON
            vc.vendor_id = vd.vendor_id
        WHERE
            vc.vendor_id = 1;
     */
    $fetch_vendor_categories_query = "SELECT vc.`category_id` FROM `vendor_category` vc JOIN `vendor_details` vd ON vc.vendor_id = vd.vendor_id WHERE vc.vendor_id = " . (int)$vendorId;
    try {
        $fetch_vendor_categories_result = mysqli_query($db, $fetch_vendor_categories_query);
        if ($fetch_vendor_categories_result === false) {
            throw new Exception("MySQL error while fetching vendors: " . mysqli_error($db));
        } else {
            $vendorCategories = [];
            if (mysqli_num_rows($fetch_vendor_categories_result) > 0) {
                while ($vendorCategoryRow = mysqli_fetch_array($fetch_vendor_categories_result)) {
                    $vendorCategories[] = $vendorCategoryRow["category_id"];
                }
                return ['status' => true, 'categories-data' => $vendorCategories];
            } else {
                return ['status' => false, 'error-msg' => "No Categories found with given id."];
            }
        }
    } catch (Exception $e) {
        // echo $e->getMessage();
        return ['status' => false, 'error-msg' => $e->getMessage()];
    }
}

/*BOC: 
    FUNCTION NAME:- fetchVendorDetails()
    PURPOSE:- This function is for fetching vendors with a vendor Id.
    PARAMETER:- none
    
    RETURN VALUES- 
    1. ['status'=> true, 'vendor-data'=> $vendor]: an array consisting of vendor name and id and status
    2. ['status'=> false, 'error-msg'=> $e->getMessage()]: an array with key db-error and value error message and status
    --------------------
    NAME : SAURABH SINGH
    DATE : 22/May/2024
    --------------------
*/
function fetchVendorDetails($vendorId)
{
    global $db;
    $vendorId = trim($vendorId);
    // validate vendorId

    // Check if the vendor ID is not empty
    if (empty($vendorId)) {
        return ['status' => false, 'error-msg' => "Vendor ID cannot be empty."]; // Vendor ID cannot be empty
    }

    // Check if the vendor ID is an integer
    if (!is_numeric($vendorId)) {
        return ['status' => false, 'error-msg' => "Vendor Id must be a number."]; // Vendor ID must be a number
    }

    $vendorId = mysqli_real_escape_string($db, $vendorId);
    /*RAW QUERY
        SELECT
            vd.user_id,
            ud.first_name,
            ud.last_name
        FROM
            `vendor_details` vd
        JOIN `user_details` ud ON
            ud.user_id = vd.user_id
        WHERE
            vd.vendor_id = 1
     */
    $fetch_vendor_query = "SELECT vd.user_id, ud.first_name, ud.last_name, ud.email FROM `vendor_details` vd JOIN `user_details` ud ON ud.user_id = vd.user_id WHERE vd.vendor_id = '$vendorId';";
    try {
        $fetch_vendor_result = mysqli_query($db, $fetch_vendor_query);
        if ($fetch_vendor_result === false) {
            throw new Exception("MySQL error while fetching vendors: " . mysqli_error($db));
        } else {
            if (mysqli_num_rows($fetch_vendor_result) > 0) {
                $vendorRow = mysqli_fetch_array($fetch_vendor_result);
                return ['status' => true, 'vendor-data' => $vendorRow];
            } else {
                ['status' => false, 'error-msg' => "Vendor details not found."];
            }
        }
    } catch (Exception $e) {
        // echo $e->getMessage();
        return ['status' => false, 'error-msg' => $e->getMessage()];
    }
}

/*BOC: 
    FUNCTION NAME:- sendMail()
    PURPOSE:- This function is send email.
    PARAMETER:- 
    1. $to: receiver's email address
    2. $from: senders's email address
    3. $subject: subject of the email
    4. $body: body of the email
    
    RETURN VALUES- 
    1. ['send-status' => true]: an array with key send-status with value true if email is sent otherwise false.
    2. ['mail-error' => "error-message", send-status: false]: an array with key mail-error and value error message.
    --------------------
    NAME : SAURABH SINGH
    DATE : 22/May/2024
    --------------------
*/
function sendMail($to, $from, $subject, $body)
{
    require('includes/mailer.php'); // contains php mailer config
    $mail->setFrom($from);
    $mail->addAddress($to);
    $mail->Subject = $subject;
    $mail->Body = $body;

    try {
        $mail->send();
        return ['send-status' => true];
    } catch (Exception $e) {
        return ['mail-error' => $e->getMessage(), 'send-status' => false];
    }
}

/*BOC: 
    FUNCTION NAME:- emailExist($email)
    PURPOSE:- This function is to check if email exist or not
    PARAMETER:- 
    1. $email: email to check if it exist or not in system
    
    RETURN VALUES- 
    1. ['email_found' => true, 'user_data' => $user]: an array containing key email_found with true if email exist along with user data in key user_data
    2. ['email_found' => false]: an array containing key email_found with value false if email doesn't exist
    3. ['db-error' => $e->getMessage(), 'email_found' => false]: an array with key db-error and value error message and email_found false.
    --------------------
    NAME : SAURABH SINGH
    DATE : 22/May/2024
    --------------------
*/
function emailExist($email)
{
    global $db;

    /* RAW QUERY
            SELECT
                user_id,
                first_name,
                last_name,
                password,
                email,
                type,
                status
            FROM
                `user_details`
            WHERE
                email = 'rsingh@velsof.com';
         */
    $email_exist = "SELECT `user_id`, `email`, `first_name`, `last_name`, `password`, `type`, `status` FROM `user_details` WHERE email = '" . $email . "'";
    try {
        $email_exist_result = mysqli_query($db, $email_exist);
        if ($email_exist_result === FALSE) {
            throw new Exception("MySQL Error in emailExist function: " . mysqli_error($db));
        } else {
            if (mysqli_num_rows($email_exist_result) > 0) {
                // email found
                $user = mysqli_fetch_array($email_exist_result);
                return ['email_found' => true, 'user_data' => $user];
            } else {
                // email not found
                return ['email_found' => false];
            }
        }
    } catch (Exception $e) {
        return ['db-error' => $e->getMessage(), 'email_found' => false];
    }
}

/*BOC: 
    FUNCTION NAME:- quoteExist($rfpId)
    PURPOSE:- This function is to check if quote exist or not for a particular vendor and rfp
    PARAMETER:- 
    1. $vendorId: vendorId to check if it exist or not in quote table
    2. $rfpId: rfpId to check if it exist or not in quote table
    
    RETURN VALUES- 
    1. ['closed-status' => true]: closed status is true when rfp status is closed successfully.
    2. ['closed-status' => false]: closed status is false when rfp status is not closed successfully.
    3. ['error-msg' => $e->getMessage(), 'closed-status' => false]: an array with key error-msg and value error message and closed-status false.
    --------------------
    NAME : SAURABH SINGH
    DATE : 22/May/2024
    --------------------
*/
function closeRFP($rfpId)
{
    global $db;

    // Check if the rfp ID is not empty
    if (empty($rfpId)) {
        return ['closed-status' => false, 'error-msg' => "Rfp Id cannot be empty."]; // Category ID cannot be empty
    }

    // Check if the rfp ID is an integer
    if (!is_numeric($rfpId)) {
        return ['closed-status' => false, 'error-msg' => "Rfp Id must be a number."]; // Category ID must be a number
    }

    $rfpId = mysqli_real_escape_string($db, $rfpId);

    /*RAW QUERY
        UPDATE
            `rfp`
        SET
            `status` = 'Closed'
        WHERE
            `rfp`.`rfp_id` = 1;
    */
    $close_rfp_query = "UPDATE
                                                `rfp`
                                            SET
                                                `status` = 'Closed'
                                            WHERE
                                                `rfp`.`rfp_id` = " . $rfpId;

    try {
        $close_rfp_result = mysqli_query($db, $close_rfp_query);
        if ($close_rfp_result === FALSE) {
            throw new Exception("MySQL Error: " . mysqli_error($db));
        } else {
            // status closed successfully
            return ['closed-status' => true];
        }
    } catch (Exception $e) {
        return ['error-msg' => $e->getMessage(), 'closed-status' => false];
    }
}

/*BOC: 
    FUNCTION NAME:- quoteExist($rfpId)
    PURPOSE:- This function is to check if quote exist or not for a particular vendor and rfp
    PARAMETER:- 
    1. $vendorId: vendorId to check if it exist or not in quote table
    2. $rfpId: rfpId to check if it exist or not in quote table
    
    RETURN VALUES- 
    1. ['quote_found' => true, 'quote_data' => $user]: an array containing key quote_found with true if quote exist along with user data in key user_data
    2. ['quote_found' => false]: an array containing key email_found with value false if quote doesn't exist
    3. ['db-error' => $e->getMessage(), 'quote_found' => false]: an array with key db-error and value error message and email_found false.
    --------------------
    NAME : SAURABH SINGH
    DATE : 22/May/2024
    --------------------
*/
function quoteExist($rfpId, $vendorId)
{
    global $db;

    // Check if the vendor ID is not empty
    if (empty($vendorId)) {
        return ['status' => false, 'error-msg' => "Vendor Id cannot be empty."]; // Category ID cannot be empty
    }

    // Check if the vendor ID is an integer
    if (!is_numeric($vendorId)) {
        return ['status' => false, 'error-msg' => "Vendor Id must be a number."]; // Category ID must be a number
    }
    // Check if the rfp ID is not empty
    if (empty($rfpId)) {
        return ['status' => false, 'error-msg' => "Rfp Id cannot be empty."]; // Category ID cannot be empty
    }

    // Check if the rfp ID is an integer
    if (!is_numeric($rfpId)) {
        return ['status' => false, 'error-msg' => "Rfp Id must be a number."]; // Category ID must be a number
    }

    $vendorId = mysqli_real_escape_string($db, $vendorId);
    $rfpId = mysqli_real_escape_string($db, $rfpId);

    /* RAW QUERY
            SELECT
                quote_id,
                rfp_id,
                vendor_id
            FROM
                `quote`
            WHERE
                rfp_id = 2 AND vendor_id = 2;
         */
    $quote_exist_query = "SELECT
                        quote_id,
                        rfp_id,
                        vendor_id
                    FROM
                        `quote`
                    WHERE
                        rfp_id = $rfpId AND vendor_id = $vendorId";
    try {
        $quote_exist_result = mysqli_query($db, $quote_exist_query);
        if ($quote_exist_result === FALSE) {
            throw new Exception("MySQL Error in quoteExist function: " . mysqli_error($db));
        } else {
            if (mysqli_num_rows($quote_exist_result) > 0) {
                // email found
                $quote = mysqli_fetch_array($quote_exist_result);
                return ['quote_found' => true, 'quote_data' => $quote];
            } else {
                // email not found
                return ['quote_found' => false, 'error-msg' => "Quote not found."];
            }
        }
    } catch (Exception $e) {
        return ['db-error' => $e->getMessage(), 'quote_found' => false];
    }
}
