document.addEventListener('DOMContentLoaded', function () {
    const currentPage = window.location.pathname.split('/').pop() || 'index.php';

    document.querySelectorAll('.sidebar .nav-link').forEach(function (link) {
        const linkPage = link.getAttribute('href').split('/').pop();
        link.classList.toggle('active', linkPage === currentPage);
    });

    const forms = document.querySelectorAll('form');

    forms.forEach(function (form) {
        form.addEventListener('submit', function (event) {
            const requiredFields = form.querySelectorAll('input[required], select[required], textarea[required]');
            let valid = true;

            requiredFields.forEach(function (field) {
                if (!field.value.trim()) {
                    valid = false;
                    field.classList.add('is-invalid');
                } else {
                    field.classList.remove('is-invalid');
                }
            });

            if (!valid) {
                event.preventDefault();
                alert('Please fill in all required fields.');
            }
        });
    });

    document.querySelectorAll('.alert').forEach(function (alert) {
        window.setTimeout(function () {
            if (window.bootstrap) {
                bootstrap.Alert.getOrCreateInstance(alert).close();
            }
        }, 5000);
    });

    document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(function (element) {
        if (window.bootstrap) {
            new bootstrap.Tooltip(element);
        }
    });

    document.querySelectorAll('[data-count-to]').forEach(function (counter) {
        const target = Number(counter.dataset.countTo);
        const duration = 700;
        const start = performance.now();

        function updateCount(now) {
            const progress = Math.min((now - start) / duration, 1);
            counter.textContent = Math.floor(progress * target).toLocaleString();
            if (progress < 1) window.requestAnimationFrame(updateCount);
        }

        window.requestAnimationFrame(updateCount);
    });

    document.querySelectorAll('[data-table-filter]').forEach(function (filter) {
        const table = document.querySelector(filter.dataset.tableFilter);
        if (!table) return;

        filter.addEventListener('input', function () {
            const query = filter.value.toLowerCase().trim();
            table.querySelectorAll('tbody tr').forEach(function (row) {
                row.hidden = query && !row.textContent.toLowerCase().includes(query);
            });
        });
    });
});
