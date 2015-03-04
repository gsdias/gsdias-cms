--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `uid` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `email` varchar(255) NOT NULL,
  `password` varchar(32) DEFAULT NULL,
  `name` varchar(120) NOT NULL,
  `locale` varchar(10) DEFAULT 'en_GB',
  `level` varchar(10) DEFAULT 'user',
  `notifications` int(1) DEFAULT NULL,
  `sync` int(1) DEFAULT NULL,
  `creator` int(11) NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `disabled` int(1) DEFAULT NULL,
  `last_login` timestamp NULL DEFAULT NULL,
  `code` varchar(32) DEFAULT NULL,
    UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
