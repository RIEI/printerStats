<?php
/*
daemon.php
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
 * User: PFerland
 * Date: 8/17/13
 * Time: 2:15 PM
 * To change this template use File | Settings | File Templates.
 */

class daemon {
    private $config;
    public function __construct($config, $printers = array(), $printerObj)
    {
        $this->config = $config;

        require 'lib/SQL.inc.php';
        $this->SQL = new SQL($config);
        $this->printerStatuses = $printerObj;
        $this->checkPrinterTable($printers);
    }

    public function restartSQL()
    {
        unset($this->SQL);
        $this->SQL = new SQL($this->config);
    }

    private function checkPrinterTable($printers = array())
    {
        if(empty($printers))
        {
            throw new ErrorException("Printers array cannot be empty, you need to be able to check something...");
        }
        $this->printerStatuses->resetIndex();
        foreach($printers as $printer)
        {
            $result = $this->SQL->conn->query("SELECT `id`, `name`, `mac`, `serial` FROM `printers`.`printers` WHERE `name` = '{$printer}'");
            $this->SQL->checkError();
            $fetch = $result->fetch(2);
            if(empty($fetch))
            {
                echo date("Y-m-d H:i:s")." ----- Fetching data...\r\n";
                $data = $this->printerStatuses->getAll(1);
                echo date("Y-m-d H:i:s")." ----- Creating Printer...\r\n";
                $this->createPrinter($printer, $data);
                echo date("Y-m-d H:i:s")." ----- Inserting History Data...\r\n";
                $this->insertData($data);
            }
            $this->printerStatuses->nextPrinter();
        }
        $this->printerStatuses->resetIndex();
    }

    public function createPrinter($printer, $info)
    {
        echo date("Y-m-d H:i:s")." ----- Creating Info Row for Printer: $printer ...\r\n";
        $prep = $this->SQL->conn->prepare("INSERT INTO `printers`.`printers`
        (
            `id`,
            `name`,
            `mac`,
            `serial`
        )
        VALUES
        (
            '',
            ?,
            ?,
            ?
        )");
        $prep->bindParam(1, $printer, PDO::PARAM_STR);
        $prep->bindParam(2, $info['mac'], PDO::PARAM_STR);
        $prep->bindParam(3, $info['serial'], PDO::PARAM_STR);
        $prep->execute();
        $this->SQL->checkError();
    }

    private function getPrinterID($name = "")
    {
        $result = $this->SQL->conn->query("SELECT `id` FROM `printers`.`printers` WHERE `name` = '".$name."'");
        $this->SQL->checkError();
        $fetch = $result->fetch(2);
        return $fetch['id'];
    }

    public function insertData($data = array())
    {
        if(empty($data))
        {
            throw new ErrorException("Data array is empty, you cant put no data in the table...");
        }
        $prep = $this->SQL->conn->prepare("INSERT INTO  `printers`.`history` (
            `id` ,
            `printer_id` ,
            `timestamp` ,
            `status` ,
            `desc` ,
            `tray_1` ,
            `tray_2` ,
            `tray_3` ,
            `count` ,
            `toner` ,
            `kit_a` ,
            `kit_b`
            )
            VALUES (
            '' ,
            ?,
            ?,
            ?,
            ?,
            ?,
            ?,
            ?,
            ?,
            ?,
            ?,
            ?
        )");
        $printerID = $this->getPrinterID($data['name']);
        $prep->bindParam(1, $printerID, PDO::PARAM_STR);
        $prep->bindParam(2, $data['time'], PDO::PARAM_INT);
        $prep->bindParam(3, $data['status'][0], PDO::PARAM_STR);
        $prep->bindParam(4, $data['status'][1], PDO::PARAM_STR);
        $prep->bindParam(5, $data['paper']['Tray 1'], PDO::PARAM_INT);
        $prep->bindParam(6, $data['paper']['Tray 2'], PDO::PARAM_INT);
        $prep->bindParam(7, $data['paper']['Tray 3'], PDO::PARAM_INT);
        $prep->bindParam(8, $data['count'], PDO::PARAM_INT);
        $prep->bindParam(9, $data['levels']['Toner'], PDO::PARAM_STR);
        $prep->bindParam(10, $data['levels']['Maint Kit A'], PDO::PARAM_STR);
        $prep->bindParam(11, $data['levels']['Maint Kit B'], PDO::PARAM_STR);
        $prep->execute();
        $this->SQL->checkError();
        echo date("Y-m-d H:i:s")." ----- Data inserted.\r\n";

    }
}