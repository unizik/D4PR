<?php 

function generateId($len = 10){

    $source = "abcdefghijklm0123456789nopqrstuvwxyz0123456789abcdefghijklmn0123456789opqrstuvwxyz0123456789";
    $range = strlen($source);
    $output = '';
    for($i = 0; $i<$len; $i++){
            $output .= substr($source, rand(0, $range - 1), 1);
    }
    return $output;
    
}

function get_error($msg){
    return '<div class="alert alert-danger">'.$msg.'</div>';
}

function get_success($msg){
    return '<div class="alert alert-info">'.$msg.'</div>';
}

function get_img($imagename){
    return  base_url('/resources/assets/img/'.$imagename);
}

function encrypt($string) {
    return hash('sha512', $string. config_item('encryption_key'));
}

function get_del_btn($url) {
    
    return anchor($url, '<i class="glyphicon glyphicon-remove"></i> ', 
            array(
                'title' => 'Delete this record', 
                'onclick' => "return confirm('You are about to delete a record. Action cannot be undone. Are you sure you want to proceed?');"
                )
            );
}

function get_edit_btn($url) {
    
    return anchor($url, '<i class="glyphicon glyphicon-edit"></i> ', 
            array(
                'title' => 'Edit this record'
                )
            );
}
