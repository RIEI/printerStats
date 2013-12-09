printerStats
============

printerStats

Right now it is a very crude Printer Status Monitor, for the Ricoh Aficio SP 8200DN. 
If I ever get around to adding more printers, I will, but these are all the ones that I have at the place I work.

A very very crude HP LaserJet 4200 module has been made, just remember its not perfect.

Requirements:
MySQL or MariaDB 5.0+
PHP 5.4+ for the front end
Python 2.7 for the daemon (packages used are: sys, pymysql, re, time, BeautifulSoup, urllib2, matplotlib.pyplot, gnumpy, collections, types
Apache 2+

To use:
Create a MySQL Database called `printers` make sure it is UTF8_bin.
Import the printers.sql file into that database.

Copy the contents of the www folder to your HTTP root folder or where ever.
Copy the contents of the daemon folder some where that is not in the HTTP root.

Configure the config files in /folder/you/put/daemon/config/config.ini and www/lib/config.php
Add the printers you want to watch in /folder/you/put/daemon/printers.ini

Run the daemon from /folder/you/put/daemon/getstatsd.py
View the results at http://printershost/index.php

Graphs are limited to the last 1000 history points.
