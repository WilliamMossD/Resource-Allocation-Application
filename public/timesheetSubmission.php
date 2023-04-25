<?php 

/*
 * timesheetSubmission.php file for the Individual Project (University of Sussex 2023) 
 * Author: William Moss (235319)
 *
 * Handles user timesheet submissions
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

    // formHandler only accepts POST requests
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        returnHTTPResponse(400, 'HTTP Status 400: GET Requests not supported');
        exit();
    }

    // Check origin request came from server domain name
    if(!isset($_SERVER['HTTP_ORIGIN']) or $_SERVER['HTTP_ORIGIN'] != getDomainName()) {
        returnHTTPResponse(400, 'HTTP Status 400: Invalid request origin!');
        exit();
    }

    // Verify user is logged in 
    if (!isset($_SESSION['loggedin']) or !$_SESSION['loggedin'] or !isset($_SESSION['email'])) {
        // User is not logged in. Send bad request
        returnHTTPResponse(401, 'HTTP Status 401: You are not permitted to access this resource!');
        session_destroy();
        exit();
    }

    // Verify CSRF token exists and is valid
    if (!isset($_POST['CSRFToken']) or !isset($_SESSION['token']) or !(hash_equals($_SESSION['token'], $_POST['CSRFToken']))) {
        returnHTTPResponse(400, 'HTTP Status 400: Invalid CSRF Token!');
        exit();
    }

    // Sanitize Inputs 
    $nameInput = sanitizeInput($_POST['nameInput']);
    $idInput = filter_var($_POST['idInput'], FILTER_SANITIZE_NUMBER_INT);
    $emailInput = sanitizeInput($_POST['emailInput']);
    $weekInput = filter_var($_POST['weekInput'], FILTER_SANITIZE_NUMBER_INT);
    $yearInput = filter_var($_POST['yearInput'], FILTER_SANITIZE_NUMBER_INT);
    $monStartTime = $_POST['monStartTime'];
    $monEndTime = $_POST['monEndTime'];
    $tueStartTime = $_POST['tueStartTime'];
    $tueEndTime = $_POST['tueEndTime'];
    $wedStartTime = $_POST['wedStartTime'];
    $wedEndTime = $_POST['wedEndTime'];
    $thuStartTime = $_POST['thuStartTime'];
    $thuEndTime = $_POST['thuEndTime'];
    $friStartTime = $_POST['friStartTime'];
    $friEndTime = $_POST['friEndTime'];
    $timesheetTextInput = sanitizeInput($_POST['timesheetTextInput']);

    if ($_POST["formID"] == "submitTimesheet") {
        // Validate Inputs
        if (validateInput($idInput, inputType::Number) && $idInput > 0) {

            // Checks user exists
            if (!userExists($idInput, $con)) {
                returnHTTPResponse(400, 'Invalid User ID');
                exit();
            } else {
                $userInfo = getUserData($idInput, $con)->fetch_array(MYSQLI_ASSOC);
            }

            // Checks email matches
            if (validateInput($emailInput, inputType::Email) && $emailInput == $userInfo['email'] && $emailInput == $_SESSION['email']) {
                // Checks name matches
                if (validateInput($nameInput, inputType::Name) && $nameInput == ($userInfo['fname'] . ' ' . $userInfo['lname'])) {
                    // Make sure a valid week number has been entered
                    if (validateInput($weekInput, inputType::Number) && $weekInput > 0 && $weekInput < 53){
                        // Make sure they are not submitting a timesheet for a week greater than the current week
                        if ($weekInput <= date("W")) {
                            // Make sure a valid year has been entered
                            if (validateInput($yearInput, inputType::Number) && $yearInput == date("Y")) {

                                if (timesheetExistsForWeekAndYearForUser($weekInput, $yearInput, $idInput, $con)) {
                                    returnHTTPResponse(400, 'An active timesheet for this week already exists');
                                    exit();
                                }

                                // Validate all time inputs are in the correct format and that end times are greater than start times
                                if (!validateInput($monStartTime, inputType::HHMM) && !validateInput($monEndTime, inputType::HHMM) && (strtotime($monEndTime) < strtotime($monStartTime))) {
                                    returnHTTPResponse(400, 'Monday start time and/or end time are incorrect');
                                    exit();
                                }

                                if (($monStartTime == ''  && $monEndTime != '') or ($monStartTime != ''  && $monEndTime == '')) {
                                    returnHTTPResponse(400, 'Monday start time and/or end time are incorrect');
                                    exit();
                                }

                                if (!validateInput($tueStartTime, inputType::HHMM) && !validateInput($tueEndTime, inputType::HHMM) && (strtotime($tueEndTime) < strtotime($tueStartTime))) {
                                    returnHTTPResponse(400, 'Tuesday start time and/or end time are incorrect');
                                    exit();
                                }
                                
                                if (($tueStartTime == ''  && $tueEndTime != '') or ($tueStartTime != ''  && $tueEndTime == '')) {
                                    returnHTTPResponse(400, 'Monday start time and/or end time are incorrect');
                                    exit();
                                }

                                if (!validateInput($wedStartTime, inputType::HHMM) && !validateInput($wedEndTime, inputType::HHMM) && (strtotime($wedEndTime) < strtotime($wedStartTime))) {
                                    returnHTTPResponse(400, 'Wednesday start time and/or end time are incorrect');
                                    exit();
                                }
                                
                                if (($wedStartTime == ''  && $wedEndTime != '') or ($wedStartTime != ''  && $wedEndTime == '')) {
                                    returnHTTPResponse(400, 'Monday start time and/or end time are incorrect');
                                    exit();
                                }

                                if (!validateInput($thuStartTime, inputType::HHMM) && !validateInput($thuEndTime, inputType::HHMM) && (strtotime($thuEndTime) < strtotime($thuStartTime))) {
                                    returnHTTPResponse(400, 'Thursday start time and/or end time are incorrect');
                                    exit();
                                }
                                
                                if (($thuStartTime == ''  && $thuEndTime != '') or ($thuStartTime != ''  && $thuEndTime == '')) {
                                    returnHTTPResponse(400, 'Monday start time and/or end time are incorrect');
                                    exit();
                                }

                                if (!validateInput($friStartTime, inputType::HHMM) && !validateInput($friEndTime, inputType::HHMM) && (strtotime($friEndTime) < strtotime($friStartTime))) {
                                    returnHTTPResponse(400, 'Friday start time and/or end time are incorrect');
                                    exit();
                                }
                                
                                if (($friStartTime == ''  && $friEndTime != '') or ($friStartTime != ''  && $friEndTime == '')) {
                                    returnHTTPResponse(400, 'Monday start time and/or end time are incorrect');
                                    exit();
                                }

                                // Validate text input 
                                if (validateInput($timesheetTextInput, inputType::Text)) {
                                    // Calculate total hours
                                    $totalSeconds = getTotalSeconds($monStartTime, $monEndTime, $tueStartTime, $tueEndTime, $wedStartTime, $wedEndTime, $thuStartTime, $thuEndTime, $friStartTime, $friEndTime);

                                    $totalTime = getTotalTime($totalSeconds);

                                    // Makes sure total hours is not greater than 168 (24 * 7) and not less than 0
                                    if ((($totalSeconds / 168) == 3600) && (($totalSeconds % 168) > 0)) {
                                        returnHTTPResponse(400, 'Cannot claim more time than there is in a week');
                                        exit();
                                    } else if ($totalSeconds < 0) {
                                        returnHTTPResponse(400, 'Cannot claim negative time');
                                        exit();
                                    }

                                    // Execute MySQL Statement
                                    if (!insertTimesheet($idInput, $weekInput, $yearInput, $monStartTime, $monEndTime, $tueStartTime, $tueEndTime, $wedStartTime, $wedEndTime, $thuStartTime, $thuEndTime, $friStartTime, $friEndTime, $totalTime, $timesheetTextInput, $con)) {
                                        returnHTTPResponse(500, 'Database Insertion Failed');
                                        exit();
                                    } else {
                                        returnHTTPResponse(201, 'Timesheet Successfully Submitted');
                                        exit();
                                    }

                                } else {
                                    returnHTTPResponse(400, 'Incorrect text entered in additional comments');
                                    exit();
                                }
                            } else {
                                returnHTTPResponse(400, 'Incorrect Year Entered');
                                exit();
                            }
                        } else {
                            returnHTTPResponse(400, 'Cannot submit a timesheet for a week greater than the current week');
                            exit();
                        }
                    } else {
                        returnHTTPResponse(400, 'Incorrect Week Number Entered');
                        exit();
                    }
                } else {
                    returnHTTPResponse(400, 'Incorrect Name Entered');
                    exit();
                }
            } else {
                returnHTTPResponse(400, 'Incorrect Email Entered');
                exit();
            }
        }

    } else {
        returnHTTPResponse(400, 'Incorrect Form ID');
        exit();
    }

?>