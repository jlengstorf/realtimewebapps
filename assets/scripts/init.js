/**
 * Initialization script for Realtime Web Apps
 */
(function($) {

    channel.bind('close',  function(data){ room.close(data);      });
    channel.bind('open',   function(data){ room.open(data);       });
    channel.bind('ask',    function(data){ question.ask(data);    });
    channel.bind('vote',   function(data){ question.vote(data);   });
    channel.bind('answer', function(data){ question.answer(data); });

    var nonce = $('input[name=nonce]:eq(0)').val(),
        room = {
            open: function(data){
                location.reload();
            },
            close: function(data){
                location.reload();
            }
        },
        question = {
            ask: function(data){
                $(data.markup)
                    .find('input[name=nonce]').val(nonce).end()
                    .hide().prependTo('#questions').slideDown('slow');
            },
            vote: function(data){
                var question  = $('#question-'+data.question_id),
                    cur_count = question.data('count'),
                    new_count = cur_count+1;

                // Updates the count
                question
                    .attr('data-count', new_count)
                    .data('count', new_count)
                    .addClass('new-vote');

                setTimeout(1000, function(){
                    question.removeClass('new-vote');
                });
            },
            answer: function(data){
                var question = $("#question-"+data.question_id),
                    detach_me = function() {
                        question
                            .detach()
                            .appendTo('#questions')
                            .slideDown(500);
                    }

                question
                    .addClass('answered')
                    .delay(1000)
                    .slideUp(500, detach_me);
            }
        };

})(jQuery);
