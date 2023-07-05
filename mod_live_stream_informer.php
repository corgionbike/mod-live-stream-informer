<?php
/**
 * Live_stream_informer
 * 
 * @version 	1.0.4	
 * @author	Ametov S.I.
 * @copyright	© 2013. All rights reserved. 
 * @license 	GNU/GPL v.3 or later.
 */

// No direct access.
defined('_JEXEC') or die();

require_once dirname(__FILE__).'/helper.php';

$json_list = array();

$moduleclass_sfx = htmlspecialchars($params->get('moduleclass_sfx'));
$effect = $params->get('effects', "blind");
$width = $params->get('width', 600);
$height = $params->get('height', 400);
$display = $params->get('display_ch', 0);
$typePlayer = $params->get('typeplayer', 0);
$char = $params->get('char', 0);
$streams = new modLSIHelper($params->get('id_channels'), $display);
modLSIHelper::inizialize($params->get('theme', 0), $params->get('local', 0));
$json_list = $streams->jsons;
$strId = implode(" ", $streams->clients);
$layout = $params->get('layout', 'default');

require JModuleHelper::getLayoutPath('mod_live_stream_informer', $layout);

