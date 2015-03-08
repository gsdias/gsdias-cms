--
-- Table structure for table `emails`
--

CREATE TABLE IF NOT EXISTS `emails` (
    `eid` INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `template` VARCHAR(100) NOT NULL,
    `subject` VARCHAR(100) DEFAULT NULL,
    `from` VARCHAR(100) DEFAULT NULL,
    `to` VARCHAR(100) DEFAULT NULL,
    `cc` VARCHAR(100) DEFAULT NULL,
    `bcc` VARCHAR(100) DEFAULT NULL,
    `attachment` TEXT DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

INSERT INTO `emails` (`eid`, `template`, `subject`, `from`, `to`, `cc`, `bcc`, `attachment`) VALUES
(1, 'register', NULL, NULL, NULL, NULL, NULL, '[]');
