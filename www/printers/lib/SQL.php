<?php
/*
SQL.inc.php
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

class SQL
{
    function __construct($config)
    {
        $this->host             = $config['host'];
        $this->service          = $config['srvc'];
        $this->db               = $config['db'];
        $dsn                    = $this->service.':host='.$this->host;
        if($this->service === "mysql")
        {
            $options = array(
                PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',
                PDO::ATTR_PERSISTENT => TRUE,
            );
        }
        else
        {
            $options = array(
                PDO::ATTR_PERSISTENT => TRUE,
            );
        }
        $this->conn = new PDO($dsn, $config['db_user'], $config['db_pwd'], $options);
        $this->conn->query("SET NAMES 'utf8'");
    }

    function checkError()
    {
        $err = $this->conn->errorCode();
        if($err === "00000")
        {
            return 0;
        }else
        {
            throw new ErrorException("There was an error running the SQL statement: ".var_export($this->conn->errorInfo() ,1));
        }
    }
}