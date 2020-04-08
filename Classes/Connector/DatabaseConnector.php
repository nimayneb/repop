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
        protected $repositories;

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
         * @param string $key
         * @param string $className
         *
         * @return DatabaseConnector
         */
        public function addRepository(string $key, string $className): DatabaseConnector
        {
            $this->repositories[$key] = new $className($this);

            return $this;
        }

        /**
         * @param string $key
         *
         * @return RepositoryObject
         * @throws RepositoryNotFound
         */
        public function getRepository(string $key): RepositoryObject
        {
            if (!isset($this->repositories[$key])) {
                throw new RepositoryNotFound(sprintf('Cannot find repository key <%s> in DatabaseConnector.', $key));
            }

            return $this->repositories[$key];
        }

        /**
         *
         */
        public function connectToDatabase()
        {
            $dsn = sprintf(
                '%s:host=%s;port=%d;dbname=%s',
                getenv('DATABASE_DRIVER'),
                getenv('DATABASE_HOSTNAME'),
                getenv('DATABASE_PORT'),
                getenv('DATABASE_NAME')
            );

            try {
                $this->connection = new PDO(
                    $dsn,
                    getenv('DATABASE_USERNAME'),
                    getenv('DATABASE_PASSWORD')
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
