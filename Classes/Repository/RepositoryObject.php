<?php namespace JayBeeR\Repop\Repository {

    /*
     * See LICENSE.txt that was shipped with this package.
     */

    use Generator;
    use JayBeeR\Repop\Model\ModelObject;
    use JayBeeR\Repop\Repository\Operation\CreateOperation;
    use JayBeeR\Repop\Repository\Operation\DeleteOperation;
    use JayBeeR\Repop\Repository\Operation\UpdateOperation;
    use PDO;
    use PDOException;
    use PDOStatement;

    /**
     * Class RepositoryObject
     *
     */
    abstract class RepositoryObject
    {
        use ConstructorWithConnector;
        use CreateOperation;
        use UpdateOperation;
        use DeleteOperation;

        /**
         * @var string
         */
        protected static $table;

        /**
         * @var array
         */
        protected $columns = [];

        /**
         * @var ModelObject[]
         */
        protected $driedRepository;

        /**
         * @return string
         */
        public static function getTableName(): string
        {
            return static::$table;
        }

        /**
         * @param PDOStatement $statement
         *
         * @return Generator
         */
        public function iterate(PDOStatement $statement): Generator
        {
            while($modelObject = $this->toObject($statement)) {
                yield $modelObject;
            }
        }

        /**
         * @param int $identifier
         *
         * @return ModelObject
         */
        protected function getObject(int $identifier)
        {
            if (isset($this->driedRepository[$identifier])) {
                return $this->driedRepository[$identifier];
            }

            // TODO: uid?
            $statement = $this->getConnection()->prepare(
                "select {$this->buildTableColumnsStatement()} from `{$this->getTable()}` where `uid` = :identifier;"
            );

            $statement->bindParam(':identifier', $identifier, PDO::PARAM_INT);

            $this->ensureStatementExecution($statement);

            return $this->driedRepository[$identifier] = $this->toObject($statement);
        }

        /**
         * @param int $identifier
         *
         * @return ModelObject|null
         */
        public function byIdentifier(int $identifier): ?ModelObject
        {
            return $this->getObject($identifier);
        }

        /**
         * @return string
         */
        protected function buildTableColumnsStatement(): string
        {
            return StatementHelper::buildTableColumnStatement($this->getTableColumns());
        }

        /**
         * @param PDOStatement $statement
         *
         * @return mixed
         */
        abstract protected function toObject(PDOStatement $statement);

        /**
         * @return array
         */
        abstract protected function getTableColumns(): array;

        /**
         * @return string
         */
        public function getTable(): string
        {
            return static::$table;
        }

        /**
         * @param PDOStatement $statement
         * @param ModelObject $modelObject
         */
        protected function ensureStatementExecution(PDOStatement $statement, ModelObject $modelObject = null): void
        {
            try {
                $this->ensureSuccess($statement->execute());
            } catch (PDOException $e) {
                fwrite(STDERR, $e->getMessage() . "\n");
                debug_print_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
                fwrite(STDERR, "\n\n");
                exit(1);
            }
        }

        /**
         * @param $result
         */
        protected function ensureSuccess($result): void
        {
            if (false === $result) {
                fwrite(STDERR, sprintf("%s\n\n", $this->getConnection()->errorCode()));
                debug_print_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
                exit(1);
            }
        }
    }
}
