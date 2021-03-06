<?php declare(strict_types=1);

namespace JayBeeR\Repop\Repository\Operation {

    /*
     * See LICENSE.txt that was shipped with this package.
     */

    use JayBeeR\Repop\Failure\UnknownTableColumnType;
    use JayBeeR\Repop\Model\IdentifiedModelObject;
    use JayBeeR\Repop\Repository\Persistence;
    use PDO;

    /**
     * @method PDO getConnection()
     */
    trait CreateOperation
    {
        /**
         * @param IdentifiedModelObject $modelObject
         *
         * @throws UnknownTableColumnType
         */
        public function insert(IdentifiedModelObject $modelObject)
        {
            $columnNames = [];
            $columnParameters = [];
            $columnValues = $modelObject->toArray();

            foreach ($columnValues as $columnName => $columnValue) {
                if ($modelObject->getIdentifierName() === $columnName) {
                    unset($columnValues[$columnName]);

                    continue;
                }

                $columnNames[] = "`$columnName`";
                $columnParameters[] = ":{$columnName}";
            }

            $columnNameList = implode(', ', $columnNames);
            $columnParameterList = implode(', ', $columnParameters);

            $statement = $this->getConnection()->prepare(
                "insert into `{$this->getTableName()}` ({$columnNameList}) values ({$columnParameterList});"
            );

            foreach ($columnValues as $columnName => $columnValue) {
                $statement->bindParam($columnName, $columnValue, Persistence::getType($columnValue));
            }

            $this->ensureStatementExecution($statement, $modelObject);
        }
    }
}
