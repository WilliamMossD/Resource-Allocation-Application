<?php 

/*
 * getUserList.php file for the Individual Project (University of Sussex 2023) 
 * Author: William Moss (235319)
 *
 * Supports GET requests and returns a data list of users
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