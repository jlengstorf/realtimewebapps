<?php

    // Set the timezone and generate two formatted date strings
    date_default_timezone_set('US/Pacific');
    $datetime = date('c');
    $date_fmt = date('F d, Y');

?>
<!doctype html>
<html lang="en">

    <head>
        <meta charset="utf-8" />
        <title>Realtime Web Apps &ndash; Exercise 02-03</title>
        <link rel="stylesheet" href="styles/02.css" />
    </head>

    <body>

        <header>
            <h1><em>Realtime Web Apps</em> &ndash; Exercise 02-03</h1>
            <p>
                Published on 
                <time datetime="<?php echo $datetime; ?>">
                    <?php echo $date_fmt; ?>
                </time>.
            </p>
        </header>

        <section>
            <p>
                This is an example HTML file to demonstrate really basic 
                HTML5 markup.
            </p>
            <p>
                We're using several of the new HTML5 elements, including 
                the <code>&lt;section&gt;</code> and 
                <code>&lt;time&gt;</code> elements.
            </p>
        </section>

        <footer>
            <p>All content &copy; 2012 Jason Lengstorf &amp; Phil Leggetter</p>
        </footer>

        <script src="http://code.jquery.com/jquery-1.7.2.min.js"></script>
        <script src="scripts/03.js"></script>

    </body>

</html>
