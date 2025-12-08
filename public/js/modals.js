(function($) {
    'use strict';

    const Modals = {
        init: function() {
            $(document).on('click', '.close-btn', () => {
                $('.modal').removeClass('show');
                this.restoreBodyScroll();
            });

            $(document).on('click', '.modal-overlay', (e) => {
                if (e.target === e.currentTarget) {
                    $(e.target).closest('.modal').removeClass('show');
                    this.restoreBodyScroll();
                }
            });
        },

        restoreBodyScroll: function() {
            if ($('.modal.show').length === 0) {
                $('body').css('overflow', '');
                $('body').css('position', '');
                $('body').css('width', '');
            }
        },

        open: function(modalId) {
            $(modalId).addClass('show');
            // Prevent body scroll on mobile
            $('body').css('overflow', 'hidden');
            $('body').css('position', 'fixed');
            $('body').css('width', '100%');
        },

        close: function(modalId) {
            $(modalId).removeClass('show');
            this.restoreBodyScroll();
        }
    };

    window.Modals = Modals;

})(jQuery);


