--
-- Table structure for table `layouttypes`
--

CREATE TABLE IF NOT EXISTS `layouttypes` (
  `ltid` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `name` varchar(100) NOT NULL,
  `creator` int(11) NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

INSERT INTO layouttypes (`name`, `creator`) VALUES ('home', 1), ('main', 1), ('detail', 1);
