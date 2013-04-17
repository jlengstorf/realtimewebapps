
<section>

    <header>
        <h2><?php echo $room_name; ?></h2>
        <p>
            Presented by <?php echo $presenter; ?>
            (<a href="mailto:<?php echo $email; ?>">email</a>)
        </p>
        <?php echo $controls; ?>
    </header>

    <?php echo $ask_form; ?>

    <ul id="questions" class="<?php echo $questions_class; ?>">
        <?php echo $questions; ?>
    </ul><!--/#questions-->

</section>
