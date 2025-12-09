<?php
defined('BASEPATH') or exit('No direct script access allowed');

function mcsp_get_linked_sites($contact_id)
{
    $CI = &get_instance();
    $CI->db->select('c.customer_id, cl.company, cl.userid');
    $CI->db->from(db_prefix().'mcsp_contact_sites as c');
    $CI->db->join(db_prefix().'clients as cl', 'cl.userid = c.customer_id', 'left');
    $CI->db->where('c.contact_id', (int)$contact_id);
    return $CI->db->get()->result_array();
}

function mcsp_link_site($contact_id, $customer_id)
{
    $CI = &get_instance();
    $exists = $CI->db->get_where(db_prefix().'mcsp_contact_sites', [
        'contact_id'=>(int)$contact_id, 'customer_id'=>(int)$customer_id
    ])->row_array();
    if (!$exists) {
        $CI->db->insert(db_prefix().'mcsp_contact_sites', [
            'contact_id'=>(int)$contact_id,
            'customer_id'=>(int)$customer_id
        ]);
    }
}

function mcsp_unlink_site($contact_id, $customer_id)
{
    $CI = &get_instance();
    $CI->db->where('contact_id',(int)$contact_id)->where('customer_id',(int)$customer_id)->delete(db_prefix().'mcsp_contact_sites');
}

function mcsp_switch_active_site($customer_id)
{
    $CI = &get_instance();
    if (!is_client_logged_in()) return false;
    $customer_id = (int)$customer_id;
    $contact = get_contact(get_contact_user_id());
    if (!$contact) return false;

    $linked = mcsp_get_linked_sites($contact->id);
    $allowed = false;
    foreach ($linked as $s) { if ((int)$s['customer_id'] === $customer_id) { $allowed = true; break; } }
    if (!$allowed) return false;

    $existing = $CI->db->get_where(db_prefix().'contacts', [
        'email'=>$contact->email, 'userid'=>$customer_id
    ])->row();
    if (!$existing) {
        $CI->load->model('clients_model');
        $data = [
            'userid'     => $customer_id,
            'firstname'  => $contact->firstname,
            'lastname'   => $contact->lastname,
            'email'      => $contact->email,
            'phonenumber'=> $contact->phonenumber,
            'title'      => $contact->title,
            'password'   => '',
            'is_primary' => 0,
            'active'     => 1,
        ];
        $new_id = $CI->clients_model->add_contact($data, $customer_id, true);
        $existing = get_contact($new_id);
    }
    $CI->session->set_userdata([
        'client_user_id'  => (int)$existing->id,
        'contact_user_id' => (int)$existing->id,
    ]);
    return true;
}
