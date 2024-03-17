<?php
//Static label + Date. Data entry screen.
function abcfsl_mbox_item_date_SLDTE( $tplateOptns, $itemOptns, $F, $showField ){

    if($showField == 0) { return ''; }

    $staticTxt = isset( $tplateOptns['_lblTxt_' . $F] ) ? esc_attr( $tplateOptns['_lblTxt_' . $F][0] ) : '';
    $linkLblLbl = isset( $tplateOptns['_inputLbl_' . $F] ) ? esc_attr( $tplateOptns['_inputLbl_' . $F][0] ) : '';
    $linkLblHlp = isset( $tplateOptns['_inputHlp_' . $F] ) ? esc_attr( $tplateOptns['_inputHlp_' . $F][0] ) : '';
    $dteDisplayLbl = isset( $tplateOptns['_dteDisplayLbl_' . $F] ) ? esc_attr( $tplateOptns['_dteDisplayLbl_' . $F][0] ) : '';
    $dteDisplayHlp = isset( $tplateOptns['_dteDisplayHlp_' . $F] ) ? esc_attr( $tplateOptns['_dteDisplayHlp_' . $F][0] ) : '';
    $dtFormat = isset( $tplateOptns['_dtFormat_' . $F] ) ? $tplateOptns['_dtFormat_' . $F][0] : '';

    $dteYMD = isset( $itemOptns['_dteYMD_' . $F] ) ?  $itemOptns['_dteYMD_' . $F][0] : '';
    //$dteOut = abcfsl_mbox_item_date_format_for_ouput( $dteYMD, $dtFormat );
    $dteOut = abcfsl_cnt_date_formated( $dteYMD, $dtFormat );

    $staticTxtFieldLbl = abcfsl_mbox_item_text_line_number( $F , '' );
    $linkLblLbl = abcfsl_mbox_item_text_line_number( $F , $linkLblLbl );
    $dteDisplayLbl = abcfsl_mbox_item_text_line_number( $F , $dteDisplayLbl );

    //----------------------------------------------------
    $flexCntrS = abcfl_html_tag( 'div', '', 'abcflFGCntr' );
    $flex3ColS = abcfl_html_tag( 'div', '', 'abcflFG3Col' );
    $divE = abcfl_html_tag_end( 'div');     

    $staticLblRO = abcfl_input_txt_readonly('ro_lblTxt_' . $F, '', $staticTxt, $staticTxtFieldLbl, '', '100%', '', '', 'abcflFldCntr', 'abcflFldLbl');
    $dteOutRO = abcfl_input_txt_readonly('ro_dteOut_' . $F, '', $dteOut, $dteDisplayLbl, $dteDisplayHlp, '100%', '', '', 'abcflFldCntr', 'abcflFldLbl');
    
    //-- READ ONLY ---------
    if( $showField == 2 ) {
        $dteYMDRO = abcfl_input_txt_readonly('ro_dteYMD_' . $F, '', $dteOut, $linkLblLbl, '', '100%', '', '', 'abcflFldCntr', 'abcflFldLbl');
        echo $flexCntrS . $flex3ColS . $staticLblRO . $divE . $flex3ColS . $dteYMD . $divE . $flex3ColS . $dteOutRO . abcfl_html_tag_ends( 'div,div');
        return ;        
     }

     $dteYMDF = abcfl_input_date('dteYMD_' . $F, '', $dteYMD, $linkLblLbl, $linkLblHlp, '100%', '', '', 'abcflFldCntr', 'abcflFldLbl');
     echo $flexCntrS . $flex3ColS . $staticLblRO . $divE . $flex3ColS . $dteYMDF . $divE . $flex3ColS . $dteOutRO . abcfl_html_tag_ends( 'div,div');
}

function abcfsl_mbox_item_date_format_for_ouput( $dteYMD, $dtFormat ){

    
    $out = abcfsl_cnt_date_formated( $dteYMD, $dtFormat );

    return $out;

    $out = $dteYMD;
    if( empty( $dtFormat ) ) { return $out; }

    switch ( $dtFormat ) {
        case 'D/M/Y':
            $out = implode('/', array_reverse(explode('-', $dteYMD)));
            break;
        case 'D.M.Y':
            $out = implode('.', array_reverse(explode('-', $dteYMD)));
            break;
        case 'D-M-Y':
            $out = implode('-', array_reverse(explode('-', $dteYMD)));
            break;             
        case 'M/D/Y':
            $out  = date('m/d/Y', strtotime( $dteYMD ) );
            break; 
        case 'M.D.Y':
            $out  = date('m.d.Y', strtotime( $dteYMD ) );
            break;  
        case 'M-D-Y':
            $out  = date('m-d-Y', strtotime( $dteYMD ) );
            break;  
        case 'Y/M/D':
            $out = implode( '/', explode( '-', $dteYMD ) );
            break;           
        case 'Y.M.D':
            $out = implode( '.', explode( '-', $dteYMD ) );
            break;          
        default:
            break;
    }

    return $out;
}


