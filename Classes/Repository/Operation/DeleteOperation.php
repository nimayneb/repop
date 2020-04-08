<?php namespace JayBeeR\Repop\Repository\Operation {

    /*
     * See LICENSE.txt that was shipped with this package.
     */

    use JayBeeR\Repop\Model\IdentifiedModelObject;
    use PDO;

    /**
     * @method PDO getConnection()
     */
    trait DeleteOperation
    {
        /**
         * @param IdentifiedModelObject $modelObject
         */
        public function delete(IdentifiedModelObject $modelObject)
        {
            $statement = $this->getConnection()->prepare(
                "delete from `{$this->getTable()}` where `{$modelObject->getIdentifierName()}` = :identifier;"
            );

            $this->ensureStatementExecution($statement, $modelObject);
        }
    }
}
