<html>
    <head>
        <meta http-equiv="refresh" content="300"> <!-- Refresh every 15 min -->
        <style>
            body
            {
                background-color: #8ab2c0;
            }
            table.main_table
            {
                width : 1850px;
                height: 1000px;
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
            td.center
            {
                text-align: center;
            }
            td.td_align
            {
                vertical-align: center;
                text-align: center;
            }
            a.links
            {
                color: BLACK;
            }
        </style>
    </head>

<body>
<p align="center">
<table>
    <tbody>
{foreach from=$campuses item="campus"}
    <tr>
        <th colspan="10"><a class="links" href="?campus_id={$campus.id}">{$campus.name}</a></th>
    </tr>
    <tr>
{foreach $campus.array as $printer}
    {$printer.new_row}
        <td class="center">
            <table class='printer_table'>
                <tbody>
                <tr>
                    <th class='all'><a class="links" href="http://{$printer.name}">{$printer.name}</a></th>
                </tr>
                <tr>
                    <td class='all'>{$printer.date}</td>
                </tr>
                <tr>
                    <td class='{$printer.status_color}'>Status: <b>{$printer.status|default:'Offline'}</b></td>
                </tr>
                <tr>
                    <td class='all'>Count: <b>{$printer.count|default:0}</b> <a class="links" href="graphs.php?id={$printer.printer_id}&amp;graph=pagecount">Graph</a></td>
                </tr>
                <tr>
                    <td class='{$printer.tray1_color}'>Tray 1: <b>{$printer.tray_1|default:0}</b> <a class="links" href="graphs.php?id={$printer.printer_id}&amp;graph=levels&amp;type=tray1">Graph</a></td>
                </tr>
                <tr>
                    <td class='{$printer.tray2_color}'>Tray 2: <b>{$printer.tray_2|default:0}</b> <a class="links" href="graphs.php?id={$printer.printer_id}&amp;graph=levels&amp;type=tray2">Graph</a></td>
                </tr>
                <tr>
                    <td class='{$printer.tray3_color}'>Tray 3: <b>{$printer.tray_3|default:0}</b> <a class="links" href="graphs.php?id={$printer.printer_id}&amp;graph=levels&amp;type=tray3">Graph</a></td>
                </tr>
                <tr>
                    <td class='{$printer.toner_color}'>Toner: <b>{$printer.toner|default:0.00}</b> <a class="links" href="graphs.php?id={$printer.printer_id}&amp;graph=levels&amp;type=toner">Graph</a></td>
                </tr>
                <tr>
                    <td class='{$printer.kit_a_color}'>Maint Kit A: <b>{$printer.kit_a|default:0.00}</b> <a class="links" href="graphs.php?id={$printer.printer_id}&amp;graph=levels&amp;type=kita">Graph</a></td>
                </tr>
                <tr>
                    <td class='{$printer.kit_b_color}'>Maint Kit B: <b>{$printer.kit_b|default:0.00}</b> <a class="links" href="graphs.php?id={$printer.printer_id}&amp;graph=levels&amp;type=kitb">Graph</a></td>
                </tr>
                <tr>
                    <th class='all'><a class="links" href="graphs.php?id={$printer.printer_id}&amp;graph=levels">Graph All Levels</a></th>
                </tr>
                </tbody>
            </table>
        </td>
{foreachelse}
        <tr>
            <td>
                There are no Printers for this campus yet...
            </td>
        </tr>
{/foreach}
    </tr>
    <tr>
        <td></td>
    </tr>
    <tr>
        <td></td>
    </tr>
    <tr>
        <td></td>
    </tr>
    <tr>
        <td></td>
    </tr>
{foreachelse}
    <tr>
        <td>
            No Campuses yet...
        </td>
    </tr>
{/foreach}
    </tbody>
</table>
</p>
</body>
</html>