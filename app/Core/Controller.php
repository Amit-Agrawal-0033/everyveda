<?php
/**
 * EveryVeda Base Controller
 */

declare(strict_types=1);

namespace App\Core;

abstract class Controller
{
    /**
     * Render a view within standard layout
     */
    protected function view(string $viewPath, array $data = [], ?string $layout = 'layouts/main'): void
    {
        View::render($viewPath, $data, $layout);
    }

    /**
     * Render an admin view within admin layout
     */
    protected function adminView(string $viewPath, array $data = []): void
    {
        View::render($viewPath, $data, 'layouts/admin');
    }

    /**
     * Return JSON response
     */
    protected function json(mixed $data, int $statusCode = 200): void
    {
        Response::json($data, $statusCode);
    }

    /**
     * Redirect with optional flash message
     */
    protected function redirect(string $url, ?string $flashType = null, ?string $flashMessage = null): void
    {
        if ($flashType && $flashMessage) {
            set_flash($flashType, $flashMessage);
        }
        Response::redirect($url);
    }

    /**
     * Verify CSRF token from Request POST
     */
    protected function validateCsrf(Request $request): void
    {
        $token = $request->post('_csrf_token') ?? $request->header('X-CSRF-TOKEN');
        if (!verify_csrf_token($token)) {
            http_response_code(403);
            if ($request->isAjax()) {
                Response::json(['error' => 'Invalid or expired CSRF security token.'], 403);
            } else {
                set_flash('error', 'Session expired. Please try submitting the form again.');
                Response::redirect($_SERVER['HTTP_REFERER'] ?? '/');
            }
        }
    }

    /**
     * Validate array of inputs against rules
     * Returns array of error messages keyed by field, or empty array if valid
     */
    protected function validate(array $data, array $rules): array
    {
        $errors = [];

        foreach ($rules as $field => $ruleList) {
            $rulesArray = is_array($ruleList) ? $ruleList : explode('|', $ruleList);
            $val = $data[$field] ?? null;

            foreach ($rulesArray as $rule) {
                $params = [];
                if (str_contains($rule, ':')) {
                    [$rule, $paramStr] = explode(':', $rule, 2);
                    $params = explode(',', $paramStr);
                }

                switch ($rule) {
                    case 'required':
                        if ($val === null || (is_string($val) && trim($val) === '') || (is_array($val) && empty($val))) {
                            $errors[$field] = ucfirst(str_replace('_', ' ', $field)) . ' is required.';
                        }
                        break;

                    case 'email':
                        if ($val !== null && trim((string)$val) !== '' && !filter_var($val, FILTER_VALIDATE_EMAIL)) {
                            $errors[$field] = 'Please provide a valid email address.';
                        }
                        break;

                    case 'min':
                        $min = (int)($params[0] ?? 0);
                        if (is_string($val) && mb_strlen($val) < $min) {
                            $errors[$field] = ucfirst(str_replace('_', ' ', $field)) . " must be at least {$min} characters.";
                        }
                        break;

                    case 'max':
                        $max = (int)($params[0] ?? 255);
                        if (is_string($val) && mb_strlen($val) > $max) {
                            $errors[$field] = ucfirst(str_replace('_', ' ', $field)) . " must not exceed {$max} characters.";
                        }
                        break;

                    case 'numeric':
                        if ($val !== null && !is_numeric($val)) {
                            $errors[$field] = ucfirst(str_replace('_', ' ', $field)) . ' must be a valid number.';
                        }
                        break;

                    case 'phone':
                        // Indian 10-digit phone format check
                        if ($val !== null && !preg_match('/^[6-9]\d{9}$/', preg_replace('/[^\d]/', '', (string)$val))) {
                            $errors[$field] = 'Please provide a valid 10-digit Indian mobile number.';
                        }
                        break;

                    case 'pincode':
                        // Indian 6-digit postal code check
                        if ($val !== null && !preg_match('/^[1-9]\d{5}$/', trim((string)$val))) {
                            $errors[$field] = 'Please provide a valid 6-digit Indian PIN code.';
                        }
                        break;

                    case 'confirmed':
                        $confirmationField = $field . '_confirmation';
                        if (($data[$confirmationField] ?? null) !== $val) {
                            $errors[$field] = ucfirst(str_replace('_', ' ', $field)) . ' confirmation does not match.';
                        }
                        break;
                }

                if (isset($errors[$field])) {
                    break; // Stop evaluating further rules for this field on first failure
                }
            }
        }

        return $errors;
    }
}
