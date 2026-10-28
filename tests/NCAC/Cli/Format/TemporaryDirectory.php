<?php declare(strict_types=1);

namespace NCAC\Tests\Cli\Format;

use FilesystemIterator;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use RuntimeException;
use SplFileInfo;

/**
 * Self-deleting temporary directory for filesystem-based tests.
 */
final class TemporaryDirectory {

  public readonly string $path;

  public function __construct() {
    $path = sys_get_temp_dir() . \DIRECTORY_SEPARATOR . 'ncac-format-test-' . bin2hex(random_bytes(6));
    if (!mkdir($path, 0777, true)) {
      throw new RuntimeException("Unable to create {$path}");
    }
    $this->path = (string) realpath($path);
  }

  /**
   * Writes a file, creating parent directories, and returns its absolute path.
   */
  public function write(string $relative_path, string $content = ''): string {
    $path = $this->path . \DIRECTORY_SEPARATOR . $relative_path;
    if (!is_dir(\dirname($path))) {
      mkdir(\dirname($path), 0777, true);
    }
    file_put_contents($path, $content);
    return $path;
  }

  public function mkdir(string $relative_path): string {
    $path = $this->path . \DIRECTORY_SEPARATOR . $relative_path;
    mkdir($path, 0777, true);
    return $path;
  }

  public function remove(): void {
    $iterator = new RecursiveIteratorIterator(
      new RecursiveDirectoryIterator($this->path, FilesystemIterator::SKIP_DOTS),
      RecursiveIteratorIterator::CHILD_FIRST
    );
    /** @var SplFileInfo $file */
    foreach ($iterator as $file) {
      $file->isDir() ? rmdir($file->getPathname()) : unlink($file->getPathname());
    }
    rmdir($this->path);
  }

}
