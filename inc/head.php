<?php date_default_timezone_set('Asia/Kolkata'); ?>
<?php
$pagename = basename($_SERVER['SCRIPT_NAME']);
if ($pagename == "index.php") $title = "Cabs Home";
else if ($pagename == "search.php") $title = "SEARCH";
else if ($pagename == "results.php") $title = "RESULTS";
else if ($pagename == "cab_details.php") $title = "Cab Details";
else if($pagename=="payment.php") $title = "Payment tripodeal";
else if($pagename=="ticket.php") $title = "Ticket";
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
  <!-- <link rel="stylesheet" href="https://dev.tripodeal.com/css/style.css"> -->
  <link rel="stylesheet" href="./css/flatpickr.min.css">
  <link rel="stylesheet" href="./css/style.css?<?= uniqid(); ?>">
  <link rel="stylesheet" href="./css/ion.rangeSlider.css">
  <link rel="stylesheet" href="./css/ion.rangeSlider.min.css">
  <?php
  $style_swiperSlider = '<style>
    html,
    body {
      position: relative;
      height: 100%;
    }
    swiper-container {
      width: 100%;
      height: 100%;
    }
    swiper-slide {
      text-align: center;
      font-size: 18px;
      background: #444;
      display: flex;
      justify-content: center;
      align-items: center;
    }
    swiper-slide img {
      display: block;
      width: 100%;
      height: 100%;
      object-fit: cover;
    }
  </style>
        ';
  if ($pagename == 'index.php') {
    echo $style_swiperSlider;
  }
  ?>

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

<body>