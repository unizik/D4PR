<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

function __autoload($classname) {
    if (strpos($classname, 'CI_') !== 0) {
        $filepath = APPPATH . 'libraries/' . $classname . '.php';
        if (file_exists($filepath) && is_file($filepath)) {
            @include_once($filepath);
        }
    }
}

$config['base_url'] = 'http://localhost/D4PR/';

$config['index_page'] = 'index.php';
$config['uri_protocol'] = 'AUTO';
$config['url_suffix'] = '';
$config['language'] = 'english';
$config['charset'] = 'UTF-8';
$config['enable_hooks'] = FALSE;
$config['subclass_prefix'] = 'MY_';
$config['permitted_uri_chars'] = 'a-z 0-9~%.:_\-';
$config['allow_get_array'] = TRUE;
$config['enable_query_strings'] = FALSE;
$config['controller_trigger'] = 'c';
$config['function_trigger'] = 'm';
$config['directory_trigger'] = 'd'; // experimental not currently in use
$config['log_threshold'] = 0;
$config['log_path'] = '';
$config['log_date_format'] = 'd-m-Y H:i:s';
$config['cache_path'] = '';
$config['encryption_key'] = 'cb90ab4b184b16ffc8673';
$config['sess_cookie_name'] = 'cisession';
$config['sess_expiration'] = 7200;
$config['sess_expire_on_close'] = TRUE;
$config['sess_encrypt_cookie'] = TRUE;
$config['sess_use_database'] = TRUE;
$config['sess_table_name'] = 'ci_sessions';
$config['sess_match_ip'] = FALSE;
$config['sess_match_useragent'] = TRUE;
$config['sess_time_to_update'] = 300;
$config['cookie_prefix'] = "";
$config['cookie_domain'] = "";
$config['cookie_path'] = "/";
$config['cookie_secure'] = FALSE;
$config['global_xss_filtering'] = FALSE;
$config['csrf_protection'] = FALSE;
$config['csrf_token_name'] = 'csrf_test_name';
$config['csrf_cookie_name'] = 'csrf_cookie_name';
$config['csrf_expire'] = 7200;
$config['compress_output'] = FALSE;
$config['time_reference'] = 'local';
$config['rewrite_short_tags'] = FALSE;
$config['proxy_ips'] = '';

/*
  |--------------------------------------------------------------------------
  | Error Logging Threshold
  |--------------------------------------------------------------------------
  |
  | If you have enabled error logging, you can set an error threshold to
  | determine what gets logged. Threshold options are:
  | You can enable error logging by setting a threshold over zero. The
  | threshold determines what gets logged. Threshold options are:
  |
  |	0 = Disables logging, Error logging TURNED OFF
  |	1 = Error Messages (including PHP errors)
  |	2 = Debug Messages
  |	3 = Informational Messages
  |	4 = All Messages
  |
  | For a live site you'll usually only enable Errors (1) to be logged otherwise
  | your log files will fill up very fast.
  |
 */
/* End of file config.php */
/* Location: ./application/config/config.php */
