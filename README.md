# DB Installer

Small library to install or update database dependencies of a library, based on its current version.

```php
new \Simbiat\Database\Installer($dbh)::install(string $pattern, string $version = '0.0.0', string $replace_string = '', string $replace_with = '')
```

The constructor of the `Installer` class requires a `\PDO` object to establish connection, or it can be or `null` if you are using [DB Pool](https://github.com/Simbiat/db-pool) library.

Then pass a pattern for `glob()` to get the files with SQL commands you need to run and current `version` string. The function will scan the respective folder(s) for files and then compare their names to the version provided. Those files that have a "greater" version number (based on `version_compare()`) will be concatenated and then run against the database. If the commands succeed, the function will return `true`, otherwise an exception will be thrown.

You can also pass `$replace_string` and `$replace_with` that will `preg_replace()` in the resulting set of queries. This is useful if you need to customize a database prefix, for example.