(function($) {
    'use strict';

    const Utils = {
        showMessage: function(message, type) {
            type = type || 'info';
            const $messageBox = $('<div>')
                .addClass('info-box')
                .addClass(type)
                .text(message)
                .hide()
                .fadeIn();

            $('.info-box').remove();
            $('.container').first().prepend($messageBox);

            setTimeout(() => {
                $messageBox.fadeOut(function() {
                    $(this).remove();
                });
            }, 5000);
        },

        formatDateTimeLocal: function(dateString) {
            const date = new Date(dateString);
            const year = date.getFullYear();
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const day = String(date.getDate()).padStart(2, '0');
            const hours = String(date.getHours()).padStart(2, '0');
            const minutes = String(date.getMinutes()).padStart(2, '0');
            return `${year}-${month}-${day}T${hours}:${minutes}`;
        },

        escapeHtml: function(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        },

        confirm: function(message, title, callback) {
            title = title || 'Confirm Action';
            $('#confirm-title').text(title);
            $('#confirm-message').text(message);
            
            Modals.open('#confirm-modal');
            
            $('#confirm-ok').off('click').on('click', function() {
                Modals.close('#confirm-modal');
                if (callback) {
                    callback(true);
                }
            });
            
            $('#confirm-cancel, #confirm-modal .close-btn').off('click').on('click', function() {
                Modals.close('#confirm-modal');
                if (callback) {
                    callback(false);
                }
            });
        }
    };

    window.Utils = Utils;

})(jQuery);

