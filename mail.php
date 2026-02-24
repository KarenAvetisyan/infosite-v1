<?php
declare(strict_types=1);

header('Content-Type: text/plain; charset=UTF-8');
mb_internal_encoding('UTF-8');

// ================================
// BASIC SECURITY
// ================================

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(403);
    exit('Forbidden');
}

// Honeypot (добавь скрытое поле website в форму)
if (!empty($_POST['website'] ?? '')) {
    exit('Spam blocked');
}

// Time protection (добавь скрытое поле form_time)
// if (isset($_POST['form_time']) && (time() - (int)$_POST['form_time']) < 3) {
//     exit('Bot detected');
// }

// ================================
// MAIL SETTINGS
// ================================

$to       = "yourmail@gmail.com";   // куда отправлять
$from     = "info@your-domain.ru";    // должен быть реальный доменный email
$fromName = "Заявка с сайта";

// ================================
// FIELD DEFINITIONS (твои поля)
// ================================

$fields = [
    'name'    => 'Имя',
    'company' => 'Компания',
    'email'   => 'Email',
    'phone'   => 'Телефон',
    'message' => 'Сообщение',
];

// ================================
// SANITIZE FUNCTION
// ================================

function clean(string $value): string {
    $value = trim($value);
    $value = str_replace(["\r", "\n", "%0a", "%0d"], '', $value);
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

// ================================
// VALIDATION
// ================================

$errors = [];

if (empty($_POST['company'])) {
    $errors[] = 'Компания обязательна';
}

if (empty($_POST['email']) || !filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'Некорректный email';
}

if (empty($_POST['phone'])) {
    $errors[] = 'Телефон обязателен';
}

if (empty($_POST['_form-group__agree-input']) && empty($_POST['agree'])) {
    // checkbox
}

// если есть ошибки
if (!empty($errors)) {
    http_response_code(400);
    echo implode("\n", $errors);
    exit;
}

// ================================
// BUILD MESSAGE
// ================================

$message  = "<h2>Новая заявка</h2>";
$message .= "<table border='1' cellpadding='8' cellspacing='0' style='border-collapse:collapse;font-family:Arial'>";

foreach ($fields as $key => $label) {

    if (!empty($_POST[$key])) {

        $value = clean((string)$_POST[$key]);

        $message .= "
        <tr>
            <td style='background:#f5f5f5;font-weight:bold'>$label</td>
            <td>$value</td>
        </tr>";
    }
}

$message .= "</table>";

// ================================
// SUBJECT
// ================================

$subject = "Новая заявка с сайта";
$subject = "=?UTF-8?B?" . base64_encode($subject) . "?=";

// ================================
// HEADERS
// ================================

$headers  = "From: " . mb_encode_mimeheader($fromName, "UTF-8") . " <$from>\r\n";
$headers .= "Reply-To: " . clean($_POST['email'] ?? $from) . "\r\n";
$headers .= "MIME-Version: 1.0\r\n";
$headers .= "Content-Type: text/html; charset=UTF-8\r\n";
$headers .= "X-Mailer: PHP/" . phpversion();

// ================================
// SEND
// ================================

if (mail($to, $subject, $message, $headers)) {
    echo "OK";
} else {
    http_response_code(500);
    echo "ERROR";
}
?>