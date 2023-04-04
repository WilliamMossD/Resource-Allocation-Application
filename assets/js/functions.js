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
document.querySelectorAll("#avaltable td").forEach((e) =>
  e.addEventListener("click", function () {
    if (e.classList.contains("active")) {
      e.classList.remove("active");
    } else {
      e.classList.add("active");
    }
  })
);

// Add Event Listeners to availability buttons
document.getElementById("clearAval").addEventListener("click", function () {
  // Removes active class from all cells in the avaltable table
  document
    .querySelectorAll("#avaltable td")
    .forEach((e) => e.classList.remove("active"));
});

document.getElementById("resetAval").addEventListener("click", function () {
  // Resets table back to last saved state
});

document.getElementById("saveAval").addEventListener("click", function () {
  // Turn table data into a 2D array
  // POST data to database
  // Send success alert
});

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

function printTable(var1) {
  var printWindow = window.open();
  printWindow.document.write(document.getElementById(var1).innerHTML);
  printWindow.print();
  printWindow.onafterprint = (event) => {
    printWindow.close();
  };
}
