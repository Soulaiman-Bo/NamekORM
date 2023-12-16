<?php


class WhereOperators extends SQL {

    public function __construct(){
    }

    //$validOperators = ['=', '<', '>', '<=', '>=', '!=', 'LIKE'];


    public function equals($field){
        SQL::$queryStatment .= " = " .  $field;
        return $this;
    }

    public function notEqual($field){
        SQL::$queryStatment .= " != " .  $field;
        return $this;
    }

    public function lessThan($field){
        SQL::$queryStatment .= " < " .  $field;
        return $this;
    }

    public function moreOrEqual($field){
        SQL::$queryStatment .= " > " .  $field;
        return $this;
    }

    public function lessOrEqual($field){
        SQL::$queryStatment .= " <= " .  $field;
        return $this;
    }

    public function moreThan($field){
        SQL::$queryStatment .= " >= " .  $field;
        return $this;
    }

    public function between($field1, $field2){
        SQL::$queryStatment .= " BETWEEN " .  $field1 .  " AND " . $field2;
        return $this;
    }

    public function like($field){
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

        $this->currentStatmentType = "select";
        SQL::$queryStatment = "SELECT ";

        for ($i = 0; $i < count($ArrayOfFields); $i++) {
            SQL::$queryStatment .= $ArrayOfFields[$i] . ", ";
        }

        SQL::$queryStatment = rtrim(SQL::$queryStatment, ', ');

        return $this;
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

    public function save()
    {
        // $query = $this->connection->prepare(SQL::$queryStatment);
        // $query->execute();
        // $result = $query->get_result();
        // $query->close();
        // return $result;

        echo SQL::$queryStatment . ";";
    }

}




$sql = new SQL("connection");

$sql->select()->from("users")->where("ID")->between(15, 50)->save();


