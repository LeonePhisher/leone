// script.js
// function subscribing(){
//     let email= prompt("Please enter your Email to Subscribe for News Updates", "")
//     if(email===email){
//         alert("Thanks for Subscribing to our page!");
//     }
//     else {
//         alert("Value not entered");
//     }
    
// }




function subscribing() {
    let email = prompt("Please enter your Email to Subscribe for News Updates", "");

    // Regular expression to validate email format
    const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

    if (email && emailPattern.test(email)) {
        alert("Thanks for Subscribing to our page!");
    } else if (email === "") {
        alert("Value not entered");
    } else {
        alert("Invalid email address. Please try again.");
    }
}