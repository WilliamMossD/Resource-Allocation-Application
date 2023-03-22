<?php 

/*
 * getModuleList.php file for the Individual Project (University of Sussex 2023) 
 * Author: William Moss (235319)
 *
 * Supports GET requests and returns a data list of module
 */

    try {

        // Database Connection Info
        $HOST = 'localhost';
        $USER = 'mossfree_admin';
        $PASSWORD = 'Btf7@w&7Dhi1';
        $DATABASE = 'mossfree_tutordatabase';

        try {
            $con = mysqli_connect($HOST, $USER, $PASSWORD, $DATABASE);
        } catch (Exception $e) {
            // Redirect back to root
            header('Location: index.html');
            exit();
        }

        if (mysqli_connect_errno()) {
            // Redirect back to root
            header('Location: index.html');
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
        header('Location: index.html');
        exit();
    }

 ?>