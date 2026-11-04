<?php
require_once __DIR__ . "/../../config/conexion.php";

// conexion.php ya inicia la sesión.
if (!isset($_SESSION['usu_id'])) {
    header('Location: ' . \Conectar::ruta() . "index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
    <?php require_once __DIR__ . "/../MainHead/head.php"; ?>
    <title>DDN_Ticket - Detalle Ticket</title>

    <style>
        /* Imágenes del editor, descripción e historial. */
        .note-editable img,
        #tick_descripusu img,
        #lbldetalle img {
            max-width: 100% !important;
            width: auto !important;
            height: auto !important;
            max-height: 350px !important;
        }

        #tick_descripusu {
            min-height: 150px;
            height: auto;
            overflow-wrap: anywhere;
            overflow-x: auto;
        }

        #tick_descripusu img {
            display: block;
            margin: 10px 0;
        }

        #lbldetalle .cont-in {
            overflow-wrap: anywhere;
        }
    </style>

</head>
<body class="with-side-menu">

    <?php require_once __DIR__ . "/../MainHeader/header.php"; ?>

    <div class="mobile-menu-left-overlay"></div>

    <?php require_once __DIR__ . "/../MainNav/nav.php"; ?>

    <!-- Contenido de la página -->
    <div class="page-content">
        <div class="container-fluid">
            <header class="seccion-header">
                <div class="tbl">
                    <div class="tbl-row">
                        <div class="tbl-cell">
                            <h3 id="lblnomidticket">Detalle Ticket</h3>
                            <div id="lblestado"></div>
                            <span class="label label-pill label-info" id="lblnomusuario"></span>
                            <span class="label label-pill label-success" id="lblfechcrea"></span>
                            <ol class="breadcrumb breadcrumb-simple">
                                <li><a href="#">Home</a></li>
                                <li class="active">Detalle Ticket</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Datos del ticket -->
            <div class="box-typical box-typical-padding">
                <div class="row">

                    <!-- Categoría -->
                    <div class="col-lg-6">
                        <fieldset class="form-group">
                            <label class="form-label semibold" for="cat_id">Categoría</label>
                            <select class="form-control"
                                    id="cat_id"
                                    name="cat_id"
                                    disabled>
                            </select>
                        </fieldset>
                    </div>

                    <!-- Título -->
                    <div class="col-lg-6">
                        <fieldset class="form-group">
                            <label class="form-label semibold" for="tick_titulo">
                                Título
                            </label>

                            <input type="text"
                                   class="form-control"
                                   id="tick_titulo"
                                   name="tick_titulo"
                                   readonly>
                        </fieldset>
                    </div>

                    <!-- Descripción -->
                    <div class="col-lg-12">
                        <fieldset class="form-group">
                            <label class="form-label semibold" for="tick_descripusu">Descripción del Requerimiento: </label>

                            <div class="summernote-theme-1">
                                <div id="tick_descripusu"class="form-control"style="min-height: 150px; height: auto; overflow-wrap: anywhere;"></div>
                                <style>
                                    #tick_descripusu img {
                                        max-width: 100% !important;
                                        width: auto !important;
                                        height: auto !important;
                                        max-height: 200px;
                                        display: block;
                                        margin: 10px 0;
                                    }
                                </style>
                            </div>
                        </fieldset>
                    </div>

                </div><!-- /.row -->
            </div><!-- /.box-typical -->

            <!-- Historial de actividad -->
            <section class="activity-line" id="lbldetalle"></section>

            <div class="box-typical box-typical-padding" id="pnlcomentario"> 

                <h5 class="m-t-lg with-border">
                   <p>
                    Ingrese un comentario para el ticket
                   </p>
                </h5>
                <div class="row">
                        <!-- NUEVA OBSERVACIÓN -->
                        <div class="col-lg-12">
                        <fieldset class="form-group">
                            <label class="form-label semibold" for="tickd_descrip">Descripción</label>
                           <div class="summernote-theme-1">
                                <textarea id="tickd_descrip"
                                            name="tickd_descrip"
                                            class="form-control summernote"
                                            rows="5"></textarea>
                            </div>
                        </fieldset>
                        </div>
                    <div class="col-lg-12">
                        <button type="button" id="btnenviar" class="btn btn-rounded btn-inline btn-primary">Enviar</button>
                        <button type="button" id="btncerrarticket" class="btn btn-rounded btn-inline btn-danger">Cerrar Ticket</button>
                    </div>
                </div><!-- row -->
                    
            </div><!-- box-typical -->


        </div><!-- /.container-fluid -->
    </div><!-- /.page-content -->

    <?php require_once __DIR__ . "/../MainJs/js.php"; ?>
    <script src="detalleticket.js"></script>

</body>
</html>
