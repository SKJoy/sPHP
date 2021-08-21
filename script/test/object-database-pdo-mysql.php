<?php
namespace sPHP;

$Database = new Database\PDO\MySQL("bondstei_vts", "Vision2021");
//$Database = new MySQL("bondstei", "@Back911");
$Database->Connect(null, null, null, null, null, null, null, true); //var_dump($Database);

$Database->VarDump($Database->Query("
	SET @X := 'Y';
	SELECT T.* FROM (SELECT 'Test' AS Test) AS T WHERE FALSE;
	SELECT @X;
", null, true, true));

$Database->VarDump("Execution continues");
?>