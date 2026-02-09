<?php
session_start();
require_once 'config/database.php';

$error = '';
$locked_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    
    if (!empty($username) && !empty($password)) {
        try {
            $pdo = getDBConnection();
            
            // Проверяем блокировку пользователя
            $stmt = $pdo->prepare("SELECT user_id, username, password, role, team_id, is_locked, locked_until, failed_attempts 
                                   FROM USERS WHERE username = ?");
            $stmt->execute([$username]);
            $user = $stmt->fetch();
            
            if ($user) {
                // Проверяем, заблокирован ли пользователь
                if ($user['is_locked'] == 1) {
                    $locked_until = strtotime($user['locked_until']);
                    $now = time();
                    
                    if ($now < $locked_until) {
                        $minutes_left = ceil(($locked_until - $now) / 60);
                        $locked_message = "Ваш аккаунт заблокирован из-за множественных неудачных попыток входа. Попробуйте снова через {$minutes_left} минут.";
                        
                        // Логируем неудачную попытку
                        $stmt_log = $pdo->prepare("INSERT INTO LOG_LOGINS (user_id, username, ip_address, user_agent, status) 
                                                   VALUES (?, ?, ?, ?, 'failed')");
                        $stmt_log->execute([$user['user_id'], $username, $_SERVER['REMOTE_ADDR'], $_SERVER['HTTP_USER_AGENT']]);
                    } else {
                        // Время блокировки истекло, снимаем блокировку
                        $stmt_unlock = $pdo->prepare("UPDATE USERS SET is_locked = 0, locked_until = NULL, failed_attempts = 0 WHERE user_id = ?");
                        $stmt_unlock->execute([$user['user_id']]);
                        $user['is_locked'] = 0;
                    }
                }
                
                // Если не заблокирован, проверяем пароль
                if ($user['is_locked'] == 0) {
                    if (password_verify($password, $user['password'])) {
                        // Успешный вход
                        $_SESSION['user_id'] = $user['user_id'];
                        $_SESSION['username'] = $user['username'];
                        $_SESSION['role'] = $user['role'];
                        $_SESSION['team_id'] = $user['team_id'];
                        
                        // Логируем успешный вход
                        $stmt_log = $pdo->prepare("INSERT INTO LOG_LOGINS (user_id, username, ip_address, user_agent, status) 
                                                   VALUES (?, ?, ?, ?, 'success')");
                        $stmt_log->execute([$user['user_id'], $username, $_SERVER['REMOTE_ADDR'], $_SERVER['HTTP_USER_AGENT']]);
                        
                        // Перенаправляем в зависимости от роли
                        switch ($user['role']) {
                            case 'admin':
                                header('Location: admin/dashboard.php');
                                break;
                            case 'teacher':
                                header('Location: teacher/dashboard.php');
                                break;
                            case 'captain':
                                header('Location: public/team_stats.php?team_id=' . $user['team_id']);
                                break;
                            default:
                                header('Location: public/student_ranking.php');
                        }
                        exit;
                    } else {
                        // Неверный пароль
                        $error = 'Неверное имя пользователя или пароль.';
                        
                        // Логируем неудачную попытку
                        $stmt_log = $pdo->prepare("INSERT INTO LOG_LOGINS (user_id, username, ip_address, user_agent, status) 
                                                   VALUES (?, ?, ?, ?, 'failed')");
                        $stmt_log->execute([$user['user_id'], $username, $_SERVER['REMOTE_ADDR'], $_SERVER['HTTP_USER_AGENT']]);
                    }
                }
            } else {
                $error = 'Неверное имя пользователя или пароль.';
                
                // Логируем попытку входа с несуществующим пользователем
                $stmt_log = $pdo->prepare("INSERT INTO LOG_LOGINS (user_id, username, ip_address, user_agent, status) 
                                           VALUES (0, ?, ?, ?, 'failed')");
                $stmt_log->execute([$username, $_SERVER['REMOTE_ADDR'], $_SERVER['HTTP_USER_AGENT']]);
            }
        } catch (PDOException $e) {
            $error = 'Ошибка базы данных: ' . $e->getMessage();
        }
    } else {
        $error = 'Заполните все поля.';
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Авторизация - DND School System</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; background: #f4f4f4; display: flex; justify-content: center; align-items: center; min-height: 100vh; }
        .login-container { background: white; padding: 40px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); width: 100%; max-width: 400px; }
        h1 { text-align: center; margin-bottom: 30px; color: #333; }
        .form-group { margin-bottom: 20px; }
        label { display: block; margin-bottom: 5px; color: #555; font-weight: bold; }
        input[type="text"], input[type="password"] { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px; }
        button { width: 100%; padding: 12px; background: #007bff; color: white; border: none; border-radius: 4px; font-size: 16px; cursor: pointer; }
        button:hover { background: #0056b3; }
        .error { background: #f8d7da; color: #721c24; padding: 10px; border-radius: 4px; margin-bottom: 20px; border: 1px solid #f5c6cb; }
        .locked { background: #fff3cd; color: #856404; padding: 10px; border-radius: 4px; margin-bottom: 20px; border: 1px solid #ffeaa7; }
    </style>
</head>
<body>
    <div class="login-container">
        <h1>🎲 DND School System</h1>
        
        <?php if ($locked_message): ?>
            <div class="locked"><?= htmlspecialchars($locked_message) ?></div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div class="error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        
        <form method="POST" action="">
            <div class="form-group">
                <label for="username">Имя пользователя:</label>
                <input type="text" id="username" name="username" required>
            </div>
            
            <div class="form-group">
                <label for="password">Пароль:</label>
                <input type="password" id="password" name="password" required>
            </div>
            
            <button type="submit">Войти</button>
        </form>
    </div>
</body>
</html>