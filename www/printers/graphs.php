<?php
/*
graphs.php
Copyright (C) 2014 Phil Ferland

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

require "lib/config.php"; #www config
$config = parse_ini_file($WWWconfig['daemon_path']."/config/config.ini");
require "lib/SQL.php"; #the uh.. SQL class...
require $WWWconfig['smarty_path']."/Smarty.class.php"; #get smarty..

#now lets build the SQL class.
$SQL = new SQL($config);

#setup smarty
$smarty = new smarty();
$smarty->setTemplateDir( $WWWconfig['smarty_path']."/templates/" );
$smarty->setCompileDir( $WWWconfig['smarty_path']."/templates_c/" );
$smarty->setCacheDir( $WWWconfig['smarty_path']."/cache/" );
$smarty->setConfigDir( $WWWconfig['smarty_path']."/configs/" );

#fetch the Printers that we are watching.
$width = $WWWconfig['width'];
$stats = array();
$printer_names = array();
$prep = $SQL->conn->prepare("SELECT * FROM (
    SELECT `history`.`id`, `name`, `timestamp`, FROM_UNIXTIME(`timestamp`) as `datestamp`, `count`, `tray_1`, `tray_2`, `tray_3`, `toner`, `kit_a`, `kit_b`
    FROM `printers`.`printers`, `printers`.`history` WHERE `printers`.`id` = `history`.`printer_id`
    AND `printers`.`id` = ? ORDER BY `history`.`id` DESC LIMIT 100)
    tmp ORDER BY `tmp`.`id` ASC");
$prep->bindParam(1, $_GET['id'], PDO::PARAM_INT);
$prep->execute();
$SQL->checkError();
$fetch = $prep->fetchAll(2);
$smarty->assign('printer_rows', $fetch);

switch(strtolower($_GET['graph']))
{
    case "pagecount":
        $smarty->display('PageCount.tpl');
        break;

    case "levels":
        $smarty->display('Levels.tpl');
        break;

    default:
        Echo "Incorrect use of the \$_GET['graph'] switch.";
        break;
}