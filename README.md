<img src="https://avatars.githubusercontent.com/u/70107733?s=128" width="100" alt="Elephant Studio">

Elephant Studio :: Laravel Helpers
============

- Config
  - [common_paths](src/Config/common_paths.php) - list of default directories where most used files are saved.
  - [vendor_fixer](src/Config/vendor_fixer.php) - list of files that needs modification before production launch.
- Entity
  - [Environment](src/Entity/Environment.php) - Environment `env('APP_ENV')` helper entity.
- Helper
  - [Helper](src/Helper/Helper.php) - Additional helpers functions
    - `namespaceToHumanReadable(object|string $class)` - convert namespace class to human-readable text.
    - `pathToNamespace(string $filePath)` - convert exact path to namespace class.
    - `collectFiles(string $path, int $maxDepth, array $suffixes = [])` - collest specific files list.
    - `collectDirectories(string $path, int $maxDepth)` - collest directories list in specific path.
    - `isJson($string)` - validate if data is json type.
  - Plain
    - [Vendor Fixer](src/Helper/Plain/VendorFixer.php) - Plain PHP script used to override auto-generated files before production launch.
  - [EnvReader](src/Helper/EnvReader.php) - `env(...)` improvement that removes new line at the end of Env variables.
  - [Namespace Generator](src/Helper/NamespaceGenerator.php) - converted from path file to namespace class.
  - [Path Scanner](src/Helper/PathScanner.php) - Path scanner used for directories or files list generation.
