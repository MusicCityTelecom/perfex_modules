<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Mcsp_public extends ClientsController
{
    public function contact_email_exists()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }
        $email       = trim($this->input->post('email'));
        $exclude     = $this->input->post('exclude');
        $customer_id = $this->input->post('customer_id');
        if ($customer_id === null || $customer_id === '') {
            $customer_id = $this->input->post('userid');
        }

        $this->db->select('id')->from(db_prefix().'contacts');
        $this->db->where('email', $email);
        if (!empty($exclude)) {
            $this->db->where('id !=', (int)$exclude);
        }
        if (!empty($customer_id)) {
            $this->db->where('userid', (int)$customer_id);
        }
        $this->db->limit(1);
        $exists = (bool)$this->db->get()->row();
        echo $exists ? 'false' : 'true';
        die;
    }
}
