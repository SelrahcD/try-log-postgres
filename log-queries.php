<?php

declare(strict_types=1);
//
//class QueryLogger {
//
//    public static array $logs = [];
//}
//class LogPDO extends PDO {
//
//    private array $logs = [];
//
//    public function query($statement, ?int $mode = null, mixed ...$fetch_mode_args): PDOStatement|false
//    {
//        $result = parent::query($statement, $mode, $fetch_mode_args);
//        QueryLogger::$logs[] = 'Query: ' . $statement;
//
//        return $result;
//    }
//
//    public function prepare(string $query, array $options = []): PDOStatement|false
//    {
//        return new LoggedPDOStatement(parent::prepare($query, $options));
//    }
//}
//
//class LoggedPDOStatement extends PDOStatement
//{
//    public function __construct(
//        private readonly PDOStatement $statement)
//    {
//    }
//
//    public function execute(?array $params = null): bool
//    {
//        $result = $this->statement->execute($params);
//        QueryLogger::$logs[] = 'Statement: ' . $this->statement->queryString . ' - ' . var_export($params, true);
//
//        return $result;
//    }
//
//    public function fetchAll(int $mode = PDO::FETCH_BOTH, ...$args): array
//    {
//        $this->statement->fetchAll($mode, $args);
//    }
//
//
//    public function __call($function, $parameters) {
//        return call_user_func_array(array($this->statement, $function), $parameters);
//    }
//}

$postgresLogFile = __DIR__ . '/postgres-logs/postgresql.log';
//unlink($postgresLogFile);
file_put_contents($postgresLogFile, '');

$dsn = "pgsql:host=localhost;port=5432;dbname=postgres;user=postgres;password=postgres";
$db = new PDO($dsn);

$stmt = $db->query("SELECT * FROM names");

$a = $stmt->fetchAll();
var_dump($a);

$sql = 'SELECT * FROM names WHERE name = :name ORDER BY name ASC';
$stmt = $db->prepare($sql);
$stmt->execute(['name' => 'Bla']);
$b = $stmt->fetchAll();

$sql = 'SELECT * FROM names WHERE name = :name ORDER BY name ASC';
$stmt = $db->prepare($sql);
$stmt->execute(['name' => 'Bla']);
$b = $stmt->fetchAll();

$sql = 'INSERT INTO names VALUES(:name)';
$stmt = $db->prepare($sql);
$stmt->execute(['name' => 'Blu']);
$b = $stmt->fetchAll();

var_dump($b);

usleep(10000);

$postgresLog = file_get_contents($postgresLogFile);
//var_dump($postgresLog);

preg_match_all('/execute .+:(.+)|DETAIL: (.+)/', $postgresLog, $matches);

foreach (array_keys($matches[0]) as $key) {
    $query = $matches[1][$key];
    if($query) {
        echo $query . PHP_EOL;
    }
    $parameters = $matches[2][$key];
    if($parameters) {
        echo $parameters . PHP_EOL;
    }
}