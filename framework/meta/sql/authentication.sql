CREATE TABLE `users` (
  `id` int(11) PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `email` varchar(255) UNIQUE NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB;

CREATE TABLE `permissions` (
  `id` int(11) PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `userId` int(11) DEFAULT NULL,
  `group` varchar(255) DEFAULT NULL,
  `permission` varchar(255) DEFAULT NULL
) ENGINE=InnoDB;

CREATE TABLE `loginsessions` (
  `id` int(11) PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `ip` varchar(45) NOT NULL,
  `token` varchar(255) NOT NULL,
  `lastUpdate` int(11) NOT NULL,
  `failedAttempts` int(11) NOT NULL
) ENGINE=MEMORY;

CREATE TABLE `sessions` (
  `id` int(11) PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `userId` int(11) NOT NULL,
  `ip` varchar(50) NOT NULL,
  `selector` varchar(255) NOT NULL,
  `validator` varchar(255) NOT NULL
) ENGINE=MEMORY;

CREATE TABLE `longlivedsessions` (
  `id` int(11) PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `userId` int(11) NOT NULL,
  `activeSessionId` int(11) NOT NULL,
  `selector` varchar(255) NOT NULL,
  `validator` varchar(255) NOT NULL
) ENGINE=InnoDB;

ALTER TABLE `permissions`
  ADD INDEX(`userId`),
  ADD CONSTRAINT `fk_userid` FOREIGN KEY (`userId`) REFERENCES `users`(`id`) ON DELETE CASCADE ON UPDATE CASCADE;