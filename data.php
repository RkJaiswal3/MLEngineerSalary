<?php
// Path to the CSV file
$csvFile = __DIR__ . '/salaries.csv';

// Open the CSV file for reading
$file = fopen($csvFile, 'r');

if ($file === false) {
    die('Error opening the file');
}

// Read the first line to get the headers
$headers = fgetcsv($file);

if ($headers === false) {
    die('Error reading the headers');
}

// Get the indices of the columns we need
$yearIndex = array_search('work_year', $headers);
$salaryIndex = array_search('salary_in_usd', $headers);

if ($yearIndex === false || $salaryIndex === false) {
    die('Required columns not found in the CSV file');
}

// Initialize arrays to hold the aggregated data
$jobCount = [];
$salarySum = [];

// Loop through the file and read each row
while (($row = fgetcsv($file)) !== false) {
    $year = $row[$yearIndex];
    $salary = (float)$row[$salaryIndex];

    if (!isset($jobCount[$year])) {
        $jobCount[$year] = 0;
        $salarySum[$year] = 0;
    }

    $jobCount[$year] += 1;  // Count the number of job entries
    $salarySum[$year] += $salary;
}

// Close the file
fclose($file);

// Prepare the data for display
$data = [];
foreach ($jobCount as $year => $totalJobs) {
    $averageSalary = $salarySum[$year] / $totalJobs;
    $data[] = [
        'Year' => $year,
        'Number of Total Jobs' => $totalJobs,
        'Average Salary (USD)' => round($averageSalary, 2)
    ];
}

// Print the data as an HTML table
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CSV Data Table</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid #dddddd;
            text-align: left;
            padding: 8px;
        }
        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
    <h1><center>Task 1</center></h1>
    <table class="main_table">
        <thead>
            <tr>
                <th>Year</th>
                <th>Number of Total Jobs</th>
                <th>Average Salary (USD)</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($data as $row): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['Year']); ?></td>
                    <td><?php echo htmlspecialchars($row['Number of Total Jobs']); ?></td>
                    <td><?php echo htmlspecialchars($row['Average Salary (USD)']); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>
