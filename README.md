printerStats
============

printerStats

Right now it is a very crude Printer Status Monitor, for the Ricoh Aficio SP 8200DN.
A very very crude HP LaserJet 4200 module has been made, just remember its not perfect.


Requirements:
1) MySQL or MariaDB 5.0+
2) PHP 5.4+ for the front end
3) Smarty 3+ will also need to configure the www config file to the path that smarty and the templates are in.
4) Python 2.7 for the daemon (packages used are: sys, pymysql, re, time, BeautifulSoup, urllib2, matplotlib.pyplot, collections, types )
5) Apache 2+

To use:
1) Create a MySQL Database called `printers` make sure it is UTF8_bin.
2) Import the printers.sql file into that database.
3) Create a MySQL user called Printers or what ever you want, give select/insert to this user on the printers db.

4) Copy the contents of the www folder to your HTTP root folder or where ever.
5) Copy the contents of the daemon folder some where that is not in the HTTP root.

6) Configure the config files in:
	a) /folder/you/put/daemon/config/config.ini
	b) /folder/you/put/www/printers/lib/config.php
7) Add the printers you want to watch in /folder/you/put/daemon/printers.ini
	a) Follow the file format carefully.

8) Run the daemon from /folder/you/put/daemon/getstatsd.py
9) View the results at http://printershost/printers/index.php

Graph data limiting is configurable in the daemon config.ini