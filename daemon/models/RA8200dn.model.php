<?php

class RA8200dn {

    public function __construct()
    {
        $this->currentPrinterName = "";
    }

    public function getPrinterStatus()
    {
        $result = printerStatuses::urlExists("http://{$this->currentPrinterName}/web/guest/en/websys/webArch/topPage.cgi");
        if(!$result)
        {
            printerStatuses::$dataFetchErrorFlag = 1;
            return -1;
        }

        $html = file_get_html("http://{$this->currentPrinterName}/web/guest/en/websys/webArch/topPage.cgi");
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
        $result = printerStatuses::urlExists("http://{$this->currentPrinterName}/web/guest/en/webprinter/supply.cgi");
        if(!$result)
        {
            printerStatuses::$dataFetchErrorFlag = 1;
            return -1;
        }
        $html = @file_get_html("http://{$this->currentPrinterName}/web/guest/en/webprinter/supply.cgi");

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
        $result = printerStatuses::urlExists("http://{$this->currentPrinterName}/web/guest/en/websys/webArch/topPage.cgi");
        if(!$result)
        {
            printerStatuses::$dataFetchErrorFlag = 1;
            return -1;
        }

        $html = file_get_html("http://{$this->currentPrinterName}/web/guest/en/websys/webArch/topPage.cgi");

        if(is_null(@$html->find('td')[139]->find("img")[0]))
        {
            $tray_1_row = 140;
            $tray_2_row = 156;
            $tray_3_row = 170;
        }else
        {
            $tray_1_row = 139;
            $tray_2_row = 153;
            $tray_3_row = 167;
        }
        foreach($html->find('td') as $key=>$element)
        {
            #var_dump($key);
            switch($key)
            {
                case $tray_1_row:
                    $tray_1 = str_replace(array("/images/deviceStP", "_16.gif"), "", @$element->find("img")[0]->src);
                    switch($tray_1)
                    {
                        case "Nend16.gif":
                            $paper["Tray 1"] = 5;
                            break;
                        case "end16.gif":
                            $paper["Tray 1"] = 0;
                            break;
                        case "Error16.gif":
                            $paper["Tray 1"] = 0;
                            break;
                        default:
                            $paper["Tray 1"] = $tray_1+0;
                            break;
                    }
                    break;
                case $tray_2_row:
                    $tray_2 = str_replace(array("/images/deviceStP", "_16.gif"), "", $element->find("img")[0]->src);
                    switch($tray_2)
                    {
                        case "Nend16.gif":
                            $paper["Tray 2"] = 5;
                            break;
                        case "end16.gif":
                            $paper["Tray 2"] = 0;
                            break;
                        case "Error16.gif":
                            $paper["Tray 2"] = 0;
                            break;
                        default:
                            $paper["Tray 2"] = $tray_2+0;
                            break;
                    }
                    break;
                case $tray_3_row:
                    $tray_3 = str_replace(array("/images/deviceStP", "_16.gif"), "", $element->find("img")[0]->src);
                    switch($tray_3)
                    {
                        case "Nend16.gif":
                            $paper["Tray 3"] = 5;
                            break;
                        case "end16.gif":
                            $paper["Tray 3"] = 0;
                            break;
                        case "Error16.gif":
                            $paper["Tray 3"] = 0;
                            break;
                        default:
                            $paper["Tray 3"] = $tray_3+0;
                            break;
                    }
                    break;
            }
        }
        return $paper;
    }

    public function getPageCount()
    {
        $result = printerStatuses::urlExists("http://{$this->currentPrinterName}/web/guest/en/websys/status/getUnificationCounter.cgi");
        if(!$result)
        {
            printerStatuses::$dataFetchErrorFlag = 1;
            return -1;
        }

        $html = file_get_html("http://{$this->currentPrinterName}/web/guest/en/websys/status/getUnificationCounter.cgi");
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
        $result = printerStatuses::urlExists("http://{$this->currentPrinterName}/web/guest/en/websys/status/configuration.cgi");
        if(!$result)
        {
            printerStatuses::$dataFetchErrorFlag = 1;
            return -1;
        }
        $html = file_get_html("http://{$this->currentPrinterName}/web/guest/en/websys/status/configuration.cgi");
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
        $result = printerStatuses::urlExists("http://{$this->currentPrinterName}/web/guest/en/websys/netw/getInterface.cgi");
        if(!$result)
        {
            printerStatuses::$dataFetchErrorFlag = 1;
            return -1;
        }
        $html = file_get_html("http://{$this->currentPrinterName}/web/guest/en/websys/netw/getInterface.cgi");
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