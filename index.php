<!DOCTYPE html>
<html lang="en">
  <head>
      <!-- <link rel="stylesheet" href="style.css"> -->
      <script type="text/javascript" src="/custom.js"></script>
      <link rel="icon" href="578113.png" type="image/x-icon">
      <meta charset="UTF-8">
      <meta http-equiv="X-UA-Compatible" content="IE=edge">
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <title>Temperaturkontrolle</title>

      <link href="//maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">

      <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  </head>

  <body>
<!--dbconnect + abfrage für tabelle -->
    <?php
    $hostname = "localhost";
    $username = "root";
    $passwort = "";
    $db = "tempcontroll";

    $dbconnect = mysqli_connect($hostname, $username, $passwort, $db);

    $query = mysqli_query($dbconnect, 
    "SELECT s.SensorID, s.MaxTemp, h.Name, 
    (SELECT t.temperatur FROM temperaturen t WHERE t.SensorID = s.SensorID ORDER BY t.Zeit DESC LIMIT 1) AS Temp
    FROM sensor s 
    JOIN hersteller h ON s.herstellerID = h.herstellerID");
    ?>
<!--navigationsbar-->
      <nav>
        <div class="row">
          <div class="col-sm-4">
            <img class="logo" src="image.png" alt="Logo">
          </div>
          <div class="col-sm-6"></div>
          <div class="col-sm-2">
            <a href="login.php">
              <button class="btn sign-in btn-primary" type="submit">Anmelden</button>
            </a>  
          </div>
        </div>        
      </nav>
      <main>
<!--Tabelle-->
        <div class="warper content">
          <table class="table">
            <thead>
              <tr>
                <th scope="col">#</th>
                <th scope="col">Sensor</th>
                <th scope="col">Temperatur</th>
                <th scope="col">Zustand</th>
                <th scope="col"></th>
              </tr>
            </thead>
            <tbody>
              <?php
                $count = 0;
                while ($row = mysqli_fetch_array($query)) {
                  $count++;
                  echo
                  "<tr>
                  <td>$count</td>
                  <td>{$row["SensorID"]}</td>
                  <td>{$row["Temp"]} C°</td>
                    <td>";
                      if(intval($row["Temp"]) > intval($row["MaxTemp"])){
                        echo '<img src="rot.png" alt="roter Kreis" width="40" height="40">';
                      }
                      elseif(intval($row["Temp"]) > intval($row["MaxTemp"]) - 5 && intval($row["Temp"]) < intval($row["MaxTemp"])){
                        echo '<img src="gelb.png" alt="gelber Kreis" width="40" height="40">';
                      }
                      elseif(intval($row["Temp"]) < intval($row["MaxTemp"]) - 5){
                        echo '<img src="grün.png" alt="grün Kreis" width="40" height="40">';
                      }
                      else{
                        echo "error";
                      }
                    echo "</td>
                    <td><button onclick='showDiagramm".$count."()'>Details</button></td>
                  </tr>\n";
                }
              ?>
            </tbody>
          </table>
        <br>
        <br>
         <!--Diagramm-->
        <?php
      $resSensorIDs = mysqli_query($dbconnect, "SELECT SensorID FROM sensor");
      while($rowanzahl = mysqli_fetch_array($resSensorIDs)){

        $resTempData = mysqli_query($dbconnect,
          "SELECT t.temperatur FROM temperaturen t 
          JOIN sensor s ON s.SensorID = t.SensorID 
          WHERE s.SensorID = ".$rowanzahl['SensorID']."  ORDER BY t.Zeit DESC LIMIT 10"
        );
        $resSensorData = mysqli_query($dbconnect,
        "SELECT s.SensorID, h.name, s.MaxTemp,
        (SELECT t.temperatur FROM temperaturen t WHERE t.SensorID = s.SensorID ORDER BY t.temperatur DESC LIMIT 1) AS JemalsMAX,
        (SELECT AVG(t.temperatur) FROM temperaturen t WHERE t.SensorID = s.SensorID) AS AVGTemp 
        FROM sensor s 
        JOIN hersteller h ON h.HerstellerID = s.HerstellerID;");
        
        $i = 0;
        $sensorID=$rowanzahl["SensorID"];
        while($rowTemp = mysqli_fetch_array($resTempData)){
          $tempPack[$sensorID-1][$i] = $rowTemp[0];
          $i++;
        }

        while($rowSensorData = mysqli_fetch_array($resSensorData)){

          echo '
          <div style="display:none" id="myDiv'.$sensorID.'" class="infocontainer">
            <div class="row">
              <div class="col-sm-4">
                <table class="sensinfo">
                  <tr>
                    <th>SensorID:</th>
                    <td>'.$rowanzahl["SensorID"].'</td>
                  </tr>
                  <tr>  
                    <th>maxTemp:</th>
                    <td>'.$rowSensorData["MaxTemp"].' °C</td>
                  </tr>
                  <tr>
                    <th>Höchste Temperatur jemals:</th>
                    <td>'.$rowSensorData["JemalsMAX"].' °C</td>
                  </tr>
                  <tr>
                    <th>Durchschnitts Temperatur:</th>
                    <td>'.round($rowSensorData["AVGTemp"],2).' °C</td>
                  </tr>
                  <tr>
                    <th>Hersteller:</th>
                    <td>'.$rowSensorData["name"].'</td>
                  </tr>
                </table>
              </div>
              <div class="col-sm-8 container chart text-center my-4">
                <canvas id="lineChart'.$sensorID.'"></canvas>
              </div>
            </div>
          </div>';
        }
      };
        ?>
          <!--SCRIPT FÜR DIAGRAMM-->
          <script>
            <?php
              
              for($i = 0 ; $i < count($tempPack); $i++){
                echo "

                var ctxL".($i+1)." = document.getElementById(\"lineChart".($i+1)."\").getContext('2d');
                
                var myLineChart".($i+1)." = new Chart(ctxL".($i+1).", {
                  type: 'line',
                  data: {
                    labels: [\"10\",\"9\",\"8\",\"7\",\"6\",\"5\",\"4\",\"3\",\"2\",\"1\"],
                    datasets: [{
                      label: \"letzten 10 temperaturen\",
                      data: [
                        ";
                        for ($j = count($tempPack[$i]) ; $j > 0 ; $j--){
                          echo $tempPack[$i][($j-1)];
                          if(($j-1) != 0 ){
                            echo ", ";
                          }
                        };
                        echo "
                      ],
                      backgroundColor: [
                        'rgba(105, 0, 132, .2)',
                      ],
                      borderColor: [
                        'rgba(200, 99, 132, .7)',
                      ],
                      fill: true,
                      borderWidth: 2,
                      stepped: false
                    }
                    ]
                  
                  },
                  options: {
                    responsive: true,
                    scales: {
                      yAxes: [{
                        type: 'linear', 
                        display: true,
                        position: 'left',
                        id: 'y-axis-1',
                      }, {
                        type: 'linear', 
                        display: true,
                        position: 'right',
                        id: 'y-axis-2',
                        gridLines: {
                          drawOnChartArea: false, 
                        },
                      }],
                    }
                  }
                });

                function showDiagramm".($i+1)."() {
                    var x = document.getElementById('myDiv".($i+1)."');
                    if (x.style.display == 'none') {
                      x.style.display = 'block';
                    } else {
                      x. style.display = 'none';
                    }
                  
                }
                ";
              }
            ?>
          </script>

          <style>
            table.scroll thead tr:after {
	            content: '';
	            overflow-y: scroll;
	            visibility: hidden;
            }
            table.scroll thead th {
	            flex: 1 auto;
	            display: block;
            }
            table.scroll tbody {
	            display: block;
	            width: 100%;
	            overflow-y: auto;
	            height: auto;
	            max-height: 200px;
            }
            table.scroll thead tr,
            table.scroll tbody tr {
	            display: flex;
            }
            table.scroll tbody tr td {
            	flex: 1 auto;
	            word-wrap: break;
            }

            canvas {
              max-width: 100%;
              
            }
            .content{
             margin: 110px; 
            }
            .sign-in{
                margin-top: 35%;
            }
            .logo{
                display: flex;
            }
            .infocontainer{
              padding-right: 15px;
              background-color:lightgrey;
              border-width: 2px;
              border-color: grey;
              border-style: solid;
              border-radius: 5px;
            }
            .chart{
                display: flex;
                align-items: center;
                justify-content: right;
            
            }
            .sensinfo{
              position: absolute;
              top:50%;
              left:50%;
              transform: translate(-50%,-50%);
              width: 80%;
              font-size: 100%;
            }
            nav{
                background-color: rgb(214, 214, 214);
            }
            .circle{
                margin-right: 120px;
            }

          </style>

      </main>
  </body>
</html>