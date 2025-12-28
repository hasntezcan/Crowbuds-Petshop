const NotificationSystem = {
    container: null,

    init: function () {
        if (!this.container) {
            this.container = document.createElement('div');
            this.container.className = 'notification-container';
            document.body.appendChild(this.container);
        }
    },

    show: function (options) {
        this.init();

        const {
            type = 'success',
            title = '',
            message = '',
            duration = 3000,
            icon = null
        } = options;

        const notification = document.createElement('div');
        notification.className = `notification notification-${type}`;

        const iconMap = {
            success: 'check_circle',
            error: 'error',
            warning: 'warning',
            info: 'info'
        };

        const iconName = icon || iconMap[type];

        notification.innerHTML = `
            <div class="notification-icon">
                <span class="material-symbols-outlined">${iconName}</span>
            </div>
            <div class="notification-content">
                ${title ? `<div class="notification-title">${title}</div>` : ''}
                <div class="notification-message">${message}</div>
            </div>
            <button class="notification-close" onclick="this.closest('.notification').remove()">
                <span class="material-symbols-outlined">close</span>
            </button>
            <div class="notification-progress"></div>
        `;

        this.container.appendChild(notification);

        if (duration > 0) {
            setTimeout(() => {
                notification.classList.add('hiding');
                setTimeout(() => notification.remove(), 300);
            }, duration);
        }

        return notification;
    },

    success: function (message, title = 'Success!') {
        return this.show({ type: 'success', title, message });
    },

    error: function (message, title = 'Error') {
        return this.show({ type: 'error', title, message, duration: 4000 });
    },

    warning: function (message, title = 'Warning') {
        return this.show({ type: 'warning', title, message });
    },

    info: function (message, title = 'Info') {
        return this.show({ type: 'info', title, message });
    }
};

window.Notify = NotificationSystem;
