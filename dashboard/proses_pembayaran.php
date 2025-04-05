<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Ensure no output before this line
include '../db_connection/db.php';

// Force JSON output
header('Content-Type: application/json');

// Decode JSON input
$data = json_decode(file_get_contents("php://input"), true);
if (json_last_error() !== JSON_ERROR_NONE) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid JSON input.']);
    exit;
}

if (isset($data['id'])) {
    $id = intval($data['id']); // Ensure ID is an integer
    $caraBayaran = mysqli_real_escape_string($conn, $data['caraBayaran']); // Prevent SQL injection

    // Get the current payment values
    $query = "SELECT * FROM bayaran WHERE id = $id";
    $result = mysqli_query($conn, $query);
    if (!$result) {
        error_log("Database error: " . mysqli_error($conn)); // Log the error
        echo json_encode(['status' => 'error', 'message' => 'Database error occurred.']);
        exit;
    }
    $row = mysqli_fetch_assoc($result);

    if ($row) {
        // Get student IC
        $noKad = $row['noKad'];
    
        // Find namaWarisPelajar of the student
        $parentQuery = "SELECT namaWarisPelajar FROM daftar_pelajar WHERE noKad = ?";
        $parentStmt = $conn->prepare($parentQuery);
        if (!$parentStmt) {
            error_log("Database error: " . $conn->error); // Log the error
            echo json_encode(['status' => 'error', 'message' => 'Database error occurred.']);
            exit;
        }
        $parentStmt->bind_param("s", $noKad);
        $parentStmt->execute();
        $parentResult = $parentStmt->get_result();
        $student = $parentResult->fetch_assoc();
    
        if ($student) {
            $namaWarisPelajar = $student['namaWarisPelajar'];
        }
    
        // Initialize total new payment
        $totalNewPayment = 0;
    
        // Special condition: Only check siblings' payments for Dana PIBG
        if (isset($data['jum_bayar_dana_pibg']) && floatval($data['jum_bayar_dana_pibg']) > 0) {
            $checkQuery = "
                SELECT COUNT(*) AS count FROM bayaran b
                JOIN daftar_pelajar dp ON b.noKad = dp.noKad
                WHERE dp.namaWarisPelajar = ? AND b.jum_bayar_dana_pibg > 0
            ";
            $checkStmt = $conn->prepare($checkQuery);
            if (!$checkStmt) {
                error_log("Database error: " . $conn->error); // Log the error
                echo json_encode(['status' => 'error', 'message' => 'Database error occurred.']);
                exit;
            }
            $checkStmt->bind_param("s", $namaWarisPelajar);
            $checkStmt->execute();
            $checkResult = $checkStmt->get_result();
            $checkRow = $checkResult->fetch_assoc();
    
            // If a sibling has already paid, prevent further payments
            if ($checkRow['count'] > 0) {
                echo json_encode(["status" => "error", "message" => "Dana PIBG sudah dibayar oleh salah seorang adik-beradik."]);
                exit;
            }
        }

        foreach ($data as $key => $value) {
            if (strpos($key, 'jum_bayar_') === 0) {
                $feeType = $key; // e.g., jum_bayar_tuisyen
                $amount = floatval($value);
        
                // Skip if amount is zero
                if ($amount <= 0) {
                    continue; // No payment, skip to next fee type
                }
        
                // Get the original fee for this fee type
                $originalFeeKey = str_replace('jum_bayar_', '', $feeType); // e.g., tuisyen -> tuisyen
                $originalFee = floatval($row[$originalFeeKey] ?? 0);
        
                // Get the amount already paid for this fee type
                $alreadyPaid = isset($row[$feeType]) ? floatval($row[$feeType]) : 0.00;
        
                // Skip if the fee has already been fully paid
                if ($alreadyPaid >= $originalFee) {
                    // If fully paid, reset the amount to 0.00
                    $amount = 0.00;
                }
        
                // Calculate the total amount paid before this transaction
                $totalPaymentsQuery = "SELECT SUM(jumlah_bayar) as totalPaid FROM transaksi_bayaran WHERE bayaran_id = $id AND fee_type = '$feeType'";
                $totalResult = mysqli_query($conn, $totalPaymentsQuery);
                if (!$totalResult) {
                    error_log("Database error: " . mysqli_error($conn)); // Log the error
                    echo json_encode(['status' => 'error', 'message' => 'Database error occurred.']);
                    exit;
                }
                $totalRow = mysqli_fetch_assoc($totalResult);
                $totalPaidBefore = round(floatval($totalRow['totalPaid']), 2); // Total before this transaction
        
                $totalPaidAfter = $totalPaidBefore + $amount; // New total paid including this transaction
        
                $bakiFee = round($originalFee - $totalPaidAfter, 2); // Correct balance calculation
        
                // Ensure balance does not go negative
                if ($bakiFee < 0) {
                    echo json_encode([
                        'status' => 'error',
                        'message' => 'Jumlah pembayaran melebihi yuran asal untuk fee type: ' . $feeType,
                    ]);
                    exit;
                }
        
                // Insert new transaction
                $insertTransactionQuery = "INSERT INTO transaksi_bayaran (bayaran_id, fee_type, jumlah_bayar, payment_time, caraBayaran) 
                                            VALUES ($id, '$feeType', $amount, NOW(), '$caraBayaran')";
        
                if (!mysqli_query($conn, $insertTransactionQuery)) {
                    error_log("Database error: " . mysqli_error($conn)); // Log the error
                    echo json_encode(['status' => 'error', 'message' => 'Database error occurred.']);
                    exit;
                }
        
                // Update the `bayaran` table with the correct balance
                $updateQuery = "UPDATE bayaran SET 
                $feeType = $totalPaidAfter 
                WHERE id = $id";
        
                if ($bakiFee !== floatval($row["baki_$originalFeeKey"])) { 
                    // Update baki only if a payment was made
                    $updateQuery = "UPDATE bayaran SET 
                        $feeType = $totalPaidAfter, 
                        baki_$originalFeeKey = $bakiFee 
                    WHERE id = $id";
                }
        
                if (!mysqli_query($conn, $updateQuery)) {
                    error_log("Database error: " . mysqli_error($conn)); // Log the error
                    echo json_encode(['status' => 'error', 'message' => 'Database error occurred.']);
                    exit;
                }
        
                // Update total new payment
                $totalNewPayment += $amount;
            }
        }
        

        // Update overall total payment and balance
        $newJumlahBayar = round(floatval($row['jumlahBayar'] ?? 0) + $totalNewPayment, 2);
        $baki = round(floatval($row['jumlahYuran'] ?? 0) - $newJumlahBayar, 2);

        $updateTotalQuery = "UPDATE bayaran SET 
            jumlahBayar = $newJumlahBayar,
            baki = $baki,
            caraBayaran = '$caraBayaran',
            payment_time = NOW()
        WHERE id = $id";

        if (mysqli_query($conn, $updateTotalQuery)) {
            // Fetch updated data for response
            $query = "SELECT * FROM bayaran WHERE id = $id";
            $result = mysqli_query($conn, $query);
            if (!$result) {
                error_log("Database error: " . mysqli_error($conn)); // Log the error
                echo json_encode(['status' => 'error', 'message' => 'Database error occurred.']);
                exit;
            }
            $updatedRow = mysqli_fetch_assoc($result);

            echo json_encode([
                'status' => 'success',
                'jumlahBayar' => $newJumlahBayar,
                'baki' => $baki,
                'baki_dana_pibg' => $updatedRow['baki_dana_pibg'] ?? 0,
                'baki_tuisyen' => $updatedRow['baki_tuisyen'] ?? 0,
                'baki_massak' => $updatedRow['baki_massak'] ?? 0,
                'baki_majalah' => $updatedRow['baki_majalah'] ?? 0,
                'baki_hac' => $updatedRow['baki_hac'] ?? 0,
                'baki_kertas_peperiksaan' => $updatedRow['baki_kertas_peperiksaan'] ?? 0,
                'baki_bas' => $updatedRow['baki_bas'] ?? 0,
                'baki_dobi' => $updatedRow['baki_dobi'] ?? 0,
                'redirect' => "receipt.php?id=$id" // Redirect to receipt page
            ]);
        } else {
            error_log("Database error: " . mysqli_error($conn)); // Log the error
            echo json_encode(['status' => 'error', 'message' => 'Database error occurred.']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Data pelajar tidak dijumpai.']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid input data.']);
}
?>