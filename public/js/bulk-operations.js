/**
 * Bulk Operations JavaScript functionality
 */

// Bulk Operations Manager
window.BulkOperationsManager = {
    currentOperation: null,
    selectedItems: [],
    
    // Initialize bulk operations
    init: function() {
        this.setupEventListeners();
        this.setupProgressTracking();
    },
    
    // Setup event listeners
    setupEventListeners: function() {
        // Operation selection
        document.addEventListener('change', function(e) {
            if (e.target.matches('#operation-select')) {
                BulkOperationsManager.handleOperationChange(e.target.value);
            }
        });
        
        // Item selection
        document.addEventListener('change', function(e) {
            if (e.target.matches('.bulk-checkbox')) {
                BulkOperationsManager.handleItemSelection();
            }
        });
        
        // Select all functionality
        document.addEventListener('change', function(e) {
            if (e.target.matches('#select-all-checkbox')) {
                BulkOperationsManager.handleSelectAll(e.target.checked);
            }
        });
        
        // Form submission
        document.addEventListener('submit', function(e) {
            if (e.target.matches('.bulk-operation-form')) {
                e.preventDefault();
                BulkOperationsManager.handleFormSubmission(e.target);
            }
        });
        
        // Filter and search
        document.addEventListener('input', function(e) {
            if (e.target.matches('.bulk-search-input')) {
                BulkOperationsManager.handleSearch(e.target.value);
            }
        });
        
        document.addEventListener('change', function(e) {
            if (e.target.matches('.bulk-filter-select')) {
                BulkOperationsManager.handleFilter();
            }
        });
    },
    
    // Handle operation type change
    handleOperationChange: function(operation) {
        this.currentOperation = operation;
        this.showOperationParameters(operation);
        this.updateExecuteButton();
    },
    
    // Show operation-specific parameters
    showOperationParameters: function(operation) {
        // Hide all parameter sections
        document.querySelectorAll('.operation-parameters').forEach(section => {
            section.classList.add('hidden');
        });
        
        // Show relevant parameter section
        const parameterSection = document.getElementById(`${operation}-parameters`);
        if (parameterSection) {
            parameterSection.classList.remove('hidden');
        }
        
        // Show general parameters container
        const parametersContainer = document.getElementById('operation-parameters');
        if (parametersContainer && operation) {
            parametersContainer.classList.remove('hidden');
        } else if (parametersContainer) {
            parametersContainer.classList.add('hidden');
        }
    },
    
    // Handle item selection
    handleItemSelection: function() {
        const checkboxes = document.querySelectorAll('.bulk-checkbox:checked');
        this.selectedItems = Array.from(checkboxes).map(cb => cb.value);
        
        // Update selection count
        const countElement = document.getElementById('selected-count');
        if (countElement) {
            countElement.textContent = this.selectedItems.length;
        }
        
        // Update select all checkbox state
        this.updateSelectAllState();
        
        // Update execute button
        this.updateExecuteButton();
    },
    
    // Handle select all
    handleSelectAll: function(checked) {
        const visibleCheckboxes = document.querySelectorAll('.bulk-item:not([style*="display: none"]) .bulk-checkbox');
        
        visibleCheckboxes.forEach(checkbox => {
            checkbox.checked = checked;
        });
        
        this.handleItemSelection();
    },
    
    // Update select all checkbox state
    updateSelectAllState: function() {
        const selectAllCheckbox = document.getElementById('select-all-checkbox');
        if (!selectAllCheckbox) return;
        
        const visibleCheckboxes = document.querySelectorAll('.bulk-item:not([style*="display: none"]) .bulk-checkbox');
        const checkedBoxes = document.querySelectorAll('.bulk-item:not([style*="display: none"]) .bulk-checkbox:checked');
        
        if (checkedBoxes.length === 0) {
            selectAllCheckbox.checked = false;
            selectAllCheckbox.indeterminate = false;
        } else if (checkedBoxes.length === visibleCheckboxes.length) {
            selectAllCheckbox.checked = true;
            selectAllCheckbox.indeterminate = false;
        } else {
            selectAllCheckbox.checked = false;
            selectAllCheckbox.indeterminate = true;
        }
    },
    
    // Update execute button state
    updateExecuteButton: function() {
        const executeBtn = document.getElementById('execute-operation-btn');
        if (!executeBtn) return;
        
        const hasOperation = this.currentOperation;
        const hasSelection = this.selectedItems.length > 0;
        
        executeBtn.disabled = !hasOperation || !hasSelection;
    },
    
    // Handle search
    handleSearch: function(searchTerm) {
        const items = document.querySelectorAll('.bulk-item');
        const term = searchTerm.toLowerCase();
        
        items.forEach(item => {
            const searchData = item.dataset.search?.toLowerCase() || '';
            const matches = !term || searchData.includes(term);
            
            item.style.display = matches ? '' : 'none';
        });
        
        this.updateSelectAllState();
    },
    
    // Handle filter
    handleFilter: function() {
        const filters = document.querySelectorAll('.bulk-filter-select');
        const items = document.querySelectorAll('.bulk-item');
        
        items.forEach(item => {
            let visible = true;
            
            filters.forEach(filter => {
                const filterValue = filter.value;
                const filterType = filter.dataset.filter;
                const itemValue = item.dataset[filterType];
                
                if (filterValue && itemValue !== filterValue) {
                    visible = false;
                }
            });
            
            item.style.display = visible ? '' : 'none';
        });
        
        this.updateSelectAllState();
    },
    
    // Handle form submission
    handleFormSubmission: function(form) {
        const formData = new FormData(form);
        
        // Add selected items to form data
        this.selectedItems.forEach(id => {
            formData.append('selected_ids[]', id);
        });
        
        // Get operation details for confirmation
        const operation = this.currentOperation;
        const operationText = document.querySelector(`#operation-select option[value="${operation}"]`)?.textContent || operation;
        const count = this.selectedItems.length;
        
        // Show confirmation dialog
        if (!confirm(`Are you sure you want to perform "${operationText}" on ${count} item(s)?`)) {
            return;
        }
        
        // Show progress modal
        this.showProgressModal();
        
        // Submit form
        fetch(form.action, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            this.handleOperationResponse(data);
        })
        .catch(error => {
            console.error('Bulk operation error:', error);
            this.hideProgressModal();
            showMessage('An error occurred during the operation', 'error');
        });
    },
    
    // Handle operation response
    handleOperationResponse: function(data) {
        if (data.success) {
            this.updateProgressModal({
                status: 'completed',
                processed: data.data?.processed || this.selectedItems.length,
                failed: data.data?.failed || 0,
                percentage: 100
            });
            
            showMessage(data.message, 'success');
            
            // Reset form and selection after delay
            setTimeout(() => {
                this.resetOperation();
                this.hideProgressModal();
            }, 2000);
            
        } else {
            this.hideProgressModal();
            showMessage(data.message || 'Operation failed', 'error');
        }
    },
    
    // Setup progress tracking
    setupProgressTracking: function() {
        // Create progress modal if it doesn't exist
        if (!document.getElementById('bulk-progress-modal')) {
            this.createProgressModal();
        }
    },
    
    // Create progress modal
    createProgressModal: function() {
        const modalHtml = `
            <div id="bulk-progress-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
                <div class="relative top-20 mx-auto p-5 border w-full max-w-md shadow-lg rounded-md bg-white">
                    <div class="mt-3">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-medium text-gray-900">Operation Progress</h3>
                            <button type="button" onclick="BulkOperationsManager.cancelOperation()" class="text-gray-400 hover:text-gray-600">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                        
                        <div class="space-y-4">
                            <div>
                                <div class="flex justify-between text-sm text-gray-600 mb-1">
                                    <span>Progress</span>
                                    <span id="bulk-progress-percentage">0%</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div id="bulk-progress-bar" class="bg-blue-600 h-2 rounded-full transition-all duration-300" style="width: 0%"></div>
                                </div>
                            </div>
                            
                            <div id="bulk-progress-details" class="text-sm text-gray-600">
                                <div>Status: <span id="bulk-operation-status">Starting...</span></div>
                                <div>Processed: <span id="bulk-processed-count">0</span></div>
                                <div>Failed: <span id="bulk-failed-count">0</span></div>
                            </div>
                            
                            <div class="flex justify-end space-x-3">
                                <button type="button" id="bulk-cancel-btn" class="btn btn-outline btn-sm">
                                    Cancel Operation
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        document.body.insertAdjacentHTML('beforeend', modalHtml);
        
        // Setup cancel button
        document.getElementById('bulk-cancel-btn').addEventListener('click', () => {
            this.cancelOperation();
        });
    },
    
    // Show progress modal
    showProgressModal: function() {
        const modal = document.getElementById('bulk-progress-modal');
        if (modal) {
            modal.classList.remove('hidden');
            this.updateProgressModal({
                status: 'starting',
                processed: 0,
                failed: 0,
                percentage: 0
            });
        }
    },
    
    // Hide progress modal
    hideProgressModal: function() {
        const modal = document.getElementById('bulk-progress-modal');
        if (modal) {
            modal.classList.add('hidden');
        }
    },
    
    // Update progress modal
    updateProgressModal: function(data) {
        const percentageElement = document.getElementById('bulk-progress-percentage');
        const progressBar = document.getElementById('bulk-progress-bar');
        const statusElement = document.getElementById('bulk-operation-status');
        const processedElement = document.getElementById('bulk-processed-count');
        const failedElement = document.getElementById('bulk-failed-count');
        
        if (percentageElement) percentageElement.textContent = Math.round(data.percentage) + '%';
        if (progressBar) progressBar.style.width = Math.round(data.percentage) + '%';
        if (statusElement) statusElement.textContent = data.status;
        if (processedElement) processedElement.textContent = data.processed;
        if (failedElement) failedElement.textContent = data.failed;
    },
    
    // Cancel operation
    cancelOperation: function() {
        if (confirm('Are you sure you want to cancel this operation?')) {
            this.hideProgressModal();
            showMessage('Operation cancelled', 'warning');
        }
    },
    
    // Reset operation
    resetOperation: function() {
        // Clear form
        const form = document.querySelector('.bulk-operation-form');
        if (form) {
            form.reset();
        }
        
        // Clear selection
        this.clearSelection();
        
        // Reset operation
        this.currentOperation = null;
        
        // Hide parameters
        const parametersContainer = document.getElementById('operation-parameters');
        if (parametersContainer) {
            parametersContainer.classList.add('hidden');
        }
        
        // Update button
        this.updateExecuteButton();
    },
    
    // Clear selection
    clearSelection: function() {
        document.querySelectorAll('.bulk-checkbox').forEach(checkbox => {
            checkbox.checked = false;
        });
        
        const selectAllCheckbox = document.getElementById('select-all-checkbox');
        if (selectAllCheckbox) {
            selectAllCheckbox.checked = false;
            selectAllCheckbox.indeterminate = false;
        }
        
        this.selectedItems = [];
        
        const countElement = document.getElementById('selected-count');
        if (countElement) {
            countElement.textContent = '0';
        }
        
        this.updateExecuteButton();
    },
    
    // Select all visible items
    selectAllVisible: function() {
        const selectAllCheckbox = document.getElementById('select-all-checkbox');
        if (selectAllCheckbox) {
            selectAllCheckbox.checked = true;
            this.handleSelectAll(true);
        }
    },
    
    // Export selected items
    exportSelected: function(format = 'excel') {
        if (this.selectedItems.length === 0) {
            showMessage('Please select items to export', 'warning');
            return;
        }
        
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '/bulk-operations/export';
        
        // Add selected items
        this.selectedItems.forEach(id => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'selected_ids[]';
            input.value = id;
            form.appendChild(input);
        });
        
        // Add format
        const formatInput = document.createElement('input');
        formatInput.type = 'hidden';
        formatInput.name = 'format';
        formatInput.value = format;
        form.appendChild(formatInput);
        
        document.body.appendChild(form);
        form.submit();
        document.body.removeChild(form);
    }
};

// Template Management
window.BulkTemplateManager = {
    templates: {},
    
    // Load template
    loadTemplate: function(templateName) {
        const template = this.templates[templateName];
        if (!template) {
            showMessage('Template not found', 'error');
            return;
        }
        
        // Apply template settings
        this.applyTemplate(template);
        showMessage(`Template "${templateName}" loaded successfully`, 'success');
    },
    
    // Apply template
    applyTemplate: function(template) {
        // Set operation
        const operationSelect = document.getElementById('operation-select');
        if (operationSelect && template.operation) {
            operationSelect.value = template.operation;
            operationSelect.dispatchEvent(new Event('change'));
        }
        
        // Set parameters
        if (template.parameters) {
            Object.keys(template.parameters).forEach(key => {
                const input = document.querySelector(`[name="parameters[${key}]"]`);
                if (input) {
                    input.value = template.parameters[key];
                }
            });
        }
        
        // Set filters
        if (template.filters) {
            Object.keys(template.filters).forEach(key => {
                const filter = document.querySelector(`[data-filter="${key}"]`);
                if (filter) {
                    filter.value = template.filters[key];
                    filter.dispatchEvent(new Event('change'));
                }
            });
        }
    },
    
    // Save template
    saveTemplate: function(templateName) {
        const template = {
            name: templateName,
            operation: BulkOperationsManager.currentOperation,
            parameters: this.getFormParameters(),
            filters: this.getActiveFilters(),
            created_at: new Date().toISOString()
        };
        
        // Save to localStorage (in production, save to server)
        const savedTemplates = JSON.parse(localStorage.getItem('bulk_templates') || '{}');
        savedTemplates[templateName] = template;
        localStorage.setItem('bulk_templates', JSON.stringify(savedTemplates));
        
        this.templates[templateName] = template;
        showMessage(`Template "${templateName}" saved successfully`, 'success');
    },
    
    // Get form parameters
    getFormParameters: function() {
        const parameters = {};
        const parameterInputs = document.querySelectorAll('[name^="parameters["]');
        
        parameterInputs.forEach(input => {
            const match = input.name.match(/parameters\[(.+)\]/);
            if (match && input.value) {
                parameters[match[1]] = input.value;
            }
        });
        
        return parameters;
    },
    
    // Get active filters
    getActiveFilters: function() {
        const filters = {};
        const filterSelects = document.querySelectorAll('.bulk-filter-select');
        
        filterSelects.forEach(select => {
            if (select.value) {
                filters[select.dataset.filter] = select.value;
            }
        });
        
        return filters;
    },
    
    // Load saved templates
    loadSavedTemplates: function() {
        const savedTemplates = JSON.parse(localStorage.getItem('bulk_templates') || '{}');
        this.templates = savedTemplates;
        
        // Populate template dropdown if exists
        const templateSelect = document.getElementById('template-select');
        if (templateSelect) {
            templateSelect.innerHTML = '<option value="">Select Template</option>';
            Object.keys(savedTemplates).forEach(name => {
                const option = document.createElement('option');
                option.value = name;
                option.textContent = name;
                templateSelect.appendChild(option);
            });
        }
    }
};

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    BulkOperationsManager.init();
    BulkTemplateManager.loadSavedTemplates();
});

// Global functions for easy access
window.selectAllItems = function() {
    BulkOperationsManager.selectAllVisible();
};

window.clearSelection = function() {
    BulkOperationsManager.clearSelection();
};

window.exportSelected = function(format) {
    BulkOperationsManager.exportSelected(format);
};

window.loadTemplate = function(templateName) {
    BulkTemplateManager.loadTemplate(templateName);
};

window.saveTemplate = function() {
    const templateName = prompt('Enter template name:');
    if (templateName) {
        BulkTemplateManager.saveTemplate(templateName);
    }
};