<?php

class Validation
{
    public static function validate(array $data, array $rules): array
    {
        $errors = [];

        foreach ($rules as $field => $fieldRules) {
            $fieldValue = $data[$field] ?? null;
            $rulesList = is_array($fieldRules) ? $fieldRules : explode('|', $fieldRules);

            foreach ($rulesList as $rule) {
                $ruleName = $rule;
                $ruleParam = null;

                if (strpos($rule, ':') !== false) {
                    [$ruleName, $ruleParam] = explode(':', $rule, 2);
                }

                $result = self::runRule($field, $fieldValue, $ruleName, $ruleParam, $data);
                if ($result !== true) {
                    $errors[$field][] = $result;
                }
            }
        }

        return $errors;
    }

    protected static function runRule(string $field, $value, string $rule, $param = null, array $data = []): bool|string
    {
        switch ($rule) {
            case 'required':
                if ($value === null || trim((string) $value) === '') {
                    return 'الحقل ' . $field . ' مطلوب';
                }
                return true;

            case 'email':
                if ($value !== null && $value !== '' && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    return 'الحقل ' . $field . ' يجب أن يكون بريد إلكتروني صحيح';
                }
                return true;

            case 'min':
                if ($value !== null && is_string($value) && mb_strlen($value) < (int) $param) {
                    return 'الحقل ' . $field . ' يجب أن يكون طوله على الأقل ' . $param . ' أحرف';
                }
                return true;

            case 'max':
                if ($value !== null && is_string($value) && mb_strlen($value) > (int) $param) {
                    return 'الحقل ' . $field . ' يجب أن يكون طوله أقصى ' . $param . ' أحرف';
                }
                return true;

            case 'numeric':
                if ($value !== null && $value !== '' && !is_numeric($value)) {
                    return 'الحقل ' . $field . ' يجب أن يكون رقماً';
                }
                return true;

            case 'in':
                $allowed = explode(',', (string) $param);
                if ($value !== null && !in_array($value, $allowed, true)) {
                    return 'الحقل ' . $field . ' غير موجود ضمن القيم المسموح بها';
                }
                return true;

            case 'confirmed':
                $confirmKey = $field . '_confirmation';
                if (($data[$confirmKey] ?? null) !== $value) {
                    return 'تأكيد الحقل ' . $field . ' غير متطابق';
                }
                return true;

            default:
                return true;
        }
    }
}

