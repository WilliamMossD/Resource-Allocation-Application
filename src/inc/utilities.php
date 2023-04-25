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
        case Location;
        case Text;
        case Number;
        case Email;
        case URL;
        case HHMMHour;
        case HHMM;
    }

    // Load .env file
    $dotenv = Dotenv\Dotenv::createImmutable($_SERVER['DOCUMENT_ROOT'] . '/../config');
    $dotenv->load();
    $dotenv->required(['HOST', 'USER', 'PASSWORD', 'DATABASE', 'DOMAIN_NAME']);

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
            case inputType::Location:
                // Makes sure name only contains alphabetic characters and has size range of 2 to 50
                if (preg_match("/^[a-zA-Z -'\d]{2,50}$/", $input)) {
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
            case inputType::HHMMHour:
                // Validates HH:MM
                // Checks MM is either 30 or 00
                if (preg_match("/^([0-1]?[0-9]|2[0-3]):[0][0]$/",$input)) {
                    return true;
                } else {
                    return false;
                }
            case inputType::HHMM:
                // Validates HH:MM
                // Checks MM is either 30 or 00
                if (preg_match("/^([0-1]?[0-9]|2[0-3]):[0-5][0-9]$/",$input)) {
                    return true;
                } else {
                    return false;
                }
            default:
                return false;
        }
    }

    // Returns the Domain name in the env config
    function getDomainName() {
        return $_ENV['DOMAIN_NAME'];
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

    // Gets total seconds for timesheet
    function getTotalSeconds($monStartTime, $monEndTime, $tueStartTime, $tueEndTime, $wedStartTime, $wedEndTime, $thuStartTime, $thuEndTime, $friStartTime, $friEndTime) {
        return (strtotime($monEndTime) - strtotime($monStartTime)) + (strtotime($tueEndTime) - strtotime($tueStartTime)) + (strtotime($wedEndTime) - strtotime($wedStartTime)) + (strtotime($thuEndTime) - strtotime($thuStartTime)) + (strtotime($friEndTime) - strtotime($friStartTime));
    }
    
    // Gets total time for timesheet
    function getTotalTime($seconds) {
        $minutes = (int) (($seconds / 60) % 60);
        $hours = (int) (($seconds / 60) - $minutes) / 60;

        if ($minutes < 10) {
            $minutes = '0' . strval($minutes);
        }

        if ($hours < 10) {
            $hours = '0' . strval($hours);
        }

        return $hours . ':' . $minutes;
    }

    // Checks timesheet exists by ID
    function timesheetExists($id, $conn) {
        $stmt = $conn->prepare('SELECT timesheet_num FROM timesheets WHERE timesheet_num = ?');
        $stmt->bind_param('i', $id);

        // Executes the statement and stores the result
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 0) {
            return false;
        } 
        return true;
    }

    // Gets current status of timesheet
    function getTimesheetStatus($id, $conn) {
        $stmt = $conn->prepare('SELECT status FROM timesheets WHERE timesheet_num = ?');
        $stmt->bind_param('i', $id);

        // Executes the statement and stores the result
        $stmt->execute();
        $result = $stmt->get_result();

        return $result;
    }
    
    // Checks to see if a timesheet for the week and year already exists for the user. Returns true or false
    function timesheetExistsForWeekAndYearForUser($weekInput, $yearInput, $idInput, $conn){
        $stmt = $conn->prepare('SELECT ta_num, week_num, year FROM timesheets WHERE ta_num = ? AND week_num = ? AND year = ? AND (status = "Pending" OR status = "Approved")');
        $stmt->bind_param('iii', $idInput, $weekInput, $yearInput);

        // Executes the statement and stores the result
        $stmt->execute();
        $result = $stmt->get_result();

        // If rows equals 0 no timesheets exist
        if ($result->num_rows != 0) {
            return true;
        }

        return false;
    }

    // Inserts new timesheet in database. Returns !$stmt->execute()
    function insertTimesheet($idInput, $weekInput, $yearInput, $monStartTime, $monEndTime, $tueStartTime, $tueEndTime, $wedStartTime, $wedEndTime, $thuStartTime, $thuEndTime, $friStartTime, $friEndTime, $totalTime, $timesheetTextInput, $conn) {

        $stmt = $conn->prepare('INSERT INTO timesheets (ta_num, week_num, year, monStart, monEnd, tueStart, tueEnd, wedStart, wedEnd, thuStart, thuEnd, friStart, friEnd, total, comments) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
        $stmt->bind_param('iiissssssssssss', $idInput, $weekInput, $yearInput, $monStartTime, $monEndTime, $tueStartTime, $tueEndTime, $wedStartTime, $wedEndTime, $thuStartTime, $thuEndTime, $friStartTime, $friEndTime, $totalTime, $timesheetTextInput);

        // Execute MySQL Statement
        return $stmt->execute();
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
        $table = '<table id="table" class="table text-center">';

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

    function getModulesAssignedToUser($userID, $conn) {
        // Get modules that user is assigned to
        $stmt = $conn->prepare('SELECT DISTINCT modules.module_num, modules.module_name, modules.module_convenor, modules.module_description, modules.link FROM modules, module_sessions, assigned_to WHERE assigned_to.ta_num = ? AND module_sessions.module_session_num = assigned_to.module_session_num AND module_sessions.module_num = modules.module_num
        ');
        $userID = getUserIDByEmail($_SESSION['email'], $conn);
        $stmt->bind_param('i', $userID);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

    }

    function getAllModules($conn){
        // Get rows from module table
        $stmt = $conn->prepare('SELECT * from modules ORDER BY module_num ASC');
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    function generateModuleHTML($modulerows) {
        $html = '';

        foreach ($modulerows as $row) {
            $html .= '<div class="col-4 pb-3">';
                $html .= '<div class="card h-100 bg-primary">';
                    $html .= '<div class="card-header">';
                        $html .= '<h6 class="mt-2 mb-2">' . $row['module_name'] . ' (ID: ' . $row['module_num'] . ')</h6>';
                    $html .= '</div>';
                    $html .= '<div class="card-body">';
                        $html .= '<h6 class="card-subtitle mb-2"><i class="fa-solid fa-chalkboard-user"></i> : '
                        . $row['module_convenor'] . '</h6>';
                        $html .= '<p class="card-text">' . $row['module_description'] . '</p>';
                        $html .= '<a href="' . $row['link'] . '" class="card-link link-info" target="_blank">Canvas Page</a>';
                    $html .= '</div>';
                $html .= '</div>';
            $html .= '</div>';
        }

        return $html;
    }

    function generateModuleList($modulerows) {
        $html = '';

        foreach ($modulerows as $row) {
            $html .= '<option value="'.$row['module_num'].'">'.$row['module_name'].'</option>';
        }

        return $html;
    }

    function getAllSessions($conn) {
        // Get rows from table 
        $stmt = $conn->prepare("SELECT module_sessions.module_session_num, modules.module_name, module_sessions.session_day, DATE_FORMAT(module_sessions.session_start, '%H:%i') AS session_start, DATE_FORMAT(module_sessions.session_end, '%H:%i') AS session_end FROM modules, module_sessions WHERE modules.module_num = module_sessions.module_num ORDER BY module_sessions.module_num ASC");
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    function generateSessionList($sessionrows) {
        $html = '';
        $currentModule = null;

        foreach ($sessionrows as $row) {
            if ($currentModule != $row['module_name']) {
                if ($currentModule != null) {
                    $html .= '</optgroup>';
                }
                $html .= '<optgroup label="' . $row['module_name'] . '">';
                $currentModule = $row['module_name'];
            }
            $html .= '<option value="'.$row['module_session_num'] .'">'.$row['session_day'].', '.$row['session_start']. ' - ' . $row['session_end'] .'</option>';
        }

        $html .= '</optgroup>'; 

        return $html;
    }

    function getAllUsers($conn) {
        // Get rows from teaching assistants table 
        $stmt = $conn->prepare('SELECT ta_num, fname, lname from teaching_assistants ORDER BY ta_num ASC');
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    function generateUserList($tarows) {
        $html = '';

        foreach ($tarows as $row) {
            $html .=  '<option value="'.$row['ta_num'] .'">'.$row['fname'].' '.$row['lname'].'</option>';
        }
        
        return $html;
    }

    function returnHTTPResponse($status, $responseText) {
        http_response_code($status);
        echo $responseText;
    }

 ?>
