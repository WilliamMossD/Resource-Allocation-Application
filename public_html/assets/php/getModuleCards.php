<?php 

/*
 * getModuleCards.php file for the Individual Project (University of Sussex 2023) 
 * Author: William Moss (235319)
 *
 * Supports GET requests and returns html elements of all modules
 */

    require_once(__DIR__ . '/../../../src/inc/utilities.php');

    try {

        try {
            $con = mysqliConnect();
        } catch (Exception $e) {
            exit();
        }
        
        if(isset($_SESSION['email']) && isset($_SESSION['loggedin']) && $_SESSION['loggedin']) {
            if (!isUserAdmin(getUserIDByEmail($_SESSION['email'], $con), $con)) {
                // Get modules that user is assigned to
                $stmt = $con->prepare('SELECT DISTINCT modules.module_num, modules.module_name, modules.module_convenor, modules.module_description, modules.link FROM modules, module_sessions, assigned_to WHERE assigned_to.ta_num = ? AND module_sessions.module_session_num = assigned_to.module_session_num AND module_sessions.module_num = modules.module_num
                ');
                $userID = getUserIDByEmail($_SESSION['email'], $con);
                $stmt->bind_param('i', $userID);
                $stmt->execute();
                $modulerows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
                
            } else {
                // Get rows from module table
                $stmt = $con->prepare('SELECT * from modules ORDER BY module_num ASC');
                $stmt->execute();
                $modulerows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
            }
        } else {
            // Get rows from module table
            $stmt = $con->prepare('SELECT * from modules ORDER BY module_num ASC');
            $stmt->execute();
            $modulerows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        }

        foreach ($modulerows as $row) {
            echo '<div class="col-4">';
                echo '<div class="card bg-primary">';
                    echo '<div class="card-header">';
                        echo '<h6 class="mt-2 mb-2">' . $row['module_name'] . ' (ID: ' . $row['module_num'] . ')</h6>';
                    echo '</div>';
                    echo '<div class="card-body">';
                        echo '<h6 class="card-subtitle mb-2"><i class="fa-solid fa-chalkboard-user"></i> : '
                        . $row['module_convenor'] . '</h6>';
                        echo '<p class="card-text">' . $row['module_description'] . '</p>';
                        echo '<a href="' . $row['link'] . '" class="card-link link-info" target="_blank">Canvas Page</a>';
                    echo '</div>';
                echo '</div>';
            echo '</div>';
        }

    } catch (Exception $e) {
        exit();
    }

 ?>