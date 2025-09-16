<?php
require './inc/head.php';
require './inc/header.php';
require 'helper/db.php';
require 'helper/result_helper.php';
$db = new db();
$sid = $_GET['sid'];
$db->get_search_request($sid);
$results = $db->get_results($sid);
$results = formate_search_request($results);
?>
<div class="bg-modify mb-0">
    <div class="container d-lg-block d-none p-1">

        <div class="row  mb-0 align-items-center m-2 my-lg-0 p-1 px-lg-0 no-gutters">
            <div class="col">
                <div class="d-flex align-items-center text-white">
                    <span class="material-symbols-outlined mr-1" style="font-variation-settings: 'FILL' 1; font-size: 20px;">location_on</span>
                    <span class="mr-2 font-weight-bold"><?= $results['cities'][0]['address'] ?>,</span>
                    <!-- <span class="material-symbols-outlined mx-1" style="font-variation-settings: 'FILL' 1; font-size: 20px;">date_range</span> -->
                    <span class="font-weight-bold mr-2"><?= date('D d, M h:i', strtotime($results['trip_info']['departure_date'])) ?> - </span>
                    <span class="material-symbols-outlined">
                        departure_board
                    </span>
                    <span class="font-weight-bold mr-3"><?= date('D d, M', strtotime($results['trip_info']['arrival_date'])) ?></span>
                    <span class="material-symbols-outlined mr-1" style="font-variation-settings: 'FILL' 1; font-size: 20px;">directions_car</span>
                    <span class="mr-3"><?= $results['trip_info']['trip_type'] ?></span>
                    <div class="ml-auto">
                        <button class="btn text-white bgshade-2 btn-lg px-3 py-2 edit round mr-0 text-center" style="border: 0; font-size: 16px;" data-toggle="modal" data-target="#edit_form"><span class="material-symbols-outlined edit">
                                edit
                            </span> Change</button>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <div class="d-block d-lg-none">
        <div>
            <div class="container py-2">
                <div class="d-flex mt-1 justify-content-center text-center text-white d-block d-lg-none">
                    <button class="btn left-icon bg-none round border ml-3" style="padding: 6.5px 10px !important; height:40px;" id="filterShow">
                        <svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 26 26" style="enable-background:new 0 0 26 26; width: 18px; padding-bottom: 3px;" xml:space="preserve">
                            <g>
                                <path style="fill: white;" d="M22.2,0H3.8C1.7,0,0,1.7,0,3.8c0,1,0.4,2,1.1,2.7L9,14.4v9.2c0,1.3,1.1,2.4,2.4,2.4c0.6,0,1.3-0.3,1.7-0.7l3-3
									c0.6-0.6,0.9-1.3,0.9-2.1v-5.8l7.9-7.9C25.6,5.8,26,4.8,26,3.8C26,1.7,24.3,0,22.2,0z"></path>
                            </g>
                        </svg>
                    </button>
                    <h5 class="text-truncate">Delhi</h5>

                    <span class=" p-md-1 border border-white round mr-3 edit mt-0 right-icon" style="padding: 6px 10px ; height: 40px; width: 40px; cursor: pointer;" data-toggle="modal" data-target="#edit_form"><i class="zmdi zmdi-edit zmdi-hc-2x"></i>
                    </span>
                </div>
                <div class="text-center text-white">Fri, 05 Sep | Sat, 06 Sep |
                    2 Guests</div>
            </div>
        </div>
    </div>
</div>
<div class="container mt-2">
    <ul class="d-flex justify-content-start align-content-center result_your_itinerary m-0 p-0 mb-3">
        <?php
        foreach ($results['cities'] as $city) {
        ?>
            <li class="border-0 itinerary_list"><?= $city['address'] ?></li>
            <span class="material-symbols-outlined itinerary_arrow"> arrow_forward </span>
        <?php
        }
        ?>
    </ul>
</div>

<div class="container d-flex align-items-start gap-3">
    <div class="col-md-3 h-100">
        <div class="d-flex justify-content-between gap-2 text-center toll_and_without_toll ">
            <div class="col-md-6 py-1 px-2 bg-light rounded active">Best Price</div>
            <div class="col-md-6  py-1 px-2 bg-light rounded">With Tax</div>
        </div>
    </div>
    <div class="col-md-9 h-auto w-auto p-2 d-flex  border border-gray rounded">
        <div class="col-md-3 px-0 mx-0">
            <img src="./img/car-white-svgrepo-com (2).svg" class="car_img" alt="">
        </div>
        <div class="col-md-7 bg-light">d</div>
        <div class="col-md-2 bg-dark">f</div>
    </div>
</div>

<?php
echo "<pre>";
print_r($results);
echo "</pre>";
require './inc/footer.php';
?>
<?php
require './inc/foot.php';
?>