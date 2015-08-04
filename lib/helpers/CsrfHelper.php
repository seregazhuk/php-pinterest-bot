<?php

namespace Pinterest\helpers;


class CsrfHelper
{
	/**
	 * Get a CSRF token from the given cookie file
	 */
	public static function getCsrfToken($file)
	{

		// Failsafe
		if( !file_exists($file) )
			return null;

		// Step through the file, line by line..
		foreach( file($file) as $line ) {

			$line = trim($line);

			// Skip blank and comment lines
			if( $line == "" or substr($line, 0, 2) == "# " )
				continue;

			list($domain, $tailmatch, $path, $secure, $expires, $name, $value) = explode("\t", $line);

			// Do we have our token?
			if( $name == "csrftoken" )
				return $value;

		}

		// Couldn't find it..
		return null;

	}
}