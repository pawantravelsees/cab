<?php
$minPrice = isset($minPrice) ? ceil($minPrice) : 100;
$maxPrice = isset($maxPrice) ? ceil($maxPrice) : 10000;
$price = [$minPrice, $maxPrice];
$values = str_replace(",", ";", json_encode($price));
// echo "<pre>";
// var_dump($values);
// echo "</pre>";
?>
<script>
    
    $(document).ready(function() {
        var $d4 = $("#demo");
        // console.log('<?//=$values?>');
        // return;
        // $d4.ionRangeSlider({
        //     skin: "round",
        //     min: <?//= $minPrice ?>,
        //     max: <?//= $maxPrice ?>,
        //     from: <?//= $minPrice ?>,
        //     to: <?//= $maxPrice ?>
        // });

        // $d4.on("change", function() {
        //     var $inp = $(this);
        //     var v = $inp.prop("value");
        //     var from = $inp.data("from");
        //     var to = $inp.data("to");

        //     console.log(v, from, to);
        // });

        $d4.ionRangeSlider({
             skin: "round",
                type: "duble",
                grid: false,
                prefix: "&#8377;",
                hide_min_max: true,
                // values: <?//=$values?>,
                min: <?= $minPrice ?>,
                max: <?= $maxPrice ?>,
                from: <?= $minPrice ?>,
                to: <?= $maxPrice ?>,
                onFinish: function(data) {
                    
                    // console.log('<?=$values?>');
                    // execute_form();
                }
            });
    });
</script>
