<?php
require("MysqliDb.php");
class db
{
    private $host, $user, $pass, $dbname, $db;

    function __construct()
    {
        $this->db = new MysqliDb();
        // $this->host = "localhost";
        // $this->user = "root";
        // $this->pass = "";
        // $this->dbname = "cabs";
        // $this->db = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->dbname, $this->user, $this->pass);
    }

    function insert_search_request($searchRequest)
    {
        // return $searchRequest;
        $id = $this->db->insert("search_request", $searchRequest);
        return $id;
    }

    function get_search_request($sid)
    {
        $result = $this->db->where("id", $sid)->get("search_request");
       
        if (!empty($result)) {
            if ($result[0]['trip_type'] == "o") {
                $detailsWithPlaceId = $this->get_place_id($result);
                return $detailsWithPlaceId;
            } elseif ($result[0]['trip_type'] == "l") {
                $details = $this->get_local_city($result);
                return [
                    "details" => [$details]
                ];
            } elseif ($result[0]['trip_type'] == "a") {
                $results = $this->get_airport_details($result);
                return [
                    "details" => [$results]
                ];
            }
        } else {
            echo '
                <div class="container">
                   <h1> 404 page not found please try again</h1>
                </div>
                ';
            exit;
        }
    }

    function get_airport_details($results)
    {
        $airportPickUpId = $results[0]['pickup_id'];
        
        // echo "Pickup ID: ".$airportPickUpId."<br>";
        $airport = $this->db->where('id', $airportPickUpId)->getOne('airports' , 'airport_id');
        $jsonRequest = [
            "trip_type" => $results[0]['trip_type'],
            "departureAt" => date('d-m-Y H:i', strtotime($results[0]['departure_date'])),
            "airportId" => $airport['airport_id'],
            "destinationCity" => $results[0]['destination'],
            "fareType" => ($results[0]['airport_from_to'] == 2) ? "from-airport" : "to-airport",
        ];
        return $jsonRequest;
    }
    function get_local_city($pickupCity)
    {
        $cityId = $pickupCity[0]['pickup_id'];
        $cityPlaceId = $this->db->where('id', $cityId)->getOne('local_cities', 'place_id');
        $jsonRequest = [
            "trip_type" => $pickupCity[0]['trip_type'],
            "cityId" => $cityPlaceId['place_id'],
            "departureAt" => date('d-m-Y H:i', strtotime($pickupCity[0]['departure_date'])),
            "fareType" => $pickupCity[0]['local_city_package']
        ];
        return $jsonRequest;
    }
    function get_place_id($detailsWithPlaceId)
    {
        $city = [];
        $pickPlaceId = $detailsWithPlaceId[0]['pickup_id'];
        $departurePlace = $this->db->where("id", $pickPlaceId)->getOne("cities", "place_id");
        $departurePlaceId = $departurePlace['place_id'];
        $city[] = [
            "id"      => $departurePlaceId,
            "address" => $detailsWithPlaceId[0]['pickup'] ?? ""
        ];
        $moreCity = json_decode($detailsWithPlaceId[0]['more_cities'], true);
        if (!empty($moreCity)) {
            foreach ($moreCity as $singleCity) {
                $placeId = $this->db->where("id", $singleCity['id'])->getOne("cities", "place_id");
                $city[] = [
                    "id"      => $placeId['place_id'],
                    "address" => $singleCity['address']
                ];
            }
        }
        $deparPlaceId = $detailsWithPlaceId[0]['destination_id'];
        $arrivalPlace = $this->db->where("id", $deparPlaceId)->getOne("cities", "place_id");
        $arrivalPlaceId = $arrivalPlace['place_id'];

        $city[] = [
            "id"      => $arrivalPlaceId,
            "address" => $detailsWithPlaceId[0]['destination'] ?? ""
        ];

        return [
            "details" => $detailsWithPlaceId,
            "city" => $city
        ];
    }

    function insert_booking_details($common_array)
    {
        foreach ($common_array as $array) {
            $this->db->insert("car_results", $array);
        }
    }

    function get_results($sid)
    {
        $serchResult = $this->db->where('id', $sid)->get('search_request');
        $apiResponse = $this->db->where('sid', $sid)->get('car_results');

        return [
            'searchResult' => $serchResult,
            'apiResponse' => $apiResponse
        ];
    }

    function get_filter($sid)
    {
        $filterResult = $this->db->where('sid', $sid)->get("cab_filters");
        return $filterResult;
    }

    function insert_cab_filters($filter_array, $sid)
    {
        $array = [];
        if (isset($filter_array['car_types'])) {
            $carTypes = implode(',', $filter_array['car_types']);
            $array = [
                "car_types" => $carTypes,
                "price_min" => $filter_array['price_min'],
                "price_max" => $filter_array['price_max'],
                "sid" => $sid
            ];
            $this->db->insert("cab_filters", $array);
        }
        return $array;
    }

    // function create_table($sql)
    // {
    //   $this->db->query($sql);

    // }

    function suggestion($query)
    {
        $this->db->where("city_name", "$query%", "LIKE");
        $this->db->orderBy("city_name", "ASC");
        $result = $this->db->get("cities", 20, ["city_name", "place_id", 'id']);

        return $result;
    }


    function get_local_cities()
    {
        $data = [];
        $localCity =  $this->db->get('local_cities');
        $localCityPackage =  $this->db->get('local_city_package');
        $airports =  $this->db->get('airports');
        $data = [
            "localCity" => $localCity,
            "localCityPackage" => $localCityPackage,
            "airports" => $airports,
        ];
        return $data;
    }

    function update_scp($sid, $value)
    {
        $this->db->where("id", $sid)->update("search_request", ["scp" => $value, "updated_at" => date("Y-m-d H:i:s")]);
    }

    function get_scp($sid)
    {
        $searchRequest =  $this->db->where("id", $sid)->get("search_request");
        return $searchRequest[0]['scp'];
    }

    function get_airport_id($airport)
    {
        $airportId = $this->db->where('id', $airport)->getOne('airports', "airport_id");
        return $airportId;
    }


    function get_local_city_details($cityId){
        $city = $this->db->where('place_id' , $cityId)->get('local_cities');
        return $city[0];
    }
    function get_local_airport_details($airport_d){
        $airport = $this->db->where('airport_id' , $airport_d)->get('airports');
        return $airport[0];
    }
}
