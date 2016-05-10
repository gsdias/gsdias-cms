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
