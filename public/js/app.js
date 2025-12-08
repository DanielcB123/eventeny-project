(function($) {
    'use strict';

    const App = {
        isOrganizerView: false,

        init: function() {
            this.bindEvents();
            this.initComponents();
            Tickets.load();
            Cart.load();
        },

        bindEvents: function() {
            $('#toggle-view-btn').on('click', () => {
                this.toggleView();
            });

            $('#cart-btn').on('click', () => {
                Cart.open();
            });
        },

        initComponents: function() {
            Modals.init();
        },

        toggleView: function() {
            this.isOrganizerView = !this.isOrganizerView;
            
            if (this.isOrganizerView) {
                $('#buyer-section').hide();
                $('#organizer-section').show();
                $('#toggle-view-btn').text('Buyer View');
            } else {
                $('#organizer-section').hide();
                $('#buyer-section').show();
                $('#toggle-view-btn').text('Organizer View');
            }
            
            Tickets.load();
        }
    };

    window.App = App;

    $(document).ready(() => {
        App.init();
    });

})(jQuery);


