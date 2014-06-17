<?php

/**
 * Wetter
 */
class wetter {
    
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
}
