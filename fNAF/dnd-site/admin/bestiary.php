<?php
$pageTitle = 'Редактор бестиария';
require_once '../config/database.php';
require_once '../includes/header.php';
requireRole('admin');

$pdo = getDBConnection();
setCurrentUserForTriggers($pdo);

$message = '';
$error = '';
$editCreature = null;

if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM BESTIARY WHERE creature_id = ?");
    $stmt->execute([(int)$_GET['edit']]);
    $editCreature = $stmt->fetch();
}

if (isset($_GET['delete'])) {
    $stmt = $pdo->prepare("DELETE FROM BESTIARY WHERE creature_id = ?");
    $stmt->execute([(int)$_GET['delete']]);
    $message = 'Существо успешно удалено из бестиария';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'name' => trim($_POST['name']),
        'type' => trim($_POST['type']),
        'size' => $_POST['size'],
        'alignment' => trim($_POST['alignment']) ?: null,
        'challenge_rating' => (float)$_POST['challenge_rating'],
        'experience_points' => (int)$_POST['experience_points'],
        'hp' => (int)$_POST['hp'],
        'armor_class' => (int)$_POST['armor_class'],
        'speed' => trim($_POST['speed']),
        'strength' => (int)$_POST['strength'],
        'dexterity' => (int)$_POST['dexterity'],
        'constitution' => (int)$_POST['constitution'],
        'intelligence' => (int)$_POST['intelligence'],
        'wisdom' => (int)$_POST['wisdom'],
        'charisma' => (int)$_POST['charisma'],
        'damage_vulnerabilities' => trim($_POST['damage_vulnerabilities']) ?: null,
        'damage_resistances' => trim($_POST['damage_resistances']) ?: null,
        'damage_immunities' => trim($_POST['damage_immunities']) ?: null,
        'condition_immunities' => trim($_POST['condition_immunities']) ?: null,
        'senses' => trim($_POST['senses']) ?: null,
        'languages' => trim($_POST['languages']) ?: null,
        'special_abilities' => trim($_POST['special_abilities']) ?: null,
        'actions' => trim($_POST['actions']) ?: null,
        'legendary_actions' => trim($_POST['legendary_actions']) ?: null,
        'description' => trim($_POST['description']) ?: null,
        'habitat' => trim($_POST['habitat']) ?: null
    ];
    
    if (empty($data['name']) || empty($data['type'])) {
        $error = 'Имя и тип существа обязательны';
    } else {
        if (isset($_POST['creature_id']) && $_POST['creature_id']) {
            $sql = "UPDATE BESTIARY SET 
                    name = ?, type = ?, size = ?, alignment = ?, challenge_rating = ?,
                    experience_points = ?, hp = ?, armor_class = ?, speed = ?,
                    strength = ?, dexterity = ?, constitution = ?, intelligence = ?, wisdom = ?, charisma = ?,
                    damage_vulnerabilities = ?, damage_resistances = ?, damage_immunities = ?, condition_immunities = ?,
                    senses = ?, languages = ?, special_abilities = ?, actions = ?, legendary_actions = ?,
                    description = ?, habitat = ?
                    WHERE creature_id = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([...array_values($data), (int)$_POST['creature_id']]);
            $message = 'Существо успешно обновлено';
        } else {
            $sql = "INSERT INTO BESTIARY 
                    (name, type, size, alignment, challenge_rating, experience_points, hp, armor_class, speed,
                     strength, dexterity, constitution, intelligence, wisdom, charisma,
                     damage_vulnerabilities, damage_resistances, damage_immunities, condition_immunities,
                     senses, languages, special_abilities, actions, legendary_actions, description, habitat)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute(array_values($data));
            $message = 'Существо успешно добавлено в бестиарий';
        }
        
        header('Location: bestiary.php?success=1');
        exit;
    }
}

if (isset($_GET['success'])) {
    $message = 'Операция выполнена успешно';
}

$creatures = $pdo->query("SELECT * FROM BESTIARY ORDER BY challenge_rating, name")->fetchAll();

$sizes = ['tiny', 'small', 'medium', 'large', 'huge', 'gargantuan'];
$sizeLabels = [
    'tiny' => 'Крошечный',
    'small' => 'Маленький',
    'medium' => 'Средний',
    'large' => 'Большой',
    'huge' => 'Огромный',
    'gargantuan' => 'Исполинский'
];
?>

<h1>📖 Редактор бестиария</h1>

<?php if ($message): ?>
    <div class="alert alert-success"><?= htmlspecialchars($message) ?></div>
<?php endif; ?>

<?php if ($error): ?>
    <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<div class="card">
    <h2><?= $editCreature ? 'Редактирование существа' : 'Добавление нового существа' ?></h2>
    
    <form method="POST">
        <?php if ($editCreature): ?>
            <input type="hidden" name="creature_id" value="<?= $editCreature['creature_id'] ?>">
        <?php endif; ?>
        
        <h3>Основная информация</h3>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px;">
            <div class="form-group">
                <label for="name">Название *</label>
                <input type="text" id="name" name="name" required maxlength="50"
                       value="<?= htmlspecialchars($editCreature['name'] ?? '') ?>">
            </div>
            
            <div class="form-group">
                <label for="type">Тип *</label>
                <input type="text" id="type" name="type" required maxlength="30"
                       placeholder="Нежить, Зверь, Гуманоид..."
                       value="<?= htmlspecialchars($editCreature['type'] ?? '') ?>">
            </div>
            
            <div class="form-group">
                <label for="size">Размер</label>
                <select name="size" id="size">
                    <?php foreach ($sizes as $size): ?>
                        <option value="<?= $size ?>" 
                                <?= (isset($editCreature['size']) && $editCreature['size'] === $size) ? 'selected' : '' ?>>
                            <?= $sizeLabels[$size] ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label for="alignment">Мировоззрение</label>
                <input type="text" id="alignment" name="alignment" maxlength="30"
                       placeholder="Хаотично-злой, Законно-добрый..."
                       value="<?= htmlspecialchars($editCreature['alignment'] ?? '') ?>">
            </div>
            
            <div class="form-group">
                <label for="challenge_rating">Уровень опасности</label>
                <input type="number" id="challenge_rating" name="challenge_rating" min="0" max="30" step="0.125"
                       value="<?= $editCreature['challenge_rating'] ?? 0 ?>">
            </div>
            
            <div class="form-group">
                <label for="experience_points">Опыт (XP)</label>
                <input type="number" id="experience_points" name="experience_points" min="0"
                       value="<?= $editCreature['experience_points'] ?? 0 ?>">
            </div>
        </div>
        
        <h3>Боевые характеристики</h3>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 15px;">
            <div class="form-group">
                <label for="hp">Хиты (HP)</label>
                <input type="number" id="hp" name="hp" min="1"
                       value="<?= $editCreature['hp'] ?? 10 ?>">
            </div>
            
            <div class="form-group">
                <label for="armor_class">Класс доспеха (AC)</label>
                <input type="number" id="armor_class" name="armor_class" min="0"
                       value="<?= $editCreature['armor_class'] ?? 10 ?>">
            </div>
            
            <div class="form-group">
                <label for="speed">Скорость</label>
                <input type="text" id="speed" name="speed" maxlength="100"
                       placeholder="30 ft., fly 60 ft."
                       value="<?= htmlspecialchars($editCreature['speed'] ?? '30 ft.') ?>">
            </div>
        </div>
        
        <h3>Атрибуты</h3>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(100px, 1fr)); gap: 15px;">
            <div class="form-group">
                <label for="strength">Сила</label>
                <input type="number" id="strength" name="strength" min="1" max="30"
                       value="<?= $editCreature['strength'] ?? 10 ?>">
            </div>
            
            <div class="form-group">
                <label for="dexterity">Ловкость</label>
                <input type="number" id="dexterity" name="dexterity" min="1" max="30"
                       value="<?= $editCreature['dexterity'] ?? 10 ?>">
            </div>
            
            <div class="form-group">
                <label for="constitution">Телосложение</label>
                <input type="number" id="constitution" name="constitution" min="1" max="30"
                       value="<?= $editCreature['constitution'] ?? 10 ?>">
            </div>
            
            <div class="form-group">
                <label for="intelligence">Интеллект</label>
                <input type="number" id="intelligence" name="intelligence" min="1" max="30"
                       value="<?= $editCreature['intelligence'] ?? 10 ?>">
            </div>
            
            <div class="form-group">
                <label for="wisdom">Мудрость</label>
                <input type="number" id="wisdom" name="wisdom" min="1" max="30"
                       value="<?= $editCreature['wisdom'] ?? 10 ?>">
            </div>
            
            <div class="form-group">
                <label for="charisma">Харизма</label>
                <input type="number" id="charisma" name="charisma" min="1" max="30"
                       value="<?= $editCreature['charisma'] ?? 10 ?>">
            </div>
        </div>
        
        <h3>Защитные свойства</h3>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 15px;">
            <div class="form-group">
                <label for="damage_vulnerabilities">Уязвимости к урону</label>
                <input type="text" id="damage_vulnerabilities" name="damage_vulnerabilities"
                       placeholder="огонь, холод..."
                       value="<?= htmlspecialchars($editCreature['damage_vulnerabilities'] ?? '') ?>">
            </div>
            
            <div class="form-group">
                <label for="damage_resistances">Сопротивления к урону</label>
                <input type="text" id="damage_resistances" name="damage_resistances"
                       placeholder="дробящий, колющий..."
                       value="<?= htmlspecialchars($editCreature['damage_resistances'] ?? '') ?>">
            </div>
            
            <div class="form-group">
                <label for="damage_immunities">Иммунитеты к урону</label>
                <input type="text" id="damage_immunities" name="damage_immunities"
                       placeholder="яд, некротический..."
                       value="<?= htmlspecialchars($editCreature['damage_immunities'] ?? '') ?>">
            </div>
            
            <div class="form-group">
                <label for="condition_immunities">Иммунитеты к состояниям</label>
                <input type="text" id="condition_immunities" name="condition_immunities"
                       placeholder="отравление, страх..."
                       value="<?= htmlspecialchars($editCreature['condition_immunities'] ?? '') ?>">
            </div>
        </div>
        
        <h3>Чувства и языки</h3>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 15px;">
            <div class="form-group">
                <label for="senses">Чувства</label>
                <input type="text" id="senses" name="senses"
                       placeholder="Темное зрение 60 ft., пассивное восприятие 12"
                       value="<?= htmlspecialchars($editCreature['senses'] ?? '') ?>">
            </div>
            
            <div class="form-group">
                <label for="languages">Языки</label>
                <input type="text" id="languages" name="languages"
                       placeholder="Общий, Орочий, Гоблинский"
                       value="<?= htmlspecialchars($editCreature['languages'] ?? '') ?>">
            </div>
        </div>
        
        <h3>Способности и действия</h3>
        <div style="display: grid; grid-template-columns: 1fr; gap: 15px;">
            <div class="form-group">
                <label for="special_abilities">Особые способности</label>
                <textarea id="special_abilities" name="special_abilities" rows="4"
                          placeholder="Описание особых способностей существа..."><?= htmlspecialchars($editCreature['special_abilities'] ?? '') ?></textarea>
            </div>
            
            <div class="form-group">
                <label for="actions">Действия</label>
                <textarea id="actions" name="actions" rows="4"
                          placeholder="Описание действий в бою..."><?= htmlspecialchars($editCreature['actions'] ?? '') ?></textarea>
            </div>
            
            <div class="form-group">
                <label for="legendary_actions">Легендарные действия</label>
                <textarea id="legendary_actions" name="legendary_actions" rows="4"
                          placeholder="Легендарные действия (если есть)..."><?= htmlspecialchars($editCreature['legendary_actions'] ?? '') ?></textarea>
            </div>
        </div>
        
        <h3>Дополнительная информация</h3>
        <div style="display: grid; grid-template-columns: 1fr; gap: 15px;">
            <div class="form-group">
                <label for="description">Описание</label>
                <textarea id="description" name="description" rows="4"
                          placeholder="Общее описание существа, его поведение, история..."><?= htmlspecialchars($editCreature['description'] ?? '') ?></textarea>
            </div>
            
            <div class="form-group">
                <label for="habitat">Среда обитания</label>
                <input type="text" id="habitat" name="habitat"
                       placeholder="Леса, пещеры, подземелья..."
                       value="<?= htmlspecialchars($editCreature['habitat'] ?? '') ?>">
            </div>
        </div>
        
        <div style="margin-top: 20px;">
            <button type="submit" class="btn btn-success">
                <?= $editCreature ? 'Сохранить изменения' : 'Добавить существо' ?>
            </button>
            <?php if ($editCreature): ?>
                <a href="bestiary.php" class="btn btn-secondary">Отмена</a>
            <?php endif; ?>
        </div>
    </form>
</div>

<div class="card">
    <h2>Список существ в бестиарии</h2>
    
    <div class="form-group" style="margin-bottom: 20px;">
        <input type="text" id="searchTable" placeholder="Поиск по таблице..." 
               onkeyup="searchTable()" style="max-width: 300px;">
    </div>
    
    <div style="overflow-x: auto;">
        <table id="bestiaryTable">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Название</th>
                    <th>Тип</th>
                    <th>Размер</th>
                    <th>CR</th>
                    <th>XP</th>
                    <th>HP</th>
                    <th>AC</th>
                    <th>Действия</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($creatures)): ?>
                    <tr>
                        <td colspan="9" style="text-align: center;">Бестиарий пуст</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($creatures as $creature): ?>
                    <tr>
                        <td><?= $creature['creature_id'] ?></td>
                        <td><?= htmlspecialchars($creature['name']) ?></td>
                        <td><?= htmlspecialchars($creature['type']) ?></td>
                        <td><?= $sizeLabels[$creature['size']] ?? $creature['size'] ?></td>
                        <td><?= $creature['challenge_rating'] ?></td>
                        <td><?= $creature['experience_points'] ?></td>
                        <td><?= $creature['hp'] ?></td>
                        <td><?= $creature['armor_class'] ?></td>
                        <td style="white-space: nowrap;">
                            <a href="../public/bestiary-view.php?creature_id=<?= $creature['creature_id'] ?>" 
                               class="btn btn-secondary" style="padding: 5px 10px;" target="_blank">Просмотр</a>
                            <a href="bestiary.php?edit=<?= $creature['creature_id'] ?>" 
                               class="btn btn-primary" style="padding: 5px 10px;">Редактировать</a>
                            <a href="bestiary.php?delete=<?= $creature['creature_id'] ?>" 
                               class="btn btn-danger" style="padding: 5px 10px;"
                               onclick="return confirm('Удалить существо «<?= htmlspecialchars($creature['name']) ?>»?')">Удалить</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="card">
    <h2>Быстрое добавление стандартных существ</h2>
    <p>Нажмите на кнопку, чтобы добавить предустановленное существо в бестиарий:</p>
    
    <div style="display: flex; flex-wrap: wrap; gap: 10px; margin-top: 15px;">
        <button type="button" class="btn btn-secondary" onclick="fillTemplate('goblin')">Гоблин</button>
        <button type="button" class="btn btn-secondary" onclick="fillTemplate('skeleton')">Скелет</button>
        <button type="button" class="btn btn-secondary" onclick="fillTemplate('zombie')">Зомби</button>
        <button type="button" class="btn btn-secondary" onclick="fillTemplate('orc')">Орк</button>
        <button type="button" class="btn btn-secondary" onclick="fillTemplate('wolf')">Волк</button>
        <button type="button" class="btn btn-secondary" onclick="fillTemplate('ogre')">Огр</button>
        <button type="button" class="btn btn-secondary" onclick="fillTemplate('troll')">Тролль</button>
        <button type="button" class="btn btn-secondary" onclick="fillTemplate('dragon')">Молодой дракон</button>
    </div>
</div>

<script>
function searchTable() {
    const input = document.getElementById('searchTable');
    const filter = input.value.toLowerCase();
    const table = document.getElementById('bestiaryTable');
    const rows = table.getElementsByTagName('tr');
    
    for (let i = 1; i < rows.length; i++) {
        const cells = rows[i].getElementsByTagName('td');
        let found = false;
        
        for (let j = 0; j < cells.length - 1; j++) {
            if (cells[j].textContent.toLowerCase().indexOf(filter) > -1) {
                found = true;
                break;
            }
        }
        
        rows[i].style.display = found ? '' : 'none';
    }
}

const templates = {
    goblin: {
        name: 'Гоблин',
        type: 'Гуманоид',
        size: 'small',
        alignment: 'нейтрально-злой',
        challenge_rating: 0.25,
        experience_points: 50,
        hp: 7,
        armor_class: 15,
        speed: '30 ft.',
        strength: 8,
        dexterity: 14,
        constitution: 10,
        intelligence: 10,
        wisdom: 8,
        charisma: 8,
        senses: 'Тёмное зрение 60 ft.',
        languages: 'Общий, Гоблинский',
        special_abilities: 'Проворный побег: Гоблин может совершать Отход или Засаду бонусным действием в каждый свой ход.',
        actions: 'Ятаган: +4 к попаданию, досягаемость 5 ft., одна цель. Попадание: 5 (1d6+2) рубящего урона.\nКороткий лук: +4 к попаданию, дальность 80/320 ft., одна цель. Попадание: 5 (1d6+2) колющего урона.',
        description: 'Маленькие злобные гуманоиды, обитающие в тёмных местах. Гоблины трусливы поодиночке, но опасны в больших группах.',
        habitat: 'Пещеры, подземелья, леса'
    },
    skeleton: {
        name: 'Скелет',
        type: 'Нежить',
        size: 'medium',
        alignment: 'законно-злой',
        challenge_rating: 0.25,
        experience_points: 50,
        hp: 13,
        armor_class: 13,
        speed: '30 ft.',
        strength: 10,
        dexterity: 14,
        constitution: 15,
        intelligence: 6,
        wisdom: 8,
        charisma: 5,
        damage_vulnerabilities: 'дробящий',
        damage_immunities: 'яд',
        condition_immunities: 'отравление, истощение',
        senses: 'Тёмное зрение 60 ft.',
        languages: 'Понимает языки, которые знал при жизни, но не может говорить',
        actions: 'Короткий меч: +4 к попаданию, досягаемость 5 ft., одна цель. Попадание: 5 (1d6+2) колющего урона.\nКороткий лук: +4 к попаданию, дальность 80/320 ft., одна цель. Попадание: 5 (1d6+2) колющего урона.',
        description: 'Оживлённые магией кости умерших. Скелеты подчиняются командам своего создателя.',
        habitat: 'Кладбища, склепы, подземелья'
    },
    zombie: {
        name: 'Зомби',
        type: 'Нежить',
        size: 'medium',
        alignment: 'нейтрально-злой',
        challenge_rating: 0.25,
        experience_points: 50,
        hp: 22,
        armor_class: 8,
        speed: '20 ft.',
        strength: 13,
        dexterity: 6,
        constitution: 16,
        intelligence: 3,
        wisdom: 6,
        charisma: 5,
        damage_immunities: 'яд',
        condition_immunities: 'отравление',
        senses: 'Тёмное зрение 60 ft.',
        languages: 'Понимает языки, которые знал при жизни, но не может говорить',
        special_abilities: 'Стойкость нежити: Если урон уменьшает хиты зомби до 0, он должен совершить спасбросок Телосложения со Сл 5 + полученный урон, если только урон не был излучением или критическим попаданием. При успехе хиты зомби вместо этого становятся равны 1.',
        actions: 'Удар: +3 к попаданию, досягаемость 5 ft., одна цель. Попадание: 4 (1d6+1) дробящего урона.',
        description: 'Оживлённые тела умерших, подчиняющиеся воле своего создателя.',
        habitat: 'Кладбища, склепы, места тёмной магии'
    },
    orc: {
        name: 'Орк',
        type: 'Гуманоид',
        size: 'medium',
        alignment: 'хаотично-злой',
        challenge_rating: 0.5,
        experience_points: 100,
        hp: 15,
        armor_class: 13,
        speed: '30 ft.',
        strength: 16,
        dexterity: 12,
        constitution: 16,
        intelligence: 7,
        wisdom: 11,
        charisma: 10,
        senses: 'Тёмное зрение 60 ft.',
        languages: 'Общий, Орочий',
        special_abilities: 'Агрессия: Бонусным действием орк может переместиться на расстояние, не превышающее его скорость, к враждебному существу, которое он видит.',
        actions: 'Секира: +5 к попаданию, досягаемость 5 ft., одна цель. Попадание: 9 (1d12+3) рубящего урона.\nМетательное копьё: +5 к попаданию, дальность 30/120 ft., одна цель. Попадание: 6 (1d6+3) колющего урона.',
        description: 'Свирепые воины с серо-зелёной кожей. Орки живут войной и набегами.',
        habitat: 'Горы, леса, пустоши'
    },
    wolf: {
        name: 'Волк',
        type: 'Зверь',
        size: 'medium',
        alignment: 'без мировоззрения',
        challenge_rating: 0.25,
        experience_points: 50,
        hp: 11,
        armor_class: 13,
        speed: '40 ft.',
        strength: 12,
        dexterity: 15,
        constitution: 12,
        intelligence: 3,
        wisdom: 12,
        charisma: 6,
        senses: 'Пассивное восприятие 13',
        special_abilities: 'Тонкий слух и обоняние: Волк совершает с преимуществом проверки Мудрости (Внимательность), связанные со слухом или обонянием.\nТактика стаи: Волк совершает с преимуществом броски атаки по существу, если в пределах 5 футов от этого существа находится дееспособный союзник волка.',
        actions: 'Укус: +4 к попаданию, досягаемость 5 ft., одна цель. Попадание: 7 (2d4+2) колющего урона. Если цель — существо, она должна преуспеть в спасброске Силы со Сл 11, иначе будет сбита с ног.',
        description: 'Хищные звери, охотящиеся стаями. Волки опасны благодаря своей тактике.',
        habitat: 'Леса, равнины, горы'
    },
    ogre: {
        name: 'Огр',
        type: 'Великан',
        size: 'large',
        alignment: 'хаотично-злой',
        challenge_rating: 2,
        experience_points: 450,
        hp: 59,
        armor_class: 11,
        speed: '40 ft.',
        strength: 19,
        dexterity: 8,
        constitution: 16,
        intelligence: 5,
        wisdom: 7,
        charisma: 7,
        senses: 'Тёмное зрение 60 ft.',
        languages: 'Общий, Великаний',
        actions: 'Палица: +6 к попаданию, досягаемость 5 ft., одна цель. Попадание: 13 (2d8+4) дробящего урона.\nМетательное копьё: +6 к попаданию, дальность 30/120 ft., одна цель. Попадание: 11 (2d6+4) колющего урона.',
        description: 'Огромные тупые гуманоиды с ненасытным аппетитом. Огры едят всё, что могут поймать.',
        habitat: 'Холмы, пещеры, руины'
    },
    troll: {
        name: 'Тролль',
        type: 'Великан',
        size: 'large',
        alignment: 'хаотично-злой',
        challenge_rating: 5,
        experience_points: 1800,
        hp: 84,
        armor_class: 15,
        speed: '30 ft.',
        strength: 18,
        dexterity: 13,
        constitution: 20,
        intelligence: 7,
        wisdom: 9,
        charisma: 7,
        senses: 'Тёмное зрение 60 ft.',
        languages: 'Великаний',
        special_abilities: 'Тонкое обоняние: Тролль совершает с преимуществом проверки Мудрости (Внимательность), связанные с обонянием.\nРегенерация: Тролль восстанавливает 10 хитов в начале своего хода. Если тролль получает урон огнём или кислотой, эта особенность не работает в начале следующего хода тролля. Тролль умирает только если начинает ход с 0 хитами и не регенерирует.',
        actions: 'Мультиатака: Тролль совершает три атаки: одну укусом и две когтями.\nУкус: +7 к попаданию, досягаемость 5 ft., одна цель. Попадание: 7 (1d6+4) колющего урона.\nКогти: +7 к попаданию, досягаемость 5 ft., одна цель. Попадание: 11 (2d6+4) рубящего урона.',
        description: 'Уродливые великаны с невероятной способностью к регенерации. Боятся только огня и кислоты.',
        habitat: 'Болота, подземелья, горы',
        damage_vulnerabilities: '',
        damage_resistances: '',
        damage_immunities: '',
        condition_immunities: ''
    },
    dragon: {
        name: 'Молодой красный дракон',
        type: 'Дракон',
        size: 'large',
        alignment: 'хаотично-злой',
        challenge_rating: 10,
        experience_points: 5900,
        hp: 178,
        armor_class: 18,
        speed: '40 ft., climb 40 ft., fly 80 ft.',
        strength: 23,
        dexterity: 10,
        constitution: 21,
        intelligence: 14,
        wisdom: 11,
        charisma: 19,
        damage_immunities: 'огонь',
        senses: 'Слепое зрение 30 ft., тёмное зрение 120 ft.',
        languages: 'Общий, Драконий',
        special_abilities: 'Легендарное сопротивление (3/день): Если дракон проваливает спасбросок, он может вместо этого сделать его успешным.',
        actions: 'Мультиатака: Дракон совершает три атаки: одну укусом и две когтями.\nУкус: +10 к попаданию, досягаемость 10 ft., одна цель. Попадание: 17 (2d10+6) колющего урона плюс 3 (1d6) урона огнём.\nКоготь: +10 к попаданию, досягаемость 5 ft., одна цель. Попадание: 13 (2d6+6) рубящего урона.\nОгненное дыхание (перезарядка 5-6): Дракон выдыхает огонь 30-футовым конусом. Все существа в этой области должны совершить спасбросок Ловкости со Сл 17, получая 56 (16d6) урона огнём при провале, или половину этого урона при успехе.',
        legendary_actions: 'Дракон может совершить 3 легендарных действия, выбирая из представленных ниже вариантов. Только одно легендарное действие может быть использовано за раз, и только в конце хода другого существа.\nОбнаружение: Дракон совершает проверку Мудрости (Внимательность).\nАтака хвостом: Дракон совершает атаку хвостом.\nАтака крыльями (стоит 2 действия): Дракон бьёт крыльями. Все существа в пределах 10 футов должны преуспеть в спасброске Ловкости со Сл 19, иначе получат 13 (2d6+6) дробящего урона и будут сбиты с ног.',
        description: 'Красные драконы — самые жадные и высокомерные из истинных драконов. Они обожают сокровища и поклонение.',
        habitat: 'Горы, вулканы'
    }
};

function fillTemplate(type) {
    const template = templates[type];
    if (!template) return;
    
    document.getElementById('name').value = template.name || '';
    document.getElementById('type').value = template.type || '';
    document.getElementById('size').value = template.size || 'medium';
    document.getElementById('alignment').value = template.alignment || '';
    document.getElementById('challenge_rating').value = template.challenge_rating || 0;
    document.getElementById('experience_points').value = template.experience_points || 0;
    document.getElementById('hp').value = template.hp || 10;
    document.getElementById('armor_class').value = template.armor_class || 10;
    document.getElementById('speed').value = template.speed || '30 ft.';
    document.getElementById('strength').value = template.strength || 10;
    document.getElementById('dexterity').value = template.dexterity || 10;
    document.getElementById('constitution').value = template.constitution || 10;
    document.getElementById('intelligence').value = template.intelligence || 10;
    document.getElementById('wisdom').value = template.wisdom || 10;
    document.getElementById('charisma').value = template.charisma || 10;
    document.getElementById('damage_vulnerabilities').value = template.damage_vulnerabilities || '';
    document.getElementById('damage_resistances').value = template.damage_resistances || '';
    document.getElementById('damage_immunities').value = template.damage_immunities || '';
    document.getElementById('condition_immunities').value = template.condition_immunities || '';
    document.getElementById('senses').value = template.senses || '';
    document.getElementById('languages').value = template.languages || '';
    document.getElementById('special_abilities').value = template.special_abilities || '';
    document.getElementById('actions').value = template.actions || '';
    document.getElementById('legendary_actions').value = template.legendary_actions || '';
    document.getElementById('description').value = template.description || '';
    document.getElementById('habitat').value = template.habitat || '';
    
    window.scrollTo({top: 0, behavior: 'smooth'});
    
    document.getElementById('name').focus();
}
</script>

<?php require_once '../includes/footer.php'; ?>