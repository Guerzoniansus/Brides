// Can still choose gifts
var canStillChoose = true;

var listID = "";

function validateOwner() {
    if (canStillChoose == false) return;

    var ownerName = $("#gast-naam").val();

    $.post('ajax/list_ajax.php', {
        action: "validateOwner",
        listID: listID,
        ownerName: ownerName
    }, function(data) {
        if (data == "valid") {
            submitOwner(ownerName);
        }
        else {
            $("#ownerNameErr").text(data);
        }
    })
}

function submitOwner(ownerName) {
    var items = $(".btn-primary");
    itemNames = [];

    if (items.length == 0) {
        $("#ownerNameErr").text("Je hebt geen cadeaus gekozen!");
        return;
    }

    for (var i = 0; i < items.length; i++) {
        itemNames[i] = items[i].id;
    }

    $.post('ajax/list_ajax.php', {
        action: "submitOwner",
        listID: listID,
        ownerName: ownerName,
        "itemNames[]": itemNames
    }, function(data) {
        if (data == "success") {
            var d = new Date();
            var days = 300; //expire cookie after 300 days
            d.setTime(d.getTime() + (days*24*60*60*1000));
            var expires = "expires="+ d.toUTCString();
            document.cookie = listID + "=" + ownerName +";" + expires + "; path=/";
            window.location.reload();
        }
        else {
            $("#ownerNameErr").text("Er is iets fout gegaan, probeer het opnieuw");
        }
    })
}

$(document).ready(function() {
    canStillChoose = $("#canStillChoose").val();
    listID = $("#listID").val();

    $("#bevestiging-tekst").hide();
    $("#naam-vergeten-tekst").hide();

    if (canStillChoose == false) {
        $("#gast-naam").prop("disabled", true);
    }

    // When click on cadeau buttons, change the color
    $("div#geschenken-buttons-gasten .btn-outline-primary").click(function() {
        if (canStillChoose) {
            $(this).blur();
            $(this).toggleClass("btn-outline-primary btn-primary");
        }
    });

    // When click on bevestiging knop
    $("#bevestiging-knop").click(function() {

        // Check if naam is ingevoerd, and if not give a warning
        if ($("#gast-naam").val() == false) {
            $("#gast-naam").addClass("border border-danger");
            $("#naam-vergeten-tekst").show();
        }

        // Confirm bevestiging
        else {
            validateOwner();
            //$("#bevestiging-tekst").show();
            //$("#gast-naam").prop("disabled", true);
            //canStillChoose = false;

        }
    });

    // Remove warning from entering name when focusing on the box
    $("#gast-naam").focus(function() {
        $(this).removeClass("border border-danger");
        $("#naam-vergeten-tekst").hide();
        $("#ownerNameErr").text("");
    });


});

