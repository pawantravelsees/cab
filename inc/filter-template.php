<?php
$sid = $_REQUEST['sid'];
require '../helper/db.php';
$db = new db();
$results = $db->get_filter($sid);
$carTypes = [];
foreach ($results as $result) {
    $carTypes = explode(",", $result['car_types']);
    $minPrice = $result['price_min'];
    $maxPrice = $result['price_max'];
}
?>
<div class="card shadow-sm p-3 mb-3 rounded">
    <h5 class="mb-3">Filters</h5>
    <div class="mb-3">
        <label class="form-label fw-bold">Car Type</label>
        <div class="d-flex flex-column gap-1">
            <?php
            for ($i = 0; $i < sizeof($carTypes); $i++) {
            ?>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" value="<?= $carTypes[$i] ?>" id="<?= $carTypes[$i] ?>">
                    <label class="form-check-label" for="<?= $carTypes[$i] ?>"><?= ucfirst($carTypes[$i]) ?></label>
                </div>
            <?php
            }
            ?>
        </div>
    </div>
    <div class="mb-3">
        <label class="form-label fw-bold">Price Range</label>
        <input type="text" class="js-range-slider" id="demo" name="my_range" value=""
            data-type="double"
            data-min="<?= $minPrice ?>"
            data-max="<?= $maxPrice ?>"
            data-from="<?= $minPrice ?>"
            data-to="<?= $maxPrice ?>"
            data-grid="false" />

    </div>
    <div>
        <label class="form-label fw-bold">Seating Capacity (Excluding Driver)</label>
        <div class="mb-3 d-flex flex-wrap gap-2 align-items-center">

            <div class="form-check ">
                <input class="form-check-input" type="checkbox" value="4" id="seat4">
                <label class="form-check-label" for="seat4">
                    4 Seater
                </label>
            </div>
            <div class="form-check ">
                <input class="form-check-input" type="checkbox" value="5" id="seat5">
                <label class="form-check-label" for="seat5">
                    5 Seater
                </label>
            </div>
            <div class="form-check ">
                <input class="form-check-input" type="checkbox" value="6" id="seat6">
                <label class="form-check-label" for="seat6">
                    6 Seater
                </label>
            </div>
            <div class="form-check ">
                <input class="form-check-input" type="checkbox" value="8" id="seat8">
                <label class="form-check-label" for="seat8">
                    7+1 Seater
                </label>
            </div>

        </div>
    </div>
</div>
<?php
include "../js/range-slider.php";
?>