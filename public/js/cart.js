(function($) {
    'use strict';

    const Cart = {
        items: [],

        load: function() {
            const saved = localStorage.getItem('eventeny_cart');
            if (saved) {
                this.items = JSON.parse(saved);
            }
            this.updateCount();
        },

        save: function() {
            localStorage.setItem('eventeny_cart', JSON.stringify(this.items));
            this.updateCount();
        },

        updateCount: function() {
            const count = this.items.reduce((sum, item) => sum + item.quantity, 0);
            $('#cart-count').text(count);
        },

        add: function(ticketId, quantity) {
            API.get(`/tickets/${ticketId}`, (response) => {
                if (response.success) {
                    const ticket = response.data;
                    const existingItem = this.items.find(item => item.id === ticketId);

                    if (existingItem) {
                        existingItem.quantity += quantity;
                    } else {
                        this.items.push({
                            id: ticket.id,
                            title: ticket.title,
                            price: parseFloat(ticket.price),
                            quantity: quantity,
                            image_path: ticket.image_path
                        });
                    }

                    this.save();
                    this.updateUI();
                    Utils.showMessage('Ticket added to cart', 'success');
                }
            });
        },

        remove: function(ticketId) {
            this.items = this.items.filter(item => item.id !== ticketId);
            this.save();
            this.updateUI();
        },

        updateQuantity: function(ticketId, quantity) {
            const item = this.items.find(item => item.id === ticketId);
            if (item) {
                if (quantity <= 0) {
                    this.remove(ticketId);
                } else {
                    item.quantity = quantity;
                    this.save();
                    this.updateUI();
                }
            }
        },

        updateUI: function() {
            const $cartItems = $('#cart-items');
            const $cartEmpty = $('#cart-empty');
            const $cartFooter = $('#cart-footer');

            if (this.items.length === 0) {
                $cartItems.hide();
                $cartEmpty.show();
                $cartFooter.hide();
                return;
            }

            $cartItems.show();
            $cartEmpty.hide();
            $cartFooter.show();

            $cartItems.empty();
            let total = 0;

            this.items.forEach(item => {
                const itemTotal = item.price * item.quantity;
                total += itemTotal;

                const imageHtml = item.image_path 
                    ? `<img src="${item.image_path}" alt="${item.title}" class="cart-item-image">`
                    : `<div class="cart-item-image"></div>`;

                const $item = $(`
                    <div class="cart-item" data-ticket-id="${item.id}">
                        ${imageHtml}
                        <div class="cart-item-details">
                            <div class="cart-item-title">${Utils.escapeHtml(item.title)}</div>
                            <div class="cart-item-price">$${item.price.toFixed(2)} each</div>
                            <div class="cart-item-actions">
                                <div class="cart-item-quantity">
                                    <label>Qty:</label>
                                    <input type="number" class="cart-qty-input" min="1" value="${item.quantity}" data-ticket-id="${item.id}">
                                </div>
                                <button class="btn btn-danger btn-sm remove-from-cart-btn" data-ticket-id="${item.id}">Remove</button>
                            </div>
                        </div>
                    </div>
                `);
                $cartItems.append($item);
            });

            $('#cart-total').text(total.toFixed(2));
        },

        open: function() {
            this.updateUI();
            Modals.open('#cart-modal');
        },

        openReview: function() {
            const $reviewItems = $('#review-items');
            $reviewItems.empty();

            let total = 0;

            this.items.forEach(item => {
                const itemTotal = item.price * item.quantity;
                total += itemTotal;

                const $item = $(`
                    <div class="review-item">
                        <div class="cart-item-title">${Utils.escapeHtml(item.title)}</div>
                        <div class="cart-item-price">
                            ${item.quantity} x $${item.price.toFixed(2)} = $${itemTotal.toFixed(2)}
                        </div>
                    </div>
                `);
                $reviewItems.append($item);
            });

            $('#review-total').text(total.toFixed(2));
            Modals.close('#cart-modal');
            Modals.open('#review-modal');
        },

        completeCheckout: function() {
            this.items = [];
            this.save();
            Modals.close('#review-modal');
            Utils.showMessage('Checkout completed! (Payment processing skipped)', 'success');
        }
    };

    $(document).on('change', '.cart-qty-input', function() {
        const ticketId = $(this).data('ticket-id');
        const quantity = parseInt($(this).val()) || 1;
        Cart.updateQuantity(ticketId, quantity);
    });

    $(document).on('click', '.remove-from-cart-btn', function() {
        const ticketId = $(this).data('ticket-id');
        Cart.remove(ticketId);
    });

    $('#proceed-to-review-btn').on('click', function() {
        Cart.openReview();
    });

    $('#back-to-cart-btn').on('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        Modals.close('#review-modal');
        Cart.open();
    });

    $('#complete-checkout-btn').on('click', function() {
        Cart.completeCheckout();
    });

    window.Cart = Cart;

})(jQuery);


