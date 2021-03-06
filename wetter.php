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
    public function getWeather(){
        // JSON holen
        $json_string = file_get_contents('api.json');
        $json_gelesen = json_decode($json_string, true);

        // Daten aus JSON für die nächsten vier Tage holen
        for($i = 0; $i <= 9; $i++){
            $regen[$i] = $json_gelesen['forecast']['simpleforecast']
                    ['forecastday'][$i]['pop'];
            $icon[$i] = $json_gelesen['forecast']['simpleforecast']
                    ['forecastday'][$i]['icon_url'];
            $tag[$i] = $json_gelesen['forecast']['simpleforecast']
                    ['forecastday'][$i]['date']['weekday'];
            $tempMax[$i] = $json_gelesen['forecast']['simpleforecast']
                    ['forecastday'][$i]['high']['celsius'];
            $wind[$i] = $json_gelesen['forecast']['simpleforecast']
                    ['forecastday'][$i]['avewind']['kph'];

            $fahrrad = $this->getEmpfehlung($i, $regen[$i], $tempMax[$i],
                    $wind[$i]);

            // Array mit den Daten für einen Tag zusammenbauen
            $wetter[$i] =
                array(
                    'regen' => $regen[$i],
                    'icon' => $icon[$i],
                    'tag' => $tag[$i],
                    'tempMax' => $tempMax[$i],
                    'rad' => $fahrrad,
                    'wind' => $wind[$i]
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
    public function makeTable($array, $schluessel, $stringVor = NULL,
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
   
   /**
    * Filtere POST auf Integer
    * @param string $name Variablenname
    * @return int
    */
   public function filter_post_int($name){
       return filter_input(INPUT_POST, $name, FILTER_SANITIZE_NUMBER_INT);
   }
   
   /**
    * Filtere POST auf Integer
    * @param string $name Variablenname
    * @return int
    */
   public function filter_cookie_int($name){
       return filter_input(INPUT_COOKIE, $name, FILTER_SANITIZE_NUMBER_INT);
   }
   
   /**
    * Gib eine Empfehlung anhander der Nutzereinstellungenab, ob Rad gefahren
    * werden kann.
    * @param int $i Laufvariable
    * @param int $regen aktueller Vorhersagewert für den Regen
    * @param int $temp aktueller Vorhersagewert für die Temperatur
    * @param int $wind aktueller Vorhersagewert für die Windgeschwindigkeit
    * @return string
    */
   protected function getEmpfehlung($i, $regen, $temp, $wind){
        
        // Werte einrichten
        // Regen
        if(isset($_COOKIE['regen'])){
            $regenWert = $this->filter_cookie_int('regen');
        }else{
            $regenWert = 55;
        }
        
        // min. Temperatur
        if(isset($_COOKIE['templow'])){
            $tempLowWert = $this->filter_cookie_int('templow');
        }else{
            $tempLowWert = 10;
        }
        
        // max. Temperatur
        if(isset($_COOKIE['temphigh'])){
            $tempMaxWert = $this->filter_cookie_int('temphigh');
        }else{
            $tempMaxWert = 27;
        }
        
        // Wind
        if(isset($_COOKIE['wind'])){
            $windWert = $this->filter_cookie_int('wind');
        }else{
            $windWert = 35;
        }

        // Fahrradwetter? - Grundsätzlich ja.
        $fahrrad = '<b>Ja</b>';

        // Vielleicht, wenn Regenwahrscheinlichkeit größer als 40% oder
        // Temperaturen nicht zwischen 15 und 24°C
        // oder Wind 35 km/h oder schneller
        if(empty($_COOKIE['wind']) && $regen >= 40 ||
				empty($_COOKIE['wind']) && $temp <= 15 ||
				empty($_COOKIE['wind']) && $temp > 24 ||
				$wind >= $windWert){
                
           $fahrrad = '<a href="'. WETTERSTATION .'">Vielleicht</a>';
        }
        // Kein Fahrradwetter, wenn Regenwahrscheinlichkeit über 55%
        // oder Temperaturen nicht zwischen 10 und 27°C
        // oder Wind 40 km/h oder schneller
        if($temp >= $tempMaxWert || $temp <= $tempLowWert || $regen >= $regenWert
                || $wind >= $windWert){
                
           $fahrrad = 'Nein';
        }
       
       return $fahrrad;
    }
}
