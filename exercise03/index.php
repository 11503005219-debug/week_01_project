<?php
declare(strict_types=1);

$members = ["Pema Choden", "Tashi", "Karma Lhamo", "Sonam Dorji", "Dechen", "Jigme Wangchuk", "Yeshey"];

// Decision: on ties, the first name found wins, because only a strictly shorter/longer name replaces the current one.
function summariseMembers(array $members): array
{
    if (count($members) === 0) {
        return ["count" => 0, "shortest" => null, "longest" => null, "average" => 0.0];
    }

    $shortest = $members[0];
    $longest  = $members[0];
    $total    = 0;

    foreach ($members as $member) {
        $length = mb_strlen($member);
        $total += $length;
        if ($length < mb_strlen($shortest)) {
            $shortest = $member;
        }
        if ($length > mb_strlen($longest)) {
            $longest = $member;
        }
    }

    return [
        "count"    => count($members),
        "shortest" => $shortest,
        "longest"  => $longest,
        "average"  => round($total / count($members), 2),
    ];
}

$summary = summariseMembers($members);
?>
<!DOCTYPE html>
<html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Exercise 3</title><style>body{font-family:system-ui,sans-serif;max-width:960px;margin:2rem auto;padding:0 1rem;line-height:1.5}table{border-collapse:collapse;width:100%}th,td{border:1px solid #ccc;padding:.4rem .6rem;text-align:left}th{background:#f0f0f0}.warn{color:#9a3412}.ok{color:#166534}.err{color:#991b1b}section{margin:1.5rem 0}</style></head>
<body>
<h1>Exercise 3: Household Member Summary</h1>

<section>
    <h2>Members</h2>
    <ol>
        <?php foreach ($members as $member): ?>
            <li><?= htmlspecialchars($member) ?> (<?= mb_strlen($member) ?> characters)</li>
        <?php endforeach; ?>
    </ol>
</section>

<section>
    <h2>Summary</h2>
    <p>Total members: <?= $summary["count"] ?></p>
    <p>First name: <?= htmlspecialchars($members[0]) ?></p>
    <p>Last name: <?= htmlspecialchars($members[array_key_last($members)]) ?></p>
    <p>Shortest name: <?= htmlspecialchars((string) $summary["shortest"]) ?> (<?= mb_strlen((string) $summary["shortest"]) ?> characters)</p>
    <p>Longest name: <?= htmlspecialchars((string) $summary["longest"]) ?> (<?= mb_strlen((string) $summary["longest"]) ?> characters)</p>
    <p>Average name length: <?= $summary["average"] ?> characters</p>
</section>
</body></html>
