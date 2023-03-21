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

// Add event listener to avaliability table cells
document.querySelectorAll("#avaltable td").forEach((e) =>
  e.addEventListener("click", function () {
    if (e.classList.contains("active")) {
      e.classList.remove("active");
    } else {
      e.classList.add("active");
    }
  })
);

// Add Event Listeners to avaliability buttons
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

/* Form Handler */
function formHandler(id) {
  var actionUrl = "assets/php/formHandler.php";
  if (document.getElementById(id).tagName == 'FORM') {
    var formData = $("#" + id).serializeArray(); // Convert form to array
    formData.unshift({name: "formID", value: id});
  } else {
    return;
  }

  $.ajax({
    type: "POST",
    url: actionUrl,
    data: $.param(formData),
    beforeSend: function() {
      // Disable button
      document.getElementById(id).querySelector('button[type="submit"]').disabled = true;
    },
    success: function (data) {
      // Convert JSON data into an array
      try {
        var array = JSON.parse(data);
        if (id == "viewSession") { 

          // Get table by ID
          var table = document.getElementById("sessionsTable");

          // Remove existing rows
          for (var i = table.rows.length - 1; i > 0; i--) {
              table.deleteRow(i);
          }

          // Add new rows and populate
          for (var i = 0; i < array.length; i++) {
            var newRow = table.insertRow();
            var cell1 = newRow.insertCell();
            cell1.innerHTML = array[i][0];
            var cell2 = newRow.insertCell();
            cell2.innerHTML = array[i][2];
            var cell3 = newRow.insertCell();
            cell3.innerHTML = array[i][3];  
            var cell4 = newRow.insertCell();
            cell4.innerHTML = array[i][4];
            var cell5 = newRow.insertCell();
            cell5.innerHTML = array[i][5];
            var cell6 = newRow.insertCell();
            cell6.innerHTML = array[i][6];
            var cell7 = newRow.insertCell();
            cell7.innerHTML = array[i][1];
          }

          // Display Table
          table.style.display = "table";

        } else if (["selectUser", "selectModule", "selectSession"].includes(id)) {

          var form;

          switch (id) {
            case "selectUser":
              form = "updateUser";
              break;
            case "selectModule":
              form = "updateModule";
              break;
            case "selectSession":
              form = "updateSession";
              break;
          }

          // Update form values
          setFormValues(form, Object.values(array));
          // Enable Inputs
          formDisable(form, false);
          

        } else if (["updateUser", "updateModule", "updateSession"].includes(id)) {

          // Disable form inputs
          formDisable(id, true);

          // Alert Data
          alert(data);
        }
  
      } catch (e) {
        // Alert Data
        alert(data);
      }

      // Reset Form
      document.getElementById(id).reset();
    },
    error: function () {
      alert("Unknown Error Occured");
    },

    complete: function() {
      // Enable button
      document.getElementById(id).querySelector('button[type="submit"]').disabled = false;
    },
  });
}

/* Update Form Values Function */
function setFormValues(id, values) {
  formInputs = document.getElementById(id).querySelectorAll('input, select, textarea');
  for (var i = 0; i < formInputs.length; i++) {
    if (formInputs[i].type == "checkbox") {
      formInputs[i].checked = values[i];
    } else {
      formInputs[i].value = values[i];
    }
  }
}

/* Enable/Disable Form Function 
   State = True/False (Sets the disabled attribute true/false) */
function formDisable(id, state) {
  var formElements =  document.getElementById(id).elements;
  for (var i = 0; i < formElements.length; i++) {
    formElements[i].disabled = state;
  }
}

/* Date Function */
function date() {
  let date = new Date();
  document.getElementById("date").innerText = date.toDateString();
  setTimeout(time, 1000);
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
