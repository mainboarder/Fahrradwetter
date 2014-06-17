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

require_once('wetter.php');

$daten = new wetter(WETTERSTATION, STADT);

$wetter = $daten->getWeather();

echo '<div class="container">
    <h2 class="text-center trigger"><a href="#">Fahrradwetter in '. STADT .
        '?</a> - '.$wetter[0]['rad'].'.</h2>
    <div class="toggle_container">
        <table class="table table-striped">';
echo $daten->makeTable($wetter, 'tag');
echo $daten->makeTable($wetter, 'icon', '<img src="', '" alt="" />');
echo $daten->makeTable($wetter, 'regen', NULL, '%', 'Regenwahrscheinlichkeit');
echo $daten->makeTable($wetter, 'tempMax', NULL, '°C', 'Höchsttemperatur');
echo $daten->makeTable($wetter, 'wind', NULL, 'km/h', 'Wind');
echo $daten->makeTable($wetter, 'rad', NULL, NULL, 'Fahrradwetter');

?>

        </table>

        <p><br /></p>
        <div class="text-center bg-info"><p><small>Daten via <a href="http://www.wunderground.com/?apiref=5493fcc3357cb244">Wunderground</a>, alle 10 Minuten neu abgerufen.</small></p>
<p>Fahrradwetter hat eine Regenwahrscheinlichkeit unter 40%, Temperaturen zwischen 15 und 24°C und Wind durchschnittlich langsamer als 35km/h.</p>
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