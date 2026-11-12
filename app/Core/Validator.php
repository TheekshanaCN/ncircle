<?php
/**
 * app/Core/Validator.php
 *   Validator::check($data, ['email' => 'required|email|max:120'], ['email' => 'University email']);
 * Rules: required, email, nsbm_email, password, int, min:n, max:n, between:a,b, in:a,b, same:field, date, future
 */
class Validator
{
    public static function check(array $data, array $rules, array $labels = []): array
    {
        $errors = [];
        foreach ($rules as $field => $ruleText) {
            $value = $data[$field] ?? '';
            $label = $labels[$field] ?? ucfirst(str_replace('_', ' ', $field));
            foreach (explode('|', $ruleText) as $rule) {
                [$name, $arg] = array_pad(explode(':', $rule, 2), 2, null);
                $message = self::test($name, $arg, $value, $data, $label, $labels);
                if ($message !== null) {
                    $errors[$field] = $message;
                    break;
                }
            }
        }
        return $errors;
    }

    private static function test(string $name, ?string $arg, $value, array $data, string $label, array $labels): ?string
    {
        $text = is_string($value) ? $value : '';
        if ($name === 'required') {
            return $text === '' ? "{$label} is required." : null;
        }
        if ($text === '') {
            return null;   // optional and empty: nothing else to check
        }
        switch ($name) {
            case 'email':
                return filter_var($text, FILTER_VALIDATE_EMAIL) ? null : 'Enter a valid email address.';
            case 'nsbm_email':
                $domain = config('app.student_email_domain');
                return str_ends_with(strtolower($text), '@' . $domain) ? null : "Please use official NSBM domain (@{$domain}).";
            case 'password':
                return strlen($text) >= 8 && preg_match('/[A-Z]/', $text) && preg_match('/\d/', $text)
                    ? null : 'Use 8+ characters with an uppercase letter and a number.';
            case 'int':
                return ctype_digit($text) ? null : "{$label} must be a whole number.";
            case 'min':
                return mb_strlen($text) >= (int) $arg ? null : "{$label} must be at least {$arg} characters.";
            case 'max':
                return mb_strlen($text) <= (int) $arg ? null : "{$label} must be {$arg} characters or fewer.";
            case 'between':
                [$low, $high] = explode(',', (string) $arg);
                return ctype_digit($text) && (int) $text >= (int) $low && (int) $text <= (int) $high
                    ? null : "{$label} must be between {$low} and {$high}.";
            case 'in':
                return in_array($text, explode(',', (string) $arg), true) ? null : "Choose a valid {$label}.";
            case 'same':
                return $text === ($data[$arg] ?? null) ? null : 'Passwords do not match. Please verify confirmation.';
            case 'date':
                return strtotime($text) !== false ? null : "Enter a valid {$label}.";
            case 'future':
                return strtotime($text) > time() ? null : "{$label} must be in the future.";
        }
        return null;
    }
}
