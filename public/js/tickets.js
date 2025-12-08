(function($) {
    'use strict';

    const Tickets = {
        load: function() {
            const publicOnly = !App.isOrganizerView;
            const endpoint = `/tickets${publicOnly ? '?public=true' : ''}`;
            const targetId = App.isOrganizerView ? '#organizer-tickets' : '#buyer-tickets';

            API.get(endpoint, (response) => {
                if (response.success) {
                    this.render(response.data, targetId);
                }
            });
        },

        render: function(tickets, containerId) {
            const $container = $(containerId);
            $container.empty();

            if (tickets.length === 0) {
                $container.html('<p class="text-center">No tickets available</p>');
                return;
            }

            tickets.forEach(ticket => {
                const $card = this.createCard(ticket);
                $container.append($card);
            });
        },

        createCard: function(ticket) {
            const imageHtml = ticket.image_path 
                ? `<img src="${ticket.image_path}" alt="${ticket.title}" class="ticket-image">`
                : `<div class="ticket-image"></div>`;

            const saleStart = new Date(ticket.sale_start_date).toLocaleDateString();
            const saleEnd = new Date(ticket.sale_end_date).toLocaleDateString();
            const isAvailable = new Date() >= new Date(ticket.sale_start_date) && 
                              new Date() <= new Date(ticket.sale_end_date) &&
                              ticket.quantity > 0;

            if (App.isOrganizerView) {
                return this.createOrganizerCard(ticket, imageHtml, saleStart, saleEnd);
            } else {
                return this.createBuyerCard(ticket, imageHtml, saleStart, saleEnd, isAvailable);
            }
        },

        createOrganizerCard: function(ticket, imageHtml, saleStart, saleEnd) {
            return $(`
                <div class="ticket-card" data-ticket-id="${ticket.id}">
                    ${imageHtml}
                    <div class="ticket-body">
                        <h3 class="ticket-title">${Utils.escapeHtml(ticket.title)}</h3>
                        <div class="ticket-price">$${parseFloat(ticket.price).toFixed(2)}</div>
                        <div class="ticket-info">
                            <div>Quantity: ${ticket.quantity}</div>
                            <div>Sale: ${saleStart} - ${saleEnd}</div>
                            <div>Status: ${ticket.is_public ? 'Public' : 'Private'}</div>
                        </div>
                        <div class="ticket-actions">
                            <button class="btn btn-primary edit-ticket-btn" data-id="${ticket.id}">Edit</button>
                            <button class="btn btn-danger delete-ticket-btn" data-id="${ticket.id}">Delete</button>
                        </div>
                    </div>
                </div>
            `);
        },

        createBuyerCard: function(ticket, imageHtml, saleStart, saleEnd, isAvailable) {
            return $(`
                <div class="ticket-card" data-ticket-id="${ticket.id}">
                    ${imageHtml}
                    <div class="ticket-body">
                        <h3 class="ticket-title">${Utils.escapeHtml(ticket.title)}</h3>
                        <div class="ticket-price">$${parseFloat(ticket.price).toFixed(2)}</div>
                        <div class="ticket-info">
                            <div>Available: ${ticket.quantity}</div>
                            <div>Sale: ${saleStart} - ${saleEnd}</div>
                        </div>
                        ${isAvailable ? `
                            <div class="ticket-quantity">
                                <label>Quantity:</label>
                                <input type="number" class="ticket-qty-input" min="1" max="${ticket.quantity}" value="1" data-ticket-id="${ticket.id}">
                            </div>
                            <button class="btn btn-primary add-to-cart-btn" data-id="${ticket.id}">Add to Cart</button>
                        ` : '<p class="text-center" style="color: var(--text-secondary);">Not available</p>'}
                    </div>
                </div>
            `);
        },

        openForm: function(ticket = null) {
            $('#ticket-form')[0].reset();
            $('#ticket-image-preview').empty();
            $('#ticket-id').val('');

            if (ticket) {
                $('#ticket-form-title').text('Edit Ticket');
                $('#ticket-id').val(ticket.id);
                $('#ticket-title').val(ticket.title);
                $('#ticket-sale-start').val(Utils.formatDateTimeLocal(ticket.sale_start_date));
                $('#ticket-sale-end').val(Utils.formatDateTimeLocal(ticket.sale_end_date));
                $('#ticket-quantity').val(ticket.quantity);
                $('#ticket-price').val(ticket.price);
                $('#ticket-is-public').prop('checked', ticket.is_public == 1);
                
                if (ticket.image_path) {
                    $('#ticket-image-preview').html(`<img src="${ticket.image_path}" alt="Preview">`);
                }
            } else {
                $('#ticket-form-title').text('Create Ticket');
            }

            Modals.open('#ticket-form-modal');
        },

        edit: function(id) {
            API.get(`/tickets/${id}`, (response) => {
                if (response.success) {
                    this.openForm(response.data);
                }
            });
        },

        handleSubmit: function() {
            const ticketId = $('#ticket-id').val();
            const endpoint = ticketId ? `/tickets/${ticketId}` : '/tickets';
            const method = ticketId ? 'put' : 'post';
            
            const fileInput = $('#ticket-image')[0];
            const hasFile = fileInput && fileInput.files && fileInput.files.length > 0;
            
            if (hasFile) {
                const formData = new FormData();
                formData.append('title', $('#ticket-title').val());
                formData.append('sale_start_date', $('#ticket-sale-start').val());
                formData.append('sale_end_date', $('#ticket-sale-end').val());
                formData.append('quantity', $('#ticket-quantity').val());
                formData.append('price', $('#ticket-price').val());
                formData.append('is_public', $('#ticket-is-public').is(':checked') ? 1 : 0);
                formData.append('image', fileInput.files[0]);
                
                API.upload(endpoint, formData, method, (response) => {
                    if (response.success) {
                        Utils.showMessage(response.message, 'success');
                        Modals.close('#ticket-form-modal');
                        this.load();
                    }
                });
            } else {
                const formData = {
                    title: $('#ticket-title').val(),
                    sale_start_date: $('#ticket-sale-start').val(),
                    sale_end_date: $('#ticket-sale-end').val(),
                    quantity: parseInt($('#ticket-quantity').val()),
                    price: parseFloat($('#ticket-price').val()),
                    is_public: $('#ticket-is-public').is(':checked') ? 1 : 0
                };

                API[method](endpoint, formData, (response) => {
                    if (response.success) {
                        Utils.showMessage(response.message, 'success');
                        Modals.close('#ticket-form-modal');
                        this.load();
                    }
                });
            }
        },

        delete: function(id) {
            Utils.confirm('Are you sure you want to delete this ticket?', 'Delete Ticket', (confirmed) => {
                if (confirmed) {
                    API.delete(`/tickets/${id}`, (response) => {
                        if (response.success) {
                            Utils.showMessage('Ticket deleted successfully', 'success');
                            this.load();
                        }
                    });
                }
            });
        }
    };

    $(document).on('click', '.edit-ticket-btn', function() {
        Tickets.edit($(this).data('id'));
    });

    $(document).on('click', '.delete-ticket-btn', function() {
        Tickets.delete($(this).data('id'));
    });

    $(document).on('click', '.add-to-cart-btn', function() {
        const id = $(this).data('id');
        const $card = $(this).closest('.ticket-card');
        const quantity = parseInt($card.find('.ticket-qty-input').val()) || 1;
        Cart.add(id, quantity);
    });

    $(document).on('click', '#create-ticket-btn', function() {
        Tickets.openForm();
    });

    $('#ticket-form').on('submit', function(e) {
        e.preventDefault();
        Tickets.handleSubmit();
    });

    $('#ticket-image').on('change', function(e) {
        if (e.target.files && e.target.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                $('#ticket-image-preview').html(`<img src="${e.target.result}" alt="Preview">`);
            };
            reader.readAsDataURL(e.target.files[0]);
        }
    });

    window.Tickets = Tickets;

})(jQuery);


