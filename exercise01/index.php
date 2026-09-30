<?php
declare(strict_types=1);

// Decision: an associative array ties every value to a label, so the display code never depends on position.
$profiles = [
    ["full_name" => "Pema Dorji", "cid" => "10901000123", "dob" => "1998-04-12", "dzongkhag" => "Thimphu", "gewog" => "Chang", "occupation" => "Teacher", "active" => true],
    ["full_name" => "Karma Wangmo", "cid" => "109010", "dob" => "2001-09-30", "dzongkhag" => "Paro", "gewog" => "Doteng", "occupation" => "Farmer", "active" => false],
];

function displayProfileField(string $label, string $value): string
{
    return '<p><strong>' . htmlspecialchars($label) . ':</strong> ' . htmlspecialchars($value) . '</p>';
}
?>
<!DOCTYPE html>
<html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Exercise 1</title><style>body{font-family:system-ui,sans-serif;max-width:960px;margin:2rem auto;padding:0 1rem;line-height:1.5}table{border-collapse:collapse;width:100%}th,td{border:1px solid #ccc;padding:.4rem .6rem;text-align:left}th{background:#f0f0f0}.warn{color:#9a3412}.ok{color:#166534}.err{color:#991b1b}section{margin:1.5rem 0}</style></head>
<body>
<h1>Exercise 1: Fictional Citizen Profile</h1>
<?php foreach ($profiles as $index => $profile): ?>
    <section>
        <h2>Profile <?= $index + 1 ?></h2>
        <?php
        $statusText = $profile["active"] ? "Active" : "Inactive";
        $cidLength  = strlen($profile["cid"]);

        echo displayProfileField("Full name (uppercase)", mb_strtoupper($profile["full_name"]));
        echo displayProfileField("Fictional CID", $profile["cid"]);
        echo displayProfileField("CID length", $cidLength . " characters");
        echo displayProfileField("Date of birth", $profile["dob"]);
        echo displayProfileField("Dzongkhag", $profile["dzongkhag"]);
        echo displayProfileField("Gewog", $profile["gewog"]);
        echo displayProfileField("Occupation", $profile["occupation"]);
        echo displayProfileField("Status", $statusText);

        if ($cidLength !== 11) {
            echo '<p class="warn">Warning: the CID must contain exactly 11 characters (found ' . $cidLength . ').</p>';
        }
        ?>
    </section>
<?php endforeach; ?>
</body></html>
