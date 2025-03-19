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

    function cerrarPregunta(pregunta, ocultarHijas = true) {
        const id = pregunta.find('.faq-question').data('id');
        pregunta.find('.faq-answer').hide();
        pregunta.data('estado', 'cerrado');

        // Eliminar formulario asociado a esta pregunta
        $('.formulario-base[data-padre-form="' + id + '"]').remove();

        if (ocultarHijas) {
            pregunta.nextAll('.faq-item[data-padre="' + id + '"]').each(function () {
                cerrarPregunta($(this), true);
                $(this).hide();
            });
        }
    }

    function cerrarPreguntasMismoNivel(preguntaActual) {
        const padreId = preguntaActual.attr('data-padre');
        $('.faq-item[data-padre="' + padreId + '"]').each(function () {
            if (!$(this).is(preguntaActual)) {
                cerrarPregunta($(this), true);
            }
        });
    }

    function eliminarTodosLosFormularios() {
        $('.formulario-base').remove();
    }

    $('.faq-list').on('click', '.faq-question', function () {
        const pregunta = $(this).closest('.faq-item');
        const id = $(this).data('id');
        const respuesta = pregunta.find('.faq-answer');
        const estado = pregunta.data('estado');

        // Eliminar todos los formularios antes de cualquier acción
        eliminarTodosLosFormularios();

        if (estado === 'abierto') {
            cerrarPregunta(pregunta, true);
        } else {
            cerrarPreguntasMismoNivel(pregunta);
            respuesta.show();
            pregunta.data('estado', 'abierto');
            pregunta.nextAll('.faq-item[data-padre="' + id + '"]').show();
            cargarPreguntasHijas(id, pregunta);
        }
    });

    $('.faq-list').on('submit', '.fqr-form', function(e) {
        e.preventDefault();
        var form = $(this);
        var formData = form.serialize();

        $.ajax({
            url: ajax_object.ajax_url,
            type: 'POST',
            data: formData,
            success: function(response) {
                if (response.success) {
                    form.html('<p style="color: green;">' + response.message + '</p>');
                } else {
                    form.prepend('<p style="color: red;">' + response.message + '</p>');
                }
            },
            error: function() {
                form.prepend('<p style="color: red;">Hubo un error al enviar el formulario. Por favor, intenta de nuevo.</p>');
            }
        });
    });
});