<?php 

/*
 * timesheetSubmission.php file for the Individual Project (University of Sussex 2023) 
 * Author: William Moss (235319)
 *
 * Handles user timesheet submissions
 */

    require_once(__DIR__ . '/../../../src/inc/utilities.php');

    // DEBUGGING ONLY
    // echo(print_r($_POST));

    // Connect to database
    try {
        $con = mysqliConnect();
        if ($con->connect_error) {
            echo "Connection Failed";
            exit();
        } 
    } catch (Exception $e) {
        echo "Unknown Error. Please try again";
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
                echo "Invalid User ID";
                exit();
            } else {
                $userInfo = getUserData($idInput, $con)->fetch_array(MYSQLI_ASSOC);
            }

            // Checks email matches
            if (validateInput($emailInput, inputType::Email) && $emailInput == $userInfo['email']) {
                // Checks name matches
                if (validateInput($nameInput, inputType::Name) && $nameInput == ($userInfo['fname'] . ' ' . $userInfo['lname'])) {
                    // Make sure a valid week number has been entered
                    if (validateInput($weekInput, inputType::Number) && $weekInput > 0 && $weekInput < 53){
                        // Make sure they are not submitting a timesheet for a week greater than the current week
                        if ($weekInput <= date("W")) {
                            // Make sure a valid year has been entered
                            if (validateInput($yearInput, inputType::Number) && $yearInput == date("Y")) {

                                // Check that a timesheet with the same week number and year doesn't already exist for the user 
                                $stmt = $con->prepare('SELECT ta_num, week_num, year FROM timesheets WHERE ta_num = ? AND week_num = ? AND year = ? AND (status = "Pending Approval" OR status = "Approved")');
                                $stmt->bind_param('iii', $idInput, $weekInput, $yearInput);
                        
                                // Executes the statement and stores the result
                                $stmt->execute();
                                $result = $stmt->get_result();

                                // If rows equals 0 no timesheets exist
                                if ($result->num_rows != 0) {
                                    echo 'An active timesheet for this week already exists';
                                    exit();
                                }

                                // Validate all time inputs are in the correct format and that end times are greater than start times
                                if (!validateInput($monStartTime, inputType::HHMM) && !validateInput($monEndTime, inputType::HHMM) && (strtotime($monEndTime) < strtotime($monStartTime))) {
                                    echo 'Monday start time and/or end time are incorrect';
                                    exit();
                                }

                                if (!validateInput($tueStartTime, inputType::HHMM) && !validateInput($tueEndTime, inputType::HHMM) && (strtotime($tueEndTime) < strtotime($tueStartTime))) {
                                    echo 'Tuesday start time and/or end time are incorrect';
                                    exit();
                                }

                                if (!validateInput($wedStartTime, inputType::HHMM) && !validateInput($wedEndTime, inputType::HHMM) && (strtotime($wedEndTime) < strtotime($wedStartTime))) {
                                    echo 'Wednesday start time and/or end time are incorrect';
                                    exit();
                                }

                                if (!validateInput($thuStartTime, inputType::HHMM) && !validateInput($thuEndTime, inputType::HHMM) && (strtotime($thuEndTime) < strtotime($thuStartTime))) {
                                    echo 'Thursday start time and/or end time are incorrect';
                                    exit();
                                }

                                if (!validateInput($friStartTime, inputType::HHMM) && !validateInput($friEndTime, inputType::HHMM) && (strtotime($friEndTime) < strtotime($friStartTime))) {
                                    echo 'Friday start time and/or end time are incorrect';
                                    exit();
                                }

                                // Validate text input 
                                if (validateInput($timesheetTextInput, inputType::Text)) {
                                    // Calculate total hours
                                    $totalSeconds = (strtotime($monEndTime) - strtotime($monStartTime)) + (strtotime($tueEndTime) - strtotime($tueStartTime)) + (strtotime($wedEndTime) - strtotime($wedStartTime)) + (strtotime($thuEndTime) - strtotime($thuStartTime)) + (strtotime($friEndTime) - strtotime($friStartTime));
                                    $minutes = (int) (($totalSeconds / 60) % 60);
                                    $hours = (int) (($totalSeconds / 60) - $minutes) / 60;

                                    if ($minutes < 10) {
                                        $minutes = '0' . strval($minutes);
                                    }

                                    if ($hours < 10) {
                                        $hours = '0' . strval($hours);
                                    }

                                    $totalTime = $hours . ':' . $minutes;

                                    echo $totalSeconds;
                                    echo '---';
                                    echo $totalTime;

                                    // Makes sure total hours is not greater than 168 (24 * 7) and not less than 0
                                    if ((($totalSeconds / 168) == 3600) && (($totalSeconds % 168) > 0)) {
                                        echo 'Cannot claim more than 168 hours in a week';
                                        exit();
                                    } else if ($totalSeconds < 0) {
                                        echo 'Cannot claim negative time';
                                        exit();
                                    }

                                    // Insert new timesheet into database
                                    $stmt = $con->prepare('INSERT INTO timesheets (ta_num, week_num, year, monStart, monEnd, tueStart, tueEnd, wedStart, wedEnd, thuStart, thuEnd, friStart, friEnd, total, comments) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
                                    $stmt->bind_param('iiissssssssssss', $idInput, $weekInput, $yearInput, $monStartTime, $monEndTime, $tueStartTime, $tueEndTime, $wedStartTime, $wedEndTime, $thuStartTime, $thuEndTime, $friStartTime, $friEndTime, $totalTime, $timesheetTextInput);

                                    // Execute MySQL Statement
                                    if (!$stmt->execute()) {
                                        echo "Database Insertion Failed";
                                        exit();
                                    } else {
                                        echo "Timesheet Successfully Submitted";
                                        exit();
                                    }

                                } else {
                                    echo 'Incorrect text entered in additional comments';
                                    exit();
                                }
                            } else {
                                echo 'Incorrect Year Entered';
                                exit();
                            }
                        } else {
                            echo 'Cannot submit a timesheet for a week greater than the current week';
                            exit();
                        }
                    } else {
                        echo 'Incorrect Week Number Entered';
                        exit();
                    }
                } else {
                    echo 'Incorrect Name Entered';
                    exit();
                }
            } else {
                echo 'Incorrect Email Entered';
                exit();
            }
        }

    } else {
        echo 'Incorrect Form ID';
        exit();
    }

?>