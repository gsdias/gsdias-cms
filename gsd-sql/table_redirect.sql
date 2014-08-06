--
-- Table structure for table `pages`
--

CREATE TABLE IF NOT EXISTS `redirect` (
  `rid` int(11) NOT NULL AUTO_INCREMENT,
  `pid` int(11) NOT NULL,
  `from` varchar(120) NOT NULL,
  `destination` varchar(120) NOT NULL,
  `creator` int(11) NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `pages`
--
ALTER TABLE `redirect` ADD PRIMARY KEY (`rid`);
