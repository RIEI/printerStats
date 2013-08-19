<?php
/*
graph.php
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
require $WWWconfig['daemon_path']."config.php"; #daemon config, used for the SQL class
require $WWWconfig['daemon_path']."lib/SQL.inc.php"; #the uh.. SQL class...
require "lib/graphs.php"; #for graphing the stats history...
$limit = 100;
$from = 0;
$width = $WWWconfig['width'];
$graphing = new graphs();
$SQL = new SQL($config);
$result = $SQL->conn->query("SELECT `id`, `name` FROM `printers`.`printers`");
$i = 0;
while($fetch = $result->fetch(2))
{
    #get the newest stats for each printer.
    $sql = "SELECT * FROM `printers`.`history` WHERE `printer_id` = '{$fetch["id"]}' ORDER BY `timestamp` DESC LIMIT 100";
    $result2 = $SQL->conn->query($sql);
    $fetch2 = $result2->fetchAll(2);
    $ret[$i]['name'] = $fetch['name'];
    $ret[$i]['toner'] = $graphing->line($fetch2, "toner", $fetch['name']);

    $ret[$i]['kit_a'] = $graphing->line($fetch2, "kit_a", $fetch['name']);

    $ret[$i]['kit_b'] = $graphing->line($fetch2, "kit_b", $fetch['name']);

    $ret[$i]['tray_1'] = $graphing->line($fetch2, "tray_1", $fetch['name']);

    $ret[$i]['tray_2'] = $graphing->line($fetch2, "tray_2", $fetch['name']);

    $ret[$i]['tray_3'] = $graphing->line($fetch2, "tray_3", $fetch['name']);

    $ret[$i]['count'] = $graphing->line($fetch2, "count", $fetch['name']);
    $i++;
}

?>

<html>
<head>
    <style>
        body
        {
            background-color: lightblue;
        }
        table.main_table
        {
            width : 1920px;
            height: 1080px;
        }
        td.good
        {
            border-style: solid;
            border-width: 1;
            background-color : #00FF00;
        }
        td.bad
        {
            border-style: solid;
            border-width: 1;
            background-color : #FF0000;
        }
        td.warning
        {
            border-style: solid;
            border-width: 1;
            background-color : #FFFF00;
        }
        th
        {
            border-style: solid;
            border-width: 1;
            background-color : lightseagreen;
        }
        td.white
        {
            border-style: solid;
            border-width: 1;
        }
        table.printer_table
        {
            border-style: solid;
            border-width: 1;
            width: 200px;
        }
        td.all
        {
            border-style: solid;
            border-width: 1;
        }
        td.td_align
        {
            vertical-align: center;
            text-align: center;
        }

    </style>
</head>
<body>
<table class="main_table">
    <tr>
        <?php
        $i = 0;
        foreach($ret as $return)
        {
        ?>
        <td class='td_align'>
            <table class='printer_table'>
                <tbody>
                    <tr>
                        <th><? echo $return['name']; ?></th>
                    </tr>
                    <tr>
                        <td>
                            <? foreach($return as $key=>$img)
                            {
                                if($key == "name"){continue;}
                            ?>
                            <table>
                                <tr>
                                    <td>
                                        <a href="<? echo $img; ?>"><? echo $img; ?></a>
                                    </td>
                                </tr>
                            </table>
                            <?
                            }
                            ?>
                        </td>
                    </tr>
                </tbody>
            </table>
        </td>
            <?
            if($i == $width)
            {
                $c = $i+1;
                echo "</tr>
                <tr><td colspan='{$c}'></td></tr><tr>";
                $i=0;
            }else
            {
                $i++;
            }
        }
        ?>
    </tr>
</table>
</body>
</html>






<html>
<head>
    <style>
        body
        {
            background-color: lightblue;
        }
        table.main_table
        {
            width : 1920px;
            height: 1080px;
        }
        td.good
        {
            border-style: solid;
            border-width: 1;
            background-color : #00FF00;
        }
        td.bad
        {
            border-style: solid;
            border-width: 1;
            background-color : #FF0000;
        }
        td.warning
        {
            border-style: solid;
            border-width: 1;
            background-color : #FFFF00;
        }
        th
        {
            border-style: solid;
            border-width: 1;
            background-color : lightseagreen;
        }
        td.white
        {
            border-style: solid;
            border-width: 1;
        }
        table.printer_table
        {
            border-style: solid;
            border-width: 1;
            width: 200px;
        }
        td.all
        {
            border-style: solid;
            border-width: 1;
        }
        td.td_align
        {
            vertical-align: center;
            text-align: center;
        }

    </style>
</head>
<body>
<table class="main_table">
    <tr>
        <?
        $i = 0;
        foreach($stats as $key=>$stat)
        {
            $tray1_color = find_color((int)$stat['tray_1']);
            $tray2_color = find_color((int)$stat['tray_2']);
            $tray3_color = find_color((int)$stat['tray_3']);
            $toner_color = find_color((int)$stat['toner']);
            $kit_a_color = find_color((int)$stat['kit_a']);
            $kit_b_color = find_color((int)$stat['kit_b']);
            if($stat['status'] == "Offline" || $stat['status'] == "Alert")
            {
                $status_color = "bad";
            }else{
                $status_color = "white";
            }
            $count = number_format($stat['count']);
            $date = date('Y-m-d H:i:s', $stat['timestamp']);
            echo "
                <td class='td_align'>
                    <table class='printer_table'>
                        <tbody>
                            <tr>
                                <th class='all'>{$printer_names[$key]}</th>
                            </tr>
                            <tr>
                                <td class='all'>$date</td>
                            </tr>
                            <tr>
                                <td class='{$status_color}'>Status: <b>{$stat['status']}</b></td>
                            </tr>
                            <tr>
                                <td class='all'>Count: <b>{$count}</b></td>
                            </tr>
                            <tr>
                                <td class='{$tray1_color}'>Tray 1: <b>{$stat['tray_1']}</b></td>
                            </tr>
                            <tr>
                                <td class='{$tray2_color}'>Tray 2: <b>{$stat['tray_2']}</b></td>
                            </tr>
                            <tr>
                                <td class='{$tray3_color}'>Tray 3: <b>{$stat['tray_3']}</b></td>
                            </tr>
                            <tr>
                                <td class='{$toner_color}'>Toner: <b>{$stat['toner']}</b></td>
                            </tr>
                            <tr>
                                <td class='{$kit_a_color}'>Maint Kit A: <b>{$stat['kit_a']}</b></td>
                            </tr>
                            <tr>
                                <td class='{$kit_b_color}'>Maint Kit B: <b>{$stat['kit_b']}</b></td>
                            </tr>
                        </tbody>
                    </table>
                </td>";
            if($i == $width)
            {
                $c = $i+1;
                echo "</tr>
                    <tr><td colspan='{$c}'></td></tr><tr>";
                $i=0;
            }else
            {
                $i++;
            }
        }
        ?>
    </tr>
</table>
</body>
</html>