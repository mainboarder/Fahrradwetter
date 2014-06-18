<?php
require_once('config.php');
require_once('wetter.php');

$daten = new wetter();

// Setze die Einstellungen als Cookie
if($_SERVER['REQUEST_METHOD'] === 'POST'){
    setcookie('regen', $daten->filter_post_int('regenwahrscheinlichkeit'),
            time()*9);
    setcookie('templow', $daten->filter_post_int('temperaturunten'), time()*9);
    setcookie('temphigh', $daten->filter_post_int('temperaturoben'), time()*9);
    setcookie('wind', $daten->filter_post_int('wind'), time()*9);
}
?>
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
    <div class="container">
    <h2 class="text-center trigger"><a href="#">Fahrradwetter in 
<?php
$wetter = $daten->getWeather();

echo STADT .
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
<p>Fahrradwetter hat eine Regenwahrscheinlichkeit unter 40%, Temperaturen zwischen 15 und 24°C und Wind durchschnittlich langsamer als 35km/h, soweit nicht anders eingestellt.</p>
<h6>Immer trocken unterwegs mit <a href="http://mainboarder.de">Mainboarder</a> | Code auf <a href="https://github.com/mainboarder/Fahrradwetter">Github</a></h6>
        </div>
    </div>
    <h4 class="trigger"><a href="#">Optionen</a></h4>
    <div class="toggle_container row">
        <h3>Schwellwerte</h3>
        <form action="index.php" method="post">
            <div class="col-md-3">
            <div class="form-group">
                <label for="regenwahrscheinlichkeit">Regenwahrscheinlichkeit</label>
                <select name="regenwahrscheinlichkeit" size="1" class="form-control">
                    <option value="10">10%</option>
                    <option value="20">20%</option>
                    <option value="30">30%</option>
                    <option value="40" selected>40%</option>
                    <option value="50">50%</option>
                    <option value="60">60%</option>
                    <option value="70">70%</option>
                    <option value="80">80%</option>
                    <option value="90">90%</option>
                    <option value="100">100%</option>
                </select>
            </div>
            <button type="submit" class="button">Speichern</button>
            </div>
            <div class="col-md-3">
                <label for="temperaturunten">Untere Temperatur</label>
                <select name="temperaturunten" size="1" class="form-control">
                    <option value="0">0°C</option>
                    <option value="5">5°C</option>
                    <option value="10">10°C</option>
                    <option value="15" selected>15°C</option>
                </select>
            </div>
            <div class="col-md-3">
                <label for="temperaturoben">Obere Temperatur</label>
                <select name="temperaturoben" size="1" class="form-control">
                    <option value="20">20°C</option>
                    <option value="25" selected>25°C</option>
                    <option value="30">30°C</option>
                    <option value="35">35°C</option>
                </select>
            </div>
            <div class="col-md-3">
                <label for="wind">Max. Wind</label>
                <select name="wind" size="1" class="form-control">
                    <option value="10">10km/h</option>
                    <option value="15">15km/h</option>
                    <option value="20">20km/h</option>
                    <option value="25">25km/h</option>
                    <option value="30" selected>30km/h</option>
                    <option value="35">35km/h</option>
                </select>
            </div>
        </form>
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