(function($) {
    'use strict';

    const Theme = {
        init: function() {
            this.loadTheme();
            this.bindEvents();
        },

        loadTheme: function() {
            const savedTheme = localStorage.getItem('eventeny_theme') || 'light';
            this.setTheme(savedTheme);
        },

        setTheme: function(theme) {
            if (theme === 'dark') {
                document.documentElement.setAttribute('data-theme', 'dark');
                $('.theme-icon').text('☀️');
                localStorage.setItem('eventeny_theme', 'dark');
            } else {
                document.documentElement.removeAttribute('data-theme');
                $('.theme-icon').text('🌙');
                localStorage.setItem('eventeny_theme', 'light');
            }
        },

        toggle: function() {
            const currentTheme = document.documentElement.getAttribute('data-theme');
            const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
            this.setTheme(newTheme);
        },

        bindEvents: function() {
            $('#theme-toggle').on('click', () => {
                this.toggle();
            });
        }
    };

    window.Theme = Theme;

    $(document).ready(() => {
        Theme.init();
    });

})(jQuery);

