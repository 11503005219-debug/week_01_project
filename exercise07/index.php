<?php
declare(strict_types=1);

const DISCOUNT_RATE      = 0.10;
const DISCOUNT_THRESHOLD = 5;
const PROCESSING_CHARGE  = 20.00;

$serviceFees = ["Certificate" => 100, "Verification" => 50, "Replacement" => 200, "Correction" => 75];

// Decision: return every part of the calculation (or an error) in one array, so the page never repeats the maths.
function calculateServiceFee(array $fees, string $service, int $quantity): array
{
    if (!array_key_exists($service, $fees)) {
        return ["valid" => false, "error" => "Unknown service: \"$service\"."];
    }
    if ($quantity < 1) {
        return ["valid" => false, "error" => "Quantity must be at least 1 (received $quantity)."];
    }

    $unitFee  = (float) $fees[$service];
    $cost     = $unitFee * $quantity;
    $discount = $quantity >= DISCOUNT_THRESHOLD ? $cost * DISCOUNT_RATE : 0.0;

    return [
        "valid"      => true,
        "error"      => null,
        "service"    => $service,
        "unitFee"    => $unitFee,
        "quantity"   => $quantity,
        "cost"       => $cost,
        "discount"   => $discount,
        "processing" => PROCESSING_CHARGE,
        "total"      => $cost - $discount + PROCESSING_CHARGE,
    ];
}

function money(float $amount): string
{
    return "Nu. " . number_format($amount, 2);
}

$scenarios = [
    ["Certificate", 2],
    ["Verification", 5],
    ["Replacement", 10],
    ["Passport", 1],
    ["Correction", 0],
];
?>
<!DOCTYPE html>
<html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Exercise 7</title><style>body{font-family:system-ui,sans-serif;max-width:960px;margin:2rem auto;padding:0 1rem;line-height:1.5}table{border-collapse:collapse;width:100%}th,td{border:1px solid #ccc;padding:.4rem .6rem;text-align:left}th{background:#f0f0f0}.warn{color:#9a3412}.ok{color:#166534}.err{color:#991b1b}section{margin:1.5rem 0}</style></head>
<body>
<h1>Exercise 7: Service Fee Calculator</h1>
<?php foreach ($scenarios as [$service, $quantity]): $r = calculateServiceFee($serviceFees, $service, $quantity); ?>
    <section>
        <h2><?= htmlspecialchars($service) ?> &times; <?= $quantity ?></h2>
        <?php if (!$r["valid"]): ?>
            <p class="err">Error: <?= htmlspecialchars($r["error"]) ?></p>
        <?php else: ?>
            <table>
                <tr><th>Service</th><td><?= htmlspecialchars($r["service"]) ?></td></tr>
                <tr><th>Unit fee</th><td><?= money($r["unitFee"]) ?></td></tr>
                <tr><th>Quantity</th><td><?= $r["quantity"] ?></td></tr>
                <tr><th>Cost before discount</th><td><?= money($r["cost"]) ?></td></tr>
                <tr><th>Discount</th><td><?= money($r["discount"]) ?></td></tr>
                <tr><th>Processing charge</th><td><?= money($r["processing"]) ?></td></tr>
                <tr><th>Final amount</th><td><strong><?= money($r["total"]) ?></strong></td></tr>
            </table>
        <?php endif; ?>
    </section>
<?php endforeach; ?>
</body></html>
