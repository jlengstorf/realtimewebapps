
        <form id="close-this-room" method="post" 
              action="<?php echo $form_action; ?>">
            <label>
                Link to your room.
                <input type="text" name="room-uri" 
                       value="<?php echo $room_uri; ?>" 
                       disabled />
            </label>
            <input type="submit" value="Close This Room" />
            <input type="hidden" name="room_id" 
                   value="<?php echo $room_id; ?>" />
            <input type="hidden" name="nonce" 
                   value="<?php echo $nonce; ?>" />
        </form><!--/#close-this-room-->
