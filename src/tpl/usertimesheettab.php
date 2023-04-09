<?php

/*
 * usertimesheettab.php file for the Individual Project (University of Sussex 2023) 
 * Author: William Moss (235319)
 *
 * HTML template for the user timesheet tab
 */

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

     </div>
 </div>
 <div class="tab-pane fade" id="pills-submittimesheet" role="tabpanel" aria-labelledby="pills-submittimesheet-tab" tabindex="0">
     <div class="container">
         <form id="submitTimesheet" class="row g-3 pt-2 pb-2">
             <div class="col-5 mb-2">
                 <label for="nameInput" class="form-label">Name</label>
                 <input type="text" class="form-control custom-input" id="nameInput" name="nameInput" value="<?= $name ?>" disabled>
             </div>
             <div class="col-1 mb-2">
                 <label for="idInput" class="form-label">ID</label>
                 <input type="number" class="form-control custom-input" id="idInput" name="idInput" value="<?= getUserIDByEmail($_SESSION['email'], $con) ?>" disabled>
             </div>
             <div class="col-6 mb-2">
                 <label for="emailInput" class="form-label">Email</label>
                 <input type="email" class="form-control custom-input" id="emailInput" name="emailInput" value="<?= $_SESSION['email'] ?>" disabled>
             </div>
             <div class="col-12 mb-2">
                 <label for="weekInput" class="form-label">Week Selection</label>
                 <input type="week" class="form-control custom-textarea" id="weekInput" name="weekInput" required></input>
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
                             <td><input type="time" class="form-control custom-input" id="tueStartTime" name="monStartTime"></td>
                             <td><input type="time" class="form-control custom-input" id="tueEndTime" name="monEndTime"></td>
                             <td id="tueHours" style="text-align: center;">00:00</td>
                         </tr>
                         <tr>
                             <th scope="row">Wednesday</th>
                             <td><input type="time" class="form-control custom-input" id="wedStartTime" name="monStartTime"></td>
                             <td><input type="time" class="form-control custom-input" id="wedEndTime" name="monEndTime"></td>
                             <td id="wedHours" style="text-align: center;">00:00</td>
                         </tr>
                         <tr>
                             <th scope="row">Thursday</th>
                             <td><input type="time" class="form-control custom-input" id="thuStartTime" name="monStartTime"></td>
                             <td><input type="time" class="form-control custom-input" id="thuEndTime" name="monEndTime"></td>
                             <td id="thuHours" style="text-align: center;">00:00</td>
                         </tr>
                         <tr>
                             <th scope="row">Friday</th>
                             <td><input type="time" class="form-control custom-input" id="friStartTime" name="monStartTime"></td>
                             <td><input type="time" class="form-control custom-input" id="friEndTime" name="monEndTime"></td>
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