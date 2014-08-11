--
-- Table structure for table `options`
--

CREATE TABLE IF NOT EXISTS `options` (
  `name` varchar(20) NOT NULL PRIMARY KEY,
  `index` int(10) DEFAULT NULL,
  `label` varchar(30) NOT NULL,
  `value` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ;

--
-- Indexes for table `options`
--
INSERT INTO options (`name`, `label`, `index`) VALUES ('gsd-name', 'Nome', 1), ('gsd-email', 'Email', 2), ('gsd-ga', 'Google Analytics', 3), ('gsd-fb', 'Facebook', 4);
