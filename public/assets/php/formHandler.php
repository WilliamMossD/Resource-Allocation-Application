<?php 

/*
 * formHandler.php file for the Individual Project (University of Sussex 2023) 
 * Author: William Moss (235319)
 *
 * Handles form submissions
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

    try {
        switch ($_POST["formID"]) {
            case "addUser":

                // Sanitize Inputs
                $firstNameInput = sanitizeInput($_POST['firstNameInput']);
                $lastNameInput = sanitizeInput($_POST['lastNameInput']);
                $emailInput = sanitizeInput($_POST['emailInput']);

                // Validate Inputs
                if (validateInput($firstNameInput, inputType::Name)){
                    if (validateInput($lastNameInput, inputType::Name)) {
                        if (validateInput($emailInput, inputType::Email) && !userExistsByEmail($emailInput, $con)) {
                            
                            // Prepare MySQL Statement
                            $stmt = $con->prepare('INSERT INTO teaching_assistants (fname, lname, email, admin) VALUES (?, ?, ?, ?)');
                            if (is_null($_POST['adminCheck'])) {
                                $admin = 0;
                            } else {
                                $admin = 1;
                            }
                            $stmt->bind_param('sssi', $firstNameInput, $lastNameInput, $emailInput, $admin);

                            // Execute MySQL Statement
                            if (!$stmt->execute()) {
                                echo "Database Insertion Failed";
                                exit();
                            } else {
                                echo "User Successfully Added";
                                exit();
                            }

                        } else {
                            echo "Invalid Email Format or Email already in use";
                            exit();
                        }
                    } else {
                        echo "Invalid Last Name";
                        exit();
                    }
                } else {
                    echo "Invalid First Name";
                    exit();
                }

            // selectUser Form 
            case "selectUser":

                $userSelect = filter_var($_POST['userSelect'], FILTER_SANITIZE_NUMBER_INT);

                if (validateInput($userSelect, inputType::Number) && $userSelect > 0) {
                    // Gets user data
                    $result = getUserData($userSelect, $con);
                    
                    // Checks that user data is not empty
                    if ($result->num_rows == 0) {
                        echo "Invalid User ID";
                        exit();
                    } 

                    // Returns row in a JSON format
                    echo json_encode($result->fetch_array(MYSQLI_ASSOC));
                    exit();

                } else {
                    echo "Invalid User ID Format";
                    exit();
                }

            // updateUser Form
            case "updateUser":
            
                // Sanitize Inputs
                $editFirstNameInput = sanitizeInput($_POST['editFirstNameInput']);
                $editLastNameInput = sanitizeInput($_POST['editLastNameInput']);
                $editEmailInput = sanitizeInput($_POST['editEmailInput']);
                $editUserSelect = filter_var($_POST['editUserSelect'], FILTER_SANITIZE_NUMBER_INT);

                // Validate Inputs
                if (validateInput($editUserSelect, inputType::Number) && $editUserSelect > 0) {

                    // Checks user exists
                    if (!userExists($editUserSelect, $con)) {
                        echo "Invalid User ID";
                        exit();
                    } 

                    if (validateInput($editFirstNameInput, inputType::Name)){
                        if (validateInput($editLastNameInput, inputType::Name)) {
                            if (validateInput($editEmailInput, inputType::Email)) {
                                
                                // Prepare MySQL Statement
                                $stmt = $con->prepare('UPDATE teaching_assistants SET fname=?, lname=?, email=?, admin=? WHERE ta_num=?');
                                if (is_null($_POST['editAdminCheck'])) {
                                    $admin = 0;
                                } else {
                                    $admin = 1;
                                }
                                $stmt->bind_param('sssii', $editFirstNameInput, $editLastNameInput, $editEmailInput, $admin, $editUserSelect);

                                // Execute MySQL Statement
                                if (!$stmt->execute()) {
                                    echo "Database Update Failed";
                                    exit();
                                } else {
                                    echo "User Successfully Edited";
                                    exit();
                                }

                            } else {
                                echo "Invalid Email Format";
                                exit();
                            }
                        } else {
                            echo "Invalid Last Name";
                            exit();
                        }
                    } else {
                        echo "Invalid First Name";
                        exit();
                    }
                } else {
                    echo "Invalid User ID Format";
                    exit();
                }

            // selectModule Form
            case "selectModule":

                $moduleSelect = filter_var($_POST['moduleSelect'], FILTER_SANITIZE_NUMBER_INT);

                if (validateInput($moduleSelect, inputType::Number) && $moduleSelect > 0) {

                    // Gets module data
                    $result = getModuleData($moduleSelect, $con);
                    
                    // Checks that module data is not empty
                    if ($result->num_rows == 0) {
                        echo "Invalid Module ID";
                        exit();
                    } 

                    // Returns row in a JSON format
                    echo json_encode($result->fetch_array(MYSQLI_ASSOC));
                    exit();

                } else {
                    echo "Invalid Module ID Format";
                    exit();
                }

            // updateModule Form
            case "updateModule":

                // Sanitize Inputs
                $editModuleNameInput = sanitizeInput($_POST['editModuleNameInput']);
                $editModuleConInput = sanitizeInput($_POST['editModuleConInput']);
                $editModuleDesInput = sanitizeInput($_POST['editModuleDesInput']);
                $editModuleLinkInput = sanitizeInput($_POST['editModuleLinkInput']);
                $editModuleSelect = filter_var($_POST['editModuleSelect'], FILTER_SANITIZE_NUMBER_INT);

                // Validate Inputs
                if (validateInput($editModuleSelect, inputType::Number) && $editModuleSelect > 0) {

                    // Checks that module exists
                    if (!moduleExists($editModuleSelect, $con)) {
                        echo "Invalid Module ID";
                        exit();
                    } 

                    if (validateInput($editModuleNameInput, inputType::Name)) {
                        if (validateInput($editModuleConInput, inputType::Name)) {
                            if (validateInput($editModuleDesInput, inputType::Text)) {
                                if (validateInput($editModuleLinkInput, inputType::URL)) {
                                
                                    // Prepare MySQL Statement
                                    $stmt = $con->prepare('UPDATE modules SET module_name=?, module_convenor=?, module_description=?, link=? WHERE module_num=?');
                                    $stmt->bind_param('ssssi', $editModuleNameInput, $editModuleConInput, $editModuleDesInput, $editModuleLinkInput, $editModuleSelect);

                                    // Execute MySQL Statement
                                    if (!$stmt->execute()) {
                                        echo "Database Update Failed";
                                        exit();
                                    } else {
                                        echo "Module Successfully Edited";
                                        exit();
                                    }

                                } else {
                                    echo "Invalid Link Format";
                                    exit();
                                }
                            } else {
                                echo "Invalid Text Format";
                                exit();
                            }
                        } else {
                            echo "Invalid Convenor Name";
                            exit();
                        }
                    } else {
                        echo "Invalid Module Name";
                        exit();
                    }
                } else {
                    echo "Invalid Module ID Format";
                    exit();
                }

            // deleteUser Form 
            case "deleteUser":

                // Sanitize Input
                $deleteUserSelect = filter_var($_POST['deleteUserSelect'], FILTER_SANITIZE_NUMBER_INT);

                // Validate Input
                if (validateInput($deleteUserSelect, inputType::Number) && $deleteUserSelect > 0) {

                    // Checks user exists
                    if (!userExists($deleteUserSelect, $con)) {
                        echo "Invalid User ID";
                        exit();
                    } 

                    // Prepare MySQL Statement
                    $stmt = $con->prepare('DELETE FROM teaching_assistants WHERE ta_num=?');
                    $stmt->bind_param('i', $deleteUserSelect);

                    // Execute MySQL Statement
                    if (!$stmt->execute()) {
                        echo "Database Deletion Failed";
                        exit();
                    } else {
                        echo "User Successfully Deleted";
                        exit();
                    }

                } else {
                    echo "Invalid User ID Format";
                    exit();
                }

            // addModule Form 
            case "addModule":

                // Sanitize Inputs
                $moduleNameInput = sanitizeInput($_POST['moduleNameInput']);
                $moduleConInput = sanitizeInput($_POST['moduleConInput']);
                $moduleDesInput = sanitizeInput($_POST['moduleDesInput']);
                $moduleLinkInput = sanitizeInput($_POST['moduleLinkInput']);

                // Validate Inputs
                if (validateInput($moduleNameInput, inputType::Name)) {
                    if (validateInput($moduleConInput, inputType::Name)) {
                        if (validateInput($moduleDesInput, inputType::Text)) {
                            if (validateInput($moduleLinkInput, inputType::URL)) {
                            
                                // Prepare MySQL Statement
                                $stmt = $con->prepare('INSERT INTO modules (module_name	, module_convenor, module_description, link) VALUES (?, ?, ?, ?)');
                                $stmt->bind_param('ssss', $moduleNameInput, $moduleConInput, $moduleDesInput, $moduleLinkInput);

                                // Execute MySQL Statement
                                if (!$stmt->execute()) {
                                    echo "Database Insertion Failed";
                                    exit();
                                } else {
                                    echo "Module Successfully Added";
                                    exit();
                                }

                            } else {
                                echo "Invalid Link Format";
                                exit();
                            }
                        } else {
                            echo "Invalid Text Format";
                            exit();
                        }
                    } else {
                        echo "Invalid Convenor Name";
                        exit();
                    }
                } else {
                    echo "Invalid Module Name";
                    exit();
                }

            // deleteModule Form 
            case "deleteModule":

                // Sanitize Input
                $deleteModuleSelect = filter_var($_POST['deleteModuleSelect'], FILTER_SANITIZE_NUMBER_INT);

                // Validate Input
                if (validateInput($deleteModuleSelect, inputType::Number) && $deleteModuleSelect > 0) {

                    // Checks that module exists
                    if (!moduleExists($deleteModuleSelect, $con)) {
                        echo "Invalid Module ID";
                        exit();
                    } 

                    // Prepare MySQL Statement
                    $stmt = $con->prepare('DELETE FROM modules WHERE module_num=?');
                    $stmt->bind_param('i', $deleteModuleSelect);

                    // Execute MySQL Statement
                    if (!$stmt->execute()) {
                        echo "Database Deletion Failed";
                        exit();
                    } else {
                        echo "Module Successfully Deleted";
                        exit();
                    }

                } else {
                    echo "Invalid Module ID Format";
                    exit();
                }

            // viewSession Form 
            case "viewSession":

                $sessionsModuleSelect = filter_var($_POST['sessionsModuleSelect'], FILTER_SANITIZE_NUMBER_INT);

                // Validate Input
                if (validateInput($sessionsModuleSelect, inputType::Number) && $sessionsModuleSelect > 0) {

                    // Checks that module exists
                    if (!moduleExists($sessionsModuleSelect, $con)) {
                        echo "Invalid Module ID";
                        exit();
                    } 

                    // Prepare MySQL Statement
                    $stmt = $con->prepare("SELECT module_session_num AS 'Session ID', num_of_ta AS 'TA Limit', session_day as 'Day', DATE_FORMAT(module_sessions.session_start, '%H:%i') AS 'Start Time', DATE_FORMAT(module_sessions.session_end, '%H:%i') AS 'End Time', session_type AS 'Session Type', session_location AS 'Location' FROM module_sessions WHERE module_num=?");
                    $stmt->bind_param('i', $sessionsModuleSelect);

                    // Execute MySQL Statement
                    $stmt->execute();
                    $result = $stmt->get_result();

                    if ($result->num_rows == 0) {
                        echo "No Sessions Exist";
                        exit();
                    } else {
                        echo generateTable($result->fetch_all(MYSQLI_ASSOC), $result->fetch_fields());
                        exit();
                    }

                } else {
                    echo "Invalid Module ID";
                    exit();
                }

            // addSession Form
            case "addSession":

                // Sanitize Inputs
                $sessionModuleNameInput = filter_var($_POST['sessionModuleNameInput'], FILTER_SANITIZE_NUMBER_INT);
                $moduleLocInput = sanitizeInput($_POST['moduleLocInput']);
                $sessionTypeSelect = sanitizeInput($_POST['sessionTypeSelect']);
                $sessionTAInput = filter_var($_POST['sessionTAInput'], FILTER_SANITIZE_NUMBER_INT);
                $sessionDaySelect = sanitizeInput($_POST['sessionDaySelect']);
                $sessionStartTimeInput = $_POST['sessionStartTimeInput'];
                $sessionEndTimeInput = $_POST['sessionEndTimeInput'];

                if (validateInput($sessionModuleNameInput, inputType::Number) && $sessionModuleNameInput > 0) {
                    
                    // Checks that module exists
                    if (!moduleExists($sessionModuleNameInput, $con)) {
                        echo "Invalid Module ID";
                        exit();
                    } 

                    if (validateInput($moduleLocInput, inputType::Location)) {
                        if (in_array($sessionTypeSelect, ["Lab", "Teaching", "Other"])) {
                            if (validateInput($sessionTAInput, inputType::Number) && (0 < $sessionTAInput) && ($sessionTAInput <= 5)) {
                                if (in_array($sessionDaySelect, ["Monday", "Tuesday", "Wednesday", "Thursday", "Friday"])) {
                                    if (validateInput($sessionStartTimeInput, inputType::HHMM) && (strtotime("08:00") <= strtotime($sessionStartTimeInput)) && (strtotime($sessionStartTimeInput) <= strtotime("20:00"))) {
                                        if (validateInput($sessionEndTimeInput, inputType::HHMM) && (strtotime("09:00") <= strtotime($sessionEndTimeInput)) && (strtotime($sessionEndTimeInput) <= strtotime("21:00")) && (strtotime($sessionEndTimeInput) > strtotime($sessionStartTimeInput))) {
                                            
                                            // Prepare MySQL Statement
                                            $stmt = $con->prepare('INSERT INTO module_sessions (module_num, num_of_ta, session_day, session_start, session_end, session_type, session_location) VALUES (?, ?, ?, ?, ?, ?, ?)');
                                            $stmt->bind_param('issssss', $sessionModuleNameInput, $sessionTAInput, $sessionDaySelect, $sessionStartTimeInput, $sessionEndTimeInput, $sessionTypeSelect, $moduleLocInput);

                                            // Execute MySQL Statement
                                            if (!$stmt->execute()) {
                                                echo "Database Insertion Failed";
                                                exit();
                                            } else {
                                                echo "Session Successfully Created";
                                                exit();
                                            }
                                        } else {
                                            echo "Invalid End Time Format or Range";
                                            exit();
                                        }

                                    } else {
                                        echo "Invalid Start Time Format or Range";
                                        exit();
                                    }
                                } else {
                                    echo "Invalid Date";
                                    exit();
                                }
                            } else {
                                echo "Invalid TA Number";
                                exit();
                            }
                        } else {
                            echo "Invalid Session Type Selected";
                            exit();
                        }
                    } else {
                        echo "Invalid Location Format";
                        exit();
                    }
                } else {
                    echo "Invalid Module ID";
                    exit();
                }

            // Select Session Form
            case "selectSession":
                $sessionSelect = filter_var($_POST['sessionSelect'], FILTER_SANITIZE_NUMBER_INT);
    
                if (validateInput($sessionSelect, inputType::Number) && $sessionSelect > 0) {

                    // Gets session data
                    $result = getSessionData($sessionSelect, $con);
                    
                    // Checks that session data is not empty
                    if ($result->num_rows == 0) {
                        echo "Invalid Session ID";
                        exit();
                    } 

                    echo json_encode($result->fetch_array(MYSQLI_ASSOC));
                    exit();

                } else {
                    echo "Invalid Session ID Format";
                    exit();
                }

            // Update Session Form
            case "updateSession":

                // Sanitize Inputs
                $editSessionSelect = filter_var($_POST['editSessionSelect'], FILTER_SANITIZE_NUMBER_INT);
                $editSessionModuleNameInput = filter_var($_POST['editSessionModuleNameInput'], FILTER_SANITIZE_NUMBER_INT);
                $editSessionLocInput = sanitizeInput($_POST['editSessionLocInput']);
                $editSessionTypeSelect = sanitizeInput($_POST['editSessionTypeSelect']);
                $editSessionTAInput = filter_var($_POST['editSessionTAInput'], FILTER_SANITIZE_NUMBER_INT);
                $editSessionDaySelect = sanitizeInput($_POST['editSessionDaySelect']);
                $editSessionStartTimeInput = $_POST['editSessionStartTimeInput'];
                $editSessionEndTimeInput = $_POST['editSessionEndTimeInput'];

                if (validateInput($editSessionSelect, inputType::Number) && $editSessionSelect > 0) {
                    
                    // Checks session exisits
                    if (!sessionExists($editSessionSelect, $con)) {
                        echo "Invalid Session ID";
                        exit();
                    } 

                    if (validateInput($editSessionModuleNameInput, inputType::Number) && $editSessionModuleNameInput > 0) {
                        
                        // Checks that module exists
                        if (!moduleExists($editSessionModuleNameInput, $con)) {
                            echo "Invalid Module ID";
                            exit();
                        } 

                        if (validateInput($editSessionLocInput, inputType::Location)) {
                            if (in_array($editSessionTypeSelect, ["Lab", "Teaching", "Other"])) {
                                if (validateInput($editSessionTAInput, inputType::Number) && (0 < $editSessionTAInput) && ($editSessionTAInput <= 5)) {
                                    if (in_array($editSessionDaySelect, ["Monday", "Tuesday", "Wednesday", "Thursday", "Friday"])) {
                                        if (validateInput($editSessionStartTimeInput, inputType::HHMM) && (strtotime("08:00") <= strtotime($editSessionStartTimeInput)) && (strtotime($editSessionStartTimeInput) <= strtotime("20:30"))) {
                                            if (validateInput($editSessionEndTimeInput, inputType::HHMM) && (strtotime("08:30") <= strtotime($editSessionEndTimeInput)) && (strtotime($editSessionEndTimeInput) <= strtotime("21:00")) && (strtotime($editSessionEndTimeInput) > strtotime($editSessionStartTimeInput))) {
                                                
                                                // Prepare MySQL Statement
                                                $stmt = $con->prepare('UPDATE module_sessions SET module_num=?, num_of_ta=?, session_day=?, session_start=?, session_end=?, session_type=?, session_location=? WHERE module_session_num=?');
                                                $stmt->bind_param('issssssi', $editSessionModuleNameInput, $editSessionTAInput, $editSessionDaySelect, $editSessionStartTimeInput, $editSessionEndTimeInput, $editSessionTypeSelect, $editSessionLocInput, $editSessionSelect);

                                                // Execute MySQL Statement
                                                if (!$stmt->execute()) {
                                                    echo "Database Update Failed";
                                                    exit();
                                                } else {
                                                    echo "Session Successfully Edited";
                                                    exit();
                                                }
                                            } else {
                                                echo "Invalid End Time Format or Range";
                                                exit();
                                            }
                                        } else {
                                            echo "Invalid Start Time Format or Range";
                                            exit();
                                        }
                                    } else {
                                        echo "Invalid Date";
                                        exit();
                                    }
                                } else {
                                    echo "Invalid TA Number";
                                    exit();
                                }
                            } else {
                                echo "Invalid Session Type Selected";
                                exit();
                            }
                        } else {
                            echo "Invalid Location Format";
                            exit();
                        }
                    } else {
                        echo "Invalid Module ID Format";
                        exit();
                    }
                } else {
                    echo "Invalid Session ID Format";
                    exit();
                }

            // Delete Session Form
            case "deleteSession":

                // Sanitize Input
                $deleteSessionSelect = filter_var($_POST['deleteSessionSelect'], FILTER_SANITIZE_NUMBER_INT);

                // Validate Input
                if (validateInput($deleteSessionSelect, inputType::Number) && $deleteSessionSelect > 0) {

                    // Checks session exists
                    if (!sessionExists($deleteSessionSelect, $con)) {
                        echo "Invalid Session ID";
                        exit();
                    } 

                    // Prepare MySQL Statement
                    $stmt = $con->prepare('DELETE FROM module_sessions WHERE module_session_num=?');
                    $stmt->bind_param('i', $deleteSessionSelect);
                    
                    // Execute MySQL Statement
                    if (!$stmt->execute()) {
                        echo "Database Deletion Failed";
                        exit();
                    } else {
                        echo "Session Successfully Deleted";
                        exit();
                    }

                } else {
                    echo "Invalid Session ID Format";
                    exit();
                }

            // View Allocation by Module Form
            case "viewAllocationByModule":

                // Sanitize Input
                $viewAllocModuleSelect = filter_var($_POST['viewAllocModuleSelect'], FILTER_SANITIZE_NUMBER_INT);

                // Validate Input
                if (validateInput($viewAllocModuleSelect, inputType::Number) && $viewAllocModuleSelect > 0) {

                    // Checks session exists
                    if (!moduleExists($viewAllocModuleSelect, $con)) {
                        echo "Invalid Module ID";
                        exit();
                    } 

                    // Gets sessions assigned to module
                    $sessions = getModuleSessions($viewAllocModuleSelect, $con);

                    // Makes sure module has sessions
                    if ($sessions->num_rows == 0) {
                        echo "No Sessions Exist";
                        exit();
                    } 

                    // Iterate through each session and generate HTML
                    $sessions = $sessions->fetch_all(MYSQLI_ASSOC);
                    echo '<div>';
                    echo generateText(getModuleName($viewAllocModuleSelect, $con), 3);
                    foreach ($sessions as $session) {
                        $result = getSessionAllocation($session['module_session_num'], $con);
                        $sessionInfo = getSessionData($session['module_session_num'], $con)->fetch_array(MYSQLI_ASSOC);
                        echo generateText($sessionInfo['session_day'] . ' ' . $sessionInfo['session_start'] . ' - ' . $sessionInfo['session_end'] , 4) . generateAllocTable($result->fetch_all(MYSQLI_ASSOC), $result->fetch_fields()) ;
                    }
                    echo '</div>';
                    exit();

                } else {
                    echo "Invalid Module ID Format";
                    exit();
                }
            
            // View Allocation by Session Form
            case "viewAllocationBySession":

                // Sanitize Input
                $viewAllocSessionSelect = filter_var($_POST['viewAllocSessionSelect'], FILTER_SANITIZE_NUMBER_INT);

                // Validate Input
                if (validateInput($viewAllocSessionSelect, inputType::Number) && $viewAllocSessionSelect > 0) {

                    // Checks session exists
                    if (!sessionExists($viewAllocSessionSelect, $con)) {
                        echo "Invalid Session ID";
                        exit();
                    } 

                    $result = getSessionAllocation($viewAllocSessionSelect, $con);

                    if ($result->num_rows == 0) {
                        echo "No Allocation Exists";
                        exit();
                    } else {
                        $sessionInfo = getSessionData($viewAllocSessionSelect, $con)->fetch_array(MYSQLI_ASSOC);
                        echo '<div>' .  generateText(getModuleName(getModuleNumBySession($viewAllocSessionSelect, $con), $con) . ': ' . $sessionInfo['session_day'] . ' ' . $sessionInfo['session_start'] . ' - ' . $sessionInfo['session_end'] , 4) . generateAllocTable($result->fetch_all(MYSQLI_ASSOC), $result->fetch_fields()) . '</div>';
                        exit();
                    }

                } else {
                    echo "Invalid Session ID Format";
                    exit();
                }

            // View Allocation by User Form
            case "viewAllocationByUser":

                // Sanitize Input
                $viewAllocUserSelect = filter_var($_POST['viewAllocUserSelect'], FILTER_SANITIZE_NUMBER_INT);

                // Validate Input
                if (validateInput($viewAllocUserSelect, inputType::Number) && $viewAllocUserSelect > 0) {

                    // Checks user exists
                    if (!userExists($viewAllocUserSelect, $con)) {
                        echo "Invalid User ID";
                        exit();
                    } 

                    // Prepare MySQL Statement
                    $stmt = $con->prepare("SELECT modules.module_name AS 'Module Name', module_sessions.session_day AS 'Day', DATE_FORMAT(module_sessions.session_start, '%H:%i') AS 'Session Start', DATE_FORMAT(module_sessions.session_end, '%H:%i') AS 'Session End', module_sessions.session_type AS 'Session Type', module_sessions.session_location AS 'Location', assigned_to.ta_num, assigned_to.module_session_num FROM modules, module_sessions, assigned_to WHERE modules.module_num = module_sessions.module_num AND module_sessions.module_session_num = assigned_to.module_session_num AND assigned_to.ta_num=?");
                    $stmt->bind_param('i', $viewAllocUserSelect);

                    // Execute MySQL Statement
                    $stmt->execute();
                    $result = $stmt->get_result();

                    if ($result->num_rows == 0) {
                        echo "No Allocation Exists";
                        exit();
                    } else {
                        echo '<div>' . generateText(getUserName($viewAllocUserSelect, $con), 4) . generateAllocTable($result->fetch_all(MYSQLI_ASSOC), $result->fetch_fields()) . '</div>';
                        exit();
                    }

                } else {
                    echo "Invalid User ID Format";
                    exit();
                }

            // Remove Allocation Form
            case "removeAlloc":

                $ta_num = filter_var($_POST['ta_num'], FILTER_SANITIZE_NUMBER_INT);
                $module_session_num = filter_var($_POST['module_session_num'], FILTER_SANITIZE_NUMBER_INT);

                // Validate Input
                if (validateInput($ta_num, inputType::Number) && $ta_num > 0) {
                    if (validateInput($module_session_num, inputType::Number) && $module_session_num > 0) {
                    
                        // Prepare MySQL Statement
                        $stmt = $con->prepare('SELECT * FROM assigned_to WHERE assigned_to.ta_num = ? AND assigned_to.module_session_num = ?');
                        $stmt->bind_param('ii', $ta_num, $module_session_num);

                        // Execute MySQL Statement
                        $stmt->execute();
                        $result = $stmt->get_result();

                        if ($result->num_rows == 0) {
                            echo "No Allocation Exists";
                            exit();
                        } else {

                            // Prepare MySQL Statement
                            $stmt = $con->prepare('DELETE FROM assigned_to WHERE assigned_to.ta_num = ? AND assigned_to.module_session_num = ?');
                            $stmt->bind_param('ii', $ta_num, $module_session_num);

                            // Execute MySQL Statement
                            if (!$stmt->execute()) {
                                echo "Allocation Removal Failed";
                                exit();
                            } else {
                                echo "Allocation Successfully Removed";
                                exit();
                            }
                        }
                        
                    } else {
                        echo "Invalid Session ID Format";
                        exit();
                    }
                } else {
                    echo "Invalid User ID Format";
                    exit();
                }

            // Manual Allocation Form
            case "manualAlloc":
                
                // Sanitize Inputs
                $sessionSelect = filter_var($_POST['manualAllocSessionSelect'], FILTER_SANITIZE_NUMBER_INT);
                $usersSelect = array_filter($_POST['manualAllocUserSelect'], 'ctype_digit');

                // Validate Input
                if (validateInput($sessionSelect, inputType::Number) && $sessionSelect > 0) { 
                    if (sessionExists($sessionSelect, $con)) {
                        // Checks that there are 1-5 users entered
                        if (count($usersSelect) > 0 && count($usersSelect) < 6) {
                            // Makes sure all users entered are unique and its an array of numbers
                            if (count($usersSelect) === count(array_flip($usersSelect))) {
                                // Checks that the allocation does not exceed the session ta num
                                if ((sessionTAAllocation($sessionSelect, $con) + count($usersSelect)) >= sessionTALimit($sessionSelect, $con)) {

                                    // Iterates the usersSelect array
                                    foreach ($usersSelect as $user) {
                                        // Validates input
                                        if (validateInput($user, inputType::Number) && $user > 0) {
                                            // Checks user exists
                                            if (userExists($user, $con)) {

                                                $sessionInfo = getSessionData($sessionSelect, $con)->fetch_array(MYSQLI_NUM);

                                                // Checks user isn't already allocated to session
                                                if (isAssigned($user, $sessionSelect, $con)) {
                                                    echo getUserName($user, $con) . " is already allocated to this session";
                                                    exit();
                                                }

                                                // Checks if user is free to be allocated to this session
                                                if (!isAvailable($user, $sessionInfo[6], $sessionInfo[7], $sessionInfo[5], $con)) {
                                                    echo getUserName($user, $con) . " is unavailable to be allocated to this session";
                                                    exit();
                                                }

                                            } else {
                                                echo "One or more users entered do not exist";
                                                exit();
                                            } 
                                        } else {
                                            echo "One or more users entered have an invalid ID format";
                                            exit();
                                        }
                                    }

                                    // If all checks are valid assign each user to the session
                                    foreach ($usersSelect as $user) { 

                                        // Execute MySQL Statement
                                        if (!allocateUser($user, $sessionSelect, $con)) {
                                            echo "Allocation Error. Some users may have been allocated!";
                                            exit();
                                        } 
                                    }

                                    echo "Allocation Successful";
                                    exit();    

                                } else {
                                    echo 'Unable to allocate users as it will exceed TA limit of session';
                                    exit();
                                }

                            } else {
                                echo "Duplicate Users Entered";
                                exit();
                            }
                        } else {
                            echo "Invalid Number of Users Entered";
                            exit();
                        }
                    } else {
                        echo "Invalid Session ID";
                        exit();
                    }
                } else {
                    echo "Invalid Session ID Format";
                    exit();
                }

            // Auto Allocate Form
            case "autoAlloc":

                // Sanitize Inputs
                $sessionSelect = filter_var($_POST['autoAllocSessionSelect'], FILTER_SANITIZE_NUMBER_INT);

                // Validate Input
                if (validateInput($sessionSelect, inputType::Number) && $sessionSelect > 0) { 
                    // Checks session exists
                    if (sessionExists($sessionSelect, $con)) { 

                        // Get session data
                        $sessionInfo = getSessionData($sessionSelect, $con)->fetch_array(MYSQLI_NUM);

                        // Get all users from database
                        $stmt = $con->prepare('SELECT ta_num from teaching_assistants WHERE admin=0 ORDER BY ta_num ASC');
                        $stmt->execute();
                        $userrows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

                        $aval = [];
                        // Check each user from database if they are available
                        foreach ($userrows as $row) {
                            if(isAvailable($row['ta_num'], $sessionInfo[6], $sessionInfo[7], $sessionInfo[5], $con)) {
                                // If they are available add the user to a list
                                array_push($aval, $row['ta_num']);
                            }
                        }

                        // If list of users available is equal to or less than zero echo error message and exit
                        if (count($aval) <= 0) {
                            echo "No users are available to be allocated to this module";
                            exit();
                        }

                        // Get session TA limit
                        $stmt = $con->prepare('SELECT * FROM assigned_to WHERE module_session_num = ?');
                        $stmt->bind_param('i', $sessionSelect);
                        $stmt->execute();
                        $result = $stmt->get_result();

                        // If session is already fully allocated echo error message
                        if ($result->num_rows >= $sessionInfo[4]) {
                            echo "Session is already fully allocated";
                            exit();
                        } 

                        // Session ta allocation minus users already allocated to session
                        $spaceLeft = $sessionInfo[4] - $result->num_rows;

                        // If list of users available is greater than remaining session space randomly allocate
                        if (count($aval) > $spaceLeft) {
                            // Loops for the amount of time there is space left
                            for ($i = 0 ; $i < $spaceLeft; $i++){ 
                                // Selects random user from aval array and allocates them to the module
                                if (!allocateUser($aval[rand(0, (count($aval) - 1))], $sessionSelect, $con)) {
                                    echo "Allocation Error. Some users may have been allocated!";
                                    exit();
                                } 
    
                            }

                            // Echo successful allocation of all remaining spaces of session
                            echo "Automatic Allocation Successful";
                            exit();
                        }
                        // If list of users available is equal to remaining session space. Allocate those users
                        else if (count($aval) == $spaceLeft) {
                            foreach ($aval as $user) {
                                // Allocate user to module
                                if (!allocateUser($aval[rand(0, (count($aval) - 1))], $sessionSelect, $con)) {
                                    echo "Allocation Error. Some users may have been allocated!";
                                    exit();
                                } 
                            }

                            // Echo successful allocation of all remaining spaces of session
                            echo "Automatic Allocation Successful";
                            exit();
                        } 
                        // List of users available is less than remaining session space. Allocate the users and echo message explaining
                        else {
                            foreach ($aval as $user) {
                                // Allocate user to module
                                if (!allocateUser($aval[rand(0, (count($aval) - 1))], $sessionSelect, $con)) {
                                    echo "Allocation Error. Some users may have been allocated!";
                                    exit();
                                } 
                            }

                            // Echo successful allocation of all remaining spaces of session
                            echo "Allocation partially successful. Unable to fill all spaces due to limited availability of users";
                            exit();
                        }

                    } else {
                        echo "Invalid Session ID";
                        exit();
                    }
                } else {
                    echo "Invalid Session ID Format";
                    exit();
                }

            // Unknown Form ID
            default:
                echo "Unknown Form Submitted";
                exit();
        }
    } catch (Exception $e) {
        echo "Unknown Error";
    }
?>