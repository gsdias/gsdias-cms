--
-- Table structure for table `pagemodules`
--

CREATE TABLE IF NOT EXISTS `pagemodules` (
  `pmid` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `pid` int(11) NOT NULL,
  `lsid` int(11) NOT NULL,
  `mtid` int(11) NOT NULL,
  `data` text DEFAULT NULL,
  `creator` int(11) NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (creator)
    REFERENCES users(uid)
    ON UPDATE CASCADE ON DELETE RESTRICT,
  FOREIGN KEY (pid)
    REFERENCES pages(pid)
    ON UPDATE CASCADE ON DELETE RESTRICT,
  FOREIGN KEY (lsid)
    REFERENCES layoutsections(lsid)
    ON UPDATE CASCADE ON DELETE RESTRICT,
  FOREIGN KEY (mtid)
    REFERENCES moduletypes(mtid)
    ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
