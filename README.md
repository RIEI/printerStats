printerStats
============

printerStats

Right now it is a very crude Printer Status Monitor, for the Ricoh Aficio SP 8200DN. 
If I ever get around to adding more printers, I will, but these are all the ones that I have at the place I work.

To use:
Create a MySQL Database call `printers` make sure it is UTF8_bin.
Import the Printers.sql file into that database.

Copy the contents of the www folder to your HTTP root folder or where ever.
Copy the contents of the daemon folder some where that is not in the HTTP root.

Configure the config files in /folder/you/put/daemon/config.php and www/lib/config.php
Add the printers you want to watch in /folder/you/put/daemon/printers_list.php

Run the daemon from /folder/you/put/daemon/get_stats.php
View the results at http://printershost/index.php
View graphs of history at http://printershost/graph.php

Graphs are limited to the last 100 history points.
