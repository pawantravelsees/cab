<?php
function cab_filters($results)
{
    $filter_array = [];
    $prices = [];
    foreach ($results as $row) {
        if (!empty($row['car_type'])) {
            $filter_array['car_types'][] = $row['car_type'];
        }
        if (!empty($row['price'])) {
            $prices[] = $row['price'];
        }
    }
    if (!empty($prices)) {
        $filter_array['price_min'] = min($prices);
        $filter_array['price_max'] = max($prices);
    }
    return $filter_array;
}

function process_CBZ_cabs($results, $sid)
{

    $common_array = [];
    foreach ($results['fareChart'] as $data) {
        if ($data['carType'] == 'hatchback' || $data['carType'] == 'sedan') {
            $carCapacity = 4;
        } elseif ($data['carType'] == 'suv') {
            $carCapacity = 5;
        } elseif ($data['carType'] == 'innova (7+1)' || $data['carType'] == 'innova' || $data['carType'] == 'crysta' || $data['carType'] == 'crysta (7+1)' || $data['carType'] == 'suv (7+1)') {
            $carCapacity = 8;
        } else {
            $carCapacity = null; // fallback if type not matched
        }

        $common_array[] = [
            "sid" => $sid,
            "vendor" => "cbz",
            "card_id" => $data['carId'] ?? "",
            "car_type" => $data['carType'] ?? "",
            "car_capacity" => $carCapacity,
            "inc_distance" => isset($data['includedKm'])
                ? $data['includedKm']
                : (isset($results['details']['fareType'])
                    ? $results['details']['fareType']
                    : ""),
            "price" => $data['estimatedPrice'] ?? "",
            "gst" => $data['estimatedGst'] ?? "",
            "extra_price" => $data['extraKmPricePerKm'] ?? "", // extra price in KM
            "carrier_charge" => $data['carrierChargesWithoutGst'] ?? "",
            "carrier_gst" => (int)$data['carrierCharges'] - (int)$data['carrierChargesWithoutGst'],
            "new_car_charge" => $data['newCarChargesWithoutGst'],
            "new_car_gst" => (int)$data['newCarCharges'] - (int)$data['newCarChargesWithoutGst'],
            "pet_charge" => $data['petChargesWithoutGst'] ?? "",
            "pet_gst" => (int)$data['petCharges'] - (int)$data['petChargesWithoutGst'] ?? "",
            "total_price" => $data['tollInclusivePrice']['estimatedPrice'] ?? "",
            // "total_gst" => (int)$data['tollInclusivePrice']['estimatedGst'] ?? ""
            "total_gst" => (isset($data['tollInclusivePrice']['estimatedGst'])) ?? ""
        ];
    };

    return $common_array;
}