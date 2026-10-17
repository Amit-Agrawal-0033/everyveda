<?php
/**
 * EveryVeda HTTP Request Abstraction
 */

declare(strict_types=1);

namespace App\Core;

class Request
{
    private array $get;
    private array $post;
    private array $server;
    private array $files;
    private ?array $jsonPayload = null;

    public function __construct(array $get = [], array $post = [], array $server = [], array $files = [])
    {
        $this->get = $get ?: $_GET;
        $this->post = $post ?: $_POST;
        $this->server = $server ?: $_SERVER;
        $this->files = $files ?: $_FILES;
    }

    public static function capture(): self
    {
        return new self($_GET, $_POST, $_SERVER, $_FILES);
    }

    public function method(): string
    {
        $method = strtoupper($this->server['REQUEST_METHOD'] ?? 'GET');
        if ($method === 'POST' && isset($this->post['_method'])) {
            return strtoupper((string)$this->post['_method']);
        }
        return $method;
    }

    public function uri(): string
    {
        $uri = $this->server['REQUEST_URI'] ?? '/';
        $parts = explode('?', $uri, 2);
        return '/' . trim($parts[0], '/');
    }

    public function path(): string
    {
        return $this->uri();
    }

    public function get(string $key, mixed $default = null): mixed
    {
        return $this->get[$key] ?? $default;
    }

    public function post(string $key, mixed $default = null): mixed
    {
        return $this->post[$key] ?? $default;
    }

    public function input(string $key, mixed $default = null): mixed
    {
        return $this->post[$key] ?? $this->get[$key] ?? $this->json($key) ?? $default;
    }

    public function all(): array
    {
        return array_merge($this->get, $this->post, $this->jsonAll());
    }

    public function json(?string $key = null, mixed $default = null): mixed
    {
        if ($this->jsonPayload === null) {
            $raw = file_get_contents('php://input');
            $decoded = json_decode($raw, true);
            $this->jsonPayload = is_array($decoded) ? $decoded : [];
        }

        if ($key === null) {
            return $this->jsonPayload;
        }

        return $this->jsonPayload[$key] ?? $default;
    }

    public function jsonAll(): array
    {
        return $this->json() ?: [];
    }

    public function file(string $key): ?array
    {
        return $this->files[$key] ?? null;
    }

    public function header(string $name, mixed $default = null): mixed
    {
        $normalized = 'HTTP_' . strtoupper(str_replace('-', '_', $name));
        return $this->server[$normalized] ?? $default;
    }

    public function rawBody(): string
    {
        return file_get_contents('php://input');
    }

    public function isAjax(): bool
    {
        return strtolower((string)$this->header('X-Requested-With')) === 'xmlhttprequest'
            || str_contains((string)$this->header('Accept'), 'application/json');
    }

    public function isPost(): bool
    {
        return $this->method() === 'POST';
    }

    public function isGet(): bool
    {
        return $this->method() === 'GET';
    }

    public function ip(): string
    {
        return $this->server['REMOTE_ADDR'] ?? '127.0.0.1';
    }

    public function userAgent(): string
    {
        return $this->server['HTTP_USER_AGENT'] ?? '';
    }
}
