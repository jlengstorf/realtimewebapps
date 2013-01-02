
        <form id="close-this-room" method="post" 
              action="<?php echo $form_action; ?>">
            <input type="submit" value="Open This Room" />
            <input type="hidden" name="room_id" 
                   value="<?php echo $room_id; ?>" />
            <input type="hidden" name="nonce" 
                   value="<?php echo $nonce; ?>" />
        </form><!--/#close-this-room-->
