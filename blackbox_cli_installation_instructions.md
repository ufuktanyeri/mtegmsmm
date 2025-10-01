# Blackbox CLI Installation Instructions

We've identified that the original download URL for the Blackbox CLI is no longer valid. The correct URL is:

```
https://shell.blackbox.ai/api/scripts/omni-cli/download.ps1
```

## Installation Steps

1. Open PowerShell as Administrator
2. Run the following command:

```powershell
Invoke-WebRequest -Uri "https://shell.blackbox.ai/api/scripts/omni-cli/download.ps1" -OutFile "download.ps1"; .\download.ps1
```

This command will:
- Download the installation script from the correct URL
- Save it as download.ps1
- Execute the script to install the Blackbox CLI

## Troubleshooting

If you encounter any issues:

1. Make sure you're running PowerShell as Administrator
2. Check your internet connection
3. Verify that the URL is accessible from your network
4. If you're behind a corporate firewall or proxy, you may need to configure PowerShell to use your proxy settings

## Alternative Installation Methods

If the above method doesn't work, you can try:

1. Visiting the official Blackbox AI website at https://www.blackbox.ai/
2. Looking for CLI installation instructions in the documentation
3. Contacting Blackbox AI support for assistance

## What Changed

The original URL was using `releases.blackbox.ai` as the base domain, but the correct domain is `shell.blackbox.ai`. This is why the original command was failing with a 404 Not Found error.
