<?php
/*
$a = snmp2_walk("wor_techctr_1", "public", "");

foreach ($a as $key=>$val) {
    echo $key." => ".$val."\r\n";
}
*/
print_r(snmp2_get("wor_techctr_1", "public", 'IF-MIB::interfaces.ifTables.ifEntry.ifAdminStatus'));