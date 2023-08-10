<?php
/**
 * @discrabe Анализ отказов
 * @author Mahnev S.A.
 * @date 10.08.2023
 */


$options = getopt("u:t:");
$availabilityThreshold = isset($options['u']) ? (float) $options['u'] : 99.9;
$responseTimeThreshold = isset($options['t']) ? (float) $options['t'] : 45;


$intervals = [];
$currentInterval = null;
$totalRequests = 0;
$totalFailures = 0;


while ($line = fgets(STDIN)) {
    $fields = explode(" ", $line);
    list($datePart, $timePart) = explode(':', str_replace(['[', ']'], '', $fields[3]), 2);
    $datePart = str_replace('/', '.', $datePart);
    $timestamp = strtotime($datePart . ' ' . $timePart);
    $httpCode = (int) $fields[8];
    $responseTime = (float) $fields[10];

    if ($httpCode >= 500 || $responseTime > $responseTimeThreshold) {
        if (!$currentInterval) {
            $currentInterval = ['start' => $timestamp, 'end' => $timestamp];
        } else {
            $currentInterval['end'] = $timestamp;
        }
        $totalFailures++;
    } else {
        if ($currentInterval) {
            $currentInterval['end'] = $timestamp;
            $intervals[] = $currentInterval;
            $currentInterval = null;
        }
    }
    $totalRequests++;
}
if ($currentInterval) {
    $currentInterval['end'] = $timestamp;
    $intervals[] = $currentInterval;
}


foreach ($intervals as $key => $interval) {
    $intervalDuration = $interval['end'] - $interval['start'];
    $intervals[$key]['availability'] = (($totalRequests - $totalFailures) / $totalRequests) * 100;
}




usort($intervals, function ($a, $b) {
    return $a['start'] - $b['start'];
});

foreach ($intervals as $interval) {
    if ($interval['availability'] <= $availabilityThreshold) {
        echo date('H:i:s', $interval['start']) . "\t" . date('H:i:s', $interval['end']) . "\t" . number_format($interval['availability'], 1) . PHP_EOL;
    }
}