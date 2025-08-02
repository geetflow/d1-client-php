# 🎧 D1 Client PHP

A lightweight PHP client for connecting to a [D1-compatible WebSocket worker](https://github.com/geetflow/d1_connector) – enabling MySQL-like query syntax over WebSockets.

---

## 🔗 Worker Backend

* GitHub Repository: [https://github.com/geetflow/d1\_connector](https://github.com/geetflow/d1_connector)
* Supports WebSocket endpoint: `/ws?username=&password=&database=`
* Compatible with Cloudflare Workers and D1 databases

---

## 📦 Installation

### Requirements

* PHP 7.4+
* Composer
* [ext-sockets](https://www.php.net/manual/en/sockets.installation.php) (for WebSocket support)

### Install with Composer

```bash
composer require geetflow/d1-client-php
```

> Make sure the `ws`/`wss` transport is enabled in your PHP (`php.ini` or runtime config).

---

## 📁 Project Structure

```
d1-client-php/
├── composer.json
├── vendor/                 # Installed by composer
├── src/
│   └── D1Connection.php    # Main implementation
├── examples/
│   └── test.php            # Usage example
└── README.md
```

---

## ✅ Quick Start

### Example: `examples/test.php`

```php
<?php

require __DIR__ . '/../vendor/autoload.php';

use D1Client\D1Connection;

$conn = new D1Connection(
    "d1_connector.wispy-wildflower-4850.workers.dev",
    "root",
    "Sita9950k",
    "music"
);

$cursor = $conn->cursor();
$cursor->execute("SELECT * FROM tracks LIMIT 1");

$result = $cursor->fetchall();

print_r($result);
```

---

## 🧠 API Reference

### `D1Connection`

| Method                                                | Description                         |
| ----------------------------------------------------- | ----------------------------------- |
| `__construct($host, $username, $password, $database)` | Connects to the WebSocket D1 worker |
| `cursor()`                                            | Returns a new `D1Cursor` instance   |
| `close()`                                             | Closes the WebSocket connection     |

---

### `D1Cursor`

| Method                        | Description                                     |
| ----------------------------- | ----------------------------------------------- |
| `execute($sql, $params = [])` | Executes the SQL statement                      |
| `fetchall()`                  | Returns all results as an array                 |
| `fetchone()`                  | Returns a single row (first) or `null` if empty |

---

## 🌐 WebSocket Worker Protocol

Your backend should handle requests like:

```
GET ws://host/ws?username=...&password=...&database=...
```

It expects JSON messages like:

```json
{
  "sql": "SELECT * FROM tracks",
  "params": []
}
```

And responds with:

```json
{
  "result": {
    "results": [ { ... row ... }, ... ]
  }
}
```

In case of error:

```json
{
  "error": "Message about failure"
}
```

> Full worker example here: [https://github.com/geetflow/d1\_connector](https://github.com/geetflow/d1_connector)

---

## 🔒 Security Notes

* Always validate and sanitize SQL queries and parameters.
* Use HTTPS (WSS) and authenticate your WebSocket connections.
* In production, restrict database access using env variables.

---

## ⚖️ License

MIT License

© 2025 [GeetFlow](https://github.com/geetflow)

---

Would you like help pushing this to **Packagist**, building `.phar`, or deploying it as a **Dockerized API service**?
