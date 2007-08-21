RENAME TABLE agents_circuits TO users_circuits;
ALTER TABLE `users_circuits` CHANGE `agent_id` `user_id` INT( 11 ) NOT NULL DEFAULT '0';