CREATE TABLE `users` (
  `id` int(11) PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL
) ENGINE=InnoDB;

CREATE TABLE `users_permissions` (
  `id` int(11) PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `userId` int(11) NOT NULL,
  `permission` varchar(255) NOT NULL
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
  `selector` varchar(255) NOT NULL,
  `validator` varchar(255) NOT NULL
) ENGINE=MEMORY;

ALTER TABLE `users_permissions`
  ADD INDEX(`userId`),
  ADD CONSTRAINT `fk_userid` FOREIGN KEY (`userId`) REFERENCES `users`(`id`) ON DELETE CASCADE ON UPDATE CASCADE;