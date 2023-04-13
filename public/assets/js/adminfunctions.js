/*
 * adminfunctions.js file for the Individual Project (University of Sussex 2023)
 * Author: William Moss (235319)
 */

// Add event listener to all forms
for (var i = 0; i < document.forms.length; i++) {
  document.forms[i].addEventListener("submit", (event) => {
    event.preventDefault();
    formHandler(event.target.id);
  });
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
              var form = "updateUser";
              break;
            case "selectModule":
              var form = "updateModule";
              break;
            case "selectSession":
              var form = "updateSession";
              break;
          }

          // Update Values
          setFormValues(form, Object.values(array));

          // Enable Inputs
          formDisable(form, false);

          return false;
        } catch (e) {}
      } else if (["updateUser", "updateModule", "updateSession"].includes(id)) {
        // Disable form inputs
        formDisable(id, true);
      } else if (
        [
          "viewSession",
          "viewAllocationByModule",
          "viewAllocationBySession",
          "viewAllocationByUser",
        ].includes(id)
      ) {
        // Parses HTML string to HTML DOM
        var table = new DOMParser().parseFromString(data, "text/xml");

        switch (id) {
          case "viewSession":
            var div = "sessionsDiv";
            break;
          default:
            var div = "allocationDiv";
            break;
        }

        // Check if parser worked
        if (table.querySelector("parsererror")) {
          // Parsing Failed
          document.getElementById(div).innerHTML = "";
          try {
            document.getElementById(button).style.display = "none";
          } catch (e) {}
        } else {
          // Parsing Succeeded
          document.getElementById(div).innerHTML = data;
          if (id == "viewSession") {
            $("#table").dataTable({
              dom: "Bfrtip",
              paging: false,
              buttons: {
                buttons: [{ extend: "print", className: "btn-primary mb-2" }],
                dom: {
                  button: {
                    className: "btn",
                  },
                },
              },
            });
          } else if (id == "viewAllocationByModule") {
            $('div#allocationDiv table').dataTable({
              dom: "Bfrtip",
              paging: false,
              select: true,
              select: 'single',
              ordering: false,
              buttons: {
                buttons: [{ text: 'Print Table', extend: "print", className: "btn-primary" },             
                {
                text: 'Unallocate User',
                className: "btn-danger",
                action: function () {
                    var data = this.rows({ selected: true }).data()[0];
                    removeAlloc(data[0], data[4]);
                }
            }],
                dom: {
                  button: {
                    className: "btn",
                  },
                },
              },
              order: [[3, 'asc']],
              rowGroup: {
                  dataSrc: 3,
              },
              columnDefs: [
                { "visible": false, "searching": false, "targets": [3,4] }
              ],
            });
          } else if (id == "viewAllocationBySession") {
            $('div#allocationDiv table').dataTable({
              dom: "Bfrtip",
              paging: false,
              select: true,
              select: 'single',
              ordering: false,
              buttons: {
                buttons: [{ text: 'Print Table', extend: "print", className: "btn-primary" },             
                {
                text: 'Unallocate User',
                className: "btn-danger",
                action: function () {
                    var data = this.rows({ selected: true }).data()[0];
                    removeAlloc(data[3], data[4]);
                }
              }],
                dom: {
                  button: {
                    className: "btn",
                  },
                },
              },
              columnDefs: [
                { "visible": false, "searching": false, "targets": [3,4] }
              ],
            });
          } else if (id == "viewAllocationByUser") {
            $('div#allocationDiv table').dataTable({
              dom: "Bfrtip",
              paging: false,
              select: true,
              select: 'single',
              ordering: false,
              buttons: {
                buttons: [{ text: 'Print Table', extend: "print", className: "btn-primary" },             
                {
                text: 'Unallocate User',
                className: "btn-danger",
                action: function () {
                    var data = this.rows({ selected: true }).data()[0];
                    removeAlloc(data[6], data[7]);
                }
              }],
                dom: {
                  button: {
                    className: "btn",
                  },
                },
              },
              order: [[0, 'asc']],
              rowGroup: {
                  dataSrc: 0,
              },
              columnDefs: [
                { "visible": false, "searching": false, "targets": [0,6,7] }
              ],
            });
          }
          return false;
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

/* Remove Allocation Function */
function removeAlloc(var1, var2) {
  var form = document.createElement("form");
  form.id = "removeAlloc";
  var ta_numInput = document.createElement("input");
  ta_numInput.name = "ta_num";
  ta_numInput.value = var1;
  form.appendChild(ta_numInput);
  var module_session_numInput = document.createElement("input");
  module_session_numInput.name = "module_session_num";
  module_session_numInput.value = var2;
  form.appendChild(module_session_numInput);
  form.style.display = "hidden";
  document.body.appendChild(form);
  formHandler("removeAlloc");
  document.body.removeChild(form);
}
