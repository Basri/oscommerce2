<?php
use OSC\OM\OSCOM;
?>
<div class="panel panel-default">
  <div class="panel-heading"><?php echo MODULE_BOXES_SEARCH_BOX_TITLE; ?></div>
  <div class="panel-body text-center"><?php echo $form_output; ?></div>
  <div class="panel-footer"><?php echo MODULE_BOXES_SEARCH_BOX_TEXT . '<br /><a href="' . OSCOM::link('advanced_search.php') . '"><strong>' . MODULE_BOXES_SEARCH_BOX_ADVANCED_SEARCH . '</strong></a>'; ?></div>
</div>
