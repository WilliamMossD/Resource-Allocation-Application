<?php 

/*
 * getModuleList.php file for the Individual Project (University of Sussex 2023) 
 * Author: William Moss (235319)
 *
 * Supports GET requests and returns a data list of module
 */

    require_once(__DIR__ . '/../../../src/inc/utilities.php');

    try {

        try {
            $con = mysqliConnect();
        } catch (Exception $e) {
            exit();
        }

        // Get rows from module table
        $stmt = $con->prepare('SELECT * from modules ORDER BY module_num ASC');
        $stmt->execute();
        $modulerows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

        foreach ($modulerows as $row) {
            echo '<option value="'.$row['module_num'].'">'.$row['module_name'].'</option>';
        }

    } catch (Exception $e) {
        exit();
    }

 ?>