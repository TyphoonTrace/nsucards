<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer-6.6.3/src/Exception.php';
require 'PHPMailer-6.6.3/src/SMTP.php';
require 'PHPMailer-6.6.3/src/PHPMailer.php';

$mail = new PHPMailer(true);
$mail->CharSet = 'UTF-8';
$mail->setLanguage('ru', 'PHPMailer-6.6.3/language/');
$mail->IsHTML(true);

//$mail->setFrom('NSU_UNION_CARDSFORM@gmail.com', 'Форма карточек союза НГУ');

$mail->isSMTP();
//$mail->CharSet = "UTF-8";
$mail->SMTPAuth   = true;
//$mail->SMTPDebug = 2;
$mail->Debugoutput = function($str, $level) {$GLOBALS['status'][] = $str;};

//$mail->isSMTP();
//$mail->SMTPAuth = true;
//$mail->SMTPDebug = 0;

// Настройки вашей почты
$mail->Host = 'smtp.gmail.com'; // SMTP сервера вашей почты
$mail->Port = 465;
$mail->Username = 'nsu.union.cards'; // Логин на почте
$mail->Password = 'ncwrpmeuvtcpmygd'; // Пароль на почте
$mail->SMTPSecure = 'ssl';

// Адрес самой почты и имя отправителя
$mail->setFrom('nsu.union.cards@gmail.com', 'Форма для создания пропуска выпускника от СОЮЗА НГУ');



$mail->addAddress($_POST['email']);

$mail->Subject = 'Ваши данные для создания электронного пропуска выпускника НГУ';

$body = '<h1>Вы заполнили анкету для создания пропуска выпускника.</h1>';
$body .= '<h2> Пожалуйста, перед отправкой данных для печати, перепроверьте введенные вами данные:</h2>';


if (trim(!empty($_POST['SecondName']))) {
    $body .= '<p><strong>Фамилия:</strong> ' . $_POST['SecondName'] . '</p>';
}
if (trim(!empty($_POST['name']))) {
    $body .= '<p><strong>Имя:</strong> ' . $_POST['name'] . '</p>';
}
if (trim(!empty($_POST['Patronymic']))) {
    $body .= '<p><strong>Отчество:</strong> ' . $_POST['Patronymic'] . '</p>';
}

$gender = "Мужской";
if ($_POST['gender'] == "Female") {
    $gender = "Женский";
}

if (trim(!empty($_POST['gender']))) {
    $body .= '<p><strong>Пол:</strong> ' . $gender . '</p>';
}

if (trim(!empty($_POST['Birthday']))) {
    $body .= '<p><strong>Дата рождения:</strong> ' . $_POST['Birthday'] . '</p>';
}

if (trim(!empty($_POST['email']))) {
    $body .= '<p><strong>E-mail:</strong> ' . $_POST['email'] . '</p>';
}

if (trim(!empty($_POST['phone']))) {
    $body .= '<p><strong>Телефон:</strong> ' . $_POST['phone'] . '</p>';
}

if (trim(!empty($_POST['country']))) {
    $body .= '<p><strong>Страна:</strong> ' . $_POST['country'] . '</p>';
}

if (trim(!empty($_POST['city']))) {
    $body .= '<p><strong>Город:</strong> ' . $_POST['city'] . '</p>';
}


switch ($_POST['faculty']) {
    case "MMF":
        $faculty = "ММФ";
        break;
    case "FIT":
        $faculty = "ФИТ";
        break;
    case "FEN":
        $faculty = "ФЕН";
        break;
    case "FF":
        $faculty = "ФФ";
        break;
    case "EF":
        $faculty = "ЭФ";
        break;
}


if (trim(!empty($_POST['faculty']))) {
    $body .= '<p><strong>Факультет:</strong> ' . $faculty . '</p>';
}

if (trim(!empty($_POST['Year']))) {
    $body .= '<p><strong>Год выпуска:</strong> ' . $_POST['Year'] . '</p>';
}

if (!empty($_FILES['image']['tmp_name'])) {
    $filePath = __DIR__ . "/files/" . $_FILES['image']['name'];
    if (copy($_FILES['image']['tmp_name'], $filePath)) {
        $fileAttach = $filePath;
        $body .= '<p><strong>Фото в приложении: </strong>';
        try {
            $mail->addAttachment($fileAttach);
        } catch (Exception $e) {
        }
    }
}

$mail->Body = $body;

if (!$mail->send()) {
    $message = 'Ошибка';
} else {
    $message = 'Данные отправлены, проверьте указанную вами почту, возможно сообщение лежит в папке "Спам"';
}

$response = ['message' => $message];

header('Content-type: application/json');
echo json_encode($response);
?>