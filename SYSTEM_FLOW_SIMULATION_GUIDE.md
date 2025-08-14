# DMRS System Flow Simulation Guide

## 🚀 **Pre-Simulation Setup**

### Step 1: Start Your Local Server
```bash
cd c:\xampp\htdocs\DMRS
php artisan serve
```
Access the system at: `http://localhost:8000`

### Step 2: Verify Database Setup
Ensure all migrations and seeders have run successfully:
```bash
php artisan migrate:refresh --seed
```

---

## 📋 **Complete Document Workflow Simulation**

### **Phase 1: Document Upload (Records Officer)**

#### **👤 Login as Records Officer**
- **URL**: `http://localhost:8000/login`
- **Email**: `records.officer@dmrs.com`
- **Password**: `password123`

#### **📄 Upload Document**
1. Navigate to dashboard
2. Go to document upload section
3. Upload a sample document image (any JPG/PNG file)
4. Fill in:
   - **Title**: "Budget Approval Request"
   - **Description**: "Annual budget request for IT Department"
5. Submit the form

#### **✅ Expected Results**:
- Document uploaded successfully
- Status: "Received"
- Text extraction results displayed (if ML model is working)
- Document assigned to Records Officer initially

---

### **Phase 2: Forward to Authority (Records Officer)**

#### **📤 Forward Document**
1. While still logged in as Records Officer
2. Navigate to `/documents`
3. Find the uploaded document
4. Click "Forward to Authority" button
5. Confirm the action

#### **✅ Expected Results**:
- Document status changes to "Forwarded to Authority"
- Document is now assigned to Approving Authority
- Notification sent to Approving Authority

---

### **Phase 3: Authority Review (Sir Odz)**

#### **👤 Login as Approving Authority**
- **Logout** from Records Officer account
- **Login with**:
  - **Email**: `odz.authority@dmrs.com`
  - **Password**: `password123`

#### **📋 Review Document**
1. Navigate to dashboard - should see pending documents
2. Go to `/documents`
3. Click on the forwarded document
4. Review the document details
5. Add authority notes: "Approved with 10% budget reduction"
6. Click "Approve Document" or "Review Complete"

#### **✅ Expected Results**:
- Document status changes to "Reviewed by Authority"
- Authority notes saved
- Document forwarded to Document Releaser

---

### **Phase 4: Document Release (Jasmin)**

#### **👤 Login as Document Releaser**
- **Logout** from Authority account
- **Login with**:
  - **Email**: `jasmin.releaser@dmrs.com`
  - **Password**: `password123`

#### **📋 Release Document**
1. Navigate to dashboard - should see reviewed documents
2. Go to `/documents`
3. Find the reviewed document
4. Click "Release Document"
5. Confirm the release

#### **✅ Expected Results**:
- Document status changes to "Released"
- Document ready for employee assignment

---

### **Phase 5: Send to Employee (Document Releaser)**

#### **📤 Assign to Employee**
1. Still logged in as Document Releaser
2. On the released document page
3. Click "Send to Employee"
4. Select employee from dropdown: "Robert Wilson" or "Emily Davis"
5. Confirm assignment

#### **✅ Expected Results**:
- Document status changes to "Sent to Employee"
- Selected employee receives notification
- Document assigned to specific employee

---

### **Phase 6: Employee Actions**

#### **👤 Login as Employee**
- **Logout** from Document Releaser account
- **Login with**:
  - **Email**: `robert.wilson@dmrs.com` (or whichever employee was selected)
  - **Password**: `password123`

#### **📋 View and Act on Document**
1. Navigate to dashboard - should see new document notification
2. Go to `/documents`
3. Click on the assigned document
4. Review the document and authority notes
5. First, click "Mark as Seen"
6. Then, click "Mark as Actioned"

#### **✅ Expected Results**:
- First click: Status changes to "Seen by Employee"
- Second click: Status changes to "Actioned by Employee"
- Document workflow complete

---

## 🎉 **Event Management Simulation**

### **👤 Login as Event Manager**
- **Email**: `events.manager@dmrs.com`
- **Password**: `password123`

#### **📅 Create New Event**
1. Navigate to `/events/create`
2. Fill in event details:
   - **Title**: "Faculty Development Workshop"
   - **Description**: "Professional development session for faculty"
   - **Category**: "internal_campus"
   - **Date**: Tomorrow's date
   - **Time**: "14:00"
   - **Location**: "Conference Room A"
   - **Is Deadline**: No
3. Submit the form

#### **✅ Expected Results**:
- Event created successfully
- Appears in events list
- Available for homepage display

---

## 🏠 **Homepage Event Display Simulation**

### **🌐 Test Homepage API**
Visit these URLs to see event data:
- `http://localhost:8000/api/events/homepage` - JSON response of homepage events
- `http://localhost:8000/events` - Event management page
- `http://localhost:8000/events/category/university` - University events
- `http://localhost:8000/deadlines/upcoming` - Upcoming deadlines

---

## 🔄 **Multi-Role Dashboard Testing**

### **Test All Dashboards**
Login with each role to see role-specific interfaces:

1. **SuperAdmin**: `superadmin@dmrs.com`
   - Should see all documents and system-wide statistics

2. **Administrator**: `admin@dmrs.com`
   - Should see administrative features and user management

3. **Records Officer**: `records.officer@dmrs.com`
   - Should see pending documents to process

4. **Approving Authority**: `odz.authority@dmrs.com`
   - Should see documents awaiting review

5. **Document Releaser**: `jasmin.releaser@dmrs.com`
   - Should see documents ready for release/assignment

6. **Employees**: 
   - `robert.wilson@dmrs.com`
   - `emily.davis@dmrs.com`
   - `michael.brown@dmrs.com`
   - Should see assigned documents

---

## 🧪 **Advanced Testing Scenarios**

### **Scenario 1: Multiple Documents**
1. Upload 3 different documents as Records Officer
2. Forward them at different times
3. Have Authority review them with different notes
4. Release them to different employees
5. Track the workflow of each

### **Scenario 2: Event Categories**
1. Create events in all 3 categories:
   - University Events
   - Internal Campus
   - External Partners
2. Set some as deadlines
3. Test search and filtering

### **Scenario 3: Notification Testing**
1. Create documents and forward them
2. Login as different users to see notifications
3. Test marking notifications as read

---

## 🛠 **Debugging & Verification**

### **Check Database Records**
```bash
php artisan tinker
```
Then run:
```php
// Check documents
App\Models\Document::with(['uploader', 'assignedUser', 'currentHandler'])->get();

// Check notifications
App\Models\Notification::with('user')->get();

// Check events
App\Models\Event::with('creator')->get();

// Check user roles
App\Models\User::with('roles')->get();
```

### **Check Logs**
Monitor Laravel logs for any issues:
```bash
tail -f storage/logs/laravel.log
```

### **Route Testing**
Test specific routes:
- `GET /documents` - Document list
- `POST /documents/{id}/forward-authority` - Forward document
- `GET /api/events/homepage` - Homepage events
- `GET /events` - Event management

---

## 📊 **Expected System Flow Summary**

```
📧 Email → 🖨️ Print → 👩‍💼 Records Officer → 
📤 Forward → 👨‍💼 Authority Review → ✅ Approve → 
📋 Document Releaser → 📤 Send to Employee → 
👩‍🔬 Employee View → ✅ Seen → ✅ Actioned
```

### **Status Progression**:
1. `received` 
2. `forwarded_to_authority`
3. `reviewed_by_authority`
4. `released`
5. `sent_to_employee`
6. `seen_by_employee`
7. `actioned_by_employee`

---

## 🚨 **Troubleshooting**

### **Common Issues**:
1. **403 Errors**: Check user roles and permissions
2. **Route Not Found**: Ensure routes are properly cached (`php artisan route:clear`)
3. **Database Issues**: Run migrations again (`php artisan migrate:refresh --seed`)
4. **Session Issues**: Clear browser cache and cookies

### **Reset System**:
```bash
php artisan migrate:refresh --seed
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

This simulation will demonstrate the complete document workflow system we've built, showing how documents flow through different roles while maintaining the text extraction capabilities!
