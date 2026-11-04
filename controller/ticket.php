<?php

    require_once("../config/conexion.php");
    require_once("../models/Ticket.php");

    $ticket = new \Ticket();

    switch ($_GET["op"]) {
        case "insert":
            header("Content-Type: application/json; charset=utf-8");

            // Obtener el usuario desde la sesión.
            if (!isset($_SESSION["usu_id"])) {
                http_response_code(401);
                echo json_encode(["success" => false,"message" => "Debe iniciar sesión"]);
            break;
            }

            // Leer los campos sin generar avisos si faltan.
            $cat_id = filter_input(INPUT_POST,"cat_id",FILTER_VALIDATE_INT
            );

            $titulo_recibido = $_POST["tick_titulo"] ?? "";
            $descripcion_recibida = $_POST["tick_descrip"] ?? "";

            $tick_titulo = is_string($titulo_recibido)
                ? trim($titulo_recibido): "";

            $tick_descrip = is_string($descripcion_recibida)
                ? trim($descripcion_recibida): "";

            if (!$cat_id || $cat_id < 1) {
                http_response_code(400);
                echo json_encode(["success" => false,"message" => "Seleccione una categoría válida"]);
    break;
    }

        if ($tick_titulo === "") {
            http_response_code(400);

            echo json_encode([
                "success" => false,"message" => "Ingrese el título del requerimiento"]);
        break;
        }

        // Summernote puede enviar HTML aunque el editor esté vacío.
        $texto_descripcion = html_entity_decode(
            strip_tags($tick_descrip),
            ENT_QUOTES | ENT_HTML5,
            "UTF-8"
        );
        //elimina espacios en blanco, saltos de línea y caracteres invisibles
        $texto_descripcion = preg_replace(
            '/[\s\p{Z}\x{200B}-\x{200D}\x{FEFF}]+/u',
            "",
            $texto_descripcion
        );

        // Permitir también una descripción que contenga una imagen.
        $tiene_imagen = preg_match(
            '~<img\b[^>]*\bsrc\s*=\s*(?:"[^"]*\S[^"]*"|\'[^\']*\S[^\']*\'|[^\s>]+)~i',
            $tick_descrip
        ) === 1;

        if (
            ($texto_descripcion === "" || $texto_descripcion === null)
            && !$tiene_imagen
        ) {
            http_response_code(400);
            echo json_encode(["success" => false,"message" => "Ingrese la descripción del requerimiento"]);
        break;
        }   

        $results = $ticket->insert_ticket(
            (int) $_SESSION["usu_id"],
            $cat_id,
            $tick_titulo,
            $tick_descrip
        );

        echo json_encode([
            "success" => (bool) $results,
            "message" => $results
                ? "Ticket registrado correctamente"
                : "Error al guardar el ticket"
        ]);
        break;

            case "listar_x_usu":
            //$datos = $ticket->listar_ticket_x_usu($_POST["usu_id"]);//
            if (!isset($_SESSION["usu_id"], $_SESSION["rol_id"])) {
                http_response_code(401);
                echo json_encode(["error" => "Debe iniciar sesión"]);
            break;
            }

            $rol_id = (int) $_SESSION["rol_id"];

            if (!in_array($rol_id, [1, 2], true)) {
                http_response_code(403);
                echo json_encode(["error" => "Rol no autorizado"]);
            break;
            }

            $datos = $ticket->listar_ticket_x_usu(
                (int) $_SESSION["usu_id"],
                $rol_id
            );
            $data = array();
            foreach ($datos as $row) { //recorre cada fila de datos y crea un array con los valores de cada columna
                $sub_array = array();
                $sub_array[] = $row["tick_id"];
                $sub_array[] = $row["cat_nom"];
                $sub_array[] = $row["tick_titulo"];

                if ($row["tick_estado"] == "Abierto") {
                    $sub_array[] = '<span class="label label-pill label-success">Abierto</span>';
                } elseif ($row["tick_estado"] == "En Proceso") {
                    $sub_array[] = '<span class="label label-pill label-warning">En Proceso</span>';
                } elseif ($row  ["tick_estado"] == "Cerrado") {
                    $sub_array[] = '<span class="label label-pill label-danger">Cerrado</span>';
                } else {
                    $sub_array[] = '<span class="label label-pill label-secondary">Desconocido</span>';

                }
            
                $sub_array[] = date('d-m-Y - H:i:s', strtotime($row["fech_crea"]));
                $sub_array[] = '<button type="button" onClick="ver('.$row["tick_id"].');" id="'.$row["tick_id"].'" class="btn btn-intline-primary btn-sm ladda-button"><div><i class="fa fa-eye"></i></button>';
                $data[] = $sub_array;
            }
            $results = array( //crea un array con los resultados
                "sEcho" => 1,
                "iTotalRecords" => count($data),
                "iTotalDisplayRecords" => count($data),
                "aaData" => $data);
            echo json_encode($results);
            break;

            case "listardetalle": // Listar el detalles de un ticket específico
                $datos = $ticket->listar_ticketdetalle_x_ticket($_POST["tick_id"]);
                ?>
                    <?php
                        foreach ($datos as $row) {
                            ?>
                                <article class="activity-line-item box-typical">
                                    <div class="activity-line-date">
                                        <?php echo date("d-m-Y", strtotime($row["fech_crea"])); ?>
                                    </div>
                                    <header class="activity-line-item-header">
                                        <div class="activity-line-item-user">
                                            <div class="activity-line-item-user-photo">
                                                <a href="#">
                                                    <img src="../../public/img/<?php echo (int) $row["rol_id"] === 2? "DDN-logo-2-mob.png" : "DDN-logo-1-mob.png"; ?>" alt="">
                                                </a>
                                            </div>
                                            <div class="activity-line-item-user-name"><?php echo $row["usu_nombre"] . " " . $row["usu_paterno"] . " " . $row["usu_materno"]; ?></div>
                                            <div class="activity-line-item-user-status">
                                                <?php 
                                                    if ($row["rol_id"] == 1) {
                                                        echo "Usuario";
                                                    } else {
                                                        echo "Soporte TI";
                                                    }
                                                ?>
                                            </div>
                                        </div>
                                    </header>
                                    <div class="activity-line-action-list">
                                        <section class="activity-line-action">
                                            <div class="time"><?php echo date("H:i:s", strtotime($row["fech_crea"])); ?></div>
                                            <div class="cont">
                                                <div class="cont-in">
                                                    <p>
                                                        <?php echo $row["tickd_descrip"]; ?>

                                                    </p>
                                                </div>
                                            </div>
                                        </section><!--.activity-line-action-->
                                       
                                    </div><!--.activity-line-action-list-->
                                </article>
                          <?php
                        }    
                    ?>
                <?php
            break;
               
            case "mostrar":  // Muestra los datos de los detalles de un ticket específico
                $datos = $ticket->listar_ticket_x_id($_POST["tick_id"]);
                $output = [];
                if (is_array($datos) ==true and count($datos) > 0){
                    foreach ($datos as $row)
                    {
                        $output["tick_id"] = $row["tick_id"];
                        $output["usu_id"] = $row["usu_id"];
                        $output["cat_id"] = $row["cat_id"];
                        $output["tick_titulo"] = $row["tick_titulo"];
                        $output["tick_descrip"] = $row["tick_descrip"];
                        if ($row["tick_estado"] == "Abierto") {
                            $output["tick_estado"] = '<span class="label label-pill label-success">Abierto</span>';
                        } elseif ($row["tick_estado"] == "En Proceso") {
                            $output["tick_estado"] = '<span class="label label-pill label-warning">En Proceso</span>';
                        } elseif ($row  ["tick_estado"] == "Cerrado") {
                            $output["tick_estado"] = '<span class="label label-pill label-danger">Cerrado</span>';
                        } else {
                            $output["tick_estado"] = '<span class="label label-pill label-secondary">Desconocido</span>';
                        }

                        $output["tick_estado_texto"] = $row["tick_estado"];

                        $output["fech_crea"] = $row["fech_crea"];
                        $output["usu_nombre"] = $row["usu_nombre"];
                        $output["usu_paterno"] = $row["usu_paterno"];
                        $output["usu_materno"] = $row["usu_materno"];
                        $output["cat_nom"] = $row["cat_nom"];
                    }
                }
                    echo json_encode($output);
                
            break;

            case "insertdetalle":
                header("Content-Type: application/json; charset=utf-8");

                if (!isset($_SESSION["usu_id"])) {
                    http_response_code(401);
                    echo json_encode([
                        "success" => false,"message" => "Debe iniciar sesión"
                    ]);
                    break;
                }

                $tick_id = filter_input(INPUT_POST, "tick_id", FILTER_VALIDATE_INT);
                $tickd_descrip = trim($_POST["tickd_descrip"] ?? "");

                if (!$tick_id || $tick_id < 1 || $tickd_descrip === "") {
                    http_response_code(400);
                    echo json_encode([
                        "success" => false,"message" => "Falta el ticket o la observación"
                    ]);
                    break;
                }

                $resultado = $ticket->insert_ticketdetalle(
                    $tick_id,
                    (int) $_SESSION["usu_id"],
                    $tickd_descrip
                );

                echo json_encode([
                    "success" => $resultado,
                    "message" => $resultado
                        ? "Observación registrada" : "No se pudo registrar la observación"
                ]);
            break;
            case "cerrar": // Cierra un ticket específico
                header("Content-Type: application/json; charset=utf-8");

                if (!isset($_SESSION["usu_id"], $_SESSION["rol_id"])) {
                    http_response_code(401);
                    echo json_encode([
                        "success" => false,
                        "message" => "Debe iniciar sesión"
                    ]);
                    break;
                }

                $tick_id = filter_input(INPUT_POST, "tick_id", FILTER_VALIDATE_INT);
                $rol_id = (int) $_SESSION["rol_id"];

                if (!$tick_id || $tick_id < 1) {
                    http_response_code(400);
                    echo json_encode([
                        "success" => false,
                        "message" => "ID de ticket inválido"
                    ]);
                    break;
                }

                if (!in_array($rol_id, [1, 2], true)) {
                    http_response_code(403);
                    echo json_encode([
                        "success" => false,
                        "message" => "Rol no autorizado"
                    ]);
                    break;
                }

                $resultado = $ticket->cerrar_ticket(
                    $tick_id,
                    (int) $_SESSION["usu_id"],
                    $rol_id
                );

                echo json_encode([
                    "success" => $resultado,
                    "message" => $resultado
                        ? "Ticket cerrado correctamente"
                        : "El ticket ya está cerrado, no existe o no tiene permiso"
                ]);
            break;
}
?>