<?php
require_once __DIR__ . '/../config/database.php';

try {
    $pdo = getImageDBConnection();
    
    // Получаем только BLOB данных (image_type не существует!)
    $stmt = $pdo->prepare("SELECT image FROM IMAGES WHERE name = 'back' LIMIT 1");
    $stmt->execute();
    $row = $stmt->fetch();
    
    if ($row && !empty($row['image'])) {
        $imageData = $row['image'];
        
        // АВТОМАТИЧЕСКОЕ ОПРЕДЕЛЕНИЕ ФОРМАТА
        $firstBytes = bin2hex(substr($imageData, 0, 8)); // Берем 8 байт для точности
        
        // Определяем MIME-тип по магическим числам
        if (strpos($firstBytes, 'ffd8ff') === 0) {
            // JPEG: FF D8 FF
            $mimeType = 'image/jpeg';
        } elseif (strpos($firstBytes, '89504e470d0a1a0a') === 0) {
            // PNG: 89 50 4E 47 0D 0A 1A 0A
            $mimeType = 'image/png';
        } elseif (strpos($firstBytes, '47494638') === 0) {
            // GIF: 47 49 46 38
            $mimeType = 'image/gif';
        } elseif (strpos($firstBytes, '52494646') === 0 && strpos($firstBytes, '57454250', 8) === 8) {
            // WebP: RIFF....WEBP
            $mimeType = 'image/webp';
        } elseif (strpos($firstBytes, '424d') === 0) {
            // BMP: BM
            $mimeType = 'image/bmp';
        } else {
            // Пробуем определить через finfo (если доступно)
            if (function_exists('finfo_open')) {
                $finfo = finfo_open(FILEINFO_MIME_TYPE);
                $mimeType = finfo_buffer($finfo, $imageData);
                finfo_close($finfo);
                
                if ($mimeType === 'application/octet-stream') {
                    $mimeType = 'image/jpeg'; // fallback
                }
            } else {
                $mimeType = 'image/jpeg'; // fallback по умолчанию
            }
        }
        
        // Устанавливаем правильный заголовок
        header("Content-Type: " . $mimeType);
        header("Content-Length: " . strlen($imageData));
        header("Cache-Control: public, max-age=86400");
        
        // Выводим изображение
        echo $imageData;
        exit;
        
    } else {
        // Картинка не найдена в БД
        createFallbackImage();
    }
    
} catch (Exception $e) {
    // Ошибка подключения
    createFallbackImage();
}

// Функция для создания резервного фона
function createFallbackImage() {
    $width = 1920;
    $height = 1080;
    $image = imagecreatetruecolor($width, $height);
    
    // Создаём D&D стиль фон
    for ($y = 0; $y < $height; $y++) {
        // Градиент от тёмного к светлому коричневому
        $r = (int)(58 + ($y / $height) * 50);
        $g = (int)(42 + ($y / $height) * 50);
        $b = (int)(26 + ($y / $height) * 50);
        $color = imagecolorallocate($image, $r, $g, $b);
        imageline($image, 0, $y, $width, $y, $color);
    }
    
    // Добавляем текстуру старого пергамента
    $gridColor = imagecolorallocatealpha($image, 0, 0, 0, 15);
    imagesetthickness($image, 1);
    
    // Вертикальные линии
    for ($x = 50; $x < $width; $x += 50) {
        imageline($image, $x, 0, $x, $height, $gridColor);
    }
    
    // Горизонтальные линии
    for ($y = 50; $y < $height; $y += 50) {
        imageline($image, 0, $y, $width, $y, $gridColor);
    }
    
    // Угловые украшения
    $decorColor = imagecolorallocate($image, 107, 68, 35);
    imagesetthickness($image, 3);
    
    // Левый верхний угол
    imageline($image, 20, 20, 80, 20, $decorColor);
    imageline($image, 20, 20, 20, 80, $decorColor);
    
    // Правый верхний угол
    imageline($image, $width - 20, 20, $width - 80, 20, $decorColor);
    imageline($image, $width - 20, 20, $width - 20, 80, $decorColor);
    
    // Левый нижний угол
    imageline($image, 20, $height - 20, 80, $height - 20, $decorColor);
    imageline($image, 20, $height - 20, 20, $height - 80, $decorColor);
    
    // Правый нижний угол
    imageline($image, $width - 20, $height - 20, $width - 80, $height - 20, $decorColor);
    imageline($image, $width - 20, $height - 20, $width - 20, $height - 80, $decorColor);
    
    header("Content-Type: image/jpeg");
    imagejpeg($image, null, 85);
    imagedestroy($image);
    exit;
}
?>