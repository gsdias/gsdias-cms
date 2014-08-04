--
-- Table structure for table `images`
--

CREATE TABLE IF NOT EXISTS `images` (
  `iid` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `extension` varchar(5) NOT NULL,
  `creator` int(11) NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `disabled` tinyint(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Indexes for table `images`
--
ALTER TABLE `images` ADD PRIMARY KEY (`iid`), 
    MODIFY `iid` int(11) NOT NULL AUTO_INCREMENT;
