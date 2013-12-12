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
{foreach $campuses as $campus}
    <tr>
        <th colspan="10">{$campus.name}</th>
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
                    <td class='all'>Count: <b>{$printer.count|default:0}</b> <a class="links" href="graphs/{$campus.name}_{$printer.name}_count.png">Graph</a></td>
                </tr>
                <tr>
                    <td class='{$printer.tray1_color}'>Tray 1: <b>{$printer.tray_1|default:0}</b> <a class="links" href="graphs/{$campus.name}_{$printer.name}_tray_1.png">Graph</a></td>
                </tr>
                <tr>
                    <td class='{$printer.tray2_color}'>Tray 2: <b>{$printer.tray_2|default:0}</b> <a class="links" href="graphs/{$campus.name}_{$printer.name}_tray_2.png">Graph</a></td>
                </tr>
                <tr>
                    <td class='{$printer.tray3_color}'>Tray 3: <b>{$printer.tray_3|default:0}</b> <a class="links" href="graphs/{$campus.name}_{$printer.name}_tray_3.png">Graph</a></td>
                </tr>
                <tr>
                    <td class='{$printer.toner_color}'>Toner: <b>{$printer.toner|default:0.00}</b> <a class="links" href="graphs/{$campus.name}_{$printer.name}_toner.png">Graph</a></td>
                </tr>
                <tr>
                    <td class='{$printer.kit_a_color}'>Maint Kit A: <b>{$printer.kit_a|default:0.00}</b> <a class="links" href="graphs/{$campus.name}_{$printer.name}_kit_a.png">Graph</a></td>
                </tr>
                <tr>
                    <td class='{$printer.kit_b_color}'>Maint Kit B: <b>{$printer.kit_b|default:0.00}</b> <a class="links" href="graphs/{$campus.name}_{$printer.name}_kit_b.png">Graph</a></td>
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