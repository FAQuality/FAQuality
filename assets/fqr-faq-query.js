jQuery(document).ready(function($) {
    // Función para cargar y mostrar preguntas hijas
    function cargarPreguntasHijas(id_padre, elemento_padre) {
        if (elemento_padre.next('.faq-item[data-padre="' + id_padre + '"]').length === 0) {
            $.ajax({
                url: ajax_object.ajax_url,
                type: 'POST',
                data: {
                    action: 'fqr_cargar_hijas',
                    id_padre: id_padre
                },
                success: function(response) {
                    elemento_padre.after(response);
                    // Agregar atributo data-estado a las preguntas hijas
                    elemento_padre.nextAll('.faq-item[data-padre="' + id_padre + '"]').each(function() {
                        $(this).attr('data-estado', 'cerrado');
                    });
                }
            });
        }
    }

    // Función recursiva para cerrar preguntas descendientes
    function cerrarPreguntasDescendientes(id_padre) {
        $('.faq-item[data-padre="' + id_padre + '"]').each(function() {
            var id = $(this).find('.faq-question').data('id');
            $(this).find('.faq-answer').hide();
            $(this).data('estado', 'cerrado');
            cerrarPreguntasDescendientes(id);
            $(this).remove();
        });
    }

    // Delegación de eventos para preguntas
    $('.faq-list').on('click', '.faq-question', function() {
        // Seleccionar correctamente el elemento faq-item
        var pregunta = $(this).closest('.faq-item');
        var id = $(this).data('id');
        var respuesta = pregunta.find('.faq-answer');
        var estado = pregunta.data('estado');

        if (estado === 'abierto') {
            respuesta.hide();
            pregunta.data('estado', 'cerrado');
            cerrarPreguntasDescendientes(id);
            // Eliminar el formulario si la pregunta no tiene hijos
            if (pregunta.nextAll('.faq-item[data-padre="' + id + '"]').length === 0) {
                $('.formulario-base').remove();
            }
        } else {
            respuesta.show();
            pregunta.data('estado', 'abierto');
            cargarPreguntasHijas(id, pregunta);
        }
    });
});