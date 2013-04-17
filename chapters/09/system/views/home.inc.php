<section>

    <form id="attending" method="post"
          action="<?php echo $join_action; ?>">
        <h2>Attending?</h2>
        <p>Join a room using its ID.</p>
        <label>
            What is the room's ID?
            <input type="text" name="room_id" />
        </label>
        <input type="submit" value="Join This Room" />
        <input type="hidden" name="nonce"
               value="<?php echo $nonce; ?>" />
    </form><!--/#attending-->

    <form id="presenting" method="post"
          action="<?php echo $create_action; ?>">
        <h2>Presenting?</h2>
        <p>Create a room to start your Q&amp;A session.</p>
        <label>
            Tell us your name (so attendees know who you are).
            <input type="text" name="presenter-name" />
        </label>
        <label>
            Tell us your email (so attendees can get in touch with you).
            <input type="email" name="presenter-email" />
        </label>
        <label>
            What is your session called?
            <input type="text" name="session-name" />
        </label>
        <input type="submit" value="Create Your Room" />
        <input type="hidden" name="nonce"
               value="<?php echo $nonce; ?>" />
    </form><!--/#presenting-->

</section>
