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

/**
 * User: PFerland
 * Date: 8/16/13
 * Time: 4:17 PM
 */

class printerStatuses {

    public function __construct($printers = array())
    {
        $this->printers = $printers;
        $this->printerIndex = 0;
        $this->numPrinters = count($printers)-1;
    }

    public function nextPrinter()
    {
        if($this->printerIndex === $this->numPrinters)
        {
            return NULL;
        }else
        {
            $this->printerIndex++;
        }
    }

    public function previousPrinter()
    {
        $this->printerIndex--;
    }

    private function getPrinterName()
    {
        return $this->printers[$this->printerIndex];
    }

    public function resetIndex()
    {
        $this->printerIndex = 0;
    }

    function urlExists($url = NULL)
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

    public function getAll($first = 0)
    {
        $name = $this->getPrinterName();
        if(!$this->urlExists("http://$name/"))
        {
            echo date("Y-m-d H:i:s")." ---- Printer ".$name." Offline, setting blank data and skipping checks...\r\n";
            $data['name']   = $name;
            $data['time']   = time();
            if($first)
            {
                $data['mac']    = "";
                $data['serial'] = "";
            }
            $data['status'] = array(0=>"Offline", 1=>"");
            $data['paper']  = array("Tray 1"=>"","Tray 2"=>"","Tray 3"=>"");
            $data['count']  = 0;
            $data['levels'] = array("Toner"=>0.0,"Maint Kit A"=>0.0,"Maint Kit B"=>0.0);
        }else{
            echo date("Y-m-d H:i:s")." ---- Checking Printer ".$name." ...\r\n";
            $data['name']   = $name;
            $data['time']   = time();
            if($first)
            {
                echo "Mac Address...";
                $data['mac']    = $this->getMac();
                echo "Serial number...";
                $data['serial'] = $this->getSerial();
            }
            echo "Printer Status...";
            $data['status'] = $this->getPrinterStatus();
            echo "Paper Levels...";
            $data['paper']  = $this->getPaperLevels();
            echo "Page Count...";
            $data['count']  = $this->getPageCount();
            echo "Ink Levels...\r\n";
            $data['levels'] = $this->getInkLevels();
        }
        return $data;
    }

    public function getPrinterStatus()
    {
        $html = file_get_html("http://{$this->printers[$this->printerIndex]}/web/guest/en/websys/webArch/topPage.cgi");
        foreach($html->find('font') as $element)
        {
            $status = $element->innertext;
        }
        foreach($html->find('textarea') as $element)
        {
            $desc = $element->innertext;
        }
        $data = array($status, $desc);
        return $data;
    }

    public function getInkLevels()
    {
        $levels = array();
        $html = file_get_html("http://{$this->printers[$this->printerIndex]}/web/guest/en/webprinter/supply.cgi");
        foreach($html->find('td') as $key=>$element)
        {
            switch($key)
            {
                case 77:
                    $label = "Toner";
                    $width = $element->find("img")[0]->width;
                    $levels[$label] = round( ((($width+0)/162)*100), 2);
                    break;
                case 105:
                    $label = "Maint Kit A";
                    $width = $element->find("img")[0]->width;
                    $levels[$label] = round( ((($width+0)/162)*100), 2);
                    break;
                case 119:
                    $label = "Maint Kit B";
                    $width = $element->find("img")[0]->width;
                    $levels[$label] = round( ((($width+0)/162)*100), 2);
                    break;
            }
        }
        return $levels;
    }

    public function getPaperLevels()
    {
        $paper = array();
        $html = file_get_html("http://{$this->printers[$this->printerIndex]}/web/guest/en/websys/webArch/topPage.cgi");
        foreach($html->find('td') as $key=>$element)
        {
            switch($key)
            {
                case 139:
                    $paper["Tray 1"] = str_replace(array("/images/deviceStP", "_16.gif"), "", $element->find("img")[0]->src);
                    break;
                case 153:
                    $paper["Tray 2"] = str_replace(array("/images/deviceStP", "_16.gif"), "", $element->find("img")[0]->src);
                    break;
                case 167:
                    $paper["Tray 3"] = str_replace(array("/images/deviceStP", "_16.gif"), "", $element->find("img")[0]->src);
                    break;
            }
        }
        return $paper;
    }

    public function getPageCount()
    {
        $html = file_get_html("http://{$this->printers[$this->printerIndex]}/web/guest/en/websys/status/getUnificationCounter.cgi");
        foreach($html->find('td') as $key=>$element)
        {
            if($key != 57) { continue; }
            $count = $element->innertext;
            break;
        }
        return $count;
    }

    public function getSerial()
    {
        $html = file_get_html("http://{$this->printers[$this->printerIndex]}/web/guest/en/websys/status/configuration.cgi");
        foreach($html->find('td') as $key=>$element)
        {
            if($key === 92)
            {
                $serial = $element->innertext;
                break;
            }
        }
        return $serial;
    }

    public function getMac()
    {
        $html = file_get_html("http://{$this->printers[$this->printerIndex]}/web/guest/en/websys/netw/getInterface.cgi");
        if(empty($html))
        {
            return -1;
        }
        foreach($html->find('td') as $key=>$element)
        {
            if($key === 36)
            {
                $mac = $element->innertext;
                break;
            }
        }
        return $mac;
    }
}