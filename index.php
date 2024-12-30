<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Advanced Calculator</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f2f5;
            margin: 0;
            padding: 20px;
        }
        .calculator-container {
            max-width: 600px;
            margin: 0 auto;
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #1a73e8;
            text-align: center;
            margin-bottom: 30px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            color: #555;
            font-weight: bold;
        }
        select, input[type="number"] {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
        }
        .input-group {
            display: flex;
            gap: 10px;
            margin-bottom: 15px;
        }
        .checkbox-group {
            margin: 15px 0;
        }
        button {
            background-color: #1a73e8;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            width: 100%;
            font-size: 16px;
        }
        button:hover {
            background-color: #1557b0;
        }
        .result {
            margin-top: 20px;
            padding: 15px;
            background-color: #f8f9fa;
            border-radius: 4px;
            border-left: 4px solid #1a73e8;
        }
        .error {
            border-left: 4px solid #dc3545;
            color: #dc3545;
        }
        .steps {
            margin-top: 10px;
            padding: 10px;
            background-color: #e8f0fe;
            border-radius: 4px;
        }
    </style>
</head>
<body>
    <?php
    function calculateResult($operation, $num1, $num2 = null, $verbose = false) {
        $result = '';
        $steps = '';
        
        switch ($operation) {
            case 'add':
                if ($num2 === null) return ['error' => 'Addition requires two numbers'];
                $result = $num1 + $num2;
                if ($verbose) $steps = "$num1 + $num2 = $result";
                break;
                
            case 'subtract':
                if ($num2 === null) return ['error' => 'Subtraction requires two numbers'];
                $result = $num1 - $num2;
                if ($verbose) $steps = "$num1 - $num2 = $result";
                break;
                
            case 'multiply':
                if ($num2 === null) return ['error' => 'Multiplication requires two numbers'];
                $result = $num1 * $num2;
                if ($verbose) $steps = "$num1 × $num2 = $result";
                break;
                
            case 'divide':
                if ($num2 === null) return ['error' => 'Division requires two numbers'];
                if ($num2 == 0) return ['error' => 'Division by zero is not allowed'];
                $result = $num1 / $num2;
                if ($verbose) $steps = "$num1 ÷ $num2 = $result";
                break;
                
            case 'mod':
                if ($num2 === null) return ['error' => 'Modulo requires two numbers'];
                if ($num2 == 0) return ['error' => 'Modulo by zero is not allowed'];
                $result = $num1 % $num2;
                if ($verbose) $steps = "$num1 mod $num2 = $result";
                break;
                
            case 'power':
                if ($num2 === null) return ['error' => 'Power requires two numbers'];
                $result = pow($num1, $num2);
                if ($verbose) $steps = "$num1 ^ $num2 = $result";
                break;
                
            case 'fibonacci':
                if ($num1 < 0) return ['error' => 'Fibonacci requires a non-negative number'];
                $result = calculateFibonacci($num1, $verbose);
                break;
                
            case 'factorial':
                if ($num1 < 0) return ['error' => 'Factorial requires a non-negative number'];
                $result = calculateFactorial($num1, $verbose);
                break;
                
            default:
                return ['error' => 'Invalid operation'];
        }
        
        return [
            'result' => $result,
            'steps' => $steps
        ];
    }

    function calculateFibonacci($n, $verbose) {
        if ($n <= 1) return $n;
        
        $fib = [0, 1];
        $steps = $verbose ? "F(0) = 0\nF(1) = 1\n" : '';
        
        for ($i = 2; $i <= $n; $i++) {
            $fib[$i] = $fib[$i-1] + $fib[$i-2];
            if ($verbose) {
                $steps .= "F($i) = F(" . ($i-1) . ") + F(" . ($i-2) . ") = {$fib[$i-1]} + {$fib[$i-2]} = {$fib[$i]}\n";
            }
        }
        
        return [
            'value' => $fib[$n],
            'steps' => $steps
        ];
    }

    function calculateFactorial($n, $verbose) {
        $result = 1;
        $steps = '';
        
        for ($i = 1; $i <= $n; $i++) {
            $result *= $i;
            if ($verbose) {
                $steps .= "$i! = " . ($result / $i) . " × $i = $result\n";
            }
        }
        
        return [
            'value' => $result,
            'steps' => $steps
        ];
    }

    $result = null;
    $error = null;
    $steps = null;

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $operation = $_POST['operation'];
        $num1 = $_POST['num1'];
        $num2 = isset($_POST['num2']) ? $_POST['num2'] : null;
        $verbose = isset($_POST['verbose']);
        
        if (!is_numeric($num1) || ($num2 !== null && !is_numeric($num2) && 
            !in_array($operation, ['fibonacci', 'factorial']))) {
            $error = "Please enter valid numbers";
        } else {
            $calculation = calculateResult($operation, floatval($num1), $num2 ? floatval($num2) : null, $verbose);
            
            if (isset($calculation['error'])) {
                $error = $calculation['error'];
            } else {
                if (is_array($calculation['result'])) {
                    $result = $calculation['result']['value'];
                    $steps = $calculation['result']['steps'];
                } else {
                    $result = $calculation['result'];
                    $steps = $calculation['steps'];
                }
            }
        }
    }
    ?>

    <div class="calculator-container">
        <h1>Advanced Calculator</h1>
        
        <form method="post">
            <div class="form-group">
                <label for="operation">Operation:</label>
                <select name="operation" id="operation" required>
                    <option value="add">Addition (+)</option>
                    <option value="subtract">Subtraction (-)</option>
                    <option value="multiply">Multiplication (×)</option>
                    <option value="divide">Division (÷)</option>
                    <option value="mod">Modulo (%)</option>
                    <option value="power">Power (^)</option>
                    <option value="fibonacci">Fibonacci</option>
                    <option value="factorial">Factorial</option>
                </select>
            </div>

            <div class="input-group">
                <div class="form-group">
                    <label for="num1">First Number:</label>
                    <input type="number" name="num1" id="num1" required step="any">
                </div>

                <div class="form-group">
                    <label for="num2">Second Number:</label>
                    <input type="number" name="num2" id="num2" step="any">
                </div>
            </div>

            <div class="checkbox-group">
                <label>
                    <input type="checkbox" name="verbose"> Show calculation steps
                </label>
            </div>

            <button type="submit">Calculate</button>
        </form>

        <?php if ($error): ?>
            <div class="result error">
                Error: <?php echo htmlspecialchars($error); ?>
            </div>
        <?php elseif ($result !== null): ?>
            <div class="result">
                Result: <?php echo htmlspecialchars($result); ?>
                <?php if ($steps): ?>
                    <div class="steps">
                        Steps:<br>
                        <?php echo nl2br(htmlspecialchars($steps)); ?>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>

    <script>
        document.getElementById('operation').addEventListener('change', function() {
            const num2Input = document.getElementById('num2');
            const num2Container = num2Input.parentElement;
            
            if (this.value === 'fibonacci' || this.value === 'factorial') {
                num2Container.style.display = 'none';
                num2Input.removeAttribute('required');
            } else {
                num2Container.style.display = 'block';
                num2Input.setAttribute('required', 'required');
            }
        });
    </script>
</body>
</html>
