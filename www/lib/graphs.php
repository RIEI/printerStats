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

class graphs
{
    public function __construct()
    {

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
        for($ih=0; $ih<$hs; ++$ih)
        {
            imageline($image, 0, $ih*$s, $w , $ih*$s, $color);
        }
    }
	
    public function line($data, $stat, $name, $bgc='255:255:255', $linec = '255:0:0', $text = '0:0:0')
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
            $Height = 480;
            $wid    = ($count*6.2)+40;

        }else
        {
            $Height = 480;
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
        #signal strenth numbers--
        $p=460;
        $I=0;
        while($I<105)
        {
            imagestring($img, 4, 3, $p, $I, $tcolor);
            $I=$I+5;
            $p=$p-20;
        }
        #end signal strenth numbers--
        imagesetstyle($img, array($bg, $grid));
        $n=0;
        $nn=1;
        imagesetstyle($img,array($bg,$grid));
        $this->imagegrid($img,$wid,$Height,19.99,$grid);
        while($count>0)
        {
            if($stat === "count")
            {
                $segment1 = $this->makeLessThan100($data[$n][$stat]);
                #var_dump($segment1);
                $segment2 = $this->makeLessThan100(@$data[$nn][$stat]);
                #var_dump($segment2);
            }else
            {
                $segment1 = $data[$n][$stat];
                $segment2 = @$data[$nn][$stat];
            }

            imageline($img, $y, 459-($segment1*4), $y=$y+6, 459-($segment2*4), $col);
            imageline($img, $u, 460-($segment1*4), $u=$u+6, 460-($segment2*4), $col);
            imageline($img, $yy, 459-($segment1*4), $yy=$yy+6, 459-($segment2*4), $col);
            imageline($img, $uu, 460-($segment1*4), $uu=$uu+6, 460-($segment2*4), $col);
            $n++;
            $nn++;
            $count--;
        }
        $date = date("Y-m-d");
        $file = 'graphs/'.$name.'_'.$date.'_'.str_pad(rand(0,999999), 6, "0").'_'.$stat.'.png';
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
}