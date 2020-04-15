<?php declare(strict_types=1);

namespace JayBeeR\Repop\Connector {

    /*
     * See LICENSE.txt that was shipped with this package.
     */

    use JayBeeR\Repop\Failure\RepositoryNotFound;
    use JayBeeR\Repop\Failure\WrongRepositoryObject;
    use JayBeeR\Repop\Repository\RepositoryAttributes;
    use JayBeeR\Repop\Repository\RepositoryObject;
    use PDO;

    /**
     *
     */
    class DatabaseConnector
    {
        /**
         * @var PDO|null
         */
        protected ?PDO $connection;

        /**
         * @var Object[]
         */
        protected static array $repositories;

        /**
         * @param string $tableName
         *
         * @return RepositoryObject
         * @throws RepositoryNotFound
         */
        public static function getRepository(string $tableName): RepositoryObject
        {
            if (!isset(static::$repositories[$tableName])) {
                throw new RepositoryNotFound(sprintf('Cannot find repository table name <%s> in database connection.', $tableName));
            }

            return static::$repositories[$tableName];
        }

        /**
         * @param PDO $connection
         */
        public function __construct(PDO $connection)
        {
            $this->connection = $connection;
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
         * @throws WrongRepositoryObject
         */
        public function registerRepository(string $className): DatabaseConnector
        {
            /** @var RepositoryAttributes $repository */
            $repository = new $className($this);

            if (!$repository instanceof RepositoryAttributes) {
                throw new WrongRepositoryObject($className);
            }

            static::$repositories[$repository->getTableName()] = $repository;

            return $this;
        }

        /**
         *
         */
        public function __destruct()
        {
            $this->connection = null;
        }
    }
}
