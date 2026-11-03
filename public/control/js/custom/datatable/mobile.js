(function () {
    'use strict';

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
            if (row.cells.length !== headers.length) return;
            Array.from(row.cells).forEach(function (cell, index) {
                var label = headers[index].textContent.trim();
                cell.setAttribute('data-mobile-label', label);
            });
        });
    }

    function labelAllRows() {
        document.querySelectorAll('table.view-dataTable, table#view-datatable, table#transactions-table, .items-container[data-items-name="representatives"] table').forEach(labelRows);
    }

    if (window.jQuery) {
        window.jQuery(document).on('draw.dt', 'table.view-dataTable, table#view-datatable', function () {
            labelRows(this);
        });
        window.jQuery(document).on('shown.bs.tab', labelAllRows);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', labelAllRows);
    } else {
        labelAllRows();
    }
})();
