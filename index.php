<?php $name = getenv('MYNAME'); ?>

<h1> API details </h2>
    your name is <?php echo $name; ?>
    <h2> Routes & Endpoints </h2>
    <ul>
        <li>Get Movies - {GET} /movies </li>
        <li>Get Movie with ID - {GET} /movies/{ID}/ </li>
        <li>Get Movie rating with ID - {GET} /movies/{ID}/rating</li>
        <li> </li>
    </ul>