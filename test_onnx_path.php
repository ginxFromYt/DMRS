<?php

require_once 'bootstrap/app.php';

$app = app();

// Test path resolution
$onnxModelPath = env('ONNX_MODEL_PATH');
$onnxModel = $onnxModelPath ? base_path($onnxModelPath) : base_path('models/document_detector.onnx');

echo "ONNX_MODEL_PATH from env: " . ($onnxModelPath ?: 'NULL') . "\n";
echo "Constructed path: " . $onnxModel . "\n";
echo "File exists: " . (file_exists($onnxModel) ? 'YES' : 'NO') . "\n";
echo "Base path: " . base_path() . "\n";

// Also test the Python script
$pythonScript = base_path('scripts/onnx_inference.py');
echo "Python script path: " . $pythonScript . "\n";
echo "Python script exists: " . (file_exists($pythonScript) ? 'YES' : 'NO') . "\n";
