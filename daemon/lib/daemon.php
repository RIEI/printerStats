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
    public $SQL;
    public $graphs;
    public $printerStatuses;
    public $campuses;
    public $models;
    public $printerCampus_oneRun;
    public $printerOnlyName;
    public $graphsOnlyFlag;
    public function __construct($argv, $config, $printers = array())
    {
        require 'lib/graphs.php';
        require 'lib/SQL.inc.php';
        $this->args = $this->parseArgs($argv);
        if(@$this->args['g'] === true or @$this->args['graphonly'] === true)
        {
            $this->graphsOnlyFlag = 1;
        }else
        {
            $this->graphsOnlyFlag = 0;
        }

        if(@$this->args['p'] == true)
        {
            $this->printerOnlyName = $this->args['p'];
        }elseif(@$this->args['printer'] == true)
        {
            $this->printerOnlyName = $this->args['printer'];
        }else
        {
            $this->printerOnlyName = 0;
        }

        if(@$this->args['c'] == true)
        {
            $this->printerCampus_oneRun = $this->args['c'];
        }elseif(@$this->args['campus'] == true)
        {
            $this->printerCampus_oneRun = $this->args['campus'];
        }else
        {
            $this->printerCampus_oneRun = 0;
        }

        if(@$this->args['m'] == true)
        {
            $this->printerOnlyModel = $this->args['m'];
        }elseif(@$this->args['model'] == true)
        {
            $this->printerOnlyModel = $this->args['model'];
        }else
        {
            $this->printerOnlyModel = 0;
        }
        $this->verbose_levels = array   (
            0=>"Normal",
            1=>"Warnings",
            2=>"Errors",
            3=>"All",
            4=>"Debug",
        );
        $this->sleepTime = 10;
        $this->config = $config;
        $this->SQL = new SQL($config);
        $this->graphs = new graphs($this->SQL, $config['wwwroot']);
        $this->models = $this->getPrinterModels($printers);
        $this->campuses = array();
        $this->printerStatuses = new printerStatuses($this->parsePrintersList($printers), $this->models);
        if(!$this->graphsOnlyFlag)
        {
            $this->verbose("Check Campus Table", 3);
            $this->checkCampusTable($printers);

            $this->verbose("Check Printers Table", 3);
            $this->checkPrinterTable($printers);
        }
    }

    public function verbose($message = NULL, $level = NULL)
    {
        if(is_null($message))
        {
            throw new ErrorException("Message in verbose is null...");
            return 1;
        }
        if(is_null($level))
        {
            throw new ErrorException("Level in verbose is null...");
            return 1;
        }
        echo date("Y-m-d H:i:s")."\t-\t$message\r\n";

        return 0;
    }

    public function log()
    {

    }

    public function getCampusName($id = NULL)
    {
        if($id === NULL)
        {
            throw new ErrorException("ID argument is NULL.");
        }
        $sql = "SELECT `campus_id` FROM `printers`.`campuses` WHERE `id` = ?";
        $prep = $this->SQL->conn->prepare($sql);
        $prep->bindParam(1, $id, PDO::PARAM_INT);
        $prep->execute();
        $this->SQL->checkError();
        $fetch = $prep->fetch(2);
        return $fetch['campus_name'];
    }

    private function checkCampusTable($printers = array())
    {
        if(empty($printers))
        {
            throw new ErrorException("Printers array cannot be empty, you need to be able to check something...");
        }
        foreach($printers as $campus => $printers)
        {
            $prep = $this->SQL->conn->prepare("SELECT * FROM `printers`.`campuses` WHERE `campus_name` = ? LIMIT 1");
            $prep->bindParam(1, $campus, PDO::PARAM_STR);
            $prep->execute();
            $this->SQL->checkError();
            $fetch = $prep->fetch(2);
            if(empty($fetch))
            {
                $prep1 = $this->SQL->conn->prepare("INSERT INTO `printers`.`campuses` (`campus_name`) VALUES ( ? )");
                $prep1->bindParam(1, $campus, PDO::PARAM_STR);
                $prep1->execute();
                $this->SQL->checkError();
                $id = $this->SQL->conn->lastInsertId();
                echo date("Y-m-d H:i:s")." ----- Inserted new row for Campus: $campus (ID: {$id})\r\n";
            }else{
                $id = $fetch['id'];
                echo date("Y-m-d H:i:s")." ----- Campus ($campus) already exists, (ID: $id)\r\n";
            }
            $this->campuses[$id] = $campus;
        }
    }

    public function getPrinterName()
    {
        return $this->printerStatuses->printers[$this->printerStatuses->printerIndex][0];
    }

    public function getPrinterModels($printers = array())
    {
        $models_ = array();
        foreach($printers as $printers)
        {
            foreach($printers as $printer)
            {
                $models_[] = $printer[1];
            }
        }
        return array_unique($models_);
    }

    private function parsePrintersList($printers)
    {
        $printers_new = array();
        foreach($printers as $key=>$printer)
        {
            $campus = $key;
            foreach($printer as $p)
            {
                $p[2] = $campus;
                $printers_new[] = $p;
            }
            echo "-----------\r\n";
        }
        return $printers_new;
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
        foreach($printers as $campus => $printers)
        {
            echo date("Y-m-d H:i:s")." ----- Campus: $campus...\r\n";
            foreach($printers as $printer)
            {
                $prep = $this->SQL->conn->prepare("SELECT `id`, `name`, `mac`, `serial` FROM `printers`.`printers` WHERE `name` = ?");
                $prep->bindParam(1, $printer[0], PDO::PARAM_STR);
                $prep->execute();
                $this->SQL->checkError();
                $fetch = $prep->fetch(2);
                if(empty($fetch))
                {
                    $this->printerStatuses->printerFirstRun = 1;
                    echo date("Y-m-d H:i:s")." ----- Fetching data...\r\n";
                    $data = $this->printerStatuses->getAll($printer[0], $printer[1]);
                    if((empty($fetch['serial']) or empty($fetch['mac'])) and !empty($fetch['name']))
                    {
                        echo date("Y-m-d H:i:s")." ----- Updating Printer...\r\n";
                        $this->updatePrinter($fetch['id'], $data['mac'], $data['serial']);
                    }else
                    {
                        echo date("Y-m-d H:i:s")." ----- Creating Printer...\r\n";
                        $this->createPrinter($printer[0], $printer[1], $this->getCampusID($campus), $data['mac'], $data['serial']);
                    }
                    echo date("Y-m-d H:i:s")." ----- Inserting History Data...\r\n";
                    $this->insertData($data);
                }else{$this->printerStatuses->printerFirstRun = 0;}
            }
            $this->printerStatuses->printerFirstRun = 0;
        }
    }

    private function updatePrinter($id, $mac, $serial)
    {
        $prep = $this->SQL->conn->prepare("UPDATE `printers`.`printers` SET `mac` = ? AND `serial` = ? WHERE `id` = ?");
        $prep->bindParam(1, $mac, PDO::PARAM_STR);
        $prep->bindParam(2, $serial, PDO::PARAM_STR);
        $prep->bindParam(3, $id, PDO::PARAM_INT);
        $prep->execute();
        $this->SQL->checkError();
        echo date("Y-m-d H:i:s")." ----- Updated Printer Row with MAC Address and Serial Number...\r\n";
        return 1;
    }

    public function getCampusID($campus = NULL)
    {
        if($campus === NULL)
        {
            throw new ErrorException("Campus argument is NULL.");
        }
        $prep = $this->SQL->conn->prepare("SELECT `id` FROM `printers`.`campuses` WHERE `campus_name` = ?");
        $prep->bindParam(1, $campus, PDO::PARAM_STR);
        $prep->execute();
        $this->SQL->checkError();
        $fetch = $prep->fetch(2);
        return $fetch['id'];
    }

    public function createPrinter($printer, $model, $campus_id, $mac, $serial)
    {
        echo date("Y-m-d H:i:s")." ----- Creating Info Row for Printer: $printer ...\r\n";
        $prep = $this->SQL->conn->prepare("INSERT INTO `printers`.`printers`
        (
            `id`,
            `name`,
            `mac`,
            `serial`,
            `model`,
            `campus_id`
        )
        VALUES
        (
            '',
            ?,
            ?,
            ?,
            ?,
            ?
        )");

        $prep->bindParam(1, $printer, PDO::PARAM_STR);
        $prep->bindParam(2, $mac, PDO::PARAM_STR);
        $prep->bindParam(3, $serial, PDO::PARAM_STR);
        $prep->bindParam(4, $model, PDO::PARAM_STR);
        $prep->bindParam(5, $campus_id, PDO::PARAM_STR);
        $prep->execute();
        $this->SQL->checkError();
    }

    public function getPrinterID($name = "")
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

    function parseArgs($argv)
    {
        array_shift($argv);
        $out = array();
        foreach ($argv as $arg)
        {
            if (substr($arg,0,2) == '--')
            {
                $eqPos = strpos($arg,'=');
                if ($eqPos === false)
                {
                    $key = substr($arg,2);
                    $out[$key] = isset($out[$key]) ? $out[$key] : true;
                } else
                {
                    $key = substr($arg,2,$eqPos-2);
                    $out[$key] = substr($arg,$eqPos+1);
                }
            }elseif(substr($arg,0,1) == '-')
            {
                if (substr($arg,2,1) == '=')
                {
                    $key = substr($arg,1,1);
                    $out[$key] = substr($arg,3);
                }else
                {
                    $chars = str_split(substr($arg,1));
                    foreach ($chars as $char)
                    {
                        $key = $char;
                        $out[$key] = isset($out[$key]) ? $out[$key] : true;
                    }
                }
            }else
            {
                $out[] = $arg;
            }
        }
        return $out;
    }
}