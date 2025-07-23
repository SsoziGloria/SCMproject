# Chocolate Supply Chain Management System

A comprehensive supply chain management platform designed specifically for the chocolate industry, managing the complete journey from raw cocoa beans to finished products on store shelves.

## Table of Contents

- [Description](#description)
- [Features](#features)
- [Installation](#installation)
- [Configuration](#configuration)
- [Usage](#usage)
- [Machine Learning](#machine-learning)
- [API Documentation](#api-documentation)
- [Contributing](#contributing)
- [License](#license)

## Description

The Chocolate Supply Chain Management System is a full-stack web application built with Laravel that streamlines operations across the entire chocolate supply chain. The platform supports multiple user roles including suppliers, retailers, administrators, and end customers, providing specialized dashboards and functionality for each stakeholder.

This system integrates advanced features such as:
- **Real-time inventory management** with automatic stock level monitoring
- **Vendor verification** using Java-based PDF document validation
- **Machine learning-powered demand prediction** and customer segmentation
- **Multi-channel sales tracking** across online and physical stores
- **Automated order fulfillment** with inventory adjustments
- **Real-time chat communication** between supply chain partners

This project was developed as part of a collaborative 7-week development process to demonstrate comprehensive supply chain management capabilities in the chocolate industry.

## Features

### 🏪 **Multi-Role Management**
- **Suppliers**: Product catalog management, inventory tracking, order fulfillment
- **Retailers**: Product browsing, order placement, inventory monitoring
- **Administrators**: System oversight, vendor validation, analytics dashboard
- **Customers**: Product browsing, order placement, review system

### 📦 **Inventory Management**
- Real-time stock level tracking with low stock alerts
- Automatic inventory adjustments on order fulfillment
- Batch tracking with expiration date monitoring
- Supplier-specific inventory controls with admin toggle functionality
- Historical inventory movement tracking with detailed audit trails

### 🛒 **Order Management**
- Complete order lifecycle management (pending → processing → shipped → delivered)
- Partial shipment support for complex fulfillment scenarios
- Automatic inventory reduction on order status changes
- Sales channel tracking (online store, retail shop, marketplace)
- Order history with comprehensive status timeline

### 🔍 **Vendor Validation System**
- Automated PDF document validation using Java microservice
- Manual vendor verification workflow with admin controls
- Certification status tracking and management
- Document storage and retrieval system
- Vendor application history and revalidation capabilities

### 🤖 **Machine Learning Capabilities**
- **Demand Prediction**: LSTM-based quarterly demand forecasting
- **Customer Segmentation**: K-means clustering for personalized recommendations
- **Seasonal Analysis**: Christmas and holiday demand pattern recognition
- **Analytics Dashboard**: Chart.js visualizations of predictions and trends

### 💬 **Communication System**
- Real-time chat between supply chain partners using WireChat
- System notifications for important events
- Order status updates and automated alerts
- Admin messaging system for vendor communications

### 📊 **Analytics & Reporting**
- Revenue trends and growth metrics
- Inventory movement patterns and forecasting
- Top-performing products analysis
- User engagement and role-based statistics
- Export capabilities for external analysis using Maatwebsite Excel

## Installation

### Prerequisites

- **PHP** >= 8.2
- **Composer** >= 2.0
- **Node.js** >= 16.0
- **MySQL** >= 8.0
- **Java** >= 11 (for vendor validation service)
- **Python** >= 3.8 (for machine learning features)

### Step-by-Step Installation

1. **Clone the Repository**
   ```bash
   git clone https://github.com/SsoziGloria/SCMproject.git
   cd SCMproject
   ```

2. **Install PHP Dependencies**
   ```bash
   composer install
   ```

3. **Install Node.js Dependencies**
   ```bash
   npm install
   ```

4. **Environment Configuration**
   ```bash
   cp .env.example .env
   ```

5. **Generate Application Key**
   ```bash
   php artisan key:generate
   ```

6. **Database Setup**
   ```bash
   # Create database
   mysql -u root -p -e "CREATE DATABASE chocolate_scm;"
   
   # Run migrations
   php artisan migrate
   
   # Seed initial data
   php artisan db:seed
   ```

7. **Storage Setup**
   ```bash
   php artisan storage:link
   ```

8. **Build Assets**
   ```bash
   npm run build
   ```

9. **Set Up Machine Learning Environment**
   ```bash
   cd ml
   pip install -r requirements.txt
   cp config.yaml.example config.yaml
   # Configure database connection in config.yaml
   cd ..
   ```

10. **Start Development Servers**
    ```bash
    # Start all services concurrently
    composer run dev
    
    # Or start services individually:
    # Laravel server
    php artisan serve
    
    # Queue worker
    php artisan queue:work
    
    # Asset watcher
    npm run dev
    ```

## Configuration

### Environment Variables

Configure the following in your `.env` file:

```env
# Application
APP_NAME="Chocolate SCM"
APP_ENV=local
APP_KEY=base64:your-key-here
APP_DEBUG=true
APP_URL=http://localhost:8000
APP_TIMEZONE=Africa/Nairobi

# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=chocolate_scm
DB_USERNAME=root
DB_PASSWORD=

# Mail Configuration
MAIL_MAILER=smtp
MAIL_HOST=mailpit
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="hello@example.com"
MAIL_FROM_NAME="${APP_NAME}"

# Pusher (for real-time features)
PUSHER_APP_ID=your-app-id
PUSHER_APP_KEY=your-app-key
PUSHER_APP_SECRET=your-app-secret
PUSHER_HOST=
PUSHER_PORT=443
PUSHER_SCHEME=https
PUSHER_APP_CLUSTER=mt1

# Vendor Validation Service
VENDOR_VALIDATION_URL=http://localhost:8080
VENDOR_VALIDATION_STORAGE_DISK=vendor_docs
```

### Additional Configuration Files

1. **Machine Learning Configuration** (`ml/config.yaml`)
   ```yaml
   database:
     host: localhost
     port: 3306
     database: chocolate_scm
     username: root
     password: ""
   
   models:
     demand_prediction:
       epochs: 50
       batch_size: 32
     customer_segmentation:
       n_clusters: 5
   ```

2. **Java Service Configuration** (vendor-validation service)
   - Configure database connection in `application.properties`
   - Set up file storage paths for document validation

## Usage

### Getting Started

1. **Access the Application**
   - Navigate to `http://localhost:8000` in your browser
   - Register a new account or use seeded credentials

2. **Role-Based Access**
   - **Admin**: Full system access, vendor validation, analytics
   - **Supplier**: Product management, inventory tracking, order fulfillment
   - **Retailer**: Product browsing, order placement, inventory monitoring
   - **Customer**: Product browsing, order placement, reviews

### Key Workflows

#### For Suppliers
1. **Vendor Verification**: Complete verification process with document upload
2. **Product Management**: Add products, manage inventory, set pricing
3. **Order Fulfillment**: Process orders, update shipping status
4. **Inventory Monitoring**: Track stock levels, receive low stock alerts

#### For Retailers
1. **Product Browsing**: Search and filter products by category, supplier
2. **Order Placement**: Add products to cart, complete checkout process
3. **Order Tracking**: Monitor order status, view shipping updates
4. **Inventory Management**: Track received products, manage local stock

#### For Administrators
1. **Vendor Validation**: Review and approve vendor applications
2. **System Monitoring**: View analytics, manage users, system settings
3. **Inventory Oversight**: Monitor global inventory levels, adjustments
4. **Supplier Controls**: Toggle supplier product visibility system-wide

## Machine Learning

The system includes advanced ML capabilities for supply chain optimization:

### Demand Prediction
- **Model**: LSTM neural network built with TensorFlow
- **Purpose**: Quarterly demand forecasting with seasonal adjustments
- **Features**: Historical sales data, seasonal patterns, external factors
- **Output**: Demand predictions stored in database, visualized on dashboard

### Customer Segmentation
- **Model**: K-means clustering using scikit-learn
- **Purpose**: Customer behavior analysis and personalized recommendations
- **Features**: Purchase history, product preferences, buying patterns
- **Output**: Customer segments for targeted marketing and product recommendations

### Running ML Scripts
```bash
cd ml

# Run demand prediction
python src/demand_prediction/predict.py

# Run customer segmentation
python src/customer_segmentation/segment.py
```

## API Documentation

The system provides REST APIs for various functionalities:

### Vendor Validation API
```
POST /api/vendor-validation/validate
GET  /api/vendor-validation/vendor/{id}/history
POST /api/vendor-validation/validate-existing/{vendorId}
POST /api/vendor-validation/validate-existing-document/{vendor}
```

### Analytics API
```
GET /api/admin/analytics/revenue-data
GET /api/admin/analytics/order-status-data
```

### Health Check
```
GET /api/service-health/vendor-validation
```

## Contributing

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add some amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

### Development Guidelines
- Follow PSR-12 coding standards for PHP
- Use meaningful commit messages
- Add tests for new features
- Update documentation for API changes
- Maintain consistency with existing code patterns

## License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

---

## Technology Stack

- **Backend**: Laravel 12.x (PHP 8.2+)
- **Frontend**: Blade templates with Bootstrap, Chart.js, TailwindCSS
- **Database**: MySQL 8.0+
- **Real-time**: Pusher WebSockets, Laravel Echo
- **Machine Learning**: Python (TensorFlow, scikit-learn)
- **Document Processing**: Java Spring Boot microservice
- **File Storage**: Laravel Storage with multiple disk support
- **Image Processing**: Intervention Image
- **Export/Import**: Maatwebsite Excel
- **Communication**: WireChat for real-time messaging

## Support

For support and questions:
- Create an issue in the GitHub repository
- Contact the development team
- Check the documentation in the `/docs` directory

## Acknowledgments

- Laravel Framework for the robust backend foundation
- TensorFlow and scikit-learn for machine learning capabilities
- Chart.js for data visualization
- Bootstrap and TailwindCSS for responsive UI components
- All contributors who helped build this comprehensive supply chain solution
