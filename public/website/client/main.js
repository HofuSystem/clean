// ===================================================================
// MAIN.JS - Clean Station Dashboard
// Rebuilt from scratch for better organization and functionality
// ===================================================================

class CleanStationApp {
    constructor() {
        this.init();
    }

    init() {
        document.addEventListener('DOMContentLoaded', () => {
            this.initSidebar();
            this.initOrderNowModal();
            this.initLocationDropdowns();
            this.initAddressModal();
            this.initOrderDetailsModal();
            this.initFormValidation();
            this.initCityDistrictFilter();
            this.initGlobalEvents();
        });
    }

    // ===================================================================
    // SIDEBAR FUNCTIONALITY
    // ===================================================================
    initSidebar() {
        // Create backdrop element
        const backdrop = document.createElement('div');
        backdrop.className = 'sidebar-backdrop';
        document.body.appendChild(backdrop);
        
        // Get elements
        const sidebar = document.querySelector('.sidebar');
        const sidebarToggle = document.querySelector('.sidebar-toggle');
        const sidebarLinks = document.querySelectorAll('.sidebar-link');
        
        if (!sidebar || !sidebarToggle) return;

        // Toggle sidebar function
        const toggleSidebar = () => {
            sidebar.classList.toggle('show');
            backdrop.classList.toggle('show');
            document.body.style.overflow = sidebar.classList.contains('show') ? 'hidden' : '';
        };

        // Close sidebar function
        const closeSidebar = () => {
            sidebar.classList.remove('show');
            backdrop.classList.remove('show');
            document.body.style.overflow = '';
        };

        // Event listeners
        sidebarToggle.addEventListener('click', (e) => {
            e.stopPropagation();
            toggleSidebar();
        });

        backdrop.addEventListener('click', closeSidebar);

        // Handle sidebar link clicks
        sidebarLinks.forEach(link => {
            link.addEventListener('click', (e) => {
                // Remove active class from all items
                document.querySelectorAll('.sidebar-item').forEach(item => {
                    item.classList.remove('active');
                });
                
                // Add active class to clicked item
                link.parentElement.classList.add('active');
                
                // Close sidebar on mobile
                if (window.innerWidth <= 1000) {
                    setTimeout(closeSidebar, 100);
                }
            });
        });

        // Close sidebar on window resize to desktop
        window.addEventListener('resize', () => {
            if (window.innerWidth > 1000) {
                closeSidebar();
            }
        });

        // Prevent sidebar from closing when clicking inside it
        sidebar.addEventListener('click', (e) => {
            e.stopPropagation();
        });
    }

    // ===================================================================
    // ORDER NOW MODAL FUNCTIONALITY
    // ===================================================================
    initOrderNowModal() {
        const modal = document.getElementById('orderNowModal');
        const openBtn = document.querySelector('.feature-btn');
        const cancelBtn = document.getElementById('cancelOrderNowModal');
        const form = document.getElementById('orderNowForm');

        if (!modal) return;

        const openModal = () => {
            modal.classList.add('active');
            document.body.style.overflow = 'hidden';
        };

        const closeModal = () => {
            modal.classList.remove('active');
            document.body.style.overflow = '';
        };

        // Event listeners
        if (openBtn) {
            openBtn.addEventListener('click', (e) => {
                e.preventDefault();
                openModal();
            });
        }

        if (cancelBtn) {
            cancelBtn.addEventListener('click', closeModal);
        }

        // Close on backdrop click
        modal.addEventListener('click', (e) => {
            if (e.target === modal) {
                closeModal();
            }
        });

        // Prevent modal content click from closing modal
        const modalContent = modal.querySelector('.order-now-modal');
        if (modalContent) {
            modalContent.addEventListener('click', (e) => {
                e.stopPropagation();
            });
        }

        // Close on Escape key
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && modal.classList.contains('active')) {
                closeModal();
            }
        });
    }

    // ===================================================================
    // LOCATION DROPDOWNS FUNCTIONALITY
    // ===================================================================
    initLocationDropdowns() {
        const pickupToggle = document.getElementById('pickupLocationToggle');
        const deliveryToggle = document.getElementById('deliveryLocationToggle');
        
        if (!pickupToggle || !deliveryToggle) return;

        // Toggle dropdown function
        const toggleDropdown = (currentId, otherId, currentToggleId, otherToggleId) => {
            const currentList = document.getElementById(currentId);
            const otherList = document.getElementById(otherId);
            const currentIcon = document.querySelector(`#${currentToggleId} ion-icon`);
            const otherIcon = document.querySelector(`#${otherToggleId} ion-icon`);

            if (!currentList || !otherList || !currentIcon || !otherIcon) return;

            // Toggle current dropdown
            if (!currentList.classList.contains('show')) {
                // Open current, close other
                currentList.classList.add('show');
                currentIcon.setAttribute('name', 'chevron-up-outline');
                
                if (otherList.classList.contains('show')) {
                    otherList.classList.remove('show');
                    otherIcon.setAttribute('name', 'chevron-down-outline');
                }
            } else {
                // Close current
                currentList.classList.remove('show');
                currentIcon.setAttribute('name', 'chevron-down-outline');
            }
        };

        // Handle location selection
        const selectLocation = (item, type) => {
            const city = item.querySelector('.location-info p')?.textContent || '';
            const description = item.querySelector('.location-description')?.textContent || '';
            const fullText = `${city} - ${description}`;
            
            // Update radio button
            const radio = item.querySelector('input[type="radio"]');
            if (radio) radio.checked = true;
            if (radio) {
                const event = new Event('change', { bubbles: true });
                radio.dispatchEvent(event);
            }
            
            // Update display text
            const spanSelector = type === 'pickup' ? 
                '#pickupLocationToggle span' : '#deliveryLocationToggle span';
            const span = document.querySelector(spanSelector);
            
            if (span) {
                span.textContent = fullText;
                span.style.color = '#495057';
            }
            
            // Close dropdown
            const listId = type === 'pickup' ? 'pickupLocationList' : 'deliveryLocationList';
            const toggleId = type === 'pickup' ? 'pickupLocationToggle' : 'deliveryLocationToggle';
            const list = document.getElementById(listId);
            const icon = document.querySelector(`#${toggleId} ion-icon`);
            
            if (list) list.classList.remove('show');
            if (icon) icon.setAttribute('name', 'chevron-down-outline');
            
            // Visual feedback
            const allItems = document.querySelectorAll(`#${listId} .location-item`);
            allItems.forEach(i => i.classList.remove('selected'));
            item.classList.add('selected');
        };

        // Event listeners for toggles
        pickupToggle.addEventListener('click', (e) => {
            e.preventDefault();
            e.stopPropagation();
            toggleDropdown('pickupLocationList', 'deliveryLocationList', 'pickupLocationToggle', 'deliveryLocationToggle');
        });

        deliveryToggle.addEventListener('click', (e) => {
            e.preventDefault();
            e.stopPropagation();
            toggleDropdown('deliveryLocationList', 'pickupLocationList', 'deliveryLocationToggle', 'pickupLocationToggle');
        });

        // Event listeners for location items
        const setupLocationItems = (listId, type) => {
            const items = document.querySelectorAll(`#${listId} .location-item`);
            items.forEach(item => {
                item.addEventListener('click', (e) => {
                    e.preventDefault();
                    e.stopPropagation();
                    selectLocation(item, type);
                });
            });
        };

        setupLocationItems('pickupLocationList', 'pickup');
        setupLocationItems('deliveryLocationList', 'delivery');

        // Close dropdowns when clicking outside
        document.addEventListener('click', (e) => {
            const isPickupArea = e.target.closest('#pickupLocationToggle, #pickupLocationList');
            const isDeliveryArea = e.target.closest('#deliveryLocationToggle, #deliveryLocationList');
            
            if (!isPickupArea) {
                const pickupList = document.getElementById('pickupLocationList');
                const pickupIcon = document.querySelector('#pickupLocationToggle ion-icon');
                if (pickupList) pickupList.classList.remove('show');
                if (pickupIcon) pickupIcon.setAttribute('name', 'chevron-down-outline');
            }
            
            if (!isDeliveryArea) {
                const deliveryList = document.getElementById('deliveryLocationList');
                const deliveryIcon = document.querySelector('#deliveryLocationToggle ion-icon');
                if (deliveryList) deliveryList.classList.remove('show');
                if (deliveryIcon) deliveryIcon.setAttribute('name', 'chevron-down-outline');
            }
        });
    }

    // ===================================================================
    // ADDRESS MODAL FUNCTIONALITY
    // ===================================================================
    initAddressModal() {
        const modal = document.getElementById('addressModal');
        const openBtn = document.querySelector('.add-address-btn');
        const closeBtn = document.getElementById('closeModal');
        const cancelBtn = document.getElementById('cancelModal');
        const form = document.getElementById('addressForm');

        if (!modal) return;

        const openModal = () => {
            modal.classList.add('active');
            document.body.style.overflow = 'hidden';
        };

        const closeModal = () => {
            modal.classList.remove('active');
            document.body.style.overflow = '';
        };

        // Event listeners
        if (openBtn) {
            openBtn.addEventListener('click', (e) => {
                e.preventDefault();
                openModal();
            });
        }

        if (closeBtn) closeBtn.addEventListener('click', closeModal);
        if (cancelBtn) cancelBtn.addEventListener('click', closeModal);

        // Close on backdrop click
        modal.addEventListener('click', (e) => {
            if (e.target === modal) {
                closeModal();
            }
        });

        // Handle form submission
        if (form) {
            form.addEventListener('submit', (e) => {
                e.preventDefault();
                
                const formData = new FormData(form);
                console.log('Address form submitted:', Object.fromEntries(formData));
                
                closeModal();
                form.reset();
            });
        }

        // Close on Escape key
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && modal.classList.contains('active')) {
                closeModal();
            }
        });
    }

    // ===================================================================
    // ORDER DETAILS MODAL FUNCTIONALITY
    // ===================================================================
    initOrderDetailsModal() {
        const openBtns = document.querySelectorAll('.order-details-btn');

        if (openBtns.length === 0) return;

        // Event listeners for open buttons
        openBtns.forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.preventDefault();
                
                // Get the order ID from the button's data attribute
                const orderId = btn.getAttribute('data-order-id');
                const modalId = `orderDetailsModal${orderId}`;
                const modal = document.getElementById(modalId);
                
                if (!modal) {
                    console.error(`Modal with ID ${modalId} not found`);
                    return;
                }

                // Remove active from all buttons, add to current
                openBtns.forEach(b => b.classList.remove('active'));
                btn.classList.add('active');
                
                // Open the specific modal
                modal.classList.add('active');
                document.body.style.overflow = 'hidden';
            });
        });

        // Handle close buttons for all modals
        document.querySelectorAll('[id^="orderDetailsModal"]').forEach(modal => {
            const closeBtn = modal.querySelector('.modal-header .btn-close, .modal-header .close-btn');
            
            if (closeBtn) {
                closeBtn.addEventListener('click', () => {
                    modal.classList.remove('active');
                    document.body.style.overflow = '';
                });
            }

            // Close on backdrop click
            modal.addEventListener('click', (e) => {
                if (e.target === modal) {
                    modal.classList.remove('active');
                    document.body.style.overflow = '';
                }
            });

            // Prevent modal content click from closing
            const modalContent = modal.querySelector('.order-modal');
            if (modalContent) {
                modalContent.addEventListener('click', (e) => {
                    e.stopPropagation();
                });
            }
        });

        // Close on Escape key
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                const activeModal = document.querySelector('.order-modal-backdrop.active');
                if (activeModal) {
                    activeModal.classList.remove('active');
                    document.body.style.overflow = '';
                }
            }
        });
    }

    // ===================================================================
    // FORM VALIDATION
    // ===================================================================
    initFormValidation() {
        // Phone input validation (numbers only)
        const phoneInputs = document.querySelectorAll('.phone-input, input[type="tel"]');
        phoneInputs.forEach(input => {
            input.addEventListener('input', (e) => {
                e.target.value = e.target.value.replace(/[^0-9]/g, '');
            });
        });

        // Real-time form validation
        const forms = document.querySelectorAll('form');
        forms.forEach(form => {
            const inputs = form.querySelectorAll('input[required], select[required]');
            
            inputs.forEach(input => {
                input.addEventListener('blur', () => {
                    this.validateField(input);
                });
                
                input.addEventListener('input', () => {
                    if (input.classList.contains('is-invalid')) {
                        this.validateField(input);
                    }
                });
            });
        });
    }

    // ===================================================================
    // CITY-DISTRICT FILTERING
    // ===================================================================
    initCityDistrictFilter() {
        const citySelect = document.querySelector('select[name="city_id"]');
        const districtSelect = document.querySelector('select[name="district_id"]');
        
        if (!citySelect || !districtSelect) return;

        // Store all district options for filtering
        const allDistrictOptions = Array.from(districtSelect.querySelectorAll('option')).map(option => ({
            element: option,
            cityId: option.getAttribute('data-city-id'),
            value: option.value,
            text: option.textContent
        }));

        citySelect.addEventListener('change', (e) => {
            const selectedCityId = e.target.value;
            
            // Clear current district selection
            districtSelect.value = '';
            
            // Hide all district options first
            allDistrictOptions.forEach(option => {
                option.element.style.display = 'none';
            });
            
            // Show only districts that belong to the selected city
            allDistrictOptions.forEach(option => {
                if (option.cityId === selectedCityId || option.value === '' || !option.cityId) {
                    option.element.style.display = '';
                }
            });
            
            // Reset district select to show placeholder
            districtSelect.selectedIndex = 0;
        });
    }

    validateField(field) {
        const isValid = field.checkValidity();
        
        field.classList.remove('is-valid', 'is-invalid');
        field.classList.add(isValid ? 'is-valid' : 'is-invalid');
        
        return isValid;
    }

    // ===================================================================
    // GLOBAL EVENTS AND UTILITIES
    // ===================================================================
    initGlobalEvents() {
        // Smooth scroll for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', (e) => {
                const target = document.querySelector(anchor.getAttribute('href'));
                if (target) {
                    e.preventDefault();
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });

        // Handle page navigation
        const navLinks = document.querySelectorAll('.sidebar-link[href]');
        navLinks.forEach(link => {
            link.addEventListener('click', (e) => {
                const href = link.getAttribute('href');
                if (href && href !== '#' && !href.startsWith('#')) {
                    // Let the default navigation happen
                    return;
                }
            });
        });
    }

    // ===================================================================
    // UTILITY FUNCTIONS
    // ===================================================================
    resetLocationDropdowns() {
        // Reset pickup location
        const pickupSpan = document.querySelector('#pickupLocationToggle span');
        const pickupList = document.getElementById('pickupLocationList');
        const pickupIcon = document.querySelector('#pickupLocationToggle ion-icon');
        
        if (pickupSpan) {
            pickupSpan.textContent = 'ادخل موقع الاستلام';
            pickupSpan.style.color = '';
        }
        if (pickupList) pickupList.classList.remove('show');
        if (pickupIcon) pickupIcon.setAttribute('name', 'chevron-down-outline');
        
        // Reset delivery location
        const deliverySpan = document.querySelector('#deliveryLocationToggle span');
        const deliveryList = document.getElementById('deliveryLocationList');
        const deliveryIcon = document.querySelector('#deliveryLocationToggle ion-icon');
        
        if (deliverySpan) {
            deliverySpan.textContent = 'ادخل موقع التسليم';
            deliverySpan.style.color = '';
        }
        if (deliveryList) deliveryList.classList.remove('show');
        if (deliveryIcon) deliveryIcon.setAttribute('name', 'chevron-down-outline');
        
        // Clear radio selections
        document.querySelectorAll('#pickupLocationList input[type="radio"]').forEach(radio => {
            radio.checked = false;
        });
        document.querySelectorAll('#deliveryLocationList input[type="radio"]').forEach(radio => {
            radio.checked = false;
        });
        
        // Remove selected class
        document.querySelectorAll('.location-item.selected').forEach(item => {
            item.classList.remove('selected');
        });
    }
}

// ===================================================================
// INITIALIZE APPLICATION
// ===================================================================
const app = new CleanStationApp();