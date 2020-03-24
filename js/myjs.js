$("#bevestiging-tekst").hide();
$("#naam-vergeten-tekst").hide();

// Can still choose gifts
var canStillChoose = true;

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
        $("#bevestiging-tekst").show();
        $("#gast-naam").prop("disabled", true);
        canStillChoose = false;
    }
});

// Remove warning from entering name when focusing on the box
$("#gast-naam").focus(function() {
    $(this).removeClass("border border-danger");
    $("#naam-vergeten-tekst").hide();
});

// Make sure they can't press "Enter" when editing bruiloft parameters (things will look really weird)
$(".bruiloft-parameters").keypress(function (event) {
    if (event.keyCode === 10 || event.keyCode === 13) {
        event.preventDefault();
    }
});

// After editing bruiloft parameters it won't have borders anymore (but can still edit)
$(".bruiloft-parameters").blur(function() {
    $(this).removeClass("border");
});

var cadeau_naam;
var numberOfCadeaus = 4;

$("#nieuw-cadeau").click(function() {
    cadeau_naam = $("#cadeau-naam").val();
    numberOfCadeaus++;

    $(".geschenken-buttons-owner").append('<div class="btn-group geschenken-buttons-owner-row" id="cadeau' + numberOfCadeaus + '">' +
    '<button class="btn btn-lg btn-outline-primary" type="button">' + cadeau_naam +'</button>' +
    '<div class="input-group-append">' +
        '<button class="btn btn-success up-button" type="button">▲</button>' +
        '<button class="btn btn-success down-button" type="button">▼</button>' +
        '<button class="btn btn-danger delete-button" data-toggle="modal" type="button" data-target="#myModal">×</button>' +
    '</div>' +
'</div>');
});

var row_to_delete;

$(document).on("click", ".delete-button", function() {
    row_to_delete = $(this).parent().parent();
});

$("#verwijder-cadeau-button").click(function() {
    $(row_to_delete).remove();
})

var row_index;
var row_to_move;

$(document).on("click", ".up-button", function() {
    row_to_move = $(this).parent().parent();
    row_index = row_to_move.index() + 1;

    if (row_index > 1) {
        $(row_to_move).insertBefore($(".geschenken-buttons-owner-row:nth-child(" + (row_index - 1) + ")"));
    }
});

$(document).on("click", ".down-button", function() {
    row_to_move = $(this).parent().parent();
    row_index = row_to_move.index() + 1;

    $(row_to_move).insertAfter($(".geschenken-buttons-owner-row:nth-child(" + (row_index + 1) + ")"));
});

