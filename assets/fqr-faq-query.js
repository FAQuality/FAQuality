jQuery(document).ready(function ($) {
    function eliminarTodosLosFormularios() {
        $('.formulario-base').stop(true, true).slideUp(400, function() {
            $(this).remove();
        });
    }

    function cerrarPreguntasMismoNivel(preguntaActual) {
        const padreId = preguntaActual.attr('data-padre');
        $('.faq-item[data-padre="' + padreId + '"]').not(preguntaActual).each(function () {
            cerrarPregunta($(this), true);
        });
    }

    function abrirPregunta(pregunta) {
        const respuesta = pregunta.find('.faq-answer');
        pregunta.data('estado', 'abierto');
        respuesta.stop(true, true).slideDown(300);
        
        const id = pregunta.find('.faq-question').data('id');
        pregunta.nextAll('.faq-item[data-padre="' + id + '"]').slideDown(300);
    }

    function cerrarPregunta(pregunta, ocultarHijas = true) {
        const id = pregunta.find('.faq-question').data('id');
        const respuesta = pregunta.find('.faq-answer');
        pregunta.data('estado', 'cerrado');
        respuesta.stop(true, true).slideUp(300);

        $('.formulario-base[data-padre-form="' + id + '"]').stop(true, true).slideUp(400, function() {
            $(this).remove();
        });

        if (ocultarHijas) {
            pregunta.nextAll('.faq-item[data-padre="' + id + '"]').each(function () {
                cerrarPregunta($(this), true);
                $(this).slideUp(300);
            });
        }
    }

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
                    mostrarPreguntasHijas(id_padre);
                }
            });
        } else {
            mostrarPreguntasHijas(id_padre);
        }
    }

    function mostrarPreguntasHijas(id_padre) {
        $('.faq-item[data-padre="' + id_padre + '"]')
            .attr('data-estado', 'cerrado')
            .hide()
            .slideDown(300);
        
        $('.formulario-base[data-padre-form="' + id_padre + '"]')
            .hide()
            .slideDown(400); // Duración aumentada para el formulario
    }

    $('.faq-list').on('click', '.faq-question', function () {
        const pregunta = $(this).closest('.faq-item');
        const id = $(this).data('id');
        const estado = pregunta.data('estado');
        
        console.log('Estado actual:', estado); // Para depuración

        eliminarTodosLosFormularios();

        if (estado === 'abierto') {
            cerrarPregunta(pregunta, true);
        } else {
            cerrarPreguntasMismoNivel(pregunta);
            abrirPregunta(pregunta);
            cargarPreguntasHijas(id, pregunta);
        }
    });
});