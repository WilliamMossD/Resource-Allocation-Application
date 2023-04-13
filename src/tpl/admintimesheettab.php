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
        $stmt = $con->prepare("SELECT CONCAT(teaching_assistants.fname, ' ', teaching_assistants.lname) AS 'name', timesheets.ta_num, week_num , year, monStart, monEnd, tueStart, tueEnd, wedStart, wedEnd, thuStart, thuEnd, friStart, friEnd, total, comments, status, timesheet_num FROM timesheets, teaching_assistants WHERE timesheets.ta_num = teaching_assistants.ta_num");

        $stmt->execute();
        $timesheets = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
}

?>

<div class="container">
    <table id="timesheetsAdmin" class="table text-center fw-normal table-striped table-hover w-100 ">
        <thead>
            <tr>
                <th></th>
                <th>User Name</th>
                <th>ID</th>
                <th>Week</th>
                <th>Year</th>
                <th>Monday</th>
                <th>Tuesday</th>
                <th>Wednesday</th>
                <th>Thursday</th>
                <th>Friday</th>
                <th>Total Hours</th>
                <th>Comments</th>
                <th>Status</th>
                <th>Timesheet ID</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($timesheets as $timesheet) : ?>
                <tr id="<?= $timesheet['timesheet_num'] ?>">
                    <td></td>
                    <td><?= $timesheet['name'] ?></td>
                    <td><?= $timesheet['ta_num'] ?></td>
                    <td><?= $timesheet['week_num'] ?></td>
                    <td><?= $timesheet['year'] ?></td>
                    <td><?php if ($timesheet['monStart'] == '') : ?>--:--<?php else : ?><?= $timesheet['monStart'] ?><?php endif; ?> to <?php if ($timesheet['monEnd'] == '') : ?>--:--<?php else : ?><?= $timesheet['monEnd'] ?><?php endif; ?></th>
                    <td><?php if ($timesheet['tueStart'] == '') : ?>--:--<?php else : ?><?= $timesheet['tueStart'] ?><?php endif; ?> to <?php if ($timesheet['tueEnd'] == '') : ?>--:--<?php else : ?><?= $timesheet['tueEnd'] ?><?php endif; ?></th>
                    <td><?php if ($timesheet['wedStart'] == '') : ?>--:--<?php else : ?><?= $timesheet['wedStart'] ?><?php endif; ?> to <?php if ($timesheet['wedEnd'] == '') : ?>--:--<?php else : ?><?= $timesheet['wedEnd'] ?><?php endif; ?></th>
                    <td><?php if ($timesheet['thuStart'] == '') : ?>--:--<?php else : ?><?= $timesheet['thuStart'] ?><?php endif; ?> to <?php if ($timesheet['thuEnd'] == '') : ?>--:--<?php else : ?><?= $timesheet['thuEnd'] ?><?php endif; ?></th>
                    <td><?php if ($timesheet['friStart'] == '') : ?>--:--<?php else : ?><?= $timesheet['friStart'] ?><?php endif; ?> to <?php if ($timesheet['friEnd'] == '') : ?>--:--<?php else : ?><?= $timesheet['friEnd'] ?><?php endif; ?></th>
                    <td><?= $timesheet['total'] ?></th>
                    <td><?= $timesheet['comments'] ?></td>
                    <td><?= $timesheet['status'] ?></td>
                    <td><?= $timesheet['timesheet_num'] ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>