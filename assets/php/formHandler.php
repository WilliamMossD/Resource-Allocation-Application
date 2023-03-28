<?php 

/*
 * formHandler.php file for the Individual Project (University of Sussex 2023) 
 * Author: William Moss (235319)
 *
 * Handles form submissions
 */

    require('utilities.php');

    // Database Connection Info
    $HOST = 'localhost';
    $USER = 'mossfree_admin';
    $PASSWORD = 'Btf7@w&7Dhi1';
    $DATABASE = 'mossfree_tutordatabase';

    // DEBUGGING ONLY
    //echo(print_r($_POST));

    // Connect to database
    try {
        $con = mysqli_connect($HOST, $USER, $PASSWORD, $DATABASE);
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
            // addUser Form - [firstNameInput, lastNameInput, emailInput] / [Text, Text, Email] / [50,50,100]
            case "addUser":

                // Sanitize Inputs
                $firstNameInput = sanitizeInput($_POST['firstNameInput']);
                $lastNameInput = sanitizeInput($_POST['lastNameInput']);
                $emailInput = sanitizeInput($_POST['emailInput']);

                // Validate Inputs
                if (validateInput($firstNameInput, inputType::Name)){
                    if (validateInput($lastNameInput, inputType::Name)) {
                        if (validateInput($emailInput, inputType::Email)) {
                            
                            // Prepare MySQL Statment
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

            // selectUser Form 
            case "selectUser":

                $userSelect = $_POST['userSelect'];

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
                $editUserSelect = $_POST['editUserSelect'];

                // Validate Inputs
                if (validateInput($editUserSelect, inputType::Number) && $editUserSelect > 0) {

                    // Checks user exists
                    if (!userExists($editUserSelect, $con)) {
                        echo "Invalid User ID";
                        exit();
                    } 

                    // Validates that the user entered refrences an actual user in the database
                    if (validateInput($editFirstNameInput, inputType::Name)){
                        if (validateInput($editLastNameInput, inputType::Name)) {
                            if (validateInput($editEmailInput, inputType::Email)) {
                                
                                // Prepare MySQL Statment
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
            // selectModule Case
            case "selectModule":

                $moduleSelect = $_POST['moduleSelect'];

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
                $editModuleSelect = $_POST['editModuleSelect'];

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
                                
                                    // Prepare MySQL Statment
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

            // deleteUser Form - [deleteUserSelect] / [Num]
            case "deleteUser":

                $deleteUserSelect = $_POST['deleteUserSelect'];

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

            // addModule Form - [moduleNameInput, moduleConInput, moduleDesInput, moduleLinkInput] / [Text, Text, Text, URL] / [50, 50, 100, 2048]
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
                            
                                // Prepare MySQL Statment
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

            // deleteModule Form -  [deleteModuleSelect] / [Text] / [50]
            case "deleteModule":

                $deleteModuleSelect = $_POST['deleteModuleSelect'];

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

            // viewSession Form - [sessionsModuleSelect] / [Num] / [N/A]
            case "viewSession":

                $sessionsModuleSelect = $_POST['sessionsModuleSelect'];

                // Validate Input
                if (validateInput($sessionsModuleSelect, inputType::Number) && $sessionsModuleSelect > 0) {

                    // Validates that the module_num entered refrences an actual module in the database
                    $stmt = $con->prepare('SELECT module_num FROM modules WHERE module_num = ?');
                    $stmt->bind_param('i', $sessionsModuleSelect);

                    // Executes the statement and stores the result
                    $stmt->execute();
                    $stmt->store_result();
                    
                    if ($stmt->num_rows == 0) {
                        echo "Invalid Module ID";
                        exit();
                    } 

                    // Prepare MySQL Statement
                    $stmt = $con->prepare('SELECT module_session_num, num_of_ta, session_day, session_start, session_end, session_type, session_location FROM module_sessions WHERE module_num=?');
                    $stmt->bind_param('i', $sessionsModuleSelect);

                    // Execute MySQL Statement
                    $stmt->execute();
                    $result = $stmt->get_result();

                    if ($result->num_rows == 0) {
                        echo "No Sessions Exist";
                        exit();
                    } else {
                        $i = 0;
                        while($row = $result->fetch_array(MYSQLI_ASSOC)) {
                            $rows[$i] = [$row["module_session_num"], $row["num_of_ta"], $row["session_day"], $row["session_start"], $row["session_end"], $row["session_type"], $row["session_location"]];
                            $i++;
                        }
                        echo json_encode($rows);
                        exit();
                    }

                } else {
                    echo "Invalid Module ID";
                    exit();
                }

            // addSession Form - [sessionModuleNameInput, moduleLocInput, sessionTypeSelect, sessionTAInput, sessionDaySelect, sessionStartTimeInput, sessionEndTimeInput]
            //                   [Number, Name, [Lab/Teaching/Other], Number, [Monday-Friday], HHMM, HHMM]
            //                   [N/A, 50, N/A, 0 < number < 5, N/A, N/A, N/A ]
            //                   sessionEndTimeInput must be greater than sessionStartTimeInput by atleast 30 minutes and less than 21:00
            //                   sessionStartTimeInput must be equal to or greater than 8:00 and less than 20:30
            //                   sessionStartTimeInput and sessionEndTimeInput must be in 30 minute blocks
            case "addSession":

                // Sanitize Inputs
                $sessionModuleNameInput = $_POST['sessionModuleNameInput'];
                $moduleLocInput = sanitizeInput($_POST['moduleLocInput']);
                $sessionTypeSelect = sanitizeInput($_POST['sessionTypeSelect']);
                $sessionTAInput = $_POST['sessionTAInput'];
                $sessionDaySelect = sanitizeInput($_POST['sessionDaySelect']);
                $sessionStartTimeInput = $_POST['sessionStartTimeInput'];
                $sessionEndTimeInput = $_POST['sessionEndTimeInput'];

                if (validateInput($sessionModuleNameInput, inputType::Number) && $sessionModuleNameInput > 0) {
                    
                    // Validates that the module_num entered refrences an actual module in the database
                    $stmt = $con->prepare('SELECT module_num FROM modules WHERE module_num = ?');
                    $stmt->bind_param('i', $sessionModuleNameInput);

                    // Executes the statement and stores the result
                    $stmt->execute();
                    $stmt->store_result();
                    
                    if ($stmt->num_rows == 0) {
                        echo "Invalid Module Number";
                        exit();
                    } 


                    if (validateInput($moduleLocInput, inputType::Name)) {
                        if (in_array($sessionTypeSelect, ["Lab", "Teaching", "Other"])) {
                            if (validateInput($sessionTAInput, inputType::Number) && (0 < $sessionTAInput) && ($sessionTAInput <= 5)) {
                                if (in_array($sessionDaySelect, ["Monday", "Tuesday", "Wednesday", "Thursday", "Friday"])) {
                                    if (validateInput($sessionStartTimeInput, inputType::HHMM) && (strtotime("08:00") <= strtotime($sessionStartTimeInput)) && (strtotime($sessionStartTimeInput) <= strtotime("20:30"))) {
                                        if (validateInput($sessionEndTimeInput, inputType::HHMM) && (strtotime("08:30") <= strtotime($sessionEndTimeInput)) && (strtotime($sessionEndTimeInput) <= strtotime("21:00")) && (strtotime($sessionEndTimeInput) > strtotime($sessionStartTimeInput))) {
                                            
                                            // Prepare MySQL Statment
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
                $sessionSelect = $_POST['sessionSelect'];
    
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
                $editSessionSelect = $_POST['editSessionSelect'];
                $editSessionModuleNameInput = $_POST['editSessionModuleNameInput'];
                $editSessionLocInput = sanitizeInput($_POST['editSessionLocInput']);
                $editSessionTypeSelect = sanitizeInput($_POST['editSessionTypeSelect']);
                $editSessionTAInput = $_POST['editSessionTAInput'];
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
                        
                        // Validates that the module_num entered refrences an actual module in the database
                        $stmt = $con->prepare('SELECT module_num FROM modules WHERE module_num = ?');
                        $stmt->bind_param('i', $editSessionModuleNameInput);

                        // Executes the statement and stores the result
                        $stmt->execute();
                        $stmt->store_result();
                        
                        if ($stmt->num_rows == 0) {
                            echo "Invalid Module Number";
                            exit();
                        } 


                        if (validateInput($editSessionLocInput, inputType::Name)) {
                            if (in_array($editSessionTypeSelect, ["Lab", "Teaching", "Other"])) {
                                if (validateInput($editSessionTAInput, inputType::Number) && (0 < $editSessionTAInput) && ($editSessionTAInput <= 5)) {
                                    if (in_array($editSessionDaySelect, ["Monday", "Tuesday", "Wednesday", "Thursday", "Friday"])) {
                                        if (validateInput($editSessionStartTimeInput, inputType::HHMM) && (strtotime("08:00") <= strtotime($editSessionStartTimeInput)) && (strtotime($editSessionStartTimeInput) <= strtotime("20:30"))) {
                                            if (validateInput($editSessionEndTimeInput, inputType::HHMM) && (strtotime("08:30") <= strtotime($editSessionEndTimeInput)) && (strtotime($editSessionEndTimeInput) <= strtotime("21:00")) && (strtotime($editSessionEndTimeInput) > strtotime($editSessionStartTimeInput))) {
                                                
                                                // Prepare MySQL Statment
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

                $deleteSessionSelect = $_POST['deleteSessionSelect'];

                // Validate Input
                if (validateInput($deleteSessionSelect, inputType::Number) && $deleteSessionSelect > 0) {

                    // Checks session exisits
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
            
            // View Allocation by Session Form
            case "viewAllocationBySession":

                $viewAllocSessionSelect = $_POST['viewAllocSessionSelect'];

                // Validate Input
                if (validateInput($viewAllocSessionSelect, inputType::Number) && $viewAllocSessionSelect > 0) {

                    // Checks session exisits
                    if (!sessionExists($viewAllocSessionSelect, $con)) {
                        echo "Invalid Session ID";
                        exit();
                    } 

                    // Prepare MySQL Statement
                    $stmt = $con->prepare('SELECT teaching_assistants.ta_num, teaching_assistants.fname, teaching_assistants.lname, assigned_to.ta_num, assigned_to.module_session_num FROM teaching_assistants, assigned_to WHERE teaching_assistants.ta_num = assigned_to.ta_num AND assigned_to.module_session_num=?');
                    $stmt->bind_param('i', $viewAllocSessionSelect);

                    // Execute MySQL Statement
                    $stmt->execute();
                    $result = $stmt->get_result();

                    if ($result->num_rows == 0) {
                        echo "No Allocation Exists";
                        exit();
                    } else {
                        $i = 0;
                        while($row = $result->fetch_array(MYSQLI_ASSOC)) {
                            $rows[$i] = [$row["ta_num"], $row["fname"], $row["lname"], $row["ta_num"], $row["module_session_num"]];
                            $i++;
                        }
                        echo json_encode($rows);
                        exit();
                    }

                } else {
                    echo "Invalid Session ID Format";
                    exit();
                }

            // View Allocation by User Form
            case "viewAllocationByUser":

                $viewAllocUserSelect = $_POST['viewAllocUserSelect'];

                // Validate Input
                if (validateInput($viewAllocUserSelect, inputType::Number) && $viewAllocUserSelect > 0) {

                    // Checks session exisits
                    if (!userExists($viewAllocUserSelect, $con)) {
                        echo "Invalid User ID";
                        exit();
                    } 

                    // Prepare MySQL Statement
                    $stmt = $con->prepare('SELECT modules.module_name, module_sessions.session_day, module_sessions.session_start, module_sessions.session_end, module_sessions.session_type, module_sessions.session_location, assigned_to.ta_num, assigned_to.module_session_num FROM modules, module_sessions, assigned_to WHERE modules.module_num = module_sessions.module_num AND module_sessions.module_session_num = assigned_to.module_session_num AND assigned_to.ta_num=?');
                    $stmt->bind_param('i', $viewAllocUserSelect);

                    // Execute MySQL Statement
                    $stmt->execute();
                    $result = $stmt->get_result();

                    if ($result->num_rows == 0) {
                        echo "No Allocation Exists";
                        exit();
                    } else {
                        $i = 0;
                        while($row = $result->fetch_array(MYSQLI_ASSOC)) {
                            $rows[$i] = [$row["module_name"], $row["session_day"], $row["session_start"], $row["session_end"], $row["session_type"], $row["session_location"], $row["ta_num"], $row["module_session_num"]];
                            $i++;
                        }
                        echo json_encode($rows);
                        exit();
                    }

                } else {
                    echo "Invalid User ID Format";
                    exit();
                }

            // Remove Allocation Form
            case "removeAlloc":

                $ta_num = $_POST['ta_num'];
                $module_session_num = $_POST['module_session_num'];

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
            // Make sure user is not already allocated. Make sure allocation does not exceed ta limit
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
                                    foreach ($usersSelect as $user) {
                                        // Validates each user entered is in the correct format, exists and isn't already allocated 
                                        if (validateInput($user, inputType::Number) && $user > 0) {
                                            if (userExists($user, $con)) {

                                                // Execute MySQL Statement
                                                if (isAssigned($user, $sessionSelect, $con)) {
                                                    echo "User ID " . $user . " Already Allocated";
                                                    exit();
                                                }

                                            } else {
                                                echo "No User Exists With ID " . $user;
                                                exit();
                                            } 
                                        } else {
                                            echo "Invalid User ID Format";
                                            exit();
                                        }
                                    }

                                    // If all checks are valid assign each user to the session
                                    foreach ($usersSelect as $user) { 

                                        // Prepare MySQL Statment
                                        $stmt = $con->prepare('INSERT INTO `assigned_to` (`ta_num`, `module_session_num`) VALUES (?, ?)');
                                        $stmt->bind_param('ii', $user, $sessionSelect);

                                        // Execute MySQL Statement
                                        if (!$stmt->execute()) {
                                            echo "Allocation Error. Some Users May Have Been Allocated!";
                                            exit();
                                        } 
                                    }

                                    echo "Allocation Successful";
                                    exit();

                                } else {
                                    echo 'Allocation Will Exceed TA Limit';
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

            // Unknown Form ID
            default:
                echo "Unknown Form Submitted";
                exit();
        }
    } catch (Exception $e) {
        echo "Unknown Error";
    }
?>