<?php
namespace Pagup\MassPingTool\Core;

class Request
{
    public static function post($key, $safe)
    {

        if ( isset( $_POST[$key] ) && in_array( $_POST[$key], $safe ) ) 
        { 
            $request = sanitize_text_field( $_POST[$key] ); 
        }
        
        return $request ?? '';
    }

    public static function check($key)
    {

        return isset( $_POST[$key] ) && !empty( $_POST[$key] ); 
        
    }

    public static function text($key)
    {

        return static::check($key) ? sanitize_text_field( $_POST[$key] ) : '';
        
    }

    public static function numeric($key)
    {
        if ( isset( $_POST[$key] ) && is_numeric( $_POST[$key] ) ) {

            return sanitize_text_field( $_POST[$key] );

        }
        
        else {

            return null;

        }
    }

    public static function array( $array ) {
        foreach ( (array) $array as $k => $v ) {
           if ( is_array( $v ) ) {
               $array[$k] =  array( $v );
           } else {
               $array[$k] = sanitize_text_field( $v );
           }
        }
     
       return $array;                                                       
     }
}