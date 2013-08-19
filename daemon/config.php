<?php
/*
config.php
Copyright (C) 2013 Phil Ferland

This program is free software; you can redistribute it and/or modify it under the terms
of the GNU General Public License as published by the Free Software Foundation; either
version 2 of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
See the GNU General Public License for more details.

ou should have received a copy of the GNU General Public License along with this program;
if not, write to the

   Free Software Foundation, Inc.,
   59 Temple Place, Suite 330,
   Boston, MA 02111-1307 USA
*/
$config['timezone'] =   "America/New_York";
$config['host']     =   'host'; #mysql host IP or hostname
$config['srvc']     =   'mysql'; #PDO Service (mysql)
$config['db_user']  =   'user'; #mysql User
$config['db_pwd']   =   'password'; #mysql passowrd
$config['collate']  =   'utf8_bin'; #mysql collate to use, UTF8 is default.
$config['engine']   =   'innodb'; #Mysql Engine to use, InnoDB is default.