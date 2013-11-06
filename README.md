printerStats
============

printerStats

Right now it is a very crude Printer Status Monitor, for the Ricoh Aficio SP 8200DN. 
If I ever get around to adding more printers, I will, but these are all the ones that I have at the place I work.

Requirements:
MySQL or MariaDB 5.0+
PHP 5.4+
GD2 Extension (for the graphing)
Apache 2

To use:
Create a MySQL Database called `printers` make sure it is UTF8_bin.
Import the Printers.sql file into that database.

Copy the contents of the www folder to your HTTP root folder or where ever.
Copy the contents of the daemon folder some where that is not in the HTTP root.

Configure the config files in /folder/you/put/daemon/config/config.php and www/lib/config.php
Add the printers you want to watch in /folder/you/put/daemon/config/printers_list.php

Run the daemon from /folder/you/put/daemon/get_stats.php
View the results at http://printershost/index.php
View graphs of history at http://printershost/graph.php

Graphs are limited to the last 1000 history points.
