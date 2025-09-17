<?php
class cabbazar
{
    private $apikey, $base_url;

    function __construct()
    {
        $this->base_url = "https://api.stage.cabbazar.com/partner/api";
        $this->apikey = "892aafbf-e283-4df0-a738-7a1f3daf798f";
    }

    function CALL_API($endpoint, $method, $jsonRequest)
    {
        $url = $this->base_url . $endpoint;
        if ($method == "GET") {
            $url = $url . "?key=" . $jsonRequest['key'] . "&airportId=" . $jsonRequest['airportId'] . "&fareType=" . $jsonRequest['fareType'];
        }
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_HTTPHEADER => ['Content-Type: application/json']
        ]);
        if ($method == "POST") {
            curl_setopt($curl, CURLOPT_POSTFIELDS, $jsonRequest);
        }
        $response = json_decode(curl_exec($curl), 1);
        return $response;
    }

    function make_search_request($searchRequest)
    {
        if ($searchRequest['details'][0]['trip_type'] === "l") {
            $searchRequest['details'][0]['key'] = $this->apikey;
            unset($searchRequest['details'][0]['trip_type']);
            return json_encode($searchRequest['details'][0]);
        } else if ($searchRequest['details'][0]['trip_type'] === "a") {
            $searchRequest['details'][0]['key'] = $this->apikey;
            unset($searchRequest['details'][0]['trip_type']);
            return json_encode($searchRequest['details'][0]);
        } else {
            $arrivalAt = "";
            $itinerary = [];
            $jsonRequest = [];
            $searchDetails = $searchRequest['details'][0];
            $isReturn = ($searchDetails['is_return'] == 1) ? true : false;
            $departureAt = date('d/m/Y H:i', strtotime($searchDetails['departure_date']));
            $searchcities = $searchRequest['city'];

            foreach ($searchcities as $city) {
                $itinerary[] = [
                    "address" => $city['address'],
                    "placeId" => $city['id']
                ];
            }

            if (count($itinerary) > 2 || $isReturn == 1) {
                $arrivalAt = date('d/m/Y', strtotime($searchDetails['arrival_date']));
            } else {
                $arrivalAt = "";
            }

            $jsonRequest[] =
                [
                    "key" => $this->apikey,
                    "isReturn" => $isReturn,
                    "departureAt" => $departureAt,
                    "arrivalAt" => $arrivalAt,
                    "itinerary" => $itinerary,
                ];

            $jsonRequest = json_encode($jsonRequest[0]);
            return $jsonRequest;
        }
    }

    function search($sid, $endpoint)
    {
        $endpoint = $endpoint;
        $method = "POST";
        require_once 'helper/db.php';
        $db = new db();
        $searchRequest = $db->get_search_request($sid);
        $jsonRequest = $this->make_search_request($searchRequest);
        $response = $this->CALL_API($endpoint, $method, $jsonRequest);
        return $response;
    }

    function get_local_airport_cities($requestCities)
    {
        $endpoint = "/airport/destination";
        $method = "GET";
        $requestCities['key'] = $this->apikey;
        $response = $this->CALL_API($endpoint, $method, $requestCities);
        return $response;
    }
}


// function PROCESS_TJK_HOTELS($results, $sid, $nights)
// {
//     $common_array = array();
//     foreach ($results['searchResult']['his'] as $hotel) {
//         if (strpos(strtolower($hotel['name']), "oyo") !== false || strpos(strtolower($hotel['name']), "fab") !== false) continue;
//         $v_total = $hotel['pops'][0]['tpc'];
//         $img = (isset($hotel['img'][0]['url'])) ? $hotel['img'][0]['url'] : "";
//         $common_array[] = array(
//             "sid" => $sid,
//             "vendor" => "TJK",
//             "type" => strtolower($hotel['pt']),
//             "name" => $hotel['name'],
//             "rating" => $hotel['rt'],
//             "hotel_id" => $hotel['id'],
//             "lat" => $hotel['gl']['lt'],
//             "long" => $hotel['gl']['ln'],
//             "total" => $v_total,
//             "markUp" => 0,
//             "showPrice" => 0,
//             "meal" => strtolower($hotel['pops'][0]['fc'][0]),
//             "pcode" => ($hotel['ad']['postalCode'])??"",
//             "city" => ($hotel['ad']['city']['name'])??"",
//             "state" => ($hotel['ad']['state']['name'])??"",
//             "country" => ($hotel['ad']['country']['name'])??"",
//             "ad1" => (isset($hotel['ad']['adr'])) ? $hotel['ad']['adr'] : "",
//             "ad2" => (isset($hotel['ad']['adr2'])) ? $hotel['ad']['adr2'] : "",
//             // "img" => "",
//             "img" => $img,
//         );
//     }
//     return $common_array;
// }