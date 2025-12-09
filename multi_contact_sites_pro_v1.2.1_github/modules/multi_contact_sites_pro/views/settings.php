<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="row">
  <div class="col-md-12">
    <h4><?php echo _l('mcsp_settings_tab'); ?></h4>
    <div class="alert alert-info">
      This free module allows the same email across different customers by adding a composite unique constraint on <code>(userid,email)</code> and removing any global-unique on <code>email</code>.<br/>
      It also overrides the contact modal <em>remote</em> validator (admin & portal) to check uniqueness per customer without editing core files.
    </div>
    <p>No additional settings required. Use the customer profile tab <strong>Multi Contact Sites PRO</strong> to link contacts to additional customers. The client portal gets a "My Sites" dropdown.</p>
  </div>
</div>
