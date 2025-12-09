<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<style>.mcsp-switcher{position:fixed;right:10px;top:10px;z-index:9999}</style>
<div class="mcsp-switcher dropdown">
  <button class="btn btn-default dropdown-toggle" type="button" data-toggle="dropdown">
    <?php echo _l('mcsp_my_sites'); ?> <span class="caret"></span>
  </button>
  <ul class="dropdown-menu dropdown-menu-right">
    <?php foreach($sites as $s): ?>
      <li><a href="<?php echo site_url('mcsp_portal/switch_site/'.$s['customer_id']); ?>"><?php echo html_escape($s['company']); ?></a></li>
    <?php endforeach; ?>
    <li role="separator" class="divider"></li>
    <li><a href="<?php echo site_url('mcsp_portal/sites'); ?>"><?php echo _l('mcsp_manage'); ?></a></li>
  </ul>
</div>
