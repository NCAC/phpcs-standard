# Dev Container Setup Guide

This guide explains how to set up the development environment using VS Code Dev Containers, particularly for Windows users with WSL2.

## Prerequisites

- **Windows users**: WSL2 with a Linux distribution installed
- VS Code with the "Dev Containers" extension
- Docker Desktop running in WSL2 mode

## Quick Setup

### 1. Clone the Repository (WSL2 Required)

**Important**: For Windows users, you MUST clone the repository inside WSL2, not in the Windows filesystem.

```bash
# In WSL2 terminal
cd ~
git clone https://github.com/ncac/phpcs-standard.git
cd phpcs-standard
```

### 2. Generate Environment File

Before opening in VS Code, generate the required `.env` file:

```bash
# Run this in WSL2, before starting the Dev Container
.docker/generate-env.sh
```

This script will:

- Detect your WSL2 IP address for XDebug configuration
- Generate a `.env` file with the necessary environment variables
- Validate the configuration

Expected output:

```
🔧 Generating .env file for Dev Container...
🔍 Detecting WSL2 IP address...
✅ Detected WSL2 IP: 192.168.x.x
📝 Writing environment variables to /workspace/.env...
✅ Environment file generated successfully!
🚀 You can now start the Dev Container in VS Code
```

### 3. Open in VS Code

```bash
# From WSL2 terminal, in the project directory
code .
```

VS Code will detect the `.devcontainer.json` and prompt you to reopen in container.

### 4. Post-Setup Verification

Once the Dev Container is running, verify the setup:

```bash
# Test environment
echo $WSL_IP
echo $XDEBUG_PORT

# Run quality checks
vendor/bin/phing check

# Test commit hooks
scripts/test-commit-hooks.sh
```

## Environment Variables

The `.env` file contains these variables:

| Variable      | Description             | Example               |
| ------------- | ----------------------- | --------------------- |
| `APP_NAME`    | Application identifier  | `ncac-phpcs-standard` |
| `PHP_VERSION` | PHP version used        | `7.4.33`              |
| `APP_ROOT`    | Workspace path          | `/workspace`          |
| `WSL_IP`      | WSL2 host IP for XDebug | `192.168.65.2`        |
| `XDEBUG_PORT` | XDebug listening port   | `9047`                |

## XDebug Configuration

The environment setup automatically configures XDebug to work with WSL2:

- **Host**: Uses detected WSL2 IP (`$WSL_IP`)
- **Port**: 9047 (configurable via `$XDEBUG_PORT`)
- **IDE Key**: `VSCODE`

### VS Code XDebug Setup

Add this to your VS Code `launch.json`:

```json
{
  "version": "0.2.0",
  "configurations": [
    {
      "name": "Listen for XDebug",
      "type": "php",
      "request": "launch",
      "port": 9047,
      "pathMappings": {
        "/workspace": "${workspaceFolder}"
      }
    }
  ]
}
```

## Troubleshooting

### "Could not detect WSL2 IP address"

This error occurs when:

- Running the script from Windows instead of WSL2
- Running the script inside a container
- Network configuration issues

**Solution**: Ensure you're running `.docker/generate-env.sh` from a WSL2 terminal.

### XDebug Not Connecting

1. **Check environment variables**:

   ```bash
   echo $WSL_IP
   echo $XDEBUG_PORT
   ```

2. **Verify IP is reachable**:

   ```bash
   ping $WSL_IP
   ```

3. **Check port availability**:

   ```bash
   netstat -ln | grep :9047
   ```

4. **Regenerate environment**:
   ```bash
   .docker/generate-env.sh
   # Rebuild Dev Container
   ```

### Container Won't Start

1. **Check Docker Desktop is running in WSL2 mode**
2. **Verify `.env` file exists and is valid**:
   ```bash
   cat .env
   ```
3. **Check Docker Compose syntax**:
   ```bash
   docker-compose config
   ```

## Manual Setup (Alternative)

If the automatic script doesn't work, you can manually create `.env`:

```bash
# Get WSL2 IP manually
WSL_IP=$(ip route | grep default | awk '{print $3}')
echo $WSL_IP

# Create .env file manually
cat > .env << EOF
APP_NAME=ncac-phpcs-standard
PHP_VERSION=7.4.33
APP_ROOT=/workspace
WSL_IP=$WSL_IP
XDEBUG_PORT=9047
EOF
```

## Files Overview

| File                      | Purpose                         |
| ------------------------- | ------------------------------- |
| `.devcontainer.json`      | Dev Container configuration     |
| `docker-compose.yml`      | Docker services definition      |
| `.docker/generate-env.sh` | Environment setup script        |
| `.env`                    | Generated environment variables |
| `.env.example`            | Environment template            |

## Security Notes

- The `.env` file is automatically ignored by Git
- Contains only local development configuration
- No sensitive data should be stored
- Regenerate after IP changes or system updates
