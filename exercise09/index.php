<?php
declare(strict_types=1);

$records = [
    ["name" => "Pema Dorji",     "dzongkhag" => "Thimphu",  "age" => 24, "status" => "Active",   "type" => "Registration"],
    ["name" => "Karma Wangmo",   "dzongkhag" => "Paro",     "age" => 31, "status" => "Active",   "type" => "Verification"],
    ["name" => "Tashi Wangchuk", "dzongkhag" => "Punakha",  "age" => 45, "status" => "Inactive", "type" => "Correction"],
    ["name" => "Sonam Dorji",    "dzongkhag" => "Bumthang", "age" => 67, "status" => "Active",   "type" => "Verification"],
    ["name" => "Dechen Lhamo",   "dzongkhag" => "Paro",     "age" => 16, "status" => "Active",   "type" => "Registration"],
    ["name" => "Jigme Dorji",    "dzongkhag" => "Thimphu",  "age" => 62, "status" => "Inactive", "type" => "Transfer"],
    ["name" => "Yeshey Choden",  "dzongkhag" => "Trongsa",  "age" => 28, "status" => "Active",   "type" => "Verification"],
    ["name" => "Ugyen Tenzin",   "dzongkhag" => "Thimphu",  "age" => 36, "status" => "Active",   "type" => "Registration"],
    ["name" => "Nima Zangmo",    "dzongkhag" => "Paro",     "age" => 71, "status" => "Inactive", "type" => "Transfer"],
    ["name" => "Kinley Om",      "dzongkhag" => "Thimphu",  "age" => 9,  "status" => "Active",   "type" => "Registration"],
    ["name" => "Rinchen Pelden", "dzongkhag" => "Haa",      "age" => 53, "status" => "Active",   "type" => "Verification"],
    ["name" => "Sangay Phuntsho","dzongkhag" => "Punakha",  "age" => 40, "status" => "Inactive", "type" => "Correction"],
];

// Decision: one generic tally helper backs the dzongkhag and record-type functions, so counting logic exists once.
function tallyBy(array $records, string $field): array
{
    $tally = [];
    foreach ($records as $record) {
        $key = $record[$field];
        $tally[$key] = ($tally[$key] ?? 0) + 1;
    }
    arsort($tally);
    return $tally;
}

function countByStatus(array $records): array
{
    $counts = ["Active" => 0, "Inactive" => 0];
    foreach ($records as $record) {
        if (isset($counts[$record["status"]])) {
            $counts[$record["status"]]++;
        }
    }
    return $counts;
}

function countByDzongkhag(array $records): array { return tallyBy($records, "dzongkhag"); }
function countByRecordType(array $records): array { return tallyBy($records, "type"); }

function countAdultsAndSeniors(array $records): array
{
    $result = ["Adults (18-59)" => 0, "Senior Citizens (60+)" => 0];
    foreach ($records as $record) {
        if ($record["age"] >= 60) {
            $result["Senior Citizens (60+)"]++;
        } elseif ($record["age"] >= 18) {
            $result["Adults (18-59)"]++;
        }
    }
    return $result;
}

function calculatePercentage(int $part, int $total): float
{
    return $total > 0 ? round($part / $total * 100, 1) : 0.0;
}

function topKey(array $tally): string
{
    return (string) array_key_first($tally); // tallies are sorted highest first
}

$total      = count($records);
$statuses   = countByStatus($records);
$byDz       = countByDzongkhag($records);
$byType     = countByRecordType($records);
$ageGroups  = countAdultsAndSeniors($records);
$activePct  = calculatePercentage($statuses["Active"], $total);
?>
<!DOCTYPE html>
<html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Exercise 9</title><style>body{font-family:system-ui,sans-serif;max-width:960px;margin:2rem auto;padding:0 1rem;line-height:1.5}table{border-collapse:collapse;width:100%}th,td{border:1px solid #ccc;padding:.4rem .6rem;text-align:left}th{background:#f0f0f0}.warn{color:#9a3412}.ok{color:#166534}.err{color:#991b1b}section{margin:1.5rem 0}</style></head>
<body>
<h1>Exercise 9: B-PIS Records Report</h1>

<section>
    <h2>Overview</h2>
    <table>
        <tr><th>Measure</th><th>Value</th></tr>
        <tr><td>Total records</td><td><?= $total ?></td></tr>
        <tr><td>Active</td><td><?= $statuses["Active"] ?></td></tr>
        <tr><td>Inactive</td><td><?= $statuses["Inactive"] ?></td></tr>
        <tr><td>Active records (%)</td><td><?= $activePct ?>%</td></tr>
        <?php foreach ($ageGroups as $label => $count): ?>
            <tr><td><?= htmlspecialchars($label) ?></td><td><?= $count ?></td></tr>
        <?php endforeach; ?>
    </table>
</section>

<section>
    <h2>Records by dzongkhag</h2>
    <table>
        <tr><th>Dzongkhag</th><th>Records</th><th>Share</th></tr>
        <?php foreach ($byDz as $label => $count): ?>
            <tr><td><?= htmlspecialchars($label) ?></td><td><?= $count ?></td><td><?= calculatePercentage($count, $total) ?>%</td></tr>
        <?php endforeach; ?>
    </table>
</section>

<section>
    <h2>Records by type</h2>
    <table>
        <tr><th>Record type</th><th>Records</th><th>Share</th></tr>
        <?php foreach ($byType as $label => $count): ?>
            <tr><td><?= htmlspecialchars($label) ?></td><td><?= $count ?></td><td><?= calculatePercentage($count, $total) ?>%</td></tr>
        <?php endforeach; ?>
    </table>
</section>

<section>
    <h2>All records</h2>
    <table>
        <tr><th>#</th><th>Name</th><th>Dzongkhag</th><th>Age</th><th>Status</th><th>Type</th></tr>
        <?php foreach ($records as $i => $r): ?>
            <tr>
                <td><?= $i + 1 ?></td><td><?= htmlspecialchars($r["name"]) ?></td><td><?= htmlspecialchars($r["dzongkhag"]) ?></td>
                <td><?= $r["age"] ?></td><td><?= htmlspecialchars($r["status"]) ?></td><td><?= htmlspecialchars($r["type"]) ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
</section>

<section>
    <h2>Interpretation</h2>
    <p>
        <?= htmlspecialchars(topKey($byDz)) ?> appears most often (<?= reset($byDz) ?> of <?= $total ?> records),
        and <?= htmlspecialchars(topKey($byType)) ?> is the most common record type (<?= reset($byType) ?> records).
        About <?= $activePct ?>% of all records are active.
    </p>
</section>
</body></html>
