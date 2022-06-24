<?php

namespace WC_ADDON_FOR_AF;
use FPDM;

class PDF extends BASE{

  function __construct(){
    require_once( 'fpdm/fpdm.php' );
  }

  function getValueFields( $fields, $values ){
    $value_fields = array();
    foreach( $fields as $slug => $repeat ){
      if( isset( $values[ $slug ] ) && $values[ $slug ] ){
        $value_fields[ $slug ] = strtoupper( $values[ $slug ] );
        foreach( $repeat as $i ){
          $value_fields[ $slug . $i ] = strtoupper( $values[ $slug ] );
        }
      }
    }
    return $value_fields;
  }

  // RETURNING THE FILE PATH WHICH EXISTS IN THE WP UPLOADS DIRECTORY
	function getFilePath( $file_slug ) {
		$file             = "$file_slug.pdf";
		$filePath         = array();
		$path             = wp_upload_dir();
		$filePath['path'] = $path['path'] . "/$file";
		$filePath['url']  = $path['url'] . "/$file";
		return $filePath;
	}


  function download( $type, $values, $download = false, $newfileslug = 'sample' ){
    $filepath = apply_filters( 'af_pdf_filepath_' . $type, '' );

    $fields = apply_filters( 'af_pdf_fields_' . $type, array() );
    if( count( $fields ) ){
      $value_fields = $this->getValueFields( $fields, $values );
    }
    else{
      $value_fields = $values;
    }


    //$this->test( $fields );
    //$this->test( $value_fields );

    $newfile = $this->getFilePath( $newfileslug );

    $pdf = new FPDM( $filepath );
    $pdf->useCheckboxParser = true;
    $pdf->Load( $value_fields, false );     // second parameter: false if field values are in ISO-8859-1, true if UTF-8
    $pdf->Merge();

    return $pdf->output();

    $pdf->Output( 'F', $newfile['path'] );

    return $newfile['url'];


    //print_r(  );

  }

}
