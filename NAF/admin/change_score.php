<?php
require_once '../config/database.php';
require_once '../includes/auth.php';
checkRole(['admin']);

$pdo = getDBConnection();
$success = '';
$error = '';

// Получаем список студентов
$stmt = $pdo->query("
    SELECT s.student_id, s.first_name, s.last_name, s.middle_name, s.score, t.team_color
    FROM STUDENTS s
    LEFT JOIN TEAMS t ON s.team_id = t.team_id
    ORDER BY s.last_name, s.first_name
");
$students = $stmt->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $student_id = (int)$_POST['student_id'];
    $new_score = (int)$_POST['new_score'];
    $reason = trim($_POST['reason']);
    
    try {
        // Получаем текущий счет
        $stmt = $pdo->prepare("SELECT score FROM STUDENTS WHERE student_id = ?");
        $stmt->execute([$student_id]);
        $old_score = $stmt->fetchColumn();
        
        // Обновляем счет
        $stmt = $pdo->prepare("UPDATE STUDENTS SET score = ? WHERE student_id = ?");
        $stmt->execute([$new_score, $student_id]);
        
        // Логируем изменение
        $stmt = $pdo->prepare("INSERT INTO LOG_SCORE_CHANGES (student_id, team_id, old_score, new_score, change_amount, changed_by, reason) 
                               SELECT ?, team_id, ?, ?, ?, ?, ? FROM STUDENTS WHERE student_id = ?");
        $stmt->execute([$student_id, $old_score, $new_score, $new_score - $old_score, $_SESSION['user_id'], $reason, $student_id]);
        
        $success = 'Баллы успешно изменены!';
        
        // Обновляем список студентов
        $stmt = $pdo->query("
            SELECT s.student_id, s.first_name, s.last_name, s.middle_name, s.score, t.team_color
            FROM STUDENTS s
            LEFT JOIN TEAMS t ON s.team_id = t.team_id
            ORDER BY s.last_name, s.first_name
        ");
        $students = $stmt->fetchAll();
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
    <title>Изменить баллы студента</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; background: #f4f4f4; }
        .header { background: #333; color: white; padding: 20px; }
        .container { max-width: 900px; margin: 30px auto; padding: 30px; background: white; border-radius: 8px; }
        .form-group { margin-bottom: 20px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; color: #555; }
        input, select, textarea { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px; }
        button { background: #28a745; color: white; padding: 12px 30px; border: none; border-radius: 4px; cursor: pointer; font-size: 16px; }
        button:hover { background: #218838; }
        .back { background: #6c757d; color: white; padding: 10px 20px; text-decoration: none; border-radius: 4px; display: inline-block; margin-bottom: 20px; }
        .success { background: #d4edda; color: #155724; padding: 10px; border-radius: 4px; margin-bottom: 20px; }
        .error { background: #f8d7da; color: #721c24; padding: 10px; border-radius: 4px; margin-bottom: 20px; }
        .current-score { background: #e7f3ff; padding: 15px; border-radius: 4px; margin-top: 10px; font-size: 18px; font-weight: bold; }
    </style>
    <script>
        function updateCurrentScore() {
            const select = document.getElementById('student_id');
            const scoreDiv = document.getElementById('current_score');
            if (select.selectedIndex > 0) {
                const score = select.options[select.selectedIndex].dataset.score;
                scoreDiv.innerHTML = 'Текущие баллы: ' + score + ' 💰';
                scoreDiv.style.display = 'block';
                document.getElementById('new_score').value = score;
            } else {
                scoreDiv.style.display = 'none';
            }
        }
    </script>
</head>
<body>
    <div class="header">
        <h1>💰 Изменить баллы студента</h1>
    </div>
    
    <div class="container">
        <a href="dashboard.php" class="back">← Назад к панели</a>
        
        <?php if ($success): ?>
            <div class="success"><?= $success ?></div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div class="error"><?= $error ?></div>
        <?php endif; ?>
        
        <form method="POST">
            <div class="form-group">
                <label>Выберите студента:</label>
                <select name="student_id" id="student_id" required onchange="updateCurrentScore()">
                    <option value="">-- Выберите студента --</option>
                    <?php foreach ($students as $student): ?>
                        <option value="<?= $student['student_id'] ?>" data-score="<?= $student['score'] ?>">
                            <?= htmlspecialchars($student['last_name'] . ' ' . $student['first_name'] . ' ' . ($student['middle_name'] ?? '')) ?> 
                            (<?= htmlspecialchars($student['team_color'] ?? 'Без команды') ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
                <div id="current_score" class="current-score" style="display: none;"></div>
            </div>
            
            <div class="form-group">
                <label>Новое количество баллов:</label>
                <input type="number" name="new_score" id="new_score" min="0" required>
            </div>
            
            <div class="form-group">
                <label>Причина изменения:</label>
                <textarea name="reason" rows="3" placeholder="Укажите причину изменения баллов..." required></textarea>
            </div>
            
            <button type="submit">Изменить баллы</button>
        </form>
    </div>
</body>
</html>