<?php
session_start();
require_once __DIR__ . '/db.php';

$error = '';
// Server-side login handling: when the form POSTs to this script we try DB authentication.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');
    if (!$email || !$password) {
        $error = 'Please fill in both fields.';
    } else {
        // Default bypass credentials (do not require DB lookup)
        $defaultEmail = 'admin123@email.com';
        $defaultPassword = 'admin123';

        if ($email === $defaultEmail && $password === $defaultPassword) {
            // Log in the default admin without DB
            $_SESSION['user_id'] = 'default_admin';
            header('Location: Index.php');
            exit;
        }

        // Not the default credentials -> attempt DB authentication (if DB available)
        try {
            $pdo = getDb();
            $stmt = $pdo->prepare('SELECT id, email, password FROM users WHERE email = ? LIMIT 1');
            $stmt->execute([$email]);
            $user = $stmt->fetch();

            if ($user) {
                // Support both hashed passwords and plain-text (replace with hashed in production)
                if (password_verify($password, $user['password']) || $password === $user['password']) {
                    $_SESSION['user_id'] = $user['id'];
                    header('Location: Index.php');
                    exit;
                } else {
                    $error = 'Invalid email or password.';
                }
            } else {
                $error = 'Invalid email or password.';
            }
        } catch (Exception $e) {
            $error = 'Login error: ' . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Web Page</title>
    <link rel="stylesheet" href="style.css">
    
</head>
<body>
<div class=container>
    <h1>Log in</h1>
    <p>Please type your email and password</p>
    <form id="loginForm" method="post" action="login.php" data-server="1">

        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" required>
        </div>
        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" required>
        </div>

        <div class="form-group">
            <button type="submit" id="loginButton" class="login-btn">Log In</button>
        </div>

    </form>
</div>
        

<?php if (!empty($error)): ?>
    <script>alert(<?php echo json_encode($error); ?>);</script>
<?php endif; ?>

    <script src="log.js"></script>
</div>
</body>
</html>
