<html>
<title>{$printer_name} {$label} Count</title>
<head>
    <script type="text/javascript" src="https://www.google.com/jsapi"></script>
    <script type="text/javascript">
        google.load("visualization", "1", {ldelim}packages:["corechart"] {rdelim});
        google.setOnLoadCallback(drawChart);
        function drawChart() {ldelim}

            var options = {ldelim}
                selectionMode: 'multiple',
                title: "{$printer_rows.0.name}",
                crosshair: {ldelim} trigger: 'both' {rdelim},
                chartArea: {ldelim} width: '100%', height: '70%'},
                legend: {ldelim} position: 'none'},
                titlePosition: 'none',
                hAxis: {ldelim} textPosition: 'bottom'{rdelim},
                vAxis: {ldelim} textPosition: 'in'{rdelim},
                explorer:
                {ldelim}
                    maxZoomIn: .005,
                    actions: ['dragToZoom', 'rightClickToReset'],
                    axis: 'vertical',
                    keepInBounds: false
                {rdelim}
            {rdelim};

            var data = google.visualization.arrayToDataTable(
            [
                ['Timestamp', '{$label}'],
                {foreach $printer_rows as $prow}
    ['{$prow.0}',  {$prow.1}],
                {/foreach}
]
            );

            var chart = new google.visualization.LineChart(document.getElementById('chart_div'));
            chart.draw(data, options);
            {rdelim}
    </script>
</head>
<body>
<a href="{$allURL}">Show All History</a>
<div id="chart_div" style="width: 100%; height: 100%;"></div>
</body>
</html>