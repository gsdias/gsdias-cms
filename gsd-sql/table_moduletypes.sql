--
-- Table structure for table `modulestype`
--

CREATE TABLE IF NOT EXISTS `moduletypes` (
  `mtid` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `name` varchar(100) NOT NULL,
  `file` varchar(100) NOT NULL,
  `creator` int(11) DEFAULT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (creator)
    REFERENCES users(uid)
    ON UPDATE CASCADE ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Indexes for table `options`
--
INSERT INTO moduletypes (`mtid`, `name`, `file`, `creator`) VALUES (1, 'image', '_image', 1), (2, 'input', '_input', 1), (3, 'textarea', '_textarea', 1), (4, 'document', '_document', 1), (5, 'html', '_html', 1), (6, 'list', '_list', 1);
