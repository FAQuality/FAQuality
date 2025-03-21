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

    $('.faq-container').on('submit', '.fqr-form', function (e) {
        e.preventDefault(); // Evitar envío tradicional del formulario
        const $form = $(this);
        const $responseContainer = $form.closest('.formulario-base');

        // Recopilar datos del formulario
        const formData = $form.serialize();

        // Enviar datos mediante AJAX
        $.ajax({
            url: ajax_object.ajax_url, // URL de admin-ajax.php
            type: 'POST',
            data: formData,
            beforeSend: function () {
                $form.find('button[type="submit"]').prop('disabled', true); // Deshabilitar botón mientras se envía
            },
            success: function (response) {
                if (response.success) {
                    // Mostrar mensaje de éxito
                    $responseContainer.html(
                        `<div class="fqr-alert success">${response.message}</div>`
                    );
                } else {
                    // Mostrar mensaje de error
                    $responseContainer.prepend(
                        `<div class="fqr-alert error">${response.message}</div>`
                    );
                }
                // Recargar CAPTCHA en caso de error
                $('#captcha-img').attr('src', $('#captcha-img').attr('src') + '?' + Math.random());
            },
            complete: function () {
                $form.find('button[type="submit"]').prop('disabled', false); // Habilitar botón nuevamente
            },
            error: function () {
                $responseContainer.prepend(
                    `<div class="fqr-alert error">Ocurrió un error inesperado. Intenta nuevamente.</div>`
                );
            }
        });
    });
});