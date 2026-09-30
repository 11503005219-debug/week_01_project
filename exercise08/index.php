<?php
declare(strict_types=1);

// Decision: permissions live in an associative array (role => actions), so adding a role is data, not new conditions.
$permissions = [
    "administrator" => ["create", "read", "update", "delete", "export"],
    "data officer"  => ["create", "read", "update"],
    "viewer"        => ["read"],
];

function canPerformAction(array $permissions, string $role, string $action): bool
{
    $role   = strtolower(trim($role));
    $action = strtolower(trim($action));

    if (!array_key_exists($role, $permissions)) {
        return false; // unknown roles get no permissions
    }
    return in_array($action, $permissions[$role], true);
}

$tests = [
    ["Administrator", "delete"],
    ["ADMINISTRATOR", "Export"],
    ["Data Officer", "update"],
    ["data officer", "delete"],
    ["Data Officer", "export"],
    ["Viewer", "read"],
    ["viewer", "create"],
    ["Guest", "read"],
    ["Viewer", "approve"],
];
?>
<!DOCTYPE html>
<html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Exercise 8</title><style>body{font-family:system-ui,sans-serif;max-width:960px;margin:2rem auto;padding:0 1rem;line-height:1.5}table{border-collapse:collapse;width:100%}th,td{border:1px solid #ccc;padding:.4rem .6rem;text-align:left}th{background:#f0f0f0}.warn{color:#9a3412}.ok{color:#166534}.err{color:#991b1b}section{margin:1.5rem 0}</style></head>
<body>
<h1>Exercise 8: Role Permission Checker</h1>
<table>
    <tr><th>Role</th><th>Action</th><th>Allowed?</th><th>Message</th></tr>
    <?php foreach ($tests as [$role, $action]):
        $allowed = canPerformAction($permissions, $role, $action);
        $known   = array_key_exists(strtolower(trim($role)), $permissions);
        if ($allowed) {
            $message = "The $role role is allowed to $action records.";
        } elseif (!$known) {
            $message = "\"$role\" is not a recognised role, so it has no permissions and cannot $action records.";
        } else {
            $message = "The $role role is not allowed to $action records.";
        }
        ?>
        <tr>
            <td><?= htmlspecialchars($role) ?></td>
            <td><?= htmlspecialchars($action) ?></td>
            <td class="<?= $allowed ? "ok" : "err" ?>"><?= $allowed ? "Yes" : "No" ?></td>
            <td><?= htmlspecialchars($message) ?></td>
        </tr>
    <?php endforeach; ?>
</table>
</body></html>
