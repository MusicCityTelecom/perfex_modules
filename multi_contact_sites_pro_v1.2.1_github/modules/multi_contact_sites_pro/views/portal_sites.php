<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="panel_s">
  <div class="panel-body">
    <h4><?php echo _l('mcsp_my_sites'); ?></h4>
    <?php $linked = isset($linked)?$linked:[]; ?>
    <?php if (empty($linked)): ?>
      <p class="text-muted"><?php echo _l('mcsp_no_linked_sites'); ?></p>
    <?php else: ?>
      <ul class="list-unstyled">
      <?php foreach($linked as $s): ?>
        <li class="mtop10">
          <strong><?php echo html_escape($s['company']); ?></strong>
          <a class="btn btn-default btn-sm mleft10" href="<?php echo site_url('mcsp_portal/switch_site/'.$s['customer_id']); ?>"><?php echo _l('mcsp_switch'); ?></a>
        </li>
      <?php endforeach; ?>
      </ul>
    <?php endif; ?>
  </div>
</div>
