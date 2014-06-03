<?php
/*
index.php
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

$result = $SQL->conn->query("SELECT * FROM `printers`.`printers`");
while($fetch = $result->fetch(2))
{
    $result1 = $SQL->conn->query("SELECT `campus_name` FROM `printers`.`campuses` WHERE `id` = {$fetch["campus_id"]}");
    $campus_fetch = $result1->fetch(2);
    #get the newest stats for each printer.
    $result2 = $SQL->conn->query("SELECT * FROM `printers`.`history` WHERE `printer_id` = '{$fetch["id"]}' ORDER BY `timestamp` DESC LIMIT 1");
    $stats[$campus_fetch['campus_name']][$fetch["id"]] = $result2->fetch(2);
    $stats[$campus_fetch['campus_name']][$fetch["id"]]['name'] = $fetch['name'];
}

function find_color($stat = 0)
{
    if($stat >= 75)
    {
        $color = "good";
    }
    if($stat < 75)
    {
        $color = "warning";
    }
    if($stat <= 5)
    {
        $color = "bad";
    }
    return $color;
}

$campuses = array();
$i = 0;
foreach($stats as $key=>$printers)
{
    $campuses[$i]['name'] = $key;
    $ii = 1;
    foreach($printers as $stat)
    {
        $stat['status'] = (@$stat['status'] ? $stat['status'] : "Offline");
        if($stat['status'] == "Offline" || $stat['status'] == "Alert")
        {
            $status_color = "bad";
        }else{
            $status_color = "white";
        }
        $campuses[$i]['array'][$ii] = array_merge(array(), $stat);
        $campuses[$i]['array'][$ii]['date'] = date('Y-m-d H:i:s', @$stat["timestamp"]);
        $campuses[$i]['array'][$ii]['count'] = number_format(@$stat['count']);
        $campuses[$i]['array'][$ii]['id'] = @$stat['printer_id'];
        $campuses[$i]['array'][$ii]['status_color'] = $status_color;
        $campuses[$i]['array'][$ii]['tray1_color'] = find_color((int)@$stat['tray_1']);
        $campuses[$i]['array'][$ii]['tray2_color'] = find_color((int)@$stat['tray_2']);
        $campuses[$i]['array'][$ii]['tray3_color'] = find_color((int)@$stat['tray_3']);
        $campuses[$i]['array'][$ii]['toner_color'] = find_color((int)@$stat['toner']);
        $campuses[$i]['array'][$ii]['kit_a_color'] = find_color((int)@$stat['kit_a']);
        $campuses[$i]['array'][$ii]['kit_b_color'] = find_color((int)@$stat['kit_b']);
        $campuses[$i]['array'][$ii]['new_row'] = "";
        if((int)$width+2 === (int)$ii)
        {
            $campuses[$i]['array'][$ii]['new_row'] = "</tr><tr>";
        }
        $ii++;
    }
    $i++;
}

$smarty->assign("campuses", $campuses);
$smarty->display("index.tpl");
?>