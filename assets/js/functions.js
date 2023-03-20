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
      if (id == "viewSession") {
        // Convert JSON data into an array
        try {
          var array = JSON.parse(data);
        } catch (e) {
            alert(data);
            return;
        }

        // Update and display table
        var table = document.getElementById("sessionsTable");

        // Remove existing rows
        for (var i = table.rows.length - 1; i > 0; i--) {
            table.deleteRow(i);
        }

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
          var cell8 = newRow.insertCell();
          cell8.innerHTML = '<div class="btn-group" role="group"><button type="button" title="View Allocation" onClick="" class="btn btn-primary"><i class="fa-solid fa-chalkboard-user"></i></button><button type="button" title="Delete Session" onClick="" class="btn btn-danger"><i class="fa-solid fa-x"></i></button></div>';
      
        }

        table.style.display = "table";

      } else if (id == "selectUser") {
        // Convert JSON data into an array
        try {
          var array = JSON.parse(data);
        } catch (e) {
            alert(data);
            return;
        }

        // Update form values in updateUser form and enable inputs
        document.getElementById("editFirstNameInput").value = array['fname'];
        document.getElementById("editLastNameInput").value = array['lname'];
        document.getElementById("editEmailInput").value = array['email'];
        document.getElementById("editAdminCheck").checked = array['admin'];
        document.getElementById("editUserSelect").value = array['ta_num'];
        document.getElementById("editFirstNameInput").disabled = false;
        document.getElementById("editLastNameInput").disabled = false;
        document.getElementById("editEmailInput").disabled = false;
        document.getElementById("editAdminCheck").disabled = false;
        document.getElementById("savebtn").disabled = false;

      } else if (id == "updateUser") {
        // Disable inputs in form 
        document.getElementById("editFirstNameInput").disabled = true;
        document.getElementById("editLastNameInput").disabled = true;
        document.getElementById("editEmailInput").disabled = true;
        document.getElementById("editAdminCheck").disabled = true;
        document.getElementById("savebtn").disabled = true;
        alert(data);
        location.reload();

      } else if (id == "selectModule") {
        // Convert JSON data into an array
        try {
          var array = JSON.parse(data);
        } catch (e) {
            alert(data);
            return;
        }

        // Update form values in updateUser form and enable inputs
        document.getElementById("editModuleNameInput").value = array['module_name'];
        document.getElementById("editModuleConInput").value = array['module_convenor'];
        document.getElementById("editModuleDesInput").value = array['module_description'];
        document.getElementById("editModuleLinkInput").value = array['link'];
        document.getElementById("editModuleSelect").value = array['module_num'];
        document.getElementById("editModuleNameInput").disabled = false;
        document.getElementById("editModuleConInput").disabled = false;
        document.getElementById("editModuleDesInput").disabled = false;
        document.getElementById("editModuleLinkInput").disabled = false;
        document.getElementById("savebtn2").disabled = false;

      } else if (id == "updateModule") {
        // Disable inputs in form 
        document.getElementById("editModuleNameInput").disabled = true;
        document.getElementById("editModuleConInput").disabled = true;
        document.getElementById("editModuleDesInput").disabled = true;
        document.getElementById("editModuleLinkInput").disabled = true;
        document.getElementById("savebtn2").disabled = true;
        alert(data);
        location.reload();
      } else {
        alert(data);
        location.reload();
      }
      // Resets Form
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
