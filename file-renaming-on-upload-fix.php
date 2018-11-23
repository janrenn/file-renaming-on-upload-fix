<?php
/*
    Plugin Name: File Renaming on upload CF FIX
    Plugin URI: http://janrenner.cz/
    Description: Transliterates to Latin. Truncates file name length to 70.
    Version: 0.1
    Author: Jan Renner
    Author URI: http://janrenner.cz/
    Date: 27.11.2017 10:17:27
*/
/*  Copyright 2017  Jan Renner  (email: jan.renner@gmail.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

//remove sanitize_file_name specials
//======================================
add_filter( 'sanitize_file_name_chars', function ( $special_chars ) {
    return array( chr(0) );
}, 8 );

//fix sanitize_file_name using own specials etc.
//================================================
add_filter( 'sanitize_file_name', function ( $filename ) {

    //special characters
    //-----------------------
    $special_chars = explode( ' ', "? + [ ] / \\ = < > : ; , ' \" & $ # * ( ) | ~ ` ! { } ¨ % @ ^ – — × ’" );
    $filename = str_replace( $special_chars, '-', $filename );
    $filename = preg_replace( '~\-+~', '-', $filename );

    //transliteration
    //------------------
    if ( function_exists('transliterator_transliterate') ) $filename = transliterator_transliterate( 'Any-Latin; Latin-ASCII; Lower()', $filename );

    //truncation
    //-------------
    $ext = end( explode( '.', $filename ) );
    $filename = substr( $filename, 0, -(strlen($ext) + 1) );
    $filename = str_replace( '.', '-', $filename );
    $filename_shortened = substr( $filename, 0, 70 );
    if ( $filename_shortened != $filename ) {
        $filename = preg_replace( '~(-|_)[^\-_]{0,' . floor( strlen($filename) / 2 ) . '}$~', '', $filename_shortened );
    }
    $filename = trim( $filename, '-_' );
    $filename = $filename . '.' . $ext;

    //--------------------
    //var_dump($filename);die;
    return $filename;

}, 8 );//priority must be <9 to run before File Renaming plugin


