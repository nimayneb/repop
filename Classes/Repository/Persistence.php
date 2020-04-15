<?php declare(strict_types=1);

namespace JayBeeR\Repop\Repository {

    /*
     * See LICENSE.txt that was shipped with this package.
     */

    use Closure;
    use JayBeeR\Repop\Failure\UnknownTableColumnType;
    use PDO;
    use PDOStatement;

    /**
     *
     */
    trait Persistence
    {
        /**
         * @var Closure[]
         */
        private array $dirtyParameters = [];

        /**
         * @var Closure[]
         */
        private array $dirtyColumns = [];

        /**
         *
         */
        public function clean(): void
        {
            $this->dirtyParameters = [];
            $this->dirtyColumns = [];
        }

        /**
         * @param string $columnName
         * @param mixed $columnValue
         */
        protected function updateColumnValue(string $columnName, &$columnValue): void
        {
            $this->{$columnName} = &$columnValue;
            $this->addDirtyColumn($columnName, $columnValue);
        }

        /**
         * @param string $columnName
         * @param mixed $columnValue
         */
        protected function addDirtyColumn(string $columnName, &$columnValue): void
        {
            if (false === array_key_exists($columnName, $this->dirtyColumns)) {
                $this->dirtyColumns[$columnName] = "`$columnName` = :$columnName";
                $this->dirtyParameters[$columnName] = function (PDOStatement $statement) use ($columnName, &$columnValue) {
                    $statement->bindParam(":$columnName", $columnValue, static::getType($columnValue));
                };
            }
        }

        /**
         * @param $value
         *
         * @return int
         * @throws UnknownTableColumnType
         */
        public static function getType($value): int
        {
            $type = gettype($value);

            switch ($type) {
                case 'integer':
                {
                    $typeNum = PDO::PARAM_INT;

                    break;
                }

                case 'boolean':
                {
                    $typeNum = PDO::PARAM_BOOL;

                    break;
                }

                case 'string':
                {
                    $typeNum = PDO::PARAM_STR;

                    break;
                }

                case 'null':
                {
                    $typeNum = PDO::PARAM_NULL;

                    break;
                }

                default:
                {
                    throw new UnknownTableColumnType();
                }
            }

            return $typeNum;
        }

        /**
         * @return string
         */
        public function getDirtyColumns(): string
        {
            return StatementHelper::buildTableColumnStatement($this->dirtyColumns);
        }

        /**
         * @return Closure[]
         */
        public function getDirtyParameters(): array
        {
            return $this->dirtyParameters;
        }
    }
}
