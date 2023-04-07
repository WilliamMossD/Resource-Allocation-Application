<?php 

/*
 * getSessionList.php file for the Individual Project (University of Sussex 2023) 
 * Author: William Moss (235319)
 *
 * Supports GET requests and returns a data list of sessions
 */

    require_once(__DIR__ . '/../../../src/inc/utilities.php');

    try {

        try {
            $con = mysqliConnect();
        } catch (Exception $e) {
            exit();
        }

        // Get rows from table 
        $stmt = $con->prepare("SELECT module_sessions.module_session_num, modules.module_name, module_sessions.session_day, DATE_FORMAT(module_sessions.session_start, '%H:%i') AS session_start, DATE_FORMAT(module_sessions.session_end, '%H:%i') AS session_end FROM modules, module_sessions WHERE modules.module_num = module_sessions.module_num ORDER BY module_sessions.module_num ASC");
        $stmt->execute();
        $sessionrows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

        $currentModule = null;

        foreach ($sessionrows as $row) {
            if ($currentModule != $row['module_name']) {
                if ($currentModule != null) {
                    echo '</optgroup>';
                }
                echo '<optgroup label="' . $row['module_name'] . '">';
                $currentModule = $row['module_name'];
            }
            echo '<option value="'.$row['module_session_num'] .'">'.$row['session_day'].', '.$row['session_start']. ' - ' . $row['session_end'] .'</option>';
        }

        echo '</optgroup>'; 

    } catch (Exception $e) {
        exit();
    }

 ?>
