<?php
$sid = $_REQUEST['sid'];
require '../helper/db.php';
$db = new db();
$results = $db->get_filter($sid);
// echo "<pre>";
// print_r($results);
// echo "</pre>";
// die;
$carTypes = [];
foreach ($results as $result) {
    $carTypes = explode(",", $result['car_types']);
    $minPrice = $result['price_min'];
    $maxPrice = $result['price_max'];
}

?>
<div class="card shadow-sm p-3 mb-3 rounded">
    <div class="d-flex align-items-center justify-content-between mb-3">
        <h5 class="">Filters</h5>
        <!-- <a href="">Reset All</a> -->
    </div>
    <form id="filterForm">
        <div class="mb-3">
            <div class="d-flex align-items-center justify-content-between mb-3 rounded">
                <label class="form-label font-weight-bold m-0">Car Type</label>
                <a id="carTypeFiterReset" href="javascript:void(0)">Reset</a>
            </div>
            <div class="d-flex flex-column gap-1">
                <?php
                for ($i = 0; $i < sizeof($carTypes); $i++) {
                ?>
                    <div class="form-check carTypeFilter">
                        <input class="form-check-input" name="carType[]" type="checkbox" value="<?= $carTypes[$i] ?>" id="<?= $carTypes[$i] ?>">
                        <label class="form-check-label" for="<?= $carTypes[$i] ?>"><?= ucfirst($carTypes[$i]) ?></label>
                    </div>
                <?php
                }
                ?>
            </div>
        </div>
        <div class="mb-3 priceRangeFilter">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <label class="form-label font-weight-bold m-0">Price Range</label>
                <a id="priceRangeReset" href="javascript:void(0)">Reset</a>
            </div>
            <input type="text" class="js-range-slider" id="demo" name="my_range"
                data-type="double"
                data-min="<?= $minPrice ?>"
                data-max="<?= $maxPrice ?>"
                data-from="<?= $minPrice ?>"
                data-to="<?= $maxPrice ?>"
                data-grid="false" />

        </div>
        <div>
            <div class="d-flex align-items-center justify-content-between mb-3">
                <label class="form-label font-weight-bold m-0">Seating Capacity </label>
                <a id="carCapacityReset" href="javascript:void(0)">Reset</a>
            </div>
            <div class="mb-3 d-flex flex-wrap gap-2 align-items-center seatingCapacityFilter">

                <div class="form-check ">
                    <input class="form-check-input" name="seat_type[]" type="checkbox" value="4" id="seat4">
                    <label class="form-check-label" for="seat4">
                        4 Seater
                    </label>
                </div>
                <div class="form-check ">
                    <input class="form-check-input" name="seat_type[]" type="checkbox" value="5" id="seat5">
                    <label class="form-check-label" for="seat5">
                        5 Seater
                    </label>
                </div>
                <div class="form-check ">
                    <input class="form-check-input" name="seat_type[]" type="checkbox" value="6" id="seat6">
                    <label class="form-check-label" for="seat6">
                        6 Seater
                    </label>
                </div>
                <div class="form-check ">
                    <input class="form-check-input" name="seat_type[]" type="checkbox" value="8" id="seat8">
                    <label class="form-check-label" for="seat8">
                        7+1 Seater
                    </label>
                </div>

            </div>
        </div>
    </form>
</div>
<?php
include "../js/range-slider.php";
include "../js/filter.php";
?>