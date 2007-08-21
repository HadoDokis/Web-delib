RENAME TABLE agents_services TO users_services;
ALTER TABLE `users_services` CHANGE `agent_id` `user_id` INT( 11 ) NOT NULL DEFAULT '0';