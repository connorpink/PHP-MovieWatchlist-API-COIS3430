<?php $name = getenv('MYNAME'); ?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Index Page</title>
</head>

<body>
    <h1>This is the index page. Your name is <?= $name ?></h1>

    <?php var_dump($_SERVER); ?>
</body>

</html>