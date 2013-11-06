<?php
/*
graphs.php
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
 * Desc: Make Graphs for the printers stats
 */
class graphs
{
    public function __construct($SQL, $wwwroot)
    {
        $this->wwwroot = $wwwroot;
        $this->SQL = $SQL;
    }

#==============================================================================================================================================================#
#													Image Grid Function												         #
#==============================================================================================================================================================#

    private function imagegrid($image, $w, $h, $s, $color)
    {
        $ws = $w/$s;
        $hs = $h/$s;
        for($iw=0; $iw < $ws; ++$iw)
        {
            imageline($image, ($iw-0)*$s, 60 , ($iw-0)*$s, $w , $color);
        }
        for($ih=0; $ih < ($hs - 8); ++$ih)
        {
            imageline($image, 0, $ih*$s, $w , $ih*$s, $color);
        }
    }


    public function line($data, $stat, $file_name, $printer_name, $campus, $bgc='255:255:255', $linec = '255:0:0', $text = '0:0:0')
    {
        $y=20;
        $yy=21;
        $u=20;
        $uu=21;
        if ($text == 'rand' or $text == '')
        {
            $tr = rand(50,200);
            $tg = rand(50,200);
            $tb = rand(50,200);
        }else
        {
            $text_color = explode(':', $text);
            $tr=$text_color[0];
            $tg=$text_color[1];
            $tb=$text_color[2];
        }
        if ($linec == 'rand' or $linec == '')
        {
            $r = rand(50,200);
            $g = rand(50,200);
            $b = rand(50,200);
        }else
        {
            $line_color = explode(':', $linec);
            $r=$line_color[0];
            $g=$line_color[1];
            $b=$line_color[2];
        }
        $count = count($data);
        if(900 < ($count*6.2))
        {
            $Height = 640;
            $wid    = ($count*6.2)+40;

        }else
        {
            $Height = 640;
            $wid    = 640;
        }
        $img    = ImageCreateTrueColor($wid, $Height);
        $bgcc	= explode(":",$bgc);
        $bg     = imagecolorallocate($img, $bgcc[0], $bgcc[1], $bgcc[2]);
        if($bgc !== "000:000:000")
        {
            $grid   = imagecolorallocate($img,0,0,0);
        }else
        {
            $grid   = imagecolorallocate($img,255,255,255);
        }
        $tcolor = imagecolorallocate($img, $tr, $tg, $tb);
        $col = imagecolorallocate($img, $r, $g, $b);
        imagefill($img,0,0,$bg); #PUT HERE SO THAT THE TEXT DOESN'T HAVE BLACK FILLINGS (eww)
        $c1 = "Name  : $printer_name        Stat Label: $stat";
        $c2 = "Campus: $campus              Date & Time : ".date("Y-m-d H:i:s");
        imagestring($img, 4, 21, 3, $c1, $tcolor);
        imagestring($img, 4, 21, 23, $c2, $tcolor);
        #signal strenth numbers--
        $p=460;
        $I=0;
        if($stat == "count")
        {
            foreach($data as $point)
            {
                $data_tmp[] = $point['count'];
            }
            if(count($data_tmp) < 1)
            {
                return 0;
            }

            $high = $data_tmp[0];
            if(($data_tmp[count($data_tmp)-1]+0) === 0)
            {
                $low = 0;
            }else
            {
                $low = $data_tmp[count($data_tmp)-1];
            }
            $total_diff = ($high - $low);
            #var_dump($total_diff);

            #die();
            while($I < 105)
            {
                imagestring($img, 4, 3, $p, $low, $tcolor);
                $low = $low+20;
                $I = $I+5;
                $p = $p-20;
            }
        }else{
            while($I<105)
            {
                imagestring($img, 4, 3, $p, $I, $tcolor);
                $I=$I+5;
                $p=$p-20;
            }

        }
        #end signal strength numbers--
        imagesetstyle($img, array($bg, $grid));
        imagesetstyle($img,array($bg,$grid));
        $this->imagegrid($img,$wid,$Height,19.99,$grid);
        $count = $count - 1;
        $nn = $count - 1;
        $tc = 6;
        $th = 21;

        while($count>=0)
        {
            if($stat === "count")
            {
                $linewidth = 4.1;
                $segment1 = @$data[$count][$stat]+0;
                $segment2 = @$data[$nn][$stat]+0;
                #var_dump($segment1, $segment2);
                #$diff = $segment2 - $segment1;
                $diff1 = $segment1 - $low;
                $diff2 = $segment2 - $low;
                if($diff1 < 0)
                {
                    $diff1 = 1;
                }
                if($diff2 < 0)
                {
                    $diff2 = 1;
                }
                $segment1 =  ($diff1 / $total_diff)*105;
                $segment2 =  ($diff2 / $total_diff)*105;
                #var_dump($total_diff1);
                #var_dump($segment1, $segment2);

            }else
            {
                $linewidth = 4;
                $segment1 = @$data[$count][$stat];
                $segment2 = @$data[$nn][$stat];
            }
            if($tc === 0)
            {
                $stamp1 = date("Y-m-d H:i:s", $data[$count]['timestamp']);
                $stamp = date("Y-m-d H:i:s", @$data[$nn]['timestamp']);

                $len = strlen($stamp)*8;
                $len1 = strlen($stamp1)*8;
                imagestringup($img, 4, $th, $len1+481, $stamp1, $col);
                imagestringup($img, 4, $th = $th+19.99, $len+481, $stamp, $col);
                $th = $th+19.99;
                $tc = 6;
            }else
            {
                $tc--;
            }
            imageline($img, $y, 459-($segment1*$linewidth), $y=$y+5.55, 459-($segment2*$linewidth), $col);
            imageline($img, $u, 460-($segment1*$linewidth), $u=$u+5.55, 460-($segment2*$linewidth), $col);
            imageline($img, $yy, 459-($segment1*$linewidth), $yy=$yy+5.55, 459-($segment2*$linewidth), $col);
            imageline($img, $uu, 460-($segment1*$linewidth), $uu=$uu+5.55, 460-($segment2*$linewidth), $col);
            $count--;
            $nn = $count-1;
        }
        $file = $this->wwwroot.'/graphs/'.$file_name.'_'.$stat.'.png';
        ImagePNG($img, $file);
        ImageDestroy($img);
        return $file;
    }

    public function makeLessThan100($int = 0)
    {
        $cal = $int/10;
        if($cal > 100)
        {
            $cal = $this->makeLessThan100($cal);
        }
        return $cal;
    }

    public function genGraphs($data = array(), $printer_name = NULL, $campus = NULL)
    {
        if(empty($data[0]))
        {
            throw new ErrorException("Data array for genGraphs os empty.");
        }
        if($printer_name === NULL)
        {
            throw new ErrorException("Printer Name argument is NULL");
        }
        if($campus === NULL)
        {
            throw new ErrorException("Campus ID argument is NULL");
        }
        #get the newest stats for each printer.

        $file_name = $campus."_".$printer_name;

        $ret['name'] = $printer_name;

        $ret['toner'] = $this->line($data, "toner", $file_name, $printer_name, $campus);

        $ret['kit_a'] = $this->line($data, "kit_a", $file_name,  $printer_name, $campus);

        $ret['kit_b'] = $this->line($data, "kit_b", $file_name, $printer_name, $campus);

        $ret['tray_1'] = $this->line($data, "tray_1", $file_name, $printer_name, $campus);

        $ret['tray_2'] = $this->line($data, "tray_2", $file_name, $printer_name, $campus);

        $ret['tray_3'] = $this->line($data, "tray_3", $file_name, $printer_name, $campus);

        $ret['count'] = $this->line($data, "count", $file_name, $printer_name, $campus);
        return $ret;
    }

}