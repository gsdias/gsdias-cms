--
-- Table structure for table `options`
--

CREATE TABLE IF NOT EXISTS `options` (
  `name` varchar(20) NOT NULL,
  `value` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ;

--
-- Indexes for table `options`
--
ALTER TABLE `options` ADD PRIMARY KEY (`name`);

INSERT INTO options (`name`) VALUES ('gsd-name'), ('gsd-email'), ('gsd-ga');