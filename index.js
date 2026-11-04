function init(){
}

$(document).on("click", "#btnsoporte", function (e) {
    e.preventDefault();

    if ($('#rol_id').val() == 1) {
        $("#lbltitulo").html("Soporte TI");
        $("#btnsoporte").html("Acceso Usuario").css("color", "");
        $('#rol_id').val(2);
        $('#imgtipo').attr("src", "public/img/DDN-logo-2-mob.png");
    } else {
        $("#lbltitulo").html("Acceso Usuario");
        $("#btnsoporte").html("Soporte TI").css("color", "red");
        $('#rol_id').val(1);
        $('#imgtipo').attr("src", "public/img/DDN-logo-1-mob.png");
    }
});

init();