/*
 * functions.js file for the Individual Project (University of Sussex 2023)
 * Author: William Moss (235319)
 */

// Add event listener to all forms
for (var i = 0; i < document.forms.length; i++) {
  document.forms[i].addEventListener("submit", (event) => {
    event.preventDefault();
    formHandler(event.target.id);
  });
}

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
      inputs[i].addEventListener("input", updateHours);
  }
}

/* OnLoad Function */
function load() {
  date();
  time();
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

