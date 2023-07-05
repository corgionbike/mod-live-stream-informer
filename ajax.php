<?php
/**
 * Live_stream_informer
 * 
 * @version 	1.0.5	
 * @author	Ametov S.I.
 * @copyright	© 2013. All rights reserved. 
 * @license 	GNU/GPL v.3 or later.
 */

header('Content-Type: text/html; charset=utf-8');
header('Cache-Control: no-store; no-cache');
header('Expires: ' . date('r'));
define('_JEXEC', 1);
require_once dirname(__FILE__).'/helper.php';

$isAjaxRequest = modLSIHelper::getIsAjaxRequest();

if ($_SERVER['REQUEST_METHOD'] == 'GET' && $isAjaxRequest)
{
    $char = modLSIHelper::formatstr(filter_input(INPUT_GET, 'char', FILTER_VALIDATE_INT));
    $typePlayer = modLSIHelper::formatstr(filter_input(INPUT_GET, 'typeplayer', FILTER_VALIDATE_INT));
    $height = modLSIHelper::formatstr(filter_input(INPUT_GET, 'height', FILTER_VALIDATE_INT));
    $width = modLSIHelper::formatstr(filter_input(INPUT_GET, 'width', FILTER_VALIDATE_INT));
    $id_list = modLSIHelper::formatstr(filter_input(INPUT_GET, 'id'));
    $display = modLSIHelper::formatstr(filter_input(INPUT_GET, 'display', FILTER_VALIDATE_INT));
    $streams = new modLSIHelper($id_list, $display);
    $json_list = $streams->jsons;
    echo modLSIHelper::getContent($json_list, $char, $typePlayer, $height, $width);
}
else die('Access denied');
?>
