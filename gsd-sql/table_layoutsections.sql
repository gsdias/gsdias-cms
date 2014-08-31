--
-- Table structure for table `layoutsections`
--

CREATE TABLE IF NOT EXISTS `layoutsections` (
  `lsid` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `lid` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `creator` int(11) NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (creator)
    REFERENCES users(uid)
    ON UPDATE CASCADE ON DELETE CASCADE,
  FOREIGN KEY (lid)
    REFERENCES layouts(lid)
    ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
