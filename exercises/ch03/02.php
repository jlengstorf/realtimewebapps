<!doctype html>
<html lang="en">

    <head>
        <meta charset="utf-8" />
        <title>Realtime Web Apps &ndash; Exercise 3-2</title>
        <link rel="stylesheet" href="styles/layout.css" />
    </head>

    <body>

        <header>
            <h1>Send a Message with Pusher!</h1>
        </header>

        <section>
            <form method="post" action="post.php">
                <label>
                    Your Name
                    <input type="text" name="name" 
                           placeholder="i.e. John" />
                </label>
                <label>
                    Your Message
                    <input type="text" name="message" 
                           id="message" value="Hello world!" />
                </label>
                <input type="submit" class="input-submit" value="Send" />
            </form>
        </section>

        <aside>
            <h2>Received Messages</h2>
            <ul id="messages">
                <li class="no-messages">No messages yet...</li>
            </ul>
        </aside>

        <footer>
            <p>
                All content &copy; 2012 Jason Lengstorf &amp; Phil Leggetter
            </p>
        </footer>

        <script src="http://js.pusher.com/1.12/pusher.min.js"></script>
        <script 
            src="http://code.jquery.com/jquery-1.7.2.min.js"></script>
        <script src="scripts/init.js"></script>

    </body>

</html>
