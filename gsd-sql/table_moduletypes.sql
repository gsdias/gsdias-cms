--
-- Table structure for table `modulestype`
--

CREATE TABLE IF NOT EXISTS `moduletypes` (
  `mtid` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `name` varchar(100) NOT NULL,
  `file` varchar(100) NOT NULL,
  `creator` int(11) NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (creator)
    REFERENCES users(uid)
    ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Indexes for table `options`
--
INSERT INTO moduletypes (`name`, `file`, `creator`) VALUES ('image', '_image', 1), ('input', '_input', 1), ('textarea', '_textarea', 1), ('document', '_document', 1), ('html', '_html', 1), ('list', '_list', 1);
