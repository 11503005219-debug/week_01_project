<?php
declare(strict_types=1);

$profiles = [
    ["name" => "Pema Dorji",     "dzongkhag" => "Thimphu",  "age" => 24, "status" => "Active"],
    ["name" => "Karma Wangmo",   "dzongkhag" => "Paro",     "age" => 31, "status" => "Active"],
    ["name" => "Tashi Wangchuk", "dzongkhag" => "Punakha",  "age" => 45, "status" => "Inactive"],
    ["name" => "Sonam Dorji",    "dzongkhag" => "Bumthang", "age" => 67, "status" => "Active"],
    ["name" => "Dechen Lhamo",   "dzongkhag" => "Paro",     "age" => 19, "status" => "Active"],
    ["name" => "Jigme Dorji",    "dzongkhag" => "Thimphu",  "age" => 52, "status" => "Inactive"],
    ["name" => "Yeshey Choden",  "dzongkhag" => "Trongsa",  "age" => 28, "status" => "Active"],
    ["name" => "Ugyen Tenzin",   "dzongkhag" => "Haa",      "age" => 36, "status" => "Active"],
];

// Decision: stripos() gives a case-insensitive "contains" test without changing the stored names.
function searchProfiles(array $profiles, string $term): array
{
    $term = trim($term);
    if ($term === "") {
        return [];
    }
    $matches = [];
    foreach ($profiles as $profile) {
        if (stripos($profile["name"], $term) !== false) {
            $matches[] = $profile;
        }
    }
    return $matches;
}

function renderProfileTable(array $profiles): string
{
    $html = '<table><tr><th>Name</th><th>Dzongkhag</th><th>Age</th><th>Status</th></tr>';
    foreach ($profiles as $p) {
        $html .= '<tr><td>' . htmlspecialchars($p["name"]) . '</td><td>' . htmlspecialchars($p["dzongkhag"])
              . '</td><td>' . $p["age"] . '</td><td>' . htmlspecialchars($p["status"]) . '</td></tr>';
    }
    return $html . '</table>';
}

// Several matches (typed in lowercase on purpose), one match, no match.
$searchTerms = ["dorji", "Tashi", "Zangmo"];
?>
<!DOCTYPE html>
<html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Exercise 5</title><style>body{font-family:system-ui,sans-serif;max-width:960px;margin:2rem auto;padding:0 1rem;line-height:1.5}table{border-collapse:collapse;width:100%}th,td{border:1px solid #ccc;padding:.4rem .6rem;text-align:left}th{background:#f0f0f0}.warn{color:#9a3412}.ok{color:#166534}.err{color:#991b1b}section{margin:1.5rem 0}</style></head>
<body>
<h1>Exercise 5: B-PIS Record Search</h1>

<section>
    <h2>All profiles (<?= count($profiles) ?>)</h2>
    <?= renderProfileTable($profiles) ?>
</section>

<?php foreach ($searchTerms as $searchTerm): $results = searchProfiles($profiles, $searchTerm); ?>
    <section>
        <h2>Search: "<?= htmlspecialchars($searchTerm) ?>"</h2>
        <p>Matching profiles: <?= count($results) ?></p>
        <?php if (count($results) === 0): ?>
            <p class="warn">No matching records found</p>
        <?php else: ?>
            <?= renderProfileTable($results) ?>
        <?php endif; ?>
    </section>
<?php endforeach; ?>
</body></html>
