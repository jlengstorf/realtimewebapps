/**
 * Initialization script for Realtime Web Apps
 */
(function($) {

    channel.bind('room-close', function(data){ room.close(data); });
    channel.bind('room-open', function(data){ room.open(data); });
    channel.bind('question-create', function(data){ question.create(data); });
    channel.bind('question-vote', function(data){ question.vote(data); });
    channel.bind('question-answer', function(data){ question.answer(data); });

    var room = {
            open: function(data){
                console.log("Room "+data.room_id+" was opened.");
            },
            close: function(data){
                console.log("Room "+data.room_id+" was closed.")
            }
        },
        question = {
            create: function(data){
                console.log("Question "+data.question_id+" was created.");
            },
            vote: function(data){
                var question  = $('#question-'+data.question_id),
                    cur_count = question.data('count'),
                    new_count = cur_count+1;

                console.log(cur_count);

                // Updates the count
                question
                    .attr('data-count', new_count)
                    .data('count', new_count)
                    .addClass('new-vote')
                    .delay(1000)
                    .removeClass('new-vote');

                console.log(question.data('count'));
            },
            answer: function(data){
                console.log("Question "+data.question_id+" was answered.");
            }
        };

})(jQuery);
