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

                     // Verificar si la respuesta incluyó el formulario
                    if ($(response).filter('.formulario-base').length === 0 && $(response).hasClass('formulario-base')) {
                        elemento_padre.after(formulario_base(id_padre));
                    }
                }
            });
        }
    }

    // Función recursiva para cerrar preguntas descendientes
    function cerrarPreguntasDescendientes(id_padre) {
        $('.faq-item[data-padre="' + id_padre + '"]').each(function() {
            const id = $(this).find('.faq-question').data('id');
            
            // Eliminar formulario asociado si existe
            const formulario = $(this).nextAll('.formulario-base[data-padre-form="' + id + '"]');
            if(formulario.length > 0) formulario.remove();
            
            // Cerrar y eliminar hijos
            $(this).find('.faq-answer').hide().data('estado', 'cerrado');
            cerrarPreguntasDescendientes(id);
            $(this).remove();
        });
    }

     // Delegación de eventos modificada
     $('.faq-list').on('click', '.faq-question', function() {
        const pregunta = $(this).closest('.faq-item');
        const id = $(this).data('id');
        const respuesta = pregunta.find('.faq-answer');
        const estado = pregunta.data('estado');

        if (estado === 'abierto') {
            respuesta.hide();
            pregunta.data('estado', 'cerrado');
            cerrarPreguntasDescendientes(id);
            
            // Verificación precisa de hijos después de cerrar
            const tieneHijos = pregunta.nextAll('.faq-item[data-padre="' + id + '"]').length > 0;
            pregunta.nextAll('.formulario-base[data-padre-form="' + id + '"]').remove();
            
            if (!tieneHijos) {
                // Eliminar específicamente el formulario asociado a esta pregunta
                const formulario = pregunta.nextAll('.formulario-base[data-padre-form="' + id + '"]');
                if (formulario.length > 0) { // ← Uso de .length para comprobar existencia[1][6]
                    formulario.remove();
                }
            }
        } else {
            respuesta.show();
            pregunta.data('estado', 'abierto');
            cargarPreguntasHijas(id, pregunta);
        }
    });
});