var cadeau_naam;
var errorParameters = false;
var errorAdd = false;
var errorSave = false;

var changesMade = false;

// OLD VARIABLES --- VAN PERIODE 1
var numberOfCadeaus = 4;
var row_to_delete;
var row_index;
var row_to_move;

// Show warning message when user tries to leave the page with unsaved changes
window.onbeforeunload = function (e) {
    if (changesMade == true) {
        return "Are you sure you want to leave?";
    }
}

function hideSaveMessage() {
    $("#saveSuccess").text("")
}

$(document).ready(function() {

    // *-*-*-* First all the Ajax stuff, later on the regular JQuery stuff *-*-*-*

    /**
     * When typing any bruiloft parameters, do live verification with AJAX
     * And set error to TRUE if anything is wrong
     */
    $(".bruiloft-parameters").keyup(function() {
        changesMade = true;
        hideSaveMessage();

        var name1 = $("#name1").text();
        var name2 = $("#name2").text();
        var date = $("#date").text();
        var description = $("#description").text();
        var action = "validateParameters"

        /**
         * $.post(URL, DATA {key: value}, function (data, status) { }, "json")
         */
        $.post("/ajax/mylist_ajax.php", {
            name1: name1,
            name2: name2,
            date: date,
            description: description,
            action: action
        }, function (data) {
            // Things to do on receive

            if (data != null) {
                $("#nameErr").text(data.nameErr);
                $("#dateErr").text(data.dateErr);
                $("#descriptionErr").text(data.descriptionErr);

                // Check if anything was wrong
                errorParameters = data.error;
            }

            // Make save error invisible if no problems left
            if (errorParameters == false) {
                $("#saveErr").text("");
            }

        }, "json")


    });

    /**
     * Adding a gift, do gift name verification with AJAX
     * Set error to TRUE if there's problems with the name, otherwise add it
     */
    $("#nieuw-cadeau").click(function() {
        cadeau_naam = $("#cadeau-naam").val();
        var action = "validateGiftName";

        hideSaveMessage();

        // Check if gift with this name already exists
        // It looks weird, but it checks case INSENSITIVE (so kAt == KaT == KAT)
        if (document.querySelector('[id^="' + cadeau_naam + '" i]')) {
            $("#giftNameErr").text("Dit cadeau bestaat al");
            return;
        }

        /**
         * $.post(URL, DATA {key: value}, function (data, status) { }, "json")
         */
        $.post("/ajax/mylist_ajax.php", {
            giftName: cadeau_naam,
            action: action
        }, function (data, status) {
            // Things to do on receive

            if (data != null) {
                $("#giftNameErr").text(data.giftNameErr);

                // Check if anything was wrong
                errorAdd = data.error;
            }

            if (status != "success") {
                errorAdd = true;
                $("#giftNameErr").text("Error: Er is iets misgegaan, probeer het opnieuw");
            }

            // Add red warning border if error
            if (errorAdd == true) {
                $("#cadeau-naam").addClass("border-danger");
                return;
            }

            // No problems with the name, add the gift
            if (errorAdd == false) {
                numberOfCadeaus++;
                changesMade = true;
                $("#cadeau-naam").val("");

                // OLD CODE, KEEPING IT IN CASE SOMETHING BREAKS: $(".geschenken-buttons-owner").append('<div class="btn-group geschenken-buttons-owner-row" id="cadeau' + numberOfCadeaus + '">' +

                $(".geschenken-buttons-owner").append('<div class="btn-group geschenken-buttons-owner-row" id="' + cadeau_naam + '">' +
                    '<button class="btn btn-lg btn-outline-primary" type="button">' + cadeau_naam.toUpperCase() +'</button>' +
                    '<div class="input-group-append">' +
                    '<button class="btn btn-success up-button" type="button">▲</button>' +
                    '<button class="btn btn-success down-button" type="button">▼</button>' +
                    '<button class="btn btn-danger delete-button" data-toggle="modal" type="button" data-target="#myModal">×</button>' +
                    '</div>' +
                    '</div>');
            }

        }, "json")


    });


    /**
     * Clicking the save button to save the list
     */
    $("#savebutton").click(function() {

        if (errorParameters == true) {
            $("#saveErr").text("Er zijn nog fouten aanwezig, opslaan mislukt.");
            return;
        }

        // Remove warning message
        $("#saveErr").text("");

        var name1 = $("#name1").text();
        var name2 = $("#name2").text();
        var date = $("#date").text();
        var description = $("#description").text();
        var action = "saveList"

        var items = $('.geschenken-buttons-owner-row');
        var itemNames = [];

        for (var i = 0; i < items.length; i++) {
            itemNames[i] = items[i].id;
        }

        /**
         * $.post(URL, DATA {key: value}, function (data, status) { }, "json")
         */
        $.post("/ajax/mylist_ajax.php", {
            name1: name1,
            name2: name2,
            date: date,
            description: description,
            "itemNames[]": itemNames,
            action: action
        }, function (data, status) {
            // Things to do on receive

            if (data != null) {
                $("#saveErr").text(data.saveErr);

                // Check if anything was wrong
                errorSave = data.error;

                if (errorSave == true) {
                    if (data.errorType == "parameters") {
                        $("#nameErr").text(data.nameErr);
                        $("#dateErr").text(data.dateErr);
                        $("#descriptionErr").text(data.descriptionErr);
                    }
                }
            }

            if (status != "success") {
                errorSave = true;
                $("#saveErr").text("Error: Er is iets misgegaan, probeer het opnieuw");
            }

            // No problems, save succesful
            if (errorSave == false) {
                $("#saveSuccess").text("Lijst is opgeslagen!");
                changesMade = false;

                setTimeout(function(){
                    hideSaveMessage();
                }, 10000);
            }


        }, "json")



    });



    /*
    *-*-*-* UNDERNEATH ARE NON-AJAX RELATED JQUERY STUFF *-*-*-*
     */


    // When typing gift name, remove error messages
    $("#cadeau-naam").keyup(function() {
        $("#giftNameErr").text("");
        $("#cadeau-naam").removeClass("border-danger");
    });


    // Make sure they can't press "Enter" when editing bruiloft parameters (things will look really weird)
    $(".bruiloft-parameters").keypress(function (event) {
        if (event.keyCode === 10 || event.keyCode === 13) {
            event.preventDefault();
        }
    });

    $(document).on("click", ".delete-button", function() {
        row_to_delete = $(this).parent().parent();
    });

    $("#verwijder-cadeau-button").click(function() {
        var itemName = $(row_to_delete).attr("id");
        //$("#owner-" + itemName).remove();
        $(row_to_delete).remove();
        changesMade = true;
        hideSaveMessage();
    });

    $(document).on("click", ".up-button", function() {
        row_to_move = $(this).parent().parent();
        row_index = row_to_move.index() + 1;

        if (row_index > 1) {
            $(row_to_move).insertBefore($(".geschenken-buttons-owner-row:nth-child(" + (row_index - 1) + ")"));
            changesMade = true;
            hideSaveMessage();
        }
    });

    $(document).on("click", ".down-button", function() {
        row_to_move = $(this).parent().parent();
        row_index = row_to_move.index() + 1;
        changesMade = true;
        hideSaveMessage();

        $(row_to_move).insertAfter($(".geschenken-buttons-owner-row:nth-child(" + (row_index + 1) + ")"));
    });

});



