<?php
/**
 * EveryVeda HTTP Response Handler
 */

declare(strict_types=1);

namespace App\Core;

class Response
{
    private int $statusCode = 200;
    private array $headers = [];
    private string $content = '';

    public function __construct(string $content = '', int $statusCode = 200, array $headers = [])
    {
        $this->content = $content;
        $this->statusCode = $statusCode;
        $this->headers = $headers;
    }

    public function setStatusCode(int $code): self
    {
        $this->statusCode = $code;
        return $this;
    }

    public function header(string $name, string $value): self
    {
        $this->headers[$name] = $value;
        return $this;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;
        return $this;
    }

    public function send(): void
    {
        if (!headers_sent()) {
            http_response_code($this->statusCode);

            // Default security headers
            if (!isset($this->headers['X-Content-Type-Options'])) {
                header('X-Content-Type-Options: nosniff');
            }
            if (!isset($this->headers['X-Frame-Options'])) {
                header('X-Frame-Options: SAMEORIGIN');
            }
            if (!isset($this->headers['X-XSS-Protection'])) {
                header('X-XSS-Protection: 1; mode=block');
            }
            if (!isset($this->headers['Referrer-Policy'])) {
                header('Referrer-Policy: strict-origin-when-cross-origin');
            }

            foreach ($this->headers as $name => $value) {
                header("{$name}: {$value}");
            }
        }

        echo $this->content;
    }

    public static function json(mixed $data, int $status = 200): void
    {
        $response = new self(json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), $status, [
            'Content-Type' => 'application/json; charset=utf-8'
        ]);
        $response->send();
        exit;
    }

    public static function redirect(string $url, int $status = 302): void
    {
        if (!headers_sent()) {
            http_response_code($status);
            header("Location: {$url}");
        } else {
            echo "<script>window.location.href=" . json_encode($url) . ";</script>";
        }
        exit;
    }

    public static function download(string $filePath, ?string $filename = null, string $contentType = 'application/octet-stream'): void
    {
        if (!file_exists($filePath)) {
            http_response_code(404);
            echo "File not found.";
            exit;
        }

        $filename = $filename ?: basename($filePath);

        header('Content-Description: File Transfer');
        header('Content-Type: ' . $contentType);
        header('Content-Disposition: attachment; filename="' . addslashes($filename) . '"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($filePath));
        readfile($filePath);
        exit;
    }

    public static function csv(string $filename, array $headers, array $rows): void
    {
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . addslashes($filename) . '"');
        header('Pragma: no-cache');
        header('Expires: 0');

        $output = fopen('php://output', 'w');
        // UTF-8 BOM for Excel compatibility
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

        fputcsv($output, $headers);

        foreach ($rows as $row) {
            $sanitizedRow = array_map('sanitize_csv_cell', $row);
            fputcsv($output, $sanitizedRow);
        }

        fclose($output);
        exit;
    }
}
