<?php declare(strict_types=1);

namespace JayBeeR\Repop\Repository\Operation {

    /*
     * See LICENSE.txt that was shipped with this package.
     */

    use JayBeeR\Repop\Model\IdentifiedModelObject;
    use JayBeeR\Repop\Repository\Persistence;
    use PDO;

    /**
     * @method PDO getConnection()
     */
    trait UpdateOperation
    {
        /**
         * @param IdentifiedModelObject|Persistence $modelObject
         */
        public function update(IdentifiedModelObject $modelObject)
        {
            $statement = $this->getConnection()->prepare(
                "update `{$this->getTableName()}` set {$modelObject->getDirtyColumns()} where `{$modelObject->getIdentifierName()}` = :identifier;"
            );

            $identifier = $modelObject->getIdentifier();
            $statement->bindParam(':identifier', $identifier, PDO::PARAM_INT);

            foreach ($modelObject->getDirtyParameters() as $bindParameter) {
                $bindParameter($statement);
            }

            $this->ensureStatementExecution($statement, $modelObject);

            $modelObject->clean();
        }
    }
}
