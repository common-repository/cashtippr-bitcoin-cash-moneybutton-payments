<?php
if (!defined('ABSPATH'))
	exit("denied");
?>
<div id="ct-modal-adblock-dialog-tpl">
  <div id="ct-modal-adblock-dialog" class="{dialog_class}">
    <div id="ct-modal-background"></div>
      <div id="ct-modal" class="ct-modal">
        <div class="ct-modal-inner">
        <div class="adbl-warning">
          <h2 id="ct-adbl-title" class="ct-dialog-head">{title}</h2>
        </div>
        <div id="ct-adbl-content">{text}</div>
      </div>
    </div>
  </div>
</div>