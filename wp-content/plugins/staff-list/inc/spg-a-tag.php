<?php
//Before print delete.
//== LINKS START =========================================
//Fix in free version abcfsl_util_href_bldr ?????????????????????
//Get href parts: url + link text + target.

//Not used for links to single page anymore. Standard hyperlink fields only. Handles NT prefix.
//Hyperlinks don't have new tab options yet.
function abcfsl_spg_a_tag_parts( $itemOptns, $staffID, $sPageUrl, $F ){

    $aTagParts['hrefUrl'] = '';
    $aTagParts['hrefTxt'] = '';
    $aTagParts['target'] = '';

    //echo abcfl_input_checkbox('lnkNT_' . $F,  '', $lnkNT, abcfsl_txta(143), '', '', '', 'abcflFldCntr', '', '', '' );

    //Takes all field types. Returns empty if no URL
    $itemUrl = isset( $itemOptns['_url_' . $F] ) ? esc_attr( $itemOptns['_url_' . $F][0] ) : '';
    if( abcfl_html_isblank( $itemUrl ) ) { return $aTagParts; }

    // Splits into URL and target if NT prefix
    $splitUrl = abcfsl_util_get_url_and_target( $itemUrl ); 

    $itemTxt = isset( $itemOptns['_urlTxt_' . $F] ) ? esc_attr( $itemOptns['_urlTxt_' . $F][0] ) : '';
    if( abcfl_html_isblank( $itemTxt ) ) { $itemTxt = $splitUrl['hrefUrl']; }

    $aTagParts['hrefTxt'] = $itemTxt;
    $aTagParts['hrefUrl'] = $splitUrl['hrefUrl'];    
    $aTagParts['target'] = $splitUrl['target'];

    return $aTagParts;
}

//=== SPTL SINGLE PAGE LINK START ====================================================
function abcfsl_spg_a_tag_lnk_parts( $parLP, $itemOptns, $isImgLink ){

    $lnkParts['imgID'] = 0;
    $lnkParts['href'] = '';
    $lnkParts['target'] = '';
    $lnkParts['onclick'] = '';
    $lnkParts['args'] = '';

    //== If no hyperlink - exit =========================================
    $parLP = abcfsl_spg_a_tag_get_lnk_parts( $parLP, $itemOptns, $isImgLink );
    if( !$parLP['showLnk'] ) { return $lnkParts; }
    //=========================================================================

    // Hybrid = ST, SPGHYB. Custom = SPGCUST, SPCUST.
    switch ( $parLP['sPgLnkShow'] ) {
        case 'ST':
        case 'SPHYB':     
            $lnkParts = abcfsl_spg_a_tag_lnk_parts_hybrid( $parLP, $itemOptns, $isImgLink );
            break; 
        case 'SPGCUST':
        case 'SPCUST':    
            $lnkParts = abcfsl_spg_a_tag_lnk_parts_custom( $parLP, $itemOptns, $isImgLink );
            break;                       
        default:
            $lnkParts = abcfsl_spg_a_tag_lnk_parts_ugly_pretty( $parLP, $itemOptns, $isImgLink );
            break;
    }
    return $lnkParts;
}  

function abcfsl_spg_a_tag_get_lnk_parts( $parLP, $itemOptns, $isImgLink ){

    // Single Page Options (template). Link parts.
    // $parLP['staffID']
    // $parLP['sPageUrl']
    // $parLP['sPgLnkShow'] Show Link N, Y, ...
    // $parLP['sPgLnkNT'] Open in a new tab or window.
    // $parLP['lineTxt'] Text of text link.
    // $parLP['imgLnkLDefault'] Add link to staff image (image hyperlink).
    $parLP['showLnk'] = false; 

    //== No hyperlink - exit=====================================================
    //--- No link to single page (Show Link cbo) -----------------------------
    if(  $parLP['sPgLnkShow'] == 'N' ) { return $parLP; }

    //--- No link to single page (Staff member option) -----------------------
    $hideSPgLnk = isset( $itemOptns['_hideSPgLnk'] ) ? $itemOptns['_hideSPgLnk'][0] : '0';
    if( $hideSPgLnk == 1 ) { return  $parLP; }

    if( $isImgLink ){
        // Template option. Add link to staff image (image hyperlink).
        if( $parLP['imgLnkLDefault'] != 1 ) { return $parLP; }
    }
    else {
        //-- Link Text can't be blank: sPgLnkTxt --------------------------
        if( abcfl_html_isblank( $parLP['lineTxt'] ) ) {  return $parLP;  }
    }

    if(  $parLP['sPgLnkShow'] == 'Y' ) {
        if( abcfl_html_isblank( $parLP['sPageUrl'] ) ) { return $parLP; }   
    }
    //=========================================================================
    $parLP['showLnk'] = true; 
    $parLP['custURL'] = '';
    $parLP['imgLnkL'] = isset( $itemOptns['_imgLnkL'] ) ? esc_attr( $itemOptns['_imgLnkL'][0] ) : '';
    $parLP['target'] = ''; 
    
    $parLP = abcfsl_spg_a_tag_img_custom_url_validator( $parLP );
    //echo"<pre>", print_r( $parLP, true ), "</pre>"; 
    //-------------------------------------------------------------
    return $parLP;

    // [staffID] => 8854
    // [sPageUrl] => 
    // [sPgLnkShow] => SPHYB
    // [sPgLnkNT] => 1
    // [lineTxt] => Single Page
    // [imgLnkLDefault] => 1
    // [showLnk] => 1
    // [target] => _blank
    // [custURL] => https://Custom_URL
    // [imgLnkL] => https://Custom_URL
} 

// Check content of staff member field: imgLnkL (Custom URL)
function abcfsl_spg_a_tag_img_custom_url_validator( $parLP ){

    // imgLnkL = Custom URL = Staff member,  can be: Empty, SP, NT, NT SP, Full URL, NT Full URL. Custom URL overwrites template options    
    $imgLnkL = $parLP['imgLnkL'];
    if( $parLP['sPgLnkNT'] == 1 ) { $parLP['target'] = '_blank'; } 

    // Page type
    $showLink = $parLP['sPgLnkShow'];
    if( $showLink == 'ST') { $showLink = 'SPHYB'; }
    if( $showLink == 'SPGCUST') { $showLink = 'SPCUST'; }
    //---------------------------------------------------------------
    if( $showLink == 'Y' || $showLink == 'SPHYB') { 

        if( empty( $imgLnkL ) ) { return $parLP; } 
        if( $imgLnkL == 'SP' ) { return $parLP; }

        if( $imgLnkL == 'NT' ) { 
            $parLP['target'] = '_blank';
            return $parLP;
        }

        if( $imgLnkL == 'NT SP' ) { 
            $parLP['target'] = '_blank';
            return $parLP;
        }  

        $prefixNT = substr( $imgLnkL, 0, 3 );
        if( $prefixNT == 'NT ' ) { 
            $parLP['target'] = '_blank';
            $parLP['custURL'] = substr( $imgLnkL, 3 );
            return $parLP;
        }

        $parLP['sPageUrl'] = '';
        $parLP['custURL'] = $imgLnkL;
        return $parLP;
    } 

    if( $showLink == 'SPCUST' ) { 

        // Custom URL required.
        if( empty( $imgLnkL ) ) { return $parLP; } 
        if( $imgLnkL == 'SP' ) { return $parLP; }
        if( $imgLnkL == 'NT' ) { return $parLP; }
        if( $imgLnkL == 'NT SP' ) { return $parLP; }  

        $prefixNT = substr( $imgLnkL, 0, 3 );
        if( $prefixNT == 'NT ' ) { 
            $parLP['target'] = '_blank';
            $parLP['custURL'] = substr( $imgLnkL, 3 );
            return $parLP;
        }
        
        $parLP['custURL'] = $imgLnkL;
        return $parLP;
    } 
    return $parLP;
}

//=== SPTL SINGLE PAGE LINK END  ==========================================================

function abcfsl_spg_a_tag_lnk_parts_ugly_pretty( $parLP, $itemOptns, $isImgLink ){

    if( !empty( $parLP['custURL'] ) ) { 
        return abcfsl_spg_a_tag_img_lnk_parts_builder( $parLP['custURL'],  $parLP['target'], $itemOptns, $isImgLink  );  
    }

    $pretty = isset( $itemOptns['_pretty'] ) ? esc_attr( $itemOptns['_pretty'][0] ) : '';    
    $sPageUrl = abcfsl_spg_a_tag_url_ugly_pretty( $parLP['staffID'], $parLP['sPageUrl'], $pretty );

    return abcfsl_spg_a_tag_img_lnk_parts_builder( $sPageUrl,  $parLP['target'], $itemOptns, $isImgLink );    
}

// Hyperlink to hybrid page. 
function abcfsl_spg_a_tag_lnk_parts_hybrid( $parLP, $itemOptns, $isImgLink ){

    if( !empty( $parLP['custURL'] ) ) { 
        return abcfsl_spg_a_tag_img_lnk_parts_builder( $parLP['custURL'],  $parLP['target'], $itemOptns, $isImgLink  );  
    }
    
    // Hybrid page has to have custom URL or single page URL.
    if( empty( $parLP['sPageUrl'] ) ) { 
        return abcfsl_spg_a_tag_img_lnk_parts_builder( $parLP['sPageUrl'],  $parLP['target'], $itemOptns, $isImgLink  );
    }

    $pretty = isset( $itemOptns['_pretty'] ) ? esc_attr( $itemOptns['_pretty'][0] ) : '';
    $sPageUrl = '';
    
    if( empty( $pretty ) ) {  
        $sPageUrl = abcfl_html_url( array( 'smid' => $parLP['staffID'] ), $parLP['sPageUrl'] ); 
    }
    else{
        $sPageUrl = trailingslashit( trailingslashit( $parLP['sPageUrl'] ) . $pretty );  
    }
    
    return abcfsl_spg_a_tag_img_lnk_parts_builder( $sPageUrl,  $parLP['target'], $itemOptns, $isImgLink  ); 
}

function abcfsl_spg_a_tag_lnk_parts_custom( $parLP, $itemOptns, $isImgLink ){

    return abcfsl_spg_a_tag_img_lnk_parts_builder( $parLP['custURL'],  $parLP['target'], $itemOptns, $isImgLink  );    
}

function abcfsl_spg_a_tag_img_lnk_parts_builder( $hrefUrl, $target, $itemOptns, $isImgLink ){  

    //if( $isImgLink ){ $lnkParts['imgID'] = abcfsl_spg_a_tag_img_lnk_id( isset( $itemOptns['_imgID'] ) ? esc_attr( $itemOptns['_imgID'][0] ) : 0 ); }

    $lnkParts['href'] = $hrefUrl;
    $lnkParts['target'] = $target;
    $lnkParts['onclick'] = abcfsl_spg_a_tag_lnk_onclick( isset( $itemOptns['_imgLnkClick'] ) ? esc_attr( $itemOptns['_imgLnkClick'][0] ) : '' );
    $lnkParts['args'] = abcfsl_spg_a_tag_lnk_args(isset( $itemOptns['_imgLnkArgs'] ) ? esc_attr( $itemOptns['_imgLnkArgs'][0] ) : '');
  
    return $lnkParts;
}

//Pretty or smid.
function abcfsl_spg_a_tag_url_ugly_pretty( $staffID, $sPageUrl, $pretty ){

    if( abcfsl_spg_a_tag_is_single_pretty( $sPageUrl, $pretty ) ) { 
        return trailingslashit( trailingslashit( $sPageUrl ) . $pretty ); 
    } 
    return abcfl_html_url( array('smid' => $staffID), $sPageUrl );
}

//=== SPTL IMAGE LINK END ==========================================================

function abcfsl_spg_a_tag_lnk_onclick( $imgLnkClick ){
    //Check mix of double and single quotes. Return empty if true; ???
    return $imgLnkClick;
}

function abcfsl_spg_a_tag_lnk_args( $lnkArgs ){
    //Convert HTML entities to characters. Double quotes only;
    if(!empty($lnkArgs)){ $lnkArgs = html_entity_decode($lnkArgs, ENT_COMPAT); }
    return $lnkArgs;
}

//== PRETTY PERMALINKS START ===========================
// NOT LOCAL  Used by Staff Search
// TRUE if single page URL is ready for pretty permalink.
function abcfsl_spg_a_tag_is_single_pretty( $sPageUrl, $pretty ){

    if( empty( $pretty ) ) { return false; }
    if( strlen( $sPageUrl ) < 10 ) { return false; }

    $sPageUrl = rtrim( $sPageUrl, '/' );

    if( substr($sPageUrl, -3) == 'bio' ) { return true; }
    if( substr($sPageUrl, -7) == 'profile' ) { return true; }
    if( substr($sPageUrl, -6) == 'profil' ) { return true; }
    if( substr($sPageUrl, -7) == 'profilo' ) { return true; }
    if( substr($sPageUrl, -6) == 'perfil' ) { return true; }
    
    //Custom permalinks plugin.
    $out = false;
    if( function_exists( 'abcfslcp_is_single_pretty' )){
       $out = abcfslcp_is_single_pretty( $sPageUrl );
    }

    return $out;
}

//Return StaffID for single page. By staff ID or pretty. Called from  abcfsl_cnt_spage
function abcfsl_spg_a_tag_staff_member_id ( $scodeArgs ){
    
    $staffID = 0;

    // Hybrid page parameter
    $staffID = (int)$scodeArgs['staff-id'];
    if( $staffID > 0 ){ return $staffID; }
    //------------------------------------
    $staffID = (int)$scodeArgs['smid'];
    if( $staffID > 0) { return $staffID; }
    //------------------------------------
    $tplateID = $scodeArgs['id'];
    //------------------------------------
    // Hybrid page parameter
    $staffNameSP = $scodeArgs['staff-name-sp'];
    if( !empty( $staffNameSP ) ){
        $staffID = abcfsl_db_staff_id_by_tplate_and_pretty( $tplateID, $staffNameSP );
        if( $staffID > 0) { return $staffID; }
    }
    //------------------------------------
    // Pretty permalink rewrite
    $staffName = $scodeArgs['staff-name'];    
    if( empty( $staffName ) ) { return 0; }
    //------------------------------------
    if ( substr( $staffName, 0, 6 ) == '?smid=' ){ 
        return (int) substr( $staffName, 6 ); 
    }
    $staffID = abcfsl_db_post_id_by_pretty( $tplateID, $staffName );

    // if( strlen( $staffName ) >= 6 ){
    //     if ( substr( $staffName, 0, 6 ) == '?smid=' ){ return (int) substr( $staffName, 6 ); }
    //     $staffID = abcfsl_db_post_id_by_pretty( $tplateID, $staffName );
    // }
    // if( !empty( $staffName ) & strlen( $staffName ) > 6 ){
    //     if ( substr($staffName, 0, 6) == '?smid=' ){ return (int) substr( $staffName, 6 ); }
    //     $staffID = abcfsl_db_post_id_by_pretty( $tplateID, $staffName );
    // }
    return $staffID;
}
//== PRETTY PERMALINKS END ===============================

//Used by struct-data. Check and modify !!!!!!!!!
function abcfsl_spg_a_tag_url_selector_legacy( $staffID, $lnkUrl, $sPageUrl, $pretty ){

    $out['hrefUrl'] = '';
    $out['target'] = '';
    $out['isSP'] = false;
    if( abcfl_html_isblank( $lnkUrl ) ) { return $out;}

    if( $lnkUrl == 'NT SP' ) {
        $lnkUrl = 'SP';
        $out['target'] = '_blank';
    }

    if( $lnkUrl == 'SP' ) {
        $out['isSP'] = true;
    }

    //if($lnkUrl == 'SP') {
    if($out['isSP']) {
        //If single page url is blank return empty sting.
        if( abcfl_html_isblank( $sPageUrl ) ) { return $out; }        

        if( abcfsl_spg_a_tag_is_single_pretty( $sPageUrl, $pretty ) ) {
            $out['hrefUrl'] = trailingslashit( trailingslashit( $sPageUrl ) . $pretty ) ;
            return $out;
        }
        else {
            //Add staff member ID single page url.
            $out['hrefUrl'] = abcfl_html_url( array('smid' => $staffID), $sPageUrl );
            return $out;
        }
    }
    
    $splitUrl = abcfsl_util_get_url_and_target( $lnkUrl );
    $out['hrefUrl'] = $splitUrl['hrefUrl'];
    $out['target'] =  $splitUrl['target'];
    return $out;
}

//%%%% DISCONTINUED BUT USED BU OTHER PLUGINS %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

//NOT LOCAL abcfsl_util_img_lnk_parts
//Image Link, SPTL link, Hyperlink. ($imgLnkL = SP)
// function abcfsl_spg_a_tag_img_lnk_parts( $staffID, $sPageUrl, $itemOptns, $url ){

//     //$imgLnkL = isset( $itemOptns['_imgLnkL'] ) ? esc_attr( $itemOptns['_imgLnkL'][0] ) : '';
//     $pretty = isset( $itemOptns['_pretty'] ) ? esc_attr( $itemOptns['_pretty'][0] ) : '';
//     $urlParts = abcfsl_spg_a_tag_url_selector( $staffID, $url, $sPageUrl, $pretty );

//     $lnk['imgID'] = abcfsl_spg_a_tag_img_lnk_id( isset( $itemOptns['_imgID'] ) ? esc_attr( $itemOptns['_imgID'][0] ) : 0 );
//     $lnk['href'] = $urlParts['hrefUrl'];
//     $lnk['target'] = $urlParts['target'];
//     $lnk['onclick'] = abcfsl_spg_a_tag_lnk_onclick( isset( $itemOptns['_imgLnkClick'] ) ? esc_attr( $itemOptns['_imgLnkClick'][0] ) : '' );
//     $lnk['args'] = abcfsl_spg_a_tag_lnk_args(isset( $itemOptns['_imgLnkArgs'] ) ? esc_attr( $itemOptns['_imgLnkArgs'][0] ) : '');

//     return $lnk;
// }

// //NOT LOCAL abcfsl_util_url_selector
// //Get Single page Url if 'SP' used as url. Otherwise return URL as entered.
// function abcfsl_spg_a_tag_url_selector( $staffID, $lnkUrl, $sPageUrl, $pretty ){

//     $out['hrefUrl'] = '';
//     $out['target'] = '';
//     $out['isSP'] = false;
//     if( abcfl_html_isblank( $lnkUrl ) ) { return $out;}

//     if( $lnkUrl == 'NT SP' ) {
//         $lnkUrl = 'SP';
//         $out['target'] = '_blank';
//     }

//     if( $lnkUrl == 'SP' ) {
//         $out['isSP'] = true;
//     }

//     //if($lnkUrl == 'SP') {
//     if($out['isSP']) {
//         //If single page url is blank return empty sting.
//         if( abcfl_html_isblank( $sPageUrl ) ) { return $out; }        

//         if( abcfsl_spg_a_tag_is_single_pretty( $sPageUrl, $pretty ) ) {
//             $out['hrefUrl'] = trailingslashit( trailingslashit( $sPageUrl ) . $pretty ) ;
//             return $out;
//         }
//         else {
//             //Add staff member ID single page url.
//             $out['hrefUrl'] = abcfl_html_url( array('smid' => $staffID), $sPageUrl );
//             return $out;
//         }
//     }
    
//     $gt = abcfsl_util_get_url_and_target( $lnkUrl );
//     $out['hrefUrl'] = $gt['hrefUrl'];
//     $out['target'] =  $gt['target'];
//     return $out;
// }

// // Hyperlink builder. Prefer imgLnkL. Use global settings if empty.
// function abcfsl_spg_a_tag_lnk_parts_img_not_custom_OLD( $par, $itemOptns ){

//     $staffID = $par['itemID'];
//     $sPageUrl = $par['sPageUrl']; 
//     $imgLnkL = $par['imgLnkL'];
//     $imgLnkLDefault = $par['imgLnkLDefault'];

//     $lnkParts['imgID'] = 0;
//     $lnkParts['href'] = '';
//     $lnkParts['target'] = '';
//     $lnkParts['onclick'] = '';
//     $lnkParts['args'] = '';

//     //--- If imgLnkL is blank, check global options. ----------
//     if( empty( $imgLnkL ) && $imgLnkLDefault == 1 ) { $imgLnkL = 'SP'; }

//     // Still empty. Exit.
//     if( empty( $imgLnkL ) ) { return $lnkParts; }

//     //-- Target can be added as NT prefix or template's default -----------------------
//     $splitUrl = abcfsl_util_get_url_and_target( $par['imgLnkL'] );
//     $imgLnkL = $splitUrl['hrefUrl'];
//     $target = $splitUrl['target'];

//     if( empty( $target ) ) { 
//         if( $par['sPgLnkNT'] == 1 ) { $target = '_blank'; }  
//     }

//     //---------------------------------------------------------
//     $pretty = isset( $itemOptns['_pretty'] ) ? esc_attr( $itemOptns['_pretty'][0] ) : '';

//     if( $par['sPgLnkShow'] == 'ST' || $par['sPgLnkShow'] == 'SPGHYB' ){
//         $hrefUrl = abcfsl_spg_a_tag_url_hybrid( $staffID, $sPageUrl, $pretty );
//     }
//     else{
//         $hrefUrl = abcfsl_spg_a_tag_url_ugly_pretty( $staffID, $sPageUrl, $pretty );
//     }

//     $lnkParts['imgID'] = abcfsl_spg_a_tag_img_lnk_id( isset( $itemOptns['_imgID'] ) ? esc_attr( $itemOptns['_imgID'][0] ) : 0 );
//     $lnkParts['href'] = $hrefUrl;
//     $lnkParts['target'] = $target;
//     $lnkParts['onclick'] = abcfsl_spg_a_tag_lnk_onclick( isset( $itemOptns['_imgLnkClick'] ) ? esc_attr( $itemOptns['_imgLnkClick'][0] ) : '' );
//     $lnkParts['args'] = abcfsl_spg_a_tag_lnk_args(isset( $itemOptns['_imgLnkArgs'] ) ? esc_attr( $itemOptns['_imgLnkArgs'][0] ) : '');

//     return $lnkParts;
// }

// function abcfsl_spg_a_tag_lnk_parts_img( $parLP, $itemOptns ){

//     // $parLP['staffID']
//     // $parLP['sPageUrl']
//     // $parLP['imgLnkL']
//     // $parLP['sPgLnkShow']
//     // $parLP['imgLnkLDefault']
//     // $parLP['sPgLnkNT'] 

//     $parLP['href'] = ''; 
//     $parLP['target'] = ''; 

//     $staffID = $parLP['staffID'];
//     $sPageUrl = $parLP['sPageUrl'];
//     //$imgLnkL = $parLP['imgLnkL'];
//     $sPgLnkShow = $parLP['sPgLnkShow'];
//     //$imgLnkLDefault = $parLP['imgLnkLDefault'];
//     //$sPgLnkNT = $parLP['sPgLnkNT'];

//     $lnkParts['imgID'] = 0;
//     $lnkParts['href'] = '';
//     $lnkParts['target'] = '';
//     $lnkParts['onclick'] = '';
//     $lnkParts['args'] = '';


//     //--- No image hyperlink -----------------------------------
//     // Template option. Show Link.
//     if(  $sPgLnkShow == 'N' ) { return $lnkParts; }

//     // Template option. Add link to staff image (image hyperlink).
//     if( $parLP['imgLnkLDefault'] != 1 ) { return $lnkParts; }

//      // Staff member option.
//     $hideSPgLnk = isset( $itemOptns['_hideSPgLnk'] ) ? $itemOptns['_hideSPgLnk'][0] : '0';
//     if( $hideSPgLnk == 1 ) { return  $lnkParts; }

//     // Required for all but custom. 
//     if(  $sPgLnkShow != 'SPGCUST' ) {
//         if( abcfl_html_isblank( $sPageUrl ) ) { return $lnkParts; }   
//     }
//     //-----------------------------------------------------------------
//     $parLP = abcfsl_spg_a_tag_img_custom_url_validator( $parLP );
//     $sPgLnkShow = $parLP['sPgLnkShow'];
//     if( empty( $parLP['target'] ) ) { 
//         if( $parLP['sPgLnkNT'] == 1 ) { $parLP['target'] = '_blank'; }  
//     }
//     //------------------------------------------------

//     // ???????????????????????????????????????
//     //-- Check field imgLnkL (Custom URL). Return link if custom (not SP or NT SP) -------------------   
//     // if( !empty( $imgLnkL ) ) { 
//     //     $out = abcfsl_spg_a_tag_lnk_parts_img_custom( $parLP, $itemOptns );        
//     //     if( $out['isImgLnkLCustom'] ) { return $out['lnkParts'];  }        
//     // }

//     // $lnkParts = abcfsl_spg_a_tag_lnk_parts_img_not_custom( $parLP, $itemOptns );
//     // return $lnkParts;


//     // Hybrid = ST, SPGHYB. Custom = SPGCUST.
//     switch ( $sPgLnkShow ) {
//         case 'ST':
//         case 'SPGHYB':    
//             $lnkParts = abcfsl_spg_a_tag_lnk_parts_hybrid( $parLP, $itemOptns );
//             break; 
//         case 'SPGCUST':
//             $lnkParts = abcfsl_spg_a_tag_lnk_parts_custom( $parLP, $itemOptns );
//             break;                       
//         default:
//             $lnkParts = abcfsl_spg_a_tag_lnk_parts_ugly_pretty( $parLP, $itemOptns );
//             break;
//     }

//     return $lnkParts;
// }  

// // Not SP, NT SP, not empty. Custom URL. Full URL. Can have NT prefix.
// function abcfsl_spg_a_tag_lnk_parts_img_custom( $par, $itemOptns ){

//     $imgLnkL = $par['imgLnkL'];
//     $sPgLnkNT = $par['sPgLnkNT'];

//     $splitUrl = abcfsl_util_get_url_and_target( $imgLnkL );
//     $imgLnkL = $splitUrl['hrefUrl'];
//     $target = $splitUrl['target'];

//     $out['isImgLnkLCustom'] = false;
//     $out['lnkParts'] = '';

//     // Not custom URL.
//     if( $imgLnkL == 'SP' ) { return $out; }
//     //-----------------------------------------------    
//     $out['isImgLnkLCustom'] = true;

//     //Add default value if no NT prefix
//     if( empty( $target ) ) { 
//         if( $sPgLnkNT == 1 ) { $target = '_blank'; }  
//     }

//     $lnkParts['imgID'] = abcfsl_spg_a_tag_img_lnk_id( isset( $itemOptns['_imgID'] ) ? esc_attr( $itemOptns['_imgID'][0] ) : 0 );
//     $lnkParts['href'] = $imgLnkL;
//     $lnkParts['target'] = $target;
//     $lnkParts['onclick'] = abcfsl_spg_a_tag_lnk_onclick( isset( $itemOptns['_imgLnkClick'] ) ? esc_attr( $itemOptns['_imgLnkClick'][0] ) : '' );
//     $lnkParts['args'] = abcfsl_spg_a_tag_lnk_args(isset( $itemOptns['_imgLnkArgs'] ) ? esc_attr( $itemOptns['_imgLnkArgs'][0] ) : '');

//     $out['lnkParts'] =  $lnkParts;

//     return $out;
// }

// // Hybrid pages have to have pretty as page name.
// function abcfsl_spg_a_tag_url_hybrid( $sPageUrl, $pretty, $imgLnkL, $lnkParts ){

//     if( empty( $pretty ) ) { 
//         return $lnkParts;
//     }

//     if( $imgLnkL == 'NT' ) { 
//         $lnkParts['target'] = '_blank'; 
//     } 

//     $lnkParts['href'] = trailingslashit( trailingslashit( $sPageUrl ) . $pretty ); 
    
//     return lnkParts;
// }

// // Custom pages use staff member custom URL.
// function abcfsl_spg_a_tag_url_custom( $imgLnkL, $lnkParts ){

//     if( empty( $imgLnkL ) ) { 
//         return $lnkParts;
//     } 

//     if( $imgLnkL == 'SP' ) { 
//         return $lnkParts;
//     } 

//     $splitUrl = abcfsl_util_get_url_and_target( $imgLnkL );
//     $lnkParts['href']  = $splitUrl['hrefUrl'];
//     $target = $splitUrl['target'];

//     if( !empty( $target ) ) { 
//         $lnkParts['target'] = $target;  
//     }

//     return $lnkParts;
// }

// function abcfsl_spg_a_tag_img_custom_url_validator( $parLP ){

//     // imgLnkL (Custom URL) can be: Empty, SP, NT, NT SP, Full URL, NT Full URL
//     $imgLnkL = $parLP['imgLnkL'];

//     if( $parLP['sPgLnkShow'] != 'SPGCUST' ) { 

//         // Check content of field imgLnkL (Custom URL). 
//         if( empty( $imgLnkL ) ) { 
//             $parLP['imgLnkL'] = 'SP';
//             return $parLP;
//         } 

//         if( $imgLnkL == 'SP' ) { 
//             return $parLP;
//         }

//         if( $imgLnkL == 'NT' ) { 
//             $parLP['imgLnkL'] = 'SP';
//             $parLP['sPgLnkNT'] = 1;
//             return $parLP;
//         }

//         if( $imgLnkL == 'NT SP' ) { 
//             $parLP['imgLnkL'] = 'SP';
//             $parLP['sPgLnkNT'] = 1;
//             return $parLP;
//         }        
//     } 

//     // Custom hyperlinks - imgLnkL has to be populated with full URL
//     if( $parLP['sPgLnkShow'] == 'SPGCUST' ) { 

//         if( empty( $imgLnkL ) ) { 
//             return $parLP;
//         } 

//         if( $imgLnkL == 'SP' ) { 
//             return $parLP;
//         }
//     } 

//     $splitUrl = abcfsl_util_get_url_and_target( $imgLnkL );
//     $parLP['href'] = $splitUrl['hrefUrl'];
//     $parLP['target'] = $splitUrl['target'];

//     $parLP['sPgLnkShow'] = 'SPGCUST';
//     return $parLP;


//     // if( empty( $imgLnkL ) && $parLP['sPgLnkShow'] == 'ST' ) { 
//     //     return $parLP;
//     // }

//     // if( $imgLnkL == 'SP' && $parLP['sPgLnkShow'] == 'ST' ) { 
//     //     return $parLP;
//     // }

//     // if( $imgLnkL == 'NT SP' && $parLP['sPgLnkShow'] == 'ST' ) { 
//     //     return $parLP;
//     // }

//     // if( $imgLnkL == 'NT' && $parLP['sPgLnkShow'] == 'ST' ) { 
//     //     $parLP['sPgLnkNT'] = 1;
//     //     return $parLP;
//     // }

//     // Check content of field imgLnkL (Custom URL). 
//     // if( empty( $imgLnkL ) ) { 
//     //     $parLP['imgLnkL'] = 'SP';
//     //     return $parLP;
//     // } 

//     // if( $imgLnkL == 'SP' ) { 
//     //     return $parLP;
//     // }

//     // if( $imgLnkL == 'NT' ) { 
//     //     $parLP['imgLnkL'] = 'SP';
//     //     $parLP['sPgLnkNT'] = 1;
//     //     return $parLP;
//     // }

//     // if( $imgLnkL == 'NT SP' ) { 
//     //     $parLP['imgLnkL'] = 'SP';
//     //     $parLP['sPgLnkNT'] = 1;
//     //     return $parLP;
//     // }   
// }

// function abcfsl_spg_a_tag_lnk_parts_txt_OLD( $parLP, $itemOptns ){

//     // $parLP['staffID'] 
//     // $parLP['sPageUrl']
//     // $parLP['sPgLnkShow']
//     // $parLP['sPgLnkNT']
//     // $parLP['lineTxt']
//     // $parLP['imgLnkL']

//     $parLP['href'] = ''; 
//     $parLP['target'] = '';
//     $parLP['imgLnkLDefault'] = '';

//     $lnkParts['imgID'] = 0;
//     $lnkParts['href'] = '';
//     $lnkParts['target'] = '';
//     $lnkParts['onclick'] = '';
//     $lnkParts['args'] = '';

//     //== No hyperlink - exit=====================================================
//     //--- No link to single page (Show Link cbo) -----------------------------
//     if(  $parLP['sPgLnkShow'] == 'N' ) { return  $lnkParts; }

//     //--- No link to single page (Staff member option) -----------------------
//     $hideSPgLnk = isset( $itemOptns['_hideSPgLnk'] ) ? $itemOptns['_hideSPgLnk'][0] : '0';
//     if( $hideSPgLnk == 1 ) { return  $lnkParts; }

//     //-- Link Text can't be blank: sPgLnkTxt --------------------------
//     if( abcfl_html_isblank( $parLP['lineTxt'] ) ) {  return $lnkParts;  }
    
//     // Required for all but custom pages. 
//     // if( $sPgLnkShow == 'SPGCUST') { $sPgLnkShow = 'SPCUST'; }
//     // if(  $parLP['sPgLnkShow'] != 'SPCUST' ) {
//     //     if( abcfl_html_isblank( $parLP['sPageUrl'] ) ) { return $lnkParts; }   
//     // }

//     if(  $parLP['sPgLnkShow'] == 'Y' ) {
//         if( abcfl_html_isblank( $parLP['sPageUrl'] ) ) { return $lnkParts; }   
//     }
//     //=========================================================================
    
//     //-------------------------------------------------------------
//     $parLP = abcfsl_spg_a_tag_img_custom_url_validator( $parLP );
    
//     if( empty( $parLP['target'] ) ) { 
//         if( $parLP['sPgLnkNT'] == 1 ) { $parLP['target'] = '_blank'; }  
//     }

//     //------------------------------------------------
//     // Hybrid = ST, SPGHYB. Custom = SPGCUST.
//     switch ( $parLP['sPgLnkShow'] ) {
//         case 'ST':
//         case 'SPHYB':     
//             $lnkParts = abcfsl_spg_a_tag_lnk_parts_hybrid( $parLP, $itemOptns, false );
//             break; 
//         case 'SPGCUST':
//         case 'SPCUST':    
//             $lnkParts = abcfsl_spg_a_tag_lnk_parts_custom( $parLP, $itemOptns, false );
//             break;                       
//         default:
//             $lnkParts = abcfsl_spg_a_tag_lnk_parts_ugly_pretty( $parLP, $itemOptns, false );
//             break;
//     }

//     return $lnkParts;
// }  

// function abcfsl_spg_a_tag_lnk_parts_img_OLD( $parLP, $itemOptns ){

//     // $parLP['staffID']
//     // $parLP['sPageUrl']
//     // $parLP['imgLnkL']
//     // $parLP['sPgLnkShow']
//     // $parLP['imgLnkLDefault']
//     // $parLP['sPgLnkNT']

//     $parLP['imgLnkL'] = isset( $itemOptns['_imgLnkL'] ) ? esc_attr( $itemOptns['_imgLnkL'][0] ) : '';
//     //$parLP['href'] = ''; 
//     $parLP['target'] = ''; 

//     $lnkParts['imgID'] = 0;
//     $lnkParts['href'] = '';
//     $lnkParts['target'] = '';
//     $lnkParts['onclick'] = '';
//     $lnkParts['args'] = '';

//     //--- No image hyperlink -----------------------------------
//     // Template option. Show Link.
//     if( $parLP['sPgLnkShow'] == 'N' ) { return $lnkParts; }

//     // Template option. Add link to staff image (image hyperlink).
//     if( $parLP['imgLnkLDefault'] != 1 ) { return $lnkParts; }

//      // Staff member option.
//     $hideSPgLnk = isset( $itemOptns['_hideSPgLnk'] ) ? $itemOptns['_hideSPgLnk'][0] : '0';
//     if( $hideSPgLnk == 1 ) { return  $lnkParts; }

//     // Required for all but custom. 
//     if( $parLP['sPgLnkShow'] != 'SPGCUST' ) { if( abcfl_html_isblank( $parLP['sPageUrl'] ) ) { return $lnkParts; } }
//     //-----------------------------------------------------------------
//     $parLP = abcfsl_spg_a_tag_img_custom_url_validator( $parLP );
//     if( empty( $parLP['target'] ) ) { 
//         if( $parLP['sPgLnkNT'] == 1 ) { $parLP['target'] = '_blank'; }  
//     }
//     //------------------------------------------------
//     // Hybrid = ST, SPGHYB. Custom = SPGCUST.
//     switch ( $parLP['sPgLnkShow'] ) {
//         case 'ST': 
//         case 'SPGHYB':      
//             $lnkParts = abcfsl_spg_a_tag_lnk_parts_hybrid( $parLP, $itemOptns, true );
//             break; 
//         case 'SPGCUST':
//             $lnkParts = abcfsl_spg_a_tag_lnk_parts_custom( $parLP, $itemOptns, true );
//             break;                       
//         default:
//             $lnkParts = abcfsl_spg_a_tag_lnk_parts_ugly_pretty( $parLP, $itemOptns, true );
//             break;
//     }

//     return $lnkParts;
// }

// // Check content of staff member field: imgLnkL (Custom URL)
// function abcfsl_spg_a_tag_img_custom_url_validator_OLD( $parLP ){

//     // This field imgLnkL has a new purpose. It may contain legacy data for $parLP['sPgLnkShow'] = Y.
//        // Replacement is ro_imgLnkL. Populated by template.
   
//        // Legacy ??????? or individual settings when Open in a new tab or window. is not checked
//        // Staff member, field imgLnkL (Custom URL) can be: Empty, SP, NT, NT SP, Full URL, NT Full URL
//        $imgLnkL = $parLP['imgLnkL'];
   
//        if( $parLP['sPgLnkShow'] != 'SPGCUST' ) { 
   
//            // Check content of field imgLnkL (Custom URL). 
//            if( empty( $imgLnkL ) ) { 
//                $parLP['imgLnkL'] = 'SP';
//                return $parLP;
//            } 
   
//            if( $imgLnkL == 'SP' ) { 
//                return $parLP;
//            }
   
//            if( $imgLnkL == 'NT' ) { 
//                $parLP['imgLnkL'] = 'SP';
//                $parLP['sPgLnkNT'] = 1;
//                return $parLP;
//            }
   
//            if( $imgLnkL == 'NT SP' ) { 
//                $parLP['imgLnkL'] = 'SP';
//                $parLP['sPgLnkNT'] = 1;
//                return $parLP;
//            }        
//        } 
   
//        // Custom hyperlinks - imgLnkL has to be populated with full URL
//        if( $parLP['sPgLnkShow'] == 'SPGCUST' ) { 
   
//            if( empty( $imgLnkL ) ) { 
//                return $parLP;
//            } 
   
//            if( $imgLnkL == 'SP' ) { 
//                return $parLP;
//            }
//        } 
   
//        $splitUrl = abcfsl_util_get_url_and_target( $imgLnkL );
//        $parLP['href'] = $splitUrl['hrefUrl'];
//        $parLP['target'] = $splitUrl['target'];
   
//        $parLP['sPgLnkShow'] = 'SPGCUST';
//        return $parLP;
//    }

//    //=== SPTL SINGLE PAGE TEXT LINK START ==========================================================
// //Called from: abcfsl_cnt_field_SPTL. Returns array. Called from: abcfslub_cnt_txt_field 
// function abcfsl_spg_a_tag_lnk_parts_txt( $parLP, $itemOptns ){

//     $lnkParts['imgID'] = 0;
//     $lnkParts['href'] = '';
//     $lnkParts['target'] = '';
//     $lnkParts['onclick'] = '';
//     $lnkParts['args'] = '';

//     //== If no hyperlink - exit=========================================
//     $parLP = abcfsl_spg_a_tag_get_lnk_parts( $parLP, $itemOptns, true );
//     if( !$parLP['showLnk'] ) { return $lnkParts; }
//     //=========================================================================

//     // Hybrid = ST, SPGHYB. Custom = SPGCUST.
//     switch ( $parLP['sPgLnkShow'] ) {
//         case 'ST':
//         case 'SPHYB':     
//             $lnkParts = abcfsl_spg_a_tag_lnk_parts_hybrid( $parLP, $itemOptns, false );
//             break; 
//         case 'SPGCUST':
//         case 'SPCUST':    
//             $lnkParts = abcfsl_spg_a_tag_lnk_parts_custom( $parLP, $itemOptns, false );
//             break;                       
//         default:
//             $lnkParts = abcfsl_spg_a_tag_lnk_parts_ugly_pretty( $parLP, $itemOptns, false );
//             break;
//     }

//     return $lnkParts;
// }  
// //=== SPTL TEXT LINK END =============================================================

// //=== SPTL IMAGE LINK START ==========================================================
// //Called from: abcfsl_cnt_img_options. Returns array.
// function abcfsl_spg_a_tag_lnk_parts_img( $parLP, $itemOptns ){

//     $lnkParts['imgID'] = 0;
//     $lnkParts['href'] = '';
//     $lnkParts['target'] = '';
//     $lnkParts['onclick'] = '';
//     $lnkParts['args'] = '';

//     //== If no hyperlink - exit=========================================
//     $parLP = abcfsl_spg_a_tag_get_lnk_parts( $parLP, $itemOptns, false );
//     if( !$parLP['showLnk'] ) { return $lnkParts; }
//     //=========================================================================

//     // Hybrid = ST, SPGHYB. Custom = SPGCUST.
//     switch ( $parLP['sPgLnkShow'] ) {
//         case 'ST': 
//         case 'SPGHYB':      
//             $lnkParts = abcfsl_spg_a_tag_lnk_parts_hybrid( $parLP, $itemOptns, true );
//             break; 
//         case 'SPGCUST':
//             $lnkParts = abcfsl_spg_a_tag_lnk_parts_custom( $parLP, $itemOptns, true );
//             break;                       
//         default:
//             $lnkParts = abcfsl_spg_a_tag_lnk_parts_ugly_pretty( $parLP, $itemOptns, true );
//             break;
//     }

//     return $lnkParts;
// }
// //=== SPTL IMAGE LINK END ==========================================================