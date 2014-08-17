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
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
