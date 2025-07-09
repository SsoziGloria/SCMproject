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


## System Architecture

The Chocolate SCM system consists of multiple components:

- **Laravel Application** (Port 8000): Main web application with user interface
- **Java Vendor Validation API** (Port 8081): Microservice for validating vendor applications
- **MySQL Database**: Shared database for both Laravel and Java applications
- **Python ML Services**: Machine learning components for demand prediction


### Java Vendor Validation API Requirements
-  Java 17 or higher
-  Maven 3.6+
-  MySQL 8.0+
-  Git

### System Requirements
-  At least 4GB RAM
-  2GB free disk space
-  Network access for package downloads

Open the ⁠.env file in a text editor and update the database configuration:
```bash
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=chocolate_scm
DB_USERNAME=root
DB_PASSWORD=
```
 Add the Java API configuration:
```bash
VENDOR_API_URL=http://localhost:8081
```
Use the appropriate credentials for your configuration

### Prerequisites for Java API

1. **Install Java 17+**
   ```bash
   # Ubuntu/Debian
   sudo apt update
   sudo apt install openjdk-17-jdk
   
   # macOS (using Homebrew)
   brew install openjdk@17
   
   # Windows
   # Download from https://adoptium.net/
   ```

2. **Install Maven**
   ```bash
   # Ubuntu/Debian
   sudo apt install maven
   
   # macOS
   brew install maven
   
   # Windows
   # Download from https://maven.apache.org/download.cgi
   ```

3. **Verify Installation**
   ```bash
   java --version
   mvn --version
   ```

### Database Setup for Java API

1. **Create MySQL Database**
   ```bash
   mysql -u root -p
   CREATE DATABASE chocolate_scm;
   EXIT;
   ```

2. **Configure Database Connection**
   The Java API uses the same database as Laravel. Ensure your MySQL server is running and accessible.

### Java API Installation

1. **Navigate to Java API Directory**
   ```bash
   cd vendor-validation
   ```

2. **Build the Application**
   ```bash
   mvn clean compile
   ```

3. **Run the Application**
   ```bash
   mvn spring-boot:run
   ```

   The API will start on `http://localhost:8081`

### Java API Configuration

The Java API configuration is in `vendor-validation/src/main/resources/application.properties`:

```properties
# Server Configuration
server.port=8081
spring.application.name=ChocolateSCM Vendor Validation

# Database Configuration
spring.datasource.url=jdbc:mysql://localhost:3306/chocolate_scm
spring.datasource.username=root
spring.datasource.password=
spring.datasource.driver-class-name=com.mysql.cj.jdbc.Driver

# JPA Configuration
spring.jpa.hibernate.ddl-auto=create-drop
spring.jpa.show-sql=true
spring.jpa.database-platform=org.hibernate.dialect.MySQLDialect

# Security (Disabled for development)
spring.autoconfigure.exclude=org.springframework.boot.autoconfigure.security.servlet.SecurityAutoConfiguration,org.springframework.boot.actuator.autoconfigure.security.servlet.ManagementWebSecurityAutoConfiguration

# File Upload Configuration
spring.servlet.multipart.max-file-size=5MB
spring.servlet.multipart.max-request-size=5MB
```

### Java API Endpoints

The Java API provides the following endpoints:

1. **Test Connection**
   ```bash
   GET http://localhost:8081/api/vendors/test-resource
   ```
   Returns a sample vendor object for testing connectivity.

2. **Validate Vendor File**
   ```bash
   POST http://localhost:8081/api/vendors/validate
   Content-Type: multipart/form-data
   Body: file (PDF or TXT)
   ```
   Validates uploaded vendor application files and returns "APPROVED" or "REJECTED".

### Testing the Java API

1. **Test API Connection**
   ```bash
   curl http://localhost:8081/api/vendors/test-resource
   ```

2. **Test File Validation**
   ```bash
   curl -F "file=@vendor-validation/src/main/resources/valid_vendor.txt" \
        http://localhost:8081/api/vendors/validate
   ```

### Integration with Laravel

The Laravel application communicates with the Java API through the `VendorValidationService`:

1. **Service Location**: `app/Services/VendorValidationService.php`
2. **Controller**: `app/Http/Controllers/VendorController.php`
3. **Routes**: 
   - `GET /vendor/validate` - Vendor validation form
   - `POST /vendor/validate` - Submit vendor file for validation
   - `GET /vendor/test-api` - Test API connection

### Troubleshooting Java API

1. **Port Already in Use**
   ```bash
   # Check what's using port 8081
   lsof -i :8081
   
   # Kill the process or change port in application.properties
   ```

2. **Database Connection Issues**
   - Ensure MySQL is running
   - Verify database credentials
   - Check if `chocolate_scm` database exists

3. **Maven Build Issues**
   ```bash
   # Clean and rebuild
   mvn clean install
   
   # Update dependencies
   mvn dependency:resolve
   ```

4. **Java Version Issues**
   ```bash
   # Ensure Java 17 is being used
   java --version
   
   # Set JAVA_HOME if needed
   export JAVA_HOME=/path/to/java17
   ```

### Development Workflow

1. **Start Both Services**
   ```bash
   # Terminal 1 - Laravel
   php artisan serve
   
   # Terminal 2 - Java API
   cd vendor-validation
   mvn spring-boot:run
   ```

2. **Test Integration**
   - Visit `http://localhost:8000/vendor/validate`
   - Upload a vendor file
   - Check validation results

3. **Monitor Logs**
   - Laravel logs: `storage/logs/laravel.log`
   - Java API logs: Check terminal output

### Production Deployment

For production deployment:

1. **Java API**
   ```bash
   # Build JAR file
   mvn clean package
   
   # Run JAR
   java -jar target/vendor-validation-0.0.1-SNAPSHOT.jar
   ```

2. **Environment Variables**
   - Set proper database credentials
   - Configure security settings
   - Set appropriate file upload limits

3. **Security Considerations**
   - Enable Spring Security
   - Configure CORS properly
   - Use HTTPS in production
   - Implement proper authentication

## Complete System Startup

To run the entire Chocolate SCM system:

### 1. Start Database
```bash
# Start MySQL (if not already running)
sudo systemctl start mysql
# or
sudo service mysql start
```

### 2. Start Laravel Application
```bash
# In project root directory
php artisan serve --host=127.0.0.1 --port=8000
```

### 3. Start Java Vendor Validation API
```bash
# In a new terminal
cd vendor-validation
mvn spring-boot:run
```

### 4. Verify All Services
- **Laravel App**: http://localhost:8000
- **Java API**: http://localhost:8081/api/vendors/test-resource
- **Vendor Validation**: http://localhost:8000/vendor/validate

## Testing the Complete System

### 1. Test Laravel Application
```bash
# Test Laravel is running
curl http://localhost:8000
```

### 2. Test Java API
```bash
# Test Java API connection
curl http://localhost:8081/api/vendors/test-resource
```

### 3. Test Integration
```bash
# Test Laravel → Java API communication
curl -H "Accept: application/json" http://localhost:8000/vendor/test-api
```

### 4. Test File Validation
```bash
# Test file upload through Laravel
curl -F "vendor_file=@vendor-validation/src/main/resources/valid_vendor.txt" \
     http://localhost:8000/vendor/validate
```

## API Documentation

### Laravel Endpoints
- `GET /vendor/validate` - Vendor validation form
- `POST /vendor/validate` - Submit vendor file for validation
- `GET /vendor/test-api` - Test Java API connection

### Java API Endpoints
- `GET /api/vendors/test-resource` - Get sample vendor data
- `POST /api/vendors/validate` - Validate vendor file

## File Formats Supported

The vendor validation system supports:
- **Text files (.txt)**: Plain text vendor applications
- **PDF files (.pdf)**: PDF vendor applications
- **Maximum file size**: 5MB

## Sample Vendor File

A sample vendor file is included at `vendor-validation/src/main/resources/valid_vendor.txt` for testing purposes.

## Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Test both Laravel and Java components
5. Submit a pull request

## Support

For issues related to:
- **Laravel Application**: Check Laravel logs at `storage/logs/laravel.log`
- **Java API**: Check terminal output when running the Java application
- **Database**: Verify MySQL connection and database existence
- **Integration**: Test API endpoints individually first
