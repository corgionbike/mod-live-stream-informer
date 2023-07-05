<?php
/**
 * @package     mmotimes.ru
 * @subpackage  Module
 * @copyright   (C) 2013 http://mmotimes.ru
 * @author	Ametov S.I.
 * @license     License GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access.
defined('_JEXEC') or die();
?>
<!--Live Stream Informer RC3 for Joomla 2.5 & 3.2 powered by mmotimes.ru-->
<div id = "lsi-module" class="live-stream-informer<?=$moduleclass_sfx;?>" data-typePlayer="<?=$typePlayer;?>" data-char="<?=$char;?>" data-effect="<?=$effect;?>" data-width="<?=$width;?>" data-height="<?=$height;?>" data-display="<?=$display;?>">
    <div class="lsi-cell">
        <div class="lsi-header-cell"><span class='lsi-top-header'>Канал</span><span class='lsi-top-header'>Статус</span></div>
        <div id="lsi-content">
            <?=modLSIHelper::getContent($json_list, $char, $typePlayer, $height, $width);?>
        </div>
        <div class="lsi-line"></div>
        <div class="lsi-refresh-block"><img style="display: none;" id="lsi-bar" src="/modules/mod_live_stream_informer/css/images/bar.gif">
            <button  id="lsi-refresh" data-id="<?=$strId;?>">Обновить</button></div>     
    </div>
</div>

