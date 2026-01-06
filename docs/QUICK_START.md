# Quick Start - NCAC Format Command

## Installation (1 line)

```bash
composer require --dev ncac/phpcs-standard
```

## Usage (1 line)

```bash
vendor/bin/ncac-format src/
```

## Recommended Setup (30 seconds)

Add to your `composer.json`:

```json
{
  "scripts": {
    "format": "ncac-format",
    "format:dry": "ncac-format --dry-run",
    "check": "phpcs --standard=NCAC"
  }
}
```

Then use:

```bash
composer format       # Format automatically
composer format:dry   # Preview changes
composer check        # Check code
```

## That's it! 🎉

For more details, see:
- [Complete Installation Guide](./INSTALLATION_GUIDE.md)
- [Command Reference](./NCAC_FORMAT_COMMAND.md)
- [Main README](../README.md)
