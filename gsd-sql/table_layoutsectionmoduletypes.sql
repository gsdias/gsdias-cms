--
-- Table structure for table `layoutsectionmoduletypes`
--

CREATE TABLE IF NOT EXISTS `layoutsectionmoduletypes` (
  `lsmtid` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `lsid` int(11) NOT NULL,
  `mtid` int(11) NOT NULL,
  `smtid` int(11) DEFAULT NULL,
  `total` int(11) DEFAULT 1,
  FOREIGN KEY (lsid)
    REFERENCES layoutsections(lsid)
    ON UPDATE CASCADE ON DELETE CASCADE,
  FOREIGN KEY (mtid)
    REFERENCES moduletypes(mtid)
    ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
