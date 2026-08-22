# Sanderi Stationary

WAMP-compatible PHP 8 + MySQL starter with mobile OTP authentication.

## Setup

1. Start Apache and MySQL in WAMP.
2. Open phpMyAdmin and import `schema.sql`.
3. API ke bina testing ke liye `config.php` me `LOCAL_OTP_MODE = true` rehne do. OTP verification screen par dikhega aur `storage/otp.log` me save hoga; actual mobile SMS nahi jayega.
4. Actual SMS ke liye `LOCAL_OTP_MODE = false` karke `SMS_API_KEY` add karo. Integration Fast2SMS OTP API use karti hai.
5. Open `http://localhost/sanderi-stationary/`.
6. Admin setup ke liye `http://localhost/sanderi-stationary/install-admin.php` ek baar open karo, phir security ke liye `install-admin.php` delete kar do.
7. Admin panel: `http://localhost/sanderi-stationary/admin-login.php`.

Default development admin credentials: `admin@sanderi.local` / `Admin@12345`. Login ke baad password change workflow add karna recommended hai before production use.

Products admin panel se add kiye ja sakte hain. Customers ko browse karne ke liye `products.php`, cart ke liye `cart.php`, checkout ke liye `checkout.php`, aur order history ke liye `orders.php` use hota hai.

The application never stores plain-text passwords or OTPs. OTPs expire after 10 minutes and are stored as hashes.

A real SMS provider cannot send messages without an account/API key. No code change is needed after adding that credential.
