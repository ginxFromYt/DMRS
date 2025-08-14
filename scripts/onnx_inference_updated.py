#!/usr/bin/env python3
"""
ONNX Document Object Detection Script
This script loads an ONNX model and performs inference on document images
to detect document components like Document Number, Office Name, etc.

Based on C# model output format with classes:
0: 'CeoOfficeReleaseStamp'
1: 'DocNumContainer'
2: 'Document Number'
3: 'Document Type'
4: 'DocumentBody'
5: 'Office Name'
6: 'RecordOfficesStamp'
"""

import sys
import json
import numpy as np
import cv2
import onnxruntime as ort
from PIL import Image
import argparse

class DocumentONNXInference:
    def __init__(self, model_path, confidence_threshold=0.5, overlap_threshold=0.4):
        """
        Initialize the ONNX inference engine

        Args:
            model_path (str): Path to the ONNX model file
            confidence_threshold (float): Minimum confidence for detections
            overlap_threshold (float): IoU threshold for Non-Maximum Suppression (0.0-1.0)
                                     Lower values = more aggressive overlap removal
                                     Higher values = keep more overlapping boxes
        """
        self.model_path = model_path
        self.confidence_threshold = confidence_threshold
        self.overlap_threshold = overlap_threshold
        self.session = None
        self.input_name = None
        self.output_names = None

        # Define class names based on your C# model
        self.class_names = {
            0: 'CeoOfficeReleaseStamp',
            1: 'DocNumContainer',
            2: 'Document Number',
            3: 'Document Type',
            4: 'DocumentBody',
            5: 'Office Name',
            6: 'RecordOfficesStamp'
        }

        self.load_model()

    def calculate_iou(self, box1, box2):
        """
        Calculate Intersection over Union (IoU) between two bounding boxes
        
        Args:
            box1, box2: dictionaries with keys 'x', 'y', 'width', 'height'
            
        Returns:
            float: IoU value between 0 and 1
        """
        # Convert to x1, y1, x2, y2 format
        x1_1, y1_1 = box1['x'], box1['y']
        x2_1, y2_1 = x1_1 + box1['width'], y1_1 + box1['height']
        
        x1_2, y1_2 = box2['x'], box2['y']
        x2_2, y2_2 = x1_2 + box2['width'], y1_2 + box2['height']
        
        # Calculate intersection
        x1_intersect = max(x1_1, x1_2)
        y1_intersect = max(y1_1, y1_2)
        x2_intersect = min(x2_1, x2_2)
        y2_intersect = min(y2_1, y2_2)
        
        # Check if there's an intersection
        if x2_intersect <= x1_intersect or y2_intersect <= y1_intersect:
            return 0.0
        
        # Calculate intersection area
        intersection_area = (x2_intersect - x1_intersect) * (y2_intersect - y1_intersect)
        
        # Calculate union area
        area1 = box1['width'] * box1['height']
        area2 = box2['width'] * box2['height']
        union_area = area1 + area2 - intersection_area
        
        # Avoid division by zero
        if union_area == 0:
            return 0.0
            
        return intersection_area / union_area

    def apply_nms(self, detected_objects):
        """
        Apply Non-Maximum Suppression to remove overlapping detections
        
        Args:
            detected_objects (list): List of detected objects with bounding boxes
            
        Returns:
            list: Filtered list after NMS
        """
        if not detected_objects:
            return detected_objects
        
        # Group detections by class
        class_groups = {}
        for obj in detected_objects:
            class_name = obj['class']
            if class_name not in class_groups:
                class_groups[class_name] = []
            class_groups[class_name].append(obj)
        
        filtered_objects = []
        
        # Apply NMS for each class separately
        for class_name, objects in class_groups.items():
            if not objects:
                continue
                
            # Sort by confidence (highest first)
            objects.sort(key=lambda x: x['confidence'], reverse=True)
            
            selected = []
            
            for i, current_obj in enumerate(objects):
                should_keep = True
                
                # Check against all previously selected objects of the same class
                for selected_obj in selected:
                    iou = self.calculate_iou(current_obj['bounding_box'], selected_obj['bounding_box'])
                    
                    # If overlap is too high, discard current object
                    if iou > self.overlap_threshold:
                        should_keep = False
                        print(f"NMS: Removing {class_name} detection (IoU: {iou:.3f} > {self.overlap_threshold})")
                        break
                
                if should_keep:
                    selected.append(current_obj)
            
            filtered_objects.extend(selected)
            print(f"NMS for {class_name}: {len(objects)} -> {len(selected)} detections")
        
        # Sort final results by confidence
        filtered_objects.sort(key=lambda x: x['confidence'], reverse=True)
        
        return filtered_objects

    def load_model(self):
        """Load the ONNX model"""
        try:
            self.session = ort.InferenceSession(self.model_path)
            self.input_name = self.session.get_inputs()[0].name
            self.output_names = [output.name for output in self.session.get_outputs()]
            print(f"Model loaded successfully: {self.model_path}")
            print(f"Input name: {self.input_name}")
            print(f"Output names: {self.output_names}")
        except Exception as e:
            print(f"Error loading model: {e}")
            sys.exit(1)

    def preprocess_image(self, image_path, target_size=(640, 640)):
        """
        Preprocess image for ONNX model inference

        Args:
            image_path (str): Path to input image
            target_size (tuple): Target size for model input

        Returns:
            np.ndarray: Preprocessed image array
        """
        try:
            # Load image
            image = cv2.imread(image_path)
            if image is None:
                raise ValueError(f"Could not load image: {image_path}")

            # Convert BGR to RGB
            image = cv2.cvtColor(image, cv2.COLOR_BGR2RGB)

            # Resize image
            image = cv2.resize(image, target_size)

            # Normalize to [0, 1]
            image = image.astype(np.float32) / 255.0

            # Convert to CHW format (channels first)
            image = np.transpose(image, (2, 0, 1))

            # Add batch dimension
            image = np.expand_dims(image, axis=0)

            return image

        except Exception as e:
            print(f"Error preprocessing image: {e}")
            return None

    def run_inference(self, input_array):
        """
        Run ONNX model inference

        Args:
            input_array (np.ndarray): Preprocessed input image

        Returns:
            list: Model outputs
        """
        try:
            outputs = self.session.run(self.output_names, {self.input_name: input_array})
            return outputs
        except Exception as e:
            print(f"Inference error: {e}")
            return None

    def postprocess_results(self, outputs, original_size, target_size=(640, 640)):
        """
        Postprocess model outputs to extract bounding boxes and classes

        Based on your C# model that detects document components with classes:
        0: CeoOfficeReleaseStamp, 1: DocNumContainer, 2: Document Number,
        3: Document Type, 4: DocumentBody, 5: Office Name, 6: RecordOfficesStamp

        Args:
            outputs (list): Raw model outputs
            original_size (tuple): Original image dimensions (width, height)
            target_size (tuple): Model input size

        Returns:
            list: Detected objects with bounding boxes and confidence scores
        """
        detected_objects = []

        if not outputs or len(outputs) == 0:
            print("No outputs from model")
            return detected_objects

        try:
            # Assuming standard object detection output format
            # Typically: [batch, num_detections, 6] where 6 = [x1, y1, x2, y2, confidence, class_id]
            # Or similar format - adjust based on your model's actual output

            predictions = outputs[0]  # First output
            print(f"Predictions shape: {predictions.shape if hasattr(predictions, 'shape') else 'No shape'}")

            # Scale factors for converting back to original image size
            scale_x = original_size[0] / target_size[0]
            scale_y = original_size[1] / target_size[1]

            # Handle different output formats
            if hasattr(predictions, 'shape'):
                if len(predictions.shape) == 3:  # [batch, detections, features]
                    predictions = predictions[0]  # Remove batch dimension

                # Process each detection
                for i, detection in enumerate(predictions):
                    try:
                        # Common formats to try:
                        # Format 1: [x1, y1, x2, y2, confidence, class_id]
                        if len(detection) >= 6:
                            x1, y1, x2, y2, confidence, class_id = detection[:6]
                        # Format 2: [x_center, y_center, width, height, confidence, class_id]
                        elif len(detection) >= 6:
                            x_center, y_center, width, height, confidence, class_id = detection[:6]
                            x1 = x_center - width/2
                            y1 = y_center - height/2
                            x2 = x_center + width/2
                            y2 = y_center + height/2
                        else:
                            print(f"Unknown detection format: {len(detection)} values")
                            continue

                        # Convert class_id to integer
                        class_id = int(class_id)

                        # Filter by confidence threshold
                        if confidence >= self.confidence_threshold:
                            # Scale bounding box back to original image size
                            x1_scaled = int(x1 * scale_x)
                            y1_scaled = int(y1 * scale_y)
                            x2_scaled = int(x2 * scale_x)
                            y2_scaled = int(y2 * scale_y)

                            # Get class name
                            class_name = self.class_names.get(class_id, f"unknown_class_{class_id}")

                            detected_object = {
                                'class': class_name,
                                'confidence': float(confidence),
                                'bounding_box': {
                                    'x': x1_scaled,
                                    'y': y1_scaled,
                                    'width': x2_scaled - x1_scaled,
                                    'height': y2_scaled - y1_scaled
                                }
                            }

                            detected_objects.append(detected_object)
                            print(f"Detected: {class_name} ({confidence:.3f})")

                    except Exception as e:
                        print(f"Error processing detection {i}: {e}")
                        continue

            print(f"Total detections above confidence threshold: {len(detected_objects)}")
            
            # Apply Non-Maximum Suppression to remove overlapping detections
            if detected_objects:
                print(f"Applying NMS with overlap threshold: {self.overlap_threshold}")
                detected_objects = self.apply_nms(detected_objects)
                print(f"Final detections after NMS: {len(detected_objects)}")

        except Exception as e:
            print(f"Error in postprocessing: {e}")
            print(f"Outputs type: {type(outputs)}")
            if outputs:
                print(f"First output type: {type(outputs[0])}")
                print(f"First output shape: {outputs[0].shape if hasattr(outputs[0], 'shape') else 'No shape'}")

        return detected_objects

    def detect_objects(self, image_path):
        """
        Main method to detect objects in an image

        Args:
            image_path (str): Path to input image

        Returns:
            dict: Detection results
        """
        try:
            # Load and preprocess image
            image = cv2.imread(image_path)
            if image is None:
                raise ValueError(f"Could not load image: {image_path}")

            original_size = (image.shape[1], image.shape[0])  # (width, height)
            print(f"Original image size: {original_size}")

            # Preprocess for model
            input_array = self.preprocess_image(image_path)
            if input_array is None:
                raise ValueError("Image preprocessing failed")

            print(f"Input array shape: {input_array.shape}")

            # Run inference
            outputs = self.run_inference(input_array)
            if outputs is None:
                raise ValueError("Inference failed")

            # Postprocess results
            detected_objects = self.postprocess_results(outputs, original_size)

            return {
                'image_path': image_path,
                'detected_objects': detected_objects,
                'total_detections': len(detected_objects)
            }

        except Exception as e:
            print(f"Detection error: {e}")
            return {
                'image_path': image_path,
                'detected_objects': [],
                'total_detections': 0,
                'error': str(e)
            }


def main():
    """Main function to run the script"""
    parser = argparse.ArgumentParser(description='ONNX Document Object Detection')
    parser.add_argument('image_path', type=str, help='Path to input image')
    parser.add_argument('--model', type=str, default='models/document_detector.onnx',
                        help='Path to ONNX model file')
    parser.add_argument('--confidence', type=float, default=0.5,
                        help='Confidence threshold for detections (0.0-1.0)')
    parser.add_argument('--overlap', type=float, default=0.4,
                        help='Overlap (IoU) threshold for Non-Maximum Suppression (0.0-1.0). Lower = more aggressive overlap removal')

    args = parser.parse_args()
    
    # Validate thresholds
    if not (0.0 <= args.confidence <= 1.0):
        print("Error: Confidence threshold must be between 0.0 and 1.0")
        sys.exit(1)
        
    if not (0.0 <= args.overlap <= 1.0):
        print("Error: Overlap threshold must be between 0.0 and 1.0")
        sys.exit(1)

    print(f"Using confidence threshold: {args.confidence}")
    print(f"Using overlap threshold: {args.overlap}")

    # Initialize inference engine
    detector = DocumentONNXInference(args.model, args.confidence, args.overlap)

    # Detect objects
    results = detector.detect_objects(args.image_path)

    # Output results as JSON
    print(json.dumps(results, indent=2))


if __name__ == '__main__':
    main()
