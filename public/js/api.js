(function($) {
    'use strict';

    const API = {
        baseUrl: '/api',

        request: function(url, method, data, successCallback) {
            const settings = {
                url: url,
                method: method || 'GET',
                dataType: 'json',
                success: successCallback || function() {},
                error: function(xhr, status, error) {
                    let message = 'An error occurred. Please try again.';
                    try {
                        const response = JSON.parse(xhr.responseText);
                        message = response.message || message;
                    } catch (e) {}
                    Utils.showMessage(message, 'danger');
                }
            };

            if (method !== 'GET' && data) {
                settings.contentType = 'application/json';
                settings.data = data;
            }

            $.ajax(settings);
        },

        get: function(endpoint, successCallback) {
            return this.request(this.baseUrl + endpoint, 'GET', null, successCallback);
        },

        post: function(endpoint, data, successCallback) {
            return this.request(this.baseUrl + endpoint, 'POST', JSON.stringify(data), successCallback);
        },

        put: function(endpoint, data, successCallback) {
            return this.request(this.baseUrl + endpoint, 'PUT', JSON.stringify(data), successCallback);
        },

        delete: function(endpoint, successCallback) {
            return this.request(this.baseUrl + endpoint, 'DELETE', null, successCallback);
        },

        upload: function(endpoint, formData, method, successCallback) {
            const settings = {
                url: this.baseUrl + endpoint,
                method: method || 'POST',
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'json',
                success: successCallback || function() {},
                error: function(xhr, status, error) {
                    let message = 'An error occurred. Please try again.';
                    try {
                        const response = JSON.parse(xhr.responseText);
                        message = response.message || message;
                    } catch (e) {}
                    Utils.showMessage(message, 'danger');
                }
            };

            $.ajax(settings);
        }
    };

    window.API = API;

})(jQuery);


