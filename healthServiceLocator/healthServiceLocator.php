<?php 


	/*SIMPLE HEALTH SERVICE LOCATOR USING THE GOOGLE MAPS PLACES AND DISTANCE MATRIX APIS*/
	define('PLACES_API_KEY', '');
	define('DISTANCE_MATRIX_KEY', '');
	
	$typesOfPlaces = ["doctor", "hospital", "pharmacy"];
    $coordinates = "-33.8670,151.1957"; //YOUR COORDINATES
    $radius = 5000;

	$foundPlaces = findPlaces($typesOfPlaces, $coordinates, $radius);
	//foundPlaces is now an array containing associative arrays(formerly JSON objects) with place details
	//check it out: echo "<pre>".count($foundPlaces)."</pre>";
	//foundPlaces[i] contains all found locations of each type
	//echo "<pre>".count($foundPlaces[0])."</pre>";
	//foundPlaces[i][j] contains associative array with details of a specific place
	//echo "<pre>".count($placesList[0][0])."</pre>";

	$place_ids = collectPlaceIDs($foundPlaces);
	$distances = getDistances($place_ids); //distances expressed in meters

    function findPlaces($typesOfPlaces, $coordinates, $radius)
    {
    	$places = [];
		foreach ($typesOfPlaces as $type)
		{
			$response = file_get_contents("https://maps.googleapis.com/maps/api/place/nearbysearch/json?location=".$coordinates."&radius=".$radius."&types=".$type."&key=".PLACES_API_KEY);
	    	$jsonObj = json_decode($response, true); //decode response object
	    	array_push($places, $jsonObj['results']); //push an associative array of all the locations found
		}
	}


	function collectPlaceIDs($placesList)
	{
		$results = [];
		foreach ($placesList as $place)
		{
			$id_array = [];
			foreach ($place as $placeDetails)
			{ 
				//echo $placeDetails['types'][0];
				array_push($id_array, $placeDetails['place_id']);
			}
			array_push($place_ids, $id_array);
		}
	}

	function buildIDParameter($IDs)
	{
		$list = [];
		$string = "";
		for($i = 0; $i < count($IDs); $i++)
		{
			for($j = 0; $j < 25; $j++)
			{
				if($IDs[$i][$j] != null)
				{
					$string += "place_id:";
					$string += $IDs[$i][$j];
				}
				if($IDs[$i][$j+1] != null)
				{
					$string += "|";
				}
				else
				{
					break;
				}
			}
			array_push($list, $string);
		}
		return $list;
	}

		function getDistances($place_ids)
		{
			$id_list = buildIDParameter($place_ids);
			$distanceArray = [];
			foreach ($id_list as $id)
			{
				$response = file_get_contents('https://maps.googleapis.com/maps/api/distancematrix/json?units=imperial&origins='.$coordinates.'&destinations=place_id:'.$id.'&mode=walking&key='.DISTANCE_MATRIX_KEY);
				$responsejson = json_decode($response, true); //decode response object
				//echo "<pre>".var_dump($responsejson)."</pre>";
				for ($i=0; $i < count($responsejson['rows'][0]['elements']); $i++)
				{ 
					array_push($distanceArray, $responsejson['rows'][0]['elements'][$i]['distance']['value']);
				}
				array_push($distances, $distanceArray);
			}
		}
 ?>