<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Loan Amortization Calculator</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <style>
        :root {
            --primary-color: #2563eb;
            --secondary-color: #1e40af;
            --background-color: #f3f4f6;
            --border-color: #e5e7eb;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 20px;
            background-color: var(--background-color);
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .calculator-form {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
        }

        label {
            font-weight: 600;
            margin-bottom: 8px;
        }

        input {
            padding: 10px;
            border: 1px solid var(--border-color);
            border-radius: 4px;
            font-size: 16px;
        }

        button {
            background-color: var(--primary-color);
            color: white;
            padding: 12px 24px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            font-weight: 600;
            transition: background-color 0.2s;
        }

        button:hover {
            background-color: var(--secondary-color);
        }

        .results {
            margin-top: 30px;
        }

        .summary {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
            padding: 20px;
            background-color: var(--background-color);
            border-radius: 8px;
        }

        .summary-item {
            text-align: center;
        }

        .summary-label {
            font-size: 14px;
            color: #6b7280;
        }

        .summary-value {
            font-size: 24px;
            font-weight: 600;
            color: var(--primary-color);
        }

        .schedule {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid var(--border-color);
        }

        th {
            background-color: var(--background-color);
            font-weight: 600;
        }

        tr:nth-child(even) {
            background-color: var(--background-color);
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Loan Amortization Calculator</h1>
        
        <form id="calculatorForm" class="calculator-form">
            <div class="form-group">
                <label for="principal">Principal Amount ($)</label>
                <input type="number" id="principal" name="principal" required min="1" value="100000">
            </div>
            
            <div class="form-group">
                <label for="interest">Annual Interest Rate (%)</label>
                <input type="number" id="interest" name="interest" required min="0.01" step="0.01" value="5">
            </div>
            
            <div class="form-group">
                <label for="years">Loan Term (Years)</label>
                <input type="number" id="years" name="years" required min="1" value="15">
            </div>
            
            <div class="form-group">
                <label for="startDate">Start Date</label>
                <input type="date" id="startDate" name="startDate" required value="2024-01-01">
            </div>
            
            <div class="form-group">
                <button type="submit">Calculate</button>
            </div>
        </form>

        <div class="results" style="display: none;">
            <h2>Loan Summary</h2>
            <div class="summary">
                <div class="summary-item">
                    <div class="summary-label">Monthly Payment</div>
                    <div class="summary-value" id="monthlyPayment">$0</div>
                </div>
                <div class="summary-item">
                    <div class="summary-label">Total Interest</div>
                    <div class="summary-value" id="totalInterest">$0</div>
                </div>
                <div class="summary-item">
                    <div class="summary-label">Total Payment</div>
                    <div class="summary-value" id="totalPayment">$0</div>
                </div>
            </div>

            <h2>Amortization Schedule</h2>
            <div class="schedule">
                <table>
                    <thead>
                        <tr>
                            <th>Payment #</th>
                            <th>Date</th>
                            <th>Payment</th>
                            <th>Principal</th>
                            <th>Interest</th>
                            <th>Remaining Balance</th>
                        </tr>
                    </thead>
                    <tbody id="scheduleBody"></tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            $('#calculatorForm').on('submit', function(e) {
                e.preventDefault();
                
                const formData = {
                    principal: $('#principal').val(),
                    interest: $('#interest').val(),
                    years: $('#years').val(),
                    startDate: $('#startDate').val()
                };

                // Normally this would be an AJAX call to your PHP backend
                // For demo purposes, we'll calculate directly in JavaScript
                calculateLoan(formData);
            });

            function calculateLoan(data) {
                const principal = parseFloat(data.principal);
                const annualRate = parseFloat(data.interest) / 100;
                const monthlyRate = annualRate / 12;
                const payments = parseFloat(data.years) * 12;
                const startDate = new Date(data.startDate);

                // Calculate monthly payment
                const monthlyPayment = principal * 
                    (monthlyRate * Math.pow(1 + monthlyRate, payments)) / 
                    (Math.pow(1 + monthlyRate, payments) - 1);

                let balance = principal;
                let totalInterest = 0;
                const schedule = [];

                for (let i = 1; i <= payments; i++) {
                    const interestPayment = balance * monthlyRate;
                    const principalPayment = monthlyPayment - interestPayment;
                    totalInterest += interestPayment;
                    balance -= principalPayment;

                    const paymentDate = new Date(startDate);
                    paymentDate.setMonth(startDate.getMonth() + i - 1);

                    schedule.push({
                        number: i,
                        date: paymentDate.toISOString().split('T')[0],
                        payment: monthlyPayment,
                        principal: principalPayment,
                        interest: interestPayment,
                        balance: Math.max(0, balance)
                    });
                }

                displayResults(monthlyPayment, totalInterest, monthlyPayment * payments, schedule);
            }

            function displayResults(monthlyPayment, totalInterest, totalPayment, schedule) {
                // Update summary
                $('#monthlyPayment').text('$' + monthlyPayment.toFixed(2));
                $('#totalInterest').text('$' + totalInterest.toFixed(2));
                $('#totalPayment').text('$' + totalPayment.toFixed(2));

                // Update schedule
                const tbody = $('#scheduleBody');
                tbody.empty();

                schedule.forEach(payment => {
                    tbody.append(`
                        <tr>
                            <td>${payment.number}</td>
                            <td>${payment.date}</td>
                            <td>$${payment.payment.toFixed(2)}</td>
                            <td>$${payment.principal.toFixed(2)}</td>
                            <td>$${payment.interest.toFixed(2)}</td>
                            <td>$${payment.balance.toFixed(2)}</td>
                        </tr>
                    `);
                });

                // Show results
                $('.results').show();
            }
        });
    </script>
</body>
</html>