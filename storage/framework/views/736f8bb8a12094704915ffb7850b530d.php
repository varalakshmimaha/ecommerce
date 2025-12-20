<?php $__env->startSection('title', 'Overall Sales Report'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <!-- Header with Title and Filters -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 bg-white p-6 rounded-lg shadow">
        <h2 class="text-3xl font-bold text-gray-900">Overall Sales Report</h2>
        <form id="filter-form" class="flex flex-col md:flex-row gap-2">
            <div class="flex gap-2">
                <input type="date" id="start_date" name="start_date" value="<?php echo e($startDate); ?>" class="input-field px-3 py-2 border border-gray-300 rounded">
                <span class="flex items-center text-gray-500">to</span>
                <input type="date" id="end_date" name="end_date" value="<?php echo e($endDate); ?>" class="input-field px-3 py-2 border border-gray-300 rounded">
            </div>
            <button type="submit" class="bg-gradient-to-r from-[#D4AF37] to-[#B8962E] text-white px-6 py-3 rounded-lg font-semibold hover:shadow-lg transform hover:-translate-y-0.5 transition-all duration-300 px-6">Filter</button>
            <a href="javascript:void(0)" onclick="downloadReport()" class="btn px-6 text-center">Download</a>
        </form>
    </div>

    <!-- Key Metrics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-gradient-to-br from-blue-50 to-blue-100 p-6 rounded-lg shadow hover:shadow-lg transition">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 font-medium">Total Orders</p>
                    <p id="total-orders" class="text-3xl font-bold text-[#D4AF37] mt-2">0</p>
                </div>
                <svg class="w-12 h-12 text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                </svg>
            </div>
        </div>
        <div class="bg-gradient-to-br from-green-50 to-green-100 p-6 rounded-lg shadow hover:shadow-lg transition">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 font-medium">Total Revenue</p>
                    <p id="total-revenue" class="text-3xl font-bold text-green-600 mt-2">₹0</p>
                </div>
                <svg class="w-12 h-12 text-green-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
        </div>
        <div class="bg-gradient-to-br from-purple-50 to-purple-100 p-6 rounded-lg shadow hover:shadow-lg transition">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 font-medium">Total Subtotal</p>
                    <p id="total-subtotal" class="text-3xl font-bold text-purple-600 mt-2">₹0</p>
                </div>
                <svg class="w-12 h-12 text-purple-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                </svg>
            </div>
        </div>
        <div class="bg-gradient-to-br from-orange-50 to-orange-100 p-6 rounded-lg shadow hover:shadow-lg transition">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 font-medium">Total GST</p>
                    <p id="total-gst" class="text-3xl font-bold text-orange-600 mt-2">₹0</p>
                </div>
                <svg class="w-12 h-12 text-orange-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                </svg>
            </div>
        </div>
    </div>

    <!-- Breakdown Section -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-white p-6 rounded-lg shadow">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Breakdown</h3>
            <div class="space-y-3">
                <div class="flex justify-between items-center p-3 bg-gray-50 rounded">
                    <span class="text-gray-600">Subtotal</span>
                    <span id="breakdown-subtotal" class="font-semibold text-gray-900">₹0</span>
                </div>
                <div class="flex justify-between items-center p-3 bg-gray-50 rounded">
                    <span class="text-gray-600">Total GST</span>
                    <span id="breakdown-gst" class="font-semibold text-gray-900">₹0</span>
                </div>
                <div class="flex justify-between items-center p-3 bg-gray-50 rounded">
                    <span class="text-gray-600">Total Shipping</span>
                    <span id="breakdown-shipping" class="font-semibold text-gray-900">₹0</span>
                </div>
            </div>
        </div>
        <div class="bg-white p-6 rounded-lg shadow">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Summary</h3>
            <div class="space-y-2">
                <div class="flex justify-between items-center py-2 border-b">
                    <span class="text-gray-600">Date Range</span>
                    <span id="date-range" class="font-semibold">-</span>
                </div>
                <div class="flex justify-between items-center py-2 border-b">
                    <span class="text-gray-600">Avg Order Value</span>
                    <span id="avg-order-value" class="font-semibold text-gray-900">₹0</span>
                </div>
                <div class="flex justify-between items-center py-2 text-lg font-bold text-[#D4AF37] bg-primary-50 px-3 py-3 rounded mt-4">
                    <span>Total Amount</span>
                    <span id="total-amount">₹0</span>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function loadReportData() {
    const startDate = document.getElementById('start_date').value;
    const endDate = document.getElementById('end_date').value;
    
    fetch(`<?php echo e(url('admin/reports/overall-sales')); ?>?start_date=${startDate}&end_date=${endDate}&json=1`)
        .then(res => res.json())
        .then(data => {
            const stats = data.stats || {};
            const orders = stats.total_orders || 0;
            const revenue = parseFloat(stats.total_revenue || 0);
            const subtotal = parseFloat(stats.total_subtotal || 0);
            const gst = parseFloat(stats.total_gst || 0);
            const shipping = parseFloat(stats.total_shipping || 0);
            
            document.getElementById('total-orders').textContent = orders;
            document.getElementById('total-revenue').textContent = '₹' + revenue.toFixed(2);
            document.getElementById('total-subtotal').textContent = '₹' + subtotal.toFixed(2);
            document.getElementById('total-gst').textContent = '₹' + gst.toFixed(2);
            document.getElementById('breakdown-subtotal').textContent = '₹' + subtotal.toFixed(2);
            document.getElementById('breakdown-gst').textContent = '₹' + gst.toFixed(2);
            document.getElementById('breakdown-shipping').textContent = '₹' + shipping.toFixed(2);
            document.getElementById('total-amount').textContent = '₹' + revenue.toFixed(2);
            document.getElementById('date-range').textContent = startDate + ' to ' + endDate;
            document.getElementById('avg-order-value').textContent = orders > 0 ? '₹' + (revenue / orders).toFixed(2) : '₹0';
        })
        .catch(err => console.error('Error loading report:', err));
}

document.getElementById('filter-form').addEventListener('submit', function(e) {
    e.preventDefault();
    loadReportData();
});

function downloadReport() {
    const startDate = document.getElementById('start_date').value;
    const endDate = document.getElementById('end_date').value;
    window.location.href = `<?php echo e(url('admin/reports/overall-sales')); ?>?start_date=${startDate}&end_date=${endDate}&download=1`;
}

// Load initial data
loadReportData();
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/bestprime-ecommerce/htdocs/ecommerce.bestprime.live/suvee/resources/views/admin/reports/overall-sales.blade.php ENDPATH**/ ?>