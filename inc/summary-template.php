<?php
$sid = $_REQUEST['sid'];
require '../helper/db.php';
$db = new db();
$results = $db->get_results($sid);

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
                                                            $localCabKM = $item['inc_distance']." KM";
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