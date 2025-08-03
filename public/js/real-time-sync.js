/**
 * Real-time Synchronization JavaScript
 * Handles real-time data updates and synchronization
 */

class RealTimeSync {
    constructor(options = {}) {
        this.baseUrl = options.baseUrl || window.location.origin;
        this.apiKey = options.apiKey || null;
        this.eventSource = null;
        this.reconnectAttempts = 0;
        this.maxReconnectAttempts = 5;
        this.reconnectDelay = 1000;
        this.listeners = {};
        this.isConnected = false;
        
        this.init();
    }
    
    init() {
        this.setupEventSource();
        this.setupHeartbeat();
        this.bindEvents();
    }
    
    setupEventSource() {
        if (!this.apiKey) {
            console.warn('API key required for real-time sync');
            return;
        }
        
        const url = `${this.baseUrl}/api/stream?api_key=${this.apiKey}`;
        
        this.eventSource = new EventSource(url);
        
        this.eventSource.onopen = () => {
            console.log('Real-time sync connected');
            this.isConnected = true;
            this.reconnectAttempts = 0;
            this.trigger('connected');
        };
        
        this.eventSource.onmessage = (event) => {
            try {
                const data = JSON.parse(event.data);
                this.handleEvent(data);
            } catch (error) {
                console.error('Error parsing event data:', error);
            }
        };
        
        this.eventSource.onerror = (error) => {
            console.error('Real-time sync error:', error);
            this.isConnected = false;
            this.trigger('disconnected');
            this.handleReconnect();
        };
        
        // Handle specific event types
        this.eventSource.addEventListener('employee_updated', (event) => {
            const data = JSON.parse(event.data);
            this.trigger('employee_updated', data);
        });
        
        this.eventSource.addEventListener('attendance_updated', (event) => {
            const data = JSON.parse(event.data);
            this.trigger('attendance_updated', data);
        });
        
        this.eventSource.addEventListener('payroll_processed', (event) => {
            const data = JSON.parse(event.data);
            this.trigger('payroll_processed', data);
        });
    }
    
    handleEvent(data) {
        switch (data.type) {
            case 'employee_update':
                this.updateEmployeeData(data.payload);
                break;
            case 'attendance_sync':
                this.updateAttendanceData(data.payload);
                break;
            case 'payroll_status':
                this.updatePayrollStatus(data.payload);
                break;
            case 'notification':
                this.showNotification(data.payload);
                break;
            default:
                console.log('Unknown event type:', data.type);
        }
    }
    
    updateEmployeeData(payload) {
        // Update employee data in UI
        payload.employees.forEach(employee => {
            const employeeElements = document.querySelectorAll(`[data-employee-id="${employee.id}"]`);
            employeeElements.forEach(element => {
                this.updateElementData(element, employee);
            });
        });
        
        this.trigger('employees_updated', payload.employees);
    }
    
    updateAttendanceData(payload) {
        // Update attendance data in UI
        payload.attendance.forEach(record => {
            const attendanceElements = document.querySelectorAll(`[data-attendance-date="${record.date}"][data-employee-id="${record.employee_id}"]`);
            attendanceElements.forEach(element => {
                this.updateElementData(element, record);
            });
        });
        
        this.trigger('attendance_updated', payload.attendance);
    }
    
    updatePayrollStatus(payload) {
        // Update payroll status indicators
        const statusElements = document.querySelectorAll('.payroll-status');
        statusElements.forEach(element => {
            if (element.dataset.periodId === payload.period_id) {
                element.textContent = payload.status;
                element.className = `payroll-status status-${payload.status.toLowerCase()}`;
            }
        });
        
        this.trigger('payroll_status_updated', payload);
    }
    
    showNotification(payload) {
        if (window.NotificationUtils) {
            NotificationUtils.showNotification(payload.title, payload.message, payload.type);
        }
        
        this.trigger('notification_received', payload);
    }
    
    updateElementData(element, data) {
        // Update element content based on data attributes
        Object.keys(data).forEach(key => {
            const targetElement = element.querySelector(`[data-field="${key}"]`);
            if (targetElement) {
                if (targetElement.tagName === 'INPUT') {
                    targetElement.value = data[key];
                } else {
                    targetElement.textContent = data[key];
                }
            }
        });
        
        // Add updated class for visual feedback
        element.classList.add('data-updated');
        setTimeout(() => {
            element.classList.remove('data-updated');
        }, 2000);
    }
    
    handleReconnect() {
        if (this.reconnectAttempts >= this.maxReconnectAttempts) {
            console.error('Max reconnection attempts reached');
            this.trigger('connection_failed');
            return;
        }
        
        this.reconnectAttempts++;
        const delay = this.reconnectDelay * Math.pow(2, this.reconnectAttempts - 1);
        
        console.log(`Attempting to reconnect in ${delay}ms (attempt ${this.reconnectAttempts})`);
        
        setTimeout(() => {
            this.setupEventSource();
        }, delay);
    }
    
    setupHeartbeat() {
        setInterval(() => {
            if (this.isConnected) {
                this.ping();
            }
        }, 30000); // Ping every 30 seconds
    }
    
    ping() {
        fetch(`${this.baseUrl}/api/ping`, {
            method: 'GET',
            headers: {
                'X-API-Key': this.apiKey
            }
        }).catch(error => {
            console.warn('Heartbeat failed:', error);
        });
    }
    
    // Event listener methods
    on(event, callback) {
        if (!this.listeners[event]) {
            this.listeners[event] = [];
        }
        this.listeners[event].push(callback);
    }
    
    off(event, callback) {
        if (this.listeners[event]) {
            this.listeners[event] = this.listeners[event].filter(cb => cb !== callback);
        }
    }
    
    trigger(event, data = null) {
        if (this.listeners[event]) {
            this.listeners[event].forEach(callback => {
                try {
                    callback(data);
                } catch (error) {
                    console.error('Error in event listener:', error);
                }
            });
        }
    }
    
    // Manual sync methods
    syncEmployees(employeeIds = []) {
        return this.makeRequest('/api/sync', {
            type: 'employee_sync',
            payload: { employee_ids: employeeIds }
        });
    }
    
    syncAttendance(dateRange = {}) {
        return this.makeRequest('/api/sync', {
            type: 'attendance_sync',
            payload: dateRange
        });
    }
    
    syncPayroll(periodId) {
        return this.makeRequest('/api/sync', {
            type: 'payroll_sync',
            payload: { period_id: periodId }
        });
    }
    
    makeRequest(endpoint, data) {
        return fetch(`${this.baseUrl}${endpoint}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-API-Key': this.apiKey
            },
            body: JSON.stringify(data)
        }).then(response => response.json());
    }
    
    // Connection management
    connect() {
        if (!this.isConnected) {
            this.setupEventSource();
        }
    }
    
    disconnect() {
        if (this.eventSource) {
            this.eventSource.close();
            this.eventSource = null;
            this.isConnected = false;
            this.trigger('disconnected');
        }
    }
    
    getConnectionStatus() {
        return {
            connected: this.isConnected,
            reconnectAttempts: this.reconnectAttempts
        };
    }
    
    bindEvents() {
        // Handle page visibility changes
        document.addEventListener('visibilitychange', () => {
            if (document.hidden) {
                // Page is hidden, reduce activity
                console.log('Page hidden, reducing sync activity');
            } else {
                // Page is visible, resume normal activity
                console.log('Page visible, resuming sync activity');
                if (!this.isConnected) {
                    this.connect();
                }
            }
        });
        
        // Handle online/offline events
        window.addEventListener('online', () => {
            console.log('Connection restored');
            this.connect();
        });
        
        window.addEventListener('offline', () => {
            console.log('Connection lost');
            this.disconnect();
        });
    }
}

// Auto-connect configuration
class AutoConnect {
    constructor(baseUrl) {
        this.baseUrl = baseUrl;
        this.config = null;
        this.realTimeSync = null;
        
        this.init();
    }
    
    async init() {
        try {
            // Fetch auto-connect configuration
            const response = await fetch(`${this.baseUrl}/api/auto-connect-config`);
            this.config = await response.json();
            
            if (this.config.success && this.config.enabled) {
                await this.setupConnection();
            }
        } catch (error) {
            console.error('Auto-connect initialization failed:', error);
        }
    }
    
    async setupConnection() {
        try {
            // Auto-generate API key if needed
            if (this.config.auto_generate_keys) {
                const keyResponse = await fetch(`${this.baseUrl}/api/auto-generate-key`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        name: 'Auto-Connect Client',
                        permissions: ['employees.read', 'payroll.read', 'attendance.read']
                    })
                });
                
                const keyData = await keyResponse.json();
                if (keyData.success) {
                    this.apiKey = keyData.api_key;
                }
            }
            
            // Setup real-time sync
            if (this.config.real_time_sync && this.apiKey) {
                this.realTimeSync = new RealTimeSync({
                    baseUrl: this.baseUrl,
                    apiKey: this.apiKey
                });
                
                this.setupEventHandlers();
            }
            
            console.log('Auto-connect setup completed');
        } catch (error) {
            console.error('Auto-connect setup failed:', error);
        }
    }
    
    setupEventHandlers() {
        // Setup automatic UI updates
        this.realTimeSync.on('employees_updated', (employees) => {
            this.updateEmployeeUI(employees);
        });
        
        this.realTimeSync.on('attendance_updated', (attendance) => {
            this.updateAttendanceUI(attendance);
        });
        
        this.realTimeSync.on('payroll_status_updated', (status) => {
            this.updatePayrollStatusUI(status);
        });
        
        this.realTimeSync.on('notification_received', (notification) => {
            this.showSystemNotification(notification);
        });
    }
    
    updateEmployeeUI(employees) {
        // Update employee tables, cards, etc.
        employees.forEach(employee => {
            const elements = document.querySelectorAll(`[data-employee="${employee.id}"]`);
            elements.forEach(element => {
                this.updateElementContent(element, employee);
            });
        });
    }
    
    updateAttendanceUI(attendance) {
        // Update attendance displays
        attendance.forEach(record => {
            const elements = document.querySelectorAll(`[data-attendance="${record.employee_id}-${record.date}"]`);
            elements.forEach(element => {
                this.updateElementContent(element, record);
            });
        });
    }
    
    updatePayrollStatusUI(status) {
        // Update payroll status indicators
        const elements = document.querySelectorAll('.payroll-status');
        elements.forEach(element => {
            if (element.dataset.periodId === status.period_id) {
                element.textContent = status.status;
                element.className = `payroll-status ${status.status.toLowerCase()}`;
            }
        });
    }
    
    showSystemNotification(notification) {
        // Show browser notification if permitted
        if (Notification.permission === 'granted') {
            new Notification(notification.title, {
                body: notification.message,
                icon: '/favicon.ico'
            });
        }
        
        // Show in-app notification
        if (window.showMessage) {
            showMessage(notification.message, notification.type || 'info');
        }
    }
    
    updateElementContent(element, data) {
        Object.keys(data).forEach(key => {
            const field = element.querySelector(`[data-field="${key}"]`);
            if (field) {
                if (field.tagName === 'INPUT' || field.tagName === 'SELECT') {
                    field.value = data[key];
                } else {
                    field.textContent = data[key];
                }
            }
        });
        
        // Add visual feedback
        element.classList.add('updated');
        setTimeout(() => element.classList.remove('updated'), 1000);
    }
    
    // Public methods for manual operations
    sync(type, data = {}) {
        if (this.realTimeSync) {
            return this.realTimeSync.makeRequest('/api/sync', {
                type: type,
                payload: data
            });
        }
    }
    
    getStatus() {
        return {
            configured: !!this.config,
            connected: this.realTimeSync ? this.realTimeSync.isConnected : false,
            apiKey: !!this.apiKey
        };
    }
}

// Initialize auto-connect when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    // Check if auto-connect is enabled
    const autoConnectEnabled = document.querySelector('meta[name="auto-connect"]');
    
    if (autoConnectEnabled && autoConnectEnabled.content === 'true') {
        const baseUrl = document.querySelector('meta[name="base-url"]')?.content || window.location.origin;
        window.autoConnect = new AutoConnect(baseUrl);
    }
    
    // Setup manual real-time sync if API key is available
    const apiKey = document.querySelector('meta[name="api-key"]')?.content;
    if (apiKey && !window.autoConnect) {
        window.realTimeSync = new RealTimeSync({
            baseUrl: window.location.origin,
            apiKey: apiKey
        });
    }
});

// Add CSS for visual feedback
document.addEventListener('DOMContentLoaded', function() {
    const style = document.createElement('style');
    style.textContent = `
        .data-updated {
            animation: dataUpdate 2s ease-out;
        }
        
        .updated {
            animation: elementUpdate 1s ease-out;
        }
        
        @keyframes dataUpdate {
            0% { background-color: #dbeafe; }
            100% { background-color: transparent; }
        }
        
        @keyframes elementUpdate {
            0% { 
                background-color: #10b981; 
                transform: scale(1.02); 
            }
            100% { 
                background-color: transparent; 
                transform: scale(1); 
            }
        }
        
        .payroll-status.processing {
            color: #f59e0b;
            animation: pulse 2s infinite;
        }
        
        .payroll-status.completed {
            color: #10b981;
        }
        
        .payroll-status.failed {
            color: #ef4444;
        }
        
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }
    `;
    document.head.appendChild(style);
});

// Export for global access
window.RealTimeSync = RealTimeSync;
window.AutoConnect = AutoConnect;