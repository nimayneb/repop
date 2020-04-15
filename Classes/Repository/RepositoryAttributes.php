<?php declare(strict_types=1);

namespace JayBeeR\Repop\Repository {

    use Generator;
    use JayBeeR\Repop\Model\ModelObject;
    use PDOStatement;

    /**
     *
     */
    abstract class RepositoryAttributes
    {
        /**
         * @var string
         */
        protected static string $tableName;

        /**
         * @return string
         */
        public function getTableName(): string
        {
            return static::$tableName;
        }

        /**
         * @param PDOStatement $statement
         *
         * @return Generator
         */
        abstract public function iterate(PDOStatement $statement): Generator;

        /**
         * @param PDOStatement $statement
         *
         * @return mixed
         */
        abstract protected function toObject(PDOStatement $statement);

        /**
         * @param int $identifier
         *
         * @return ModelObject
         */
        abstract protected function getObject(int $identifier);

        /**
         * @return array
         */
        abstract protected function getTableColumns(): array;

        /**
         * @return array
         */
        abstract protected function getColumnNameOfUniqueIdentifier(): array;
    }
} 