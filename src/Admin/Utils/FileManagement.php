<?php

namespace App\Admin\Utils;

class FileManagement {
	
	/**
   * Busca os modelos disponiveis.
   */
	public static function getModelFiles()
	{
		
		$files = scandir(__DIR__ . '/../../Models');
		
		if ( is_array( $files ) ){
			
			foreach( $files as $key => $file ) {
				
				if ( strpos($file, 'php') > 0 ){
					
					$files[ $key ] = explode(".", $file)[0];
					
				}
				else {
					
					unset( $files[ $key ] );
					
				}
				
			}
			
		}
		
		return array_filter( $files, function( $file ) {
			
			if ( $file != 'Model' ) {
				
				return true;
				
			}
			else{
				
				return false;
				
			}
			
		}
		);
		
	}
	
}
