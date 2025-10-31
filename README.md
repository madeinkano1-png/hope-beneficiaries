# HoPE Beneficiaries and Tranche Payment Management System

A secure, enterprise-grade web application built with Core PHP and MySQL to manage household beneficiaries and track up to three payment tranches per household.

## 🎯 Features

### Core Functionality
- **CSV Import/Export**: Validate headers, types, formats, deduplicate by 'nidhh', preview changes, transactional commit
- **CRUD Operations**: Complete beneficiary management with audit trail
- **Tranche Management**: Track up to 3 payment tranches per household with detailed recipient information
- **Search & Filter**: Advanced filtering by State, LGA, Ward, Community, TrancheStatus, date ranges
- **Analytics Dashboard**: KPIs, demographic charts, disbursement tracking
- **Role-Based Access**: Admin and StandardUser roles with granular permissions

### Security & Compliance
- **Authentication**: Secure login with session management
- **Authorization**: Role-based access control (RBAC)
- **Audit Trail**: Complete tracking of all changes (created_by, updated_by, timestamps)
- **Data Validation**: Comprehensive validation rules for all data fields
- **SQL Injection Protection**: Prepared statements and parameterized queries

### User Experience
- **Responsive Design**: Bootstrap-based UI that works on all devices
- **Accessibility**: WCAG compliant with keyboard navigation and screen reader support
- **Pagination**: Efficient handling of large datasets
- **Export Capabilities**: CSV/Excel export of filtered results
- **Real-time Feedback**: Progress indicators and error reporting

## 🛠 Technology Stack

- **Backend**: Core PHP 7.4+
- **Database**: MySQL 8.0+
- **Frontend**: Bootstrap 5.3, HTML5, CSS3, JavaScript
- **Icons**: Bootstrap Icons
- **Architecture**: MVC-like pattern with clean separation of concerns

## 📋 Requirements

- PHP 7.4 or higher
- MySQL 8.0 or higher
- Web server (Apache/Nginx)
- 50MB+ disk space for file uploads
- Modern web browser

## 🚀 Installation

### 1. Clone the Repository
```bash
git clone https://github.com/your-org/hope-beneficiaries.git
cd hope-beneficiaries
```

### 2. Configure Database
Edit `config/database.php` with your database credentials:
```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'hope_beneficiaries');
define('DB_USER', 'your_username');
define('DB_PASS', 'your_password');
```

### 3. Run Database Migration
```bash
php migrate.php
```

### 4. Set Permissions
```bash
chmod 755 uploads/
chmod 644 config/*.php
```

### 5. Configure Web Server
Point your web server document root to the project directory and ensure URL rewriting is enabled.

#### Apache (.htaccess)
```apache
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ index.php [QSA,L]
```

## 🔐 Default Credentials

After running the migration, use these credentials to log in:

- **Username**: `admin`
- **Password**: `admin123`
- **Email**: `admin@hope.gov.ng`

⚠️ **Important**: Change the default password immediately after first login.

## 📊 Database Schema

### Main Tables

#### `beneficiaries`
- **Primary Key**: `nidhh` (VARCHAR(50))
- **Location Fields**: State, LGA, Ward, Community
- **Household Info**: HouseHoldNo, HAddress
- **Status Tracking**: TrancheStatus, TotalAmount
- **Tranche Details**: 3 sets of recipient information (name, account, bank, payment date, phone, gender, age, ID type)
- **Audit Fields**: created_at, updated_at, created_by, updated_by

#### `users`
- User authentication and role management
- Supports Admin and StandardUser roles
- Password reset functionality

#### `audit_logs`
- Complete audit trail of all changes
- JSON storage of old and new values
- User and IP tracking

#### `csv_import_sessions`
- Track CSV import operations
- Error reporting and status tracking

## 📁 Project Structure

```
hope-beneficiaries/
├── config/                 # Configuration files
│   ├── app.php             # Application settings
│   └── database.php        # Database configuration
├── src/                    # Source code
│   ├── Controllers/        # Request handlers
│   ├── Models/            # Data models
│   ├── Services/          # Business logic
│   ├── Validators/        # Data validation
│   ├── Middleware/        # Authentication & authorization
│   └── autoload.php       # Class autoloader
├── views/                 # Templates and views
│   ├── layouts/           # Layout templates
│   ├── components/        # Reusable components
│   ├── auth/             # Authentication views
│   ├── beneficiaries/    # Beneficiary management
│   ├── dashboard/        # Dashboard views
│   └── import/           # CSV import interface
├── assets/               # Static assets
│   ├── css/             # Stylesheets
│   └── js/              # JavaScript files
├── database/            # Database files
│   ├── schema.sql       # Complete database schema
│   └── migrations/      # Migration files
├── uploads/             # File upload directory
├── sample_data/         # Sample CSV files
├── tests/              # Test files
└── docs/               # Documentation
```

## 🔧 Configuration

### Application Settings (`config/app.php`)
- File upload limits
- Pagination settings
- Validation rules
- User roles and permissions
- Error messages

### Database Settings (`config/database.php`)
- Connection parameters
- PDO options
- Database helper methods

## 📝 Usage

### CSV Import Format
The system expects CSV files with the following header structure:
```csv
State,LGA,Ward,Community,nidhh,HouseHoldNo,HAddress,TrancheStatus,TotalAmount,FirstTrancheRecipient,FirstTrancheAccountNumber,FirstTrancheBankName,FirstTranchePaymentDate,FirstTranchePhone,FirstTrancheGender,FirstTrancheAge,FirstTrancheIDType,SecondTrancheRecipient,SecondTrancheAccountNumber,SecondTrancheBankName,SecondTranchePaymentDate,SecondTranchePhone,SecondTrancheGender,SecondTrancheAge,SecondTrancheIDType,ThirdTrancheRecipient,ThirdTrancheAccountNumber,ThirdTrancheBankName,ThirdTranchePaymentDate,ThirdTranchePhone,ThirdTrancheGender,ThirdTrancheAge,ThirdTrancheIDType
```

### Validation Rules
- **nidhh**: Required, unique, max 50 characters
- **Phone**: E.164 format preferred, 7-15 digits
- **Gender**: Male, Female, Other, Unknown
- **Age**: 0-120 years
- **TrancheStatus**: NotStarted, Partial, Completed
- **PaymentDate**: ISO 8601 date format (YYYY-MM-DD)

### User Roles

#### Admin
- Full system access
- User management
- System settings
- Audit log access
- All beneficiary operations

#### StandardUser
- View and manage beneficiaries
- CSV import/export
- Dashboard access
- Limited to data operations

## 🔍 API Endpoints

The system uses a simple routing mechanism:

- `GET /` - Dashboard (redirects to login if not authenticated)
- `GET /login` - Login form
- `POST /login` - Process login
- `GET /logout` - Logout
- `GET /dashboard` - Main dashboard
- `GET /beneficiaries` - Beneficiary listing
- `GET /import` - CSV import interface
- `POST /import` - Process CSV upload

## 🧪 Testing

### Sample Data
Use the provided sample CSV file in `sample_data/sample_beneficiaries.csv` to test the import functionality.

### Manual Testing Checklist
- [ ] User authentication and authorization
- [ ] CSV import with validation
- [ ] Beneficiary CRUD operations
- [ ] Search and filtering
- [ ] Dashboard analytics
- [ ] Export functionality
- [ ] Responsive design
- [ ] Accessibility features

## 🚨 Security Considerations

1. **Change Default Credentials**: Update admin password immediately
2. **Database Security**: Use strong database passwords and restrict access
3. **File Uploads**: Validate file types and sizes
4. **Session Security**: Configure secure session settings
5. **Input Validation**: All user inputs are validated and sanitized
6. **SQL Injection**: All queries use prepared statements
7. **XSS Protection**: Output is properly escaped

## 📈 Performance Optimization

- Database indexes on frequently queried columns
- Pagination for large datasets
- Chunked processing for CSV imports
- Optimized SQL queries
- CSS/JS minification for production

## 🐛 Troubleshooting

### Common Issues

1. **Database Connection Failed**
   - Check database credentials in `config/database.php`
   - Ensure MySQL service is running
   - Verify database exists

2. **File Upload Errors**
   - Check `uploads/` directory permissions
   - Verify PHP upload limits
   - Ensure disk space availability

3. **Login Issues**
   - Verify user exists in database
   - Check session configuration
   - Clear browser cache/cookies

## 📚 Documentation

- [Installation Guide](docs/INSTALLATION.md)
- [User Guide](docs/USER_GUIDE.md)
- [API Documentation](docs/API.md)
- [Database Schema](docs/DATABASE.md)

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Add tests if applicable
5. Submit a pull request

## 📄 License

This project is licensed under the MIT License - see the LICENSE file for details.

## 🆘 Support

For support and questions:
- Create an issue in the GitHub repository
- Contact the development team
- Check the documentation in the `docs/` directory

## 🔄 Version History

- **v1.0.0** - Initial release with core functionality
  - User authentication and authorization
  - Beneficiary management
  - CSV import/export
  - Dashboard analytics
  - Responsive UI

---

**HoPE Beneficiaries Management System** - Empowering communities through efficient beneficiary management.
