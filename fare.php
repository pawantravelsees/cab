<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>
    <?php
    echo "<pre>";
    print_r($_REQUEST['locs']);
    foreach ($_REQUEST['locs'] as $locs) {
        $locs = json_decode($locs, true);
        echo $locs['address'] . "<br>";
    }
    echo "<h1>Hello </h1>";
    ?>
</body>

</html>