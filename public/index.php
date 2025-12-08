<?php
header('Content-Type: text/html; charset=UTF-8');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Ticket Platform">
    <title>Eventeny - Ticket Platform</title>
    
    <link rel="stylesheet" href="/css/main.css">
    <link rel="preconnect" href="https://code.jquery.com">
    <script>
        (function() {
            const savedTheme = localStorage.getItem('eventeny_theme') || 'light';
            if (savedTheme === 'dark') {
                document.documentElement.setAttribute('data-theme', 'dark');
            }
        })();
    </script>
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="container">
            <div class="header-content">
                <div class="header-left">
                    <h1 class="header-logo">TicketMe</h1>
                    <nav class="header-nav">
                        <button class="nav-link" id="toggle-view-btn">Organizer View</button>
                    </nav>
                </div>
                <div class="header-right">
                    <button class="btn btn-signin theme-toggle" id="theme-toggle" aria-label="Toggle theme" title="Toggle dark mode">
                        <span class="theme-icon">🌙</span>
                    </button>
                    <button class="btn btn-show-cart" id="cart-btn">
                        Cart (<span id="cart-count">0</span>)
                    </button>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="main-content">
        <div class="container">
            <!-- Organizer Section (Hidden by default) -->
            <section id="organizer-section" class="section" style="display: none;">
                <div class="section-header">
                    <h2>Event Organizer Dashboard</h2>
                    <button class="btn btn-primary" id="create-ticket-btn">Create New Ticket</button>
                </div>
                <div id="organizer-tickets" class="tickets-grid">
                    <!-- Tickets will be loaded here -->
                </div>
            </section>

            <!-- Buyer Section (Default view) -->
            <section id="buyer-section" class="section">
                <div class="section-header">
                    <h2>Available Tickets</h2>
                </div>
                <div id="buyer-tickets" class="tickets-grid">
                    <!-- Tickets will be loaded here -->
                </div>
            </section>
        </div>
    </main>

    <!-- Cart Modal -->
    <div id="cart-modal" class="modal">
        <div class="modal-overlay"></div>
        <div class="modal-content">
            <div class="modal-header">
                <h2>Shopping Cart</h2>
                <button class="modal-close close-btn" aria-label="Close modal">&times;</button>
            </div>
            <div class="modal-body">
                <div id="cart-items">
                    <!-- Cart items will be loaded here -->
                </div>
                <div id="cart-empty" class="cart-empty">
                    <p>Your cart is empty</p>
                </div>
            </div>
            <div class="modal-footer" id="cart-footer" style="display: none;">
                <div class="cart-summary">
                    <div class="cart-total">
                        <strong>Total: $<span id="cart-total">0.00</span></strong>
                    </div>
                </div>
                <div class="cart-actions">
                    <button class="btn btn-secondary close-btn">Continue Shopping</button>
                    <button class="btn btn-primary" id="proceed-to-review-btn">Proceed to Review</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Review/Checkout Modal -->
    <div id="review-modal" class="modal">
        <div class="modal-overlay"></div>
        <div class="modal-content">
            <div class="modal-header">
                <h2>Review Your Order</h2>
                <button class="modal-close close-btn" aria-label="Close modal">&times;</button>
            </div>
            <div class="modal-body">
                <div id="review-items">
                    <!-- Review items will be loaded here -->
                </div>
                <div class="review-summary">
                    <div class="review-total">
                        <strong>Total: $<span id="review-total">0.00</span></strong>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" id="back-to-cart-btn">Back to Cart</button>
                <button class="btn btn-primary" id="complete-checkout-btn">Complete Checkout</button>
            </div>
        </div>
    </div>

    <!-- Confirm Modal -->
    <div id="confirm-modal" class="modal">
        <div class="modal-overlay"></div>
        <div class="modal-content">
            <div class="modal-header">
                <h2 id="confirm-title">Confirm Action</h2>
                <button class="modal-close close-btn" aria-label="Close modal">&times;</button>
            </div>
            <div class="modal-body">
                <p id="confirm-message">Are you sure you want to proceed?</p>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary close-btn" id="confirm-cancel">Cancel</button>
                <button class="btn btn-danger" id="confirm-ok">Confirm</button>
            </div>
        </div>
    </div>

    <!-- Ticket Form Modal (Create/Edit) -->
    <div id="ticket-form-modal" class="modal">
        <div class="modal-overlay"></div>
        <div class="modal-content">
            <div class="modal-header">
                <h2 id="ticket-form-title">Create Ticket</h2>
                <button class="modal-close close-btn" aria-label="Close modal">&times;</button>
            </div>

            <div class="modal-body">
                <form id="ticket-form">
                    <input type="hidden" id="ticket-id" name="id">

                    <div class="form-group">
                        <label for="ticket-title" class="form-label">Title *</label>
                        <input type="text" id="ticket-title" name="title" class="form-control" required>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="ticket-sale-start" class="form-label">Sale Start Date *</label>
                            <input type="datetime-local" id="ticket-sale-start" name="sale_start_date" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="ticket-sale-end" class="form-label">Sale End Date *</label>
                            <input type="datetime-local" id="ticket-sale-end" name="sale_end_date" class="form-control" required>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="ticket-quantity" class="form-label">Quantity *</label>
                            <input type="number" id="ticket-quantity" name="quantity" class="form-control" min="0" required>
                        </div>
                        <div class="form-group">
                            <label for="ticket-price" class="form-label">Price ($) *</label>
                            <input type="number" id="ticket-price" name="price" class="form-control" min="0" step="0.01" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-checkbox">
                            <input type="checkbox" id="ticket-is-public" name="is_public" value="1" checked>
                            <span>Public (visible to buyers)</span>
                        </label>
                    </div>

                    <div class="form-group">
                        <label for="ticket-image" class="form-label">Image (optional)</label>
                        <input type="file" id="ticket-image" name="image" class="form-control" accept="image/*">
                        <div id="ticket-image-preview" class="image-preview"></div>
                    </div>
                </form>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary close-btn">Cancel</button>
                <button type="submit" form="ticket-form" class="btn btn-primary">Save Ticket</button>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js" 
            integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" 
            crossorigin="anonymous"></script>
    
    <script>
        window.jQuery || document.write('<script src="/js/vendor/jquery-3.7.1.min.js"><\/script>');
    </script>
    <script src="/js/utils.js"></script>
    <script src="/js/api.js"></script>
    <script src="/js/theme.js"></script>
    <script src="/js/modals.js"></script>
    <script src="/js/cart.js"></script>
    <script src="/js/tickets.js"></script>
    <script src="/js/app.js"></script>
</body>
</html>
