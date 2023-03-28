<?php 

/*
 * utilities.php file for the Individual Project (University of Sussex 2023) 
 * Author: William Moss (235319)
 *
 * Contains utility functions used by multiple php files
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
        return htmlspecialchars($input, ENT_QUOTES, 'UTF-8');
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
                if (filter_var($input, FILTER_VALIDATE_INT)) {
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

    // Gets user data by ID - Does not return password column
    function getUserData($id, $conn) {
        $stmt = $conn->prepare('SELECT ta_num, fname, lname, email, admin FROM teaching_assistants WHERE ta_num = ?');
        $stmt->bind_param('i', $id);

        // Executes the statement and stores the result
        $stmt->execute();
        $result = $stmt->get_result();
        
        return $result;
    }

    // Gets module data by ID
    function getModuleData($id, $conn) {
        $stmt = $conn->prepare('SELECT * FROM modules WHERE module_num = ?');
        $stmt->bind_param('i', $id);

        // Executes the statement and stores the result
        $stmt->execute();
        $result = $stmt->get_result();
        
        return $result;
    }

    // Gets session data by ID
    function getSessionData($id, $conn) {
        $stmt = $conn->prepare("SELECT module_session_num, module_num, session_location, session_type, num_of_ta, session_day, DATE_FORMAT(session_start, '%H:%i') AS session_start, DATE_FORMAT(session_end, '%H:%i') AS session_end FROM module_sessions WHERE module_session_num = ?");
        $stmt->bind_param('i', $id);

        // Executes the statement and stores the result
        $stmt->execute();
        $result = $stmt->get_result();
        
        return $result;
    }

    // Checks if user exists
    function userExists($id, $conn) {
        if (getUserData($id, $conn)->num_rows == 0) {
            return false;
        } 
        return true;
    }

    // Checks if module exists
    function moduleExists($id, $conn) {
        if (getModuleData($id, $conn)->num_rows == 0) {
            return false;
        } 
        return true;
    }

    // Checks if session exists
    function sessionExists($id, $conn) {
        if (getSessionData($id, $conn)->num_rows == 0) {
            return false;
        } 
        return true;
    }

    // Returns the TA limit of a session
    function sessionTALimit($sessionID, $conn) {
        $stmt = $conn->prepare('SELECT num_of_ta FROM module_sessions WHERE module_session_num = ?');
        $stmt->bind_param('i', $sessionID);

        // Executes the statement and stores the result
        $stmt->execute();
        $result = $stmt->get_result();
        
        return $result;
    }

    // Returns the number of TAs currently allocated
    function sessionTAAllocation($sessionID, $conn) {
        $stmt = $conn->prepare('SELECT module_session_num FROM assigned_to WHERE module_session_num=?');
        $stmt->bind_param('i', $sessionID);

        $stmt->execute();
        $result = $stmt->get_result();

        return $result->num_rows;
    }

    // Checks if user is assigned to a session
    function isAssigned($userID, $sessionID, $conn) {
        $stmt = $conn->prepare('SELECT * FROM assigned_to WHERE ta_num=? AND module_session_num=?');
        $stmt->bind_param('ii', $userID, $sessionID);

        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 0) {
            return false;
        }
        return true;
    }
 ?>
