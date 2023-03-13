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
                if (preg_match("/^[a-zA-Z]{2,50}$/", $input)) {
                    return true;
                } else {
                    return false;
                }
                break;
            case inputType::Text:
                // Checks text uses the UTF-8 encoding and has a max size of 100
                if (mb_check_encoding($input, 'UTF-8') && (strlen($input) <= 100)) {
                    return true;
                } else {
                    return false;
                }
                break;
            case inputType::Number:
                // Checks to see that input is a number
                if (is_numeric($input)) {
                    return true;
                } else {
                    return false;
                }
                break;
            case inputType::Email:
                // Validates email
                if (filter_var($input, FILTER_VALIDATE_EMAIL)) {
                    return true;
                } else {
                    return false;
                }
                break;
            case inputType::URL:
                // Validates URL
                if (filter_var($input, FILTER_VALIDATE_URL)) {
                    return true;
                } else {
                    return false;
                }
                break;
            case inputType::HHMM:
                // Validates HH:MM
                if (preg_match("^([0-1]?[0-9]|2[0-3]):[0-5][0-9]$",$input)) {
                    return true;
                } else {
                    return false;
                }
                break;
            default:
                return false;
                break;
        }
    }

    // Database Connection Info
    $HOST = 'localhost';
    $USER = 'mossfree_admin';
    $PASSWORD = 'Btf7@w&7Dhi1';
    $DATABASE = 'mossfree_tutordatabase';

    // DEBUGGING ONLY
    echo(print_r($_POST));

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
                                echo "Success";
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

                break;

            // editUser Form 
            case "editUser":
                break;

            // deleteUser Form - [deleteUserSelect] / [Text] / [50]
            case "deleteUser":
                break;

            // addModule Form - [moduleNameInput, ModuleConInput, ModuleDesInput, ModuleLinkInput] / [Text, Text, Text, URL] / [50, 50, 100, 2048]
            case "addModule":
                break;

            // editModule Form
            case "editModule":
                break;

            // deleteModule Form -  [deleteModuleSelect] / [Text] / [50]
            case "deleteModule":
                break;

            // viewSession Form
            case "viewSession":
                break;

            // addSession Form
            case "addSession":
                break;

            // Unknown Form ID
            default:
                echo "Error: Unknown Form ID";
                exit();
                break;
        }
    } catch (Exception $e) {
        echo "Unknown Error";
    }

?>
