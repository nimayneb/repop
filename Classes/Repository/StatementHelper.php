<?php declare(strict_types=1);

namespace JayBeeR\Repop\Repository {

    class StatementHelper
    {
        /**
         * @param array $columns
         *
         * @return string
         */
        public static function buildTableColumnStatement(array $columns): string
        {
            return implode(', ', array_map(function (string $value) {
                return "`{$value}`";
            }, $columns));
        }
    }
} 