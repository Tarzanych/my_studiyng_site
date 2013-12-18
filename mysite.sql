-- phpMyAdmin SQL Dump
-- version 4.0.6deb1
-- http://www.phpmyadmin.net
--
-- Хост: localhost
-- Час створення: Лис 16 2013 р., 10:12
-- Версія сервера: 5.5.34-0ubuntu0.13.10.1
-- Версія PHP: 5.5.3-1ubuntu2

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- База даних: `mysite`
--

-- --------------------------------------------------------

--
-- Структура таблиці `category`
--

CREATE TABLE IF NOT EXISTS `category` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `url` varchar(255) NOT NULL,
  `parent` int(11) NOT NULL,
  `description` mediumtext NOT NULL,
  `createTime` int(11) NOT NULL,
  `createUser` int(11) NOT NULL,
  `publish` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=11 ;

--
-- Дамп даних таблиці `category`
--

INSERT INTO `category` (`id`, `title`, `url`, `parent`, `description`, `createTime`, `createUser`, `publish`) VALUES
(1, 'Category 1', 'category1', 0, '', 1383581406, 6, 1),
(2, 'Category 2', 'cat2', 0, '', 1383581449, 6, 1),
(3, 'Category 3', 'cat3', 0, '<p>Description description description description</p>\r\n<p>description description description description</p>\r\n<p>description description description</p>', 1383581488, 6, 1),
(5, 'Category 5', 'cat5', 2, '<p>Test</p>', 1383586251, 6, 1),
(6, 'Category 6', 'cat6', 5, '', 1383586304, 6, 1),
(10, 'Category 7', 'cat7', 6, '', 1383665995, 6, 1);

-- --------------------------------------------------------

--
-- Структура таблиці `comments`
--

CREATE TABLE IF NOT EXISTS `comments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `content_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `time` int(11) NOT NULL,
  `ip` varchar(15) CHARACTER SET utf8 NOT NULL,
  `text` mediumtext CHARACTER SET utf8 NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=cp1251 AUTO_INCREMENT=21 ;

--
-- Дамп даних таблиці `comments`
--

INSERT INTO `comments` (`id`, `content_id`, `user_id`, `time`, `ip`, `text`) VALUES
(1, 33, 6, 1383977173, '', 'asf as asf '),
(2, 33, -1, 1383984758, '127.0.0.1', 'фвыафыва'),
(3, 33, 6, 1383984844, '127.0.0.1', 'asdf sadf sadf asf'),
(4, 33, 6, 1383984850, '127.0.0.1', 'asdfsadf asdf'),
(5, 33, 6, 1383984862, '127.0.0.1', 'asdf asf'),
(6, 33, 6, 1383984864, '127.0.0.1', 'as dfas dfadsf'),
(7, 33, 6, 1383984864, '127.0.0.1', 'as dfas dfadsf'),
(8, 33, 6, 1383984866, '127.0.0.1', 'asdf asfd asfd asd'),
(9, 33, 6, 1383984866, '127.0.0.1', 'asdf asfd asfd asd'),
(17, 33, 6, 1383986161, '127.0.0.1', 'jfj'),
(20, 33, 6, 1383986340, '127.0.0.1', 'k;lkk');

-- --------------------------------------------------------

--
-- Структура таблиці `content`
--

CREATE TABLE IF NOT EXISTS `content` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `url` varchar(255) NOT NULL,
  `author` int(11) NOT NULL,
  `createTime` int(11) NOT NULL,
  `publish` tinyint(1) NOT NULL,
  `publishTime` int(11) NOT NULL,
  `onFront` tinyint(1) NOT NULL,
  `preText` mediumtext NOT NULL,
  `totalText` mediumtext NOT NULL,
  `rating` float(3,2) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=42 ;

--
-- Дамп даних таблиці `content`
--

INSERT INTO `content` (`id`, `category_id`, `title`, `url`, `author`, `createTime`, `publish`, `publishTime`, `onFront`, `preText`, `totalText`, `rating`) VALUES
(39, 0, 'Title', 'title', 6, 1384288906, 1, 1384288906, 1, '', '', 0.00),
(40, 10, '', 'url', 6, 1384553595, 1, 1384553595, 1, '', '', 0.00),
(41, 10, '', 'title3', 6, 1384579891, 1, 1384579891, 1, '', '', 0.00);

-- --------------------------------------------------------

--
-- Структура таблиці `content_language`
--

CREATE TABLE IF NOT EXISTS `content_language` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `content_id` int(11) NOT NULL,
  `language_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `preText` mediumtext NOT NULL,
  `totalText` mediumtext NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=11 ;

--
-- Дамп даних таблиці `content_language`
--

INSERT INTO `content_language` (`id`, `content_id`, `language_id`, `title`, `preText`, `totalText`) VALUES
(5, 39, 1, 'Title', '<p>1111</p>', '<p>2222</p>'),
(6, 39, 2, 'Заголовок', '<p>3333</p>', '<p>4444</p>'),
(7, 40, 1, 'Title 2', '<p>asdfasdfaааа</p>', '<p>фывафыв ыфваыфва</p>'),
(8, 40, 2, 'Заголовок 2', '', '<p>фы вфвыа фвыа ыфва</p>'),
(9, 41, 1, 'Title 3', '', '<p>Test</p>'),
(10, 41, 2, 'Заголовок 3', '', '<p>Тест</p>');

-- --------------------------------------------------------

--
-- Структура таблиці `languageConstants`
--

CREATE TABLE IF NOT EXISTS `languageConstants` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `var` varchar(255) NOT NULL,
  `val` mediumtext NOT NULL,
  `language` int(11) NOT NULL,
  `isHtml` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=41 ;

--
-- Дамп даних таблиці `languageConstants`
--

INSERT INTO `languageConstants` (`id`, `var`, `val`, `language`, `isHtml`) VALUES
(1, 'WELCOME', 'Welcome', 1, 0),
(2, 'WELCOME', 'Ласкаво просимо', 2, 0),
(3, 'SEARCH', 'Search', 1, 0),
(4, 'SEARCH', 'Пошук', 2, 0),
(5, 'ADMIN_PANEL', 'Administration panel', 1, 0),
(6, 'ADMIN_PANEL', 'Панель керування', 2, 0),
(7, 'NICKNAME', 'Login', 1, 0),
(8, 'NICKNAME', 'Логін', 2, 0),
(9, 'PASSWORD', 'Password', 1, 0),
(10, 'PASSWORD', 'Пароль', 2, 0),
(11, 'ENTER', 'Log In', 1, 0),
(12, 'ENTER', 'Увійти', 2, 0),
(13, 'BLOCKED', 'blocked', 1, 0),
(14, 'BLOCKED', 'заблоковано', 2, 0),
(15, 'LOGOUT', 'Logout', 1, 0),
(16, 'LOGOUT', 'Вийти', 2, 0),
(17, 'NOT_ACTIVE', 'not active', 1, 0),
(18, 'NOT_ACTIVE', 'неактивний профіль', 2, 0),
(19, 'REGISTER', 'Register', 1, 0),
(20, 'REGISTER', 'Зареєструватись', 2, 0),
(21, 'FIRSTNAME', 'First name', 1, 0),
(22, 'FIRSTNAME', 'Ім''я', 2, 0),
(23, 'SECONDNAME', 'Second name', 1, 0),
(24, 'SECONDNAME', 'Прізвище', 2, 0),
(25, 'PASSCONFIRM', 'Password confirm', 1, 0),
(26, 'PASSCONFIRM', 'Підтвердження паролю', 2, 0),
(27, 'FROM', 'from', 1, 0),
(28, 'FROM', 'від', 2, 0),
(29, 'TO', 'to', 1, 0),
(30, 'TO', 'до', 2, 0),
(31, 'SYMBOLS', 'symbols', 1, 0),
(32, 'SYMBOLS', 'символів', 2, 0),
(33, 'REG_NEW', 'Registering new account', 1, 0),
(34, 'REG_NEW', 'Реєстрація нового користувача', 2, 0),
(35, 'SOME_ERRORS', 'Sorry, there are some errors', 1, 0),
(36, 'SOME_ERRORS', 'Sorry, there are some errors', 2, 0),
(37, 'NAME', 'Name', 1, 0),
(38, 'NAME', 'Ім''я', 2, 0),
(39, 'VARIABLE', 'Variable', 1, 0),
(40, 'VARIABLE', 'Змінна', 2, 0);

-- --------------------------------------------------------

--
-- Структура таблиці `languages`
--

CREATE TABLE IF NOT EXISTS `languages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `abbr` varchar(3) NOT NULL,
  `flag` varchar(255) NOT NULL,
  `default` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- Дамп даних таблиці `languages`
--

INSERT INTO `languages` (`id`, `title`, `abbr`, `flag`, `default`) VALUES
(1, 'English', 'eng', 'eng.png', 1),
(2, 'Ukrainian', 'ukr', 'ukr.png', 0);

-- --------------------------------------------------------

--
-- Структура таблиці `menu`
--

CREATE TABLE IF NOT EXISTS `menu` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `publish` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Дамп даних таблиці `menu`
--

INSERT INTO `menu` (`id`, `title`, `publish`) VALUES
(1, 'Main Menu', 1);

-- --------------------------------------------------------

--
-- Структура таблиці `menuItems`
--

CREATE TABLE IF NOT EXISTS `menuItems` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `menuId` int(11) NOT NULL,
  `parent` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `linkType` varchar(2) NOT NULL,
  `link` mediumtext NOT NULL,
  `publish` int(11) NOT NULL,
  `order_by` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=7 ;

--
-- Дамп даних таблиці `menuItems`
--

INSERT INTO `menuItems` (`id`, `menuId`, `parent`, `title`, `linkType`, `link`, `publish`, `order_by`) VALUES
(1, 1, 0, 'Main page', 'L', '/', 1, 0),
(2, 1, 0, 'About Drupal', 'L', '#', 1, 0),
(3, 1, 0, 'Our noob projects', 'S', '10', 1, 0),
(4, 1, 0, 'Contacts', 'L', '#', 1, 0),
(5, 1, 3, 'PHP', '3', '#', 1, 0),
(6, 1, 3, 'HTML5', 'L', '#', 1, 0);

-- --------------------------------------------------------

--
-- Структура таблиці `permissions`
--

CREATE TABLE IF NOT EXISTS `permissions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL DEFAULT '0',
  `root` tinyint(1) NOT NULL DEFAULT '0',
  `admin` int(1) NOT NULL,
  `create` tinyint(1) NOT NULL DEFAULT '0',
  `edit` tinyint(1) NOT NULL DEFAULT '0',
  `editOwn` tinyint(1) NOT NULL DEFAULT '0',
  `delete` tinyint(1) NOT NULL DEFAULT '0',
  `deleteOwn` tinyint(1) NOT NULL DEFAULT '0',
  `publish` tinyint(1) NOT NULL DEFAULT '0',
  `publishOwn` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=10 ;

--
-- Дамп даних таблиці `permissions`
--

INSERT INTO `permissions` (`id`, `user_id`, `root`, `admin`, `create`, `edit`, `editOwn`, `delete`, `deleteOwn`, `publish`, `publishOwn`) VALUES
(1, 6, 1, 0, 0, 0, 0, 0, 0, 0, 0),
(2, 7, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(3, 8, 0, 0, 1, 0, 1, 0, 1, 0, 0),
(4, 9, 0, 0, 1, 0, 1, 1, 1, 1, 1),
(5, 10, 0, 1, 1, 1, 1, 1, 1, 1, 1),
(6, 11, 0, 0, 1, 0, 1, 0, 1, 0, 1),
(7, 12, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(8, 13, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(9, 14, 0, 0, 0, 0, 0, 0, 0, 0, 0);

-- --------------------------------------------------------

--
-- Структура таблиці `sessions`
--

CREATE TABLE IF NOT EXISTS `sessions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sess_id` varchar(255) NOT NULL,
  `user_id` int(11) NOT NULL,
  `lastActive` int(11) NOT NULL,
  `ip` varchar(15) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=91 ;

--
-- Дамп даних таблиці `sessions`
--

INSERT INTO `sessions` (`id`, `sess_id`, `user_id`, `lastActive`, `ip`) VALUES
(84, '6mldpnjlopg7vbnbetosamsho7', 10, 1383954257, '127.0.0.1'),
(90, 'rg83mfkg5vgbhl2gfif4po60r1', 6, 1384587538, '127.0.0.1');

-- --------------------------------------------------------

--
-- Структура таблиці `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nickname` varchar(25) NOT NULL,
  `password` varchar(40) NOT NULL,
  `email` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `surname` varchar(255) NOT NULL,
  `active` tinyint(1) NOT NULL,
  `activateCode` varchar(20) NOT NULL,
  `regDate` int(11) NOT NULL,
  `language` int(11) NOT NULL,
  `avatar` varchar(255) NOT NULL,
  `lastActive` int(11) NOT NULL,
  `deleted` tinyint(1) NOT NULL,
  `blocked` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=15 ;

--
-- Дамп даних таблиці `users`
--

INSERT INTO `users` (`id`, `nickname`, `password`, `email`, `name`, `surname`, `active`, `activateCode`, `regDate`, `language`, `avatar`, `lastActive`, `deleted`, `blocked`) VALUES
(6, 'Tarzanych', '54fb518a200c7c63714476e06e70eca8', 'tarzanych@gmail.com', 'Sergey', 'Skrypchuk', 1, 'vOth3e5ML0', 1383154419, 2, 'KXzb5TvGyS.jpg', 1384587529, 0, 0),
(7, 'Admin', '200ceb26807d6bf99fd6f4f0d1ca54d4', 'admin@admin.com', 'Сергей', 'Skrypchuk', 0, 'nVSJJZtyNw', 1383230824, 1, '', 1383244455, 1, 0),
(8, 'User', '5f4dcc3b5aa765d61d8327deb882cf99', 'user@user.com', '', '', 1, 'JTIu1KsMzz', 1383315780, 1, 'PMIe03k8fM.jpg', 1383952352, 0, 0),
(9, 'User2', '5f4dcc3b5aa765d61d8327deb882cf99', 'email@email.com', '', '', 1, 'Qra2l99XiO', 1383316595, 1, '', 1383953285, 0, 0),
(10, 'User3', '5f4dcc3b5aa765d61d8327deb882cf99', 'password@password.com', '', '', 1, 'aYhbPEz28M', 1383316718, 1, '', 1383953479, 0, 0),
(11, 'User4', '5f4dcc3b5aa765d61d8327deb882cf99', 'woody@woodpecker.com', '', '', 0, 'neqFV0f78', 1383316759, 1, '', 0, 0, 0),
(12, 'User5', '5f4dcc3b5aa765d61d8327deb882cf99', 'email2@email.com', '', '', 0, '4cV4T6McX8', 1383316849, 1, '', 1383316856, 0, 0),
(13, 'User6', '5f4dcc3b5aa765d61d8327deb882cf99', 'email3@email.com', '', '', 0, 'cQrhVO8f31', 1383316978, 1, '', 1383322923, 1, 0),
(14, 'User7', '5f4dcc3b5aa765d61d8327deb882cf99', 'email4asfd@email.com', 'Sergey', '', 1, 'z4wVo8Q3Ny', 1383638891, 1, 'WqpsZ9t9HJ.jpg', 1383639300, 1, 0);

-- --------------------------------------------------------

--
-- Структура таблиці `votes`
--

CREATE TABLE IF NOT EXISTS `votes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `content_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `vote` int(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=20 ;

--
-- Дамп даних таблиці `votes`
--

INSERT INTO `votes` (`id`, `content_id`, `user_id`, `vote`) VALUES
(6, 3, 8, 4),
(7, 19, 8, 3),
(8, 33, 9, 4),
(9, 3, 9, 2),
(10, 31, 9, 4),
(11, 25, 9, 3),
(12, 13, 9, 5),
(13, 19, 10, 4),
(14, 33, 10, 3),
(15, 25, 10, 4),
(16, 31, 10, 2),
(17, 13, 10, 4),
(18, 3, 10, 5),
(19, 33, 6, 2);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
