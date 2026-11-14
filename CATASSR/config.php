<?php
if (is_file(__DIR__.'/config.local.php')) return require __DIR__.'/config.local.php';
// XAMPP local. Cambiar estos valores si su MySQL usa otra configuración.
return ['host'=>'127.0.0.1','port'=>3306,'database'=>'catassr','user'=>'root','password'=>''];
