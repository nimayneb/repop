<?php namespace JayBeeR\Repop\Connector {

    /*
     * See LICENSE.txt that was shipped with this package.
     */

    use JayBeeR\Repop\Failure\RepositoryNotFound;
    use JayBeeR\Repop\Repository\RepositoryObject;
    use PDO;
    use PDOException;

    /**
     * class DatabaseConnector
     *
     */
    class DatabaseConnector
    {
        /**
         * @var PDO
         */
        protected $connection;

        /**
         * @var RepositoryObject[]
         */
        protected static $repositories;

        /**
         *
         */
        public function __destruct()
        {
            $this->connection = null;
        }

        /**
         *
         */
        public function getConnection(): PDO
        {
            return $this->connection;
        }

        /**
         * @param string $className
         *
         * @return DatabaseConnector
         */
        public function registerRepository(string $className): DatabaseConnector
        {
            /** @var RepositoryObject $repository */
            $repository = new $className($this);

            static::$repositories[$repository->getTable()] = $repository;

            return $this;
        }

        /**
         * @param string $key
         *
         * @return RepositoryObject
         * @throws RepositoryNotFound
         */
        public static function getRepository(string $key): RepositoryObject
        {
            if (!isset(static::$repositories[$key])) {
                throw new RepositoryNotFound(sprintf('Cannot find repository key <%s> in DatabaseConnector.', $key));
            }

            return static::$repositories[$key];
        }

        /**
         * @param string|null $driver
         * @param string|null $hostname
         * @param string|null $port
         * @param string|null $database
         * @param string|null $username
         * @param string|null $password
         */
        public function connectToDatabase(
            string $driver = null,
            string $hostname = null,
            string $port = null,
            string $database = null,
            string $username = null,
            string $password = null
        )
        {
            $dsn = sprintf(
                '%s:host=%s;port=%d;dbname=%s',
                $driver ?? getenv('DATABASE_DRIVER'),
                $hostname ?? getenv('DATABASE_HOSTNAME'),
                $port ?? getenv('DATABASE_PORT'),
                $database ?? getenv('DATABASE_NAME')
            );

            try {
                $this->connection = new PDO(
                    $dsn,
                    $username ?? getenv('DATABASE_USERNAME'),
                    $password ?? getenv('DATABASE_PASSWORD')
                );

                $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (PDOException $e) {
                fwrite(STDERR, sprintf("    Data Source Name: %s\n", $dsn));
                fwrite(STDERR, sprintf("       Error message: %s\n\n", $e->getMessage()));
                exit(1);
            }
        }
    }
}
