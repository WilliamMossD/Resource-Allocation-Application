<?php 

/*
 * utilities.php file for the Individual Project (University of Sussex 2023) 
 * Author: William Moss (235319)
 *
 * Contains utility functions used by multiple php files
 */

    require_once(__DIR__ . '/../../vendor/autoload.php');

    enum inputType {
        case Name;
        case Text;
        case Number;
        case Email;
        case URL;
        case HHMM;
    }

    // Load .env file
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../../config');
    $dotenv->load();
    $dotenv->required(['HOST', 'USER', 'PASSWORD', 'DATABASE']);

    function mysqliConnect() {
        try {
            return mysqli_connect($_ENV['HOST'], $_ENV['USER'], $_ENV['PASSWORD'], $_ENV['DATABASE']);
        } catch (Exception $e) {
            // Redirect back to root
            header('Location: index.html');
            exit();
        }
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

    // Returns true if user is admin false otherwise
    function isUserAdmin($id, $conn) {
        $stmt = $conn->prepare("SELECT admin FROM teaching_assistants WHERE ta_num=? AND admin=1");
        $stmt->bind_param('i', $id);

        // Executes the statement and stores the result
        $stmt->execute();
        $result = $stmt->get_result();

        return $result->num_rows;

        if ($result->num_rows == 0) {
            return false;
        } 
        return true;
    }

    // Returns the user ID associated with the email
    function getUserIDByEmail($email, $conn) {
        $stmt = $conn->prepare('SELECT ta_num FROM teaching_assistants WHERE email = ?');
        $stmt->bind_param('i', $email);

        // Executes the statement and stores the result
        $stmt->execute();
        $result = $stmt->get_result()->fetch_array(MYSQLI_ASSOC);
        
        return $result['ta_num'];
    }

    // Gets user data by ID - Does not return password column
    function getUserDataByEmail($email, $conn) {
        $stmt = $conn->prepare('SELECT ta_num, fname, lname, email, admin FROM teaching_assistants WHERE email = ?');
        $stmt->bind_param('i', $email);

        // Executes the statement and stores the result
        $stmt->execute();
        $result = $stmt->get_result();
        
        return $result;
    }

    // Gets user name by user ID
    function getUserName($id, $conn) {
        $stmt = $conn->prepare('SELECT fname, lname FROM teaching_assistants WHERE ta_num = ?');
        $stmt->bind_param('i', $id);

        // Executes the statement and stores the result
        $stmt->execute();
        $result = $stmt->get_result()->fetch_array(MYSQLI_NUM);

        return $result[0] . " " . $result[1];

    }

    // Gets module name by ID
    function getModuleName($id, $conn) {
        $stmt = $conn->prepare('SELECT module_name FROM modules WHERE module_num = ?');
        $stmt->bind_param('i', $id);

        // Executes the statement and stores the result
        $stmt->execute();
        $result = $stmt->get_result()->fetch_array(MYSQLI_NUM);

        return $result[0];
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

    // Returns the module num of the session by the session ID
    function getModuleNumBySession($id, $conn) {
        $stmt = $conn->prepare('SELECT module_num FROM module_sessions WHERE module_session_num = ?');
        $stmt->bind_param('i', $id);

        // Executes the statement and stores the result
        $stmt->execute();
        $result = $stmt->get_result()->fetch_array(MYSQLI_NUM);

        return $result[0];
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

    // Checks if user exists
    function userExistsByEmail($email, $conn) {
        $stmt = $conn->prepare("SELECT email FROM teaching_assistants WHERE email=?");
        $stmt->bind_param('s', $email);

        // Executes the statement and stores the result
        $stmt->execute();
        $result = $stmt->get_result();

        return $result->num_rows;

        if ($result->num_rows == 0) {
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

    // Returns the number of TAs currently allocated to a session
    function sessionTAAllocation($sessionID, $conn) {
        return getSessionAllocation($sessionID, $conn)->num_rows;
    }

    // Returns session allocation by ID
    function getSessionAllocation($sessionID, $conn) {
        // Prepare MySQL Statement
        $stmt = $conn->prepare("SELECT teaching_assistants.ta_num AS 'User ID', teaching_assistants.fname AS 'First Name', teaching_assistants.lname AS 'Last Name', assigned_to.ta_num, assigned_to.module_session_num FROM teaching_assistants, assigned_to WHERE teaching_assistants.ta_num = assigned_to.ta_num AND assigned_to.module_session_num=?");
        $stmt->bind_param('i', $sessionID);

        // Execute MySQL Statement
        $stmt->execute();
        return $stmt->get_result();
    }

    // Returns an array containing the session IDs of sessions assigned to a module
    function getModuleSessions($moduleID, $conn) {
        $stmt = $conn->prepare("SELECT module_session_num FROM module_sessions WHERE module_num = ? ORDER BY module_session_num ASC");
        $stmt->bind_param('i', $moduleID);
        $stmt->execute();
        return $stmt->get_result();
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

    // Checks to see if user is available between a set time by checking current sessions they are assigned to   
    // True if they are available, false if not   
    function isAvailable($userID, $starttime, $endtime, $day, $conn) {
        
        $stmt = $conn->prepare('SET @start = ?');
        $stmt->bind_param('s', $starttime);
        $stmt->execute();

        $stmt = $conn->prepare('SET @end = ?');
        $stmt->bind_param('s', $endtime);
        $stmt->execute();

        $stmt = $conn->prepare('SELECT CASE WHEN (module_sessions.session_start > @start AND module_sessions.session_start < @end) or (module_sessions.session_end > @start AND module_sessions.session_end < @end) or (module_sessions.session_start <= @start AND module_sessions.session_end >= @end) or (module_sessions.session_start > @start AND module_sessions.session_end < @end) THEN "true" ELSE "false" END AS overlap FROM module_sessions, assigned_to WHERE module_sessions.module_session_num = assigned_to.module_session_num AND module_sessions.session_day = ? AND assigned_to.ta_num = ?');
        $stmt->bind_param('si', $day, $userID);

        $stmt->execute();
        $result = $stmt->get_result();
        $overlap = $result->fetch_all(MYSQLI_ASSOC);

        if ($result->num_rows == 0) {
            return true;
        } 

        foreach ($overlap as $row) {
            if ($row['overlap'] == 'true') {
                return false;
            }
        }

        return true;
    }

    // Checks to see if user is available between a set time by checking current sessions they are assigned to
    // returns array containing the session_IDs of the session they are assigned to during that time   
    function isAvailableModule($userID, $starttime, $endtime, $day, $conn) {
        $stmt = $conn->prepare('SET @start = ?');
        $stmt->bind_param('s', $starttime);
        $stmt->execute();

        $stmt = $conn->prepare('SET @end = ?');
        $stmt->bind_param('s', $endtime);
        $stmt->execute();

        $stmt = $conn->prepare('SELECT CASE WHEN (module_sessions.session_start > @start AND module_sessions.session_start < @end) or (module_sessions.session_end > @start AND module_sessions.session_end < @end) or (module_sessions.session_start <= @start AND module_sessions.session_end >= @end) or (module_sessions.session_start > @start AND module_sessions.session_end < @end) THEN module_sessions.module_session_num END AS module_session_num FROM module_sessions, assigned_to WHERE module_sessions.module_session_num = assigned_to.module_session_num AND module_sessions.session_day = ? AND assigned_to.ta_num = ?');
        $stmt->bind_param('si', $day, $userID);

        $stmt->execute();
        $result = $stmt->get_result();
        $sessions = $result->fetch_all(MYSQLI_ASSOC);

        $session_IDs = array();

        foreach($sessions as $session) {
            if ($session['module_session_num'] != NULL) {
                array_push($session_IDs, $session['module_session_num']);
            }
        }

        return $session_IDs;
    }

    // Allocates user to session
    function allocateUser($userID, $sessionID, $conn) {

        // Prepare MySQL Statement
        $stmt = $conn->prepare('INSERT INTO assigned_to (ta_num, module_session_num) VALUES (?, ?)');
        $stmt->bind_param('ii', $userID, $sessionID);

        return $stmt->execute();
    }
    
    /**
     * Generates a HTML title using the string as the title
     *
     * @param string $string String of text
     * @param int $font_size Font size variable from 1 to 6
     * @return string String containing a HTML title
     */
    function generateText($string, $font_size) {
        return '<p class="fs-' . $font_size .'">' . $string . '</p>';
    }
    
    /**
     * Generates a HTML table from an associative array
     *
     * @param  mixed $associativeArray Must be an array containing array(s) which contain (key,value) pairs and the keys being the column headers
     * @param  mixed $fields Must be an 1D array containing the keys in the associative array
     * @return string String containing a HTML table
     */        
    function generateTable($associativeArray, $fields) {
        
        $keys = array();

        // Get names of keys
        foreach($fields as $field) {
            array_push($keys, $field->name);
        }

        // Generate HTML table
        $table = '<table class="table text-center">';

        // Add header row to table
        $table = $table . '<thead><tr>';
        foreach($keys as $key) {
            $table = $table . '<th scope="col">' . $key . '</th>';
        }
        $table = $table . '</tr></thead>';

        // Add row data to table
        $table = $table . '<tbody>';
        foreach($associativeArray as $row){
            $table = $table . '<tr>';
            foreach($keys as $key) {
                $table = $table . '<td>'.$row[$key].'</td>';
            }
            $table = $table . '</tr>';
        }
        $table = $table . '</tbody>';

        // Close table
        $table = $table . '</table>';
        
        return $table;

    }

        /**
     * Generates a HTML table from an associative array and adds the removal button to the end which uses the last two keys
     *
     * @param  mixed $associativeArray Must be an array containing array(s) which contain (key,value) pairs and the keys being the column headers
     * @param  mixed $fields Must be an 1D array containing the keys in the associative array
     * @return string String containing a HTML table
     */    
    function generateAllocTable($associativeArray, $fields) {
        
        $keys = array();

        // Get names of keys
        foreach($fields as $field) {
            array_push($keys, $field->name);
        }

        // Pop last two keys
        array_pop($keys);
        array_pop($keys);

        // Push actions col
        array_push($keys, 'Actions');

        // Generate HTML table
        $table = '<table class="table text-center">';

        // Add header row to table
        $table = $table . '<thead><tr>';
        foreach($keys as $key) {
            $table = $table . '<th scope="col">' . $key . '</th>';
        }
        $table = $table . '</tr></thead>';

        // Add row data to table
        $table = $table . '<tbody>';
        foreach($associativeArray as $row){
            $table = $table . '<tr>';
            foreach($keys as $key) {
                if ($key == 'Actions') {
                    $table = $table . '<td><button type="button" title="Remove Allocation" onclick="removeAlloc(' . $row['ta_num']  . ',' . $row['module_session_num'] . ')' . '" class="btn btn-danger"><i class="fa-solid fa-x"></i></button></td>';
                } else {
                    $table = $table . '<td>'.$row[$key].'</td>';
                }
            }
            $table = $table . '</tr>';
        }
        $table = $table . '</tbody>';

        // Close table
        $table = $table . '</table>';
        
        return $table;

    }

 ?>
