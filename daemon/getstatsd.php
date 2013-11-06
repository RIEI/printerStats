<?php
/*
get_stats.php
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


/**
 * User: Pferland
 * Date: 8/16/13
 * Time: 10:07 AM
 * Desc: Get the Toner/ Maint Kit/ Errors from the printers
 */
date_default_timezone_set("EST");

require 'lib/simple_html_dom.php';
require 'lib/printerStatuses.php';
require 'lib/daemon.php';
require 'config/config.php';
require 'config/printers_list.php';

echo "printerStats V1.1 GPLv2 (23/Aug/2013) pferland@randomintervals.com\r\n";
echo date("Y-m-d H:i:s")."-------------------\tConstructing the Daemon Class...\r\n";
$daemon = new daemon($argv, $config, $printers);
echo date("Y-m-d H:i:s")."-------------------\tStarting Main loop...\r\n";
while(1)
{
    if($daemon->printerOnlyName !== 0)
    {
        onlyOnePrinter($daemon);
    }else
    {
        foreach_Printer($daemon);
    }
    echo date("Y-m-d H:i:s")." -------------------\t Sleeping for 10 min.\r\n";
    sleep($daemon->sleepTime);
}


function onlyOnePrinter($daemon)
{
    echo date("Y-m-d H:i:s")." -------------------\tCheck URL for Printer { $daemon->printerOnlyFlag }...\r\n";
    if($daemon->printerStatuses->urlExists("http://$daemon->printerOnlyFlag"))
    {
        if(!$daemon->graphsOnlyFlag)
        {
            echo date("Y-m-d H:i:s")." -------------------\tGather Printer Info...\r\n";
            $data = $daemon->printerStatuses->getAll($daemon->printerOnlyName, $daemon->printerOnlyModel);
            if($data === -1)
            {
                echo date("Y-m-d H:i:s")." -------------------\tOne of the data points returned with an error, skipping this import for this printer so there is no good data mixed with bad data...\r\n";
            }
            else
            {
                echo date("Y-m-d H:i:s")." -------------------\tInsert into DB...\r\n";
                $daemon->insertData($data);
            }

        }
        echo date("Y-m-d H:i:s")." -------------------\tGenerating Graphs...\r\n";

        $sql = "SELECT * FROM `printers`.`history` WHERE `printer_id` = '".$daemon->getPrinterID($daemon->printerOnlyName)."' and `count` != 0 ORDER BY `timestamp` DESC LIMIT 1000";
        $result2 = $daemon->SQL->conn->query($sql);
        $data = $result2->fetchAll(2);

        $daemon->graphs->genGraphs( $data, $daemon->printerOnlyFlag, $daemon->printerCampus_oneRun );
        #var_dump($graphs);
    }
    else
    {
        echo date("Y-m-d H:i:s")." -------------------\tPrinter Offline...\r\n";
        $data['name']   = $daemon->printerOnlyName;
        $data['time']   = time();
        $data['status'] = array(0=>"Offline", 1=>"");
        $data['paper']  = array("Tray 1"=>"","Tray 2"=>"","Tray 3"=>"");
        $data['count']  = 0;
        $data['levels'] = array("Toner"=>0.0,"Maint Kit A"=>0.0,"Maint Kit B"=>0.0);
        echo date("Y-m-d H:i:s")." -------------------\tInsert into DB...\r\n";
        $daemon->insertData($data);
    }
    $daemon->printerStatuses->nextPrinter();
    echo date("Y-m-d H:i:s")." -------------------\tNEXT!\r\n";
}


function foreach_Printer($daemon)
{
    $daemon->restartSQL(); # check to make sure the SQL connection is still there.
    $daemon->printerStatuses->resetIndex();
    foreach($daemon->printerStatuses->printers as $printer)
    {
        echo date("Y-m-d H:i:s")." -------------------\tCheck URL for Printer { $printer[0] }...\r\n";
        if($daemon->printerStatuses->urlExists("http://$printer[0]"))
        {
            if(!$daemon->graphsOnlyFlag)
            {
                echo date("Y-m-d H:i:s")." -------------------\tGather Printer Info...\r\n";
                $data = $daemon->printerStatuses->getAll();
                if($data === -1)
                {
                    echo date("Y-m-d H:i:s")." -------------------\tOne of the data points returned with an error, skipping this import for this printer so there is no good data mixed with bad data...\r\n";
                }
                else
                {
                    echo date("Y-m-d H:i:s")." -------------------\tInsert into DB...\r\n";
                    $daemon->insertData($data);
                }
            }
            echo date("Y-m-d H:i:s")." -------------------\tGenerating Graphs...\r\n";

            $sql = "SELECT * FROM `printers`.`history` WHERE `printer_id` = '".$daemon->getPrinterID($printer[0])."' and `count` != 0 ORDER BY `timestamp` DESC LIMIT 1000";
            $result2 = $daemon->SQL->conn->query($sql);
            $data = $result2->fetchAll(2);

            $daemon->graphs->genGraphs( $data, $printer[0], $daemon->printerCampus_oneRun );

        }
        else
        {
            echo date("Y-m-d H:i:s")." -------------------\tPrinter Offline...\r\n";
            $data['name']   = $printer[0];
            $data['time']   = time();
            $data['status'] = array(0=>"Offline", 1=>"");
            $data['paper']  = array("Tray 1"=>"","Tray 2"=>"","Tray 3"=>"");
            $data['count']  = 0;
            $data['levels'] = array("Toner"=>0.0,"Maint Kit A"=>0.0,"Maint Kit B"=>0.0);
            echo date("Y-m-d H:i:s")." -------------------\tInsert into DB...\r\n";
            $daemon->insertData($data);
        }
        $daemon->printerStatuses->nextPrinter();
        echo date("Y-m-d H:i:s")." --------------------------------------\t-->\tNEXT!\r\n";
    }
}