--
-- Table structure for table `layoutsections`
--

CREATE TABLE IF NOT EXISTS `layoutsections` (
  `lsid` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `lid` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `creator` int(11) NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
