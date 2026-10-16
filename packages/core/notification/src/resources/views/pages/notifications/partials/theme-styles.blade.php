<style>
.notification-page {
    --notify-ink: #1d1d1f;
    --notify-secondary: #6e6e73;
    --notify-muted: #86868b;
    --notify-line: #e5e5ea;
    --notify-surface: #ffffff;
    --notify-canvas: #f5f5f7;
    --notify-blue: #0071e3;
    --notify-blue-soft: #edf5ff;
    min-height: 100%;
    padding-bottom: 32px;
    background: var(--notify-canvas) !important;
    color: var(--notify-ink) !important;
    font-family: inherit;
}
.notification-page #kt_toolbar,
.notification-page #kt_toolbar_container { background: transparent !important; }
.notification-page .row[class*="g-"] { --bs-gutter-x: 1rem; --bs-gutter-y: 1rem; }
.notification-page .page-title h1,
.notification-page h1 { color: var(--notify-ink) !important; letter-spacing: -.025em; }
.notification-page .breadcrumb { color: var(--notify-muted); }
.notification-page .breadcrumb a { color: var(--notify-secondary) !important; }
.notification-page .card,
.notification-page .card-flush {
    overflow: hidden;
    border: 1px solid rgba(29,29,31,.07) !important;
    border-radius: 18px !important;
    background: var(--notify-surface) !important;
    box-shadow: 0 3px 16px rgba(29,29,31,.045) !important;
}
.notification-page .card-header,
.notification-page .card-footer {
    border-color: var(--notify-line) !important;
    background: var(--notify-surface) !important;
}
.notification-page .card-header { min-height: 58px; padding: 16px 20px; }
.notification-page .card-body { padding: 20px; }
.notification-page .card-title,
.notification-page .card-label { color: var(--notify-ink) !important; }
.notification-page .text-muted,
.notification-page .text-gray-500,
.notification-page .text-gray-600 { color: var(--notify-secondary) !important; }
.notification-page .text-dark,
.notification-page .text-gray-800,
.notification-page .text-gray-900 { color: var(--notify-ink) !important; }
.notification-page .btn-primary,
.notification-page button.btn-primary,
.notification-page a.btn-primary {
    border: 1px solid var(--notify-blue) !important;
    border-radius: 10px !important;
    background: var(--notify-blue) !important;
    box-shadow: none !important;
    color: #fff !important;
}
.notification-page .btn-primary:hover { background: #0062c4 !important; }
.notification-page .btn-secondary,
.notification-page .btn-light,
.notification-page .btn-light-info,
.notification-page .btn-light-primary,
.notification-page .btn-light-warning,
.notification-page .btn-light-danger {
    border: 1px solid var(--notify-line) !important;
    border-radius: 10px !important;
    background: #fff !important;
    color: var(--notify-secondary) !important;
    box-shadow: none !important;
}
.notification-page .btn-secondary:hover,
.notification-page .btn-light:hover { background: var(--notify-canvas) !important; color: var(--notify-ink) !important; }
.notification-page .btn-operation,
.notification-page .btn-operation-teal,
.notification-page .btn-operation-warning {
    border: 1px solid var(--notify-line) !important;
    border-radius: 10px !important;
    background: #fff !important;
    box-shadow: none !important;
    color: var(--notify-ink) !important;
}
.notification-page .btn-operation i,
.notification-page .btn-operation span { color: inherit !important; }
.notification-page .stat-pill-total,
.notification-page .stat-pill-trash {
    border: 1px solid var(--notify-line) !important;
    border-radius: 12px !important;
    background: var(--notify-canvas) !important;
    color: var(--notify-ink) !important;
}
.notification-page .stat-pill-total a,
.notification-page .stat-pill-trash a { color: inherit !important; }
.notification-page .badge {
    border-radius: 8px !important;
    font-weight: 600 !important;
    letter-spacing: 0 !important;
}
.notification-page .badge.bg-label-marketing,
.notification-page .badge.badge-light-marketing,
.notification-page .badge.bg-label-primary,
.notification-page .badge.badge-light-primary,
.notification-page .badge.bg-label-info,
.notification-page .badge.badge-light-info,
.notification-page .badge.bg-label-warning,
.notification-page .badge.badge-light-warning,
.notification-page .badge.bg-label-secondary,
.notification-page .badge.badge-light-secondary,
.notification-page .badge.bg-label-dark,
.notification-page .badge.badge-light-dark {
    border: 1px solid #e5e5ea !important;
    background: #f5f5f7 !important;
    color: #515154 !important;
}
.notification-page .badge.bg-label-success,
.notification-page .badge.badge-light-success {
    border: 1px solid #ccebd8 !important;
    background: #eff9f2 !important;
    color: #237a45 !important;
}
.notification-page .badge.bg-label-danger,
.notification-page .badge.badge-light-danger {
    border: 1px solid #f2d2d0 !important;
    background: #fff4f3 !important;
    color: #b42318 !important;
}
.notification-page .symbol-label,
.notification-page .stat-icon-box { border-radius: 12px !important; background: var(--notify-blue-soft) !important; color: var(--notify-blue) !important; }
.notification-page .symbol-label i,
.notification-page .stat-icon-box i { color: var(--notify-blue) !important; }
.notification-page .form-control,
.notification-page .form-select,
.notification-page select,
.notification-page textarea {
    min-height: 42px;
    border: 1px solid #d2d2d7 !important;
    border-radius: 10px !important;
    background-color: #fff !important;
    color: var(--notify-ink) !important;
}
.notification-page textarea { min-height: 100px; }
.notification-page .form-control:focus,
.notification-page .form-select:focus,
.notification-page select:focus,
.notification-page textarea:focus {
    border-color: var(--notify-blue) !important;
    box-shadow: 0 0 0 3px rgba(0,113,227,.13) !important;
}
.notification-page label { color: var(--notify-secondary); font-weight: 600; }
.notification-page .accordion-item,
.notification-page .list-group-item { border-color: var(--notify-line) !important; background: #fff !important; }
.notification-page .table-responsive { border-radius: 12px; }
.notification-page .table { --bs-table-bg: transparent; color: var(--notify-ink); }
.notification-page .table thead th,
.notification-page .table-primary th,
.notification-page .table-light th {
    border-top: 0 !important;
    border-bottom: 1px solid var(--notify-line) !important;
    background: #f5f5f7 !important;
    color: #515154 !important;
    font-size: .76rem !important;
    font-weight: 650 !important;
    white-space: nowrap;
}
.notification-page .table tbody td {
    border-bottom: 1px solid #eeeef0 !important;
    color: var(--notify-ink) !important;
    vertical-align: middle;
}
.notification-page .table-striped > tbody > tr:nth-of-type(odd) > *,
.notification-page .table-hover > tbody > tr:hover > * { background: #fafafd !important; }
.notification-page .nav-tabs .nav-link,
.notification-page .nav-tabs-custom .nav-link {
    border: 0 !important;
    border-bottom: 2px solid transparent !important;
    background: transparent !important;
    color: var(--notify-secondary) !important;
    font-weight: 600;
}
.notification-page .nav-tabs .nav-link.active,
.notification-page .nav-tabs-custom .nav-link.active {
    border-bottom-color: var(--notify-blue) !important;
    color: var(--notify-blue) !important;
}
.notification-page .progress { overflow: hidden; border-radius: 999px; background: #e9e9ed !important; }
.notification-page .progress-bar { background: var(--notify-blue) !important; }
.notification-page .pagination .page-link { border-radius: 8px; color: var(--notify-blue); }
.notification-page .pagination .page-item.active .page-link { border-color: var(--notify-blue); background: var(--notify-blue); color: #fff; }
.notification-page .modal-content { overflow: hidden; border: 1px solid var(--notify-line); border-radius: 18px; box-shadow: 0 18px 60px rgba(0,0,0,.16); }
.notification-page .modal-header,
.notification-page .modal-footer { border-color: var(--notify-line); background: #fff; }
.notification-page .dataTables_wrapper .dataTables_paginate .paginate_button.current,
.notification-page .page-item.active .page-link { border-color: var(--notify-blue) !important; background: var(--notify-blue) !important; color: #fff !important; }
@media (max-width: 767.98px) {
    .notification-page .card-header { padding: 14px 16px; }
    .notification-page .card-body { padding: 16px; }
    .notification-page .page-title { align-items: flex-start !important; }
    .notification-page .table { min-width: 720px; }
}
@media (prefers-reduced-motion: reduce) {
    .notification-page *, .notification-page *::before, .notification-page *::after { scroll-behavior: auto !important; transition-duration: .01ms !important; animation-duration: .01ms !important; }
}
</style>
