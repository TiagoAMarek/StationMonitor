<?php
    include 'src/php/files.php';
    include 'src/php/monitor.php';

    try{
        if($_FILES){
            $file = new Files("./files/", $_FILES);
            $monitor = new Monitor($file->getFileName(), "files/");
        }
    } catch(Exception $e) {
        echo 'Exception: ', $e->getMessage(), "\n";
    }
?>
<!DOCTYPE html>
<html>
<head>
    <title>Station Monitor</title>
    <link rel="stylesheet" href="bower_components/bootstrap/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="src/css/default.css">
</head>
<body role="document">
    <nav class="navbar navbar-inverse navbar-fixed-top">
          <div class="container">
            <div class="navbar-header">
              <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
              </button>
              <a class="navbar-brand" href="#">Station Monitor</a>
            </div>
          </div>
    </nav>

    <div class="container theme-showcase" role="main">
        <div class="page-header">
            File:
        </div>
        <div class="row">
            <div class="col-lg-6">
                <form action="index.php" id="data" method="post" enctype="multipart/form-data">
                    <div class="input-group">
                        <input type="file" name="inputfile" class="form-control">
                        <span class="input-group-btn">
                            <button class="btn btn-default" type="button">Enviar</button>
                        </span>
                    </div><!-- /input-group -->
                </form>
            </div><!-- /.col-lg-6 -->
        </div>
        <div class="row padding">
            <?php if(isset($monitor)): ?>
                <ul class="list-group">
                    <li class="list-group-item">
                        <?php $monitor->maxRain(); ?>
                        <b>Precipitação total</b>
                    </li>
                </ul>
            <?php endif; ?>

            <div class="panel panel-default">
                <!-- Table -->
                <table class="table">
                    <thead>
                      <tr>
                        <th>Data:</th>
                        <th>Temperatura<br> mínima:</th>
                        <th>Temperatura<br> média:</th>
                        <th>Temperatura<br> máxima:</th>
                        <th>Umidade<br> mínima:</th>
                        <th>Umidade<br> média:</th>
                        <th>Umidade<br> máxima:</th>
                        <th>Precipitação<br> acumulada diária:</th>
                      </tr>
                    </thead>
                    <tbody>
                    <?php
                        if (isset($monitor)) {
                            $monitor->createTable();
                        }
                    ?>
                    </tbody>
                </table>
            </div>

        </div>
    </div>
        <script src="bower_components/jquery/dist/jquery.min.js" type="text/javascript" charset="utf-8" async defer></script>
        <link rel="stylesheet" href="bower_components/bootstrap/dist/js/bootstrap.min.js">
        <script type="text/javascript">
            var btn = document.querySelector(".btn");
            btn.addEventListener('click', function(e) {
                document.querySelector("table tbody").innerHTML = '';
                document.getElementById("data").submit();
            });
        </script>
</body>
</html>
