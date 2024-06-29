// jQuery(document).ready(function($) {
//     // Initialize the first tab and content as active
//     $('.tabs .tab-button:first-child').addClass('active');
//     $('.tab-content .tab-panel:first-child').addClass('active');

//     $('.vertical-tabs .tab-button:first-child').addClass('active');
//     $('.vertical-tab-content .tab-panel:first-child').addClass('active');

//     // Handle tab button click event
//     $('.tabs .tab-button').click(function(event) {
//         event.preventDefault();

//         // Remove active class from all tabs and content panels
//         $('.tabs .tab-button').removeClass('active');
//         $('.tab-content .tab-panel').removeClass('active');

//         // Add active class to the clicked tab button
//         $(this).addClass('active');

//         // Show the corresponding content panel
//         var tabId = $(this).attr('data-tab');
//         $('#'+tabId).addClass('active');
//     });
// });
jQuery(document).ready(function($) {
    $('.horizontal-tabs .tab-button:first-child').addClass('active');
    $('.tab-content .tab-panel:first-child').addClass('active');
    // Horizontal tabs click handler
    $('.horizontal-tabs .tab-button').click(function(event) {
        event.preventDefault();
        var tab_id = $(this).attr('data-tab');

        $('.horizontal-tabs .tab-button').removeClass('active');
        $('.vertical-tabs .tab-button').removeClass('active');
        $('.tab-panel').removeClass('active');

        $(this).addClass('active');
        $("#" + tab_id).addClass('active');
    });


    $('.vertical-tabs .tab-button:first-child').addClass('active');
    $('.tab-content .tab-panel:first-child').addClass('active');
    // Vertical tabs click handler
    $('.vertical-tabs .tab-button').click(function(event) {
        event.preventDefault();
        var tab_id = $(this).attr('data-tab');

        $('.vertical-tabs .tab-button').removeClass('active');
        //$('.horizontal-tabs .tab-button').removeClass('active');
        $('.vertical-tabs .tab-panel').removeClass('active');

        $(this).addClass('active');
        $("#" + tab_id).addClass('active');
    });
});
