<?php
/*
printerStatuses.php
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


class printerStatuses
{
    public static $dataFetchErrorFlag;
    public function __construct($printers = array(), $models = array())
    {
        $this->printers = $printers;
        $this->printerFirstRun = 0;
        $this->printerIndex = 0;
        $this->numPrinters = count($printers)-1;
        $this->currentPrinterName = "";
        $this->currentPrinterCampus = "";
        $this->createPrinterClasses($models);
        $this->currentPrinterModel = "";
        $this->dataFetchErrorFlag = 0;
    }

    public function nextPrinter()
    {
        if($this->printerIndex === $this->numPrinters)
        {
            return NULL;
        }else
        {
            $this->dataFetchErrorFlag = 0;
            $this->printerIndex++;
        }
    }

    private function createPrinterClasses($models = array())
    {
        foreach($models as $model)
        {
            require "models/$model.model.php";
            $this->$model = new $model();
        }
    }

    public function previousPrinter()
    {

        if($this->printerIndex === 0)
        {
            return 0;
        }else
        {
            $this->dataFetchErrorFlag = 0;
            $this->printerIndex;
            return 0;
        }
    }

    private function getPrinterModel()
    {
        return $this->printers[$this->printerIndex][1];
    }

    private function getPrinterName()
    {
        return $this->printers[$this->printerIndex][0];
    }

    public function resetIndex()
    {
        $this->dataFetchErrorFlag = 0;
        $this->printerIndex = 0;
    }

    public static function urlExists($url = NULL)
    {
        if($url == NULL) return false;
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        if($httpcode>=200 && $httpcode<300){
            return true;
        } else {
            return false;
        }
    }

    public function getAll($name = NULL, $model = NULL)
    {
        if($name === NULL)
        {
            $name = $this->getPrinterName();
        }
        if($model === NULL)
        {
            $model = $this->getPrinterModel();
        }
        $this->currentPrinterModel = $model;
        $data['name']   = $name;
        $data['time']   = time();
        $this->$model->currentPrinterName = $name;
        if($this->printerFirstRun)
        {
            echo "Mac Address...";
            $data['mac']    = $this->$model->getMac();
            echo "Serial number...";
            $data['serial'] = $this->$model->getSerial();
        }
        echo "Printer Status...";
        $data['status'] = $this->$model->getPrinterStatus();
        echo "Paper Levels...";
        $data['paper']  = $this->$model->getPaperLevels();
        echo "Page Count...";
        $data['count']  = $this->$model->getPageCount();
        echo "Ink Levels...\r\n";
        $data['levels'] = $this->$model->getInkLevels();

        if($this->dataFetchErrorFlag)
        {
            return -1;
        }else
        {
            return $data;
        }
    }
}