# Joomla Extension ZIP Issue on Windows

## Problem Summary

When packaging a Joomla extension on Windows, the resulting ZIP file often causes Joomla to fail with "manifest file not detected" during installation. This is caused by two separate issues introduced by Windows ZIP tools.

---

## Issue 1: Manifest File Not at Archive Root

### What Joomla Expects

Joomla's installer scans the **root level** of the ZIP archive for a file matching `*.xml` that contains `<extension ...>` — this is the manifest. If it cannot find the manifest at root, installation fails immediately.

### What Windows Does Wrong

When you right-click a folder and choose "Compress to ZIP", or use PowerShell's `Compress-Archive` like this:

```powershell
Compress-Archive -Path "com_ma360viewer" -DestinationPath "com_ma360viewer.zip"
```

The folder itself becomes the top-level entry inside the archive:

```
com_ma360viewer/               <-- extra wrapping folder
com_ma360viewer/com_ma360viewer.xml
com_ma360viewer/admin/
com_ma360viewer/site/
...
```

Joomla looks for `*.xml` at the root. It finds nothing — only the `com_ma360viewer/` directory — and throws the "manifest not detected" error.

### The Fix

Compress the **contents** of the folder, not the folder itself. In PowerShell, use the wildcard `*`:

```powershell
Compress-Archive -Path "com_ma360viewer\*" -DestinationPath "com_ma360viewer.zip"
```

Or using the .NET API (preferred — see Issue 2):

```powershell
# Entries will start at com_ma360viewer.xml, admin/, site/, etc.
# NOT at com_ma360viewer/com_ma360viewer.xml
```

Correct archive structure:

```
com_ma360viewer.xml    <-- manifest at root, Joomla finds it
admin/
admin/ma360viewer.php
admin/forms/
...
site/
site/ma360viewer.php
...
media/
```

---

## Issue 2: Windows Backslash Paths in ZIP Entries

### What the ZIP Specification Requires

The ZIP file format specification (PKWARE APPNOTE.TXT) states that path separators inside entry names **must be forward slashes** (`/`). This is true even on Windows.

### What Windows Does Wrong

PowerShell's `Compress-Archive` and most Windows GUI tools encode entry names using the Windows path separator (backslash `\`):

```
admin\language\en-GB\com_ma360viewer.ini   <-- wrong
admin\services\provider.php                <-- wrong
```

### Why This Breaks Joomla

Joomla runs on a server (usually Linux/Apache). PHP's `ZipArchive` class, which Joomla uses to extract the archive, treats `\` as a literal character in the entry name rather than a path separator on Linux. This means:

- Directory traversal fails — files may be extracted to wrong locations or not at all.
- Even if the manifest is found, the files referenced inside it may not install correctly.
- Behaviour is inconsistent across PHP versions and server OS.

### The Fix

Use the .NET `System.IO.Compression.ZipArchive` API directly in PowerShell, which lets you control the entry name explicitly. Replace `\` with `/` when building each entry name:

```powershell
Add-Type -AssemblyName System.IO.Compression
Add-Type -AssemblyName System.IO.Compression.FileSystem

$srcDir  = "C:\Users\Intel\Desktop\MA360viewer\com_ma360viewer"
$destZip = "C:\Users\Intel\Desktop\MA360viewer\com_ma360viewer.zip"

if (Test-Path $destZip) { Remove-Item $destZip -Force }

$zipStream = [System.IO.File]::Open($destZip, [System.IO.FileMode]::Create)
$archive   = [System.IO.Compression.ZipArchive]::new($zipStream, [System.IO.Compression.ZipArchiveMode]::Create)

Get-ChildItem -Path $srcDir -Recurse -File | ForEach-Object {
    $filePath  = $_.FullName
    # Strip the src directory prefix, then replace backslashes with forward slashes
    $entryName = $filePath.Substring($srcDir.Length + 1).Replace('\', '/')
    $entry      = $archive.CreateEntry($entryName, [System.IO.Compression.CompressionLevel]::Optimal)
    $entryStream = $entry.Open()
    $fileStream  = [System.IO.File]::OpenRead($filePath)
    $fileStream.CopyTo($entryStream)
    $fileStream.Close()
    $entryStream.Close()
}

$archive.Dispose()
$zipStream.Dispose()
```

Resulting entry names (correct):

```
com_ma360viewer.xml
admin/ma360viewer.php
admin/forms/panorama.xml
admin/language/en-GB/com_ma360viewer.ini
admin/services/provider.php
admin/sql/install.mysql.utf8.sql
site/ma360viewer.php
site/language/en-GB/com_ma360viewer.ini
media/js/three.min.js
...
```

---

## Issue 3: Installer Script Not at Package Root

### The Symptom

The component installs or updates, but every administrator submenu shows a database error like:

```text
An error has occurred.
1146 Table 'database_name.josdl_mabooking_bookings' doesn't exist
```

For MA Booking, this means Joomla is loading the component code and the admin menu links, but the database tables were not created. The views query these tables:

```text
#__mabooking_bookings
#__mabooking_spaces
#__mabooking_venues
```

### Why This Can Happen

The normal install SQL is stored in:

```text
com_mabooking/admin/sql/install.mysql.utf8.sql
```

and the component manifest references it as:

```xml
<install>
    <sql>
        <file driver="mysql" charset="utf8mb4">sql/install.mysql.utf8.sql</file>
    </sql>
</install>
```

That layout is correct for Joomla components. However, if Joomla already has an extension record for `com_mabooking`, it may treat the upload as an upgrade instead of a fresh install. In that case, the install SQL may not be run again. If the extension record exists but the custom tables are missing, admin submenu pages fail when they query the missing tables.

MA Booking `0.4.6` adds a repair guard:

```xml
<scriptfile>script.php</scriptfile>
```

Important: the file named by `<scriptfile>` must be at the root of the component ZIP:

```text
com_mabooking.xml
script.php                 <-- correct
admin/
site/
```

Do not place it only under `admin/script.php` when the manifest says `script.php`:

```text
admin/script.php           <-- wrong for <scriptfile>script.php</scriptfile>
```

If the script file is in the wrong place, Joomla will not run the schema repair code, and the missing table error can persist even though the ZIP looks otherwise valid.

### The Fix

MA Booking uses both of these repair paths:

1. Root `script.php` creates the tables during install, update, and discover install.
2. `admin/sql/updates/mysql/0.4.6.sql` creates the same tables with `CREATE TABLE IF NOT EXISTS` during Joomla schema updates.

The root script file starts like this:

```php
<?php

\defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Installer\InstallerAdapter;

class com_mabookingInstallerScript
{
    public function install(InstallerAdapter $parent): bool
    {
        return $this->ensureSchema();
    }

    public function update(InstallerAdapter $parent): bool
    {
        return $this->ensureSchema();
    }

    public function discover_install(InstallerAdapter $parent): bool
    {
        return $this->ensureSchema();
    }

    private function ensureSchema(): bool
    {
        $db = Factory::getDbo();

        foreach ($this->getSchemaQueries() as $query)
        {
            $db->setQuery($query)->execute();
        }

        return true;
    }
}
```

The actual `script.php` in this repository includes the full table creation SQL and default venue/space seed data.

---

## Verification

After creating the ZIP, always verify the structure before uploading to Joomla:

```powershell
Add-Type -AssemblyName System.IO.Compression.FileSystem
$zip = [System.IO.Compression.ZipFile]::OpenRead("com_ma360viewer.zip")
$zip.Entries | ForEach-Object { Write-Host $_.FullName }
$zip.Dispose()
```

Checklist:
- [ ] `com_ma360viewer.xml` appears at the top of the list (root level)
- [ ] All paths use forward slashes (`/`), not backslashes (`\`)
- [ ] No wrapping folder (no entry starts with `com_ma360viewer/`)
- [ ] For components with `<scriptfile>script.php</scriptfile>`, `script.php` appears at the ZIP root
- [ ] SQL files appear under `admin/sql/`
- [ ] Local tooling folders such as `.git` and `.claude` are not included

---

## MA Booking Packaging Script

Run this from the repository root:

```powershell
Add-Type -AssemblyName System.IO.Compression
Add-Type -AssemblyName System.IO.Compression.FileSystem

function New-ZipFromDirectory($SourceDir, $DestZip) {
    $source = (Resolve-Path $SourceDir).Path
    $zipPath = Join-Path (Get-Location) $DestZip

    if (Test-Path -LiteralPath $zipPath) {
        Remove-Item -LiteralPath $zipPath -Force
    }

    $zipStream = [System.IO.File]::Open($zipPath, [System.IO.FileMode]::Create)
    $archive = [System.IO.Compression.ZipArchive]::new(
        $zipStream,
        [System.IO.Compression.ZipArchiveMode]::Create
    )

    try {
        Get-ChildItem -Path $source -Recurse -File -Force |
            Where-Object {
                $_.FullName.Substring($source.Length + 1) -notmatch '^(\.claude|\.git)(\\|/)'
            } |
            ForEach-Object {
                $filePath = $_.FullName
                $entryName = $filePath.Substring($source.Length + 1).Replace('\', '/')
                $entry = $archive.CreateEntry(
                    $entryName,
                    [System.IO.Compression.CompressionLevel]::Optimal
                )
                $entryStream = $entry.Open()
                $fileStream = [System.IO.File]::OpenRead($filePath)

                try {
                    $fileStream.CopyTo($entryStream)
                } finally {
                    $fileStream.Close()
                    $entryStream.Close()
                }
            }
    } finally {
        $archive.Dispose()
        $zipStream.Dispose()
    }
}

function New-PackageZip($DestZip) {
    $zipPath = Join-Path (Get-Location) $DestZip

    if (Test-Path -LiteralPath $zipPath) {
        Remove-Item -LiteralPath $zipPath -Force
    }

    $zipStream = [System.IO.File]::Open($zipPath, [System.IO.FileMode]::Create)
    $archive = [System.IO.Compression.ZipArchive]::new(
        $zipStream,
        [System.IO.Compression.ZipArchiveMode]::Create
    )

    try {
        foreach ($item in @(
            @{ Path = '.\pkg_mabooking.xml'; Entry = 'pkg_mabooking.xml' },
            @{ Path = '.\com_mabooking.zip'; Entry = 'packages/com_mabooking.zip' },
            @{ Path = '.\plg_quickicon_mabooking.zip'; Entry = 'packages/plg_quickicon_mabooking.zip' }
        )) {
            $filePath = (Resolve-Path $item.Path).Path
            $entry = $archive.CreateEntry(
                $item.Entry,
                [System.IO.Compression.CompressionLevel]::Optimal
            )
            $entryStream = $entry.Open()
            $fileStream = [System.IO.File]::OpenRead($filePath)

            try {
                $fileStream.CopyTo($entryStream)
            } finally {
                $fileStream.Close()
                $entryStream.Close()
            }
        }
    } finally {
        $archive.Dispose()
        $zipStream.Dispose()
    }
}

New-ZipFromDirectory '.\com_mabooking' 'com_mabooking.zip'
New-ZipFromDirectory '.\plg_quickicon_mabooking' 'plg_quickicon_mabooking.zip'
New-PackageZip 'pkg_mabooking.zip'
```

Verify the generated component ZIP:

```powershell
tar -tf .\com_mabooking.zip | Select-Object -First 15
tar -tf .\com_mabooking.zip | Select-String -Pattern '\\|^com_mabooking/|^\.claude/'
tar -tf .\com_mabooking.zip | Select-String -Pattern '^com_mabooking.xml$|^script.php$|^admin/sql/install.mysql.utf8.sql$|^admin/sql/updates/mysql/0.4.6.sql$'
tar -tf .\pkg_mabooking.zip
```

Expected critical entries:

```text
com_mabooking.xml
script.php
admin/sql/install.mysql.utf8.sql
admin/sql/updates/mysql/0.4.6.sql
pkg_mabooking.xml
packages/com_mabooking.zip
packages/plg_quickicon_mabooking.zip
```

The second verification command should return no output. If it returns a path, the ZIP contains either a backslash path, a wrapping folder, or a local `.claude` folder.

---

## Quick Reference

| Tool | Issue | Use Instead |
|---|---|---|
| Windows Explorer right-click ZIP | Both issues | Use PowerShell script above |
| `Compress-Archive -Path "folder"` | Issue 1 (wrapping) | Use `Compress-Archive -Path "folder\*"` |
| `Compress-Archive -Path "folder\*"` | Issue 2 (backslashes) | Use .NET ZipArchive script above |
| .NET ZipArchive with `.Replace('\','/')` | Neither | Correct approach |

---

## Affected Extensions

This issue affects all Joomla extension types packaged on Windows:

- Components (`com_*`)
- Modules (`mod_*`)
- Plugins (`plg_*`)
- Templates
- Packages
