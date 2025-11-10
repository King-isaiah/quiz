<?php
declare(strict_types=1);

namespace app\controller;

class Snippet
{

	public static function ajax ()
	{
		return (isset($_SERVER['HTTP_X_REQUESTED_WITH']) 
			&& 
				($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest'));
	}

	public static function output ($data)
	{
		return is_string($data) ? $data : json_encode($data);
	}

	public static function preview ($data = 'Too few arguments given, exactly 1 expected') {
		if (is_object($data) || is_array($data))
			echo "<pre>", print_r($data, true), "</pre>";
		else
			echo $data;
	}

	public static function trimCode ( string $code )
	{
		$split = explode('-', $code);

		return "{$split[0]}-{$split[1]}-{$split[2]}-{$split[3]}-{$split[4]}";
	}

	public static function decrementLetter ( string $letter )
	{
		if ( empty($letter) )
			throw new \Exception('Empty string cannot be accepted by method.');

		if ( strlen($letter) > 1 )
			throw new \Exception('Character too long, expecting a single letter.');

		if ( strtoupper($letter) === 'A' )
			throw new \Exception('The provided character cannot be decrement.');

		$arr = [ 1 => 'A', 2 => 'B', 3 => 'C', 4 => 'D', 5 => 'E', 6 => 'F', 7 => 'G', 8 => 'H', 9 => 'I', 10 => 'J', 11 => 'K', 12 => 'L', 13 => 'M', 14 => 'N', 15 => 'O', 16 => 'P', 17 => 'Q', 18 => 'R', 19 => 'S', 20 => 'T', 21 => 'U', 22 => 'V', 23 => 'W', 24 => 'X', 25 => 'Y', 26 => 'Z' ];
		$flipArr = array_flip($arr);
		$char = strtoupper($letter);
		$number = $flipArr[$char];
		$number--;

		return $arr[$number];
	}

	public static function Dates ( string $date )
	{
		$timestamp = strtotime($date);
		return [
			'date'		=>	date("Y/m/d", $timestamp),
			'shortday'	=>	date("D", $timestamp),
			'longday'	=>	date("l", $timestamp),
			'digitday'	=>	date("d", $timestamp)
		];
	}

	public static function seenDates ( string $date, int $includePreviousMonthDays = 0 )
	{
		$split = explode('/', $date);
		$time = strtotime("$split[0]/$split[1]/01");
		
		while ( $includePreviousMonthDays > 0 ) {
			$res[] = date("Y/m/d", $time - ($includePreviousMonthDays * 86400));
			$includePreviousMonthDays--;
		}

		$timestamp = strtotime($date);
		$cur_day = (int) $split[2];
		for ( $i = 0; $i < $cur_day; $i++ ) {
			$res[] = date("Y/m/d", $timestamp - ($i * 86400));
		}
		return $res;
	}

	public static function monthDates (int $minDay = 1)
	{
		if ($minDay < 1) {
			throw new \InvalidArgumentException('Invalid day provided');
		}

		$parts = explode('/', date("Y/m/d"));
		$cur_day = (int) $parts[2];
		$res = [];

		for ( $i = $minDay; $i <= $cur_day; $i++ ) {
			$res[] = date("Y/m/d", mktime(0, 0, 0, (int) $parts[1], $i, (int) $parts[0]));
		}
		return $res;
	}

	public static function previousDate ( string $date )
	{
		return date("Y/m/d", strtotime($date) - 86400);
	}

	public static function scriptDuration ()
	{
		return microtime(true) - $_SERVER["REQUEST_TIME_FLOAT"];
	}

	public static function empty2zero ($arg)
	{
		return empty($arg) ? 0 : $arg;
	}

	public static function pusher ( $input, $appendToClause, $clause = '' )
	{
		if ( ! empty($input) && $input !== 'all' ) {
			$clause .= ! empty($clause) ? "{$appendToClause}" : $appendToClause;
			
			return $clause;
		}

		return $clause;
	}

    /**
     * array_switch — Creates an array by using one column for keys and another for its values
     */
    public static function array_switch ( ?array $input, $k, $v ): array
    {
    	if ( is_array($input) && ! empty($input) ) {
	    	foreach ( $input as $result ) {
	    		$data[$result[$k]] = $result[$v];
	    	}
	    	
	    	return $data;    		
    	}

    	return [];
    }

	public static function generateString (int $length = 6, $key = ''): string
	{
	    $charset = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";

	    for($i=0; $i<$length; $i++) 
	        $key .= $charset[(mt_rand(0,(strlen($charset)-1)))];

	    return $key;
	} 

	public static function generateRef (string $tablename, $column, object $conn, string $prepend = '', int $len = 6): string
	{
		do {
			$ref = strtoupper($prepend . self::generateString($len));
		} while ($conn->fetchOne(" SELECT `$column` FROM `$tablename` WHERE `$column` = '$ref' "));

		return $ref;
	}

	public static function prefixZero ($id): string
	{	
		switch (strlen((string) $id)) {
			case 1:
				$id = "0000{$id}";
				break;

			case 2:
				$id = "000{$id}";
				break;

			case 3:
				$id = "00{$id}";
				break;

			case 4:
				$id = "0{$id}";
				break;	

			default:
				$id = $id;
				break;
		}

		return $id;
	}

	public static function sum ($data, string $field, float $result = 0): float
	{
		foreach ($data as $res) {
			$res = (array) $res;
			$result += (float) $res[$field];
		}

		return $result;
	} 

	public static function get_string_between ( string $str, string $start, string $end ): string
	{
		$str = ' ' . $str;
		$ini = strpos($str, $start);

		if ($ini == 0) return '';

		$ini += strlen($start);
		$len = strpos($str, $end, $ini) - $ini;

		return substr($str, $ini, $len);
	}

	public static function ends_with ( string $str, string $lastStr ): bool
	{
		$count = strlen($lastStr);
		if ($count === 0) {
			return true;
		}
		
		return (substr($str, -$count) === $lastStr);
	}

	public static function file ( $error = null, $message = false, $dir = null )
	{
		if ( is_null($dir) && in_array(PHP_SAPI, ['cli', 'phpdbg', 'embed'], true) ) {
			$dir = __DIR__ . '/../../../log';
		}

		if ( ! is_null($error) ) {
			$folder = ( is_null($dir) ) ? $_SERVER['DOCUMENT_ROOT']."/bil/log" : $dir;

			if ( file_put_contents( $folder . '/error.log', $error."\n\n", FILE_APPEND ) ) {
				// $error->getMessage() // Message from error object
				echo $message ? Snippet::output($message) : 'Oops, something went wrong. Check error log for fix';
			} else {
				echo Snippet::output('Error log failed.');
			}
		} else {
			echo Snippet::output('Too few arguments given, at least 1 expected<br />');
		}
		exit();
	}
}