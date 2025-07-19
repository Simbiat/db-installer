<?php
declare(strict_types = 1);

namespace Simbiat\Database;

/**
 * Install database dependencies for a library
 */
class Installer
{
    /**
     * @var null|\PDO PDO object to run queries against
     */
    public static ?\PDO $dbh = null;
    
    /**
     * @param \PDO|null $dbh PDO obj
     */
    public function __construct(?\PDO $dbh = null)
    {
        if ($dbh === null) {
            if (\method_exists(Pool::class, 'openConnection')) {
                self::$dbh = Pool::openConnection();
            } else {
                throw new \RuntimeException('Pool class not loaded and no PDO object provided.');
            }
        } else {
            self::$dbh = $dbh;
        }
    }
    
    /**
     * Install database dependencies for a library based on the current version
     *
     * @param string $pattern        Path and file pattern for GLOB
     * @param string $version        Version
     * @param string $replace_string Optional regex to match for replacement
     * @param string $replace_with   Optional regex to replace with
     *
     * @return bool
     */
    public static function install(string $pattern, string $version = '0.0.0', string $replace_string = '', string $replace_with = ''): bool
    {
        #Generate SQL to run
        $sql = '';
        #Get SQL from all files. Sorting is required since we need a specific order of execution.Add commentMore actions
        /** @noinspection LowPerformingFilesystemOperationsInspection */
        foreach (\glob($pattern) as $file) {
            #Compare version and take only newer ones
            if (\version_compare(\basename($file, '.sql'), $version, 'gt')) {
                #Get contents from the SQL file
                $sql .= \file_get_contents($file);
            }
        }
        #String replacement if it was set up
        if (!empty($replace_string)) {
            $sql = \preg_replace($replace_string, $replace_with, $sql);
        }
        #If empty - we are up to date
        if (empty($sql)) {
            return true;
        }
        return Query::query($sql);
    }
}