jQuery(document).ready(function ($) {
    function cargarPreguntasHijas(id_padre, elemento_padre) {
        if (elemento_padre.next('.faq-item[data-padre="' + id_padre + '"]').length === 0) {
            $.ajax({
                url: ajax_object.ajax_url,
                type: 'POST',
                data: {
                    action: 'fqr_cargar_hijas',
                    id_padre: id_padre
                },
                success: function (response) {
                    elemento_padre.after(response);
                    elemento_padre.nextAll('.faq-item[data-padre="' + id_padre + '"]').attr('data-estado', 'cerrado');
                }
            });
        }
    }

    function cerrarPregunta(pregunta, ocultarRespuesta = true) {
        const id = pregunta.find('.faq-question').data('id');

        if (ocultarRespuesta) {
            pregunta.find('.faq-answer').hide(); // Oculta la respuesta de la pregunta actual
        }

        pregunta.data('estado', 'cerrado'); // Marca como cerrada

        // Eliminar formulario asociado a esta pregunta
        const formulario = pregunta.nextAll('.formulario-base[data-padre-form="' + id + '"]');
        if (formulario.length > 0) {
            formulario.remove();
        }

        // Ocultar y cerrar recursivamente las preguntas hijas
        pregunta.nextAll('.faq-item[data-padre="' + id + '"]').each(function () {
            cerrarPregunta($(this)); // Cierra las hijas recursivamente
            $(this).hide(); // Oculta las hijas
        });
    }

    function cerrarPreguntasMismoNivel(preguntaActual) {
        const padreId = preguntaActual.attr('data-padre'); // ID del padre de la pregunta actual
        $('.faq-item[data-padre="' + padreId + '"]').each(function () {
            if (!$(this).is(preguntaActual)) {
                cerrarPregunta($(this)); // Cierra hermanas y sus descendientes
            }
        });
    }

    $('.faq-list').on('click', '.faq-question', function () {
        const pregunta = $(this).closest('.faq-item'); // La pregunta actual
        const id = $(this).data('id'); // ID de la pregunta actual
        const respuesta = pregunta.find('.faq-answer'); // Respuesta de la pregunta actual
        const estado = pregunta.data('estado'); // Estado actual ("abierto" o "cerrado")

        if (estado === 'abierto') {
            // Si está abierta, cierra esta pregunta y sus descendientes pero deja visibles las hijas
            cerrarPregunta(pregunta, true);
        } else {
            // Si está cerrada, cierra otras del mismo nivel y abre esta
            cerrarPreguntasMismoNivel(pregunta);

            respuesta.show(); // Muestra la respuesta de la pregunta actual
            pregunta.data('estado', 'abierto'); // Marca como abierta

            // Muestra las hijas directas pero no carga nuevas si ya están visibles
            pregunta.nextAll('.faq-item[data-padre="' + id + '"]').show();

            cargarPreguntasHijas(id, pregunta); // Carga las preguntas hijas si es necesario
        }
    });
});