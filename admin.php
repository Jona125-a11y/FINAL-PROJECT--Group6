<?php
// Set the title and include the header
$title = 'Retrofee | Dashboard';
include './view/admin_header.php';


// Query to get total number of users, products, and total sales
$userCountQuery = "SELECT COUNT(*) AS total_users FROM tbluser";
$productCountQuery = "SELECT COUNT(*) AS total_products FROM tblproduct";
$totalSalesQuery = "SELECT SUM(tblproduct.price) AS total_sales FROM tblorder inner join tblproduct on tblorder.product_id= tblproduct.product_id";

$userCount = $conn->query($userCountQuery)->fetch_assoc();
$productCount = $conn->query($productCountQuery)->fetch_assoc();
$totalSales = $conn->query($totalSalesQuery)->fetch_assoc();

// Query for best 8 selling products
$bestSellersQuery = "SELECT p.productname, SUM(o.qt) AS total_sales 
                     FROM tblorder o
                     JOIN tblproduct p ON o.product_id = p.product_id
                     GROUP BY o.product_id
                     ORDER BY total_sales DESC
                     LIMIT 4";
$bestSellers = $conn->query($bestSellersQuery);

// Query to get sales data for charts (yearly, monthly, weekly)
$chartQuery = "SELECT YEAR(dateandtime) AS year, MONTH(dateandtime) AS month, 
                      WEEK(dateandtime) AS week, SUM(tblproduct.price) AS sales 
               FROM tblproduct inner join tblorder on tblproduct.product_id = tblorder.product_id
               GROUP BY year, month, week";
$salesData = $conn->query($chartQuery);

$conn->close();
?>

<!-- Dashboard Section -->
<div class="dashboard-container">
        <div class="summary">
            <div class="card">
                <h3>Total Users</h3>
                <p><?php echo $userCount['total_users']; ?></p>
                <i class="fa fa-user" aria-hidden="true"></i>
            </div>
            <div class="card">
                <h3>Total Products</h3>
                <p><?php echo $productCount['total_products']; ?></p>
                <i class="fa fa-shopping-cart" aria-hidden="true"></i>
                
            </div>
            <div class="card">
                <h3>Total Sales</h3>
                <p>₱<?php echo number_format($totalSales['total_sales'], 2); ?></p>
                <i class="fas fa-chart-line"></i>
            </div>
        </div>

        <!-- Sales Chart Section -->
        <div class="chart-container">
    <label for="sales-timeframe">Sales Performance</label>
    <select id="sales-timeframe">
        <option value="weekly">Weekly</option>
        <option value="monthly">Monthly</option>
        <option value="yearly">Yearly</option>
    </select>
    <canvas id="salesChart"></canvas>
    <button id="exportCsv">Export to CSV</button>
    <button id="exportExcel">Export to Excel</button>
    <button id="exportPdf">Export to PDF</button>
</div>

        <!-- Donut Chart Section -->
        <div class="donut-chart-container">
            <h3>Best Selling Products</h3>
            <canvas id="donutChart"></canvas>
        </div>
    </div>

    <script>
        // Fetching sales data for the chart
let salesData = <?php echo json_encode($salesData->fetch_all(MYSQLI_ASSOC)); ?>;
let bestSellers = <?php echo json_encode($bestSellers->fetch_all(MYSQLI_ASSOC)); ?>;
// Get the context for the sales chart
let salesChart = document.getElementById('salesChart').getContext('2d');
let salesTimeframe = document.getElementById('sales-timeframe');

// Initialize the chart data structure
let salesChartData = {
    labels: [], // Time labels (weeks, months, years)
    datasets: [{
        label: 'Sales',
        data: [],
        backgroundColor: '#a84d14', // Sales color
        borderColor: '#a84d14',
        borderWidth: 1
    }]
};

// Create the chart instance
let chartInstance = new Chart(salesChart, {
    type: 'line',
    data: salesChartData,
    options: {
        responsive: true,
        scales: {
            x: {
                beginAtZero: true
            },
            y: {
                beginAtZero: true
            }
        }
    }
});

// Function to update the chart based on the selected timeframe
function updateSalesChart(timeframe) {
    salesChartData.labels = [];
    salesChartData.datasets[0].data = [];

    salesData.forEach(data => {
        if (timeframe === 'weekly' && data.week) {
            // Weekly: Use "Week X"
            salesChartData.labels.push('Week ' + data.week);
            salesChartData.datasets[0].data.push(data.sales);
        } else if (timeframe === 'monthly' && data.month) {
            // Monthly: Use month names
            const monthNames = [
                "January", "February", "March", "April", "May", "June", "July", "August",
                "September", "October", "November", "December"
            ];
            salesChartData.labels.push(monthNames[data.month - 1]); // Month names (adjusting index)
            salesChartData.datasets[0].data.push(data.sales);
        } else if (timeframe === 'yearly' && data.year) {
            // Yearly: Use the year
            salesChartData.labels.push(data.year);
            salesChartData.datasets[0].data.push(data.sales);
        }
    });

    // Update the chart without creating a new instance
    chartInstance.update();
}

// Event listener to detect change in the timeframe selection
salesTimeframe.addEventListener('change', function() {
    updateSalesChart(this.value);  // Update the chart when the timeframe changes
});

// Initial chart load with 'weekly' as default
updateSalesChart('weekly');

// Donut Chart (Best 8 Sellers)
let donutChart = document.getElementById('donutChart').getContext('2d');
let donutChartData = {
    labels: bestSellers.map(product => product.productname),
    datasets: [{
        data: bestSellers.map(product => product.total_sales),
        backgroundColor: ['#a84d14', '#e4681b', '#e9d66b', '#f2e6a6'],
        hoverBackgroundColor: ['#a84d14', '#e4681b', '#e9d66b', '#f2e6a6']
    }]
};

new Chart(donutChart, {
    type: 'doughnut',
    data: donutChartData,
    options: {
        responsive: true,
        plugins: {
            legend: {
                position: 'top',
            },
            tooltip: {
                callbacks: {
                    label: function(tooltipItem) {
                        return tooltipItem.label + ': ' + tooltipItem.raw;
                    }
                }
            }
        }
    }
});

    </script>

    <script>
        // Export to CSV
document.getElementById('exportCsv').addEventListener('click', function() {
    const labels = salesChartData.labels;
    const data = salesChartData.datasets[0].data;

    let csvContent = "Label, Sales\n";
    labels.forEach((label, index) => {
        csvContent += `${label}, ${data[index]}\n`;
    });

    const link = document.createElement('a');
    link.href = 'data:text/csv;charset=utf-8,' + encodeURIComponent(csvContent);
    link.download = 'sales_data.csv';
    link.click();
});

// Export to Excel (using SheetJS)
document.getElementById('exportExcel').addEventListener('click', function() {
    const labels = salesChartData.labels;
    const data = salesChartData.datasets[0].data;

    const workbook = XLSX.utils.book_new();
    const worksheet = XLSX.utils.aoa_to_sheet([['Label', 'Sales'], ...labels.map((label, index) => [label, data[index]])]);
    XLSX.utils.book_append_sheet(workbook, worksheet, 'Sales Data');
    XLSX.writeFile(workbook, 'sales_data.xlsx');
});

// Export to PDF (using jsPDF)
document.getElementById('exportPdf').addEventListener('click', function() {
    const labels = salesChartData.labels;
    const data = salesChartData.datasets[0].data;

    const doc = new jsPDF();
    doc.text('Sales Data', 10, 10);
    labels.forEach((label, index) => {
        doc.text(`${label}: ${data[index]}`, 10, 20 + index * 10);
    });

    doc.save('sales_data.pdf');
});
    </script>

<?php include('./view/admin_footer.php'); ?>



