-- phpMyAdmin SQL Dump
-- version 5.0.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3308
-- Erstellungszeit: 19. Jul 2020 um 16:27
-- Server-Version: 10.4.11-MariaDB
-- PHP-Version: 7.4.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Datenbank: `twitterlitedb`
--

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `comment`
--

CREATE TABLE `comment` (
  `commentId` int(10) UNSIGNED NOT NULL,
  `postId` int(10) UNSIGNED NOT NULL,
  `userId` int(10) UNSIGNED NOT NULL,
  `content` varchar(300) NOT NULL,
  `creationTime` timestamp NOT NULL DEFAULT current_timestamp(),
  `deleted` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Daten für Tabelle `comment`
--

INSERT INTO `comment` (`commentId`, `postId`, `userId`, `content`, `creationTime`, `deleted`) VALUES
(59, 24, 13, 'Can not wait to hear them!', '2020-07-17 18:11:58', 0),
(60, 25, 13, 'Fine, and you?', '2020-07-17 18:13:11', 0),
(61, 18, 13, 'can not wait to see it', '2020-07-17 18:14:35', 0),
(63, 25, 14, 'Enjoying the sun B-)', '2020-07-17 18:18:10', 0),
(64, 21, 14, 'no pain, no gain', '2020-07-17 18:18:45', 0),
(65, 19, 15, 'Nice!', '2020-07-17 18:19:46', 0);

--
-- Trigger `comment`
--
DELIMITER $$
CREATE TRIGGER `Delete_Comment` AFTER DELETE ON `comment` FOR EACH ROW IF old.deleted = 0 THEN
UPDATE `post` SET `comments` = `comments` - 1 WHERE `postId` = old.postId;
END IF
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `Deleted=0` AFTER UPDATE ON `comment` FOR EACH ROW IF  old.deleted = 1 AND new.deleted = 0 THEN 
	UPDATE post SET comments = 		comments + 1  WHERE postId = 	old.postId;
ENd If
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `Deleted=1` AFTER UPDATE ON `comment` FOR EACH ROW IF  old.deleted = 0 AND new.deleted = 1 THEN 
	UPDATE post SET comments = 		comments - 1 WHERE postId = 	old.postId;
ENd If
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `New_Comment` AFTER INSERT ON `comment` FOR EACH ROW UPDATE `post` SET `comments` = `comments` +1 WHERE `postId` = new.postId
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `post`
--

CREATE TABLE `post` (
  `postId` int(10) UNSIGNED NOT NULL,
  `userId` int(10) UNSIGNED NOT NULL,
  `content` varchar(300) NOT NULL,
  `creationTime` timestamp NOT NULL DEFAULT current_timestamp(),
  `likes` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `deleted` tinyint(1) NOT NULL DEFAULT 0,
  `comments` int(10) UNSIGNED NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Daten für Tabelle `post`
--

INSERT INTO `post` (`postId`, `userId`, `content`, `creationTime`, `likes`, `deleted`, `comments`) VALUES
(18, 12, 'New Film is coming soon', '2020-05-20 16:00:00', 2, 0, 1),
(19, 12, 'Family Time!', '2020-06-22 16:00:00', 3, 0, 1),
(20, 13, 'A wonderful day :D', '2020-06-30 07:00:00', 1, 0, 0),
(21, 13, 'Workout was good.', '2020-06-30 18:30:00', 1, 0, 1),
(22, 14, 'I missed football #Corona', '2020-05-17 20:00:00', 1, 0, 0),
(23, 14, 'Deutscher Meister!', '2020-06-16 16:00:00', 1, 0, 0),
(24, 15, 'New song ideas', '2020-06-16 13:32:00', 1, 0, 1),
(25, 15, 'How are you guys?', '2020-07-17 16:00:00', 3, 0, 2);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `user`
--

CREATE TABLE `user` (
  `userId` int(10) UNSIGNED NOT NULL,
  `email` varchar(255) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `registryDate` date NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Daten für Tabelle `user`
--

INSERT INTO `user` (`userId`, `email`, `username`, `password`, `registryDate`) VALUES
(12, 'jd@email.com', 'johnny.depp', '$2y$10$wMqFt1kZ1NZDC2hfgxkF6OFBYts..XGwpUakinB4VWzSjIzZZscuW', '2020-07-17'),
(13, 'sj@email.com', 'samuel.jackson', '$2y$10$7WoQFP0KAzajp2zkDkCJSOrg00ysZh00XH09RngWlwS109.2zFuMy', '2020-07-17'),
(14, 'mn@email.com', 'manuel.neuer', '$2y$10$Exrk7ggt.aWC3bfjwT2AjujQa3G2bOUHVDakPKCAqNBLAyCefSzZ.', '2020-07-17'),
(15, 'beyonce@email.com', 'beyonce', '$2y$10$uAFnWa4J9xeJcFfzm688Je2K1FJVBWIg0rGtsg4gFf0uZeXOOTa/C', '2020-07-17'),
(20, 'student@thm.de', 'student123', '$2y$10$djLoosDk18ekinLfSbMSGuN3gxUgNgP84A.vJm/Y3vGrsI8DzAxUK', '2020-07-18');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `user_follows`
--

CREATE TABLE `user_follows` (
  `userA` int(10) UNSIGNED NOT NULL,
  `userB` int(10) UNSIGNED NOT NULL,
  `followDate` date NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Daten für Tabelle `user_follows`
--

INSERT INTO `user_follows` (`userA`, `userB`, `followDate`) VALUES
(12, 15, '2020-07-17'),
(13, 12, '2020-07-17'),
(13, 15, '2020-07-17'),
(14, 12, '2020-07-17'),
(14, 13, '2020-07-17'),
(14, 15, '2020-07-17'),
(15, 12, '2020-07-17'),
(15, 14, '2020-07-17');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `user_like_post`
--

CREATE TABLE `user_like_post` (
  `userId` int(10) UNSIGNED NOT NULL,
  `postId` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Daten für Tabelle `user_like_post`
--

INSERT INTO `user_like_post` (`userId`, `postId`) VALUES
(12, 25),
(13, 18),
(13, 19),
(13, 24),
(13, 25),
(14, 19),
(14, 21),
(14, 25),
(15, 18),
(15, 19),
(15, 22),
(15, 23);

--
-- Trigger `user_like_post`
--
DELIMITER $$
CREATE TRIGGER `Delike_Post` AFTER DELETE ON `user_like_post` FOR EACH ROW UPDATE `post` SET `likes` = `likes` - 1 WHERE `postId` = old.postId
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `Like_Post` AFTER INSERT ON `user_like_post` FOR EACH ROW UPDATE `post` SET `likes` = `likes` + 1 WHERE `postId` = new.postId
$$
DELIMITER ;

--
-- Indizes der exportierten Tabellen
--

--
-- Indizes für die Tabelle `comment`
--
ALTER TABLE `comment`
  ADD PRIMARY KEY (`commentId`),
  ADD KEY `postId foreignkey` (`postId`),
  ADD KEY `userId foreignkey comment` (`userId`);

--
-- Indizes für die Tabelle `post`
--
ALTER TABLE `post`
  ADD PRIMARY KEY (`postId`),
  ADD KEY `userId foreignkey` (`userId`);

--
-- Indizes für die Tabelle `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`userId`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indizes für die Tabelle `user_follows`
--
ALTER TABLE `user_follows`
  ADD PRIMARY KEY (`userA`,`userB`),
  ADD KEY `userB` (`userB`),
  ADD KEY `userA` (`userA`);

--
-- Indizes für die Tabelle `user_like_post`
--
ALTER TABLE `user_like_post`
  ADD PRIMARY KEY (`userId`,`postId`),
  ADD KEY `userId` (`userId`),
  ADD KEY `postId` (`postId`);

--
-- AUTO_INCREMENT für exportierte Tabellen
--

--
-- AUTO_INCREMENT für Tabelle `comment`
--
ALTER TABLE `comment`
  MODIFY `commentId` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=69;

--
-- AUTO_INCREMENT für Tabelle `post`
--
ALTER TABLE `post`
  MODIFY `postId` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT für Tabelle `user`
--
ALTER TABLE `user`
  MODIFY `userId` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- Constraints der exportierten Tabellen
--

--
-- Constraints der Tabelle `comment`
--
ALTER TABLE `comment`
  ADD CONSTRAINT `postId foreignkey` FOREIGN KEY (`postId`) REFERENCES `post` (`postId`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `userId foreignkey comment` FOREIGN KEY (`userId`) REFERENCES `user` (`userId`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints der Tabelle `post`
--
ALTER TABLE `post`
  ADD CONSTRAINT `userId foreignkey` FOREIGN KEY (`userId`) REFERENCES `user` (`userId`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints der Tabelle `user_follows`
--
ALTER TABLE `user_follows`
  ADD CONSTRAINT `userA foreign key` FOREIGN KEY (`userA`) REFERENCES `user` (`userId`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `userB foreign key` FOREIGN KEY (`userB`) REFERENCES `user` (`userId`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints der Tabelle `user_like_post`
--
ALTER TABLE `user_like_post`
  ADD CONSTRAINT `geliketerpost` FOREIGN KEY (`postId`) REFERENCES `post` (`postId`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `userderliket` FOREIGN KEY (`userId`) REFERENCES `user` (`userId`) ON DELETE CASCADE ON UPDATE NO ACTION;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
