# REPository OPerator

## Connection to database (.env supported)

Prepare with `.env` file:

    DATABASE_DRIVER=mysql
    DATABASE_NAME=test
    DATABASE_HOSTNAME=localhost
    DATABASE_USERNAME=admin
    DATABASE_PASSWORD=secret
    DATABASE_PORT=3306

Usage in `application.php` (w/ `.env`):

    $connection = new JayBeeR\Repop\Connector\DatabaseConnector;
    $connection->connectToDatabase();

Usage in `application.php` (w/o `.env`):

    $connection->connectToDatabase('mysql', 'localhost', 3306, 'test', 'admin', 'secret');
    
## Register Repository with current connection 

**Usage:**

    $connection->registerRepository({Repository model class}::class);

## Use Repository with allocated connection 

**Usage:**

    $repository = DatabaseConnector::getRepository('{unique table name for repository}');

## Create Repository

**Template:**

    class {model name in plural} extends JayBeeR\Repop\Repository\RepositoryObject
    {
    }

---

### Set the table name of your repository

**Usage:**

        /**
         * @var string
         */
        protected static $table = '{table name}';

---

### Specified the properties of a model object

**Usage:**

        /**
         * @return array
         */
        protected function getTableColumns(): array
        {
            return {static model class}::getTableColumns();
        }

---

### Specified the creation of a model object

**Usage (PHP <= 7.3):**

        /**
         * @param PDOStatement $statement
         *
         * @return {model class}|JayBeeR\Repop\Model\ModelObject
         */
        protected function toObject(PDOStatement $statement): JayBeeR\Repop\Model\ModelObject
        {
            return {static model class}::fromResult($statement);
        }

**Usage (PHP >= 7.4 - invariant return types):**

        /**
         * @param PDOStatement $statement
         *
         * @return {model class}
         */
        protected function toObject(PDOStatement $statement): {model class}
        {
            return {static model class}::fromResult($statement);
        }
        
---

### Define a public method for a query

**Conventions:**

- say what you want to find: "findElementWithLove" => returns PDOStatement (reusable)
- say what you want to get: "elementWithLove" => returns Generator (not reusable, usage with `foreach`)

**Usage (as Generator):**

        /**
         * @return Generator
         */
        public function elementWithLove(string $value): Generator
        {
            $statement = $this->getConnection()->prepare(
                "select {$this->buildTableColumnsStatement()}
                    from `{$this->getTable()}`
                        where `column_name` = :columnName;"
            );

            $statement->bindParam(':columnName', $value, PDO::PARAM_STR);

            $this->ensureStatementExecution($statement);

            return $this->iterate($statement);
        }

**Usage (reusable):**

        /**
         * @return PDOStatement
         */
        public function elementWithLove(string $value): PDOStatement
        {
            $statement = $this->getConnection()->prepare(
                "select {$this->buildTableColumnsStatement()}
                    from `{$this->getTable()}`
                        where `column_name` = :columnName;"
            );

            $statement->bindParam(':columnName', $value, PDO::PARAM_STR);

            $this->ensureStatementExecution($statement);

            return $statement;
        }

## Create Model class

**Template:**

    class {model name in singular} extends JayBeeR\Repop\Model\ModelObject
    {
    }

---

### Add new column property

**Conventions:**

- use underscores (like SQL)
  - RIGHT: `$column_name;`
  - WRONG: `$columnName;`
- must be protected properties

**Possible types:**

- string
- float
- int
- bool

**Usage (PHP <= 7.3):**

        /**
         * {Description}
         *
         * @var {column type}
         */
        protected ${column name};

**Usage (PHP >= 7.4 - property types):**

        /**
         * {Description}
         */
        protected {column type} ${column name};

---

### Initialize column type (only for PHP <= 7.3)

**Possible types:**

- string
- float
- int
- bool

**Usage:**

        /**
         *
         */
        public function __construct()
        {
            settype($this->{column name}, '{column type}');
            ...
        }

---

### Add Getter method for column property

**Conventions:**

- use camel case and more understandable name of your column
  - not "getUid" => "getIdentifier"
  - not "getTxExtensionLock" => "getLockState"

**Possible types:**

- string
- float
- int
- bool

**Usage:**

        /**
         * @return {column type}
         */
        public function get{colum name}(): {column type}
        {
            return $this->{colum name};
        }

---

### Add Setter method for column property

**Conventions:**

- use camel case and more understandable name of your column
  - not "setUid" => "setIdentifier"
  - not "setTxExtensionLock" => "setLockState"

**Usage:**

        /**
         * @param {column type} $value
         */
        public function set{column name}({column type} $value): void
        {
            $this->updateColumnValue(static::{column name}, $value);
        }

---

### Add static method to create this model class by PDOStatement 

**Usage (PHP <= 7.3):**

        /**
         * @param PDOStatement $statement
         *
         * @return {ModelClass}|JayBeeR\Repop\Model\ModelObject|null
         */
        public static function fromResult(PDOStatement $statement): ?JayBeeR\Repop\Model\ModelObject
        {
            return static::get($statement);
        }

**Usage (PHP >= 7.4 - invariant return types):**

        /**
         * @param PDOStatement $statement
         *
         * @return {ModelClass}|null
         */
        public static function fromResult(PDOStatement $statement): ?{ModelClass}
        {
            return static::get($statement);
        }