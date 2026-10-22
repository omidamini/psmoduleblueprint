# PrestaShop Module Blueprint

![PrestaShop](https://img.shields.io/badge/PrestaShop-9.0+-blue.svg)
![PHP](https://img.shields.io/badge/PHP-8.1+-purple.svg)
![License](https://img.shields.io/badge/license-OSL3.0-red.svg)

## Overview

**psmoduleblueprint** is a PrestaShop 9 module blueprint. It is a starting point for a custom module: PSR-4 classes, a Symfony service container, an admin configuration page, and install logic for hooks, the database, and a back-office tab.

Version 1.1.0 tightens the boundaries around input, files, SQL, and admin access. The admin controller uses the PrestaShop 9 `PrestaShopAdminController` and the `AdminSecurity` attribute.

## Features

- Admin configuration page protected by the employee tab permission
- Symfony forms with CSRF protection
- Hook classes created only when they extend the hook base class
- Installer that registers hooks, the configuration table, and the admin tab, and rolls back if a step fails
- Configuration repository and a module-scoped entity repository
- HTML sanitizing through PrestaShop's purifier, and image uploads limited to real images
- Logs written to PrestaShop's `var/logs` directory
- Twig and Smarty rendering that escapes output
- Rename script for starting a new module from this blueprint

## Module structure

```
psmoduleblueprint/
├── config/
│   ├── services.yml                 # Logger and shared services
│   ├── routes.yml
│   ├── admin/                       # Admin services and controller
│   └── front/                       # Front container (no admin services)
├── controllers/admin/               # Legacy tab controller, redirects to Symfony
├── src/
│   ├── Config/
│   ├── Controller/                  # Admin configuration controller
│   ├── Entity/                      # Module configuration entity only
│   ├── Factory/                     # Hook factory and installer composition
│   ├── Form/
│   ├── Hook/
│   ├── Install/
│   │   ├── Database/                # Schema strategy and query executor
│   │   ├── Hook/
│   │   └── Tabmenu/
│   ├── Interface/
│   └── Service/                     # Form, config, repository, sanitizer, uploader, logger
├── views/templates/admin/
├── views/img/                       # Upload target; PHP execution is denied
├── composer.json
├── psmoduleblueprint.php
└── rename_module.php                # CLI only
```

## Requirements

- PrestaShop 9.0.0 through 9.99.99
- PHP 8.1 or newer
- Composer

## Installation

1. Copy the module into `modules/psmoduleblueprint`.
2. Install Composer dependencies from that directory: `composer install`.
3. In the back office, open **Modules > Module Manager**, find **ps module blueprint**, and install it.

Installation registers the `actionFrontControllerSetMedia` hook, creates `ps_psmoduleblueprint_conf`, and adds an admin tab under **Configure**. If any step fails, the module removes the tab, the table, and the hooks, then uninstalls.

After pulling these changes into a shop where the module is already installed, clear the cache:

```bash
php bin/console cache:clear
```

Reinstall the module if the configuration table was created by an older copy and must gain the unique key on `name`.

## Configuration

**Modules > Module Manager > ps module blueprint > Configure** opens the Symfony page. The same page is available from the **ps module blueprint** tab under Configure.

The route is limited to employees who can read `AdminPsmoduleblueprintConfig`. The controller declares that with the `AdminSecurity` attribute. The search form uses the Symfony CSRF token and accepts a short plain-text query.

A new action that changes data should use the update permission:

```php
#[AdminSecurity("is_granted('update', request.get('_legacy_controller'))", message: 'Access denied.', redirectRoute: 'admin_homepage')]
```

## Design

- **Factory Method.** `HookFactory` builds a hook only when the class extends `Hook`. It does not instantiate an arbitrary class name.
- **Composition root.** `InstallFactory` builds the installer with the hook list, schema executor, and tab manager. It does not accept a class name from the caller.
- **Strategy.** `CreateSchema` and `DropSchema` implement `SchemaInterface`. `QueryManager` runs one schema statement at a time and restores foreign key checks if a statement fails.
- **Repository.** `ConfigService` reads and writes configuration rows. `ModuleEntityService` can query only classes in this module's `Entity` namespace.
- **Single responsibility.** `FormService` handles the form workflow. `HtmlSanitizer` purifies HTML. `SecureFileUploader` stores images.

## Adding a hook

1. Create a class in `src/Hook/` that extends `Hook` and implement `run()`.
2. Add the hook name to `src/Install/Hook/HooksList.php`. The name must be letters and digits only.
3. Add the module method and pass the hook parameters through `setParams()`:

```php
public function hookDisplayFooter(array $params)
{
    return $this->hookFactory()
        ->create(DisplayFooterHook::class)
        ->setParams($params)
        ->run();
}
```

For a Smarty template, pass only the file name, for example `footer.tpl`. The file is resolved from `views/templates/hook/`. For Twig, pass `footer.html.twig`. Do not build the template name from a request parameter.

A new hook is registered on the next install. On an existing shop, register it from the module or reinstall.

## Adding a service

1. Create the class in `src/Service/`.
2. Register it in `config/services.yml` when both the front office and the back office need it. Register admin-only services in `config/admin/service/default.yml`.
3. Inject the class through the constructor. The container autowires it.

`config/front/services.yml` is loaded on the storefront. Do not put admin form or upload services there.

## Database

- Add a table in `src/Install/Database/CreateSchema.php`.
- `DropSchema` drops those tables on uninstall. Table names must be letters, digits, and underscores.
- `QueryManager` accepts `CREATE`, `ALTER`, `DROP`, `TRUNCATE`, and `RENAME` statements only, and it rejects more than one statement per call.
- Map a module entity with `@ORM\Table(name="psmoduleblueprint_conf")` (use your own table name). Do not map PrestaShop core tables such as `shop` or `lang`.

`ConfigService::set()` accepts a name of up to 64 characters (`[A-Za-z0-9_]`) and a value of up to 255 characters.

## Security practices in this blueprint

- The configuration route sets `_legacy_controller` to `AdminPsmoduleblueprintConfig`. The controller requires the read permission for that tab. A save action that you add later should require the update permission.
- Twig output is escaped. Do not add the `raw` filter for request data, configuration values, or labels.
- Text areas saved by `FormService` go through `Tools::purifyHTML()`.
- File fields accept JPEG, PNG, GIF, and WebP images up to 2 MB. The stored name is random. `views/img/.htaccess` refuses to execute scripts from that directory.
- `FormService` persists only objects from this module's entity namespace, and it redirects only to a relative shop path or to this shop's host.
- Install and runtime errors shown in the back office are short. The detailed message is written to `var/logs/psmoduleblueprint.log`.
- The module `.htaccess` denies web access to YAML, Twig, JSON, logs, Composer files, and `rename_module.php`. On nginx, deny those files in the server configuration as well. `rename_module.php` also exits unless it is run from the CLI.
- The rename script accepts a module name made of letters, digits, and underscores, and a display name without quotes or path characters. It will not rename or rewrite a file outside the module directory.

## Rename script

From a copy of this module:

```bash
php rename_module.php
```

Enter the technical name, for example `my_new_module`, then the display name, for example `My New Module`. The script rewrites the supported name formats and asks for confirmation before writing. `README.md` is left unchanged. Rename the module directory to the technical name, run `composer dump-autoload`, and review the result before installing.

## Composer

| Command | Description |
|---------|-------------|
| `composer install` | Install dependencies |
| `composer update` | Update dependencies |
| `composer dump-autoload` | Regenerate the PSR-4 autoloader |

## Files to review before using this as a base

- `psmoduleblueprint.php`
- `composer.json`
- `config/services.yml` and `config/admin/service/default.yml`
- `config/routes/admin/config/_config.yml`
- `src/Install/Hook/HooksList.php`
- `src/Install/Tabmenu/MenusList.php`
- `src/Install/Database/CreateSchema.php`

## Author

**Omid AMINI**

- LinkedIn: [omid-amini](https://www.linkedin.com/in/omid-amini/)
- Email: bcs.omid@gmail.com

## License

OSL 3.0 (Open Software License 3.0)

## Troubleshooting

- **Class not found.** Run `composer dump-autoload`, then clear the PrestaShop cache.
- **Module missing from the list.** The directory name and the main class name must match. PrestaShop's technical name is lowercase.
- **Service not found.** Admin services are registered in `config/admin/`. Front services are registered in `config/front/`. Shared services are registered in `config/services.yml`.
- **Access denied on Configure.** The employee profile needs access to the `AdminPsmoduleblueprintConfig` tab.
- **Database error on install.** Read `var/logs/psmoduleblueprint.log`. The back office shows a short message only.

## Resources

- [PrestaShop Developer Documentation](https://devdocs.prestashop.com/)
- [Symfony service container](https://symfony.com/doc/current/service_container.html)
- [PSR-4 autoloading](https://www.php-fig.org/psr/psr-4/)

---

**Version**: 1.1.0
**Compatible with**: PrestaShop 9.0.0–9.99.99
**Last updated**: October 2026
