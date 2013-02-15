/*
 * Exercise 3-2, Realtime Web Apps
 *
 * @author Jason Lengstorf <jason@copterlabs.com>
 * @author Phil Leggetter <phil@leggetter.co.uk>
 */

(function($){

    // Handles receiving messages
    var pusher  = new Pusher('1507a86011e47d3d00ad'),
        channel = pusher.subscribe('exercise-3-2');

    // Turns on Pusher logging
    Pusher.log = function( msg ) {
        if( console && console.log ) {
            console.log( msg );
        }
    };


    // Adds an event listener for the custom event triggered by Pusher
    channel
        .bind(
            'send-message', 
            function(data) {
                var cont  = $('#messages');

                // Removes the placeholder LI if it's present
                cont.find('.no-messages').remove();

                // Adds the new message to the page
                $('<li>')
                    .html('<strong>'+data.name+':</strong> '+data.msg)
                    .appendTo(cont);
            }
        );

    // Handles form submission
    $('form').submit(function(){
        // Posts the form data so it can be sent to other browsers
        $.post('post.php', $(this).serialize());

        // Empties the input
        $('#message').val('').focus();

        // Prevents the default form submission
        return false;
    });

})(jQuery);
