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

require 'lib/simple_html_dom.php'; 
require 'lib/printerStatuses.php';
require 'lib/daemon.php';
require 'config.php';
require 'printers_list.php';

date_default_timezone_set($config['timezone']); 
echo date("Y-m-d H:i:s")."-------------------\r\nConstructing the Daemon Class...\r\n";
$daemon = new daemon($config, $printers, new printerStatuses($printers));
echo date("Y-m-d H:i:s")."-------------------\r\nStarting Main loop...\r\n";
while(1)
{
    $daemon->restartSQL(); #make sure that the SQL connection is still there. If not, restart it.
    $daemon->printerStatuses->resetIndex(); #reset the printer pointer index back to 0.
    foreach($daemon->printerStatuses->printers as $printer)
    {
        echo date("Y-m-d H:i:s")." -------------------\r\nGather Printer Info...\r\n";
        $data = $daemon->printerStatuses->getAll();
        echo date("Y-m-d H:i:s")." -------------------\r\nInsert into DB...\r\n";
        $daemon->insertData($data);
        $daemon->printerStatuses->nextPrinter(); #Increment into next Printer Pointer index.
    }
    echo date("Y-m-d H:i:s")." -------------------\r\n Sleeping for 10 min.\r\n";
    sleep(600);
}