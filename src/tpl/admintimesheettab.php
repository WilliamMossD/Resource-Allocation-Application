<?php

/*
 * admintimesheettab.php file for the Individual Project (University of Sussex 2023) 
 * Author: William Moss (235319)
 *
 * HTML template for the admin timesheet tab
 */

require_once(__DIR__ . '/../inc/utilities.php');

try {
    $con = mysqliConnect();
} catch (Exception $e) {
    exit();
}

if(isset($_SESSION['email']) && isset($_SESSION['loggedin']) && $_SESSION['loggedin']) {
    if (isUserAdmin(getUserIDByEmail($_SESSION['email'], $con), $con) && userExists(getUserIDByEmail($_SESSION['email'], $con), $con)) {
        // Get all timesheets
        $stmt = $con->prepare('SELECT * FROM timesheets');
        $stmt->bind_param('i', $userID);

        $stmt->execute();
        $timesheets = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
}

?>

<div class="container">
    <table id="timesheetsAdmin" class="w-100">
        <thead>
            <tr>
                <th></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($timesheets as $timesheet) : ?>
                <tr>
                    
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>