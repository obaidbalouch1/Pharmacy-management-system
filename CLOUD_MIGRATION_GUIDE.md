# Cloud Database Migration Guide

## Overview
This guide will help you migrate your pharmacy database from XAMPP's local MySQL to a cloud MySQL database (accessible via MySQL Workbench).

## Prerequisites

1. ✓ MySQL Workbench installed
2. ✓ Cloud MySQL database account (AWS RDS, Google Cloud SQL, Azure, DigitalOcean, etc.)
3. ✓ Cloud database credentials (host, username, password)
4. ✓ Network access to cloud database (firewall rules configured)

## Migration Steps

### Step 1: Create Backup of Current Database

**Option A: Using Migration Tool (Recommended)**
1. Open browser and go to: `http://localhost/pharmacy/migrate_to_cloud.php`
2. The tool will automatically create a backup
3. Download the backup file (e.g., `migration_backup_2026-03-04.sql`)

**Option B: Using phpMyAdmin**
1. Go to `http://localhost/phpmyadmin`
2. Select `pharmacy_db` database
3. Click "Export" tab
4. Choose "Quick" export method
5. Format: SQL
6. Click "Go" to download

**Option C: Using Command Line**
```bash
cd C:\xampp\htdocs\pharmacy
mysqldump -u root -p pharmacy_db > pharmacy_backup.sql
```

### Step 2: Set Up Cloud Database

#### Using MySQL Workbench:

1. **Open MySQL Workbench**

2. **Create New Connection**
   - Click "+" next to "MySQL Connections"
   - Connection Name: "Pharmacy Cloud DB"
   - Hostname: Your cloud database host (e.g., `mysql.example.com`)
   - Port: 3306 (or your custom port)
   - Username: Your cloud database username
   - Password: Click "Store in Vault" and enter password

3. **Test Connection**
   - Click "Test Connection"
   - Should show "Successfully made the MySQL connection"

4. **Connect to Database**
   - Double-click the connection to open

### Step 3: Create Database on Cloud

In MySQL Workbench query window:

```sql
CREATE DATABASE IF NOT EXISTS pharmacy_db;
USE pharmacy_db;
```

Click Execute (⚡ icon)

### Step 4: Import Data to Cloud

**Option A: Using MySQL Workbench (Recommended)**

1. In MySQL Workbench, go to **Server → Data Import**
2. Select **Import from Self-Contained File**
3. Click "..." and browse to your backup file
4. Default Target Schema: Select `pharmacy_db`
5. Click **Start Import**
6. Wait for completion (progress bar will show)
7. Check for errors in the log

**Option B: Using Command Line**

```bash
mysql -h your-cloud-host.com -u your_username -p pharmacy_db < pharmacy_backup.sql
```

**Option C: Using Query Window**

1. Open the backup SQL file in a text editor
2. Copy all content
3. Paste into MySQL Workbench query window
4. Click Execute (⚡ icon)
5. Wait for completion

### Step 5: Update Application Configuration

1. **Backup Current Config**
   ```bash
   copy config\db.php config\db_local_backup.php
   ```

2. **Edit config/db.php**
   
   Replace with your cloud credentials:
   
   ```php
   <?php
   // Cloud Database Configuration
   define('DB_HOST', 'your-cloud-host.com');
   define('DB_USER', 'your_username');
   define('DB_PASS', 'your_password');
   define('DB_NAME', 'pharmacy_db');
   define('DB_PORT', '3306');
   
   // Create connection
   $conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_PORT);
   
   if (!$conn) {
       die("Connection failed: " . mysqli_connect_error());
   }
   
   mysqli_set_charset($conn, "utf8mb4");
   
   if (session_status() === PHP_SESSION_NONE) {
       session_start();
   }
   ?>
   ```

3. **Save the file**

### Step 6: Test Cloud Connection

1. Open browser: `http://localhost/pharmacy/test_cloud_connection.php`
2. Check connection status
3. Verify all tables are listed
4. Check row counts match your local database

### Step 7: Verify Application

1. Login to your pharmacy system
2. Check dashboard loads correctly
3. Test creating a sale
4. Test viewing medicines
5. Test all major features

## Common Cloud Providers

### AWS RDS MySQL

```php
define('DB_HOST', 'your-instance.us-east-1.rds.amazonaws.com');
define('DB_PORT', '3306');
define('DB_USER', 'admin');
define('DB_PASS', 'your-password');
```

**Setup:**
1. Create RDS MySQL instance
2. Configure security group (allow your IP)
3. Enable public accessibility
4. Note endpoint URL

### Google Cloud SQL

```php
define('DB_HOST', '34.123.45.67');  // Public IP
define('DB_PORT', '3306');
define('DB_USER', 'root');
define('DB_PASS', 'your-password');
```

**Setup:**
1. Create Cloud SQL instance
2. Add authorized network (your IP)
3. Create database user
4. Enable public IP

### Azure Database for MySQL

```php
define('DB_HOST', 'your-server.mysql.database.azure.com');
define('DB_PORT', '3306');
define('DB_USER', 'username@servername');
define('DB_PASS', 'your-password');
```

**Setup:**
1. Create Azure Database for MySQL
2. Configure firewall rules
3. Enable SSL (recommended)
4. Create database

### DigitalOcean Managed Database

```php
define('DB_HOST', 'your-cluster.db.ondigitalocean.com');
define('DB_PORT', '25060');
define('DB_USER', 'doadmin');
define('DB_PASS', 'your-password');
```

**Setup:**
1. Create managed MySQL database
2. Add trusted source (your IP)
3. Download CA certificate (for SSL)
4. Note connection details

## Security Best Practices

### 1. Use SSL Connection (Recommended)

```php
$conn = mysqli_init();
mysqli_ssl_set($conn, NULL, NULL, '/path/to/ca-cert.pem', NULL, NULL);
mysqli_real_connect($conn, DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_PORT, NULL, MYSQLI_CLIENT_SSL);
```

### 2. Restrict IP Access
- Only allow your server's IP address
- Use VPN for additional security
- Regularly update firewall rules

### 3. Strong Passwords
- Use complex passwords (16+ characters)
- Mix uppercase, lowercase, numbers, symbols
- Don't reuse passwords

### 4. Regular Backups
- Set up automated backups on cloud provider
- Download backups regularly
- Test restore process

### 5. Monitor Access
- Enable query logging
- Monitor for suspicious activity
- Set up alerts for failed login attempts

## Troubleshooting

### Connection Timeout
**Problem:** Can't connect to cloud database

**Solutions:**
1. Check firewall rules allow your IP
2. Verify database is running
3. Check host/port are correct
4. Test with MySQL Workbench first

### Access Denied
**Problem:** "Access denied for user"

**Solutions:**
1. Verify username/password
2. Check user has proper permissions
3. For Azure, use format: `username@servername`
4. Ensure user can connect from your IP

### SSL Required
**Problem:** "SSL connection required"

**Solutions:**
1. Download CA certificate from provider
2. Configure SSL in db.php
3. Use mysqli_ssl_set() function

### Slow Performance
**Problem:** Application is slow

**Solutions:**
1. Check network latency
2. Optimize queries (add indexes)
3. Use connection pooling
4. Consider CDN for static files
5. Enable query caching

## Rollback Plan

If migration fails, rollback to local database:

1. **Restore config/db.php**
   ```bash
   copy config\db_local_backup.php config\db.php
   ```

2. **Restart XAMPP MySQL**

3. **Test application**

## Maintenance

### Regular Tasks

1. **Weekly Backups**
   - Use backup_restore.php
   - Download and store securely

2. **Monitor Performance**
   - Check query execution times
   - Monitor connection pool

3. **Update Credentials**
   - Rotate passwords quarterly
   - Update SSL certificates

4. **Check Logs**
   - Review error logs
   - Monitor slow queries

## Support

### Tools Created
- `migrate_to_cloud.php` - Migration wizard
- `test_cloud_connection.php` - Connection tester
- `config/db_cloud.php.example` - Configuration template

### Need Help?
1. Check cloud provider documentation
2. Test connection with MySQL Workbench
3. Review error logs
4. Contact cloud provider support

## Success Checklist

- [ ] Backup created successfully
- [ ] Cloud database created
- [ ] Data imported to cloud
- [ ] config/db.php updated
- [ ] Connection test passed
- [ ] Application works correctly
- [ ] All features tested
- [ ] Backup plan in place
- [ ] SSL configured (if required)
- [ ] Firewall rules set

## Next Steps

After successful migration:
1. Keep XAMPP backup for 1 week
2. Monitor application performance
3. Set up automated cloud backups
4. Document your cloud credentials securely
5. Train team on new backup procedures

Your data is now safely stored in the cloud! 🎉
