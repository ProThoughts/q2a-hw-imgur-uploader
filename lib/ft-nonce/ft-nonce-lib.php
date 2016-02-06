<?php
/*
 * Name: FT-NONCE-LIB
 * Created By: Full Throttle Development, LLC (http://fullthrottledevelopment.com)
 * Created On: July 2009
 * Last Modified On: August 12, 2009
 * Last Modified By: Glenn Ansley (glenn@fullthrottledevelopment.com)
 * Version: 0.2
 */

/* 
Copyright 2009 Full Throttle Development, LLC

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/
		
define( 'FT_NONCE_UNIQUE_KEY' , 'hisoyw93hdgdfgw89jvhj7857842342384635s' );
define( 'FT_NONCE_DURATION' , 300 ); // 300 makes link or form good for 5 minutes from time of generation
define( 'FT_NONCE_KEY' , '_nonce' );

// This method creates a key / value pair for a url string
function ft_nonce_create_query_string( $action = '' , $user = '' ){
	return FT_NONCE_KEY."=".ft_nonce_create( $action , $user );
}

// This method creates an nonce for a form field
function ft_nonce_create_form_input( $action = '' , $user='' ){
	echo "<input type='hidden' name='".FT_NONCE_KEY."' value='".ft_nonce_create( $action . $user )."' />";
}

// This method creates an nonce. It should be called by one of the previous two functions.
function ft_nonce_create( $action = '' , $user='' ){
	return substr( ft_nonce_generate_hash( $action . $user ), -12, 10);
}

// This method validates an nonce
function ft_nonce_is_valid( $nonce , $action = '' , $user='' ){
	// Nonce generated 0-12 hours ago
	if ( substr(ft_nonce_generate_hash( $action . $user ), -12, 10) == $nonce ){
		return true;
	}
	return false;
}

// This method generates the nonce timestamp
function ft_nonce_generate_hash( $action='' , $user='' ){
	$i = ceil( time() / ( FT_NONCE_DURATION / 2 ) );
	return md5( $i . $action . $user . $action );
}

if ( FT_NONCE_UNIQUE_KEY == '' ){ die( 'You must enter a unique key on line 2 of ft_nonce_lib.php to use this library.'); }
/**
 * I modified the library into the following functions.
 * What I did was check for the current interval and the previous interval by using nonce_create(time()-NONCE_DURATION)==$nonce.
 * Are there ways to further improve the functions?:
 */
/**
 * @param bool $time
 * @return string
 */
function nonce_create($time = false){
    if(!$time)
        $time=time();
    $i=ceil($time/(FT_NONCE_DURATION));
    return substr(md5($i.FT_NONCE_UNIQUE_KEY),-12,10);
}

/**
 * @param $nonce
 * @return bool
 */
function nonce_is_valid($nonce){
    if (nonce_create() == $nonce || nonce_create(time()-FT_NONCE_DURATION) == $nonce)
        return true;
    return false;
}
?>