# PrestaShop Module Blueprint

![PrestaShop](https://img.shields.io/badge/PrestaShop-9.0+-blue.svg)
![PHP](https://img.shields.io/badge/PHP-8.0+-purple.svg)
![License](https://img.shields.io/badge/license-OSL3.0-red.svg)

## 📋 Overview

**psmoduleblueprint** is a comprehensive PrestaShop module blueprint/boilerplate designed to accelerate module development. It provides a professional, well-structured foundation with modern PHP practices, PSR-4 autoloading, service container integration, and a complete MVC architecture.

This blueprint is created as a starting point for developing custom PrestaShop modules with best practices.

## ✨ Features

- **Modern Architecture**: Follows MVC pattern with clean separation of concerns
- **PSR-4 Autoloading**: Composer-based autoloading for efficient class management
- **Service Container**: Symfony-based dependency injection
- **Hook Management**: Factory pattern for hook implementation
- **Database Management**: Entity-based with schema creation/dropping utilities
- **Admin Interface**: Pre-configured admin controllers and forms
- **Tab Menu System**: Automatic tab menu registration in PrestaShop back-office
- **Logger Service**: Built-in logging functionality
- **Twig Templates**: Modern templating system
- **Easy Renaming**: Automated script to rename and create new modules
- **Multi-language Support**: Translation-ready structure

## 📁 Module Structure

```
psmoduleblueprint/
├── config/                      # Configuration files
│   ├── services.yml            # Service container definitions
│   ├── routes.yml              # Route configurations
│   ├── admin/                  # Admin-specific configs
│   └── front/                  # Front-end configs
├── controllers/                 # PrestaShop controllers
│   └── admin/                  # Admin controllers
├── src/                        # Source code (PSR-4)
│   ├── Config/                 # Configuration classes
│   ├── Controller/             # Controllers
│   ├── Entity/                 # Entities
│   ├── EventSubscriber/        # Event subscribers
│   ├── Factory/                # Factory classes
│   ├── Form/                   # Form types
│   ├── Hook/                   # Hook implementations
│   ├── Install/                # Installation logic
│   │   ├── Database/           # Database schema management
│   │   ├── Hook/               # Hook registration
│   │   └── Tabmenu/            # Tab menu management
│   ├── Interface/              # Interfaces
│   └── Service/                # Services
├── views/
│   └── templates/              # Twig templates
│       ├── admin/              # Admin templates
│       └── front/              # Front-office templates
├── vendor/                     # Composer dependencies
├── composer.json               # Composer configuration
├── psmoduleblueprint.php       # Main module file
└── rename_module.php           # Module renaming utility
```

## 🚀 Installation

### Prerequisites

- PrestaShop 9.0 or higher
- PHP 8.0 or higher
- Composer

### Method 1: Direct Installation

1. **Download/Clone** the module to your PrestaShop modules directory:

   ```bash
   cd /path/to/prestashop/modules/
   git clone <repository-url> psmoduleblueprint
   ```
2. **Install Composer Dependencies**:

   ```bash
   cd psmoduleblueprint
   composer install
   ```
3. **Regenerate Autoloader** (if you make changes to the structure):

   ```bash
   composer dump-autoload
   ```
4. **Install via PrestaShop Back-Office**:

   - Navigate to **Modules > Module Manager**
   - Search for "ps module blueprint"
   - Click **Install**

### Method 2: Using as a Blueprint

To use this as a template for your own module:

1. **Copy the module directory**:

   ```bash
   cp -r psmoduleblueprint /path/to/prestashop/modules/yournewmodule
   cd /path/to/prestashop/modules/yournewmodule
   ```
2. **Run the rename script**:

   ```bash
   php rename_module.php
   ```

   The script will:

   - Prompt for a new module name
   - Automatically rename files, classes, and namespaces
   - Update all references throughout the codebase
   - Support 14+ different naming conventions (PascalCase, camelCase, snake_case, etc.)
3. **Install dependencies**:

   ```bash
   composer install
   ```
4. **Install the module** in PrestaShop back-office

## 🔧 Configuration

After installation, configure the module:

1. Navigate to **Modules > Module Manager**
2. Find "ps module blueprint" and click **Configure**
3. Access the configuration panel in the admin menu

## 🛠️ Development

### Adding a New Hook

1. Create a hook class in `src/Hook/`:

   ```php
   <?php
   namespace Blueprint\Module\Psmoduleblueprint\Hook;

   class YourCustomHook extends Hook
   {
       public function run(): string
       {
           // Your hook logic
           return $this->module->display($this->module->getPathUri(), 'template.tpl');
       }
   }
   ```
2. Register the hook in `src/Install/Hook/HooksList.php`
3. Add the hook method in `psmoduleblueprint.php`:

   ```php
   public function hookYourHookName($params)
   {
       return (new HookFactory($this, Context::getContext()))
           ->create(YourCustomHook::class)
           ->run();
   }
   ```

### Adding a Service

1. Create your service class in `src/Service/`
2. Register it in `config/services.yml` or `config/admin/services.yml`
3. Use dependency injection in your controllers

### Database Schema

- **Create tables**: Add schema in `src/Install/Database/CreateSchema.php`
- **Drop tables**: Add schema in `src/Install/Database/DropSchema.php`
- Tables are automatically created on install and dropped on uninstall

### After Making Structural Changes

Always regenerate the autoloader:

```bash
composer dump-autoload
```

Or with optimization for production:

```bash
composer dump-autoload -o
```

## 📝 Composer Commands

| Command                       | Description                                 |
| ----------------------------- | ------------------------------------------- |
| `composer install`          | Install all dependencies (first time setup) |
| `composer update`           | Update dependencies to latest versions      |
| `composer dump-autoload`    | Regenerate autoload files                   |
| `composer dump-autoload -o` | Regenerate with optimization (production)   |

## 🔍 Available Hooks

The blueprint includes sample implementations for:

- `actionFrontControllerSetMedia` - Add CSS/JS assets to front-office
- `displayCustomerAccount` - Display content in customer account (commented)
- `getContent` - Module configuration page

You can add more hooks as needed following the Factory pattern.

## 🧪 Testing

The blueprint is test-ready with a dedicated namespace for tests:

```bash
# Add testing dependencies
composer require --dev phpunit/phpunit

# Run tests (once implemented)
vendor/bin/phpunit
```

## 📄 Files to Review Before Using

- `psmoduleblueprint.php` - Main module class
- `composer.json` - Update author and package information
- `config/services.yml` - Service definitions
- `src/Install/Hook/HooksList.php` - Hooks to register
- `src/Install/Tabmenu/MenusList.php` - Admin menu items

## 👤 Author

**Omid AMINI**

- LinkedIn: [omid-amini](https://www.linkedin.com/in/omid-amini/)
- Email: bcs.omid@gmail.com

## 📜 License

OSL 3.0 (Open Software License 3.0) - Copyright © 2026

## 🤝 Contributing

This module blueprint is open source under OSL 3.0 license. Contributions are welcome! For custom development needs or commercial support, contact ITIS COMMERCE.

## 🐛 Troubleshooting

### Autoload errors

Run `composer dump-autoload` to regenerate class mappings.

### Module not appearing

Ensure the module folder name matches the class name (case-sensitive).

### Service not found

Check `config/services.yml` and verify service definitions.

### Database errors

Check `src/Install/Database/` classes for schema definitions.

## 📚 Additional Resources

- [PrestaShop Developer Documentation](https://devdocs.prestashop.com/)
- [Symfony Service Container](https://symfony.com/doc/current/service_container.html)
- [PSR-4 Autoloading](https://www.php-fig.org/psr/psr-4/)

---

**Version**: 1.0.2
**Compatible with**: PrestaShop 9.0+
**Last Updated**: March 2026
