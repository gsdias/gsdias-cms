--
-- Table structure for table `layouttypes`
--

CREATE TABLE IF NOT EXISTS `layouttypes` (
  `ltid` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `name` varchar(100) NOT NULL,
  `creator` int(11) DEFAULT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (creator)
    REFERENCES users(uid)
    ON UPDATE CASCADE ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

INSERT INTO layouttypes (`ltid`, `name`, `creator`) VALUES (1, 'home', 1), (2, 'main', 1), (3, 'detail', 1);
