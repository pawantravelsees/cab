<?php
$minPrice = isset($minPrice) ? $minPrice : 100;
$maxPrice = isset($maxPrice) ? $maxPrice : 10000;
?>
<script>
    $(document).ready(function() {
        var $d4 = $("#demo");

        $d4.ionRangeSlider({
            skin: "round",
            min: <?= $minPrice ?>,
            max: <?= $maxPrice ?>,
            from: <?= $minPrice ?>,
            to: <?= $maxPrice ?>
        });

        $d4.on("change", function() {
            var $inp = $(this);
            var v = $inp.prop("value");
            var from = $inp.data("from");
            var to = $inp.data("to");

            console.log(v, from, to);
        });
    });
</script>
