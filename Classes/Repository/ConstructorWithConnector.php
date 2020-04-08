<?php namespace JayBeeR\Repop\Repository {

    /*
     * See LICENSE.txt that was shipped with this package.
     */

    use JayBeeR\Repop\Connector\DatabaseConnector;
    use PDO;

    /**
     *
     */
    trait ConstructorWithConnector
    {
        /**
         * @var DatabaseConnector
         */
        protected $connector;

        /**
         * RepositoryObject constructor.
         *
         * @param DatabaseConnector $connector
         */
        public function __construct(DatabaseConnector $connector)
        {
            $this->connector = $connector;
        }

        /**
         * @return PDO
         */
        protected function getConnection(): PDO
        {
            return $this->connector->getConnection();
        }
    }
}
