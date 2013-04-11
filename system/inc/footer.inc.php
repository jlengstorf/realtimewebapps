
<footer>
    <ul>
        <li class="copyright">
            &copy; 2013 Jason Lengstorf &amp; Phil Leggetter
        </li><!--/.copyright-->
        <li>
            Part of <em>Realtime Web Apps: With HTML5 WebSocket, PHP, 
                and jQuery</em>. 
        </li>
        <li>
            <a href="http://amzn.to/XKcBbG">Get the Book</a> | 
            <a href="http://cptr.me/UkMSmn">Source Code (on GitHub)</a>
        </li>
    </ul>
</footer>

<script src="http://js.pusher.com/1.12/pusher.min.js"></script>
<script src="http://code.jquery.com/jquery-1.8.2.min.js"></script>
<script>
    var pusher  = new Pusher('<?php echo PUSHER_KEY; ?>'),
        channel = pusher.subscribe('<?php echo $channel; ?>');
</script>
<script src="<?php echo APP_URI; ?>assets/scripts/init.js"></script>

</body>

</html>
