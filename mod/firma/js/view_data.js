$(".submit-data").on('click', function() {
    var empresa = $("#empresa").val();
    var dni = $("#dni").val();
    var id = $("#cmId").val();

    window.location.href ='view.php?id='+id+'&emp='+empresa+'&dni='+dni;
});