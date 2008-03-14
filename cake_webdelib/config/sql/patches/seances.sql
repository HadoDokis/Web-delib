ALTER TABLE `seances` ADD `traitee` INT( 1 ) NOT NULL DEFAULT '0';

INSERT INTO `acos` (`id`, `object_id`, `alias`, `lft`, `rght`) VALUES
(268, 0, 'Seances:changeStatus', 520, 521);

INSERT INTO
INSERT INTO `webdelib`.`aros_acos` (
`id` ,
`aro_id` ,
`aco_id` ,
`_create` ,
`_read` ,
`_update` ,
`_delete`
)
VALUES (
NULL , '3', '268', '1', '1', '1', '1'
);


INSERT INTO `acos` (`id`, `object_id`, `alias`, `lft`, `rght`) VALUES
(268, 0, 'Deliberations:PositionneDelibsSeance', 522, 523);
INSERT INTO `aros_acos` (`id`, `aro_id`, `aco_id`, `_create`, `_read`, `_update`, `_delete`) VALUES
(null, 3, 269, '1', '1', '1', '1');