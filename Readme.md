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

    $connection = JayBeeR\Repop\Connector\DatabaseFactory::connectToDatabase();

Usage in `application.php` (w/o `.env`):

    $connection = JayBeeR\Repop\Connector\DatabaseFactory::connectToDatabase(
        'mysql',
        'localhost',
        3306,
        'test',
        'admin',
        'secret'
    );
    
## Register Repository with current connection 

The given class must be extended from abstract JayBeeR\Repop\Repository\RepositoryAttributes.

**Usage:**

    $connection = JayBeeR\Repop\Connector\DatabaseFactory::connectToDatabase();
    $connection->registerRepository({Repository model class}::class);

## Use Repository with allocated connection 

**Usage:**

    $repository = Repository::getRepository('{unique table name for repository}');

## Create Repository

If the name is ambiguous (like singular), use "s", "Container" as a postfix.

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
        protected static $tableName = '{table name}';

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

**Usage:**

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

If the name is ambiguous (like plural), use "Single", "Item", "Piece" as a postfix.

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

**Usage:**

        /**
         * {Description}
         */
        protected {column type} ${column name};

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

**Usage:**

        /**
         * @param PDOStatement $statement
         *
         * @return {ModelClass}|null
         */
        public static function fromResult(PDOStatement $statement): ?{ModelClass}
        {
            return static::get($statement);
        }