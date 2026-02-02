<?php
require_once 'config/database.php';
require_once 'includes/auth.php';

if (isLoggedIn()) {
    header('Location: dashboard.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($username) || empty($password)) {
        $error = 'Заполните все поля';
    } else {
        $pdo = getDBConnection();
        
        $stmt = $pdo->prepare("SELECT * FROM USERS WHERE username = ? AND is_active = 1");
        $stmt->execute([$username]);
        $user = $stmt->fetch();
        
        if ($user && password_verify($password, $user['password_hash'])) {
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['team_id'] = $user['team_id'];
            $_SESSION['student_id'] = $user['student_id'];
            
            $stmt = $pdo->prepare("UPDATE USERS SET last_login = NOW() WHERE user_id = ?");
            $stmt->execute([$user['user_id']]);
            
            $stmt = $pdo->prepare("INSERT INTO LOG_LOGINS (user_id, username, ip_address, user_agent, status) VALUES (?, ?, ?, ?, 'success')");
            $stmt->execute([$user['user_id'], $user['username'], $_SERVER['REMOTE_ADDR'], $_SERVER['HTTP_USER_AGENT']]);
            
            header('Location: dashboard.php');
            exit;
        } else {
            $error = 'Неверное имя пользователя или пароль';
            
            if ($user) {
                $stmt = $pdo->prepare("INSERT INTO LOG_LOGINS (user_id, username, ip_address, user_agent, status) VALUES (?, ?, ?, ?, 'failed')");
                $stmt->execute([$user['user_id'], $username, $_SERVER['REMOTE_ADDR'], $_SERVER['HTTP_USER_AGENT']]);
            }
        }
    }
}

$pageTitle = 'Вход в систему';
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?></title>
    <link rel="stylesheet" href="/dnd-site/css/style.css">
    <style>
        .login-container {
            max-width: 400px;
            margin: 100px auto;
            padding: 30px;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }
        .login-container h1 {
            text-align: center;
            margin-bottom: 30px;
            color: #333;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h1>🎲 DnD System</h1>
        
        <?php if ($error): ?>
            <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        
        <form method="POST">
            <div class="form-group">
                <label for="username">Имя пользователя</label>
                <input type="text" id="username" name="username" required 
                       value="<?= htmlspecialchars($_POST['username'] ?? '') ?>">
            </div>
            
            <div class="form-group">
                <label for="password">Пароль</label>
                <input type="password" id="password" name="password" required>
            </div>
            
            <button type="submit" class="btn btn-primary" style="width: 100%;">Войти</button>
        </form>
    </div>
</body>
</html>