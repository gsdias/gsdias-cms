ALTER TABLE `pages`
 ADD FULLTEXT KEY `search` (`title`,`description`);