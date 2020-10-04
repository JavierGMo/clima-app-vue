<?php
    /* 
    
        {
            today : {
                img_abbr : x,
                the_temp : x,
                min_temp : x,
                max_temp : x,
                applicable_date : x,
                place : x,
                wind_speed : x,
                humidity : x,
                visibility : x,
                air_pressure : x
            }
            next_days : [
                {
                    img_abbr : x,
                    min_temp : x,
                    max_temp : x
                },
                .,
                .,
                .,
            ]
        }
    
    
    */
    date_default_timezone_set('UTC');
    date_default_timezone_set("America/Mexico_City");
    $fecha = new DateTime('now');

    // $d->add(new DateInterval('P5D'));

    // echo $d->format('D, d M');
    $location = $_GET['location'];


    $data = file_get_contents('https://www.metaweather.com/api/location/search/?query='.$location);
    $dataJSON = json_decode($data, true);
    $len = count($dataJSON);
    if($len === 0){
        // echo json_encode([
        //     'status' => 404,
        //     'data' => 'err'
        // ]);
        header('HTTP/1.0 404 NOT FOUND');
        return;
    }
    $woeid = $dataJSON[0]['woeid'];
    // echo $woeid;
    $weatherData = file_get_contents('https://www.metaweather.com/api/location/'.$woeid);

    // var_dump(json_decode($weatherData, true));
    // $weatherJSON = $weatherData['consolidated_weather'];
    $weatherJSON = json_decode($weatherData, true);
    // var_dump($weatherJSON['consolidated_weather']);
    // echo count($weatherJSON['consolidated_weather']);
    $today['today'] = [
        'img_abbr' => $weatherJSON['consolidated_weather'][0]['weather_state_abbr'],
        'the_temp' => round($weatherJSON['consolidated_weather'][0]['the_temp']),
        'min_temp' => round($weatherJSON['consolidated_weather'][0]['min_temp']),
        'max_temp' => round($weatherJSON['consolidated_weather'][0]['max_temp']),
        'date' => $fecha->format('D, d M'),
        // 'applicable_date' => (new DateTime($weatherJSON['consolidated_weather'][0]['applicable_date']))->format('D, d M'),
        'place' => $weatherJSON['title'],
        'weather_state_name' => $weatherJSON['consolidated_weather'][0]['weather_state_name'],
        'wind_speed' => round($weatherJSON['consolidated_weather'][0]['wind_speed']),
        'wind_direction_compass' => $weatherJSON['consolidated_weather'][0]['wind_direction_compass'],
        'humidity' => round($weatherJSON['consolidated_weather'][0]['humidity']),
        'visibility' => round($weatherJSON['consolidated_weather'][0]['visibility']),
        'air_pressure' => round($weatherJSON['consolidated_weather'][0]['air_pressure'])

    ];
    $dataResp = $today;
    $dataResp['next_days'] = [];
    for ($i=1; $i < 6; $i++) {
        $fecha->add(new DateInterval("P1D"));
        array_push($dataResp['next_days'], [
            'date'  => $fecha->format('D, d M'),
            // 'applicable_date' => (new DateTime($weatherJSON['consolidated_weather'][0]['applicable_date']))->format('D, d M'),
            'img_abbr' => $weatherJSON['consolidated_weather'][$i]['weather_state_abbr'],
            'the_temp' => round($weatherJSON['consolidated_weather'][$i]['the_temp']),
            'min_temp' => round($weatherJSON['consolidated_weather'][$i]['min_temp']),
            'max_temp' => round($weatherJSON['consolidated_weather'][$i]['max_temp'])
        ]);
    }
    echo json_encode($dataResp);


?>