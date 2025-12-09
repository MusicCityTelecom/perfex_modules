<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div role="tabpanel" class="tab-pane" id="multi_contact_sites_pro">
  <h4 class="mbot15"><?php echo _l('mcsp_tab_title'); ?></h4>
  <p class="text-muted"><?php echo _l('mcsp_tab_help'); ?></p>
  <?php echo form_open(admin_url('mcsp_admin/link')); ?>
    <div class="row">
      <div class="col-md-3">
        <label><?php echo _l('mcsp_contact_id'); ?></label>
        <input type="number" name="contact_id" class="form-control" placeholder="e.g., 123" required>
      </div>
      <div class="col-md-3">
        <label><?php echo _l('mcsp_customer_id'); ?></label>
        <input type="number" name="customer_id" class="form-control" placeholder="e.g., 57" required>
      </div>
      <div class="col-md-2">
        <label>&nbsp;</label>
        <button class="btn btn-primary btn-block" type="submit"><?php echo _l('mcsp_link_btn'); ?></button>
      </div>
    </div>
  <?php echo form_close(); ?>
  <hr/>
  <h5 class="mbot10"><?php echo _l('mcsp_existing_links'); ?></h5>
  <?php
    $client_id = isset($client_id)?(int)$client_id:0;
    $links = $this->db->select('c.contact_id, c.customer_id, cl.company')
                      ->from(db_prefix().'mcsp_contact_sites as c')
                      ->join(db_prefix().'clients as cl','cl.userid=c.customer_id','left')
                      ->where('c.customer_id', $client_id)->get()->result_array();
  ?>
  <?php if(empty($links)): ?>
    <p class="text-muted"><?php echo _l('mcsp_no_links'); ?></p>
  <?php else: ?>
    <table class="table table-bordered">
      <thead>
        <tr>
          <th><?php echo _l('mcsp_contact_id'); ?></th>
          <th><?php echo _l('mcsp_customer_id'); ?></th>
          <th><?php echo _l('mcsp_company'); ?></th>
          <th></th>
        </tr>
      </thead>
      <tbody>
        <?php foreach($links as $row): ?>
        <tr>
          <td><?php echo (int)$row['contact_id']; ?></td>
          <td><?php echo (int)$row['customer_id']; ?></td>
          <td><?php echo html_escape($row['company']); ?></td>
          <td>
            <a class="btn btn-danger btn-xs" href="<?php echo admin_url('mcsp_admin/unlink/'.$row['contact_id'].'/'.$row['customer_id']); ?>" onclick="return confirm('Unlink?');">
              <?php echo _l('mcsp_unlink_btn'); ?>
            </a>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  <?php endif; ?>
</div>
