<?php
declare(strict_types=1);

$requests = ["Thimphu", "Paro", "Thimphu", "Punakha", "Paro", "Thimphu"];

// Decision: counters live in one associative array keyed by dzongkhag, so new dzongkhags need no new variables.
$counts = [];
foreach ($requests as $dzongkhag) {
    $counts[$dzongkhag] = ($counts[$dzongkhag] ?? 0) + 1;
}

$total = count($requests);

$highest = 0;
foreach ($counts as $count) {
    if ($count > $highest) {
        $highest = $count;
    }
}
$topDzongkhags = [];
foreach ($counts as $dzongkhag => $count) {
    if ($count === $highest) {
        $topDzongkhags[] = $dzongkhag;
    }
}

$thimphuCount   = $counts["Thimphu"] ?? 0;
$thimphuPercent = $total > 0 ? round($thimphuCount / $total * 100, 1) : 0.0;
?>
<!DOCTYPE html>
<html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Exercise 4</title><style>body{font-family:system-ui,sans-serif;max-width:960px;margin:2rem auto;padding:0 1rem;line-height:1.5}table{border-collapse:collapse;width:100%}th,td{border:1px solid #ccc;padding:.4rem .6rem;text-align:left}th{background:#f0f0f0}.warn{color:#9a3412}.ok{color:#166534}.err{color:#991b1b}section{margin:1.5rem 0}</style></head>
<body>
<h1>Exercise 4: Dzongkhag Service Counter</h1>

<section>
    <h2>All requests</h2>
    <?php foreach ($requests as $index => $dzongkhag): ?>
        <p>Request #<?= $index + 1 ?>: <?= htmlspecialchars($dzongkhag) ?></p>
    <?php endforeach; ?>
</section>

<section>
    <h2>Requests per dzongkhag</h2>
    <ul>
        <?php foreach ($counts as $dzongkhag => $count): ?>
            <li><?= htmlspecialchars($dzongkhag) ?>: <?= $count ?></li>
        <?php endforeach; ?>
    </ul>
</section>

<section>
    <h2>Summary</h2>
    <p>Total requests: <?= $total ?></p>
    <p>Highest: <?= htmlspecialchars(implode(", ", $topDzongkhags)) ?> (<?= $highest ?> requests)</p>
    <p>Share from Thimphu: <?= $thimphuPercent ?>%</p>
</section>
</body></html>
