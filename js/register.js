// "true" or "false" to check if ANY error happened
var error = true;
var statusErr = "";

// Do an AJAX post, with boolean isSubmitting
function post(isSubmitting) {
    var email = $("#email").val();
    var password = $("#password").val();
    var password2 = $("#password2").val();
    var submit = isSubmitting ? "true" : "false";

    /**
     * $.post(URL, DATA {key: value}, function (data, status) { }, "json")
     */
    $.post("/ajax/register_ajax.php", {
        email: email,
        password: password,
        password2: password2,
        submit: submit
    }, function (data, status) {

        // Things to do on receive

        if (data != "") {
            $("#emailErr").text(data.emailErr);
            $("#passErr").text(data.passErr);
            $("#pass2Err").text(data.pass2Err);
            $("#submitErr").text(data.submitErr);
        }

        // Check if anything was wrong
        error = data.error;

        // Check status (only when submitting) if it went alright, else display else
        if (status != "success" && isSubmitting == true) {
            statusErr = "Error: Er is iets mis gegaan, probeer het opnieuw.";
            error = true;
        }

        else {
            statusErr = "";
        }

        $("#submitErr").text(statusErr);


        // Register successful, redirect
        if (isSubmitting == true && error == false) {
            window.location.replace("login.php?register=ok");
        }


    }, "json")
}

$(document).ready(function() {

    // Display errors while typing
    $("input").keyup(function() {
        post(false);
    });

    // When user submits form
    $("form").submit(function() {
        event.preventDefault();

        if (error == true) {
            $("#submitErr").text("Er zijn nog velden die niet kloppen!");
        }

        else {
            $("#submitErr").text("");
            post(true);
        }
    });



})