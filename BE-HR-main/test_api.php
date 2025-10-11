<?php
// Test API endpoints nhanh
require_once 'vendor/autoload.php';

// Bootstrap ứng dụng Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Salary;
use App\Models\Employee;

echo "=== TEST API BACKEND ===\n";

try {
    echo "1. Kiểm tra kết nối database...\n";
    $employees = Employee::count();
    echo "   Số employees: $employees\n";
    
    $salaries = Salary::count();
    echo "   Số salaries: $salaries\n";
    
    echo "\n2. Kiểm tra dữ liệu salaries...\n";
    $allSalaries = Salary::with('employee')->get();
    
    if($allSalaries->count() > 0) {
        echo "   Có {$allSalaries->count()} bản ghi salary:\n";
        foreach($allSalaries->take(3) as $salary) {
            echo "   - ID: {$salary->id}, Employee: " . ($salary->employee->name ?? 'N/A') . ", Amount: {$salary->amount}\n";
        }
    } else {
        echo "   Không có dữ liệu salary nào!\n";
        
        echo "\n3. Tạo dữ liệu mẫu...\n";
        $employee = Employee::first();
        if($employee) {
            $testSalary = Salary::create([
                'employee_id' => $employee->id,
                'amount' => 5000000,
                'month' => '2025-10-01',
                'note' => 'Test salary'
            ]);
            echo "   Đã tạo salary test với ID: {$testSalary->id}\n";
        } else {
            echo "   Không có employee nào để tạo salary!\n";
        }
    }
    
} catch (Exception $e) {
    echo "LỖI: " . $e->getMessage() . "\n";
}

echo "\n=== KẾT THÚC TEST ===\n";