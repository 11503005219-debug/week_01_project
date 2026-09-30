<?php
declare(strict_types=1);

// Decision: collect every error instead of returning on the first, so users can fix all problems in one pass.
function validateProfile(array $profile): array
{
    $errors = [];

    $name = isset($profile["name"]) && is_string($profile["name"]) ? trim($profile["name"]) : "";
    if ($name === "" || mb_strlen($name) < 3) {
        $errors[] = "Name is required and must contain at least 3 characters.";
    }

    $age = $profile["age"] ?? null;
    if (!is_int($age) || $age < 0 || $age > 120) {
        $errors[] = "Age must be a whole number between 0 and 120.";
    }

    $dzongkhag = isset($profile["dzongkhag"]) && is_string($profile["dzongkhag"]) ? trim($profile["dzongkhag"]) : "";
    if ($dzongkhag === "") {
        $errors[] = "Dzongkhag is required.";
    }

    $cid = $profile["cid"] ?? null;
    if (!is_string($cid) || strlen($cid) !== 11) {
        $errors[] = "CID must contain exactly 11 characters.";
    }

    $status = $profile["status"] ?? null;
    if (!in_array($status, ["Active", "Inactive"], true)) {
        $errors[] = "Status must be Active or Inactive.";
    }

    return ["isValid" => count($errors) === 0, "errors" => $errors];
}

$tests = [
    "Valid profile" => ["name" => "Pema Dorji", "age" => 24, "dzongkhag" => "Thimphu", "cid" => "10901000123", "status" => "Active"],
    "Invalid: everything wrong" => ["name" => "Al", "age" => 130, "dzongkhag" => "", "cid" => "1234", "status" => "Pending"],
    "Invalid: empty name, text age" => ["name" => "", "age" => "twenty", "dzongkhag" => "Paro", "cid" => "10901000124", "status" => "Inactive"],
    "Invalid: negative age, spaces only" => ["name" => "   ", "age" => -5, "dzongkhag" => "   ", "cid" => "10901000125", "status" => "active"],
];
?>
<!DOCTYPE html>
<html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Exercise 6</title><style>body{font-family:system-ui,sans-serif;max-width:960px;margin:2rem auto;padding:0 1rem;line-height:1.5}table{border-collapse:collapse;width:100%}th,td{border:1px solid #ccc;padding:.4rem .6rem;text-align:left}th{background:#f0f0f0}.warn{color:#9a3412}.ok{color:#166534}.err{color:#991b1b}section{margin:1.5rem 0}</style></head>
<body>
<h1>Exercise 6: Profile Validation Engine</h1>
<?php foreach ($tests as $label => $profile): $result = validateProfile($profile); ?>
    <section>
        <h2><?= htmlspecialchars($label) ?></h2>
        <?php if ($result["isValid"]): ?>
            <p class="ok">Profile is valid</p>
        <?php else: ?>
            <p class="err">Profile has <?= count($result["errors"]) ?> error(s):</p>
            <ul>
                <?php foreach ($result["errors"] as $error): ?>
                    <li><?= htmlspecialchars($error) ?></li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </section>
<?php endforeach; ?>
</body></html>
