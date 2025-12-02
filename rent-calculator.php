<?php
session_start();
require 'db.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rent Calculator - RoomFinder</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Pacifico&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/4.6.0/remixicon.min.css">
    <script src="https://cdn.tailwindcss.com/3.4.16"></script>
    <style>
        body { font-family: 'Inter', sans-serif; }
        .calculator-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 20px;
            padding: 40px;
            color: white;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
        }
        .result-card {
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            margin-top: 30px;
        }
    </style>
</head>
<body class="bg-gray-50 min-h-screen">
    <!-- Header -->
    <header class="bg-white shadow-sm sticky top-0 z-50">
        <div class="container mx-auto px-4 py-3 flex items-center justify-between">
            <a href="index.php" class="text-2xl font-['Pacifico'] text-primary" style="color:#4A90E2;">RoomFinder</a>
            <nav class="hidden md:flex items-center space-x-6">
                <a href="index.php" class="text-gray-700 hover:text-primary transition-colors">Home</a>
                <a href="find-rooms.php" class="text-gray-700 hover:text-primary transition-colors">Find Rooms</a>
                <a href="rent-calculator.php" class="text-primary font-semibold">Rent Calculator</a>
            </nav>
        </div>
    </header>

    <div class="container mx-auto px-4 py-12 max-w-4xl">
        <div class="text-center mb-10">
            <h1 class="text-4xl font-bold mb-4" style="font-family:'Pacifico',cursive;color:#4A90E2;">Rent Calculator</h1>
            <p class="text-gray-600 text-lg">Calculate your total monthly and upfront costs for renting a property</p>
        </div>

        <div class="calculator-card">
            <h2 class="text-2xl font-bold mb-6">Enter Property Details</h2>
            <form id="calculatorForm" class="space-y-6">
                <div class="grid md:grid-cols-2 gap-6">
                    <div>
                        <label class="block mb-2 font-semibold">Monthly Rent (¥)</label>
                        <input type="number" id="monthlyRent" class="w-full px-4 py-3 rounded-lg text-gray-900" placeholder="e.g. 80000" required>
                    </div>
                    <div>
                        <label class="block mb-2 font-semibold">Utilities Cost (¥/month)</label>
                        <input type="number" id="utilities" class="w-full px-4 py-3 rounded-lg text-gray-900" placeholder="e.g. 5000" value="0">
                    </div>
                    <div>
                        <label class="block mb-2 font-semibold">Management Fee (¥/month)</label>
                        <input type="number" id="managementFee" class="w-full px-4 py-3 rounded-lg text-gray-900" placeholder="e.g. 3000" value="0">
                    </div>
                    <div>
                        <label class="block mb-2 font-semibold">Deposit (months of rent)</label>
                        <input type="number" id="depositMonths" class="w-full px-4 py-3 rounded-lg text-gray-900" placeholder="e.g. 2" value="0" step="0.5">
                    </div>
                    <div>
                        <label class="block mb-2 font-semibold">Key Money (months of rent)</label>
                        <input type="number" id="keyMoneyMonths" class="w-full px-4 py-3 rounded-lg text-gray-900" placeholder="e.g. 1" value="0" step="0.5">
                    </div>
                    <div>
                        <label class="block mb-2 font-semibold">Agency Fee (months of rent)</label>
                        <input type="number" id="agencyFeeMonths" class="w-full px-4 py-3 rounded-lg text-gray-900" placeholder="e.g. 1" value="0" step="0.5">
                    </div>
                </div>
                <button type="submit" class="w-full bg-white text-purple-600 py-4 rounded-lg font-bold text-lg hover:bg-gray-100 transition-colors">
                    <i class="ri-calculator-line mr-2"></i>Calculate Total Costs
                </button>
            </form>
        </div>

        <div id="results" class="result-card" style="display:none;">
            <h3 class="text-2xl font-bold mb-6 text-gray-800">Cost Breakdown</h3>
            <div class="space-y-4">
                <div class="flex justify-between items-center p-4 bg-gray-50 rounded-lg">
                    <span class="text-gray-700 font-medium">Monthly Rent:</span>
                    <span class="text-gray-900 font-bold" id="resultMonthlyRent">¥0</span>
                </div>
                <div class="flex justify-between items-center p-4 bg-gray-50 rounded-lg">
                    <span class="text-gray-700 font-medium">Utilities:</span>
                    <span class="text-gray-900 font-bold" id="resultUtilities">¥0</span>
                </div>
                <div class="flex justify-between items-center p-4 bg-gray-50 rounded-lg">
                    <span class="text-gray-700 font-medium">Management Fee:</span>
                    <span class="text-gray-900 font-bold" id="resultManagementFee">¥0</span>
                </div>
                <div class="border-t-2 border-gray-300 my-4"></div>
                <div class="flex justify-between items-center p-4 bg-blue-50 rounded-lg">
                    <span class="text-gray-800 font-semibold text-lg">Total Monthly Cost:</span>
                    <span class="text-blue-600 font-bold text-xl" id="resultMonthlyTotal">¥0</span>
                </div>
                <div class="border-t-2 border-gray-300 my-4"></div>
                <div class="flex justify-between items-center p-4 bg-gray-50 rounded-lg">
                    <span class="text-gray-700 font-medium">Deposit:</span>
                    <span class="text-gray-900 font-bold" id="resultDeposit">¥0</span>
                </div>
                <div class="flex justify-between items-center p-4 bg-gray-50 rounded-lg">
                    <span class="text-gray-700 font-medium">Key Money:</span>
                    <span class="text-gray-900 font-bold" id="resultKeyMoney">¥0</span>
                </div>
                <div class="flex justify-between items-center p-4 bg-gray-50 rounded-lg">
                    <span class="text-gray-700 font-medium">Agency Fee:</span>
                    <span class="text-gray-900 font-bold" id="resultAgencyFee">¥0</span>
                </div>
                <div class="border-t-2 border-gray-300 my-4"></div>
                <div class="flex justify-between items-center p-6 bg-gradient-to-r from-green-500 to-green-600 rounded-lg">
                    <span class="text-white font-bold text-xl">Total Upfront Cost:</span>
                    <span class="text-white font-bold text-2xl" id="resultUpfrontTotal">¥0</span>
                </div>
                <div class="mt-6 p-4 bg-yellow-50 border-l-4 border-yellow-400 rounded">
                    <p class="text-gray-700 text-sm">
                        <i class="ri-information-line text-yellow-600 mr-2"></i>
                        <strong>Note:</strong> These are estimated costs. Actual costs may vary. Always confirm with the property owner or agency.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('calculatorForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const monthlyRent = parseFloat(document.getElementById('monthlyRent').value) || 0;
            const utilities = parseFloat(document.getElementById('utilities').value) || 0;
            const managementFee = parseFloat(document.getElementById('managementFee').value) || 0;
            const depositMonths = parseFloat(document.getElementById('depositMonths').value) || 0;
            const keyMoneyMonths = parseFloat(document.getElementById('keyMoneyMonths').value) || 0;
            const agencyFeeMonths = parseFloat(document.getElementById('agencyFeeMonths').value) || 0;
            
            // Calculate
            const monthlyTotal = monthlyRent + utilities + managementFee;
            const deposit = monthlyRent * depositMonths;
            const keyMoney = monthlyRent * keyMoneyMonths;
            const agencyFee = monthlyRent * agencyFeeMonths;
            const upfrontTotal = deposit + keyMoney + agencyFee;
            
            // Display results
            document.getElementById('resultMonthlyRent').textContent = '¥' + monthlyRent.toLocaleString();
            document.getElementById('resultUtilities').textContent = '¥' + utilities.toLocaleString();
            document.getElementById('resultManagementFee').textContent = '¥' + managementFee.toLocaleString();
            document.getElementById('resultMonthlyTotal').textContent = '¥' + monthlyTotal.toLocaleString();
            document.getElementById('resultDeposit').textContent = '¥' + deposit.toLocaleString();
            document.getElementById('resultKeyMoney').textContent = '¥' + keyMoney.toLocaleString();
            document.getElementById('resultAgencyFee').textContent = '¥' + agencyFee.toLocaleString();
            document.getElementById('resultUpfrontTotal').textContent = '¥' + upfrontTotal.toLocaleString();
            
            document.getElementById('results').style.display = 'block';
            document.getElementById('results').scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        });
    </script>
</body>
</html>

