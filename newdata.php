<?php

$csvFile = __DIR__ . '/salaries.csv';

$file = fopen($csvFile, 'r');
if ($file === false) {
    die('Error opening the file');
}

$headers = fgetcsv($file);
if ($headers === false) {//checking he headers
    die('Error reading the headers');
}

// Getting indicess of the column
$yearIndex = array_search('work_year', $headers);
$salaryIndex = array_search('salary_in_usd', $headers);
$titleIndex = array_search('job_title', $headers);

if ($yearIndex === false || $salaryIndex === false || $titleIndex === false) {
    die('Required columns not found in the CSV file');
}

// Initialize arrays to hold the aggregated data
$jobCount = [];
$salarySum = [];
$jobTitles = [];

// Loop through the file and read each row
while (($row = fgetcsv($file)) !== false) {
    $year = $row[$yearIndex];
    $salary = (float)$row[$salaryIndex];
    $title = $row[$titleIndex];

    if (!isset($jobCount[$year])) {
        $jobCount[$year] = 0;
        $salarySum[$year] = 0;
        $jobTitles[$year] = [];
    }

    $jobCount[$year] += 1;  // Count the number of job entries
    $salarySum[$year] += $salary;

    if (!isset($jobTitles[$year][$title])) {
        $jobTitles[$year][$title] = 0;
    }
    $jobTitles[$year][$title] += 1;
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

// Convert job titles data to JSON for use in JavaScript
$jobTitlesJson = json_encode($jobTitles);
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
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #dddddd;
            text-align: left;
            padding: 8px;
        }
        th {
            background-color: #f2f2f2;
        }
        .hidden {
            display: none;
        }
    </style>
</head>
<body>
    <table id="mainTable">
        <thead>
            <tr>
                <th>Year</th>
                <th>Number of Total Jobs</th>
                <th>Average Salary (USD)</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($data as $row): ?>
                <tr data-year="<?php echo htmlspecialchars($row['Year']); ?>">
                    <td><?php echo htmlspecialchars($row['Year']); ?></td>
                    <td><?php echo htmlspecialchars($row['Number of Total Jobs']); ?></td>
                    <td><?php echo htmlspecialchars($row['Average Salary (USD)']); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <h1><center>Details of Data</center></h1>
    <table id="detailTable" class="hidden">
        <thead>
            <tr>
                <th>Job Title</th>
                <th>Number of Jobs</th>
            </tr>
        </thead>
        <tbody>
            <!-- Dynamically Presented -->
        </tbody>
    </table>

    <script>
        const jobTitles = <?php echo $jobTitlesJson; ?>;
        const mainTable = document.getElementById('mainTable');
        const detailTable = document.getElementById('detailTable');
        const detailTableBody = detailTable.querySelector('tbody');

        mainTable.addEventListener('click', (event) => {
            const target = event.target.closest('tr');
            if (!target || !target.dataset.year) return;
            const year = target.dataset.year;
            const titles = jobTitles[year];

            detailTableBody.innerHTML = '';
            for (const [title, count] of Object.entries(titles)) {
                const row = document.createElement('tr');
                row.innerHTML = `<td>${title}</td><td>${count}</td>`;
                detailTableBody.appendChild(row);
            }

            detailTable.classList.remove('hidden');
        });
    </script>
</body>
</html>
