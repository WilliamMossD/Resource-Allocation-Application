/*
 * functions.js file for the Individual Project (University of Sussex 2023) 
 * Author: William Moss (235319)
 */

/* OnLoad Function */
function load() {
    date();
    time();    
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
    if (mn < 10){
        mn =  "0" + mn;
    }
    document.getElementById("time").innerText = hr + ':' + mn;
    setTimeout(time, 1000);
}