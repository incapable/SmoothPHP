CREATE TABLE `users` (
  `id` int(11) PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `loginsession` (
  `id` int(11) PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `ip` varchar(45) NOT NULL,
  `token` varchar(255) NOT NULL,
  `state` tinyint(1) NOT NULL,
  `lastUpdate` int(11) NOT NULL,
  `failedAttempts` int(11) NOT NULL
) ENGINE=MEMORY DEFAULT CHARSET=latin1;

CREATE TABLE `sessions` (
  `id` int(11) PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `userId` int(11) NOT NULL,
  `selector` varchar(255) NOT NULL,
  `validator` varchar(255) NOT NULL
) ENGINE=MEMORY DEFAULT CHARSET=latin1;
