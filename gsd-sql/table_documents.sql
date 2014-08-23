--
-- Table structure for table `documents`
--

CREATE TABLE IF NOT EXISTS `documents` (
  `did` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `name` varchar(100) NOT NULL,
  `description` varchar(100) DEFAULT NULL,
  `extension` varchar(5) NOT NULL,
  `size` varchar(10) NOT NULL,
  `creator` int(11) NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
  FOREIGN KEY (creator)
    REFERENCES users(uid)
    ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
