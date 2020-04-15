<?php declare(strict_types=1);

namespace JayBeeR\Repop\Model {

    /*
     * See LICENSE.txt that was shipped with this package.
     */

    use PDOStatement;

    /**
     * Class ModelObject
     *
     */
    abstract class ModelObject
    {
        /**
         * @param PDOStatement $statement
         *
         * @return ModelObject
         */
        public static function get(PDOStatement $statement): ?ModelObject
        {
            $model = $statement->fetchObject(static::class);

            if (false === $model) {
                $model = null;
            }

            return $model;
        }

        /**
         * @return array
         */
        public function toArray(): array
        {
            return get_object_vars($this);
        }

        /**
         * @return array
         */
        public function getColumns(): array
        {
            return static::getTableColumns();
        }

        /**
         * @return array
         */
        public static function getTableColumns(): array
        {
            return array_keys(get_class_vars(static::class));
        }

        /**
         * @param PDOStatement $statement
         *
         * @return ModelObject
         */
        abstract static function fromResult(PDOStatement $statement): ?ModelObject;
    }
}
