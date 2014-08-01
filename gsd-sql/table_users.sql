--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `uid` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(32) NOT NULL,
  `name` varchar(120) NOT NULL,
  `level` int(11) NOT NULL,
  `notifications` int(1) DEFAULT NULL,
  `sync` int(1) DEFAULT NULL,
  `disabled` int(1) DEFAULT NULL,
  `creator` int(11) NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `last_login` timestamp NULL DEFAULT NULL,
  `code` varchar(32) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Indexes for table `users`
--
ALTER TABLE `users` ADD PRIMARY KEY (`uid`);
ALTER TABLE `users` ADD UNIQUE(`email`);
