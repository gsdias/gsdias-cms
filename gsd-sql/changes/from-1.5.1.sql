ALTER TABLE `options` ADD `type` VARCHAR(20) NULL AFTER `name`;

UPDATE `options` SET `name` = 'version', `type` = NULL WHERE `options`.`name` = 'gsd-version';
UPDATE `options` SET `name` = 'name', `type` = 'input' WHERE `options`.`name` = 'gsd-name';
UPDATE `options` SET `name` = 'email', `type` = 'input' WHERE `options`.`name` = 'gsd-email';
UPDATE `options` SET `name` = 'ga', `type` = 'input' WHERE `options`.`name` = 'gsd-ga';
UPDATE `options` SET `name` = 'fb', `type` = 'input' WHERE `options`.`name` = 'gsd-fb';
UPDATE `options` SET `name` = 'gtm', `type` = 'input' WHERE `options`.`name` = 'gsd-gtm';
UPDATE `options` SET `name` = 'locale', `type` = 'select' WHERE `options`.`name` = 'gsd-locale_select';
UPDATE `options` SET `name` = 'maintenance', `type` = 'checkbox' WHERE `options`.`name` = 'gsd-maintenance_checkbox';
UPDATE `options` SET `name` = 'debug', `type` = 'checkbox' WHERE `options`.`name` = 'gsd-debug_checkbox';

CREATE TABLE IF NOT EXISTS `pages_extra` (
  `peid` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `pid` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `value` text DEFAULT NULL,
    FOREIGN KEY (pid)
    REFERENCES pages(pid)
    ON UPDATE CASCADE ON DELETE CASCADE,
    UNIQUE KEY `unique_field` (`pid`, `name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
