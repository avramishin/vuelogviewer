<?php
require_once __DIR__ . "/config.php";

class MainController
{
    var $cfg;

    function __construct()
    {
        $this->checkEnvironment();
        $this->cfg = require __DIR__ . "/config.php";
        $action = $this->getRequest("action", "index");
        $method = "{$action}Action";

        if (!method_exists($this, $method)) {
            die("Unknown action");
        }

        $this->handleCORS();
        $this->{$method}();
    }

    function getRecordsAction()
    {
        $user = $this->getAuthUser();
        try {
            $source = $this->getRequest("source");

            if (!in_array($source, $user["sources"])) {
                throw new Exception("User does not have source: {$source}");
            }

            if (!isset($this->cfg["database_sources"][$source])) {
                throw new Exception("Invalid source name: {$source}");
            }

            $sort = $this->getRequest("sort", "id");
            $order = $this->getRequest("order", "DESC");
            $limit = (int)$this->getRequest("rows", 50);
            $page = $this->getRequest("page", 1);
            $filter = $this->getRequest("query");

            $offset = ($page - 1) * $limit;

            $table = $this->cfg["database_sources"][$source]["table"];
            $pdo = $this->getPdo($this->cfg["database_sources"][$source]);
            $sql = ["SELECT SQL_CALC_FOUND_ROWS * FROM {$table} WHERE 1=1"];

            if ($filter) {
                $sql[] = "AND (message LIKE :filter OR channel LIKE :filter OR extra LIKE :filter OR context LIKE :filter)";
            }

            $sql[] = "ORDER BY `{$sort}` {$order} LIMIT :limit OFFSET :offset";

            $dataQuery = $pdo->prepare(join(" ", $sql));
            $dataQuery->bindParam(":limit", $limit, PDO::PARAM_INT);
            $dataQuery->bindParam(":offset", $offset, PDO::PARAM_INT);
            if ($filter) {
                $wideFilter = "%{$filter}%";
                $dataQuery->bindParam(":filter", $wideFilter);
            }

            $dataQuery->execute();
            $records = $dataQuery->fetchAll();
            $foundRows = $pdo->query("SELECT FOUND_ROWS()")->fetchColumn();

            $this->jsonResponse([
                "rows" => $records,
                "total" => $foundRows
            ]);
        } catch (Exception $e) {
            $this->jsonResponse(["error" => $e->getMessage()]);
        }
    }

    function getSourcesAction()
    {
        $user = $this->getAuthUser();
        $this->jsonResponse([
            "sources" => $user["sources"]
        ]);
    }

    function logoutAction()
    {
        setcookie("vue_auth_token", "", 0, '/');
        $this->jsonResponse([]);
    }

    function loginAction()
    {
        try {

            $username = $this->getRequest("username");
            $password = $this->getRequest("password");
            $authenticated = false;

            foreach ($this->cfg["users"] as $user) {
                if ($user["username"] == $username) {
                    if ($user["password"] == $password) {
                        $authenticated = $user;
                        break;
                    }
                }
            }

            if (!$authenticated) {
                throw new Exception("Username or password is incorrect");
            }

            $expires = time() + 3600 * 24 * 14;

            $jwt = [
                "user" => $username,
                "expires" => $expires,
                "sign" => substr(md5($username . $expires . $this->cfg["app_secret"]), 0, 16)
            ];

            $auth_token = base64_encode(json_encode($jwt));
            setcookie("vue_auth_token", $auth_token, $expires, '/');
            $this->jsonResponse([
                "username" => $username,
                "sources" => $authenticated["sources"],
                "auth_token" => $auth_token,
                "expires" => date("Y-m-d H:i:s", $expires)
            ]);
        } catch (Exception $e) {
            $this->jsonResponse(["error" => $e->getMessage()]);
        }
    }

    function indexAction()
    {
        require_once __DIR__ . "/index.phtml";
    }

    function getAuthUser()
    {
        try {

            if (!isset($_COOKIE["vue_auth_token"])) {
                throw new Exception("Authentication required");
            }

            $jwt = @json_decode(@base64_decode($_COOKIE["vue_auth_token"]));
            if (!isset($jwt->user) || !isset($jwt->expires) || !isset($jwt->sign)) {
                throw new Exception("Invalid auth token, try to login again");
            }

            if ($jwt->expires < time()) {
                throw new Exception("Session has expires, try to login again");
            }

            $sign = substr(md5($jwt->user . $jwt->expires . $this->cfg["app_secret"]), 0, 16);
            if ($sign != $jwt->sign) {
                throw new Exception("Invalid signature, try to login again");
            }

            foreach ($this->cfg["users"] as $user) {
                if ($user["username"] == $jwt->user) {
                    return $user;
                }
            }

            throw new Exception("User not found");
        } catch (Exception $e) {
            http_response_code(401);
            $this->jsonResponse(["error" => $e->getMessage()]);
        }
    }

    function getPdo($conn)
    {
        $dsn = "mysql:host={$conn["host"]};dbname={$conn["name"]}";

        $opt = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ
        ];

        return new PDO($dsn, $conn["user"], $conn["pass"], $opt);
    }

    function checkEnvironment()
    {
        if (!class_exists("PDO")) {
            die("PDO extension not installed");
        }

        if (!file_exists(__DIR__ . "/config.php")) {
            die("File config.php not found");
        }
    }

    function handleCORS()
    {
        $origin = isset($_SERVER["HTTP_ORIGIN"]) ? $_SERVER["HTTP_ORIGIN"] : "*";
        header("Access-Control-Allow-Origin: {$origin}");
        header("Access-Control-Allow-Credentials: true");
        header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
        if (isset($_SERVER["HTTP_ACCESS_CONTROL_REQUEST_HEADERS"])) {
            header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");
        }

        if ($_SERVER["REQUEST_METHOD"] == "OPTIONS") {
            exit(0);
        }
    }

    function jsonResponse($response)
    {
        header('Content-Type: application/json');
        echo json_encode($response, JSON_PRETTY_PRINT);
        flush();
        exit();
    }

    function getRequest($key, $default = "")
    {
        if (isset($_REQUEST[$key])) {
            if (is_array($_REQUEST[$key])) {
                return $_REQUEST[$key];
            }

            return trim($_REQUEST[$key]);
        }

        return $default;
    }
}

new MainController();
