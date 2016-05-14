--
-- Table structure for table `pages_extra`
--

CREATE TABLE IF NOT EXISTS `pages_extra` (
  `peid` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `pid` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `value` text DEFAULT NULL,
    FOREIGN KEY (pid)
    REFERENCES pages(pid)
    ON UPDATE CASCADE ON DELETE CASCADE,
    UNIQUE KEY `unique_field` (`pid`, `name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
