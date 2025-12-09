# Multi Contact Sites PRO (Perfex CRM module)

**Free & open-source** module that lets you use the **same contact email across multiple customers** and gives a **client-portal site switcher** for contacts linked to multiple customers.

## Highlights
- Drops any **global-unique** on `contacts.email` and enforces **UNIQUE (`userid`,`email`)** instead.
- Fixes the **"Email already exists"** validator (Admin & Portal) **without core edits**.
- Adds **Customer profile tab** to link a contact to multiple customers (aka "sites").
- Adds **portal "My Sites"** dropdown + page; auto-creates a sibling contact if needed.
- Safe for **Perfex 3.x**; respects `db_prefix()` and uses hooks only.

## Install
1. Download the latest ZIP from releases or this repo.
2. Upload via **Setup → Modules → Upload** in Perfex.
3. **Activate** the module.
4. Optional: Clear any frontend caches (Cloudflare, etc.) and hard-refresh the Admin page.

On activation the module will:
- Remove any **unique index solely on** `contacts.email`.
- Ensure **composite unique** index `ux_contacts_userid_email (userid,email)` exists.
- Create link table `{db_prefix()}mcsp_contact_sites`.

## Uninstall / Disable
- Deactivate the module from **Setup → Modules** to stop all behavior.
- (Optional) Use the provided **SQL revert script** to fully restore the original unique index approach.

## SQL Revert (manual, optional)
Edit the prefix (`tbl`) to match your installation if needed, then run:

```sql
-- 1) Drop the composite unique (userid,email)
ALTER TABLE `tblcontacts` DROP INDEX `ux_contacts_userid_email`;

-- 2) Re-create a global unique on email (name it as you like)
ALTER TABLE `tblcontacts` ADD UNIQUE `ux_contacts_email` (`email`);

-- 3) (Optional) Drop the link table created by the module
DROP TABLE IF EXISTS `tblmcsp_contact_sites`;
```

> If your prefix isn't `tbl`, replace it (e.g., `app_contacts`, `crm_contacts`).

## How it works (no core edits)
- **Admin contact modal**: The module injects a tiny JS snippet to rebind the jQuery Validate `remote` rule so it calls our endpoint `admin/mcsp_misc/contact_email_exists`, which validates **per-customer** using `userid`.
- **Portal**: Similar rebinding to `mcsp_public/contact_email_exists`.
- **Linking & switching**: Admin can link a contact to more customers; the client portal shows a "My Sites" dropdown. When switching, we create a **sibling** contact record (same email) under the target customer if missing and switch the session to it.

## Compatibility
- Perfex **3.0+** (tested on 3.4.x).
- Custom `db_prefix()` supported.
- Works alongside most modules; conflicts are unlikely unless another module rebinds the same modal validator in a conflicting way.

## Folder structure
```
modules/multi_contact_sites_pro/
├─ multi_contact_sites_pro.php
├─ install.php
├─ controllers/
│  ├─ Mcsp_admin.php
│  ├─ Mcsp_misc.php           (admin AJAX validator)
│  ├─ Mcsp_portal.php         (portal pages & switching)
│  └─ Mcsp_public.php         (portal AJAX validator)
├─ helpers/mcsp_helper.php
├─ language/english/multi_contact_sites_pro_lang.php
└─ views/
   ├─ admin_tab.php
   ├─ portal_sites.php
   ├─ portal_switcher.php
   └─ settings.php
```

## Troubleshooting
- **"Email already exists" still appears**  
  Clear caches and hard-refresh. Ensure there are **no other custom scripts** rebinding the modal validator. The module injects its logic only on `admin/clients/client/*` routes.
- **Composite index already exists**  
  The migration will skip re-adding it; nothing to do.
- **No "Multi Contact Sites PRO" tab**  
  Make sure the module is activated. Check file permissions. Try reloading with Ctrl/Cmd+Shift+R.

## Contributing
PRs welcome! Please keep the module:
- Without core file edits
- Compatible with Perfex 3.x
- Prefix-agnostic and MySQL 5.7+/MariaDB friendly

## License
MIT

© 2025 TechFinity — Enjoy!
