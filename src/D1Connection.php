<?php
namespace D1Client;

use WebSocket\Client;

class D1Cursor
{
    private $client;
    private $lastResult = null;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function execute(string $query, array $params = [])
    {
        $this->client->send(json_encode([
            "sql" => $query,
            "params" => $params
        ]));

        $response = $this->client->receive();
        $data = json_decode($response, true);

        if (isset($data["error"])) {
            throw new \Exception("Server error: " . $data["error"]);
        }

        $this->lastResult = $data["result"];
    }

    public function fetchall(): array
    {
        return $this->lastResult["results"] ?? [];
    }

    public function fetchone(): ?array
    {
        $all = $this->fetchall();
        return $all[0] ?? null;
    }
}

class D1Connection
{
    private $client;
    private $cursor;

    public function __construct(string $host, string $username, string $password, string $database)
    {
        $url = "ws://$host/ws?username=$username&password=$password&database=$database";
        $this->client = new Client($url);
        $this->cursor = new D1Cursor($this->client);
    }

    public function cursor(): D1Cursor
    {
        return $this->cursor;
    }

    public function close()
    {
        if ($this->client) {
            $this->client->close();
        }
    }

    public function __destruct()
    {
        $this->close();
    }
}
