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
    let hr = currentTime.getHours();
    let mn = currentTime.getMinutes();
    document.getElementById("time").innerText = hr.toString() + ':' + mn.toString();
    setTimeout(time, 1000);
}