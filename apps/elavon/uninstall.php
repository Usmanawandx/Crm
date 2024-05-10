<?php

$d = ORM::for_table('sys_pg')->where('processor','elavon')->find_one();

if($d){

   $d->delete();

}