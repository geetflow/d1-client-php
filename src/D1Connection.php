<?php
namespace D1Client;

use WebSocket\Client;
use WebSocket\ConnectionException;

/**
 * Class D1Cursor - Handles SQL execution and result retrieval over WebSocket
 */
class D1Cursor
{
    private Client $client;
    private ?array $lastResult = null;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * Execute an SQL query
     *
     * @param string $query
     * @param array $params
     * @throws \Exception
     */
    public function execute(string $query, array $params = []): void
    {
        $payload = [
            "sql" => $query,
            "params" => $params
        ];

        $this->client->send(json_encode($payload));
        $response = $this->client->receive();

        $data = json_decode($response, true);

        if (isset($data["error"])) {
            throw new \Exception("Server error: " . $data["error"]);
        }

        $this->lastResult = $data["result"];
    }

    /**
     * Fetch all rows from last result
     *
     * @return array
     */
    public function fetchall(): array
    {
        return $this->lastResult["results"] ?? [];
    }

    /**
     * Fetch only the first row
     *
     * @return array|null
     */
    public function fetchone(): ?array
    {
        $rows = $this->fetchall();
        return $rows[0] ?? null;
    }
}

/**
 * Class D1Connection - Manages WebSocket connection to the D1 worker
 */
class D1Connection
{
    private Client $client;
    private ?D1Cursor $cursor = null;

    /**
     * D1Connection constructor
     *
     * @param string $host WebSocket host (e.g., d1.example.workers.dev)
     * @param string $username
     * @param string $password
     * @param string $database
     * @throws \Exception
     */
    public function __construct(string $host, string $username, string $password, string $database)
    {
        $url = "ws://$host/ws?username=$username&password=$password&database=$database";

        try {
            $this->client = new Client($url, ['timeout' => 10]);
        } catch (\Exception $e) {
            throw new \Exception("WebSocket connection failed: " . $e->getMessage());
        }
    }

    /**
     * Returns a reusable cursor object
     *
     * @return D1Cursor
     */
    public function cursor(): D1Cursor
    {
        if (!$this->cursor) {
            $this->cursor = new D1Cursor($this->client);
        }
        return $this->cursor;
    }

    /**
     * Close the WebSocket connection
     */
    public function close(): void
    {
        if (isset($this->client)) {
            try {
                $this->client->close();
            } catch (ConnectionException $e) {
                // Silently ignore if already disconnected
            }
        }
    }

    public function __destruct()
    {
        $this->close();
    }
}
