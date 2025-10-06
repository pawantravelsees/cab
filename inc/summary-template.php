<?php
$sid = $_REQUEST['sid'];
require '../helper/db.php';
$db = new db();
// if (isset($_REQUEST['action']) == 'filter_call') {
$resultsummary = $db->get_filter($sid);
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
$dbMin = (isset($priceRange['price_min']) && is_numeric($priceRange['price_min'])) ? $priceRange['price_min'] : '';
$dbMax = (isset($priceRange['price_max']) && is_numeric($priceRange['price_max'])) ? $priceRange['price_max'] : '';

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
$resetBtn = "<button id='resetAllFilter' class='btn btn-outline-danger btn-sm  " . (($added_filt == "") ? 'd-none ' : 'mx-1') . "'>Reset All</button>";
$added_filt = "<div class='alert alert-primary  justify-content-between align-content-center" . (($added_filt == "") ? ' d-none ' : ' d-flex px-1 py-2') . "'><div>" . $added_filt . "</div>" . $resetBtn;
$added_filt .= "</div>";
echo $added_filt;
$currentPage = isset($_REQUEST['page']) ? (int)$_REQUEST['page'] : 1;
$results = $db->get_results($sid, $carType, $range, $seat_type, $short_by, $currentPage);
// echo "<pre>";
// print_r($resultsummary);
// echo "</pre>";
// die;
if (isset($resultsummary[0]['total_result'])) {
    $totalResults = $resultsummary[0]['total_result'];
} else {
    echo '<div class="alert alert-warning d-flex align-items-center gap-2" role="alert">
        <span class="material-symbols-outlined">info</span>
        No results found. Please adjust your search and try again.
      </div>';
    die;
};
// $totalResults = count($results['apiResponse']);
$limit = 3;
$pages = ceil($totalResults / $limit);
if ($currentPage < 1) $currentPage = 1;
if ($currentPage > $pages) $currentPage = $pages;
foreach ($results['apiResponse'] as $item) {
?>
    <div class="col-md-12 mb-3 w-auto p-2 d-flex  bg-white rounded items flight-summary">
        <div class="col-md-3 px-0 mx-0 border-right border-black text-center">
            <img src="
                    <?php
                    if ($item['car_type'] == "hatchback") {
                        echo "./img/hatchback.png";
                    } elseif ($item['car_type'] == "sedan") {
                        echo "./img/sedan.png";
                    } else {
                        echo "./img/suv.png";
                    }
                    ?>" class="car_img" alt="">
        </div>
        <div class="col-md-7 py-2 px-4">
            <div class="row">
                <span class="px-2 py-0 bg-warning rounded-pill small"><?= ucfirst($item['car_type']) ?></span>
            </div>
            <div class="row mt-1">
                <span class="text h5 m-0 font-weight-bold">
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
        <?php
        $originalPrice = ceil($item['price']);
        $discountPrice = 0;

        if ($item['price'] > 0 && $item['price'] < 1000) {
            $discountPrice = floatval($item['price'] - 0);
        } elseif ($item['price'] < 30000) {
            $discountPrice = floatval($item['price'] - 500);
        } else {
            $discountPrice = floatval($item['price'] - 1500);
        };

        // Calculate discount percentage
        $discountPercent = 0;
        if ($originalPrice > 0 && $discountPrice < $originalPrice) {
            $discountPercent = round((($originalPrice - $discountPrice) / $originalPrice) * 100);
        }
        ?>
        <div class="col-md-2 bg-light price_section d-flex flex-column align-items-end justify-content-center rounded">
            <div class="d-flex align-items-center justify-content-end gap-2">
                <?php if ($discountPercent > 0):
                ?>
                    <span class="badge badge-success position-absolute shimmer" style="font-size: 14px !important; top: -10px; right: -10px;"><i class=" text-white"><?php echo $discountPercent; ?>% off</i></span>
                <?php endif; ?>
                <strike>₹<?php echo number_format($originalPrice, 2); ?></strike>
            </div>
            <h3 class="m-0 font-weight-bold">₹<?php echo number_format($discountPrice, 2); ?></h3>
            <a href="cab_details.php?cid=<?= $item['id'] ?>&sid=<?= $sid ?>" rel="noopener noreferrer" class="text-decoration-none text-white">
                <button class="selectBtn">SELECT
                    <span class="material-symbols-outlined text-white">chevron_right</span>
                </button>
            </a>

        </div>

    </div>

<?php
}
?>
<div class="d-flex align-content-center justify-content-between">
    <nav aria-label="Page navigation example">
        <ul class="pagination justify-content-center">
            <li class="page-item <?= ($currentPage == 1) ? 'disabled' : '' ?>">
                <a class="page-link" page_no="<?= (($currentPage == 1) ? 1 : ($currentPage - 1)) ?>" href="result.php?sid=<?= $sid ?>&page=<?= $currentPage - 1 ?>" tabindex="-1">Previous</a>
            </li>
            <?php for ($i = 1; $i <= $pages; $i++): ?>
                <li class="page-item <?= ($i == $currentPage) ? 'active' : '' ?>">
                    <a class="page-link" page_no="<?= $i ?>" href="result.php?sid=<?= $sid ?>&page=<?= $i ?>"><?= $i ?></a>
                </li>
            <?php endfor; ?>

            <li class="page-item <?= ($currentPage == $pages) ? 'disabled' : '' ?>">
                <a class="page-link" page_no="<?= (($currentPage == $pages) ? $pages : ($currentPage + 1)) ?>" href="result.php?sid=<?= $sid ?>&page=<?= $currentPage + 1 ?>">Next</a>
            </li>

        </ul>
    </nav>
    <!-- <div class="d-flex justify-content-between align-items-start gap-2">
        <span class="text-muted">
            Showing <strong>1</strong> to <strong>10</strong> of <strong>100</strong> entries
        </span>

        <div>
            <label for="DataCount" class="form-label me-2">Show</label>
            <select id="DataCount" class="form-select form-select-lg " aria-label=".form-select-lg example">
                <option value="10" selected>10</option>
                <option value="25">25</option>
                <option value="50">50</option>
                <option value="100">100</option>
            </select>
        </div>
    </div> -->
</div>