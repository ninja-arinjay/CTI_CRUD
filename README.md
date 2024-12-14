# CRUD Application

## Overview

This CRUD application is a PHP-based project designed to manage products. It supports user authentication, session management, and various operations on a `products` database table. The application uses **PhpSpreadsheet** for importing products from Excel files.

---

## Features

1. **Authentication**: 
   - Login functionality to restrict unauthorized access.
   - Session management to ensure only authenticated users can access certain pages.

2. **Product Management**:
   - **Create**: Add new products via `create.php`.
   - **Read**: View product details using `read.php`.
   - **Update**: Edit existing product details via `update.php`.
   - **Delete**: Remove products using `delete.php`.

3. **Search Products**:
   - Search by product name or category on `home.php`.

4. **Import Products**:
   - Upload Excel files (`.xls`, `.xlsx`) to bulk import products using `import.php`.
   - Utilizes the **PhpSpreadsheet** library for processing Excel files.

5. **Error Handling**:
   - Redirects to `error.php` for invalid requests.

6. **Session Management**:
   - Logout functionality via `logout.php` to end sessions securely.

---

## File Structure

### Main Files
- **`index.php`**: Starting point for user login.
- **`home.php`**: Displays all products and provides search functionality.
- **`create.php`**: Page to add new products.
- **`read.php`**: View details of a specific product.
- **`update.php`**: Edit existing product details.
- **`delete.php`**: Remove a product.
- **`import.php`**: Import products from an Excel file.

### Helper Files
- **`error.php`**: Displays an error message for invalid requests.
- **`helper.php`**: Contains reusable functions.
- **`config.php`**: Stores database configuration and encryption keys.
- **`logout.php`**: Ends the user session and redirects to the login page.

---

## Database Schema

The application uses a single table, `products`, with the following schema:

| Column Name      | Data Type   | Attributes   |
|-------------------|-------------|--------------|
| `productname`     | `VARCHAR`   | -            |
| `product_category`| `VARCHAR`   | -            |
| `product_desc`    | `VARCHAR`   | -            |
| `serialno`        | `INT`       | `UNIQUE`     |

---

## Setup Instructions

1. **Prerequisites**:
   - A web server with PHP 7.4 or higher.
   - MySQL database.
   - Composer (for PhpSpreadsheet installation).

2. **Clone the Repository**:
   ```bash
   git clone <repository-url>
   cd <repository-folder>

3. **Install Dependencies:** Run the following command to install PhpSpreadsheet:
    composer install

4. **Configure Database:** Open config.php and update the database connection details:
    $host = "your_host";
    $db = "your_database_name";
    $user = "your_username";
    $password = "your_password";

5. **Create the Database Table:** Run the following SQL query to create the products table:
    CREATE TABLE products (
        productname VARCHAR(255),
        product_category VARCHAR(255),
        product_desc VARCHAR(255),
        serialno INT UNIQUE
    );

6. **Run the Application:**
    - Place the application folder in your web server’s root directory.
    - Open the browser and navigate to http://localhost/<application-folder>/index.php.


## Usage

### Login
- Start at `index.php` and log in using your credentials.

### View Products
- After logging in, `home.php` displays a list of products.
- Use the search bar to filter products by name or category.

### Manage Products
- **Add**: Click "Add New Product" to go to `create.php`.
- **View**: Click "View" next to a product to open `read.php`.
- **Edit**: Click "Edit" next to a product to open `update.php`.
- **Delete**: Click "Delete" next to a product to remove it.

### Import Products
- Go to `import.php` and upload an Excel file to bulk import products.

### Logout
- Click the "Logout" button to end your session.

---

## Error Handling
- If a user attempts to access a restricted page without logging in, they will be redirected to `error.php`.
- Invalid file uploads or database errors during import operations are logged, and the application continues processing other valid entries.

---

## Dependencies
- **PhpSpreadsheet**: Used for processing Excel files in `import.php`.
- **Bootstrap 4**: For styling the application.
- **PHP**: Core language for the backend.
- **MySQL**: Database to store product details.





