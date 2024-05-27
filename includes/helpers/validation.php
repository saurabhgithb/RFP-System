<?php

/*BOC: 
    FUNCTION NAME:- checkUniqueEmail()
    PURPOSE:- This function is for checking unique email from db
    PARAMETER:- 
    1. $email: email to check for uniqueness
    
    RETURN VALUES- 
    1. true: if email is not unique
    2. false: if email is unique
    3. ['db-error'=>$e->getMessage()]: an array with key db-error and value error message
    --------------------
    NAME : SAURABH SINGH
    DATE : 22/May/2024
    --------------------
*/
function checkUniqueEmail($email, $current_user_id = null)
{
    global $db;

    // query for counting how many email exist with given email
    /*RAW QUERY
        SELECT
            COUNT(*) AS email_count
        FROM
            `user_details`
        WHERE
            email = 'saurabh.singh@velsof.com';
    */
    $unique_email = "SELECT COUNT(*) AS email_count FROM `user_details` WHERE email = '$email';";

    if ($current_user_id !== null) {
        // query for counting how many email exist with given email excluding current user details
        /*RAW QUERY
            SELECT
                COUNT(*) AS email_count
            FROM
                `user_details`
            WHERE
                email = 'saurabh.singh@velsof.com' AND user_id != 1
        */
        $unique_email = "SELECT COUNT(*) AS email_count FROM `user_details` WHERE email = '$email' and user_id != " . (int)$current_user_id;
    }

    try {
        $unique_email_result = mysqli_query($db, $unique_email);
        if ($unique_email_result === false) {
            throw new Exception("MySQL error: " . mysqli_error($db));
        } else {
            $row = mysqli_fetch_array($unique_email_result);
            if ($row['email_count'] > 0) { // check if any user found with given email
                return false; // false this is not a unique email
            }
            return true; // true this is a unique email
        }
    } catch (Exception $e) {
        // echo $e->getMessage();
        return ['db-error' => $e->getMessage()];
    }
}
