<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Mcsp_admin extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->helper('multi_contact_sites_pro/mcsp');
    }

    public function link()
    {
        $contact_id  = (int)$this->input->post('contact_id');
        $customer_id = (int)$this->input->post('customer_id');
        if ($contact_id && $customer_id) {
            mcsp_link_site($contact_id, $customer_id);
            set_alert('success', _l('mcsp_linked'));
        } else {
            set_alert('danger', _l('mcsp_invalid_input'));
        }
        redirect(admin_url('clients/client/'.$customer_id.'?group=multi_contact_sites_pro'));
    }

    public function unlink($contact_id, $customer_id)
    {
        mcsp_unlink_site((int)$contact_id, (int)$customer_id);
        set_alert('success', _l('mcsp_unlinked'));
        redirect($_SERVER['HTTP_REFERER'] ?? admin_url('clients'));
    }
}
