<?php

/*
 * usertimesheettab.php file for the Individual Project (University of Sussex 2023) 
 * Author: William Moss (235319)
 *
 * PHP to generate the HTML for the user timesheet tab
 */


require_once(__DIR__ . '/../inc/utilities.php');

try {
    $con = mysqliConnect();
} catch (Exception $e) {
    exit();
}

// Verifies user is logged in and there is a valid session
if (isset($_SESSION['email']) && isset($_SESSION['loggedin']) && $_SESSION['loggedin']) {
    $userID = getUserIDByEmail($_SESSION['email'], $con);
    if (userExists($userID, $con)) {
        // Get user timesheets
        $stmt = $con->prepare('SELECT * FROM timesheets WHERE ta_num = ?');
        $stmt->bind_param('i', $userID);

        $stmt->execute();
        $timesheets = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
} 

?>

<ul class="nav nav-pills nav-fill mb-3" id="pills-tab" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link active" id="pills-viewtimesheet-tab" data-bs-toggle="pill" data-bs-target="#pills-viewtimesheet" type="button" role="tab" aria-controls="pills-viewtimesheet" aria-selected="false" tabindex="-1">View Timesheets</button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="pills-submittimesheet-tab" data-bs-toggle="pill" data-bs-target="#pills-submittimesheet" type="button" role="tab" aria-controls="pills-submittimesheet" aria-selected="true">Submit Timesheet</button>
    </li>
</ul>
<hr>
<div class="tab-content" id="pills-tabContent">
    <div class="tab-pane fade show active" id="pills-viewtimesheet" role="tabpanel" aria-labelledby="pills-viewtimesheet-tab" tabindex="0">
        <div class="container">
            <table id="timesheets" class="w-100">
                <thead>
                    <tr>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($timesheets as $timesheet) : ?>
                        <tr>
                            <td class="pb-3">
                                <div class="card text-bg-primary w-100">
                                    <div class="card-header">
                                        <div class="row">
                                            <div class="col-8">
                                                <h5 class="card-title m-1">Week <?= $timesheet['week_num'] ?> (<?= $timesheet['timesheet_num'] ?>)</h5>
                                                <h6 class="card-subtitle m-1"><?= $timesheet['year'] ?></h6>
                                            </div>
                                            <div class="col-4 text-end">
                                                <h5 class="m-1"><?= $timesheet['status'] ?></h5>
                                                <h6 class="m-1"><?= $timesheet['submit_datetime'] ?></h6>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <h6 class="card-title">Submitted Hours:</h6>
                                        <table class="table text-white text-center timesheet-table">
                                            <thead>
                                                <tr>
                                                    <th scope="col">Monday</th>
                                                    <th scope="col">Tuesday</th>
                                                    <th scope="col">Wednesday</th>
                                                    <th scope="col">Thursday</th>
                                                    <th scope="col">Friday</th>
                                                    <th scope="col">Total</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td><?php if ($timesheet['monStart'] == '') : ?>--:--<?php else : ?><?= $timesheet['monStart'] ?><?php endif; ?> to <?php if ($timesheet['monEnd'] == '') : ?>--:--<?php else : ?><?= $timesheet['monEnd'] ?><?php endif; ?></th>
                                                    <td><?php if ($timesheet['tueStart'] == '') : ?>--:--<?php else : ?><?= $timesheet['tueStart'] ?><?php endif; ?> to <?php if ($timesheet['tueEnd'] == '') : ?>--:--<?php else : ?><?= $timesheet['tueEnd'] ?><?php endif; ?></th>
                                                    <td><?php if ($timesheet['wedStart'] == '') : ?>--:--<?php else : ?><?= $timesheet['wedStart'] ?><?php endif; ?> to <?php if ($timesheet['wedEnd'] == '') : ?>--:--<?php else : ?><?= $timesheet['wedEnd'] ?><?php endif; ?></th>
                                                    <td><?php if ($timesheet['thuStart'] == '') : ?>--:--<?php else : ?><?= $timesheet['thuStart'] ?><?php endif; ?> to <?php if ($timesheet['thuEnd'] == '') : ?>--:--<?php else : ?><?= $timesheet['thuEnd'] ?><?php endif; ?></th>
                                                    <td><?php if ($timesheet['friStart'] == '') : ?>--:--<?php else : ?><?= $timesheet['friStart'] ?><?php endif; ?> to <?php if ($timesheet['friEnd'] == '') : ?>--:--<?php else : ?><?= $timesheet['friEnd'] ?><?php endif; ?></th>
                                                    <td><?= $timesheet['total'] ?></th>
                                                </tr>
                                            </tbody>
                                        </table>
                                        <h6 class="card-title mt-2 m-0">Additional Comments:</h6>
                                        <p class="card-text"><?php if ($timesheet['comments'] == '') : ?>No Comments<?php else : ?><?= $timesheet['comments'] ?><?php endif; ?></p>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <div class="tab-pane fade" id="pills-submittimesheet" role="tabpanel" aria-labelledby="pills-submittimesheet-tab" tabindex="0">
        <div class="container">
            <form id="submitTimesheet" class="row g-3 pt-2 pb-2">
                <div class="col-5 mb-2">
                    <label for="nameInput" class="form-label">Name</label>
                    <input type="text" class="form-control custom-input" id="nameInput" name="nameInput" value="<?= $name ?>" readonly>
                </div>
                <div class="col-1 mb-2">
                    <label for="idInput" class="form-label">ID</label>
                    <input type="number" class="form-control custom-input" id="idInput" name="idInput" value="<?= getUserIDByEmail($_SESSION['email'], $con) ?>" readonly>
                </div>
                <div class="col-6 mb-2">
                    <label for="emailInput" class="form-label">Email</label>
                    <input type="email" class="form-control custom-input" id="emailInput" name="emailInput" value="<?= $_SESSION['email'] ?>" readonly>
                </div>
                <div class="col-6 mb-2">
                    <label for="weekInput" class="form-label">Week Number</label>
                    <input type="number" class="form-control custom-input" id="weekInput" name="weekInput" min="1" max="<?= date("W") ?>" value="<?= date("W") ?>" required></input>
                </div>
                <div class="col-6 mb-2">
                    <label for="yearInput" class="form-label">Year</label>
                    <input type="number" class="form-control custom-input" id="yearInput" name="yearInput" value="<?= date("Y") ?>" readonly></input>
                </div>
                <div class="col-12 mb-2">
                    <table class="table timesheet-table">
                        <thead>
                            <tr>
                                <th scope="col">Day of Week</th>
                                <th scope="col">Start Time</th>
                                <th scope="col">End Time</th>
                                <th scope="col" style="text-align: center;">Total Time</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <th scope="row">Monday</th>
                                <td><input type="time" class="form-control custom-input" id="monStartTime" name="monStartTime"></td>
                                <td><input type="time" class="form-control custom-input" id="monEndTime" name="monEndTime"></td>
                                <td id="monHours" style="text-align: center;">00:00</td>
                            </tr>
                            <tr>
                                <th scope="row">Tuesday</th>
                                <td><input type="time" class="form-control custom-input" id="tueStartTime" name="tueStartTime"></td>
                                <td><input type="time" class="form-control custom-input" id="tueEndTime" name="tueEndTime"></td>
                                <td id="tueHours" style="text-align: center;">00:00</td>
                            </tr>
                            <tr>
                                <th scope="row">Wednesday</th>
                                <td><input type="time" class="form-control custom-input" id="wedStartTime" name="wedStartTime"></td>
                                <td><input type="time" class="form-control custom-input" id="wedEndTime" name="wedEndTime"></td>
                                <td id="wedHours" style="text-align: center;">00:00</td>
                            </tr>
                            <tr>
                                <th scope="row">Thursday</th>
                                <td><input type="time" class="form-control custom-input" id="thuStartTime" name="thuStartTime"></td>
                                <td><input type="time" class="form-control custom-input" id="thuEndTime" name="thuEndTime"></td>
                                <td id="thuHours" style="text-align: center;">00:00</td>
                            </tr>
                            <tr>
                                <th scope="row">Friday</th>
                                <td><input type="time" class="form-control custom-input" id="friStartTime" name="friStartTime"></td>
                                <td><input type="time" class="form-control custom-input" id="friEndTime" name="friEndTime"></td>
                                <td id="friHours" style="text-align: center;">00:00</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="col-12 mt-1 mb-2">
                    <label for="timesheetTextInput" class="form-label">Additional Comments</label>
                    <textarea class="form-control custom-textarea" id="timesheetTextInput" name="timesheetTextInput" rows="2" maxlength="100"></textarea>
                </div>
                <button type="submit" class="btn btn-primary">Submit Timesheet</button>
            </form>
        </div>
    </div>
</div>