<?php

/*
 * timetable.php file for the Individual Project (University of Sussex 2023) 
 * Author: William Moss (235319)
 *
 * HTML template for the timetable table 
 */

    require_once(__DIR__ . '/../inc/utilities.php');

    if(isset($_SESSION['email']) && isset($_SESSION['loggedin']) && $_SESSION['loggedin']) {
        $userID = getUserIDByEmail($_SESSION['email'], $con);
        if (!isUserAdmin(getUserIDByEmail($_SESSION['email'], $con), $con) && userExists($userID, $con) && ($con = mysqliConnect())) {
            // Get sessions user is allocated to
            $stmt = $con->prepare("SELECT modules.module_name AS 'Module Name', module_sessions.session_day AS 'Day', DATE_FORMAT(module_sessions.session_start, '%H:%i') AS 'Session Start', DATE_FORMAT(module_sessions.session_end, '%H:%i') AS 'Session End', module_sessions.session_type AS 'Session Type', module_sessions.session_location AS 'Location', assigned_to.ta_num, assigned_to.module_session_num FROM modules, module_sessions, assigned_to WHERE modules.module_num = module_sessions.module_num AND module_sessions.module_session_num = assigned_to.module_session_num AND assigned_to.ta_num=?");
            $stmt->bind_param('i', $userID);

            $stmt->execute();
            $result = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

            // Echo table div and table head
            echo '
            <table id="timetable" class="table timetable text-center fw-normal table-striped table-bordered table-hover">
                <thead>
                    <tr>
                        <th class="first-cell" scope="col"></th>
                        <th scope="col">Monday</th>
                        <th scope="col">Tuesday</th>
                        <th scope="col">Wednesday</th>
                        <th scope="col">Thursday</th>
                        <th scope="col">Friday</th>
                    </tr>
                </thead>
                <tbody>';
            
            // Generate table rows from 8am to 9pm
            for ($i = 8; $i <= 21; $i++) {
                echo '<tr>';
                    echo '<th scope="row">';
                    if ($i < 10) {
                        echo '0';
                        echo $i;
                    } else {
                        echo $i;
                    }
                    echo ':00</th>';

                    $start = $i . ':00';
                    $end = $i+1 . ':00';

                    // Monday
                    echo '<td id="' . $i . 'Mon">';
                        $sessions = isAvailableModule($userID, $start, $end , 'Monday', $con);

                        foreach($sessions as $session) {
                            $session_data = getSessionData($session, $con)->fetch_array(MYSQLI_ASSOC);
                            echo '<div class="col p-1 bg-green">';
                            echo getModuleName($session_data['module_num'], $con) . '<br>';
                            echo '<i class="fa-solid fa-location-dot"></i> : ' . $session_data['session_location'] . '<br>';
                            echo '<i class="fa-solid fa-chalkboard"></i> : ' . $session_data['session_type'];
                            echo '</div>';
                        }
                    echo '</td>';

                    // Tuesday
                    echo '<td id="' . $i . 'Tue">';
                        $sessions = isAvailableModule($userID, $start, $end , 'Tuesday', $con);
                        
                        foreach($sessions as $session) {
                            $session_data = getSessionData($session, $con)->fetch_array(MYSQLI_ASSOC);
                            echo '<div class="col p-1 bg-green ">';
                            echo getModuleName($session_data['module_num'], $con) . '<br>';
                            echo '<i class="fa-solid fa-location-dot"></i> : ' . $session_data['session_location'] . '<br>';
                            echo '<i class="fa-solid fa-chalkboard"></i> : ' . $session_data['session_type'];
                            echo '</div>';
                        }
                    echo '</td>';

                    // Wednesday
                    echo '<td id="' . $i . 'Wed">';
                        $sessions = isAvailableModule($userID, $start, $end , 'Wednesday', $con);
                        
                        foreach($sessions as $session) {
                            $session_data = getSessionData($session, $con)->fetch_array(MYSQLI_ASSOC);
                            echo '<div class="col p-1 bg-green">';
                            echo getModuleName($session_data['module_num'], $con) . '<br>';
                            echo '<i class="fa-solid fa-location-dot"></i> : ' . $session_data['session_location'] . '<br>';
                            echo '<i class="fa-solid fa-chalkboard"></i> : ' . $session_data['session_type'];
                            echo '</div>';
                        }
                    echo '</td>';

                    // Thursday
                    echo '<td id="' . $i . 'Thu">';
                        $sessions = isAvailableModule($userID, $start, $end , 'Thursday', $con);
                        
                        foreach($sessions as $session) {
                            $session_data = getSessionData($session, $con)->fetch_array(MYSQLI_ASSOC);
                            echo '<div class="col p-1 bg-green">';
                            echo getModuleName($session_data['module_num'], $con) . '<br>';
                            echo '<i class="fa-solid fa-location-dot"></i> : ' . $session_data['session_location'] . '<br>';
                            echo '<i class="fa-solid fa-chalkboard"></i> : ' . $session_data['session_type'];
                            echo '</div>';
                        }
                    echo '</td>';

                    // Friday
                    echo '<td id="' . $i . 'Fri">';
                        $sessions = isAvailableModule($userID, $start, $end , 'Friday', $con);
                        
                        foreach($sessions as $session) {
                            $session_data = getSessionData($session, $con)->fetch_array(MYSQLI_ASSOC);
                            echo '<div class="col p-1 bg-green">';
                            echo getModuleName($session_data['module_num'], $con) . '<br>';
                            echo '<i class="fa-solid fa-location-dot"></i> : ' . $session_data['session_location'] . '<br>';
                            echo '<i class="fa-solid fa-chalkboard"></i> : ' . $session_data['session_type'];
                            echo '</div>';
                        }
                    echo '</td>';

                echo '</tr>';
            }

            // Close table body and table
            echo '
                </tbody>
            </table>';

        } 
    } else {
        // Echo empty table
        echo '<table class="table timetable text-center fw-normal table-striped table-bordered table-hover">
                <thead>
                    <tr>
                        <th class="first-cell" scope="col"></th>
                        <th scope="col">Monday</th>
                        <th scope="col">Tuesday</th>
                        <th scope="col">Wednesday</th>
                        <th scope="col">Thursday</th>
                        <th scope="col">Friday</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <th scope="row">08:00</th>
                        <td id="08Mon"></td>
                        <td id="08Tue"></td>
                        <td id="08Wed"></td>
                        <td id="08Thu"></td>
                        <td id="08Fri"></td>
                    </tr>
                    <tr>
                        <th scope="row">09:00</th>
                        <td id="09Mon"></td>
                        <td id="09Tue"></td>
                        <td id="09Wed"></td>
                        <td id="09Thu"></td>
                        <td id="09Fri"></td>
                    </tr>
                    <tr>
                        <th scope="row">10:00</th>
                        <td id="10Mon"></td>
                        <td id="10Tue"></td>
                        <td id="10Wed"></td>
                        <td id="10Thu"></td>
                        <td id="10Fri"></td>
                    </tr>
                    <tr>
                        <th scope="row">11:00</th>
                        <td id="11Mon"></td>
                        <td id="11Tue"></td>
                        <td id="11Wed"></td>
                        <td id="11Thu"></td>
                        <td id="11Fri"></td>
                    </tr>
                    <tr>
                        <th scope="row">12:00</th>
                        <td id="12Mon"></td>
                        <td id="12Tue"></td>
                        <td id="12Wed"></td>
                        <td id="12Thu"></td>
                        <td id="12Fri"></td>
                    </tr>
                    <tr>
                        <th scope="row">13:00</th>
                        <td id="13Mon"></td>
                        <td id="13Tue"></td>
                        <td id="13Wed"></td>
                        <td id="13Thu"></td>
                        <td id="13Fri"></td>
                    </tr>
                    <tr>
                        <th scope="row">14:00</th>
                        <td id="14Mon"></td>
                        <td id="14Tue"></td>
                        <td id="14Wed"></td>
                        <td id="14Thu"></td>
                        <td id="14Fri"></td>
                    </tr>
                    <tr>
                        <th scope="row">15:00</th>
                        <td id="15Mon"></td>
                        <td id="15Tue"></td>
                        <td id="15Wed"></td>
                        <td id="15Thu"></td>
                        <td id="15Fri"></td>
                    </tr>
                    <tr>
                        <th scope="row">16:00</th>
                        <td id="16Mon"></td>
                        <td id="16Tue"></td>
                        <td id="16Wed"></td>
                        <td id="16Thu"></td>
                        <td id="16Fri"></td>
                    </tr>
                    <tr>
                        <th scope="row">17:00</th>
                        <td id="17Mon"></td>
                        <td id="17Tue"></td>
                        <td id="17Wed"></td>
                        <td id="17Thu"></td>
                        <td id="17Fri"></td>
                    </tr>
                    <tr>
                        <th scope="row">18:00</th>
                        <td id="18Mon"></td>
                        <td id="18Tue"></td>
                        <td id="18Wed"></td>
                        <td id="18Thu"></td>
                        <td id="18Fri"></td>
                    </tr>
                    <tr>
                        <th scope="row">19:00</th>
                        <td id="19Mon"></td>
                        <td id="19Tue"></td>
                        <td id="19Wed"></td>
                        <td id="19Thu"></td>
                        <td id="19Fri"></td>
                    </tr>
                    <tr>
                        <th scope="row">20:00</th>
                        <td id="20Mon"></td>
                        <td id="20Tue"></td>
                        <td id="20Wed"></td>
                        <td id="20Thu"></td>
                        <td id="20Fri"></td>
                    </tr>
                    <tr>
                        <th scope="row">21:00</th>
                        <td id="21Mon"></td>
                        <td id="21Tue"></td>
                        <td id="21Wed"></td>
                        <td id="21Thu"></td>
                        <td id="21Fri"></td>
                    </tr>
                </tbody>
            </table>';
    }

?>