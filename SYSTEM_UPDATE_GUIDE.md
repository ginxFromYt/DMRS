# DMRS - Document Management and Routing System

## Updated System Features (August 2025)

### Document Workflow Process

The system now follows a structured document workflow based on the flowchart provided:

```
Email Received → Print Document → Records Officer → Forward to Authority → 
Review & Add Notes → Release Document → Send to Employee → 
Employee Notification → Employee Action (Seen/Actioned)
```

### User Roles and Responsibilities

#### 1. **Records Officer**
- **Login**: `records.officer@dmrs.com` / `password123`
- **Responsibilities**:
  - Receives printed documents
  - Uploads scanned documents to the system
  - Forwards documents to approving authority
  - Tracks document movement/status
- **Features**:
  - Document upload with ML text extraction
  - Document status tracking
  - Forward documents to authority

#### 2. **Approving Authority (Sir Odz)**
- **Login**: `odz.authority@dmrs.com` / `password123`
- **Responsibilities**:
  - Reviews incoming documents
  - Adds remarks, notes, or required actions
  - Updates document status for release
- **Features**:
  - Review documents forwarded by Records Officer
  - Add authority notes and comments
  - Approve documents for release

#### 3. **Document Releaser (Jasmin)**
- **Login**: `jasmin.releaser@dmrs.com` / `password123`
- **Responsibilities**:
  - Finalizes document processing
  - Sends documents to intended employees
  - Confirms document release
- **Features**:
  - Release approved documents
  - Select and notify target employees
  - Upload final scanned copies if needed

#### 4. **Employee / End Recipient**
- **Login**: `robert.wilson@dmrs.com`, `emily.davis@dmrs.com`, `michael.brown@dmrs.com` / `password123`
- **Responsibilities**:
  - Receives and reviews documents
  - Takes required action if needed
- **Features**:
  - Receive notifications of new documents
  - View assigned documents
  - Mark documents as "Seen" or "Actioned"

#### 5. **Administrator**
- **Login**: `admin@dmrs.com` / `password123`
- **Responsibilities**:
  - Manages user roles and access
  - Oversees system settings and logs
- **Features**:
  - User management
  - System configuration
  - Access to all documents

#### 6. **Event Manager**
- **Login**: `events.manager@dmrs.com` / `password123`
- **Responsibilities**:
  - Manages event schedules and deadlines
  - Updates homepage event information
- **Features**:
  - Create/edit/delete events
  - Categorize events (University/Internal/External)
  - Set deadlines and reminders

#### 7. **SuperAdmin**
- **Login**: `superadmin@dmrs.com` / `password123`
- **Responsibilities**:
  - Full system access and management
- **Features**:
  - All system features
  - User role management
  - System administration

### Homepage Features

The homepage now displays:
- **University Events**: Academic ceremonies, research symposiums, enrollment deadlines
- **Internal Campus**: Faculty meetings, maintenance schedules, internal deadlines
- **External Partners**: Partnership forums, exchange programs, agreement renewals

### Document Processing Features (Retained)

The system maintains all existing text extraction capabilities:
- **ONNX ML Model** for object detection (document numbers, text blocks, signatures)
- **Google Cloud Vision API** for OCR text extraction
- **Processing Results**: Document numbers, detected objects, extracted text

### Document Status Flow

1. **Received** - Document uploaded by Records Officer
2. **Forwarded to Authority** - Sent to Approving Authority for review
3. **Reviewed by Authority** - Authority has added notes/approval
4. **Released** - Document approved and ready for distribution
5. **Sent to Employee** - Document assigned to specific employee
6. **Seen by Employee** - Employee has viewed the document
7. **Actioned by Employee** - Employee has taken required action

### Notifications System

Users receive notifications for:
- New documents assigned to them
- Document status changes
- Approaching deadlines
- System updates

### API Endpoints

- `/api/events/homepage` - Get events for homepage display
- `/api/events/search` - Search events by criteria
- `/documents/employees/list` - Get employee list for document assignment

### Security Features

- Role-based access control
- Document access restrictions based on user role and assignment
- Audit trail for all document actions
- Secure file storage and processing

### Database Structure

#### New Tables:
- **documents** - Store document information and workflow status
- **events** - Store event schedules and deadlines
- **notifications** - Store user notifications
- **roles** - Updated with new role definitions
- **role_user** - User-role assignments

### Getting Started

1. **Login** with your assigned role credentials
2. **Navigate** to your role-specific dashboard
3. **Perform** actions based on your role responsibilities
4. **Monitor** notifications for new tasks and updates

### Development Notes

- Text extraction features are fully retained and integrated
- Document workflow follows the specified flowchart
- Role-based permissions ensure secure access
- Event management supports homepage display requirements
- Notification system keeps users informed of relevant updates
