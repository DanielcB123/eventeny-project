/**
 * Main JavaScript File
 * Eventeny Project
 * Uses jQuery for DOM manipulation
 */

(function($) {
    'use strict';

    /**
     * Main Application Object
     */
    const App = {
        /**
         * Initialize the application
         */
        init: function() {
            this.bindEvents();
            this.initComponents();
            console.log('Eventeny App initialized');
        },

        /**
         * Bind event handlers
         */
        bindEvents: function() {
            // Document ready
            $(document).ready(function() {
                App.onDocumentReady();
            });

            // Window load
            $(window).on('load', function() {
                App.onWindowLoad();
            });

            $(document).on('submit', 'form', function(e) {
                App.handleFormSubmit(e, $(this));
            });
        },

        /**
         * Initialize components
         */
        initComponents: function() {
            this.initModals();
        },

        /**
         * Document ready handler
         */
        onDocumentReady: function() {
            this.updateServerInfo();
        },

        /**
         * Window load handler
         */
        onWindowLoad: function() {
            $('body').addClass('loaded');
        },

        /**
         * Update server information display
         */
        updateServerInfo: function() {
            const phpVersion = $('#php-version').text() || 'N/A';
            const serverInfo = $('#server-info').text() || 'N/A';
            
            console.log('PHP Version:', phpVersion);
            console.log('Server Info:', serverInfo);
        },

        /**
         * Handle form submissions
         */
        handleFormSubmit: function(e, $form) {
            const $submitBtn = $form.find('button[type="submit"]');
            if ($submitBtn.length) {
                $submitBtn.prop('disabled', true).addClass('loading');
            }
        },

        /**
         * Show message to user
         */
        showMessage: function(message, type) {
            type = type || 'info';
            const $messageBox = $('<div>')
                .addClass('info-box')
                .addClass(type)
                .text(message)
                .hide()
                .fadeIn();

            // Remove existing messages
            $('.info-box').remove();
            
            // Insert message
            $('.container-sm, .container').first().prepend($messageBox);

            // Auto-hide after 5 seconds
            setTimeout(function() {
                $messageBox.fadeOut(function() {
                    $(this).remove();
                });
            }, 5000);
        },

        /**
         * Initialize modals
         */
        initModals: function() {
            $(document).on('click', '[data-modal]', function(e) {
                e.preventDefault();
                const modalId = $(this).data('modal');
                $('#' + modalId).fadeIn();
            });

            $(document).on('click', '.modal-close', function() {
                $(this).closest('.modal').fadeOut();
            });
        },

        /**
         * Utility: Make AJAX request
         */
        ajax: function(url, method, data, successCallback, errorCallback) {
            $.ajax({
                url: url,
                method: method || 'GET',
                data: data || {},
                dataType: 'json',
                success: successCallback || function() {},
                error: errorCallback || function(xhr, status, error) {
                    console.error('AJAX Error:', error);
                    App.showMessage('An error occurred. Please try again.', 'danger');
                }
            });
        },

        /**
         * Utility: Format date
         */
        formatDate: function(date) {
            if (!date) return '';
            const d = new Date(date);
            return d.toLocaleDateString('en-US', {
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            });
        },

    };

    // Initialize app when jQuery is ready
    $(document).ready(function() {
        App.init();
    });

    // Expose App globally
    window.App = App;

})(jQuery);
