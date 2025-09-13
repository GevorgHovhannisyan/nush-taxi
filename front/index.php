<?php
// Database connection
$host = "sql7.freesqldatabase.com";
$user = "sql7798079";
$password = "4b33qZKayY";
$dbname = "sql7798079";

$conn = new mysqli($host, $user, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$message = "";
$messageClass = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $userId = $_POST['userId'] ?? '';
    $code   = $_POST['code'] ?? '';
    $passportId   = $_POST['passportId'] ?? '';

    if ($userId && $code) {
        $stmt = $conn->prepare("INSERT INTO LotteryEntries (UserId, Code, PassportId) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $userId, $code, $passportId);

        try {
            $stmt->execute();
            $message = "✅ Ստացվեց! Քո կոդը <b>" . htmlspecialchars($code) . "</b> ավելացված է: Խնդրում ենք հիշել ձեր կոդը խաղարկության ժամանակ";
            $messageClass = "success";
        } catch (mysqli_sql_exception $e) {
            if ($e->getCode() === 1062) { // Duplicate entry
                $message = "❌ Այս կոդը կամ վարորդականը արդեն առկա է, խնդրում ենք փորձել այլ կոդ կամ վարորդական";
                $messageClass = "error";
            } else {
                $message = "⚠️ Ինչ որ բան այն չէ.";
                $messageClass = "error";
            }
        }

        $stmt->close();
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Lottery Entry</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background: #f7f7f7;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
    }
    .form-container {
      background: #fff;
      padding: 20px 30px;
      border-radius: 10px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.2);
      width: 320px;
    }
    h2 {
      text-align: center;
      margin-bottom: 15px;
    }
    label {
      display: block;
      margin: 10px 0 5px;
    }
    input[type="text"] {
      width: 100%;
      padding: 8px;
      border: 1px solid #ddd;
      border-radius: 5px;
    }
    button {
      margin-top: 15px;
      width: 100%;
      padding: 10px;
      background: #007bff;
      border: none;
      color: #fff;
      border-radius: 5px;
      cursor: pointer;
    }
    .success {
      margin-top: 15px;
      padding: 10px;
      background: #d4edda;
      color: #155724;
      border-radius: 5px;
    }
    .error {
      margin-top: 15px;
      padding: 10px;
      background: #f8d7da;
      color: #721c24;
      border-radius: 5px;
    }
  </style>
</head>
<body>
  <div class="form-container">
    <h2>Նուշ</h2>
    <form action="" method="POST">
      <label for="userId">Անուն Ազգանուն:</label>
      <input type="text" id="userId" name="userId" required>

      <label for="userId">Վարորդական վկայական:</label>
      <input type="text" id="passportId" name="passportId" required>

      <label for="code">Խաղարկության կոդ ( 4 նիշ ):</label>
      <input type="text" id="code" name="code" required maxlength="4" pattern="\d{4}" title="Enter 4 digits">

      <button type="submit">Ուղարկել</button>
    </form>

    <?php if ($message): ?>
      <div class="<?= $messageClass ?>"><?= $message ?></div>
    <?php endif; ?>
  </div>
</body>
</html>
