<?php
$pagename = basename($_SERVER['SCRIPT_NAME']);
if($pagename=="index.php") $title = "Cabs Home";
else if($pagename=="search.php") $title = "SEARCH";
else if($pagename=="results.php") $title = "RESULTS";
// else if($pagename=="index.php") $title = "HOME";
// else if($pagename=="index.php") $title = "HOME";
else $title = "";
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tripodeal | <?= $title ?></title>
    <link rel="stylesheet" href="./css/bootstrap.min.css">
    <link rel="stylesheet" href="./css/default.css">
    <link rel="stylesheet" href="./css/default.date.css">
    <link rel="stylesheet" href="./css/css2.css">
    <link rel="stylesheet" href="./css/tripodeal.css">
    <link rel="stylesheet" href="./css/flatpickr.min.css">
    <link rel="stylesheet" href="./css/style.css?<?=uniqid();?>">
    <link rel="stylesheet" href="./css/ion.rangeSlider.css">
    <link rel="stylesheet" href="./css/ion.rangeSlider.min.css">

    <style>
        .material-symbols-outlined {
            font-variation-settings:
                'FILL' 0,
                'wght' 300,
                'GRAD' 0,
                'opsz' 24
        }

        .icon-2x {
            font-size: 32px !important;
        }

        .icon-3x {
            font-size: 40px !important;
        }

        .icon-4x {
            font-size: 48px !important;
        }

        .icon-5x {
            font-size: 56px !important;
        }
    </style>
</head>

<body >
