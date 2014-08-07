--
-- Table structure for table `options`
--

CREATE TABLE IF NOT EXISTS `options` (
  `name` varchar(20) NOT NULL PRIMARY KEY,
  `value` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ;

--
-- Indexes for table `options`
--
INSERT INTO options (`name`) VALUES ('gsd-name'), ('gsd-email'), ('gsd-ga'), ('gsd-fb');
