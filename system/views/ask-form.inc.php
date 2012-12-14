
    <form id="ask-a-question" method="post" 
          action="<?php echo FORM_ACTION; ?>">
        <label>
            If you have a question and you don’t see it below, ask it here.
            <input type="text" name="new-question" tabindex="1" />
        </label>
        <input type="submit" value="Ask" tabindex="2" />
        <input type="hidden" name="action" value="question-create" />
        <input type="hidden" name="room_id" 
               value="<?php echo $room_id; ?>" />
        <input type="hidden" name="nonce" 
               value="<?php echo $nonce; ?>" />
    </form><!--/#ask-a-question-->
