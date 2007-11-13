<?php
/**
 * Yes. :  It's That Easy, Period <http://www.yesperiod.org/>
 * Copyright (c) 2006, Yes Period, Inc.
 *                     1610 Forster Street
 *                     Harrisburg, PA 17103
 *
 * @filesource   config.php
 * @copyright    Copyright (c) 2006, Yes Period, Inc.
 * @link         http://www.yesperiod.com/
 * @package      ACM
 * @modifiedby   $LastChangedBy: Ryan J. Peterson $
 * @lastmodified $Date: 2006/06/22 10:22:24 $
 */

 /* ------ ACM AutoLoad Config Vars ----- */

 /* This sets AutoLoad on or off
  * using true or false. This is to
  * connect ACM to your existing Users/Groups
  */
	define('ACMAUTOLOAD', false);

 /* This defines the tables that hold
  * your existing Users/Groups.
  */
	define('ACMROLES','roles');
	define('ACMUSERS','users');

 /* This defines the fields that hold
  * the Users/Groups unique alias.
  */
	define('ACMROLE_ALIAS','libelle');
	define('ACMUSER_ALIAS','login');

	/* this constant allows you to decide
	 * if ACM can add Users & Groups additional
	 * to those managed by AutoLoad, defaults
	 * to true if AutoLoad is off.
	 */
	define('ALLOWADDACO', true);

/* -------------------------------------- */

 ?>