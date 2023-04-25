for (var i = 0; i < document.forms.length; i++) {
  document.forms[i].addEventListener("submit", (event) => {
    event.preventDefault();
    formHandler(event.target.id);
  });
}
let timesheettable = null;
$(document).ready(function () {
  timesheettable = $("#timesheetsAdmin").DataTable({
    dom: "Bfrtip",
    select: true,
    select: "single",
    searching: true,
    ordering: true,
    paging: true,
    columnDefs: [
      {
        visible: false,
        searching: false,
        targets: [5, 6, 7, 8, 9, 11, 13],
      },
      {
        className: "dt-control",
        orderable: false,
        data: null,
        defaultContent: "",
        targets: 0,
      },
      {
        width: "30%",
        targets: 1,
      },
    ],
    order: [
      [3, "desc"],
      [4, "desc"],
    ],
    buttons: {
      buttons: [
        {
          text: "Approve",
          className: "btn-success",
          action: function () {
            var data = this.rows({
              selected: true,
            }).data()[0];
            timesheetManagement(data[13], "approveTimesheet");
          },
        },
        {
          text: "Deny",
          className: "btn-warning",
          action: function () {
            var data = this.rows({
              selected: true,
            }).data()[0];
            timesheetManagement(data[13], "denyTimesheet");
          },
        },
        {
          text: "Delete",
          className: "btn-danger",
          action: function () {
            var data = this.rows({
              selected: true,
            }).data()[0];
            timesheetManagement(data[13], "deleteTimesheet");
          },
        },
      ],
      dom: {
        button: {
          className: "btn",
        },
      },
    },
  });
  $("#timesheetsAdmin tbody").on("click", "td.dt-control", function () {
    var tr = $(this).closest("tr");
    var row = timesheettable.row(tr);
    if (row.child.isShown()) {
      row.child.hide();
      tr.removeClass("shown");
    } else {
      row.child(format(row.data())).show();
      tr.addClass("shown");
    }
  });
});
function formHandler(id) {
  var actionUrl = "/formHandler.php";
  if (document.getElementById(id).tagName == "FORM") {
    var formData = $("#" + id).serializeArray();
    formData.unshift({
      name: "formID",
      value: id,
    });
    var csrf = document.getElementById("CSRFToken").value
    formData.unshift({
      name: "CSRFToken",
      value: csrf,
    });
  } else {
    return;
  }
  if (["approveTimesheet", "denyTimesheet", "deleteTimesheet"].includes(id)) {
    var timesheetID = formData[1]["value"];
  }
  $.ajax({
    type: "POST",
    url: actionUrl,
    data: $.param(formData),
    beforeSend: function () {
      try {
        document
          .getElementById(id)
          .querySelector('button[type="submit"]').disabled = true;
      } catch (err) {}
    },
    success: function (data) {
      if (["selectUser", "selectModule", "selectSession"].includes(id)) {
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
          setFormValues(form, Object.values(array));
          formDisable(form, false);
          return false;
        } catch (e) {}
      } else if (["updateUser", "updateModule", "updateSession"].includes(id)) {
        formDisable(id, true);
      } else if (
        [
          "viewSession",
          "viewAllocationByModule",
          "viewAllocationBySession",
          "viewAllocationByUser",
        ].includes(id)
      ) {
        var table = new DOMParser().parseFromString(data, "text/xml");
        switch (id) {
          case "viewSession":
            var div = "sessionsDiv";
            break;
          default:
            var div = "allocationDiv";
            break;
        }
        if (table.querySelector("parsererror")) {
          document.getElementById(div).innerHTML = "";
          try {
            document.getElementById(button).style.display = "none";
          } catch (e) {}
        } else {
          document.getElementById(div).innerHTML = data;
          if (id == "viewSession") {
            $("#table").dataTable({
              dom: "Bfrtip",
              paging: false,
              buttons: {
                buttons: [
                  {
                    extend: "print",
                    className: "btn-primary mb-2",
                  },
                ],
                dom: {
                  button: {
                    className: "btn",
                  },
                },
              },
            });
          } else if (id == "viewAllocationByModule") {
            allocationTable = $("div#allocationDiv table").DataTable({
              dom: "Bfrtip",
              paging: false,
              select: true,
              select: "single",
              ordering: false,
              buttons: {
                buttons: [
                  {
                    text: "Print Table",
                    extend: "print",
                    className: "btn-primary",
                  },
                  {
                    text: "Unallocate User",
                    className: "btn-danger",
                    action: function () {
                      var data = this.rows({
                        selected: true,
                      }).data()[0];
                      removeAlloc(data[0], data[4]);
                    },
                  },
                ],
                dom: {
                  button: {
                    className: "btn",
                  },
                },
              },
              order: [[3, "asc"]],
              rowGroup: {
                dataSrc: 3,
              },
              columnDefs: [
                {
                  visible: false,
                  searching: false,
                  targets: [3, 4],
                },
              ],
            });
          } else if (id == "viewAllocationBySession") {
            allocationTable = $("div#allocationDiv table").DataTable({
              dom: "Bfrtip",
              paging: false,
              select: true,
              select: "single",
              ordering: false,
              buttons: {
                buttons: [
                  {
                    text: "Print Table",
                    extend: "print",
                    className: "btn-primary",
                  },
                  {
                    text: "Unallocate User",
                    className: "btn-danger",
                    action: function () {
                      var data = this.rows({
                        selected: true,
                      }).data()[0];
                      removeAlloc(data[3], data[4]);
                    },
                  },
                ],
                dom: {
                  button: {
                    className: "btn",
                  },
                },
              },
              columnDefs: [
                {
                  visible: false,
                  searching: false,
                  targets: [3, 4],
                },
              ],
            });
          } else if (id == "viewAllocationByUser") {
            allocationTable = $("div#allocationDiv table").DataTable({
              dom: "Bfrtip",
              paging: false,
              select: true,
              select: "single",
              ordering: false,
              buttons: {
                buttons: [
                  {
                    text: "Print Table",
                    extend: "print",
                    className: "btn-primary",
                  },
                  {
                    text: "Unallocate User",
                    className: "btn-danger",
                    action: function () {
                      var data = this.rows({
                        selected: true,
                      }).data()[0];
                      removeAlloc(data[6], data[7]);
                    },
                  },
                ],
                dom: {
                  button: {
                    className: "btn",
                  },
                },
              },
              order: [[0, "asc"]],
              rowGroup: {
                dataSrc: 0,
              },
              columnDefs: [
                {
                  visible: false,
                  searching: false,
                  targets: [0, 6, 7],
                },
              ],
            });
          }
          return false;
        }
      }
      if (
        ["approveTimesheet", "denyTimesheet", "deleteTimesheet"].includes(id)
      ) {
        switch (id) {
          case "approveTimesheet":
            if (data == "Timesheet Successfully Approved") {
              var rowIndex = timesheettable
                .rows({
                  selected: true,
                })
                .indexes()[0];
              timesheettable
                .cell({ row: rowIndex, column: 12 })
                .data("Approved");
            }
            break;
          case "denyTimesheet":
            var div = "sessionsDiv";
            if (data == "Timesheet Successfully Denied") {
              var rowIndex = timesheettable
                .rows({
                  selected: true,
                })
                .indexes()[0];
              timesheettable.cell({ row: rowIndex, column: 12 }).data("Denied");
            }
            break;
          case "deleteTimesheet":
            var div = "sessionsDiv";
            if (data == "Timesheet Successfully Deleted") {
              var rowIndex = timesheettable
                .rows({
                  selected: true,
                })
                .indexes()[0];
              timesheettable.row(rowIndex).remove().draw();
            }
            break;
        }
      } if (id == "removeAlloc") {
        if (data == "Allocation Successfully Removed") {
          var rowIndex = allocationTable
          .rows({
            selected: true,
          })
          .indexes()[0];
        allocationTable.row(rowIndex).remove().draw();
        }
      }
      alert(data);
      document.getElementById(id).reset();
      getModuleCards();
      getUserList();
      getModuleList();
      getSessionList();
    },
    error: function (data) {
      if (data["status"] == 400) {
        if (data == null) {
          alert("Unknown Error Occurred");
        } else {
          alert(data["responseText"]);
        }
      } else if (data["status"] == 401) {
        window.location.href = "/";
      } else if (data["status"] == 401) {
        if (data == null) {
          alert("Unknown Server Error");
        } else {
          alert(data["responseText"]);
        }
      } else {
        alert("Unknown Error Occurred");
      }
    },
    complete: function () {
      document
        .getElementById(id)
        .querySelector('button[type="submit"]').disabled = false;
    },
  });
}
function format(d) {
  return (
    "<table class='table'>" +
    "<thead>" +
    "<tr>" +
    "<th class='border-0 text-center'>" +
    "Monday" +
    "</th>" +
    "<th class='border-0 text-center'>" +
    "Tuesday" +
    "</th>" +
    "<th class='border-0 text-center'>" +
    "Wednesday" +
    "</th>" +
    "<th class='border-0 text-center'>" +
    "Thursday" +
    "</th>" +
    "<th class='border-0 text-center'>" +
    "Friday" +
    "</th>" +
    "</tr>" +
    "</thead>" +
    "<tbody>" +
    "<tr>" +
    "<td class='border-0'>" +
    d[5] +
    "</td>" +
    "<td class='border-0'>" +
    d[6] +
    "</td>" +
    "<td class='border-0'>" +
    d[7] +
    "</td>" +
    "<td class='border-0'>" +
    d[8] +
    "</td>" +
    "<td class='border-0'>" +
    d[9] +
    "</td>" +
    "</tr>" +
    "</tbody>" +
    "</table>" +
    "<h6 class='card-title mt-2 m-0 text-start'>Additional Comments:</h6>" +
    "<p class='text-start'>" +
    d[11] +
    "</p>"
  );
}
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
function formDisable(id, state) {
  var formElements = document.getElementById(id).elements;
  for (var i = 0; i < formElements.length; i++) {
    formElements[i].disabled = state;
  }
}
function getUserList() {
  $.get("/getUserList.php", function (data, textStatus) {
    if (textStatus == 'success') {
      var selects = document.getElementsByTagName("select");
      for (var i = 0; i < selects.length; i++) {
        if (selects[i].getAttribute("type") == "user") {
          selects[i].innerHTML = data;
        }
      }
    } else {
      alert(data)
    }
  });
}
function getModuleList() {
  $.get("/getModuleList.php", function (data, textStatus) {
    if (textStatus == 'success') {
      var selects = document.getElementsByTagName("select");
      for (var i = 0; i < selects.length; i++) {
        if (selects[i].getAttribute("type") == "module") {
          selects[i].innerHTML = data;
        }
      }
    } else {
      alert(data)
    }
  });
}
function getSessionList() {
  $.get("/getSessionList.php", function (data, textStatus) {
    if (textStatus == 'success') {
      var selects = document.getElementsByTagName("select");
      for (var i = 0; i < selects.length; i++) {
        if (selects[i].getAttribute("type") == "session") {
          selects[i].innerHTML = data;
        }
      }
    } else {
      alert(data)
    }
  });
}
function getModuleCards() {
  $.get("/getModuleCards.php", function (data, textStatus) {
    if (textStatus == 'success') {
      document.getElementById("moduleCards").innerHTML = data;
    } else {
      alert(data)
    }
  });
}
function removeAlloc(var1, var2) {
  if (
    confirm(
      "Are you sure you wish to remove user " + var1 + " from session " + var2
    )
  ) {
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
}
function timesheetManagement(var1, var2) {
  var form = document.createElement("form");
  form.id = var2;
  var timesheetID = document.createElement("input");
  timesheetID.name = "timesheet_id";
  timesheetID.value = var1;
  form.appendChild(timesheetID);
  form.style.display = "hidden";
  document.body.appendChild(form);
  formHandler(var2);
  document.body.removeChild(form);
}
