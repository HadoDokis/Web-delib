ALTER TABLE `users` ADD `mail_insertion` tinyint(1) NOT NULL AFTER  accept_notif;
ALTER TABLE `users` ADD `mail_traitement` tinyint(1) NOT NULL AFTER  accept_notif;
ALTER TABLE `users` ADD `mail_refus` tinyint(1) NOT NULL AFTER  accept_notif;
