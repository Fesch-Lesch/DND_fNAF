<?php
require_once '../config/database.php';
require_once '../includes/auth.php';
checkRole(['admin']);

$pdo = getDBConnection();

// Получаем список всех студентов
$stmt = $pdo->query("
    SELECT s.student_id, s.first_name, s.last_name, s.middle_name, s.score, 
           t.team_color, t.team_id
    FROM STUDENTS s
    LEFT JOIN TEAMS t ON s.team_id = t.team_id
    ORDER BY s.last_name, s.first_name
");
$students = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Список студентов</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; background: #f4f4f4; }
        .header { background: #333; color: white; padding: 20px; }
        .container { max-width: 1200px; margin: 30px auto; padding: 20px; background: white; border-radius: 8px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background: #007bff; color: white; }
        tr:hover { background: #f5f5f5; }
        .btn { padding: 6px 12px; text-decoration: none; border-radius: 4px; display: inline-block; margin: 2px; }
        .btn-edit { background: #ffc107; color: black; }
        .btn-delete { background: #dc3545; color: white; }
        .back { background: #6c757d; color: white; padding: 10px 20px; text-decoration: none; border-radius: 4px; display: inline-block; margin-bottom: 20px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>👥 Список студентов</h1>
    </div>
    
    <div class="container">
        <a href="dashboard.php" class="back">← Назад к панели</a>
        <a href="add_student.php" class="btn btn-edit">+ Добавить студента</a>
        
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Фамилия</th>
                    <th>Имя</th>
                    <th>Отчество</th>
                    <th>Команда</th>
                    <th>Баллы</th>
                    <th>Действия</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($students as $student): ?>
                <tr>
                    <td><?= $student['student_id'] ?></td>
                    <td><?= htmlspecialchars($student['last_name']) ?></td>
                    <td><?= htmlspecialchars($student['first_name']) ?></td>
                    <td><?= htmlspecialchars($student['middle_name'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($student['team_color'] ?? 'Без команды') ?></td>
                    <td><?= $student['score'] ?> 💰</td>
                    <td>
                        <a href="edit_student.php?id=<?= $student['student_id'] ?>" class="btn btn-edit">Изменить</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>