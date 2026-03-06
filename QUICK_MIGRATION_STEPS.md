# Quick Migration Steps - XAMPP to Cloud MySQL

## ✅ Step 1: Backup Created!
Your database backup is ready:
- **File:** `database/backups/migration_backup_2026-03-05_10-52-49.sql`
- **Size:** 28.51 KB
- **Status:** ✓ Ready for import

## 📋 Step 2: Import to Cloud (Choose One Method)

### Method A: MySQL Workbench (Easiest)
1. Open MySQL Workbench
2. Connect to your cloud database
3. Go to: **Server → Data Import**
4. Select: **Import from Self-Contained File**
5. Browse to: `C:\xampp\htdocs\pharmacy\database\backups\migration_backup_2026-03-05_10-52-49.sql`
6. Click: **Start Import**
7. Wait for completion ✓

### Method B: Command Line
```bash
mysql -h your-cloud-host.com -u your_username -p pharmacy_db < database/backups/migration_backup_2026-03-05_10-52-49.sql
```

### Method C: phpMyAdmin (If Available)
1. Login to cloud phpMyAdmin
2. Create database: `pharmacy_db`
3. Go to **Import** tab
4. Upload: `migration_backup_2026-03-05_10-52-49.sql`
5. Click **Go**

## ⚙️ Step 3: Update Configuration

Edit `config/db.php` with your cloud credentials:

```php
<?php
define('DB_HOST', 'your-cloud-host.com');  // Your cloud MySQL host
define('DB_USER', 'your_username');         // Your cloud username
define('DB_PASS', 'your_password');         // Your cloud password
define('DB_NAME', 'pharmacy_db');
define('DB_PORT', '3306');

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

## 🔍 Step 4: Test Connection

Open in browser:
```
http://localhost/pharmacy/test_cloud_connection.php
```

Should show:
- ✓ Connection Successful
- ✓ All tables listed
- ✓ Row counts displayed

## ✅ Step 5: Verify Application

1. Login to pharmacy system
2. Check dashboard
3. Test creating a sale
4. Verify all features work

## 🎯 Your Cloud Database Info

Fill in your details:
- **Provider:** _________________ (AWS/Google/Azure/DigitalOcean/Other)
- **Host:** _________________
- **Port:** _________________ (usually 3306)
- **Username:** _________________
- **Password:** _________________ (keep secure!)
- **Database Name:** pharmacy_db

## 🔒 Security Checklist

- [ ] Firewall allows your IP address
- [ ] Strong password used
- [ ] SSL enabled (if available)
- [ ] Regular backups scheduled
- [ ] Local backup kept as safety copy

## 🆘 Troubleshooting

**Can't connect?**
1. Check firewall rules
2. Verify credentials
3. Test with MySQL Workbench first
4. Check if database allows remote connections

**Import failed?**
1. Check file size limits
2. Try command line method
3. Import in smaller chunks
4. Check error logs

**Application not working?**
1. Verify config/db.php is correct
2. Check all credentials
3. Test connection script
4. Rollback to local if needed

## 🔄 Rollback (If Needed)

If something goes wrong:
```bash
# Restore original config
copy config\db_local_backup.php config\db.php

# Restart XAMPP MySQL
# Test application
```

## 📞 Need Help?

Tools available:
- `migrate_to_cloud.php` - Full migration wizard
- `test_cloud_connection.php` - Connection tester
- `CLOUD_MIGRATION_GUIDE.md` - Detailed guide

## ✨ Success!

Once completed:
- ✓ Data safely in cloud
- ✓ Accessible from anywhere
- ✓ Automatic backups (if configured)
- ✓ No data loss risk from local machine

Your pharmacy data is now cloud-ready! 🎉
