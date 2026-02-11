 <?php
// public/get_bg.php

// Подключаем конфигурацию (поднявшись на уровень выше из папки public)
require_once __DIR__ . '/../config/database.php';

try {
    // 1. Подключаемся к ТРЕТЬЕЙ базе (Images)
    // Используем функцию, которую мы создали в config/database.php
    $pdo = getImageDBConnection();

    // 2. Ищем картинку с именем 'back'
    $stmt = $pdo->prepare("SELECT image FROM IMAGES WHERE name = 'back' LIMIT 1");
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($row) {
        // 3. Сообщаем браузеру, что это картинка (предполагаем JPG)
        header("Content-Type: image/jpeg");
        
        // 4. Выводим бинарные данные картинки
        echo $row['image'];
    } else {
        // Если картинка не найдена, ничего не выводим или можно вывести 1x1 прозрачный пиксель
        http_response_code(404);
    }

} catch (Exception $e) {
    http_response_code(500);
    echo "Error: " . $e->getMessage();
}
?>
