<?php
require_once "../../config/conexion.php"; // conexion.php ya contiene session_start()
if (!isset($_SESSION['user_id'])) {
?>

<!DOCTYPE html>//template Blank-Start UI//
<html>
<?php require_once("../MainHead/head.php"); ?>
<title>DDN_Ticket - Nuevo Ticket</title>
</head>

<body class="with-side-menu">
    <?php require_once("../MainHeader/header.php"); ?>
    <div class="mobile-menu-left-overlay"></div>
    <?php require_once("../MainNav/nav.php"); ?>

    <!-- Contenido de la página -->
    <div class="page-content">
        <div class="container-fluid">
            <header class="section-header">
                <div class="tbl">
                    <div class="tbl-row">
                        <div class="tbl-cell">
                            <h3>Nuevo Ticket</h3>
                            <ol class="breadcrumb breadcrumb-simple">
                                <li><a href="../Home/">Home</a></li>
                                <li class="active">Nuevo Ticket</li>
                            </ol>

                        </div>
                    </div>
                </div>
            </header>
            <div class="box-typical box-typical-padding">
                <p>
                    En esta sección podrá crear un nuevo requerimiento de soporte TI.
                    Por favor, complete todos los campos requeridos y haga clic en
                    el botón "Guardar" para registrar su solicitud.
                </p>
                <h5 class="m-t-lg with-border">
                    Ingreso de Requerimiento
                </h5>
                <div class="row">
                    <form method="post" id="ticket_form">
                        <input type="hidden" id="usu_id" name="usu_id" value="<?php echo $_SESSION["usu_id"] ?>">

                            <!-- CATEGORIA -->
                            <div class="col-lg-6">
                                <fieldset class="form-group">
                                    <label class="form-label semibold" for="cat_id">Categoría</label>
                                    <select id="cat_id" name="cat_id" class="form-control">
                                </select>
                                </fieldset>
                            </div>
                            <!-- TITULO -->
                            <div class="col-lg-6">
                                <fieldset class="form-group">
                                    <label class="form-label semibold" for="tick_titulo">Titulo</label>
                                    <input type="text" class="form-control" id="tick_titulo" name="tick_titulo" placeholder="Ingrese título del requerimiento">
                                </fieldset>
                            </div>
                            <!-- DESCRIPCION -->
                            <div class="col-lg-12">
                                <fieldset class="form-group">
                                    <label class="form-label semibold" for="tick_descrip">Descripción</label>
                                    <div class="summernote-theme-1">
                                        <textarea id="tick_descrip" name="tick_descrip" class="summernote" name="name"></textarea>
                                    </div>
                                </fieldset>
                            </div>
                            <div class="col-lg-12">
                                <button type="submit" name="action" value="add" class="btn btn-rounded btn-inline btn-primary">Guardar</button>
                            </div>
                    </form>
                </div><!-- row -->
                    
            </div><!-- box-typical -->

        </div><!-- container-fluid -->
    </div><!-- Contenido de la pagina -->


    <?php require_once("../MainJs/js.php"); ?>


    <!-- JS especifico de Nuevo Ticket -->
    <script src="nuevoticket.js"></script>


</body>
</html>

<?php
} else {

    header('Location: ' . \Conectar::ruta() . "index.php");
    exit();

}
?>