# Document Processing Setup Guide

## Overview
This system processes document images using:
1. **ONNX ML Model** for object detection (document numbers, text blocks, signatures)
2. **Google Cloud Vision API** for OCR text extraction
3. **Laravel backend** for file handling and workflow orchestration

## Prerequisites

### 1. PHP Dependencies (Already Installed)
- Google Cloud Vision API: `google/cloud-vision`
- Image Processing: `intervention/image`

### 2. Python Environment Setup
```bash
# Navigate to scripts directory
cd scripts

# Install Python dependencies
pip install -r requirements.txt
```

### 3. Google Cloud Setup
1. Create a Google Cloud Project
2. Enable the Vision API
3. Create a Service Account
4. Download the JSON key file
5. Set environment variables:

```env
GOOGLE_CLOUD_PROJECT_ID=your-project-id
GOOGLE_CLOUD_KEY_FILE=path/to/service-account-key.json
```

### 4. ONNX Model Setup
1. Train or obtain an ONNX model for document object detection
2. Place the model file in your desired location
3. Set environment variable:

```env
ONNX_MODEL_PATH=path/to/your/document_detector.onnx
CONFIDENCE_THRESHOLD=0.5
```

## File Structure
```
DMRS/
├── app/
│   ├── Http/Controllers/AdminControllers/
│   │   └── DocumentHandlingController.php
│   └── Services/
│       └── DocumentProcessingService.php
├── scripts/
│   ├── onnx_inference.py
│   └── requirements.txt
├── storage/
│   └── app/
│       ├── public/
│       │   ├── uploads/     # Uploaded images
│       │   └── documents/   # Processed documents
│       └── temp/            # Temporary processing files
└── models/                  # ONNX model files (create this directory)
    └── document_detector.onnx
```

## Usage Workflow

### 1. Upload Image
- Admin uploads image via dashboard form
- Image is stored in `storage/app/public/uploads/`

### 2. ONNX Object Detection
- System calls Python script with uploaded image
- ONNX model detects objects (document numbers, text blocks, etc.)
- Returns bounding boxes and confidence scores

### 3. Google Cloud Vision OCR
- Full image text extraction
- Individual object text extraction (cropped regions)
- Text analysis and document number extraction

### 4. Results Display
- Processing results shown on dashboard
- Detected objects with confidence scores
- Extracted text with document numbers highlighted
- Visual feedback for users

## Configuration Options

### Environment Variables
```env
# Google Cloud Vision
GOOGLE_CLOUD_PROJECT_ID=your-project-id
GOOGLE_CLOUD_KEY_FILE=path/to/service-account-key.json

# ONNX Model
ONNX_MODEL_PATH=models/document_detector.onnx
CONFIDENCE_THRESHOLD=0.5

# Processing
MAX_PROCESSING_TIME=300
PROCESSING_TEMP_DIR=storage/app/temp
```

### Model Classes
Update the Python script class mapping based on your model:
```python
class_names = {
    0: 'document_number',
    1: 'text_block',
    2: 'signature',
    3: 'logo',
    4: 'table',
    5: 'barcode',
    # Add your specific classes
}
```

## Troubleshooting

### Common Issues

1. **Python Script Not Found**
   - Ensure `scripts/onnx_inference.py` exists
   - Check file permissions

2. **ONNX Model Issues**
   - Verify model file path in environment
   - Check model format compatibility
   - Ensure Python dependencies are installed

3. **Google Cloud Vision Errors**
   - Verify service account credentials
   - Check API is enabled in Google Cloud Console
   - Ensure sufficient quota/billing

4. **Permission Issues**
   - Check storage directory permissions
   - Ensure PHP can execute Python scripts

### Fallback Behavior
- If ONNX model fails: Uses simulated object detection
- If Google Cloud Vision fails: Returns empty text
- Processing continues with available results

## Security Considerations
- Service account keys should be stored securely
- Uploaded files are validated for type and size
- Temporary files are cleaned up after processing
- Admin-only access enforced on all routes

## Performance Optimization
- Consider caching frequent OCR results
- Implement background job processing for large files
- Use image resizing for faster ONNX inference
- Monitor API usage and costs
