<?php declare(strict_types=1);

namespace NCAC\Tests\Cli\Format;

use NCAC\Cli\Format\ProjectContext;
use PHPUnit\Framework\TestCase;

final class ProjectContextTest extends TestCase {

  private const PACKAGE_DIRECTORY = '/opt/package';

  private TemporaryDirectory $directory;

  public function testProjectRootIsTheClosestDirectoryContainingComposerJson(): void {
    $this->directory->write('composer.json', '{}');
    $working_directory = $this->directory->mkdir('web/modules/custom');
    $context = ProjectContext::resolve($working_directory, self::PACKAGE_DIRECTORY);
    $this->assertSame($this->directory->path, $context->root);
  }

  public function testWorkingDirectoryIsTheRootWithoutComposerJson(): void {
    $working_directory = $this->directory->mkdir('nowhere');
    $context = ProjectContext::resolve($working_directory, self::PACKAGE_DIRECTORY);
    $this->assertSame($working_directory, $context->root);
  }

  public function testDefaultsWithoutProjectConfiguration(): void {
    $this->directory->write('composer.json', '{}');
    $context = ProjectContext::resolve($this->directory->path, self::PACKAGE_DIRECTORY);
    $this->assertNull($context->phpcsConfig);
    $this->assertSame('NCAC', $context->phpcsStandard());
    $this->assertSame(self::PACKAGE_DIRECTORY . '/.php-cs-fixer.dist.php', $context->fixerConfig);
    $this->assertFalse($context->fixerConfigFromProject);
  }

  public function testPhpcsConfigurationFollowsPhpcsLookupOrder(): void {
    $this->directory->write('composer.json', '{}');
    $expected_order = ['.phpcs.xml', 'phpcs.xml', '.phpcs.xml.dist', 'phpcs.xml.dist'];
    foreach (array_reverse($expected_order) as $file) {
      $this->directory->write($file, '<ruleset/>');
      $context = ProjectContext::resolve($this->directory->path, self::PACKAGE_DIRECTORY);
      $this->assertSame($this->directory->path . '/' . $file, $context->phpcsConfig);
      $this->assertSame($this->directory->path . '/' . $file, $context->phpcsStandard());
    }
  }

  public function testFixerConfigurationPrefersTheLocalFile(): void {
    $this->directory->write('composer.json', '{}');
    $this->directory->write('.php-cs-fixer.dist.php', '<?php');
    $context = ProjectContext::resolve($this->directory->path, self::PACKAGE_DIRECTORY);
    $this->assertSame($this->directory->path . '/.php-cs-fixer.dist.php', $context->fixerConfig);
    $this->assertTrue($context->fixerConfigFromProject);

    $this->directory->write('.php-cs-fixer.php', '<?php');
    $context = ProjectContext::resolve($this->directory->path, self::PACKAGE_DIRECTORY);
    $this->assertSame($this->directory->path . '/.php-cs-fixer.php', $context->fixerConfig);
  }

  public function testConfigurationIsOnlyLookedUpInTheProjectRoot(): void {
    $this->directory->write('composer.json', '{}');
    $this->directory->write('web/phpcs.xml', '<ruleset/>');
    $context = ProjectContext::resolve($this->directory->path . '/web', self::PACKAGE_DIRECTORY);
    $this->assertNull($context->phpcsConfig);
  }

  public function testDisplayPath(): void {
    $context = new ProjectContext('/project', null, '/fixer.php', false);
    $this->assertSame('.', $context->displayPath('/project'));
    $this->assertSame('web/modules', $context->displayPath('/project/web/modules'));
    $this->assertSame('/project-other/x', $context->displayPath('/project-other/x'));
  }

  protected function setUp(): void {
    $this->directory = new TemporaryDirectory();
  }

  protected function tearDown(): void {
    $this->directory->remove();
  }

}
