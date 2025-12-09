<?php
defined('BASEPATH') or exit('No direct script access allowed');

/*
Module Name: Multi Contact Sites PRO
Description: Allow the same contact email across multiple customers, provide per-customer email validators (admin & portal) without core edits, add contact-to-multiple-sites linking and a client-portal site switcher.
Version: 1.2.1
Requires at least: 3.x
Author: TechFinity (Free)
*/

define('MCSP_MODULE', 'multi_contact_sites_pro');

register_activation_hook(MCSP_MODULE, 'mcsp_activate');
register_deactivation_hook(MCSP_MODULE, 'mcsp_deactivate');
register_language_files(MCSP_MODULE, ['multi_contact_sites_pro']);

// Hooks
hooks()->add_action('admin_init', 'mcsp_admin_init');
hooks()->add_action('customers_init', 'mcsp_customers_init');
hooks()->add_action('app_admin_footer', 'mcsp_inject_admin_js');           // override admin contact modal validator
hooks()->add_action('app_customers_footer', 'mcsp_inject_portal_js');      // override portal validator (if used)
hooks()->add_action('app_customers_footer', 'mcsp_portal_switcher_dropdown'); // portal dropdown

function mcsp_activate(){
    require_once(__DIR__.'/install.php');
    mcsp_run_migrations();
}

function mcsp_deactivate(){}

function mcsp_admin_init()
{
    $CI = &get_instance();

    // Real named tab in Customer profile (if available)
    if (method_exists($CI->app_tabs, 'add_customer_profile_tab')) {
        $CI->app_tabs->add_customer_profile_tab('multi_contact_sites_pro', [
            'name'     => _l('mcsp_tab_title'),
            'icon'     => 'fa fa-link',
            'view'     => __DIR__.'/views/admin_tab.php',
            'position' => 45,
        ]);
    } else {
        // Fallback injection for older builds
        hooks()->add_action('customer_profile_tab', function($client_id){
            echo view(__DIR__.'/views/admin_tab', ['client_id' => $client_id]);
        }, 120);
    }

    // Settings tab (info)
    if (isset($CI->app_tabs)) {
        $CI->app_tabs->add_settings_tab('mcsp-settings', [
            'name'     => _l('mcsp_settings_tab'),
            'view'     => __DIR__.'/views/settings.php',
            'position' => 999,
        ]);
    }
}

function mcsp_customers_init()
{
    $CI = &get_instance();
    $CI->load->helper('multi_contact_sites_pro/mcsp');
}

// JS injection: rebind admin modal validator to our endpoint
function mcsp_inject_admin_js()
{
    $CI = &get_instance();
    $uri = $CI->uri->uri_string();
    if (strpos($uri, 'admin/clients/client') === false) return;
    $ajaxUrl = admin_url('mcsp_misc/contact_email_exists');

    echo '<script>
    (function(){
      var rebind = function(){
        var $form = $("#contact-form");
        if(!$form.length){return;}
        if (typeof appValidateForm !== "function") { return; }
        try{$form.removeData("validator");}catch(e){}
        appValidateForm($form, {
          firstname: "required",
          lastname: "required",
          email: {
            required: true,
            email: true,
            remote: {
              url: "'.$ajaxUrl.'",
              type: "post",
              data: {
                email: function(){ return $form.find("input[name=email]").val(); },
                exclude: function(){ return $form.find("input[name=contactid]").val(); },
                customer_id: function(){ return $form.find("input[name=userid]").val(); }
              }
            }
          }
        });
      };
      $(document).on("shown.bs.modal", "#contact", rebind);
      $(document).ready(rebind);
    })();
    </script>';
}

// JS injection: rebind portal contact add/edit (if your portal allows contacts CRUD)
function mcsp_inject_portal_js()
{
    if (!is_client_logged_in()) return;
    $ajaxUrl = site_url('mcsp_public/contact_email_exists');
    echo '<script>
    (function(){
      var $form = $("#contact-form, form#contact-form");
      if(!$form.length){return;}
      if (typeof appValidateForm !== "function") { return; }
      try{$form.removeData("validator");}catch(e){}
      appValidateForm($form, {
        email: {
          required: true,
          email: true,
          remote: {
            url: "'.$ajaxUrl.'",
            type: "post",
            data: {
              email: function(){ return $form.find("input[name=email]").val(); },
              exclude: function(){ return $form.find("input[name=contactid]").val(); },
              customer_id: function(){ return $form.find("input[name=userid],input[name=customer_id]").val(); }
            }
          }
        }
      });
    })();
    </script>';
}

// Portal site switcher dropdown
function mcsp_portal_switcher_dropdown()
{
    if (!is_client_logged_in()) return;
    $contact_id = get_contact_user_id();
    $sites = mcsp_get_linked_sites($contact_id);
    if (!$sites) return;
    echo view(__DIR__.'/views/portal_switcher', ['sites' => $sites]);
}
