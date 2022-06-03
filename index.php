<?php
/*
Poner en .htaccess
<ifModule mod_headers.c>
Header always unset X-Frame-Options
</ifModule>
 */

date_default_timezone_set('America/Bogota');

if (isset($_GET['key'])) {
    if ($_GET['key'] != "SIAP") {
        echo "La clave no es correcta.";
        exit();
    }
} else {
    echo "Debe ingresar la clave compartida.";
    exit();
}

$msg = "";
//  && is_numeric($_GET['id'])
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $target_dir = "uploads/$id/";

    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    $names = $_FILES['upload']['name'] ?? [];
    for ($i = 0; $i < count($names); $i++) {
        // If the file is larger than 5MB
        if ($_FILES["upload"]["size"][$i] > 5000000) {
            $msg = "Uno o más archivos pesan más de lo establecido.";
        } else {
            $target_file = $target_dir . date("dmY-hisa-") . basename($names[$i]);
            move_uploaded_file($_FILES["upload"]["tmp_name"][$i], $target_file);
        }
    }

    // Si se seleccionaron archivos.
    if (count($names) > 0) {
        ($msg != "") ? header("Location:index.php?key=SIAP&id=$id&msg=$msg") : header("Location:index.php?key=SIAP&id=$id");
    }

    $files = array_diff(scandir($target_dir), array('.', '..'));
} else {
    // , numérico
    echo "Debe ingresar un ID.";
}

function human_filesize($bytes, $decimals = 2)
{
    $sz = 'BKMGTP';
    $factor = floor((strlen($bytes) - 1) / 3);
    return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . ' ' . @$sz[$factor] . 'B';
}
?>

<!doctype html>
<html lang="es">

<head>
  <!-- Required meta tags -->
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">

  <!-- Load CSS file for DataTables  -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/datatables/1.10.21/css/jquery.dataTables.min.css"
    integrity="sha512-1k7mWiTNoyx2XtmI96o+hdjP8nn0f3Z2N4oF/9ZZRgijyV4omsKOXEnqL1gKQNPy2MTSP9rIEWGcH/CInulptA=="
    crossorigin="anonymous" referrerpolicy="no-referrer" />

  <!-- Load jQuery -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"
    integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>

  <!-- Load DataTables -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/datatables/1.10.21/js/jquery.dataTables.min.js"
    integrity="sha512-BkpSL20WETFylMrcirBahHfSnY++H2O1W+UnEEO4yNIl+jI2+zowyoGJpbtk6bx97fBXf++WJHSSK2MV4ghPcg=="
    crossorigin="anonymous" referrerpolicy="no-referrer"></script>

  <!-- Add icon library -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

  <title>Steelheart FileUploader</title>
</head>

<body>
  <div class="container-fluid p-4">
    <?php if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {?>
      <h1>SteelheartFile Uploader</h1>
    <?php } else {?>
      <form method="post" enctype="multipart/form-data">
        <div class="row p-4">
          <div class="col-1"></div>
          <div class="col-8">
            <input class="form-control" type="file" name="upload[]" multiple>
            <div class="form-text">El tamaño máximo permitido es 5 MB.</div>
          </div>
          <div class="col-2 text-center">
            <input type="submit" class="btn btn-dark" value="Cargar archivos" name="submit">
          </div>
          <div class="col-1"></div>
        </div>
      </form>

      <!-- HTML for Table  -->
      <br>
      <table id="dT" class="display table">
        <thead class="table-dark">
          <tr>
            <th class="w-50">Nombre</th>
            <th>Tamaño</th>
            <th>Opciones</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($files as &$file) {?>
            <tr>
              <td><?php echo $file; ?></td>
              <td><?php echo human_filesize(filesize($target_dir . $file)); ?></td>
              <td><a href="<?php echo $target_dir . $file; ?>" class="btn btn-secondary btn-sm" download><i class="fa fa-download"></i> Descargar</a></td>
            </tr>
          <?php }?>
        </tbody>
      </table>
    <?php }?>
  </div>

  <!-- Additional JavaScript -->
  <script>
    $(document).ready(function () {
      $('#dT').dataTable({
        info: false,
        order: [[0, 'desc']],
        lengthMenu: [ 5, 10, 15, 20 ],
        language: {
          emptyTable:     "No hay entradas para mostrar",
          lengthMenu: "Mostrar _MENU_ entradas",
          loadingRecords: "Cargando...",
          processing:     "Procesando...",
          search: "Buscar:",
          zeroRecords:    "No hay resultados para mostrar",
          paginate: {
            first:    '«',
            previous: 'Anterior',
            next:     'Siguiente',
            last:     '»'
          }
        }
      });

      <?php if (isset($_GET['msg'])) {?>
        Swal.fire("<?php echo $_GET['msg']; ?>");
      <?php }?>
    });
  </script>

  <!-- Bootstrap Bundle with Popper -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p"
    crossorigin="anonymous"></script>

  <!-- Add Swal library -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</body>

</html>