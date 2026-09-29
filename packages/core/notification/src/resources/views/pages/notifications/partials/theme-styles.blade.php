<style>
/* ==========================================================================
   Clean Station — Notification Dashboard Refined Color System
   ========================================================================== */

:root {
    /* Base Surfaces & Typography */
    --cs-page-bg: #F6F8FB;
    --cs-card-bg: #FFFFFF;
    --cs-text-primary: #1F2937;
    --cs-text-secondary: #64748B;
    --cs-border-subtle: #E2E8F0;
    --cs-border-muted: #F1F5F9;

    /* Clean Station Brand Tokens */
    --cs-brand-blue: #1F5FAF;
    --cs-brand-blue-hover: #174A8B;
    --cs-brand-blue-light: #E8F2FB;
    --cs-brand-blue-border: #BFDBFE;

    --cs-brand-teal: #159A9C;
    --cs-brand-teal-hover: #0F7577;
    --cs-brand-teal-light: #E6F7F7;
    --cs-brand-teal-border: #B2E5E7;

    --cs-sidebar: #123B63;
    --cs-sidebar-active: #E8F2FB;

    /* Semantic Tokens (Strictly Scoped) */
    --cs-success: #15803D;
    --cs-success-bg: #DCFCE7;
    --cs-success-border: #BBF7D0;

    --cs-warning: #B45309;
    --cs-warning-bg: #FEF3C7;
    --cs-warning-border: #FDE68A;

    --cs-danger: #B91C1C;
    --cs-danger-bg: #FEE2E2;
    --cs-danger-border: #FECACA;

    --cs-info: #0369A1;
    --cs-info-bg: #E0F2FE;
    --cs-info-border: #BAE6FD;

    /* Marketing (Strictly Purple) */
    --cs-marketing: #6D28D9;
    --cs-marketing-bg: #F5F3FF;
    --cs-marketing-border: #DDD6FE;

    /* Neutral & Legacy */
    --cs-neutral: #475569;
    --cs-neutral-bg: #F1F5F9;
    --cs-neutral-border: #CBD5E1;
}

/* 1. Surfaces & Global Layout */
body, .content-wrapper, .layout-page, #kt_content, .container-p-y {
    background-color: var(--cs-page-bg) !important;
    color: var(--cs-text-primary) !important;
}

/* 2. Cards & Containers */
.card, .card-flush {
    background-color: var(--cs-card-bg) !important;
    border: 1px solid var(--cs-border-subtle) !important;
    box-shadow: 0 1px 3px 0 rgba(15, 23, 42, 0.04), 0 1px 2px -1px rgba(15, 23, 42, 0.03) !important;
    border-radius: 0.625rem !important;
}

.card-header {
    background-color: var(--cs-card-bg) !important;
    border-bottom: 1px solid var(--cs-border-muted) !important;
}

.card-footer {
    background-color: var(--cs-card-bg) !important;
    border-top: 1px solid var(--cs-border-muted) !important;
}

/* 3. Sidebar Override (Clean Station Brand Navy) */
.layout-menu.bg-menu-theme {
    background-color: var(--cs-sidebar) !important;
}

.layout-menu.bg-menu-theme .menu-link {
    color: #CBD5E1 !important;
}

.layout-menu.bg-menu-theme .menu-link:hover {
    background-color: rgba(255, 255, 255, 0.07) !important;
    color: #FFFFFF !important;
}

.layout-menu.bg-menu-theme .menu-item.active > .menu-link,
.layout-menu.bg-menu-theme .menu-sub > .menu-item.active > .menu-link {
    background-color: var(--cs-sidebar-active) !important;
    color: var(--cs-brand-blue) !important;
    font-weight: 700 !important;
    border-radius: 0.375rem !important;
}

.layout-menu.bg-menu-theme .menu-item.active > .menu-link i,
.layout-menu.bg-menu-theme .menu-sub > .menu-item.active > .menu-link i {
    color: var(--cs-brand-blue) !important;
}

/* 4. Semantic Badges — Light Pastel Background with High-Contrast Dark Text */
.badge {
    font-weight: 600 !important;
    padding: 0.35em 0.7em !important;
    border-radius: 0.375rem !important;
    letter-spacing: 0.01em !important;
    display: inline-flex !important;
    align-items: center !important;
    gap: 0.25rem !important;
}

/* Success (Green) */
.badge.bg-label-success, .badge.badge-light-success, .badge.badge-success {
    background-color: var(--cs-success-bg) !important;
    color: var(--cs-success) !important;
    border: 1px solid var(--cs-success-border) !important;
}

/* Warning (Amber / Yellow) */
.badge.bg-label-warning, .badge.badge-light-warning, .badge.badge-warning {
    background-color: var(--cs-warning-bg) !important;
    color: var(--cs-warning) !important;
    border: 1px solid var(--cs-warning-border) !important;
}

/* Danger / Failure (Red) */
.badge.bg-label-danger, .badge.badge-light-danger, .badge.badge-danger {
    background-color: var(--cs-danger-bg) !important;
    color: var(--cs-danger) !important;
    border: 1px solid var(--cs-danger-border) !important;
}

/* Info (Blue) */
.badge.bg-label-info, .badge.badge-light-info {
    background-color: var(--cs-info-bg) !important;
    color: var(--cs-info) !important;
    border: 1px solid var(--cs-info-border) !important;
}

/* Primary Brand (App FCM / Navigation) */
.badge.bg-label-primary, .badge.badge-light-primary {
    background-color: var(--cs-brand-blue-light) !important;
    color: var(--cs-brand-blue) !important;
    border: 1px solid var(--cs-brand-blue-border) !important;
}

/* Marketing Strictly (Purple) */
.badge.bg-label-marketing, .badge.badge-light-marketing,
.badge.bg-label-purple, .badge.badge-light-purple {
    background-color: var(--cs-marketing-bg) !important;
    color: var(--cs-marketing) !important;
    border: 1px solid var(--cs-marketing-border) !important;
}

/* Neutral / Secondary / System / Legacy */
.badge.bg-label-secondary, .badge.badge-light-secondary,
.badge.bg-label-dark, .badge.badge-light-dark {
    background-color: var(--cs-neutral-bg) !important;
    color: var(--cs-neutral) !important;
    border: 1px solid var(--cs-neutral-border) !important;
}

/* 5. Operation Action Buttons */
.btn-operation {
    color: var(--cs-brand-blue) !important;
    border: 1.5px solid var(--cs-brand-blue-border) !important;
    background-color: #FFFFFF !important;
    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.03) !important;
    transition: all 0.2s ease-in-out !important;
}

.btn-operation:hover {
    background-color: var(--cs-brand-blue-light) !important;
    border-color: var(--cs-brand-blue) !important;
    color: var(--cs-brand-blue-hover) !important;
}

.btn-operation span {
    color: var(--cs-brand-blue) !important;
}

.btn-operation.btn-operation-teal {
    color: var(--cs-brand-teal) !important;
    border-color: var(--cs-brand-teal-border) !important;
}

.btn-operation.btn-operation-teal:hover {
    background-color: var(--cs-brand-teal-light) !important;
    border-color: var(--cs-brand-teal) !important;
    color: var(--cs-brand-teal-hover) !important;
}

.btn-operation.btn-operation-teal i {
    color: var(--cs-brand-teal) !important;
}

.btn-operation.btn-operation-warning {
    color: var(--cs-warning) !important;
    border-color: var(--cs-warning-border) !important;
}

.btn-operation.btn-operation-warning:hover {
    background-color: var(--cs-warning-bg) !important;
    border-color: var(--cs-warning) !important;
    color: var(--cs-warning) !important;
}

.btn-operation.btn-operation-warning i {
    color: var(--cs-warning) !important;
}

/* 6. Standard Action Buttons */
.btn-primary, button.btn-primary, a.btn-primary {
    background-color: var(--cs-brand-blue) !important;
    border-color: var(--cs-brand-blue) !important;
    color: #FFFFFF !important;
    box-shadow: 0 1px 2px rgba(31, 95, 175, 0.15) !important;
}

.btn-primary:hover, .btn-primary:focus, .btn-primary:active {
    background-color: var(--cs-brand-blue-hover) !important;
    border-color: var(--cs-brand-blue-hover) !important;
    color: #FFFFFF !important;
}

.btn-light, .btn-secondary {
    background-color: #FFFFFF !important;
    border: 1px solid var(--cs-border-subtle) !important;
    color: var(--cs-text-secondary) !important;
}

.btn-light:hover, .btn-secondary:hover {
    background-color: var(--cs-brand-blue-light) !important;
    border-color: var(--cs-brand-blue-border) !important;
    color: var(--cs-brand-blue) !important;
}

/* 7. Tables: Crisp Headers and Calm Rows */
.table thead th, thead.table-primary th, .table-primary th {
    background-color: #F8FAFC !important;
    color: #334155 !important;
    border-bottom: 2px solid var(--cs-border-subtle) !important;
    font-weight: 700 !important;
    font-size: 12px !important;
    letter-spacing: 0.01em !important;
}

.table tbody tr:hover {
    background-color: #F8FAFC !important;
}

.table tbody td {
    color: var(--cs-text-primary) !important;
    border-bottom: 1px solid var(--cs-border-muted) !important;
    vertical-align: middle !important;
}

/* 8. Metric Stat Counters & Pills */
.stat-pill-total {
    background-color: var(--cs-success-bg) !important;
    border: 1px solid var(--cs-success-border) !important;
    color: var(--cs-success) !important;
}

.stat-pill-total:hover {
    background-color: #CFF7D7 !important;
}

.stat-pill-trash {
    background-color: var(--cs-danger-bg) !important;
    border: 1px solid var(--cs-danger-border) !important;
    color: var(--cs-danger) !important;
}

.stat-pill-trash:hover {
    background-color: #FCD8D8 !important;
}

/* 9. Symbol Boxes & Icons */
.symbol-label.bg-light-primary {
    background-color: var(--cs-brand-blue-light) !important;
    color: var(--cs-brand-blue) !important;
}

.symbol-label.bg-light-success {
    background-color: var(--cs-success-bg) !important;
    color: var(--cs-success) !important;
}

.symbol-label.bg-light-warning {
    background-color: var(--cs-warning-bg) !important;
    color: var(--cs-warning) !important;
}

.symbol-label.bg-light-danger {
    background-color: var(--cs-danger-bg) !important;
    color: var(--cs-danger) !important;
}

.symbol-label.bg-light-info {
    background-color: var(--cs-info-bg) !important;
    color: var(--cs-info) !important;
}

/* 10. Tabs */
.nav-tabs-custom .nav-link, .nav-tabs .nav-link {
    color: var(--cs-text-secondary) !important;
    border: none !important;
    border-bottom: 2px solid transparent !important;
    font-weight: 600 !important;
    padding: 0.75rem 1rem !important;
}

.nav-tabs-custom .nav-link:hover, .nav-tabs .nav-link:hover {
    color: var(--cs-brand-blue) !important;
    border-bottom-color: var(--cs-brand-blue-border) !important;
}

.nav-tabs-custom .nav-link.active, .nav-tabs .nav-link.active {
    color: var(--cs-brand-blue) !important;
    border-bottom: 2px solid var(--cs-brand-blue) !important;
    background-color: transparent !important;
}

/* 11. Pagination & DataTables Buttons */
.dataTables_wrapper .dataTables_paginate .paginate_button.current,
.dataTables_wrapper .dataTables_paginate .paginate_button.current:hover,
.page-item.active .page-link {
    background: var(--cs-brand-blue) !important;
    background-color: var(--cs-brand-blue) !important;
    border-color: var(--cs-brand-blue) !important;
    color: #FFFFFF !important;
}

div.dt-buttons>.dt-button {
    background: var(--cs-brand-blue) !important;
    background-color: var(--cs-brand-blue) !important;
    border-color: var(--cs-brand-blue) !important;
    color: #FFFFFF !important;
}

/* Form focus rings */
.form-control:focus, .form-select:focus {
    border-color: var(--cs-brand-blue) !important;
    box-shadow: 0 0 0 0.2rem rgba(31, 95, 175, 0.15) !important;
}
</style>
