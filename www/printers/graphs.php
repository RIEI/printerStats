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
#$config = parse_ini_file($WWWconfig['daemon_path']."/config/config.ini");
require "lib/SQL.php"; #the uh.. SQL class...
require $WWWconfig['http']['smarty_path']."/Smarty.class.php"; #get smarty..

#now lets build the SQL class.
$SQL = new SQL($WWWconfig['SQL']);

#setup smarty
$smarty = new smarty();
$smarty->setTemplateDir( $WWWconfig['http']['smarty_path']."/templates/" );
$smarty->setCompileDir( $WWWconfig['http']['smarty_path']."/templates_c/" );
$smarty->setCacheDir( $WWWconfig['http']['smarty_path']."/cache/" );
$smarty->setConfigDir( $WWWconfig['http']['smarty_path']."/configs/" );

#fetch the Printers that we are watching.

if(!(empty($_GET['from']) || empty($_GET['to'])))
{
    if($_GET['to'] == 'now')
    {
        $_GET['to'] = time()-15;
    }
    if($_GET['from'] == 'first')
    {
        $prep1 = $SQL->conn->prepare("SELECT `timestamp` from `printers`.`history` WHERE `printer_id` = ? ORDER BY `id` ASC LIMIT 1");
        $prep1->bindParam(1, $_GET['id'], PDO::PARAM_INT);
        $prep1->execute();
        $row = $prep1->fetch(2);
        $_GET['from'] = $row['timestamp'];
    }

    $prep = $SQL->conn->prepare("SELECT * FROM (
    SELECT `history`.`id`, `name`, `timestamp`, FROM_UNIXTIME(`timestamp`) as `datestamp`, `count`, `tray_1`, `tray_2`, `tray_3`, `toner`, `kit_a`, `kit_b`
    FROM `printers`.`printers`, `printers`.`history` WHERE `printers`.`id` = `history`.`printer_id` AND `count` != 0
    AND `printers`.`id` = ? AND `history`.`timestamp` > ? and `history`.`timestamp` < ? ORDER BY `history`.`id` DESC)
    tmp ORDER BY `tmp`.`id` ASC");

    $prep->bindParam(1, $_GET['id'], PDO::PARAM_INT);
    $prep->bindParam(2, $_GET['from'], PDO::PARAM_INT);
    $prep->bindParam(3, $_GET['to'], PDO::PARAM_INT);
}else
{
    $prep = $SQL->conn->prepare("SELECT * FROM (
    SELECT `history`.`id`, `name`, `timestamp`, FROM_UNIXTIME(`timestamp`) as `datestamp`, `count`, `tray_1`, `tray_2`, `tray_3`, `toner`, `kit_a`, `kit_b`
    FROM `printers`.`printers`, `printers`.`history` WHERE `printers`.`id` = `history`.`printer_id` AND `count` != 0
    AND `printers`.`id` = ? ORDER BY `history`.`id` DESC LIMIT 256)
    tmp ORDER BY `tmp`.`id` ASC");

    $prep->bindParam(1, $_GET['id'], PDO::PARAM_INT);
}

$prep->execute();
$SQL->checkError();
$fetch = $prep->fetchAll(2);

$smarty->assign('printer_name', $fetch[0]['name']);
switch(strtolower($_GET['graph']))
{
    case "pagecount":
        $smarty->assign('printer_rows', $fetch);
        $smarty->assign('allURL', '?id='.(int)$_GET['id'].'&amp;graph=pagecount&amp;from=first&amp;to=now');
        $smarty->display('PageCount.tpl');
        break;

    case "levels":
        if(strtolower($_GET['type']) == '')
        {
            $smarty->assign('allURL', '?id='.(int)$_GET['id'].'&amp;graph=levels&amp;from=first&amp;to=now');
            $smarty->assign('printer_rows', $fetch);
            $smarty->display('AllLevels.tpl');
        }else
        {


            Switch(strtolower($_GET['type']))
            {
                case "toner":
                    $smarty->assign('printer_rows', ExtractData($fetch, 'toner'));
                    $smarty->assign('allURL', '?id='.(int)$_GET['id'].'&amp;type=toner&amp;graph=levels&amp;from=first&amp;to=now');
                    $smarty->assign('item', 'toner');
                    $smarty->assign('label', 'toner');
                    $smarty->display('SingleLevels.tpl');
                break;

                case "kita":
                    $smarty->assign('printer_rows', ExtractData($fetch, 'kit_a'));
                    $smarty->assign('allURL', '?id='.(int)$_GET['id'].'&amp;type=kita&amp;graph=levels&amp;from=first&amp;to=now');
                    $smarty->assign('item', 'kita');
                    $smarty->assign('label', 'kita');
                    $smarty->display('SingleLevels.tpl');
                break;

                case "kitb":
                    $smarty->assign('printer_rows', ExtractData($fetch, 'kit_b'));
                    $smarty->assign('allURL', '?id='.(int)$_GET['id'].'&amp;type=kitb&amp;graph=levels&amp;from=first&amp;to=now');
                    $smarty->assign('item', 'kitb');
                    $smarty->assign('label', 'kitb');
                    $smarty->display('SingleLevels.tpl');
                break;

                case "tray1":
                    $smarty->assign('printer_rows', ExtractData($fetch, 'tray_1'));
                    $smarty->assign('allURL', '?id='.(int)$_GET['id'].'&amp;type=tray1&amp;graph=levels&amp;from=first&amp;to=now');
                    $smarty->assign('item', 'tray1');
                    $smarty->assign('label', 'tray1');
                    $smarty->display('SingleLevels.tpl');
                break;

                case "tray2":
                    $smarty->assign('printer_rows', ExtractData($fetch, 'tray_2'));
                    $smarty->assign('allURL', '?id='.(int)$_GET['id'].'&amp;type=tray2&amp;graph=levels&amp;from=first&amp;to=now');
                    $smarty->assign('item', 'tray2');
                    $smarty->assign('label', 'tray2');
                    $smarty->display('SingleLevels.tpl');
                    break;

                case "tray3":
                    $smarty->assign('printer_rows', ExtractData($fetch, 'tray_3'));
                    $smarty->assign('allURL', '?id='.(int)$_GET['id'].'&amp;type=tray3&amp;graph=levels&amp;from=first&amp;to=now');
                    $smarty->assign('item', 'tray3');
                    $smarty->assign('label', 'tray3');
                    $smarty->display('SingleLevels.tpl');
                    break;
                default:
                    echo "Incorrect Use of the \$_GET['type'] switch.";
                break;
            }
        }
        break;

    default:
        Echo "Incorrect use of the \$_GET['graph'] switch.";
        break;
}



function ExtractData($array, $item)
{
    $data = array();
    foreach($array as $row)
    {
        array_push($data, array($row['datestamp'], $row[$item]));
    }
    return $data;
}