RENAME TABLE agents_listepresences TO users_listepresences;
ALTER TABLE `users_listepresences` CHANGE `agent_id` `user_id` INT( 11 ) NOT NULL DEFAULT '0';