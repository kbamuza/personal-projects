<?php 

/*SIMPLE HEALTH SERVICE LOCATOR USING THE GOOGLE MAPS PLACES AND DISTANCE MATRIX APIS*/
/*distances in meters*/
/*time (driving) in minutes*/

	define('PLACES_API_KEY', '');
	define('DISTANCE_MATRIX_KEY', '');
	
	$typesOfPlaces = ["doctor", "hospital", "pharmacy"];
    $coordinates = "-33.8670,151.1957"; //YOUR COORDINATES
    $radius = 5000;

	$foundPlaces = findPlaces($typesOfPlaces, $coordinates, $radius);
	//echo var_dump($foundPlaces);

	$place_ids = collectPlaceIDs($foundPlaces);
	//echo var_dump($place_ids);

	$id_list = buildIDParameter($place_ids);
	//echo var_dump($id_list);

	$distanceMatrixResponse = makeRequests($id_list);
	//echo var_dump($distanceMatrixResponse);

    $destAddresses = getDestAddresses($distanceMatrixResponse);
    //echo var_dump($destAddresses);

	$distances = getDistances($distanceMatrixResponse);
	//echo var_dump($distances);

	$durations = getDurations($distanceMatrixResponse);
	//echo var_dump($durations);
	
    function findPlaces($typesOfPlaces, $coordinates, $radius)
    {
    	$places = [];
		foreach ($typesOfPlaces as $type)
		{
			$response = file_get_contents("https://maps.googleapis.com/maps/api/place/nearbysearch/json?location=".$coordinates."&radius=".$radius."&types=".$type."&key=".PLACES_API_KEY);
	    	$jsonObj = json_decode($response, true);
	    	array_push($places, $jsonObj['results']);
		}
		return $places;
	}


	function collectPlaceIDs($placesList)
	{
		$results = [];
		foreach ($placesList as $place)
		{
			$id_array = [];
			foreach ($place as $placeDetails)
			{ 
				array_push($id_array, $placeDetails['place_id']);
			}
			array_push($results, $id_array);
		}
		return $results;
	}


	/*concatenate place ids to reduce number of requests made*/
	function buildIDParameter($IDs)
	{
		$list = [];
		$string = "";
		for($i = 0; $i < count($IDs); $i++)
		{
			for($j = 0; $j < 25; $j++)
			{
				if(isset($IDs[$i][$j]))
				{
					$string .= "place_id:";
					$string .= $IDs[$i][$j];
				}
				if(isset($IDs[$i][$j+1]))
				{
					$string .= "|";
				}
				else
				{
					break;
				}
			}
			array_push($list, $string);
			$string = "";
		}
		return $list;
	}

	function makeRequests($id_list)
	{
		$responses = [];
		foreach ($id_list as $id)
		{
			$results = file_get_contents('https://maps.googleapis.com/maps/api/distancematrix/json?units=metric&origins='.$coordinates.'&destinations=place_id:'.$id.'&key='.DISTANCE_MATRIX_KEY);
			$resultsjson = json_decode($results, true);
			array_push($responses, $resultsjson);
		}
		return $responses;
	}


    function getDestAddresses($response)
    {
    	$results = [];
    	foreach ($response as $entry)
    	{
    		array_push($results, $entry['destination_addresses']);
    	}
    	return $results;
    }


	function getDistances($distanceMatrixResponse)
	{
		$results = [];
		foreach ($distanceMatrixResponse as $entry)
		{
			$values = [];
			foreach($entry['rows'][0]['elements']) as $element)
			{ 
				array_push($values, $element['distance']);
			}
			array_push($results, $values);
		}
		return $results;
	}



    function getDurations($distanceMatrixResponse)
	{
		$results = [];
		foreach ($distanceMatrixResponse as $entry)
		{
			$values = [];
			foreach($entry['rows'][0]['elements']) as $element)
			{ 
				array_push($values, intval(($element['duration']['value'])/60));
			}
			array_push($results, $values);
		}
		return $results;
	}

?>
