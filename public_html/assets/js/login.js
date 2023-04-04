/*
 * login.js file for the Individual Project (University of Sussex 2023)
 * Author: William Moss (235319)
 */

var error = new URLSearchParams(window.location.search).get('errorcode');
var logout = new URLSearchParams(window.location.search).get('logout');

/*
 * Displays logout alert
 */
if (logout) {
    alert("Logged Out Successfully");
    window.history.replaceState({}, document.title, "/" + "taallocation/index.html");
}


/*
 * Displays errors alert
 */
if (error) {
    alert(getErrorText(error));
    window.history.replaceState({}, document.title, "/" + "taallocation/index.html");
}

/*
 * Controls alerts to be shown to the user
 */

function getErrorText(data){
    if (['1', '2', '3', '4', '5'].includes(data)) {
        if (data == '1') {
            return 'You are not authorized to access this application';
        } else if (data == '2') {
            return 'OAuth2.0 Mismatch';
        } else if (data == '3') {
            return 'Database Error';
        } else if (data == '4') {
            return 'Unknown Error';
        } else if (data == '5') {
            return 'Please login first!';
        } 
    }
}

  