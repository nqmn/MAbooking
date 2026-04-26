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
