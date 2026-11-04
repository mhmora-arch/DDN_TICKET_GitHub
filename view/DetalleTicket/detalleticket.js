function init() {

}

$(document).ready(function() {
	var tick_id = getUrlParameter('ID');

	$.post("../../controller/ticket.php?op=listardetalle", { tick_id : tick_id }, function (data) {
		$("#lbldetalle").html(data);
	});

	  $('#tickd_descrip').summernote({ 
        height: 150,
		lang: 'es-ES',
		callbacks: {
			onImageUpload: function(image) {
				console.log("image detect...");
				myimagetreat(image[0]);
				},
				onPaste: function(e) {
					console.log("Text detect...");
				}
			}
	});
	
	$.post("../../controller/ticket.php?op=mostrar", { tick_id : tick_id }, function (data) {
		// Cargar datos del ticket
		console.log("Datos del ticket:", data);
		data = JSON.parse(data);
		console.log("Descripción recibida:", data.tick_descrip);
		console.log("Campos encontrados:", $("#tickd_descripusu").length);

		$("#lblestado").html(data.tick_estado);
		$("#lblnomusuario").html(data.usu_nombre + " " + data.usu_paterno + " " + data.usu_materno);
		$("#lblfechcrea").html(data.fech_crea);

		$("#lblnomidticket").html("Detalle Ticket - " + data.tick_id);

       $("#pnlcomentario").toggle(
        data.tick_estado_texto !== "Cerrado"
        );

		// Cargar las categorías y seleccionar la del ticket.
		$.post("../../controller/categoria.php?op=combo", function (categorias) {
			$("#cat_id").html(categorias);
			$("#cat_id").val(data.cat_id);
		}).fail(function (xhr) {
			console.error("Error al cargar categorías:", xhr.responseText);
		});
		$("#tick_titulo").val(data.tick_titulo);
		$("#tick_descripusu").html(data.tick_descrip || "");
	});

});

	function getUrlParameter(sParam) {
		var sPageURL = decodeURIComponent(window.location.search.substring(1)), sURLVariables = sPageURL.split('&'), sParameterName, i;

		for (i = 0; i < sURLVariables.length; i++) {
			sParameterName = sURLVariables[i].split('=');

			if (sParameterName[0] === sParam) {
				return sParameterName[1] === undefined ? true : decodeURIComponent(sParameterName[1]);
			}
		}
	}

$(document).on("click", "#btnenviar", function () { // Botón para enviar una nueva observación
    var tick_id = getUrlParameter("ID");
    var editor = $("#tickd_descrip");

    if (!tick_id) {
		    swal("Error", "No se encontró el ID del ticket", "error");
        return;
    }

    if (editor.summernote("isEmpty")) {
        swal("Atención", "Ingrese una observación", "warning");
        return;
    }

    var boton = $(this);
    boton.prop("disabled", true);

    $.ajax({
        url: "../../controller/ticket.php?op=insertdetalle",
        type: "POST",
        dataType: "json",
        data: {
            tick_id: tick_id,
            tickd_descrip: editor.summernote("code")
        },

        success: function (datos) {
            if (datos.success === true) {
                editor.summernote("reset");

                $.post(
                    "../../controller/ticket.php?op=listardetalle",
                    { tick_id: tick_id },
                    function (html) {
                        $("#lbldetalle").html(html);
                    }
                );

                swal("Correcto", "Nueva Observación registrada", "success");
            } else {
                swal(
                    "Error",
                    datos.message || "No se pudo registrar la observación",
                    "error"
                );
            }
        },

        error: function (xhr) {
            console.error("Error al registrar:", xhr.responseText);
            swal("Error", "No se pudo registrar la observación", "error");
        },

        complete: function () {
            boton.prop("disabled", false);
        }
    });
});
	
$(document).on("click", "#btncerrarticket", function () { // Botón para cerrar el ticket
    var tick_id = getUrlParameter("ID");
    var boton = $(this);

    if (!tick_id) {
        swal("Error", "No se encontró el ID del ticket", "error");
        return;
    }

    swal({
        title: "DDN-Ticket",
        text: "¿Está seguro de cerrar el ticket?",
        type: "warning",
        showCancelButton: true,
        confirmButtonClass: "btn-warning",
        confirmButtonText: "Sí, cerrar",
        cancelButtonText: "No",
        closeOnConfirm: false,
        closeOnCancel: true,
        showLoaderOnConfirm: true
    }, function (isConfirm) {
        if (!isConfirm) {
            return;
        }

        boton.prop("disabled", true);

        $.ajax({
            url: "../../controller/ticket.php?op=cerrar",
            type: "POST",
            dataType: "json",
            data: { tick_id: tick_id },

            success: function (datos) {
                if (datos.success === true) {
                    $("#lblestado").html(
                        '<span class="label label-pill label-danger">Cerrado</span>'
                    );

                    $("#pnlcomentario").hide();

                    $.post(
                        "../../controller/ticket.php?op=listardetalle",
                        { tick_id: tick_id },
                        function (html) {
                            $("#lbldetalle").html(html);
                        }
                    );

                    swal("DDN-Ticket", "Ticket cerrado correctamente", "success");
                } else {
                    swal(
                        "Error",
                        datos.message || "No se pudo cerrar el ticket",
                        "error"
                    );
                }
            },

            error: function (xhr) {
                console.error("Error al cerrar:", xhr.responseText);
                var respuesta = xhr.responseJSON;
                swal(
                    "Error",
                    respuesta && respuesta.message
                        ? respuesta.message
                        : "No se pudo cerrar el ticket",
                    "error"
                );
            },

            complete: function () {
                boton.prop("disabled", false);
            }
        });
    });
});

init();
