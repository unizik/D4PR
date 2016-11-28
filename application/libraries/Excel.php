<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Excel
 *
 * @author TOCHUKWU
 */

    //put your code here
    if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH."/third_party/PHPExcel.php";
class Excel  extends PHPExcel {
    public function __construct() {
        parent::__construct();
    }

}
?>
