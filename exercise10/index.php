<?php
declare(strict_types=1);

const SCORE_START     = 100;
const SCORE_DEDUCTION = 25;

function e(mixed $value): string
{
    return htmlspecialchars(is_scalar($value) ? (string) $value : "", ENT_QUOTES, "UTF-8");
}

// ---------- Data (all fictional). Nested arrays: each profile has a "services" list. ----------
$datasets = [
    "valid" => [
        ["name" => "Pema Dorji",     "age" => 24, "dzongkhag" => "Thimphu",  "status" => "Active",   "services" => ["Certificate"]],
        ["name" => "Karma Wangmo",   "age" => 31, "dzongkhag" => "Paro",     "status" => "Active",   "services" => ["Verification", "Correction"]],
        ["name" => "Tashi Wangchuk", "age" => 45, "dzongkhag" => "Punakha",  "status" => "Inactive", "services" => []],
        ["name" => "Sonam Dorji",    "age" => 67, "dzongkhag" => "Bumthang", "status" => "Active",   "services" => ["Replacement"]],
        ["name" => "Dechen Lhamo",   "age" => 19, "dzongkhag" => "Paro",     "status" => "Active",   "services" => ["Certificate"]],
        ["name" => "Jigme Dorji",    "age" => 52, "dzongkhag" => "Thimphu",  "status" => "Inactive", "services" => ["Verification"]],
        ["name" => "Yeshey Choden",  "age" => 28, "dzongkhag" => "Trongsa",  "status" => "Active",   "services" => ["Certificate", "Verification"]],
        ["name" => "Ugyen Tenzin",   "age" => 36, "dzongkhag" => "Thimphu",  "status" => "Active",   "services" => ["Correction"]],
    ],
    "messy" => [
        ["name" => "Pema Dorji",     "age" => 24,       "dzongkhag" => "Thimphu",  "status" => "Active",   "services" => ["Certificate"]],
        ["name" => "",               "age" => 31,       "dzongkhag" => "Paro",     "status" => "Active",   "services" => ["Verification"]],
        ["name" => "Karma Wangmo",   "age" => 150,      "dzongkhag" => "Punakha",  "status" => "Inactive", "services" => []],
        ["name" => "Tashi Wangchuk", "age" => 45,       "dzongkhag" => "",         "status" => "Pending",  "services" => ["Correction"]],
        ["name" => "Sonam Choden",   "age" => "thirty", "dzongkhag" => "Thimphu",  "status" => "Active",   "services" => ["Replacement"]],
        ["name" => "Dorji Tenzin",   "age" => 67,       "dzongkhag" => "Bumthang", "status" => "Active",   "services" => ["Certificate"]],
        ["name" => "",               "age" => -3,       "dzongkhag" => "",         "status" => "Unknown",  "services" => []],
        ["name" => "Dechen Lhamo",   "age" => 19,       "dzongkhag" => "Paro",     "status" => "Inactive", "services" => ["Verification"]],
    ],
];

$permissions = [
    "administrator" => ["create", "read", "update", "delete", "export"],
    "data officer"  => ["create", "read", "update"],
    "viewer"        => ["read"],
];

// ---------- Functions ----------
function hasValidAge(mixed $age): bool
{
    return is_int($age) && $age >= 0 && $age <= 120; // strict: the string "30" is not accepted
}

// Decision: issues are keyed by field, so the score and the warnings both come from one check.
function findIssues(array $profile): array
{
    $issues = [];
    if (!is_string($profile["name"]) || trim($profile["name"]) === "") {
        $issues["name"] = "Missing name";
    }
    if (!hasValidAge($profile["age"])) {
        $issues["age"] = "Invalid age";
    }
    if (!is_string($profile["dzongkhag"]) || trim($profile["dzongkhag"]) === "") {
        $issues["dzongkhag"] = "Missing dzongkhag";
    }
    if (!in_array($profile["status"], ["Active", "Inactive"], true)) {
        $issues["status"] = "Invalid status";
    }
    return $issues;
}

function qualityScore(array $profile): int
{
    return SCORE_START - SCORE_DEDUCTION * count(findIssues($profile));
}

function filterProfiles(array $profiles, string $dzongkhag, string $status): array
{
    $result = [];
    foreach ($profiles as $index => $profile) {
        if ($dzongkhag !== "All" && $profile["dzongkhag"] !== $dzongkhag) {
            continue;
        }
        if ($status !== "All" && $profile["status"] !== $status) {
            continue;
        }
        $result[$index] = $profile;
    }
    return $result;
}

function ageStatistics(array $profiles): array
{
    $youngest = null;
    $oldest   = null;
    $sum      = 0;
    $count    = 0;

    foreach ($profiles as $profile) {
        if (!hasValidAge($profile["age"])) {
            continue; // invalid ages must not distort the statistics
        }
        $count++;
        $sum += $profile["age"];
        if ($youngest === null || $profile["age"] < $youngest["age"]) {
            $youngest = $profile;
        }
        if ($oldest === null || $profile["age"] > $oldest["age"]) {
            $oldest = $profile;
        }
    }

    return ["youngest" => $youngest, "oldest" => $oldest, "average" => $count > 0 ? round($sum / $count, 1) : null, "counted" => $count];
}

function canPerformAction(array $permissions, string $role, string $action): bool
{
    $role   = strtolower(trim($role));
    $action = strtolower(trim($action));
    return array_key_exists($role, $permissions) && in_array($action, $permissions[$role], true);
}

function selected(string $current, string $option): string
{
    return $current === $option ? " selected" : "";
}

function displayName(array $profile): string
{
    return is_string($profile["name"]) && trim($profile["name"]) !== "" ? $profile["name"] : "(missing name)";
}

// ---------- Read the selections (whitelisted, so bad input falls back to defaults) ----------
$datasetKey = isset($_GET["dataset"]) && is_string($_GET["dataset"]) && isset($datasets[$_GET["dataset"]]) ? $_GET["dataset"] : "valid";
$profiles   = $datasets[$datasetKey];

$dzongkhagOptions = ["All"];
foreach ($profiles as $p) {
    if (is_string($p["dzongkhag"]) && trim($p["dzongkhag"]) !== "" && !in_array($p["dzongkhag"], $dzongkhagOptions, true)) {
        $dzongkhagOptions[] = $p["dzongkhag"];
    }
}
$statusOptions = ["All", "Active", "Inactive"];
$roleOptions   = [...array_keys($permissions), "guest"];
$actionOptions = ["create", "read", "update", "delete", "export"];

$pick = static function (string $key, array $options, string $default): string {
    return isset($_GET[$key]) && is_string($_GET[$key]) && in_array($_GET[$key], $options, true) ? $_GET[$key] : $default;
};
$selDzongkhag = $pick("dzongkhag", $dzongkhagOptions, "All");
$selStatus    = $pick("status", $statusOptions, "All");
$selRole      = $pick("role", $roleOptions, "viewer");
$selAction    = $pick("action", $actionOptions, "read");

// ---------- Calculations over the full dataset ----------
$total = count($profiles);
$active = 0;
$inactive = 0;
$scores = [];
$imperfect = 0;
$worstIndex = null;

foreach ($profiles as $index => $profile) {
    if ($profile["status"] === "Active") {
        $active++;
    } elseif ($profile["status"] === "Inactive") {
        $inactive++;
    }
    $scores[$index] = qualityScore($profile);
    if ($scores[$index] < SCORE_START) {
        $imperfect++;
    }
    if ($worstIndex === null || $scores[$index] < $scores[$worstIndex]) {
        $worstIndex = $index;
    }
}
$invalidStatus = $total - $active - $inactive;
$averageScore  = $total > 0 ? round(array_sum($scores) / $total, 1) : 0.0;
$ages          = ageStatistics($profiles);
$filtered      = filterProfiles($profiles, $selDzongkhag, $selStatus);
$allowed       = canPerformAction($permissions, $selRole, $selAction);
?>
<!DOCTYPE html>
<html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Exercise 10</title><style>body{font-family:system-ui,sans-serif;max-width:960px;margin:2rem auto;padding:0 1rem;line-height:1.5}table{border-collapse:collapse;width:100%}th,td{border:1px solid #ccc;padding:.4rem .6rem;text-align:left}th{background:#f0f0f0}.warn{color:#9a3412}.ok{color:#166534}.err{color:#991b1b}section{margin:1.5rem 0} form{display:flex;flex-wrap:wrap;gap:.75rem;align-items:end}label{display:flex;flex-direction:column;font-size:.9rem}</style></head>
<body>
<h1>Exercise 10: B-PIS Command Dashboard</h1>

<section>
    <form method="get">
        <label>Dataset
            <select name="dataset">
                <option value="valid"<?= selected($datasetKey, "valid") ?>>Complete valid dataset</option>
                <option value="messy"<?= selected($datasetKey, "messy") ?>>Dataset with problems</option>
            </select>
        </label>
        <label>Dzongkhag
            <select name="dzongkhag">
                <?php foreach ($dzongkhagOptions as $o): ?><option<?= selected($selDzongkhag, $o) ?>><?= e($o) ?></option><?php endforeach; ?>
            </select>
        </label>
        <label>Status
            <select name="status">
                <?php foreach ($statusOptions as $o): ?><option<?= selected($selStatus, $o) ?>><?= e($o) ?></option><?php endforeach; ?>
            </select>
        </label>
        <label>Role
            <select name="role">
                <?php foreach ($roleOptions as $o): ?><option<?= selected($selRole, $o) ?>><?= e($o) ?></option><?php endforeach; ?>
            </select>
        </label>
        <label>Action
            <select name="action">
                <?php foreach ($actionOptions as $o): ?><option<?= selected($selAction, $o) ?>><?= e($o) ?></option><?php endforeach; ?>
            </select>
        </label>
        <button type="submit">Apply</button>
    </form>
</section>

<section>
    <h2>Summary</h2>
    <table>
        <tr><th>Total profiles</th><td><?= $total ?></td></tr>
        <tr><th>Active</th><td><?= $active ?></td></tr>
        <tr><th>Inactive</th><td><?= $inactive ?></td></tr>
        <tr><th>Invalid status</th><td><?= $invalidStatus ?></td></tr>
        <tr><th>Youngest</th><td><?= $ages["youngest"] ? e(displayName($ages["youngest"])) . " (" . $ages["youngest"]["age"] . ")" : "n/a" ?></td></tr>
        <tr><th>Oldest</th><td><?= $ages["oldest"] ? e(displayName($ages["oldest"])) . " (" . $ages["oldest"]["age"] . ")" : "n/a" ?></td></tr>
        <tr><th>Average age</th><td><?= $ages["average"] !== null ? $ages["average"] . " (from " . $ages["counted"] . " valid ages)" : "n/a" ?></td></tr>
    </table>
</section>

<section>
    <h2>Permission check</h2>
    <p class="<?= $allowed ? "ok" : "err" ?>">
        <?php if ($allowed): ?>
            The <?= e($selRole) ?> role is allowed to <?= e($selAction) ?> records.
        <?php else: ?>
            The <?= e($selRole) ?> role is not allowed to <?= e($selAction) ?> records.
        <?php endif; ?>
    </p>
</section>

<section>
    <h2>Profiles (<?= count($filtered) ?> shown: <?= e($selDzongkhag) ?> / <?= e($selStatus) ?>)</h2>
    <?php if (count($filtered) === 0): ?>
        <p class="warn">No profiles match the selected filters.</p>
    <?php else: ?>
        <table>
            <tr><th>#</th><th>Name</th><th>Age</th><th>Dzongkhag</th><th>Status</th><th>Services</th><th>Score</th><th>Warnings</th></tr>
            <?php foreach ($filtered as $index => $p): $issues = findIssues($p); ?>
                <tr>
                    <td><?= $index + 1 ?></td>
                    <td><?= e(displayName($p)) ?></td>
                    <td><?= e($p["age"]) ?></td>
                    <td><?= e($p["dzongkhag"] !== "" ? $p["dzongkhag"] : "(missing)") ?></td>
                    <td><?= e($p["status"]) ?></td>
                    <td><?= count($p["services"]) > 0 ? e(implode(", ", $p["services"])) : "None" ?></td>
                    <td><?= $scores[$index] ?></td>
                    <td class="<?= count($issues) > 0 ? "warn" : "ok" ?>"><?= count($issues) > 0 ? e(implode("; ", $issues)) : "None" ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>
</section>

<section>
    <h2>Data-quality report</h2>
    <p>Average score: <strong><?= $averageScore ?></strong> / <?= SCORE_START ?></p>
    <p>Imperfect profiles: <strong><?= $imperfect ?></strong> of <?= $total ?></p>
    <?php if ($worstIndex !== null && $scores[$worstIndex] < SCORE_START): ?>
        <p class="warn">Needs the most correction: record #<?= $worstIndex + 1 ?> (<?= e(displayName($profiles[$worstIndex])) ?>), score <?= $scores[$worstIndex] ?> &ndash; <?= e(implode("; ", findIssues($profiles[$worstIndex]))) ?>.</p>
    <?php else: ?>
        <p class="ok">Every profile scored <?= SCORE_START ?>; nothing needs correction.</p>
    <?php endif; ?>
</section>
</body></html>
