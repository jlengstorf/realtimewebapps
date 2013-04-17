
<form method="post" class="answer"
      action="<?php echo $form_action; ?>">
    <input type="submit" value="Answer this question." />
    <input type="hidden" name="question_id"
           value="<?php echo $question_id; ?>" />
    <input type="hidden" name="room_id"
           value="<?php echo $room_id; ?>" />
    <input type="hidden" name="nonce"
           value="<?php echo $nonce; ?>" />
</form>
