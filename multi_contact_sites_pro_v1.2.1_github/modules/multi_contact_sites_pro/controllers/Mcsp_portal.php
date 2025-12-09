<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Mcsp_portal extends ClientsController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->helper('multi_contact_sites_pro/mcsp');
        if (!is_client_logged_in()) { redirect(site_url('clients/login')); }
    }

    public function sites()
    {
        $data['linked'] = mcsp_get_linked_sites(get_contact_user_id());
        $this->data($data);
        $this->view('multi_contact_sites_pro/portal_sites');
        $this->layout();
    }

    public function switch_site($customer_id)
    {
        if (mcsp_switch_active_site((int)$customer_id)) {
            set_alert('success', _l('mcsp_switched'));
        } else {
            set_alert('danger', _l('mcsp_switch_failed'));
        }
        redirect(site_url('mcsp_portal/sites'));
    }
}
