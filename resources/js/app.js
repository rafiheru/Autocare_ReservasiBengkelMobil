import './bootstrap';
import '../css/refinements.css';

document.addEventListener('DOMContentLoaded', () => {
    const navButton = document.querySelector('[data-nav-toggle]');
    const nav = document.querySelector('[data-nav]');
    navButton?.addEventListener('click', () => {
        const isOpen = nav.classList.toggle('open');
        navButton.setAttribute('aria-expanded', String(isOpen));
    });

    document.querySelectorAll('form[data-confirm]').forEach((form) => {
        form.addEventListener('submit', (event) => {
            if (!window.confirm(form.dataset.confirm)) event.preventDefault();
        });
    });

    document.querySelectorAll('form[data-booking-form]').forEach((form) => {
        const updateSummary = () => {
            const vehicle = form.querySelector('input[name="vehicle_id"]:checked')?.closest('label')?.querySelector('b')?.textContent;
            const service = form.querySelector('input[name="service_id"]:checked')?.dataset.serviceName;
            const date = form.querySelector('[name="booking_date"]')?.value;
            const time = form.querySelector('[name="booking_time"]')?.value;
            const dateLabel = date ? new Intl.DateTimeFormat('id-ID', { dateStyle: 'medium' }).format(new Date(`${date}T00:00:00`)) : '—';
            const values = { vehicle, service, date: dateLabel, time: time ? `${time} WIB` : '—' };
            Object.entries(values).forEach(([key, value]) => {
                const target = document.querySelector(`[data-summary-${key}]`);
                if (target) target.textContent = value || '—';
            });
        };

        form.addEventListener('change', updateSummary);
        form.addEventListener('submit', (event) => {
            if (!form.reportValidity()) {
                event.preventDefault();
                return;
            }
            const button = form.querySelector('button[type="submit"], button:not([type])');
            if (button) {
                button.disabled = true;
                button.textContent = 'Mengirim booking…';
            }
        });
        updateSummary();
    });
});
