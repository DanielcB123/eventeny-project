# Eventeny Project

A dynamic ticketing platform built with vanilla PHP, HTML, CSS, and JavaScript/jQuery.

## Features

### Event Organizers
- Create tickets with title, sale dates, quantity, price, visibility, and optional image
- View all tickets in a list
- Edit existing tickets
- Delete tickets

### Ticket Buyers
- Browse available public tickets
- Select quantities and add tickets to cart
- View cart in a modal (no page reload)
- Remove items from cart
- Proceed to checkout review
- Complete checkout (payment processing skipped)

## Setup Instructions

### 1. Database Setup

Create the database manually:

1. Connect to your MySQL server (port 3307):
   ```bash
   mysql -u root -P 3307 -h 127.0.0.1
   ```

2. Create the database and import the schema:
   ```sql
   source database/schema.sql
   ```
   
   Or run the SQL commands from `database/schema.sql` directly in your MySQL client.

### 2. Environment Configuration

Create a `.env` file in the project root (copy from `.env.example` if it exists):

```env
# Database Configuration
DB_HOST=127.0.0.1
DB_PORT=3307
DB_NAME=eventeny
DB_USERNAME=root
DB_PASSWORD=

# Application Configuration
APP_DEBUG=false
APP_TIMEZONE=UTC
```

The application will automatically load these environment variables. If `.env` is not found, it will use default values:
- Host: `127.0.0.1`
- Port: `3307`
- Database: `eventeny`
- Username: `root`
- Password: (empty)

### 3. Install Dependencies

```bash
composer install
```

### 4. Web Server Setup

#### Using PHP Built-in Server

```bash
php -S localhost:8000 -t public
```

#### Using Apache/Nginx

Point your web server document root to the `public` directory.

### 5. Access the Application

Open your browser and navigate to:
- `http://localhost:8000` (if using PHP built-in server)

## API Endpoints

### Tickets

- `GET /api/tickets` - Get all tickets
- `GET /api/tickets?public=true` - Get public tickets only
- `GET /api/tickets/{id}` - Get single ticket
- `POST /api/tickets` - Create ticket
- `PUT /api/tickets/{id}` - Update ticket
- `DELETE /api/tickets/{id}` - Delete ticket

## Technologies Used

- **Backend**: PHP 7.4+
- **Frontend**: HTML5, CSS3, JavaScript (ES6+)
- **Libraries**: jQuery 3.7.1
- **Database**: MySQL/MariaDB
- **Package Manager**: Composer

## Features Implemented

✅ Full CRUD operations for tickets
✅ Dynamic ticket browsing
✅ Shopping cart with localStorage
✅ Modal-based cart interface
✅ No page reloads (AJAX-based)
✅ Responsive design
✅ Image upload support (frontend ready)

