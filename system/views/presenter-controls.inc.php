
        <form id="close-this-room" method="post" 
              action="<?php echo FORM_ACTION; ?>">
            <label>
                Link to your room.
                <input type="text" name="room-url" 
                       value="<?php echo APP_URL . "room/". $room_id; ?>" 
                       disabled />
            </label>
            <input type="submit" value="Close This Room" />
            <input type="hidden" name="action" value="room-close>" />
            <input type="hidden" name="room_id" 
                   value="<?php echo $room_id; ?>" />
            <input type="hidden" name="nonce" 
                   value="<?php echo $nonce; ?>" />
        </form><!--/#close-this-room-->
