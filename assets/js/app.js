/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you require will output into a single css file (app.css in this case)
require('../css/app.css');
require("@fortawesome/fontawesome-free/css/all.min.css");
require("@fortawesome/fontawesome-free/js/all.js");

// Need jQuery? Install it with "yarn add jquery", then uncomment to require it.
const $ = require('jquery');
require('bootstrap');

// autofill img input when uploading file
$(document).on('change', '.custom-file-input', function () {
    let fileName = $(this).val().replace(/\\/g, '/').replace(/.*\//, '');
    $(this).parent('.custom-file').find('.custom-file-label').text(fileName);
});

// load more comment function
$(function(){
    $(".comment-show").slice(0, 5).css("display", "flex");
    $(document).on("click", "#loadMoreComment", function(event){
        event.preventDefault();
        $(".comment-show:hidden").slice(0, 5).css("display", "flex");
        if($(".comment-show:hidden").length === 0){
            $("#loadMoreComment").attr("disabled", "disabled");
        }
    });
});