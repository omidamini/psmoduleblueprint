#!/usr/bin/env php
<?php
/**
 * Module Renaming Script
 * 
 * Renames psmoduleblueprint to a new module name while preserving different naming conventions.
 * Supports multiple case formats: PascalCase, camelCase, snake_case, kebab-case, etc.
 * 
 * Usage:
 *   php rename_module.php [directory_path]
 * 
 * @author  PrestaShop Developer
 * @version 2.0.0
 * @license MIT
 */
/**
 *Features
 * 14 case formats supported
 * Exclusion of the vendor folder
 * Renaming of files/folders
 * Overwriting within content
 * Display of statistics
 * User confirmation before execution
 */

declare(strict_types=1);

// ============================================================================
// CONSOLE OUTPUT HELPERS
// ============================================================================

/**
 * Console color formatter for terminal output
 */
final class ConsoleColor
{
    private const COLOR_CYAN = "\033[0;36m";
    private const COLOR_GREEN = "\033[0;32m";
    private const COLOR_YELLOW = "\033[0;33m";
    private const COLOR_RED = "\033[0;31m";
    private const COLOR_RESET = "\033[0m";
    
    public static function info(string $text): string
    {
        return self::COLOR_CYAN . $text . self::COLOR_RESET;
    }
    
    public static function success(string $text): string
    {
        return self::COLOR_GREEN . $text . self::COLOR_RESET;
    }
    
    public static function warning(string $text): string
    {
        return self::COLOR_YELLOW . $text . self::COLOR_RESET;
    }
    
    public static function error(string $text): string
    {
        return self::COLOR_RED . $text . self::COLOR_RESET;
    }
}

// ============================================================================
// CASE CONVERTER
// ============================================================================

/**
 * Converts strings between different naming conventions
 */
final class CaseConverter
{
    /**
     * Convert to PascalCase (ModuleBluePrint)
     */
    public static function toPascalCase(string $str): string
    {
        return str_replace(' ', '', ucwords(str_replace('_', ' ', $str)));
    }
    
    /**
     * Convert to camelCase (moduleBluePrint)
     */
    public static function toCamelCase(string $str): string
    {
        return lcfirst(self::toPascalCase($str));
    }
    
    /**
     * Convert to snake_case (itis_module_blueprint)
     */
    public static function toSnakeCase(string $str): string
    {
        return strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $str));
    }
    
    /**
     * Convert to lowercase without separators (moduleblueprint)
     */
    public static function toLowerCase(string $str): string
    {
        return strtolower(str_replace('_', '', $str));
    }
    
    /**
     * Convert to UPPER_SNAKE_CASE (ITIS_MODULE_BLUEPRINT)
     */
    public static function toUpperSnakeCase(string $str): string
    {
        return strtoupper(self::toSnakeCase($str));
    }
    
    /**
     * Convert to kebab-case (module-blueprint)
     */
    public static function toKebabCase(string $str): string
    {
        return str_replace('_', '-', self::toSnakeCase($str));
    }
    
    /**
     * Convert to space separated lowercase (module blueprint)
     */
    public static function toSpaceLowerCase(string $str): string
    {
        return str_replace('_', ' ', self::toSnakeCase($str));
    }
    
    /**
     * Convert to Title Case With Spaces (Module Blue Print)
     */
    public static function toTitleCase(string $str): string
    {
        return ucwords(str_replace('_', ' ', self::toSnakeCase($str)));
    }
    
    /**
     * Convert to Sentence case (Module blueprint)
     */
    public static function toSentenceCase(string $str): string
    {
        return ucfirst(self::toSpaceLowerCase($str));
    }
    
    /**
     * Convert to UPPER CASE WITH SPACES (MODULE BLUEPRINT)
     */
    public static function toUpperSpaceCase(string $str): string
    {
        return strtoupper(str_replace('_', ' ', self::toSnakeCase($str)));
    }
    
    /**
     * Convert to slash separated Title Case (Module/Blue/Print)
     */
    public static function toSlashTitleCase(string $str): string
    {
        $words = explode('_', self::toSnakeCase($str));
        $words = array_map('ucfirst', $words);
        return implode('/', $words);
    }
    
    /**
     * Convert to slash with PascalCase (Module/BluePrint)
     */
    public static function toSlashPascalCase(string $str): string
    {
        $snake = self::toSnakeCase($str);
        $parts = explode('_', $snake);
        
        if (count($parts) === 0) {
            return self::toPascalCase($str);
        }
        
        $first = ucfirst(array_shift($parts));
        $rest = implode('', array_map('ucfirst', $parts));
        
        return $rest ? $first . '/' . $rest : $first;
    }
    
    /**
     * Convert to slash lowercase (module/blueprint)
     */
    public static function toSlashLowerCase(string $str): string
    {
        $words = explode('_', self::toSnakeCase($str));
        return implode('/', $words);
    }
    
    /**
     * Convert to slash with first word capitalized (Module/blueprint)
     */
    public static function toSlashSentenceCase(string $str): string
    {
        $words = explode('_', self::toSnakeCase($str));
        
        if (count($words) > 0) {
            $words[0] = ucfirst($words[0]);
        }
        
        return implode('/', $words);
    }
}

// ============================================================================
// FILE SYSTEM UTILITIES
// ============================================================================

/**
 * File system helper for scanning and filtering files
 */
final class FileSystemHelper
{
    private const DEFAULT_EXCLUDED_DIRS = ['vendor', 'node_modules', '.git', '.svn'];
    private const EXCLUDED_FILES = ['README.md'];
    private const TEXT_MIME_TYPES = ['application/json', 'application/xml', 'application/javascript'];
    
    /**
     * Get all files recursively from a directory
     * 
     * @param string   $directory      Directory to scan
     * @param string[] $excludedDirs   Directories to exclude
     * @return string[]                List of file paths
     */
    public static function getFilesRecursively(string $directory, array $excludedDirs = self::DEFAULT_EXCLUDED_DIRS): array
    {
        if (!is_dir($directory)) {
            return [];
        }
        
        $files = [];
        $items = scandir($directory);
        
        foreach ($items as $item) {
            if ($item === '.' || $item === '..') {
                continue;
            }
            
            $path = $directory . DIRECTORY_SEPARATOR . $item;
            
            if (is_dir($path)) {
                if (!self::isExcludedDirectory($path, $excludedDirs)) {
                    $files = array_merge($files, self::getFilesRecursively($path, $excludedDirs));
                }
            } else {
                if (!self::isExcludedFile($item)) {
                    $files[] = $path;
                }
            }
        }
        
        return $files;
    }
    
    /**
     * Check if a directory should be excluded
     */
    private static function isExcludedDirectory(string $path, array $excludedDirs): bool
    {
        $basename = basename($path);
        
        foreach ($excludedDirs as $excludeDir) {
            if ($basename === $excludeDir || strpos($path, DIRECTORY_SEPARATOR . $excludeDir . DIRECTORY_SEPARATOR) !== false) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Check if a file should be excluded
     */
    private static function isExcludedFile(string $filename): bool
    {
        return in_array($filename, self::EXCLUDED_FILES, true);
    }
    
    /**
     * Check if file is a text file (not binary)
     */
    public static function isTextFile(string $filePath): bool
    {
        if (!is_file($filePath) || !is_readable($filePath)) {
            return false;
        }
        
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $filePath);
        finfo_close($finfo);
        
        return strpos($mimeType, 'text') !== false || in_array($mimeType, self::TEXT_MIME_TYPES, true);
    }
    
    /**
     * Sort files by depth (deepest first)
     */
    public static function sortByDepth(array $files): array
    {
        usort($files, function($a, $b) {
            return substr_count($b, DIRECTORY_SEPARATOR) - substr_count($a, DIRECTORY_SEPARATOR);
        });
        
        return $files;
    }
}

// ============================================================================
// MODULE RENAMER
// ============================================================================

/**
 * Statistics tracker for renaming operations
 */
final class RenameStatistics
{
    private int $filesRenamed = 0;
    private int $filesModified = 0;
    private int $totalReplacements = 0;
    private string $newName = '';
    public function incrementFilesRenamed(): void
    {
        $this->filesRenamed++;
    }
    
    public function incrementFilesModified(): void
    {
        $this->filesModified++;
    }
    
    public function addReplacements(int $count): void
    {
        $this->totalReplacements += $count;
    }
    
    public function getFilesRenamed(): int
    {
        return $this->filesRenamed;
    }
    
    public function getFilesModified(): int
    {
        return $this->filesModified;
    }
    
    public function getTotalReplacements(): int
    {
        return $this->totalReplacements;
    }
    public function setNewName(string $newName): void
    {
        $this->newName = $newName;
    }
    public function getNewName(): string
    {
        return $this->newName;
    }
    
    public function display(): void
    {
        echo "\n" . ConsoleColor::success("=== Summary ===\n");
        echo ConsoleColor::success("Files renamed: ") . $this->filesRenamed . "\n";
        echo ConsoleColor::success("Files modified: ") . $this->filesModified . "\n";
        echo ConsoleColor::success("Total replacements: ") . $this->totalReplacements . "\n";
        echo "\n" . ConsoleColor::success("✓ Module renaming completed successfully!\n");
        echo ConsoleColor::success("Please rename module root directory to match the new module name: ". $this->newName . ", review the changes, and test your module thoroughly.\n");
    }
}

/**
 * Main module renaming orchestrator
 */
final class ModuleRenamer
{
    private const DEFAULT_OLD_NAME = 'psmoduleblueprint';
    private const DEFAULT_OLD_DISPLAY_NAME = 'p_s _module _blueprint';
    private const CONFIRMATION_KEYWORDS = ['yes', 'y'];
    
    private string $oldName;
    private string $newName;
    private string $oldDisplayName;
    private string $newDisplayName;
    private string $baseDirectory;
    private array $excludedDirectories;
    private RenameStatistics $statistics;
    
    public function __construct(string $baseDirectory = '.', array $excludedDirs = ['vendor', 'node_modules', '.git'])
    {
        $this->baseDirectory = realpath($baseDirectory);
        $this->excludedDirectories = $excludedDirs;
        $this->oldName = self::DEFAULT_OLD_NAME;
        $this->oldDisplayName = self::DEFAULT_OLD_DISPLAY_NAME;
        $this->statistics = new RenameStatistics();
    }
    
    /**
     * Execute the full renaming workflow
     */
    public function execute(): void
    {
        $this->displayHeader();
        
        if (!$this->promptForModuleName()) {
            return;
        }
        
        if (!$this->confirmOperation()) {
            echo ConsoleColor::info("Operation cancelled.\n");
            return;
        }
        
        $this->performRenaming();
        $this->statistics->setNewName($this->newName);
        $this->statistics->display();
    }
    
    /**
     * Display script header
     */
    private function displayHeader(): void
    {
        echo ConsoleColor::info("╔═══════════════════════════════════════════╗\n");
        echo ConsoleColor::info("║     Module Blueprint Renaming Script     ║\n");
        echo ConsoleColor::info("╚═══════════════════════════════════════════╝\n\n");
    }
    
    /**
     * Prompt user for new module name
     */
    private function promptForModuleName(): bool
    {
        echo ConsoleColor::info("Enter the new module name (e.g., my_new_module): ");
        $input = trim(fgets(STDIN));
        
        if (empty($input)) {
            echo ConsoleColor::error("Module name cannot be empty!\n");
            return false;
        }
        
        $this->newName = CaseConverter::toSnakeCase($input);
        
        echo ConsoleColor::info("Enter the new display name (e.g., My New Module): ");
        $displayInput = trim(fgets(STDIN));
        
        if (empty($displayInput)) {
            echo ConsoleColor::error("Display name cannot be empty!\n");
            return false;
        }
        
        $this->newDisplayName = $displayInput;
        $this->displayConversionPreview();
        
        return true;
    }
    
    /**
     * Display conversion preview
     */
    private function displayConversionPreview(): void
    {
        echo "\n" . ConsoleColor::info("Old module name: ") . $this->oldName . "\n";
        echo ConsoleColor::info("New module name: ") . $this->newName . "\n";
        echo ConsoleColor::info("Old display name: ") . $this->oldDisplayName . "\n";
        echo ConsoleColor::info("New display name: ") . $this->newDisplayName . "\n\n";
        
        echo ConsoleColor::info("The following formats will be replaced:\n");
        
        $variations = $this->getCaseVariations();
        foreach ($variations as $label => $variation) {
            $padding = str_repeat(' ', 30 - strlen($variation['old']));
            echo "  - {$variation['old']}{$padding}→ {$variation['new']}\n";
        }
        
        echo "\n";
    }
    
    /**
     * Confirm operation with user
     */
    private function confirmOperation(): bool
    {
        echo ConsoleColor::warning("Do you want to continue? (yes/no): ");
        $confirm = strtolower(trim(fgets(STDIN)));
        
        return in_array($confirm, self::CONFIRMATION_KEYWORDS, true);
    }
    
    /**
     * Get all case variations for search and replace
     */
    private function getCaseVariations(): array
    {
        $variations = [
            'snake_case' => [
                'old' => CaseConverter::toSnakeCase($this->oldName),
                'new' => CaseConverter::toSnakeCase($this->newName)
            ],
            'PascalCase' => [
                'old' => CaseConverter::toPascalCase($this->oldName),
                'new' => CaseConverter::toPascalCase($this->newName)
            ],
            'camelCase' => [
                'old' => CaseConverter::toCamelCase($this->oldName),
                'new' => CaseConverter::toCamelCase($this->newName)
            ],
            'lowercase' => [
                'old' => CaseConverter::toLowerCase($this->oldName),
                'new' => CaseConverter::toLowerCase($this->newName)
            ],
            'UPPER_SNAKE_CASE' => [
                'old' => CaseConverter::toUpperSnakeCase($this->oldName),
                'new' => CaseConverter::toUpperSnakeCase($this->newName)
            ],
            'kebab-case' => [
                'old' => CaseConverter::toKebabCase($this->oldName),
                'new' => CaseConverter::toKebabCase($this->newName)
            ],
            'space lowercase' => [
                'old' => CaseConverter::toSpaceLowerCase($this->oldName),
                'new' => CaseConverter::toSpaceLowerCase($this->newName)
            ],
            'Title Case' => [
                'old' => CaseConverter::toTitleCase($this->oldName),
                'new' => CaseConverter::toTitleCase($this->newName)
            ],
            'Sentence case' => [
                'old' => CaseConverter::toSentenceCase($this->oldName),
                'new' => CaseConverter::toSentenceCase($this->newName)
            ],
            'UPPER SPACE CASE' => [
                'old' => CaseConverter::toUpperSpaceCase($this->oldName),
                'new' => CaseConverter::toUpperSpaceCase($this->newName)
            ],
            'Slash/Title/Case' => [
                'old' => CaseConverter::toSlashTitleCase($this->oldName),
                'new' => CaseConverter::toSlashTitleCase($this->newName)
            ],
            'Slash/PascalCase' => [
                'old' => CaseConverter::toSlashPascalCase($this->oldName),
                'new' => CaseConverter::toSlashPascalCase($this->newName)
            ],
            'slash/lowercase' => [
                'old' => CaseConverter::toSlashLowerCase($this->oldName),
                'new' => CaseConverter::toSlashLowerCase($this->newName)
            ],
            'Slash/sentencecase' => [
                'old' => CaseConverter::toSlashSentenceCase($this->oldName),
                'new' => CaseConverter::toSlashSentenceCase($this->newName)
            ]
        ];
        
        // Add display name variations
        $displayVariations = [
            'Display snake_case' => [
                'old' => CaseConverter::toSnakeCase($this->oldDisplayName),
                'new' => CaseConverter::toSnakeCase($this->newDisplayName)
            ],
            'Display PascalCase' => [
                'old' => CaseConverter::toPascalCase($this->oldDisplayName),
                'new' => CaseConverter::toPascalCase($this->newDisplayName)
            ],
            'Display camelCase' => [
                'old' => CaseConverter::toCamelCase($this->oldDisplayName),
                'new' => CaseConverter::toCamelCase($this->newDisplayName)
            ],
            'Display lowercase' => [
                'old' => CaseConverter::toLowerCase($this->oldDisplayName),
                'new' => CaseConverter::toLowerCase($this->newDisplayName)
            ],
            'Display UPPER_SNAKE' => [
                'old' => CaseConverter::toUpperSnakeCase($this->oldDisplayName),
                'new' => CaseConverter::toUpperSnakeCase($this->newDisplayName)
            ],
            'Display kebab-case' => [
                'old' => CaseConverter::toKebabCase($this->oldDisplayName),
                'new' => CaseConverter::toKebabCase($this->newDisplayName)
            ],
            'Display space lower' => [
                'old' => CaseConverter::toSpaceLowerCase($this->oldDisplayName),
                'new' => CaseConverter::toSpaceLowerCase($this->newDisplayName)
            ],
            'Display Title Case' => [
                'old' => CaseConverter::toTitleCase($this->oldDisplayName),
                'new' => CaseConverter::toTitleCase($this->newDisplayName)
            ],
            'Display Sentence' => [
                'old' => CaseConverter::toSentenceCase($this->oldDisplayName),
                'new' => CaseConverter::toSentenceCase($this->newDisplayName)
            ],
            'Display UPPER SPACE' => [
                'old' => CaseConverter::toUpperSpaceCase($this->oldDisplayName),
                'new' => CaseConverter::toUpperSpaceCase($this->newDisplayName)
            ],
            'Display Slash/Title' => [
                'old' => CaseConverter::toSlashTitleCase($this->oldDisplayName),
                'new' => CaseConverter::toSlashTitleCase($this->newDisplayName)
            ],
            'Display Slash/Pascal' => [
                'old' => CaseConverter::toSlashPascalCase($this->oldDisplayName),
                'new' => CaseConverter::toSlashPascalCase($this->newDisplayName)
            ],
            'Display slash/lower' => [
                'old' => CaseConverter::toSlashLowerCase($this->oldDisplayName),
                'new' => CaseConverter::toSlashLowerCase($this->newDisplayName)
            ],
            'Display Slash/sentence' => [
                'old' => CaseConverter::toSlashSentenceCase($this->oldDisplayName),
                'new' => CaseConverter::toSlashSentenceCase($this->newDisplayName)
            ]
        ];
        
        return array_merge($variations, $displayVariations);
    }
    
    /**
     * Perform the actual renaming process
     */
    private function performRenaming(): void
    {
        echo "\n" . ConsoleColor::info("Scanning files in: ") . $this->baseDirectory . "\n";
        echo ConsoleColor::info("Excluding directories: ") . implode(', ', $this->excludedDirectories) . "\n\n";
        
        $files = FileSystemHelper::getFilesRecursively($this->baseDirectory, $this->excludedDirectories);
        echo ConsoleColor::info("Found " . count($files) . " files to process.\n\n");
        
        $this->renameFilesAndDirectories($files);
        $this->replaceFileContents($files);
    }
    
    /**
     * Rename files and directories
     */
    private function renameFilesAndDirectories(array &$files): void
    {
        echo ConsoleColor::info("=== Step 1: Renaming files and directories ===\n");
        
        $files = FileSystemHelper::sortByDepth($files);
        $renamedFiles = [];
        
        foreach ($files as $filePath) {
            $newPath = $this->renamePathIfNeeded($filePath);
            $renamedFiles[] = $newPath;
        }
        
        $files = $renamedFiles;
    }
    
    /**
     * Replace content in files
     */
    private function replaceFileContents(array $files): void
    {
        echo "\n" . ConsoleColor::info("=== Step 2: Replacing content in files ===\n");
        
        foreach ($files as $filePath) {
            if (!is_file($filePath)) {
                continue;
            }
            
            if (FileSystemHelper::isTextFile($filePath) && $this->fileContainsOldName($filePath)) {
                $this->replaceInFile($filePath);
            }
        }
    }
    
    /**
     * Rename a file or directory if it contains the old name
     */
    private function renamePathIfNeeded(string $path): string
    {
        $dirname = dirname($path);
        $basename = basename($path);
        
        foreach ($this->getCaseVariations() as $variation) {
            if (strpos($basename, $variation['old']) === false) {
                continue;
            }
            
            $newBasename = str_replace($variation['old'], $variation['new'], $basename);
            $newPath = $dirname . DIRECTORY_SEPARATOR . $newBasename;
            
            if ($this->performRename($path, $newPath, $basename, $newBasename)) {
                return $newPath;
            }
            
            break;
        }
        
        return $path;
    }
    
    /**
     * Execute file/directory rename operation
     */
    private function performRename(string $oldPath, string $newPath, string $oldName, string $newName): bool
    {
        if (rename($oldPath, $newPath)) {
            $this->statistics->incrementFilesRenamed();
            echo ConsoleColor::success("  ✓ Renamed: ") . "$oldName → $newName\n";
            return true;
        }
        
        echo ConsoleColor::error("  ✗ Failed to rename: ") . $oldPath . "\n";
        return false;
    }
    
    /**
     * Check if file contains any variation of the old module name
     */
    private function fileContainsOldName(string $filePath): bool
    {
        $content = @file_get_contents($filePath);
        
        if ($content === false) {
            return false;
        }
        
        foreach ($this->getCaseVariations() as $variation) {
            if (strpos($content, $variation['old']) !== false) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Replace content in a single file
     */
    private function replaceInFile(string $filePath): void
    {
        $content = file_get_contents($filePath);
        $originalContent = $content;
        $fileReplacements = 0;
        
        foreach ($this->getCaseVariations() as $variation) {
            $count = 0;
            $content = str_replace($variation['old'], $variation['new'], $content, $count);
            $fileReplacements += $count;
        }
        
        if ($content === $originalContent) {
            return;
        }
        
        if (file_put_contents($filePath, $content) !== false) {
            $this->statistics->incrementFilesModified();
            $this->statistics->addReplacements($fileReplacements);
            echo ConsoleColor::success("  ✓ Modified: ") . $filePath . " ($fileReplacements replacements)\n";
        } else {
            echo ConsoleColor::error("  ✗ Failed to modify: ") . $filePath . "\n";
        }
    }
}

// ============================================================================
// MAIN EXECUTION
// ============================================================================

/**
 * Validate script is run from CLI
 */
function validateCliEnvironment(): void
{
    if (php_sapi_name() !== 'cli') {
        die("This script must be run from the command line.\n");
    }
}

/**
 * Get and validate directory from command line arguments
 */
function getTargetDirectory(array $argv): string
{
    $directory = $argv[1] ?? getcwd();
    
    if (!is_dir($directory)) {
        echo ConsoleColor::error("Error: Directory not found: $directory\n");
        exit(1);
    }
    
    return $directory;
}

// Execute the script
validateCliEnvironment();
$targetDirectory = getTargetDirectory($argv);

$renamer = new ModuleRenamer($targetDirectory);
$renamer->execute();
