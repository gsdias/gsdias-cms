--
-- Table structure for table `options`
--

CREATE TABLE IF NOT EXISTS `options` (
  `name` varchar(50) NOT NULL PRIMARY KEY,
  `type` varchar(20) DEFAULT NULL,
  `label` varchar(30) NOT NULL,
  `value` varchar(100) DEFAULT NULL,
  `index` int(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ;

--
-- Indexes for table `options`
--
INSERT INTO options (`name`, `type`, `label`, `value`, `index`) VALUES
('version', NULL, '', '1.5.1', 1),
('name', 'input', '{LANG_NAME}', NULL, 2),
('email', 'input', '{LANG_EMAIL}', NULL, 3),
('ga', 'input', 'Google Analytics', NULL, 4),
('fb', 'input', 'Facebook', NULL, 5),
('gtm', 'input', 'Google Tag Manager', NULL, 6),
('locale', 'select', '{LANG_LANGUAGE}', 'pt_PT', 7),
('maintenance', 'checkbox', '{LANG_MAINTENANCE_LABEL}', NULL, 8),
('debug', 'checkbox', '{LANG_DEBUG_LABEL}', NULL, 9);
