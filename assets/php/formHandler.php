<?php 

/*
 * formHandler.php file for the Individual Project (University of Sussex 2023) 
 * Author: William Moss (235319)
 *
 * Handles form submissions
 */

    enum inputType {
        case Name;
        case Text;
        case Number;
        case Email;
        case URL;
        case HHMM;
    }


    // Sanitize Function 
    function sanitizeInput($input){
        return htmlentities($input, ENT_QUOTES, 'UTF-8');
    }

    // Validation Function
    function validateInput($input, inputType $it) {
        switch ($it) {
            case inputType::Name:
                // Makes sure name only contains alphabetic characters and has size range of 2 to 50
                if (preg_match("/^[a-zA-Z -']{2,50}$/", $input)) {
                    return true;
                } else {
                    return false;
                }
            case inputType::Text:
                // Checks text uses the UTF-8 encoding and has a max size of 100
                if (mb_check_encoding($input, 'UTF-8') && (strlen($input) <= 100)) {
                    return true;
                } else {
                    return false;
                }
            case inputType::Number:
                // Checks to see that input is a number
                if (is_numeric($input)) {
                    return true;
                } else {
                    return false;
                }
            case inputType::Email:
                // Validates email
                if (filter_var($input, FILTER_VALIDATE_EMAIL)) {
                    return true;
                } else {
                    return false;
                }
            case inputType::URL:
                // Validates URL
                if (filter_var($input, FILTER_VALIDATE_URL)) {
                    return true;
                } else {
                    return false;
                }
            case inputType::HHMM:
                // Validates HH:MM
                // Checks MM is either 30 or 00
                if (preg_match("/^([0-1]?[0-9]|2[0-3]):[03][0]$/",$input)) {
                    return true;
                } else {
                    return false;
                }
            default:
                return false;
        }
    }

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
                if (validateInput($firstNameInput, inputType::Name) && validateInput($lastNameInput, inputType::Name) && validateInput($emailInput, inputType::Email)){
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

            // editUser Form 
            case "editUser":
                break;

            // deleteUser Form - [deleteUserSelect] / [Num]
            case "deleteUser":

                $deleteUserSelect = $_POST['deleteUserSelect'];

                // Validate Input
                if (validateInput($deleteUserSelect, inputType::Number) && $deleteUserSelect > 0) {

                    // Validates that the user entered refrences an actual user in the database
                    $stmt = $con->prepare('SELECT ta_num FROM teaching_assistants WHERE ta_num = ?');
                    $stmt->bind_param('i', $deleteUserSelect);

                    // Executes the statement and stores the result
                    $stmt->execute();
                    $stmt->store_result();
                    
                    if ($stmt->num_rows == 0) {
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

            // editModule Form
            case "editModule":
                break;

            // deleteModule Form -  [deleteModuleSelect] / [Text] / [50]
            case "deleteModule":

                $deleteModuleSelect = $_POST['deleteModuleSelect'];

                // Validate Input
                if (validateInput($deleteModuleSelect, inputType::Number) && $deleteModuleSelect > 0) {

                    // Validates that the module_num entered refrences an actual module in the database
                    $stmt = $con->prepare('SELECT module_num FROM modules WHERE module_num = ?');
                    $stmt->bind_param('i', $deleteModuleSelect);

                    // Executes the statement and stores the result
                    $stmt->execute();
                    $stmt->store_result();
                    
                    if ($stmt->num_rows == 0) {
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
                    echo "Invalid Module ID";
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

                    if (!$result) {
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
                            echo "Invalid Date";
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

            // Unknown Form ID
            default:
                echo "Unknown Form Submitted";
                exit();
        }
    } catch (Exception $e) {
        echo "Unknown Error";
        echo $e;
    }

?>
