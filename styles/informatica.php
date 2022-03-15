<?php
include '../auth.php';
$header_btn_regresar = 'index.php';

$num_mes = (isset($_REQUEST['num_mes']) && !empty($_REQUEST['num_mes'])) ? $_REQUEST['num_mes'] : date('m');
$num_anio = (isset($_REQUEST['num_anio']) && !empty($_REQUEST['num_anio'])) ? $_REQUEST['num_anio'] : date('Y');

$link = mysql_connect('172.23.25.31', 'root', 'udiwf')
or die('No se pudo conectar: ' . mysql_error());
mysql_select_db('tickets') or die('No se pudo seleccionar la base de datos');

$sql = " SELECT  COUNT(tks_tickets.`id`) AS tickets, 
                 tks_categories.`acronimo` AS categorias
           FROM  tks_tickets
           JOIN  tks_categories ON tks_tickets.`category` = tks_categories.`id`
          WHERE  CAST(DATE_FORMAT(dt, '%Y%m') AS UNSIGNED) = ".$num_anio.$num_mes."
                 AND status = '3'
       GROUP BY  categorias
       ORDER BY  tickets DESC";

$result = mysql_query($sql,$link);

$categorias = array();
$tickets = array();

while($row = mysql_fetch_array($result, MYSQL_ASSOC)){
    array_push($categorias, $row['categorias']);
    array_push($tickets, $row['tickets']);
}

$sql = "  SELECT  ti.`trackid`,
                  ti.`name`,
                  ti.`dt`,
                  ca.`name` AS category,
                  ti.`owner`,
                  ti.`lastchange`,
                  ti.`subject`
            FROM  tks_tickets ti,
                  tks_categories ca        
           WHERE  ti.`category` = ca.`id`
                  AND CAST(DATE_FORMAT(dt, '%Y%m') AS UNSIGNED) = ".$num_anio.$num_mes."
                  AND status = '3'";

$result = mysql_query($sql,$link);
$a = 0;
while($row = mysql_fetch_array($result, MYSQL_ASSOC)){
    $trackid[$a] = (isset($row['trackid']) && !empty($row['trackid'])) ? $row['trackid'] : 0;
    $solicitante[$a] = (isset($row['name']) && !empty($row['name'])) ? $row['name'] : 0;
    $fecha_creacion[$a] = (isset($row['dt']) && !empty($row['dt'])) ? $row['dt'] : 0;
    $categoria[$a] = (isset($row['category']) && !empty($row['category'])) ? $row['category'] : 0;
    $atendio[$a] = (isset($row['owner']) && !empty($row['owner'])) ? $row['owner'] : 0;
    $fecha_fin[$a] = (isset($row['lastchange']) && !empty($row['lastchange'])) ? $row['lastchange'] : 0;
    $tema[$a] = (isset($row['subject']) && !empty($row['subject'])) ? $row['subject'] : 0;
    
    $query = "  SELECT  usuario
		          FROM  rh_empleados
		         WHERE  usuario_tickets = '".$atendio[$a]."'";
    $stid = oci_parse($conn, $query);
    oci_execute($stid, OCI_DEFAULT);
    $row = oci_fetch_array($stid, OCI_ASSOC+OCI_RETURN_NULLS);
    $usr_atendio[$a] = (isset($row['USUARIO']) && !empty($row['USUARIO'])) ? $row['USUARIO'] : 0;
    
    $query = "  SELECT  count(*) AS encuestas
		          FROM  msa_medicion_encabezado
		         WHERE  doc_referencia = '".$trackid[$a]."'";
    $stid = oci_parse($conn, $query);
    oci_execute($stid, OCI_DEFAULT);
    $row = oci_fetch_array($stid, OCI_ASSOC+OCI_RETURN_NULLS);
    $evaluacion[$a] = (isset($row['ENCUESTAS']) && !empty($row['ENCUESTAS'])) ? $row['ENCUESTAS'] : 0;
    
    if ($evaluacion[$a] <= 0){ $encuestado[$a] = "N"; } else { $encuestado[$a] = "S"; }
    
    $a++;
}

if ($num_mes == 1){$nom_mes = 'Enero';}
if ($num_mes == 2){$nom_mes = 'Febrero';}
if ($num_mes == 3){$nom_mes = 'Marzo';}
if ($num_mes == 4){$nom_mes = 'Abril';}
if ($num_mes == 5){$nom_mes = 'Mayo';}
if ($num_mes == 6){$nom_mes = 'Junio';}
if ($num_mes == 7){$nom_mes = 'Julio';}
if ($num_mes == 8){$nom_mes = 'Agosto';}
if ($num_mes == 9){$nom_mes = 'Septiembre';}
if ($num_mes == 10){$nom_mes = 'Octubre';}
if ($num_mes == 11){$nom_mes = 'Noviembre';}
if ($num_mes == 12){$nom_mes = 'Diciembre';}

$b = 0;

//L3D-181-3ZJ3

?>
<!DOCTYPE html>
<html>
<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <!-- Page title -->
    <title>Satisfaccion Cliente Interno</title>

    <!-- Place favicon.ico and apple-touch-icon.png in the root directory -->
    <!--<link rel="shortcut icon" type="image/ico" href="favicon.ico" />-->

    <!-- Vendor styles -->
    <link rel="stylesheet" href="vendor/fontawesome/css/font-awesome.css" />
    <link rel="stylesheet" href="vendor/metisMenu/dist/metisMenu.css" />
    <link rel="stylesheet" href="vendor/animate.css/animate.css" />
    <link rel="stylesheet" href="vendor/bootstrap/dist/css/bootstrap.css" />

    <!-- App styles -->
    <link rel="stylesheet" href="fonts/pe-icon-7-stroke/css/pe-icon-7-stroke.css" />
    <link rel="stylesheet" href="fonts/pe-icon-7-stroke/css/helper.css" />
    <link rel="stylesheet" href="styles/style.css">
    <link rel="stylesheet" href="dist/jquery.fancybox.min.css">
    <link rel="stylesheet" href="vendor/datatables/media/css/dataTables.bootstrap.min.css" />

</head>
<body class="fixed-navbar fixed-sidebar hide-sidebar">

<!-- Simple splash screen-->
<div class="splash"> <div class="color-line"></div><div class="splash-title"><h1></h1><p></p><div class="spinner"> <div class="rect1"></div> <div class="rect2"></div> <div class="rect3"></div> <div class="rect4"></div> <div class="rect5"></div> </div> </div> </div>
<!--[if lt IE 7]>
<p class="alert alert-danger">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
<![endif]-->
<?php include 'menu.php'; ?>
    <div>        
        <a href="index.php" class="pull-right btn btn-primary">
            <i class="pe-7s-back"></i> Regresar
        </a>
    </div>
   </div>
<!-- Main Wrapper -->
<div id="wrapper">

    <div class="normalheader transition animated fadeIn">
        <div class="hpanel">
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-6">
                        <h2 class="font-light m-b-xs">
                            <strong>Coordinación Informática</strong>
                        </h2>
                    </div>
                    <form action="" method="GET">
                        <div class="col-md-2">
                            <b>Mes: </b> 
                            <select class="form-control" name="num_mes">
                                <option value="01" <?php if('01' == $num_mes) { echo 'selected="selected"'; } ?>>Enero</option>
                                <option value="02" <?php if('02' == $num_mes) { echo 'selected="selected"'; } ?>>Febrero</option>
                                <option value="03" <?php if('03' == $num_mes) { echo 'selected="selected"'; } ?>>Marzo</option>
                                <option value="04" <?php if('04' == $num_mes) { echo 'selected="selected"'; } ?>>Abril</option>
                                <option value="05" <?php if('05' == $num_mes) { echo 'selected="selected"'; } ?>>Mayo</option>
                                <option value="06" <?php if('06' == $num_mes) { echo 'selected="selected"'; } ?>>Junio</option>
                                <option value="07" <?php if('07' == $num_mes) { echo 'selected="selected"'; } ?>>Julio</option>
                                <option value="08" <?php if('08' == $num_mes) { echo 'selected="selected"'; } ?>>Agosto</option>
                                <option value="09" <?php if('09' == $num_mes) { echo 'selected="selected"'; } ?>>Septiembre</option>
                                <option value="10" <?php if('10' == $num_mes) { echo 'selected="selected"'; } ?>>Octubre</option>
                                <option value="11" <?php if('11' == $num_mes) { echo 'selected="selected"'; } ?>>Noviembre</option>
                                <option value="12" <?php if('12' == $num_mes) { echo 'selected="selected"'; } ?>>Diciembre</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <b>Año: </b>
                            <select class="form-control" name="num_anio">
                                <option value="<?php echo date('Y'); ?>" <?php if($num_anio == date('Y')){echo 'selected="selected"';}?>><?php echo date('Y'); ?></option>
                                <option value="<?php echo (date('Y') -1); ?>" <?php if($num_anio == (date('Y') -1)){echo 'selected="selected"';}?>><?php echo (date('Y') -1); ?></option>
                                <option value="<?php echo (date('Y') -2); ?>" <?php if($num_anio == (date('Y') -2)){echo 'selected="selected"';}?>><?php echo (date('Y') -2); ?></option>
                                <option value="<?php echo (date('Y') -3); ?>" <?php if($num_anio == (date('Y') -3)){echo 'selected="selected"';}?>><?php echo (date('Y') -3); ?></option>
                            </select>
                        </div>
                        <div class="col-md-1">
                            <br>
                            <input type="submit" class="btn btn-md btn-primary" value="Ver">
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="content animate-panel">
        <div class="row">
            <div class="col-lg-8">    
                <div class="hpanel hgreen">
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-lg-12">
                                <h3>Detalle de Tickets</h3>
                                <div class="pull-right" id="filtro"></div>
                                <br>
                                <h4>Atencidos en el mes de <?php echo $nom_mes;?></h4>
                                <br>                     
                                <div class="col-md-12">
                                    <table class="table table-striped table-bordered table-hover" id="table">
                                        <thead>
                                            <tr>
                                                <th style="text-align: center">Solicitante</th>
                                                <th style="text-align: center">Tema</th>
                                                <th style="text-align: center">Fecha Solicitud</th>
                                                <th style="text-align: center">Categoría del Ticket</th>
                                                <th style="text-align: center">Quien Atendió</th>
                                                <th style="text-align: center">Fecha Finalizacion</th>
                                                <th style="text-align: center">Visualizar</th>
                                            </tr>
                                        </thead>
                                        <tbody> 
                                        <?php while ($b < count($trackid)){?>                                   
                                            <tr>
                                                <td style="text-align: center"><?php echo $solicitante[$b]; ?></td>
                                                <td style="text-align: center"><?php echo $tema[$b]; ?></td>
                                                <td style="text-align: center"><?php echo $fecha_creacion[$b]; ?></td>
                                                <td style="text-align: center"><?php echo $categoria[$b]; ?></td>
                                                <td style="text-align: center"><?php echo $usr_atendio[$b]; ?></td>
                                                <td style="text-align: center"><?php echo $fecha_fin[$b]; ?></td>
                                                <?php if ($encuestado[$b] == 'N'){?>
                                                <td style="text-align: center"><a class="fancy" href="detalle_ticket.php?id=<?php echo $trackid[$b]; ?>"><i class="pe-7s-search" style="font-size: 25px"></i></a></td>
                                                <?php } else {?>
                                                <td style="text-align: center"><i class="pe-7s-check text-success" style="font-size: 25px"></i></td>
                                                <?php }?>
                                            </tr>
                                            <?php $b++; }?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>                
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="hpanel hgreen">
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-lg-12">
                                <h3>Gráfica de Tickets</h3>
                                <div class="pull-right" id="filtro"></div>
                                <br>
                                <h4>Atendidos en el mes de <?php echo $nom_mes;?></h4>
                                <br>
                                <div class="chart" id="ct-chart" style="height: 650px"></div>                     
                            </div>
                        </div>                
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Vendor scripts -->
<script src="vendor/jquery/dist/jquery.min.js"></script>
<script src="vendor/jquery-ui/jquery-ui.min.js"></script>
<script src="vendor/slimScroll/jquery.slimscroll.min.js"></script>
<script src="vendor/bootstrap/dist/js/bootstrap.min.js"></script>

<!-- -->
<script src="vendor/jquery-flot/jquery.flot.js"></script>
<script src="vendor/jquery-flot/jquery.flot.resize.js"></script>
<script src="vendor/jquery-flot/jquery.flot.pie.js"></script>
<!-- -->

<script src="vendor/metisMenu/dist/metisMenu.min.js"></script>
<script src="vendor/iCheck/icheck.min.js"></script>
<script src="vendor/chartist/dist/chartist.min.js"></script>
<script src="vendor/chartjs/Chart.min.js"></script>
<script src="vendor/sparkline/index.js"></script>
<script src="dist/jquery.fancybox.min.js"></script>

<!-- -->
<!-- DataTables -->
<script src="vendor/datatables/media/js/jquery.dataTables.min.js"></script>
<script src="vendor/datatables/media/js/dataTables.bootstrap.min.js"></script>
<script src="vendor/peity/jquery.peity.min.js"></script>

<!-- App scripts -->
<script src="scripts/homer.js"></script>
<script src="scripts/charts.js"></script>
<!-- HIGHCHARTS -->
<script src="js/highcharts.js"></script>
<script>
$(function () {
    $('#ct-chart').highcharts({
        credits: {
            enabled: false
        },
        chart: {
            type: 'bar'
        },
        title: {
            text: ''
        },
        plotOptions: {
            bar: {
                dataLabels: {
                    enabled: true,
                }
            }
        },
        xAxis: {
            categories: <?php echo json_encode($categorias);?>
        },
        yAxis: {
            
            title: {
                text: ''
            }
        },
        series: [{
            name: 'Tickets',
            color: '#3498DB',
            data: <?php echo json_encode($tickets, JSON_NUMERIC_CHECK);?>
        }],
    });
});
</script> 
<script>

    $(function () {
        
        $('#table').dataTable({
          "language": {
              "url": "vendor/datatables/Spanish.json"
          },
            searching: true,
            "deferRender": true,
            initComplete: function() {
              var column = this.api().column(3);

              var select = $('<select class="form-control"><option value=""> - TODOS - </option></select>')
                .appendTo('#filtro')
                .on('change', function() {
                  var val = $(this).val();
                  column.search(val).draw()
                });

               var offices = []; 
               column.data().toArray().forEach(function(s) {
               		s = s.split(',');
                  s.forEach(function(d) {
                    if (!~offices.indexOf(d)) {
                    	offices.push(d)
                      select.append('<option value="' + d + '">' + d + '</option>');                         }
                  })
               })    
            }
        });
        
    });

</script>
<script>
       jQuery(document).ready(function() {
        $("a.fancy").fancybox({
        	smallBtn : true,
            type : 'iframe',
            iframe : {
                scrolling : 'yes'
            }       
        });
       });
</script>

</body>
</html>