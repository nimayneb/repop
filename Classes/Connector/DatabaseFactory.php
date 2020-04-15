<?php declare(strict_types=1);

namespace JayBeeR\Repop\Connector {

    /*
     * See LICENSE.txt that was shipped with this package.
     */

    use PDO;
    use PDOException;

    /**
     *
     */
    class DatabaseFactory
    {
        /**
         * @param string $driver
         * @param string $hostname
         * @param string $port
         * @param string $database
         * @param string $username
         * @param string $password
         *
         * @return DatabaseConnector
         */
        public static function connectToDatabase(
            string $driver = null,
            string $hostname = null,
            string $port = null,
            string $database = null,
            string $username = null,
            string $password = null
        ): DatabaseConnector
        {
            $dsn = sprintf(
                '%s:host=%s;port=%d;dbname=%s',
                $driver ?? getenv('DATABASE_DRIVER'),
                $hostname ?? getenv('DATABASE_HOSTNAME'),
                $port ?? getenv('DATABASE_PORT'),
                $database ?? getenv('DATABASE_NAME')
            );

            try {
                $connection = new PDO(
                    $dsn,
                    $username ?? getenv('DATABASE_USERNAME'),
                    $password ?? getenv('DATABASE_PASSWORD')
                );

                $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (PDOException $e) {
                fwrite(STDERR, sprintf("    Data Source Name: %s\n", $dsn));
                fwrite(STDERR, sprintf("       Error message: %s\n\n", $e->getMessage()));
                exit(1);
            }

            return new DatabaseConnector($connection);
        }
    }
}
