var guardando = false;

function init() {
    $("#ticket_form").on("submit", function (e) {
        e.preventDefault();
        guardaryeditar();
    });
}

$(document).ready(function () {
    // Inicializar Summernote.
    $("#tick_descrip").summernote({
        height: 150,
        lang: "es-ES",
        callbacks: {
            onImageUpload: function (image) {
                console.log("image detect...");
                myimagetreat(image[0]);
            },
            onPaste: function (e) {
                console.log("Text detect...");
            }
        }
    });

    // Cargar categorías.
    $.post("../../controller/categoria.php?op=combo", function (data) {
        $("#cat_id").html(data);
    }).fail(function (xhr) {
        console.error("Error al cargar categorías:", xhr.responseText);
    });

    init();
});

function guardaryeditar() {
    if (guardando) {
        return;
    }

    // Validar categoría.
    var categoria = $("#cat_id").val();

    if (!categoria || !/^[1-9]\d*$/.test(categoria)) {
        swal("Atención", "Seleccione una categoría", "warning");
        $("#cat_id").focus();
        return;
    }

    // Validar título.
    var titulo = $.trim($("#tick_titulo").val());

    if (titulo === "") {
        swal(
            "Atención",
            "Ingrese el título del requerimiento",
            "warning"
        );
        $("#tick_titulo").focus();
        return;
    }

    // Validar descripción, permitiendo texto o imágenes.
    var descripcion = $("#tick_descrip").summernote("code");
    var revision = document.createElement("div");
    revision.innerHTML = descripcion;

    var texto = (revision.textContent || "")
        .replace(/[\s\u00a0\u200b-\u200d\ufeff]/g, "");

    var tieneImagen = false;

    $(revision).find("img").each(function () {
        if ($.trim($(this).attr("src") || "") !== "") {
            tieneImagen = true;
        }
    });

    if (texto === "" && !tieneImagen) {
        swal("Atención","Ingrese la descripción del requerimiento","warning");
        $("#tick_descrip").summernote("focus");
        return;
    }

    // Actualizar campos antes de crear FormData.
    $("#tick_titulo").val(titulo);
    $("#tick_descrip").val(descripcion);

    var formData = new FormData(
        document.getElementById("ticket_form")
    );

    var botones = $("#ticket_form").find(":submit");

    guardando = true;
    botones.prop("disabled", true);

    $.ajax({
        url: "../../controller/ticket.php?op=insert",
        type: "POST",
        data: formData,
        contentType: false,
        processData: false,
        dataType: "json",

        success: function (datos) {
            if (datos.success === true) {
                $("#tick_titulo").val("");
                $("#tick_descrip").summernote("reset");

                swal(
                    "Correcto",
                    "Ticket registrado correctamente",
                    "success"
                );
            } else {
                swal(
                    "Error",
                    datos.message || "No se pudo guardar el ticket, faltan datos",
                    "error"
                );
            }
        },

        error: function (xhr) {
            console.error("Error HTTP:", xhr.status);
            console.error("Respuesta:", xhr.responseText);

            var respuesta = xhr.responseJSON;

            swal(
                "Error",
                respuesta && respuesta.message
                    ? respuesta.message
                    : "No fue posible guardar el ticket, faltan datos",
                "error"
            );
        },

        complete: function () {
            guardando = false;
            botones.prop("disabled", false);
        }
    });
}