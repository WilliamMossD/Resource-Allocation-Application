<?php 

/*
 * getUserList.php file for the Individual Project (University of Sussex 2023) 
 * Author: William Moss (235319)
 *
 * Supports GET requests and returns a data list of users
 */

    require_once('../src/inc/utilities.php');

    session_start();

    // Connect to database
    try {
        $con = mysqliConnect();
        if ($con->connect_error) {
            returnHTTPResponse(500, 'Database Connection Failed');
            exit();
        } 
    } catch (Exception $e) {
        returnHTTPResponse(500, 'Database Connection Failed');
        exit();
    }

    // Only accepts GET requests
    if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
        returnHTTPResponse(400, 'HTTP Status 400: POST Requests not supported');
        exit();
    }

    // Verify user is logged in 
    if (!isset($_SESSION['loggedin']) or !$_SESSION['loggedin'] or !isset($_SESSION['email'])) {
        // User is not logged in. Send bad request
        returnHTTPResponse(401, 'HTTP Status 401: You are not permitted to access this resource!');
        session_destroy();
        exit();
    }

    // Verify user is admin (Only admins can submit these forms)
    if (!isUserAdmin(getUserIDByEmail($_SESSION['email'], $con), $con)) {
        // User is not logged in. Send bad request
        returnHTTPResponse(401, 'HTTP Status 401: You are not permitted to access this resource!');
        session_destroy();
        exit();
    }

    try {

        returnHTTPResponse(200, generateUserList(getAllUsers($con)));  
        exit();

    } catch (Exception $e) {
        exit();
    }

 ?>