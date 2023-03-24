<?php 

/*
 * getModuleCards.php file for the Individual Project (University of Sussex 2023) 
 * Author: William Moss (235319)
 *
 * Supports GET requests and returns html elements of all modules
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
            echo '<div class="col-4">';
                echo '<div class="card bg-primary">';
                    echo '<div class="card-header">';
                        echo '<h6 class="mt-2 mb-2">' . $row['module_name'] . '(ID:' . $row['module_num'] . ')</h6>';
                    echo '</div>';
                    echo '<div class="card-body">';
                        echo '<h6 class="card-subtitle mb-2"><i class="fa-solid fa-chalkboard-user"></i> :'
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