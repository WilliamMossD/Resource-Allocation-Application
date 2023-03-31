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

/* Form Handler */
function formHandler(id) {
  var actionUrl = "assets/php/formHandler.php";
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
        if (["selectUser", "selectModule", "selectSession"].includes(id)) {

          // Convert JSON data into an array
          try {
            var array = JSON.parse(data);

            switch (id) {
              case "selectUser":
                var form = 'updateUser';
                break;
              case "selectModule":
                var form = 'updateModule';
                break;
              case "selectSession":
                var form = 'updateSession';
                break;
            }

            // Update Values
            setFormValues(form, Object.values(array));

            // Enable Inputs
            formDisable(form, false);

            return false;

          } catch (e){}
            
      } else if (["updateUser", "updateModule", "updateSession"].includes(id)) {

        // Disable form inputs
        formDisable(id, true);

      } else if (["viewSession", "viewAllocationBySession", "viewAllocationByUser"].includes(id)) {

        if ("viewSession" == id) {

          // Parses HTML string to HTML DOM
          var table = new DOMParser().parseFromString(data, "text/xml");

          // Check if parser worked
          if (table.querySelector("parsererror")) {
            // Parsing Failed
            document.getElementById("sessionsDiv").innerHTML = "";
            document.getElementById("sessionsTablePrint").style.display = "none";
          } else {
            // Parsing Succeeded
            document.getElementById("sessionsDiv").innerHTML = data
            document.getElementById("sessionsTablePrint").style.display = "initial";
            return false;
          }
        } else if ("viewAllocationBySession" == id) {

          // Parses HTML string to HTML DOM
          var table = new DOMParser().parseFromString(data, "text/xml");

          // Check if parser worked
          if (table.querySelector("parsererror")) {
            // Parsing Failed
            document.getElementById("allocationDiv").innerHTML = "";
            document.getElementById("allocationDivPrint").style.display = "none";
          } else {
            // Parsing Succeeded
            document.getElementById("allocationDiv").innerHTML = data
            document.getElementById("allocationDivPrint").style.display = "initial";
            return false;
          }
        } else if ("viewAllocationByUser" == id) {

          // Parses HTML string to HTML DOM
          var table = new DOMParser().parseFromString(data, "text/xml");

          // Check if parser worked
          if (table.querySelector("parsererror")) {
            // Parsing Failed
            document.getElementById("allocationDiv").innerHTML = "";
            document.getElementById("allocationDivPrint").style.display = "none";
          } else {
            // Parsing Succeeded
            document.getElementById("allocationDiv").innerHTML = data
            document.getElementById("allocationDivPrint").style.display = "initial";
            return false;
          }
        }
      }

      // Alert Data
      alert(data);

      // Reset Form
      document.getElementById(id).reset();

      // Updates lists and module HTML
      getModuleCards();
      getUserList();
      getModuleList();
      getSessionList();
      
    },
    error: function () {
      alert("Unknown Error Occurred");
    },

    complete: function () {
      // Enable button
      document
        .getElementById(id)
        .querySelector('button[type="submit"]').disabled = false;
    },
  });
}

/* Update Form Values Function */
function setFormValues(id, values) {
  formInputs = document
    .getElementById(id)
    .querySelectorAll("input, select, textarea");
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
  var formElements = document.getElementById(id).elements;
  for (var i = 0; i < formElements.length; i++) {
    formElements[i].disabled = state;
  }
}

/* Updates user list data list */
function getUserList() {
  // Send GET request
  $.get("assets/php/getUserList.php", function (data) {
    // Update HTML
    var selects = document.getElementsByTagName("select");
    for (var i = 0; i < selects.length; i++) {
      if (selects[i].getAttribute("type") == "user") {
        selects[i].innerHTML = data;
      }
    }
  });
}

/* Updates module list data list */
function getModuleList() {
  // Send GET request
  $.get("assets/php/getModuleList.php", function (data) {
    // Update HTML
    var selects = document.getElementsByTagName("select");
    for (var i = 0; i < selects.length; i++) {
      if (selects[i].getAttribute("type") == "module") {
        selects[i].innerHTML = data;
      }
    }
  });
}

/* Updates module list data list */
function getSessionList() {
  // Send GET request
  $.get("assets/php/getSessionList.php", function (data) {
    // Update HTML
    var selects = document.getElementsByTagName("select");
    for (var i = 0; i < selects.length; i++) {
      if (selects[i].getAttribute("type") == "session") {
        selects[i].innerHTML = data;
      }
    }
  });
}

/* Updates module cards tab */
function getModuleCards() {
  // Send GET request
  $.get("assets/php/getModuleCards.php", function (data) {
    // Update HTML
    document.getElementById("moduleCards").innerHTML = data;
  });
}

/* Date Function */
function date() {
  let date = new Date();
  document.getElementById("date").innerText = date.toDateString();
  setTimeout(time, 1000);
}

/* Remove Allocation Function */
function removeAlloc(var1, var2) {
  var form = document.createElement('form');
  form.id = "removeAlloc";
  var ta_numInput = document.createElement('input');
  ta_numInput.name = "ta_num";
  ta_numInput.value = var1;
  form.appendChild(ta_numInput);
  var module_session_numInput = document.createElement('input');
  module_session_numInput.name = "module_session_num";
  module_session_numInput.value = var2;
  form.appendChild(module_session_numInput);
  form.style.display = 'hidden';
  document.body.appendChild(form);
  formHandler("removeAlloc");
  document.body.removeChild(form);
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

function printTable(var1) {
  var printWindow = window.open();
  printWindow.document.write(document.getElementById(var1).innerHTML);
  printWindow.print();
  printWindow.close();
}
