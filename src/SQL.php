<?php

// namespace Akerkour\SQL;

// use \PDO;
// use \PDOException;
// use \InvalidOrder;



require_once "Exceptions/NamekORMExceptions.php";

class WhereOperators extends SQL
{
    public function __construct()
    {
    }

    public function equals($field)
    {
        SQL::$queryStatment .= " = " .  $field;
        return $this;
    }

    public function notEqual($field)
    {
        SQL::$queryStatment .= " != " .  $field;
        return $this;
    }

    public function lessThan($field)
    {
        SQL::$queryStatment .= " < " .  $field;
        return $this;
    }

    public function moreOrEqual($field)
    {
        SQL::$queryStatment .= " > " .  $field;
        return $this;
    }

    public function lessOrEqual($field)
    {
        SQL::$queryStatment .= " <= " .  $field;
        return $this;
    }

    public function moreThan($field)
    {
        SQL::$queryStatment .= " >= " .  $field;
        return $this;
    }

    public function between($field1, $field2)
    {
        SQL::$queryStatment .= " BETWEEN " .  $field1 .  " AND " . $field2;
        return $this;
    }

    public function like($field)
    {
        SQL::$queryStatment .= " LIKE " .  $field;
        return $this;
    }
}



class SQL
{
    private $connection;
    private $tableName;
    protected static  $queryStatment;
    private $currentStatmentType;

    public function __construct(string $connection)
    {
        $this->connection = $connection;
        return $this;
    }

    public function select($ArrayOfFields = ["*"])
    {
        if ($this->currentStatmentType != null) {
            throw new InvalidOrder("you can not select if you are making another operation");
        }

        $this->currentStatmentType = "select";
        SQL::$queryStatment = "SELECT ";

        for ($i = 0; $i < count($ArrayOfFields); $i++) {
            SQL::$queryStatment .= $ArrayOfFields[$i] . ", ";
        }

        SQL::$queryStatment = rtrim(SQL::$queryStatment, ', ');

        return $this;
    }

    public function insertInto($tableName, $data)
    {
        if ($this->currentStatmentType != null) {
            throw new InvalidOrder("you can not Insert if you are making another operation");
        }

        $this->currentStatmentType = "insert";
        // Check if data is provided
        if (empty($data)) {
            return false;
        }

        // Connect to your database (modify these parameters accordingly)
        $host = 'localhost';
        $dbname = 'ORM';
        $username = 'root';
        $password = '';

        try {
            $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("Error: " . $e->getMessage());
        }

        // If $data is a single associative array, convert it to an array of arrays
        if (!is_array(reset($data))) {
            $data = [$data];
        }

        // Prepare the SQL statement with named placeholders
        $columns = implode(', ', array_keys($data[0]));
        $placeholders = ':' . implode(', :', array_keys($data[0]));
        $sql = "INSERT INTO $tableName ($columns) VALUES ($placeholders)";
        SQL::$queryStatment = $sql;
        // Prepare the statement
        $stmt = $pdo->prepare($sql);

        foreach ($data as $row) {
            // Bind parameters
            foreach ($row as $key => $value) {
                $paramType = $this->getPDODataType($value);
                $stmt->bindParam(':' . $key, $row[$key], $paramType);
            }

            // Execute the statement
            $stmt->execute();
        }

        // Close the database connection
        $pdo = null;

        return $this;
    }

    public function update($tableName, $data, $id)
    {
        if ($this->currentStatmentType != null) {
            throw new InvalidOrder("you can not update if you are making another operation");
        }
        $this->currentStatmentType = "update";

          // Connect to your database (modify these parameters accordingly)
          $host = 'localhost';
          $dbname = 'ORM';
          $username = 'root';
          $password = '';
  
          try {
              $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
              $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
          } catch (PDOException $e) {
              die("Error: " . $e->getMessage());
          }

        $args = array();

        foreach ($data as $key => $value) {
            $args[] = "$key = :$key ";
        }

        $sql = "UPDATE $tableName SET " .  implode(',', $args) . " WHERE id = :id";

        
        SQL::$queryStatment = $sql;

        $stmt = $pdo->prepare($sql);

        foreach ($data as $key => $value) {
            $paramType = $this->getPDODataType($value);
            $stmt->bindParam(':' . $key, $data[$key], $paramType);
        }

        $paramType = $this->getPDODataType($id);
        $stmt->bindParam(':id', $id, $paramType);

        $stmt->execute();

        return $this;
    }

    public function delete(){
        
    }


    public function from($tableName)
    {
        $this->tableName = $tableName;

        SQL::$queryStatment .=  " FROM " . $this->tableName;

        return $this;
    }

    public function where(string $field)
    {
        $operators = new WhereOperators();

        SQL::$queryStatment .= " WHERE " . $field;

        return $operators;
    }

    public function orderBy($columns, $oder = "ASC")
    {

        SQL::$queryStatment .= " ORDER BY";


        if (is_array($columns)) {
            for ($i = 0; $i < count($columns); $i++) {
                if (is_array($oder)) {
                    $orderKeyword = $oder[$i];
                } else {
                    $orderKeyword = $oder;
                }

                SQL::$queryStatment .= "$columns[$i] $orderKeyword";
            }

            SQL::$queryStatment = rtrim(SQL::$queryStatment, ',');
        } elseif (gettype($columns) == 'string') {

            SQL::$queryStatment  .= " $columns $oder";
        }

        return $this;
    }

    public function save()
    {
        // $query = $this->connection->prepare(SQL::$queryStatment);
        // $query->execute();
        // $result = $query->get_result();
        // $query->close();
        // return $result;

        echo SQL::$queryStatment . ";";
    }

    private function getPDODataType($value)
    {
        switch (gettype($value)) {
            case 'NULL':
                return PDO::PARAM_NULL;
            case 'boolean':
                return PDO::PARAM_BOOL;
            case 'integer':
                return PDO::PARAM_INT;
            case 'double':
                return PDO::PARAM_STR; // Assuming double as string
            case 'string':
                return PDO::PARAM_STR;
            default:
                return PDO::PARAM_STR; // Default to string for other types
        }
    }


}




$sql = new SQL("connection");

//$sql->select(["name"])->from("users")->where("ID")->between(15, 50)->orderBy("name", "DESC")->save();


// $sql->insertInto("users", [
//     [
//         "firstname" => "lahsen",
//         "lastname" => "sdff",
//         "age" => 10,
//         "number" => 0613525
//     ],
//     [
//         "firstname" => "merjani",
//         "lastname" => "gf",
//         "age" => 25,
//         "number" => 0613525
//     ],

// ])->save();

$sql->update("users", ["firstname" => "Massoud", "lastname" => "lmanhous"], 2)->save();
