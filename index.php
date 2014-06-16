<!DOCTYPE html>
<html lang="de-DE">
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Fahrradwetter</title>
<link rel="stylesheet" type="text/css" media="all"
    href="//cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.1.1/css/bootstrap.min.css" />
<script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
<meta name="viewport" content="initial-scale=1,minimum-scale=1,width=device-width" />
<style>
    .toggle_container{display:none;}
    h2{margin-top:15%;}
</style>
</head>
<body>
<?php
/*
 * Konfiguration
 */
/**
 * Link zur Wetterstation auf Wunderground
 */
define('WETTERSTATION', 'http://www.wunderground.com/cgi-bin/findweather/hdfForecast?query=51.330%2C12.363&sp=ISACHSEN121&apiref=5493fcc3357cb244');
/**
 * Name der Stadt
 */
define('STADT', 'Leipzig');

/**
 * Erhalte die Wetterdaten und baue ein Array
 *
 * @param string Stadtname
 * @return array Wetterdaten
 */
function getWeather(){
	// JSON holen
	$json_string = file_get_contents('api.json');
	$parsed_json = json_decode($json_string, true);
  
	// Daten aus JSON für die nächsten vier Tage holen
	for($i = 0; $i <= 9; $i++){
		$rain[$i] = $parsed_json['forecast']['simpleforecast']
				['forecastday'][$i]['pop'];
		$icon[$i] = $parsed_json['forecast']['simpleforecast']
				['forecastday'][$i]['icon_url'];
		$day[$i] = $parsed_json['forecast']['simpleforecast']
				['forecastday'][$i]['date']['weekday'];
		$tempHi[$i] = $parsed_json['forecast']['simpleforecast']
				['forecastday'][$i]['high']['celsius'];
		
		// Fahrradwetter? - Grundsätzlich ja.
		$fahrrad = '<b>Ja</b>';
		
		// Vielleicht, wenn Regenwahrscheinlichkeit größer als 40% oder
		// Temperaturen nicht zwischen 15 und 24°C
		if($rain[$i] >= 40 || $tempHi[$i] <= 15 || $tempHi[$i] > 24){
			$fahrrad = '<a href="'. WETTERSTATION .'">Vielleicht</a>';
		}
		// Kein Fahrradwetter, wenn Regenwahrscheinlichkeit über 55%
		// oder Temperaturen nicht zwischen 10 und 27°C
		if($tempHi[$i] >= 27 || $tempHi[$i] <= 10 || $rain[$i] >= 55){
			$fahrrad = 'Nein';
		}
		
		// Array mit den Daten für einen Tag zusammenbauen
		$wetter[$i] =
						array(
							'rain' => $rain[$i],
							'icon' => $icon[$i],
							'day' => $day[$i],
							'tempHi' => $tempHi[$i],
							'rad' => $fahrrad,
							);
		
		// Arrays miteinander verknüpfen
		if(isset($wetter[$i-1])){
			array_merge($wetter[$i-1], $wetter[$i]);
		}
	}

	return $wetter;
}

/**
 * Erstelle eine Tabellenzeile aus einem Array
 *
 * @param array array Auszuwertendes Array mit den Daten
 * @param schluessel string Schlüssel nach dem im Array gesucht wird
 * @param stringVor string Zeichen die vor dem String auftauchen sollen
 * @param stringNach string Zeichen die nach dem String auftauchen sollen
 * @param first string Was soll einmalig am Anfang stehen
 *
 * @return string
 */
function makeTable($array, $schluessel, $stringVor = NULL,
		$stringNach = NULL, $first = NULL){
		
	$string = '<td>'.$first.'</td>';
	
	foreach($array as $daten){
		$string .= '<td>';
		$string .= $stringVor . $daten[$schluessel] . $stringNach;
		$string .= '</td>';
	}
	
	return '
            <tr>' . $string . '</tr>
';
}

$wetter = getWeather();

echo '<div class="container">
    <h2 class="text-center trigger"><a href="#">Fahrradwetter in '. STADT .'?</a> - '.$wetter[0]['rad'].'.</h2>
    <div class="toggle_container">
        <table class="table table-striped">';
echo makeTable($wetter, 'day');
echo makeTable($wetter, 'icon', '<img src="', '" alt="" />');
echo makeTable($wetter, 'rain', NULL, '%', 'Regenwahrscheinlichkeit');
echo makeTable($wetter, 'tempHi', NULL, '°C', 'Höchsttemperatur');
echo makeTable($wetter, 'rad', NULL, NULL, 'Fahrradwetter');
echo '
        </table>';
?>

        <p><br /></p>
        <div class="text-center bg-info"><p><small>Daten via <a href="http://www.wunderground.com/?apiref=5493fcc3357cb244">Wunderground</a>, alle 10 Minuten neu abgerufen.</small></p>
<p>Fahrradwetter hat eine Regenwahrscheinlichkeit unter 40% und Temperaturen zwischen 15 und 24°C.</p>
<h6>Immer trocken unterwegs mit <a href="http://mainboarder.de">Mainboarder</a> | Code auf <a href="https://github.com/mainboarder/Fahrradwetter">Github</a></h6>
        </div>
    </div>
    <script type="text/javascript">

                    $(document).ready( function() {
      $('.trigger').not('.trigger_active').next('.toggle_container').hide();
      $('.trigger').click( function() {

      var trig = $(this);

      if ( trig.hasClass('trigger_active') ) {
        trig.next('.toggle_container').slideToggle('slow');
        trig.removeClass('trigger_active');
        } else {
          $('.trigger_active').next('.toggle_container').slideToggle('slow');
          $('.trigger_active').removeClass('trigger_active');
            trig.next('.toggle_container').slideToggle('slow');
            trig.addClass('trigger_active');
        };
        return false;
      });
    });
    </script>
</body>
</html>