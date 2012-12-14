
        <li id="question-<?php echo $question_id; ?>" 
            data-count="<?php echo $vote_count; ?>"
            class="<?php echo $voted_class, ' ', $answered_class; ?>">
            <?php echo $answer_link; ?> 
            <p>
                <?php echo $question; ?> 
            </p>
            <?php echo $vote_link; ?> 
        </li><!--/#question-<?php echo $question_id; ?>-->
