# DMRS User Roles and Process Flow - Updated

## Overview
The Document Management and Routing System (DMRS) has been updated to support 5 distinct user roles with specific responsibilities and system access. This document outlines each role and the complete workflow process.

## User Roles and Responsibilities

### ✅ 1. Records Officer
**Primary Responsibilities:**
- Receives printed documents
- Uploads scanned documents to the system
- Tags documents as received
- Forwards documents to the Approving Authority (Sir Odz)
- Tracks document movement and status

**System Functions:**
- Upload documents with metadata
- Tag documents with appropriate categories
- Forward documents to approval workflow
- View document status and history
- Manage document reception logs

**Database User Example:**
- Name: Maria Santos Cruz
- Email: records.officer@dmrs.com
- Designation: Records Officer

---

### ✅ 2. Approving Authority (Sir Odz)
**Primary Responsibilities:**
- Reviews documents forwarded by Records Officer
- Adds remarks, notes, or required actions
- Approves or rejects documents
- Makes final approval decisions

**System Functions:**
- View incoming documents from Records Officer
- Add comments and approval instructions
- Approve documents (forwards to Document Releaser)
- Reject documents (returns to Records Officer)
- Track approval history and decisions

**Database User Example:**
- Name: Odz CEO
- Email: ceo.odz@dmrs.com
- Designation: Chief Executive Officer

---

### ✅ 3. Document Releaser (Jasmin or Admin Staff)
**Primary Responsibilities:**
- Finalizes document processing after approval
- Sends documents to intended employees
- Manages document distribution
- Uploads final scanned copies if needed

**System Functions:**
- View approved documents ready for release
- Release documents to system
- Send documents to specific employees
- Notify recipients of document assignments
- Track release and distribution logs

**Database User Example:**
- Name: Jasmin Mae Santos
- Email: jasmin.releaser@dmrs.com
- Designation: Document Releaser

---

### ✅ 4. Receiving Employee / End Recipient
**Primary Responsibilities:**
- Receives and reviews assigned documents
- Takes required action based on document content
- Provides feedback if necessary

**System Functions:**
- Receive notifications of assigned documents
- View document details and content
- Mark documents as "Seen"
- Mark documents as "Actioned" when completed
- Track personal document history

**Database User Examples:**
- Robert James Wilson (Faculty Member)
- Emily Davis (Staff Member)
- Michael Anthony Brown (Department Head)

---

### ✅ 5. Event Manager (Optional)
**Primary Responsibilities:**
- Manages university schedules and events
- Inputs and updates event information
- Maintains calendar system

**System Functions:**
- Create and manage events (University/Internal/External)
- Post categorized events on homepage dashboard
- Update event schedules and deadlines
- Manage event notifications

**Database User Example:**
- Name: Alice Jane Johnson
- Email: events.manager@dmrs.com
- Designation: Event Manager

---

## Document Workflow Process

### 1. Document Reception
```
Document Upload → Records Officer → STATUS: received
```
- Documents are uploaded/scanned by Records Officer
- System automatically sets status to "received"
- Records Officer is assigned as current handler

### 2. Forward to Authority
```
Records Officer → Forward → Approving Authority → STATUS: forwarded_to_authority
```
- Records Officer forwards document to Sir Odz
- Status changes to "forwarded_to_authority"
- Sir Odz receives notification

### 3. Authority Review
```
Approving Authority (Sir Odz) → Review → Decision
```

**If APPROVED:**
```
STATUS: reviewed_by_authority → AUTO-FORWARD → Document Releaser → STATUS: forwarded_to_releaser
```

**If REJECTED:**
```
STATUS: received (back to Records Officer)
```

### 4. Document Release
```
Document Releaser (Jasmin) → Release → STATUS: released
```
- Jasmin confirms document is ready for distribution
- Document is officially released in system

### 5. Send to Employee
```
Document Releaser → Send to Employee → STATUS: sent_to_employee
```
- Jasmin assigns document to specific employee
- Employee receives notification
- Employee becomes current handler

### 6. Employee Actions
```
Employee → Mark as Seen → STATUS: seen_by_employee
Employee → Mark as Actioned → STATUS: actioned_by_employee
```

## Status Flow Summary

1. **received** → Records Officer handles
2. **forwarded_to_authority** → Sir Odz reviews
3. **reviewed_by_authority** → Sir Odz approved
4. **forwarded_to_releaser** → Jasmin handles
5. **released** → Ready for distribution
6. **sent_to_employee** → Employee receives
7. **seen_by_employee** → Employee acknowledged
8. **actioned_by_employee** → Employee completed
9. **rejected** → Returned to Records Officer (if rejected at any stage)

## Key Improvements

1. **Separated Release Function**: Document release is now handled by dedicated Document Releaser role instead of Approving Authority
2. **Clear Role Separation**: Each role has distinct responsibilities and system access
3. **Enhanced Tracking**: Additional status tracking for forwarded_to_releaser
4. **Improved Notifications**: Role-specific notifications for each workflow step
5. **Detailed Dashboard**: Role-specific dashboard statistics and metrics

## Database Changes

### New Migration: `2025_08_05_000000_update_document_workflow_structure.php`
- Added `review_decision` field to track approval/rejection decisions
- Added `forwarded_to_releaser_at` timestamp for workflow tracking

### Updated User Seeder
- Added Jasmin Mae Santos as Document Releaser
- Updated user roles to match new workflow

### Enhanced Workflow Service
- Separated review and release functions
- Added `forwardToReleaser()` and `releaseDocument()` methods
- Updated role-specific document filtering
- Enhanced dashboard statistics for each role

## Testing the Updated System

1. **Run Migrations**: Execute the new migration to add required fields
2. **Seed Database**: Run user seeder to create role-specific users
3. **Test Workflow**: Create a test document and follow through complete workflow
4. **Verify Permissions**: Ensure each role can only access appropriate functions
5. **Check Notifications**: Verify notifications are sent at each workflow step

This updated system provides clear separation of responsibilities and ensures proper document tracking through the entire workflow process.
