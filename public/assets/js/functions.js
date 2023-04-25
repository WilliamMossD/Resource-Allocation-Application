/*
 * functions.js file for the Individual Project (University of Sussex 2023)
 * Author: William Moss (235319)
 */

// Add event listener to submit timesheet form
document.getElementById("submitTimesheet").addEventListener("submit", (event) => {
  event.preventDefault();
  submitTimesheet(event.target.id);
});

// Add event listener to availability table cells
document.querySelectorAll("#availtable td").forEach((e) =>
  e.addEventListener("click", function () {
    if (e.classList.contains("active")) {
      e.classList.remove("active");
    } else {
      e.classList.add("active");
    }
  })
);

// Add Event Listeners to availability buttons
document.getElementById("clearAvail").addEventListener("click", function () {
  // Removes active class from all cells in the availtable table
  document
    .querySelectorAll("#availtable td")
    .forEach((e) => e.classList.remove("active"));
});

document.getElementById("resetAvail").addEventListener("click", function () {
  // Resets table back to last saved state
});

document.getElementById("saveAvail").addEventListener("click", function () {
  // Turn table data into a 2D array
  // POST data to database
  // Send success alert
});

// Add events listeners to timesheet time inputs
var inputs = document.forms["submitTimesheet"].getElementsByTagName("input");
for(var i = 0; i < inputs.length; i++) {
  if(inputs[i].type.toLowerCase() == 'time') {
      inputs[i].addEventListener("change", updateHours);
      inputs[i].addEventListener("reset", updateHours);
  }
}

/* OnLoad Function */
function load() {
  date();
  time();
  $('#timesheets').dataTable({
    searching: false,
    ordering:  false
  });
  $('#timetable').dataTable({
        dom: 'Bfrtip',
        searching: false,
        ordering:  false,
        paging: false,
        buttons: {
            buttons: [
                { extend: 'print', className: 'btn-primary mb-2' }
            ],
           dom: {
    		  button: {
    		  className: 'btn'
    	         }
           }
        }
    });
}


function submitTimesheet(id) {
  var actionUrl = "/timesheetSubmission.php";
  if (document.getElementById(id).tagName == "FORM") {
    var formData = $("#" + id).serializeArray(); // Convert form to array
    formData.unshift({ name: "formID", value: id });
  } else {
    return;
  }

  $.ajax({
    type: "POST",
    url: actionUrl,
    data: $.param(formData),
    beforeSend: function () {
      try {
      // Disable button
      document
        .getElementById(id)
        .querySelector('button[type="submit"]').disabled = true;
      } catch (err) {}
    },
    success: function (data) {
      if (data['status'] == 201) {
        alert(data);
      }

      // Reload Page
      location.reload();
    },
    error: function (data) {
      if (data['status'] == 400) {
        if (data == null) {
          alert("Unknown Error Occurred");        
        } else {
          alert(data['responseText']);
        }
      } else if (data['status'] == 401){ 
        window.location.href="/"
      } else if (data['status'] == 401) {
        if (data == null) {
          alert("Unknown Server Error");        
        } else {
          alert(data['responseText']);
        }
      } else {
        alert("Unknown Error Occurred"); 
      }
    },

    complete: function () {
      // Enable button
      document
        .getElementById(id)
        .querySelector('button[type="submit"]').disabled = false;
    },
  });
}

/* Clock Function */
function time() {
  let currentTime = new Date();
  let hr = currentTime.getHours().toString();
  let mn = currentTime.getMinutes().toString();
  if (hr < 10) {
    hr = "0" + hr;
  }
  if (mn < 10) {
    mn = "0" + mn;
  }
  document.getElementById("time").innerText = hr + ":" + mn;
  setTimeout(time, 1000);
}

/* Date Function */
function date() {
  let date = new Date();
  document.getElementById("date").innerText = date.toDateString();
  setTimeout(time, 1000);
}

// Print function
function printTable(var1) {
  var printWindow = window.open();
  printWindow.document.write(document.getElementById(var1).innerHTML);
  printWindow.print();
  printWindow.onafterprint = (event) => {
    printWindow.close();
  };
}

// Timesheet Hours Function 
function updateHours(e) {
  var day = e.target.id.slice(0,3);
  var startTime = document.getElementById(day + "StartTime").value;
  var endTime = document.getElementById(day + "EndTime").value;
  if (startTime !== "" && endTime !== "") {
    startTime = startTime.split(':');
    var startTimeMinutes = (parseInt(startTime[0]) * 60) + parseInt(startTime[1]);
    endTime = endTime.split(':');
    var endTimeMinutes = (parseInt(endTime[0]) * 60) + parseInt(endTime[1]);
    var diff = endTimeMinutes - startTimeMinutes;
    if (diff < 0) {
      e.target.value = "--:--";
      alert("End time must be greater than start time");
    } else {
      var m = (diff % 60).toString();
      var h = ((diff - m) / 60).toString();
      if (m < 10) {
        var m = '0' + m;
      }
      if (h < 10) {
        var h = '0' + h;
      }
      document.getElementById(day + "Hours").innerHTML = h + ':' + m;
    }
  }
}

