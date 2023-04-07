<?php 

/*
 * getUserList.php file for the Individual Project (University of Sussex 2023) 
 * Author: William Moss (235319)
 *
 * Supports GET requests and returns a data list of users
 */

    require_once(__DIR__ . '/../../../src/inc/utilities.php');

    try {
        try {
            $con = mysqliConnect();
        } catch (Exception $e) { 
            exit();
        }

        // Get rows from teaching assistants table 
        $stmt = $con->prepare('SELECT ta_num, fname, lname from teaching_assistants ORDER BY ta_num ASC');
        $stmt->execute();
        $tarows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

        foreach ($tarows as $row) {
            echo '<option value="'.$row['ta_num'] .'">'.$row['fname'].' '.$row['lname'].'</option>';
        }

    } catch (Exception $e) {
        exit();
    }

 ?>