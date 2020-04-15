<?php declare(strict_types=1);

namespace JayBeeR\Repop\Model {

    /*
     * See LICENSE.txt that was shipped with this package.
     */

    /**
     *
     */
    abstract class IdentifiedModelObject extends ModelObject
    {
        /**
         * @return string
         */
        abstract public function getIdentifierName(): string;

        /**
         * @return mixed
         */
        abstract public function getIdentifier();
    }
}
