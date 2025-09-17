<?php
$sid = $_REQUEST['sid'];
require '../helper/db.php';
$db = new db();
// if (isset($_REQUEST['action']) == 'filter_call') {

$carType = (isset($_REQUEST['carType'])) ? $_REQUEST['carType'] : [];
$range = (isset($_REQUEST['my_range'])) ? array_unique(explode(';', $_REQUEST['my_range'])) : "";
$seat_type = (isset($_REQUEST['seat_type'])) ? $_REQUEST['seat_type'] : [];
$short_by = (isset($_REQUEST['short_by'])) ? $_REQUEST['short_by'] : "";
$my_array = [
    'car_type' => $carType,
    'price_range' => $range,
    'seat_type' => $seat_type,
    'short_by' => $short_by
];
$priceRange = $db->get_price_range($sid);
$dbMin = (isset($priceRange['price_min']) ? $priceRange['price_min'] : "");
$dbMax = (isset($priceRange['price_max']) ? $priceRange['price_max'] : "");

$added_filt = "";

foreach ($my_array as $k => $filter) {
    if (empty($filter)) continue;
    if ($k == "car_type") {
        $added_filt .= "<button type='button' class='btn btn-outline-danger btn-sm mx-1 filterAppliedBtn'  id='carType' data-name=" . $k . ">" . ucwords(str_replace('_', ' ', 'car_type')) . "</button>";
    }
    if ($k == "seat_type") {
        $added_filt .= "<button type='button' class='btn btn-outline-danger btn-sm mx-1 filterAppliedBtn' id='seatCapacity' data-name=" . $k . ">" . ucwords(str_replace('_', ' ', 'seat_type')) . "</button>";
    }
    if ($k == "price_range") {
        if ((isset($range[0]) && $range[0] != $dbMin) || (isset($range[1]) && $range[1] != $dbMax)) {
            $added_filt .= "<button type='button' class='btn btn-outline-danger btn-sm mx-1 filterAppliedBtn' id='priceRange' data-name=" . $k . ">" . ucwords(str_replace('_', ' ', 'price_range')) . "</button>";
        }
    }
    if ($k == "short_by") {
        $added_filt .= "<button type='button' class='btn btn-outline-danger btn-sm mx-1 filterAppliedBtn' id='sortReset' data-name=" . $k . ">" . ucwords(str_replace('_', ' ', 'short_by')) . "</button>";
    }
}
$added_filt = "<div class='alert alert-primary " . (($added_filt == "") ? 'd-none ' : 'px-1 py-2') . "'>" . $added_filt;
$added_filt .= "</div>";
echo $added_filt;
// echo "<pre>";
// print_r($_REQUEST);
// echo "</pre>";
// die;
$results = $db->get_results($sid, $carType, $range, $seat_type, $short_by);
foreach ($results['apiResponse'] as $item) {
?>
    <div class="col-md-12 mb-3 h-auto w-auto p-2 d-flex border border-gray rounded items">
        <div class="col-md-3 px-0 mx-0 border-right border-black">
            <img src="
                    <?php
                    if ($item['car_type'] == "hatchback") {
                        echo "./img/hatchback.svg";
                    } elseif ($item['car_type'] == "sedan") {
                        echo "./img/sedan.svg";
                    } else {
                        echo "./img/suv.svg";
                    }
                    ?>" class="car_img" alt="">
        </div>
        <div class="col-md-7 py-2 px-4">
            <div class="row">
                <span class="px-2 py-0 bg-warning rounded-pill small"><?= ucfirst($item['car_type']) ?></span>
            </div>
            <div class="row mt-1">
                <span class="text h5 m-0">
                    <?php
                    if ($item['car_type'] == "hatchback") {
                        echo "Swift, WagonR or Similar";
                    } elseif ($item['car_type'] == "sedan") {
                        echo "Dzire, Etios or Similar";
                    } else {
                        echo "Innova, Ertiga, Marazzo or Similar";
                    }
                    ?></span>
            </div>
            <div class="row mt-1">
                <div class="col-md-10 m-0 p-0">
                    <ul class="list-unstyled p-0 m-0 inlcuded_list">
                        <li class="m-0 p-0">
                            <span class="mr-2">Included Km</span>
                            <span class=" text-end"><?php
                                                    $localCabKM = "";
                                                    if (isset($item['inc_distance'])) {
                                                        if ($item['inc_distance'] == "2_20") {
                                                            $localCabKM = "2 Hours , 20Km";
                                                        } else if ($item['inc_distance'] == "4_40") {
                                                            $localCabKM = "4 Hours , 40Km";
                                                        } else if ($item['inc_distance'] == "8_80") {
                                                            $localCabKM = "8 Hours , 80Km";
                                                        } else if ($item['inc_distance'] == "12_120") {
                                                            $localCabKM = "12 Hours , 120Km";
                                                        } else {
                                                            $localCabKM = $item['inc_distance'] . " KM";
                                                        }
                                                    }
                                                    echo $localCabKM;
                                                    ?></span>
                        </li>
                        <li class="m-0 p-0 ">
                            <span class="mr-2">Extra fare/Km</span>
                            <span class="text-end"><?php echo "₹" . $item['extra_price'] . "/KM" ?></span>
                        </li>
                        <li class="m-0 p-0 ">
                            <span class="mr-2">Fuel Charges</span>
                            <span class="text-end">Included</span>
                        </li>
                        <li class="m-0 p-0 ">
                            <span class="mr-2">Driver Charges</span>
                            <span class=" text-end">Included</span>
                        </li>
                        <li class="m-0 p-0 ">
                            <span class="mr-2">Night Charges</span>
                            <span class="text-end">Included</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="col-md-2 bg-light price_section d-flex flex-column align-items-end justify-content-center rounded">
            <strike class=""><?php echo "₹" . floatval($item['price']); ?></strike>
            <h3 class="m-0 font-weight-bold "><?php echo "₹" . floatval($item['price']); ?></h3>
            <button class="selectBtn">SELECT <span class="material-symbols-outlined">
                    chevron_right
                </span></button>
        </div>
    </div>

<?php
}
?>