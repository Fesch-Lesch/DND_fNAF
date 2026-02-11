-- phpMyAdmin SQL Dump
-- version 5.1.3-3.red80
-- https://www.phpmyadmin.net/
--
-- Хост: localhost
-- Время создания: Фев 09 2026 г., 06:53
-- Версия сервера: 10.11.11-MariaDB
-- Версия PHP: 8.1.32

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `DND-fNAF`
--

-- --------------------------------------------------------

--
-- Структура таблицы `BESTIARY`
--

CREATE TABLE `BESTIARY` (
  `creature_id` int(10) UNSIGNED NOT NULL,
  `name` varchar(50) NOT NULL,
  `type` varchar(30) NOT NULL,
  `size` enum('tiny','small','medium','large','huge','gargantuan') NOT NULL DEFAULT 'medium',
  `alignment` varchar(30) DEFAULT NULL,
  `challenge_rating` decimal(4,2) UNSIGNED NOT NULL DEFAULT 0.00,
  `experience_points` int(11) NOT NULL DEFAULT 0,
  `hp` int(11) NOT NULL DEFAULT 10,
  `armor_class` int(11) NOT NULL DEFAULT 10,
  `speed` varchar(100) NOT NULL DEFAULT '30 ft.',
  `strength` int(11) NOT NULL DEFAULT 10,
  `dexterity` int(11) NOT NULL DEFAULT 10,
  `constitution` int(11) NOT NULL DEFAULT 10,
  `intelligence` int(11) NOT NULL DEFAULT 10,
  `wisdom` int(11) NOT NULL DEFAULT 10,
  `charisma` int(11) NOT NULL DEFAULT 10,
  `damage_vulnerabilities` text DEFAULT NULL,
  `damage_resistances` text DEFAULT NULL,
  `damage_immunities` text DEFAULT NULL,
  `condition_immunities` text DEFAULT NULL,
  `senses` text DEFAULT NULL,
  `languages` text DEFAULT NULL,
  `special_abilities` text DEFAULT NULL,
  `actions` text DEFAULT NULL,
  `legendary_actions` text DEFAULT NULL,
  `description` text DEFAULT NULL,
  `habitat` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Дамп данных таблицы `BESTIARY`
--

INSERT INTO `BESTIARY` (`creature_id`, `name`, `type`, `size`, `alignment`, `challenge_rating`, `experience_points`, `hp`, `armor_class`, `speed`, `strength`, `dexterity`, `constitution`, `intelligence`, `wisdom`, `charisma`, `damage_vulnerabilities`, `damage_resistances`, `damage_immunities`, `condition_immunities`, `senses`, `languages`, `special_abilities`, `actions`, `legendary_actions`, `description`, `habitat`, `created_at`, `updated_at`) VALUES
(1, 'Гоблин', 'Гуманоид', 'small', 'нейтрально-злой', '0.25', 50, 7, 15, '30 ft.', 8, 14, 10, 10, 8, 8, NULL, NULL, NULL, NULL, 'Тёмное зрение 60 ft.', 'Общий, Гоблинский', 'Проворный побег', 'Ятаган: +4 к попаданию, 5 (1d6+2) рубящего урона', NULL, 'Маленькие злобные гуманоиды', 'Пещеры, подземелья, леса', '2026-02-02 06:45:57', '2026-02-02 06:45:57'),
(2, 'Скелет', 'Нежить', 'medium', 'законно-злой', '0.25', 50, 13, 13, '30 ft.', 10, 14, 15, 6, 8, 5, NULL, NULL, NULL, NULL, 'Тёмное зрение 60 ft.', 'Понимает языки при жизни', 'Иммунитет к усталости', 'Короткий меч: +4 к попаданию, 5 (1d6+2) колющего урона', NULL, 'Оживлённые магией кости', 'Кладбища, склепы', '2026-02-02 06:45:57', '2026-02-02 06:45:57'),
(3, 'Орк', 'Гуманоид', 'medium', 'хаотично-злой', '0.50', 100, 15, 13, '30 ft.', 16, 12, 16, 7, 11, 10, NULL, NULL, NULL, NULL, 'Тёмное зрение 60 ft.', 'Общий, Орочий', 'Агрессия', 'Боевой топор: +5 к попаданию, 9 (1d12+3) рубящего урона', NULL, 'Свирепые воины', 'Горы, леса', '2026-02-02 06:45:57', '2026-02-02 06:45:57'),
(4, 'Зомби', 'Нежить', 'medium', 'нейтрально-злой', '0.25', 50, 22, 8, '20 ft.', 13, 6, 16, 3, 6, 5, NULL, NULL, NULL, NULL, 'Тёмное зрение 60 ft.', 'Понимает языки при жизни', 'Стойкость нежити', 'Удар: +3 к попаданию, 4 (1d6+1) дробящего урона', NULL, 'Оживлённые тела умерших', 'Кладбища, склепы', '2026-02-02 06:45:57', '2026-02-02 06:45:57'),
(5, 'Волк', 'Зверь', 'medium', 'без мировоззрения', '0.25', 50, 11, 13, '40 ft.', 12, 15, 12, 3, 12, 6, NULL, NULL, NULL, NULL, 'Пассивное восприятие 13', NULL, 'Тактика стаи', 'Укус: +4 к попаданию, 7 (2d4+2) колющего урона', NULL, 'Хищные звери', 'Леса, равнины', '2026-02-02 06:45:57', '2026-02-02 06:45:57'),
(6, 'Огр', 'Великан', 'large', 'хаотично-злой', '2.00', 450, 59, 11, '40 ft.', 19, 8, 16, 5, 7, 7, NULL, NULL, NULL, NULL, 'Тёмное зрение 60 ft.', 'Общий, Великаний', NULL, 'Палица: +6 к попаданию, 13 (2d8+4) дробящего урона', NULL, 'Огромные тупые гуманоиды', 'Холмы, пещеры', '2026-02-02 06:45:57', '2026-02-02 06:45:57'),
(7, 'Кобольд', 'Гуманоид', 'small', 'законно-злой', '0.13', 25, 5, 12, '30 ft.', 7, 15, 9, 8, 7, 8, NULL, NULL, NULL, NULL, 'Тёмное зрение 60 ft.', 'Общий, Драконий', 'Тактика стаи', 'Кинжал: +4 к попаданию, 4 (1d4+2) колющего урона', NULL, 'Маленькие рептилоидные существа', 'Подземелья, шахты', '2026-02-02 06:45:57', '2026-02-02 06:45:57'),
(8, 'Медведь', 'Зверь', 'large', 'без мировоззрения', '1.00', 200, 34, 11, '40 ft., climb 30 ft.', 19, 10, 16, 2, 13, 7, NULL, NULL, NULL, NULL, 'Пассивное восприятие 13', NULL, 'Тонкое обоняние', 'Мультиатака: две атаки когтями', NULL, 'Большой хищный зверь', 'Леса, горы', '2026-02-02 06:45:57', '2026-02-02 06:45:57'),
(9, 'Слизь', 'Слизь', 'large', 'без мировоззрения', '0.50', 100, 22, 8, '10 ft., climb 10 ft.', 12, 6, 16, 1, 6, 2, NULL, NULL, NULL, NULL, 'Слепое зрение 60 ft.', NULL, 'Аморфная форма', 'Ложноножка: +3 к попаданию, 4 (1d6+1) кислотного урона', NULL, 'Бесформенная кислотная масса', 'Подземелья, канализация', '2026-02-02 06:45:57', '2026-02-02 06:45:57'),
(10, 'Минотавр', 'Чудовище', 'large', 'хаотично-злой', '3.00', 700, 76, 14, '40 ft.', 18, 11, 16, 6, 16, 9, NULL, NULL, NULL, NULL, 'Тёмное зрение 60 ft.', 'Бездны', 'Атака с разбега', 'Рога: +6 к попаданию, 13 (2d8+4) колющего урона', NULL, 'Человек-бык с топором', 'Лабиринты, подземелья', '2026-02-02 06:45:57', '2026-02-02 06:45:57');

-- --------------------------------------------------------

--
-- Структура таблицы `CHARACTERS`
--

CREATE TABLE `CHARACTERS` (
  `character_id` int(10) UNSIGNED NOT NULL,
  `name` varchar(20) NOT NULL,
  `race` varchar(20) NOT NULL,
  `class` varchar(20) NOT NULL,
  `level` int(11) NOT NULL DEFAULT 1,
  `hp` int(11) NOT NULL DEFAULT 10,
  `armor` int(11) NOT NULL DEFAULT 10,
  `strength` int(11) NOT NULL DEFAULT 10,
  `dexterity` int(11) NOT NULL DEFAULT 10,
  `constitution` int(11) NOT NULL DEFAULT 10,
  `intelligence` int(11) NOT NULL DEFAULT 10,
  `wisdom` int(11) NOT NULL DEFAULT 10,
  `charisma` int(11) NOT NULL DEFAULT 10,
  `ability1` text DEFAULT NULL,
  `ability2` text DEFAULT NULL,
  `ability3` text DEFAULT NULL,
  `item1` text DEFAULT NULL,
  `item2` text DEFAULT NULL,
  `item3` text DEFAULT NULL,
  `initiative` int(11) NOT NULL DEFAULT 0,
  `speed` int(11) NOT NULL DEFAULT 30,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Дамп данных таблицы `CHARACTERS`
--

INSERT INTO `CHARACTERS` (`character_id`, `name`, `race`, `class`, `level`, `hp`, `armor`, `strength`, `dexterity`, `constitution`, `intelligence`, `wisdom`, `charisma`, `ability1`, `ability2`, `ability3`, `item1`, `item2`, `item3`, `initiative`, `speed`, `created_at`, `updated_at`) VALUES
(1, 'Торин', 'Дварф', 'Воин', 3, 35, 16, 16, 12, 15, 10, 12, 8, 'Второе дыхание', 'Всплеск действий', NULL, 'Боевой топор +1', 'Кольчуга', 'Щит', 1, 25, '2026-02-02 06:45:57', '2026-02-02 06:45:57'),
(2, 'Эларин', 'Эльф', 'Маг', 3, 18, 12, 8, 14, 12, 17, 13, 10, 'Волшебные стрелы', 'Щит', 'Обнаружение магии', 'Посох силы', 'Мантия защиты', 'Книга заклинаний', 2, 30, '2026-02-02 06:45:57', '2026-02-02 06:45:57'),
(3, 'Рагнар', 'Полуорк', 'Варвар', 3, 42, 14, 18, 13, 16, 8, 10, 10, 'Ярость', 'Безрассудная атака', 'Опасное чутьё', 'Двуручный меч', 'Шкура медведя', NULL, 1, 40, '2026-02-02 06:45:57', '2026-02-02 06:45:57'),
(4, 'Лира', 'Полурослик', 'Плут', 3, 24, 15, 10, 18, 12, 14, 12, 14, 'Скрытая атака', 'Воровской жаргон', 'Хитрое действие', 'Кинжал +1', 'Кожаный доспех', 'Воровские инструменты', 4, 25, '2026-02-02 06:45:57', '2026-02-02 06:45:57');

--
-- Триггеры `CHARACTERS`
--
DELIMITER $$
CREATE TRIGGER `trg_log_characters_update` AFTER UPDATE ON `CHARACTERS` FOR EACH ROW BEGIN
    INSERT INTO LOG_CHANGES (
        user_id,
        username,
        table_name,
        record_id,
        action_type,
        old_values,
        new_values
    ) VALUES (
        @current_user_id,
        @current_username,
        'CHARACTERS',
        NEW.character_id,
        'UPDATE',
        JSON_OBJECT(
            'name', OLD.name,
            'race', OLD.race,
            'class', OLD.class,
            'level', OLD.level,
            'hp', OLD.hp,
            'armor', OLD.armor
        ),
        JSON_OBJECT(
            'name', NEW.name,
            'race', NEW.race,
            'class', NEW.class,
            'level', NEW.level,
            'hp', NEW.hp,
            'armor', NEW.armor
        )
    );
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Структура таблицы `LOG_CHANGES`
--

CREATE TABLE `LOG_CHANGES` (
  `log_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED DEFAULT NULL,
  `username` varchar(50) DEFAULT NULL,
  `table_name` varchar(50) NOT NULL,
  `record_id` int(10) UNSIGNED NOT NULL,
  `action_type` enum('INSERT','UPDATE','DELETE') NOT NULL,
  `old_values` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`old_values`)),
  `new_values` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`new_values`)),
  `change_time` timestamp NULL DEFAULT current_timestamp(),
  `ip_address` varchar(45) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `LOG_LOGINS`
--

CREATE TABLE `LOG_LOGINS` (
  `log_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `username` varchar(50) NOT NULL,
  `login_time` timestamp NULL DEFAULT current_timestamp(),
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `status` enum('success','failed') NOT NULL DEFAULT 'success'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Дамп данных таблицы `LOG_LOGINS`
--

INSERT INTO `LOG_LOGINS` (`log_id`, `user_id`, `username`, `login_time`, `ip_address`, `user_agent`, `status`) VALUES
(1, 1, 'admin', '2026-02-02 07:42:20', '::1', 'Mozilla/5.0 (X11; Linux x86_64; rv:109.0) Gecko/20100101 Firefox/115.0', 'success'),
(2, 1, 'admin', '2026-02-02 08:07:16', '::1', 'Mozilla/5.0 (X11; Linux x86_64; rv:109.0) Gecko/20100101 Firefox/115.0', 'failed'),
(3, 1, 'admin', '2026-02-02 08:07:24', '::1', 'Mozilla/5.0 (X11; Linux x86_64; rv:109.0) Gecko/20100101 Firefox/115.0', 'failed'),
(4, 1, 'admin', '2026-02-02 08:07:28', '::1', 'Mozilla/5.0 (X11; Linux x86_64; rv:109.0) Gecko/20100101 Firefox/115.0', 'success'),
(5, 1, 'admin', '2026-02-09 05:50:02', '::1', 'Mozilla/5.0 (X11; Linux x86_64; rv:109.0) Gecko/20100101 Firefox/115.0', 'success');

-- --------------------------------------------------------

--
-- Структура таблицы `LOG_SCORE_CHANGES`
--

CREATE TABLE `LOG_SCORE_CHANGES` (
  `log_id` bigint(20) UNSIGNED NOT NULL,
  `student_id` int(10) UNSIGNED NOT NULL,
  `student_name` varchar(80) NOT NULL,
  `team_id` int(10) UNSIGNED DEFAULT NULL,
  `old_score` int(10) UNSIGNED NOT NULL,
  `new_score` int(10) UNSIGNED NOT NULL,
  `score_difference` int(11) NOT NULL,
  `changed_by_user_id` int(10) UNSIGNED DEFAULT NULL,
  `changed_by_username` varchar(50) DEFAULT NULL,
  `reason` text DEFAULT NULL,
  `change_time` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `STUDENTS`
--

CREATE TABLE `STUDENTS` (
  `student_id` int(10) UNSIGNED NOT NULL,
  `team_id` int(10) UNSIGNED DEFAULT NULL,
  `first_name` varchar(20) NOT NULL,
  `last_name` varchar(30) NOT NULL,
  `middle_name` varchar(30) DEFAULT NULL,
  `score` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Дамп данных таблицы `STUDENTS`
--

INSERT INTO `STUDENTS` (`student_id`, `team_id`, `first_name`, `last_name`, `middle_name`, `score`, `created_at`, `updated_at`) VALUES
(1, 1, 'Иван', 'Петров', 'Сергеевич', 85, '2026-02-02 06:45:57', '2026-02-02 06:45:57'),
(2, 1, 'Мария', 'Сидорова', 'Александровна', 92, '2026-02-02 06:45:57', '2026-02-02 06:45:57'),
(3, 1, 'Алексей', 'Козлов', 'Дмитриевич', 78, '2026-02-02 06:45:57', '2026-02-02 06:45:57'),
(4, 2, 'Елена', 'Новикова', 'Игоревна', 88, '2026-02-02 06:45:57', '2026-02-02 06:45:57'),
(5, 2, 'Дмитрий', 'Волков', NULL, 95, '2026-02-02 06:45:57', '2026-02-02 06:45:57'),
(6, 2, 'Анна', 'Морозова', 'Павловна', 82, '2026-02-02 06:45:57', '2026-02-02 06:45:57'),
(7, 3, 'Сергей', 'Лебедев', 'Андреевич', 90, '2026-02-02 06:45:57', '2026-02-02 06:45:57'),
(8, 3, 'Ольга', 'Соколова', 'Викторовна', 87, '2026-02-02 06:45:57', '2026-02-02 06:45:57'),
(9, 3, 'Николай', 'Кузнецов', NULL, 91, '2026-02-02 06:45:57', '2026-02-02 06:45:57'),
(10, 4, 'Татьяна', 'Попова', 'Олеговна', 79, '2026-02-02 06:45:57', '2026-02-02 06:45:57'),
(11, 4, 'Андрей', 'Васильев', 'Николаевич', 84, '2026-02-02 06:45:57', '2026-02-02 06:45:57'),
(12, 4, 'Екатерина', 'Павлова', 'Сергеевна', 93, '2026-02-02 06:45:57', '2026-02-02 06:45:57');

--
-- Триггеры `STUDENTS`
--
DELIMITER $$
CREATE TRIGGER `trg_log_score_change` AFTER UPDATE ON `STUDENTS` FOR EACH ROW BEGIN
    IF OLD.score != NEW.score THEN
        INSERT INTO LOG_SCORE_CHANGES (
            student_id,
            student_name,
            team_id,
            old_score,
            new_score,
            score_difference,
            changed_by_user_id,
            changed_by_username
        ) VALUES (
            NEW.student_id,
            CONCAT(NEW.last_name, ' ', NEW.first_name, COALESCE(CONCAT(' ', NEW.middle_name), '')),
            NEW.team_id,
            OLD.score,
            NEW.score,
            CAST(NEW.score AS SIGNED) - CAST(OLD.score AS SIGNED),
            @current_user_id,
            @current_username
        );
    END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_students_after_delete` AFTER DELETE ON `STUDENTS` FOR EACH ROW BEGIN
    IF OLD.team_id IS NOT NULL THEN
        UPDATE TEAMS 
        SET amount = (
            SELECT COALESCE(SUM(score), 0) 
            FROM STUDENTS 
            WHERE team_id = OLD.team_id
        )
        WHERE team_id = OLD.team_id;
    END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_students_after_insert` AFTER INSERT ON `STUDENTS` FOR EACH ROW BEGIN
    IF NEW.team_id IS NOT NULL THEN
        UPDATE TEAMS 
        SET amount = (
            SELECT COALESCE(SUM(score), 0) 
            FROM STUDENTS 
            WHERE team_id = NEW.team_id
        )
        WHERE team_id = NEW.team_id;
    END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_students_after_update` AFTER UPDATE ON `STUDENTS` FOR EACH ROW BEGIN
    IF OLD.team_id IS NOT NULL AND (OLD.team_id != NEW.team_id OR NEW.team_id IS NULL) THEN
        UPDATE TEAMS 
        SET amount = (
            SELECT COALESCE(SUM(score), 0) 
            FROM STUDENTS 
            WHERE team_id = OLD.team_id
        )
        WHERE team_id = OLD.team_id;
    END IF;
    
    IF NEW.team_id IS NOT NULL THEN
        UPDATE TEAMS 
        SET amount = (
            SELECT COALESCE(SUM(score), 0) 
            FROM STUDENTS 
            WHERE team_id = NEW.team_id
        )
        WHERE team_id = NEW.team_id;
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Структура таблицы `TEAMS`
--

CREATE TABLE `TEAMS` (
  `team_id` int(10) UNSIGNED NOT NULL,
  `character_id` int(10) UNSIGNED DEFAULT NULL,
  `team_color` varchar(15) NOT NULL,
  `amount` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `inspiration` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Дамп данных таблицы `TEAMS`
--

INSERT INTO `TEAMS` (`team_id`, `character_id`, `team_color`, `amount`, `inspiration`, `created_at`, `updated_at`) VALUES
(1, 1, 'Красный', 255, 2, '2026-02-02 06:45:57', '2026-02-02 06:45:57'),
(2, 2, 'Синий', 265, 1, '2026-02-02 06:45:57', '2026-02-02 06:45:57'),
(3, 3, 'Зелёный', 268, 3, '2026-02-02 06:45:57', '2026-02-02 06:45:57'),
(4, 4, 'Жёлтый', 256, 0, '2026-02-02 06:45:57', '2026-02-02 06:45:57');

--
-- Триггеры `TEAMS`
--
DELIMITER $$
CREATE TRIGGER `trg_log_teams_update` AFTER UPDATE ON `TEAMS` FOR EACH ROW BEGIN
    IF OLD.team_color != NEW.team_color OR OLD.inspiration != NEW.inspiration 
       OR OLD.character_id != NEW.character_id THEN
        INSERT INTO LOG_CHANGES (
            user_id,
            username,
            table_name,
            record_id,
            action_type,
            old_values,
            new_values
        ) VALUES (
            @current_user_id,
            @current_username,
            'TEAMS',
            NEW.team_id,
            'UPDATE',
            JSON_OBJECT(
                'team_color', OLD.team_color,
                'inspiration', OLD.inspiration,
                'character_id', OLD.character_id
            ),
            JSON_OBJECT(
                'team_color', NEW.team_color,
                'inspiration', NEW.inspiration,
                'character_id', NEW.character_id
            )
        );
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Структура таблицы `USERS`
--

CREATE TABLE `USERS` (
  `user_id` int(10) UNSIGNED NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `role` enum('admin','teacher','captain') NOT NULL DEFAULT 'captain',
  `student_id` int(10) UNSIGNED DEFAULT NULL,
  `team_id` int(10) UNSIGNED DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `last_login` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Дамп данных таблицы `USERS`
--

INSERT INTO `USERS` (`user_id`, `username`, `email`, `password_hash`, `role`, `student_id`, `team_id`, `is_active`, `last_login`, `created_at`, `updated_at`) VALUES
(1, 'admin', 'admin@school.edu', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', NULL, NULL, 1, '2026-02-09 05:50:02', '2026-02-02 06:45:57', '2026-02-09 05:50:02'),
(2, 'teacher1', 'teacher1@school.edu', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'teacher', NULL, NULL, 1, NULL, '2026-02-02 06:45:57', '2026-02-02 06:45:57'),
(3, 'teacher2', 'teacher2@school.edu', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'teacher', NULL, NULL, 1, NULL, '2026-02-02 06:45:57', '2026-02-02 06:45:57'),
(4, 'captain_red', 'petrov@school.edu', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'captain', 1, 1, 1, NULL, '2026-02-02 06:45:57', '2026-02-02 06:45:57'),
(5, 'captain_blue', 'novikova@school.edu', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'captain', 4, 2, 1, NULL, '2026-02-02 06:45:57', '2026-02-02 06:45:57'),
(6, 'captain_green', 'lebedev@school.edu', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'captain', 7, 3, 1, NULL, '2026-02-02 06:45:57', '2026-02-02 06:45:57'),
(7, 'captain_yellow', 'popova@school.edu', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'captain', 10, 4, 1, NULL, '2026-02-02 06:45:57', '2026-02-02 06:45:57');

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `BESTIARY`
--
ALTER TABLE `BESTIARY`
  ADD PRIMARY KEY (`creature_id`),
  ADD KEY `idx_name` (`name`),
  ADD KEY `idx_type` (`type`),
  ADD KEY `idx_challenge_rating` (`challenge_rating`);

--
-- Индексы таблицы `CHARACTERS`
--
ALTER TABLE `CHARACTERS`
  ADD PRIMARY KEY (`character_id`);

--
-- Индексы таблицы `LOG_CHANGES`
--
ALTER TABLE `LOG_CHANGES`
  ADD PRIMARY KEY (`log_id`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_table_name` (`table_name`),
  ADD KEY `idx_change_time` (`change_time`),
  ADD KEY `idx_action_type` (`action_type`);

--
-- Индексы таблицы `LOG_LOGINS`
--
ALTER TABLE `LOG_LOGINS`
  ADD PRIMARY KEY (`log_id`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_login_time` (`login_time`);

--
-- Индексы таблицы `LOG_SCORE_CHANGES`
--
ALTER TABLE `LOG_SCORE_CHANGES`
  ADD PRIMARY KEY (`log_id`),
  ADD KEY `idx_student_id` (`student_id`),
  ADD KEY `idx_team_id` (`team_id`),
  ADD KEY `idx_change_time` (`change_time`);

--
-- Индексы таблицы `STUDENTS`
--
ALTER TABLE `STUDENTS`
  ADD PRIMARY KEY (`student_id`),
  ADD KEY `idx_team_id` (`team_id`),
  ADD KEY `idx_last_name` (`last_name`);

--
-- Индексы таблицы `TEAMS`
--
ALTER TABLE `TEAMS`
  ADD PRIMARY KEY (`team_id`),
  ADD UNIQUE KEY `character_id` (`character_id`);

--
-- Индексы таблицы `USERS`
--
ALTER TABLE `USERS`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `fk_users_student` (`student_id`),
  ADD KEY `fk_users_team` (`team_id`),
  ADD KEY `idx_role` (`role`),
  ADD KEY `idx_username` (`username`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `BESTIARY`
--
ALTER TABLE `BESTIARY`
  MODIFY `creature_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT для таблицы `CHARACTERS`
--
ALTER TABLE `CHARACTERS`
  MODIFY `character_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT для таблицы `LOG_CHANGES`
--
ALTER TABLE `LOG_CHANGES`
  MODIFY `log_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `LOG_LOGINS`
--
ALTER TABLE `LOG_LOGINS`
  MODIFY `log_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT для таблицы `LOG_SCORE_CHANGES`
--
ALTER TABLE `LOG_SCORE_CHANGES`
  MODIFY `log_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `STUDENTS`
--
ALTER TABLE `STUDENTS`
  MODIFY `student_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT для таблицы `TEAMS`
--
ALTER TABLE `TEAMS`
  MODIFY `team_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT для таблицы `USERS`
--
ALTER TABLE `USERS`
  MODIFY `user_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Ограничения внешнего ключа сохраненных таблиц
--

--
-- Ограничения внешнего ключа таблицы `LOG_LOGINS`
--
ALTER TABLE `LOG_LOGINS`
  ADD CONSTRAINT `fk_log_logins_user` FOREIGN KEY (`user_id`) REFERENCES `USERS` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ограничения внешнего ключа таблицы `STUDENTS`
--
ALTER TABLE `STUDENTS`
  ADD CONSTRAINT `fk_students_team` FOREIGN KEY (`team_id`) REFERENCES `TEAMS` (`team_id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Ограничения внешнего ключа таблицы `TEAMS`
--
ALTER TABLE `TEAMS`
  ADD CONSTRAINT `fk_teams_character` FOREIGN KEY (`character_id`) REFERENCES `CHARACTERS` (`character_id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Ограничения внешнего ключа таблицы `USERS`
--
ALTER TABLE `USERS`
  ADD CONSTRAINT `fk_users_student` FOREIGN KEY (`student_id`) REFERENCES `STUDENTS` (`student_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_users_team` FOREIGN KEY (`team_id`) REFERENCES `TEAMS` (`team_id`) ON DELETE SET NULL ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
