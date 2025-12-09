<?php
defined('BASEPATH') or exit('No direct script access allowed');

function mcsp_run_migrations()
{
    $CI = &get_instance();
    $contacts = db_prefix().'contacts';

    // Drop unique index solely on email if present
    $indexes = $CI->db->query("SHOW INDEX FROM `{$contacts}`")->result_array();
    $byIndex = [];
    foreach ($indexes as $ix) {
        $byIndex[$ix['Key_name']][] = ['col'=>$ix['Column_name'], 'non_unique'=>$ix['Non_unique']];
    }
    foreach ($byIndex as $name => $cols) {
        $isUnique = true;
        $onlyEmail = (count($cols) === 1 && strtolower($cols[0]['col']) === 'email');
        foreach ($cols as $c) { if ((int)$c['non_unique'] == 1) { $isUnique = false; break; } }
        if ($isUnique && $onlyEmail) {
            $CI->db->query("ALTER TABLE `{$contacts}` DROP INDEX `{$name}`");
        }
    }

    // Add composite unique (userid,email) if missing
    $existsUx = $CI->db->query("
        SELECT COUNT(1) AS c FROM INFORMATION_SCHEMA.STATISTICS
        WHERE TABLE_SCHEMA = DATABASE()
          AND TABLE_NAME = '{$contacts}'
          AND INDEX_NAME = 'ux_contacts_userid_email'
    ")->row();
    $existsUx = $existsUx ? (int)$existsUx->c : 0;
    if (!$existsUx) {
        $CI->db->query("ALTER TABLE `{$contacts}` ADD UNIQUE `ux_contacts_userid_email` (`userid`,`email`)");
    }

    // Ensure link table exists
    $CI->db->query("CREATE TABLE IF NOT EXISTS `".db_prefix()."mcsp_contact_sites` (
      `contact_id` INT UNSIGNED NOT NULL,
      `customer_id` INT UNSIGNED NOT NULL,
      `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
      PRIMARY KEY (`contact_id`,`customer_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
}
