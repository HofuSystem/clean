(function () {
    'use strict';

    var tables = 'table.view-dataTable, table#view-datatable, table#transactions-table, .items-container[data-items-name="representatives"] table';
    var arabic = document.documentElement.lang === 'ar';

    function labelRows(table) {
        if (!table || !table.tHead || !table.tBodies.length) return;
        var headers = Array.from(table.tHead.querySelectorAll('tr:last-child th'));
        if (headers.length < 3 || headers.some(function (header) { return header.colSpan !== 1; })) return;
        var rows = Array.from(table.tBodies[0].rows).filter(function (row) {
            return row.cells.length === headers.length && Array.from(row.cells).every(function (cell) { return cell.colSpan === 1; });
        });
        if (!rows.length) return;

        table.classList.add('admin-mobile-cards');
        rows.forEach(function (row) {
            var cells = Array.from(row.cells);
            var useful = cells.map(function (cell, index) { return index; }).filter(function (index) {
                return !cells[index].querySelector('input[type="checkbox"]') && headers[index].textContent.trim();
            });
            var actions = useful.filter(function (index) {
                return headers[index].dataset.name === 'actions' || /الإجراءات|الاجراءات|العمليات|actions|operations/i.test(headers[index].textContent.trim());
            });
            var primary = useful.filter(function (index) {
                return /^(reference_id|status|client_id|delivery_date)$/.test(headers[index].dataset.name || '') && actions.indexOf(index) === -1;
            }).slice(0, 3);
            useful.forEach(function (index) {
                if (primary.length < 3 && primary.indexOf(index) === -1 && actions.indexOf(index) === -1) primary.push(index);
            });
            primary = primary.slice(0, 3).concat(actions);
            var secondary = useful.filter(function (index) { return primary.indexOf(index) === -1; });

            cells.forEach(function (cell, index) {
                cell.setAttribute('data-mobile-label', headers[index].textContent.trim());
                cell.classList.toggle('admin-mobile-secondary', secondary.indexOf(index) !== -1);
                cell.classList.toggle('admin-mobile-actions', actions.indexOf(index) !== -1);
            });
            var oldToggle = row.querySelector('.admin-mobile-details-toggle');
            if (oldToggle) oldToggle.remove();
            if (!secondary.length) return;
            var target = cells[actions[0] !== undefined ? actions[0] : primary[primary.length - 1]];
            if (!target) return;
            target.classList.add('admin-mobile-toggle-host');
            var button = document.createElement('button');
            button.type = 'button';
            button.className = 'admin-mobile-details-toggle';
            button.setAttribute('aria-expanded', row.classList.contains('admin-mobile-expanded') ? 'true' : 'false');
            button.textContent = row.classList.contains('admin-mobile-expanded') ? (arabic ? 'إخفاء التفاصيل' : 'Hide details') : (arabic ? 'عرض التفاصيل' : 'View details');
            target.appendChild(button);
        });
    }

    function labelAllRows() { document.querySelectorAll(tables).forEach(labelRows); }

    function setupProductPicker() {
        var modal = document.getElementById('addItem');
        if (!modal || modal.querySelector('.admin-mobile-cart-bar')) return;
        var cart = modal.querySelector('#cart-items');
        var save = modal.querySelector('#saveCart');
        if (!cart || !save) return;

        var bar = document.createElement('div');
        bar.className = 'admin-mobile-cart-bar';
        var toggle = document.createElement('button');
        toggle.type = 'button';
        toggle.className = 'admin-mobile-cart-toggle';
        toggle.setAttribute('aria-expanded', 'false');
        var submit = document.createElement('button');
        submit.type = 'button';
        submit.className = 'admin-mobile-cart-save btn btn-primary';
        submit.textContent = save.textContent.trim();
        bar.appendChild(toggle);
        bar.appendChild(submit);
        modal.querySelector('.modal-content').appendChild(bar);

        function update() {
            var cards = Array.from(cart.querySelectorAll(':scope > .card'));
            var count = cards.reduce(function (total, card) {
                var input = card.querySelector('input[name="quantity"]');
                return total + (input ? Math.max(0, Number(input.value) || 0) : 1);
            }, 0);
            toggle.textContent = (arabic ? 'السلة' : 'Cart') + ' (' + count + ')';
            submit.disabled = save.disabled || count === 0;
        }
        toggle.addEventListener('click', function () {
            var open = modal.classList.toggle('admin-mobile-cart-open');
            toggle.setAttribute('aria-expanded', String(open));
        });
        submit.addEventListener('click', function () { if (!submit.disabled) save.click(); });
        cart.addEventListener('input', update);
        new MutationObserver(update).observe(cart, { childList: true, subtree: true });
        if (window.jQuery) {
            window.jQuery(modal).on('shown.bs.modal', update);
            window.jQuery(modal).on('hidden.bs.modal', function () {
                modal.classList.remove('admin-mobile-cart-open');
                toggle.setAttribute('aria-expanded', 'false');
            });
        }
        update();
    }

    document.addEventListener('click', function (event) {
        var button = event.target.closest('.admin-mobile-details-toggle');
        if (!button) return;
        var row = button.closest('tr');
        var expanded = row.classList.toggle('admin-mobile-expanded');
        button.setAttribute('aria-expanded', String(expanded));
        button.textContent = expanded ? (arabic ? 'إخفاء التفاصيل' : 'Hide details') : (arabic ? 'عرض التفاصيل' : 'View details');
    });

    if (window.jQuery) {
        window.jQuery(document).on('draw.dt', tables, function () { labelRows(this); });
        window.jQuery(document).on('shown.bs.tab', labelAllRows);
    }
    if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', function () { labelAllRows(); setupProductPicker(); });
    else { labelAllRows(); setupProductPicker(); }
})();
