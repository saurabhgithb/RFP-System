<?php

/*BOC: 
FUNCTION NAME:- error_msg()
PURPOSE:- This function is used to return the error messages of an error code from errors table in DB.
PARAMETER:- 
$errorCode:- int
RETURN VALUES- 
1. $error_message:- returns error message corresponding to the error code
--------------------
NAME : SAURABH SINGH
DATE : 01/May/2024
--------------------
*/
function error_msg($errorCode)
{
	global $db;
	$response = array(
		'status' => false,
		'message' => ''
	);

	$errorCode = mysqli_real_escape_string($db, $errorCode);
	$errorCode = trim($errorCode);

	// check if error code is null or empty
	if (!empty($errorCode)) {
		/*	RAW QUERY
		SELECT
			error_id,
			error_code,
			error_message
		FROM
			`errors`
		WHERE
			`error_code` = 101
		*/

		$error_query = "SELECT error_id, error_code, error_message FROM `errors` WHERE `error_code` = " . (int)$errorCode;

		try {
			$error_query_result = mysqli_query($db, $error_query);
			if ($error_query_result === FALSE) {
				throw new Exception("MySQL error: " . mysqli_error($db));
			} else {
				// extract data from the fetched result
				$row = mysqli_fetch_array($error_query_result);
				if (mysqli_num_rows($error_query_result) > 0) {
					// error code found in db.
					$response['message'] = $row['error_message'];
					$response['status'] = true;
				} else {
					// error code not found in db
					$response['message'] = "Unknown Error: Error code not found.";
					$response['status'] = false;
				}
			}
		} catch (Exception $e) {
			$response['message'] = $e->getMessage();
			$response['status'] = false;
		}
	} else {
		$response['message'] = "Unknown Error: Error code not specified.";
		$response['status'] = false;
	}
	return $response;
}

?>