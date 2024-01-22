-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Хост: 127.0.0.1:3306
-- Время создания: Янв 21 2024 г., 17:48
-- Версия сервера: 8.0.30
-- Версия PHP: 8.1.9

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `test_samson`
--

-- --------------------------------------------------------

--
-- Структура таблицы `a_category`
--

CREATE TABLE `a_category` (
  `id_r` int NOT NULL,
  `code` int NOT NULL,
  `code_r` int NOT NULL,
  `name_r` varchar(255) NOT NULL,
  `parent` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `a_price`
--

CREATE TABLE `a_price` (
  `id_p` int NOT NULL,
  `code` int NOT NULL,
  `type` varchar(255) NOT NULL,
  `price` decimal(8,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `a_product`
--

CREATE TABLE `a_product` (
  `id` int NOT NULL,
  `code` int NOT NULL,
  `name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `a_property`
--

CREATE TABLE `a_property` (
  `id` int NOT NULL,
  `property` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `type` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `value_type` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `a_value_property`
--

CREATE TABLE `a_value_property` (
  `id` int NOT NULL,
  `code` int NOT NULL,
  `id_p` int NOT NULL,
  `value` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `a_category`
--
ALTER TABLE `a_category`
  ADD PRIMARY KEY (`id_r`),
  ADD KEY `id_r` (`id_r`),
  ADD KEY `parent` (`parent`),
  ADD KEY `code` (`code`);

--
-- Индексы таблицы `a_price`
--
ALTER TABLE `a_price`
  ADD PRIMARY KEY (`id_p`),
  ADD KEY `code` (`code`);

--
-- Индексы таблицы `a_product`
--
ALTER TABLE `a_product`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`);

--
-- Индексы таблицы `a_property`
--
ALTER TABLE `a_property`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `a_value_property`
--
ALTER TABLE `a_value_property`
  ADD PRIMARY KEY (`id`),
  ADD KEY `code` (`code`),
  ADD KEY `id_p` (`id_p`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `a_category`
--
ALTER TABLE `a_category`
  MODIFY `id_r` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `a_price`
--
ALTER TABLE `a_price`
  MODIFY `id_p` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `a_product`
--
ALTER TABLE `a_product`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `a_property`
--
ALTER TABLE `a_property`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `a_value_property`
--
ALTER TABLE `a_value_property`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- Ограничения внешнего ключа сохраненных таблиц
--

--
-- Ограничения внешнего ключа таблицы `a_category`
--
ALTER TABLE `a_category`
  ADD CONSTRAINT `a_category_ibfk_1` FOREIGN KEY (`code`) REFERENCES `a_product` (`code`) ON DELETE RESTRICT ON UPDATE RESTRICT;

--
-- Ограничения внешнего ключа таблицы `a_price`
--
ALTER TABLE `a_price`
  ADD CONSTRAINT `a_price_ibfk_1` FOREIGN KEY (`code`) REFERENCES `a_product` (`code`) ON DELETE RESTRICT ON UPDATE RESTRICT;

--
-- Ограничения внешнего ключа таблицы `a_value_property`
--
ALTER TABLE `a_value_property`
  ADD CONSTRAINT `a_value_property_ibfk_1` FOREIGN KEY (`code`) REFERENCES `a_product` (`code`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `a_value_property_ibfk_2` FOREIGN KEY (`id_p`) REFERENCES `a_property` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
