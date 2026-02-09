<?php
require_once '../config/database.php';
require_once '../includes/auth.php';
checkRole(['admin']);

$pdo = getDBConnection();
$success = '';
$error = '';

// Получаем список команд
$stmt = $pdo->query("SELECT team_id, team_color FROM TEAMS ORDER BY team_color");
$teams = $stmt->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $middle_name = trim($_POST['middle_name']);
    $team_id = $_POST['team_id'] ? (int)$_POST['team_id'] : null;
    $score = (int)$_POST['score'];
    
    try {
        $stmt = $pdo->prepare("INSERT INTO STUDENTS (first_name, last_name, middle_name, team_id, score) 
                               VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$first_name, $last_name, $middle_name ?: null, $team_id, $score]);
        $success = 'Студент успешно добавлен!';
    } catch (PDOException $e) {
        $error = 'Ошибка: ' . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Добавить студента</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; background: #f4f4f4; }
        .header { background: #333; color: white; padding: 20px; }
        .container { max-width: 800px; margin: 30px auto; padding: 30px; background: white; border-radius: 8px; }
        .form-group { margin-bottom: 20px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; color: #555; }
        input, select { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px; }
        button { background: #28a745; color: white; padding: 12px 30px; border: none; border-radius: 4px; cursor: pointer; font-size: 16px; }
        button:hover { background: #218838; }
        .back { background: #6c757d; color: white; padding: 10px 20px; text-decoration: none; border-radius: 4px; display: inline-block; margin-bottom: 20px; }
        .success { background: #d4edda; color: #155724; padding: 10px; border-radius: 4px; margin-bottom: 20px; }
        .error { background: #f8d7da; color: #721c24; padding: 10px; border-radius: 4px; margin-bottom: 20px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>➕ Добавить нового студента</h1>
    </div>
    
    <div class="container">
        <a href="students.php" class="back">← Назад к списку</a>
        
        <?php if ($success): ?>
            <div class="success"><?= $success ?></div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div class="error"><?= $error ?></div>
        <?php endif; ?>
        
        <form method="POST">
            <div class="form-group">
                <label>Фамилия:</label>
                <input type="text" name="last_name" required>
            </div>
            
            <div class="form-group">
                <label>Имя:</label>
                <input type="text" name="first_name" required>
            </div>
            
            <div class="form-group">
                <label>Отчество (необязательно):</label>
                <input type="text" name="middle_name">
            </div>
            
            <div class="form-group">
                <label>Команда:</label>
                <select name="team_id">
                    <option value="">Без команды</option>
                    <?php foreach ($teams as $team): ?>
                        <option value="<?= $team['team_id'] ?>"><?= htmlspecialchars($team['team_color']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label>Начальные баллы:</label>
                <input type="number" name="score" value="0" min="0" required>
            </div>
            
            <button type="submit">Добавить студента</button>
        </form>
    </div>
</body>
</html>