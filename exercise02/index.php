<?php
declare(strict_types=1);

// Decision: one function returns an array (group, eligibility, explanation) so the page only formats output.
function classifyCitizen(string $name, int $age): array
{
    if ($age < 0 || $age > 120) {
        return [
            "ageGroup"    => "Invalid",
            "eligible"    => false,
            "explanation" => "$name: age $age is invalid. Ages must be between 0 and 120.",
        ];
    }

    if ($age < 13) {
        $group = "Child";
    } elseif ($age < 18) {
        $group = "Teenager";
    } elseif ($age < 60) {
        $group = "Adult";
    } else {
        $group = "Senior Citizen";
    }

    $eligible = $age >= 18;
    $explanation = $eligible
        ? "$name is $age years old ($group), so they qualify for the adult-only service."
        : "$name is $age years old ($group). The adult-only service requires age 18 or above, so they do not qualify.";

    return ["ageGroup" => $group, "eligible" => $eligible, "explanation" => $explanation];
}

$name  = "Sonam Tenzin";
$tests = [8, 15, 18, 59, 60, 130, -1];
?>
<!DOCTYPE html>
<html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Exercise 2</title><style>body{font-family:system-ui,sans-serif;max-width:960px;margin:2rem auto;padding:0 1rem;line-height:1.5}table{border-collapse:collapse;width:100%}th,td{border:1px solid #ccc;padding:.4rem .6rem;text-align:left}th{background:#f0f0f0}.warn{color:#9a3412}.ok{color:#166534}.err{color:#991b1b}section{margin:1.5rem 0}</style></head>
<body>
<h1>Exercise 2: Age and Service Eligibility</h1>
<table>
    <tr><th>Age</th><th>Age group</th><th>Eligible?</th><th>Explanation</th></tr>
    <?php foreach ($tests as $age): $r = classifyCitizen($name, $age); ?>
        <tr>
            <td><?= $age ?></td>
            <td><?= htmlspecialchars($r["ageGroup"]) ?></td>
            <td class="<?= $r["eligible"] ? "ok" : "err" ?>"><?= $r["eligible"] ? "Yes" : "No" ?></td>
            <td><?= htmlspecialchars($r["explanation"]) ?></td>
        </tr>
    <?php endforeach; ?>
</table>
</body></html>
