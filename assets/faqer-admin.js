jQuery(document).ready(function($) {
    // Detecta clic en celdas editables
    $(document).on('click', '.editable-priority', function() {
        const cell = $(this);
        const id = cell.data('id');
        const originalValue = cell.text().trim();
        
        // Crea input temporal
        const input = $('<input type="number" class="priority-input" required>')
            .val(originalValue)
            .css({
                'width': '60px',
                'text-align': 'center'
            });
        
        cell.html(input);
        input.focus();
        
        // Guarda al presionar Enter o perder foco
        input.on('keypress blur', function(e) {
            if (e.type === 'keypress' && e.which !== 13) return;
            
            let newValue = parseInt(input.val(), 10);
            
            if (isNaN(newValue)) {
                // Si no es un número válido, volver al valor original
                cell.text(originalValue);
                return; // No enviar la actualización al servidor
            }
            
            cell.text(newValue);

            // Envía datos a WordPress
            $.post(ajaxurl, {
                action: 'actualizar_prioridad_faq',
                id: id,
                prioridad: newValue,
                security: faqVars.nonce
            });
        });
    });
});