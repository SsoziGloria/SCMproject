## About this project

Chocolate Supply Chain Management System

This project is a class assignment to design and build a system that manages the full supply chain of chocolate — from raw cocoa beans to finished products on store shelves.

The system helps different users track inventory, place and manage orders, chat across the chain, and generate useful reports. It also uses machine learning to:
- Predict future demand for chocolate.
- Group customers by their buying behavior for better service.

The system is built using Laravel (for the backend and interface), MySQL (for the database), Python (for machine learning), and Java (for vendor validation through uploaded PDF applications).

This GitHub repository supports our 7-week collaborative development process and will track team progress and contributions.

## Prerequisites

Before you begin, ensure you have the following:

-  PHP >= 8.0
-  Composer
-  A web server
-  A database server

## Installation

Follow these steps to set up the project on your local machine:

### Clone the Repository

Clone the repository using Git:

```bash
git clone https://github.com/SsoziGloria/SCMproject.git
```

Change into the project directory:
```bash
cd scmproject
```

### Install Composer Dependencies
Run the following command to install the project dependencies:
```bash
composer install
```

### Set Up Environment Configuration
1.	Copy the ⁠.env.example File:
```bash
cp .env.example .env
```
2. Open the ⁠.env file in a text editor and update the database configuration:
```bash
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=chocolate_scm
DB_USERNAME=root
DB_PASSWORD=
```
Use the appropriate credentials for your configuration.

### Generate Application Key
Run the following command to generate the application key:
```bash
php artisan key:generate
```

### Run Migrations
Execute the migrations to set up the database schema:
```bash
php artisan migrate
```

### Start the Development Server
Run the following command to start the Laravel development server:
```bash
php artisan serve
```
You can now access the application in your web browser at ⁠http://localhost:8000
