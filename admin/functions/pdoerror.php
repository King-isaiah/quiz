<?php

function pdoerror($type)
	{
	global $Pdo;
	//global $query;
	if(is_object($type))
		{
		$err = $type->errorInfo();

		}
	else
		{
		$err= array(null,null,'Not an object');
		}
	$err = $err[2]	;
	return $err;

	}

?>
