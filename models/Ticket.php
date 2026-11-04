<?php

class Ticket extends \Conectar {
    public function insert_ticket($usu_id, $cat_id, $tick_titulo, $tick_descrip) {
        $conectar = parent::Conexion();
        parent::set_names();
        $sql = "INSERT INTO tm_ticket (usu_id, cat_id, tick_titulo, tick_descrip, tick_estado, fech_crea, est) VALUES (?, ?, ?, ?, 'Abierto', NOW(), '1')";
        $stmt = $conectar->prepare($sql);
        $stmt->bindValue(1, $usu_id, PDO::PARAM_INT);
        $stmt->bindValue(2, $cat_id, PDO::PARAM_INT);
        $stmt->bindValue(3, $tick_titulo, PDO::PARAM_STR);
        $stmt->bindValue(4, $tick_descrip, PDO::PARAM_STR);
        return $stmt->execute();
    }

    // obtener todos los tickets activos que pertenecen a un usuario específico
    public function listar_ticket_x_usu($usu_id, $rol_id) {
        $conectar = parent::Conexion();
        parent::set_names();
        $sql = "SELECT
            tm_ticket.tick_id,
            tm_ticket.usu_id,
            tm_ticket.cat_id,
            tm_ticket.tick_titulo,
            tm_ticket.tick_descrip,
            tm_ticket.tick_estado,
            tm_ticket.fech_crea,
            tm_usuario.usu_nombre,
            tm_usuario.usu_paterno,
            tm_categoria.cat_nom
            FROM
            tm_ticket
            INNER JOIN tm_categoria ON tm_ticket.cat_id = tm_categoria.cat_id
            INNER JOIN tm_usuario ON tm_ticket.usu_id = tm_usuario.usu_id
            WHERE
            tm_ticket.est = 1";

        if ($rol_id == 1) {
            $sql .= " AND tm_ticket.usu_id = ?";
        }

        $stmt = $conectar->prepare($sql);

        if ($rol_id == 1) {
            $stmt->bindValue(1, $usu_id, PDO::PARAM_INT);
        }

        $stmt->execute();
        return $stmt->fetchAll();
    }

    // obtener un ticket específico por su ID.
    public function listar_ticketdetalle_x_ticket($tick_id)
    {
        $conectar = parent::Conexion();
        parent::set_names();
        $sql = "SELECT
            td_ticketdetalle.tickd_id,
            td_ticketdetalle.tickd_descrip,
            td_ticketdetalle.fech_crea,
            tm_usuario.usu_nombre,
            tm_usuario.usu_paterno,
            tm_usuario.usu_materno,
            tm_usuario.rol_id
            FROM
            td_ticketdetalle
            INNER JOIN tm_usuario ON td_ticketdetalle.usu_id = tm_usuario.usu_id
            WHERE td_ticketdetalle.tick_id = ?
            ORDER BY td_ticketdetalle.tickd_id ASC";
        $stmt = $conectar->prepare($sql);
        $stmt->bindValue(1, $tick_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    // listar los detalles de un ticket específico por su ID.
    public function listar_ticket_x_id($tick_id)
    {
        $conectar = parent::Conexion();
        parent::set_names();

        $sql = "SELECT
            t.tick_id,
            t.usu_id,
            t.cat_id,
            t.tick_titulo,
            t.tick_descrip,
            t.tick_estado,
            t.fech_crea,
            u.usu_nombre,
            u.usu_paterno,
            u.usu_materno,
            c.cat_nom
            FROM tm_ticket t
            INNER JOIN tm_usuario u ON t.usu_id = u.usu_id
            INNER JOIN tm_categoria c ON t.cat_id = c.cat_id
            WHERE t.tick_id = ?
              AND t.est = 1";

        $stmt = $conectar->prepare($sql);
        $stmt->bindValue(1, $tick_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    //inserta un detalle de ticket específico
    public function insert_ticketdetalle($tick_id, $usu_id, $tickd_descrip)
    {
        $conectar = parent::Conexion();
        parent::set_names();

        $sql = "INSERT INTO td_ticketdetalle
                    (tick_id, usu_id, tickd_descrip, fech_crea, est)
                VALUES (?, ?, ?, NOW(), 1)";

        $stmt = $conectar->prepare($sql);
        $stmt->bindValue(1, $tick_id, PDO::PARAM_INT);
        $stmt->bindValue(2, $usu_id, PDO::PARAM_INT);
        $stmt->bindValue(3, $tickd_descrip, PDO::PARAM_STR);

        return $stmt->execute();
    }
    //cierra un ticket específico, solo si el usuario es el propietario o tiene rol de administrador y actualiza el estado del ticket a "Cerrado".
    public function cerrar_ticket($tick_id, $usu_id, $rol_id)
    {
        $conectar = parent::Conexion();
        parent::set_names();

        if (!in_array($rol_id, [1, 2], true)) {
            return false;
        }

        try {
            $conectar->beginTransaction();

            $sql = "UPDATE tm_ticket
                    SET tick_estado = 'Cerrado'
                    WHERE tick_id = ?
                    AND est = 1
                    AND tick_estado <> 'Cerrado'";

            if ($rol_id === 1) {
                $sql .= " AND usu_id = ?";
            }

            $stmt = $conectar->prepare($sql);
            $stmt->bindValue(1, $tick_id, PDO::PARAM_INT);

            if ($rol_id === 1) {
                $stmt->bindValue(2, $usu_id, PDO::PARAM_INT);
            }

            if (!$stmt->execute()) {
                $conectar->rollBack();
                return false;
            }

            if ($stmt->rowCount() === 0) {
                $conectar->rollBack();
                return false;
            }

            // Registrar quién cerró el ticket.
            $sqlDetalle = "INSERT INTO td_ticketdetalle
                            (tick_id, usu_id, tickd_descrip, fech_crea, est)
                        VALUES (?, ?, ?, NOW(), 1)";

            $detalle = $conectar->prepare($sqlDetalle);
            $detalle->bindValue(1, $tick_id, PDO::PARAM_INT);
            $detalle->bindValue(2, $usu_id, PDO::PARAM_INT);
            $detalle->bindValue(3, "TICKET CERRADO", PDO::PARAM_STR);

            if (!$detalle->execute()) {
                $conectar->rollBack();
                return false;
            }

            return $conectar->commit();
        } catch (\Throwable $e) {
            if ($conectar->inTransaction()) {
                $conectar->rollBack();
            }

            error_log("Error al cerrar ticket: " . $e->getMessage());
            return false;
        }
}
}