/**
 * Sketo POS - Universal Sync Engine
 * Handles offline data integrity for all vendor entities.
 */

const SketoSync = {
    db: null,
    isOnline: navigator.onLine,
    syncInterval: 30000,

    async init() {
        console.log('Initializing Sketo Universal Sync Engine...');

        this.db = new Dexie("SketoPOS");
        this.db.version(2).stores({
            products: 'barcode, name, price, quantity',
            pending_actions: '++id, url, method, data, status, created_at'
        });

        window.addEventListener('online', () => this.handleStatusChange(true));
        window.addEventListener('offline', () => this.handleStatusChange(false));

        this.setupFormInterceptor();

        if (this.isOnline) {
            await this.syncProducts();
            this.processQueue();
        }

        setInterval(() => this.processQueue(), this.syncInterval);
        this.updateUIStatus();
    },

    handleStatusChange(online) {
        this.isOnline = online;
        this.updateUIStatus();
        if (online) this.processQueue();
    },

    updateUIStatus() {
        const indicator = document.getElementById('sync-status-indicator');
        if (indicator) {
            indicator.className = this.isOnline ? 'text-success' : 'text-warning';
            indicator.title = this.isOnline ? 'Online - System Synced' : 'Offline - Actions Queued';
            const icon = indicator.querySelector('i');
            if (icon) icon.className = this.isOnline ? 'la la-cloud-upload' : 'la la-cloud-download';
        }
    },

    setupFormInterceptor() {
        $(document).on('submit', 'form', async (e) => {
            const form = e.currentTarget;

            // Skip search forms or GET forms
            if (form.method.toLowerCase() === 'get') return;

            // If offline, intercept
            if (!this.isOnline) {
                e.preventDefault();
                console.log('Offline detected. Intercepting form submission:', form.action);

                const formData = new FormData(form);
                const data = {};
                formData.forEach((value, key) => {
                    // Handle multiple values for same key (like arrays)
                    if (data[key]) {
                        if (!Array.isArray(data[key])) data[key] = [data[key]];
                        data[key].push(value);
                    } else {
                        data[key] = value;
                    }
                });

                await this.queueAction(form.action, form.method, data);
                this.showOfflineAlert();

                // If it was the cashier checkout, we need special handling to clear cart
                if (form.id === 'checkout-form') {
                    if (window.showOfflineSuccess) window.showOfflineSuccess();
                } else {
                    // For other forms, maybe redirect back or show success
                    setTimeout(() => history.back(), 1500);
                }
            }
        });
    },

    async queueAction(url, method, data) {
        const action = {
            url: url,
            method: method.toUpperCase(),
            data: data,
            status: 'pending',
            created_at: new Date().toISOString()
        };
        await this.db.pending_actions.add(action);
        console.log('Action queued:', url);
    },

    async syncProducts() {
        if (!this.isOnline) return;
        try {
            const response = await fetch('/cashier/search-product?query=');
            const products = await response.json();
            await this.db.products.clear();
            await this.db.products.bulkAdd(products);
        } catch (error) {
            console.error('Product sync failed:', error);
        }
    },

    async processQueue() {
        if (!this.isOnline) return;

        const pending = await this.db.pending_actions.where('status').equals('pending').toArray();
        if (pending.length === 0) return;

        console.log(`Syncing ${pending.length} pending actions...`);

        for (const action of pending) {
            try {
                // Special handling for the cashier sync which has a dedicated endpoint
                let targetUrl = action.url;
                let payload = action.data;

                // If it's the cashier checkout form, route it to our sync endpoint
                if (action.url.includes('/cashier/checkout')) {
                    targetUrl = '/cashier/sync-offline-invoice';
                    payload = {
                        uuid: 'OFFLINE-' + action.id,
                        data: action.data
                    };
                }

                const response = await fetch(targetUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'X-Offline-Sync': 'true'
                    },
                    body: JSON.stringify(payload)
                });

                if (response.ok) {
                    await this.db.pending_actions.delete(action.id);
                    console.log(`Action ${action.id} synced.`);
                } else {
                    console.warn(`Action ${action.id} failed with status ${response.status}`);
                }
            } catch (error) {
                console.error(`Sync error for action ${action.id}:`, error);
                break;
            }
        }
    },

    showOfflineAlert() {
        const msg = document.documentElement.lang === 'ar' ? 'تم حفظ العمليات أوفلاين. سيتم المزامنة عند توفر الإنترنت.' : 'Actions saved offline. Will sync when online.';
        if (window.toastr) {
            toastr.info(msg);
        } else {
            alert(msg);
        }
    }
};

document.addEventListener('DOMContentLoaded', () => SketoSync.init());
