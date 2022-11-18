<?php

if( !function_exists('gSetting') ) {
    function gSetting($column) {
        $info = \DB::table('global_settings')->first();
        if( isset( $info ) && !empty( $info ) ) {
            return $info->$column ?? '';
        } else {
            return false;
        }
    }
}

if( !function_exists('errorArrays') ) {
    function errorArrays($errors) {
        return array_map( function($err) {
            return '<div>'.str_replace(',', '', $err).'</div>';
        }, $errors);
    }
}