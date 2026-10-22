<?php
/**
 * @author Omid AMINI
 * @link https://www.linkedin.com/in/omid-amini/
 * @license OSL 3.0
 */
declare(strict_types=1);

namespace Blueprint\Module\Psmoduleblueprint\Service;

use Blueprint\Module\Psmoduleblueprint\Config\ModuleConfig;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Stores an uploaded image under the module img directory.
 * Only real images with an allowed extension are accepted.
 */
final class SecureFileUploader
{
    private const ALLOWED_EXTENSIONS = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    private const ALLOWED_MIME_TYPES = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    private const MAX_BYTES = 2097152;

    public function store(UploadedFile $file): string
    {
        if (!$file->isValid() || $file->getSize() > self::MAX_BYTES) {
            throw new \InvalidArgumentException('The uploaded file is not valid.');
        }

        $extension = strtolower((string) $file->guessExtension());
        $clientExtension = strtolower($file->getClientOriginalExtension());
        $mimeType = strtolower((string) $file->getMimeType());

        if (
            !in_array($extension, self::ALLOWED_EXTENSIONS, true)
            || !in_array($clientExtension, self::ALLOWED_EXTENSIONS, true)
            || !in_array($mimeType, self::ALLOWED_MIME_TYPES, true)
        ) {
            throw new \InvalidArgumentException('This file type is not allowed.');
        }

        if (@getimagesize($file->getPathname()) === false) {
            throw new \InvalidArgumentException('The uploaded file is not a valid image.');
        }

        $directory = ModuleConfig::IMG_DIR;
        if (!is_dir($directory) && !@mkdir($directory, 0755, true) && !is_dir($directory)) {
            throw new \RuntimeException('The upload directory is not available.');
        }

        $filename = bin2hex(random_bytes(16)) . '.' . $extension;
        $file->move($directory, $filename);

        return $filename;
    }
}
