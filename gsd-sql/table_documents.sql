--
-- Table structure for table `documents`
--

CREATE TABLE IF NOT EXISTS `documents` (
  `did` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `extension` varchar(5) NOT NULL,
  `creator` int(11) NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `disabled` tinyint(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Indexes for table `documents`
--
ALTER TABLE `documents` ADD PRIMARY KEY (`did`), 
    MODIFY `did` int(11) NOT NULL AUTO_INCREMENT;
