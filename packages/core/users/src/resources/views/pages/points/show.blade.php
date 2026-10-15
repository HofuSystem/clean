@extends('admin::layouts.dashboard')
@section('content')
    <div class="point-detail-page" dir="rtl">
        <header class="point-page-header">
            <div>
                <div class="point-eyebrow">{{ trans('points_ui.movements') }}</div>
                <h1>{{ trans('points_ui.point_movement') }} <span>#{{ $item->id }}</span></h1>
            </div>
            <a href="{{ route('dashboard.points.index') }}" class="point-back-button">
                <i class="fas fa-arrow-right" aria-hidden="true"></i>
                <span>{{ trans('Back') }}</span>
            </a>
        </header>

        <section class="point-hero-card">
            <div class="point-hero-main">
                <div class="point-hero-topline">
                    <span class="point-type-badge {{ $item->operation === 'deposit' ? 'is-credit' : 'is-debit' }}">
                        <i class="fas {{ $item->operation === 'deposit' ? 'fa-arrow-down' : 'fa-arrow-up' }}" aria-hidden="true"></i>
                        {{ $item->operation === 'deposit' ? trans('points_ui.points_added') : trans('points_ui.points_deducted') }}
                    </span>
                    <span class="point-recorded-at">{{ optional($item->created_at)->format('Y/m/d · H:i') }}</span>
                </div>
                <div class="point-amount {{ $item->operation === 'deposit' ? 'is-credit' : 'is-debit' }}">
                    <span>{{ $item->operation === 'deposit' ? '+' : '−' }}{{ number_format((float) $item->amount, 2) }}</span>
                    <small>{{ trans('points_ui.points') }}</small>
                </div>
                <div class="point-reason">
                    <span class="point-reason-label">{{ trans('points_ui.reason') }}</span>
                    <strong>{{ $item->display_title }}</strong>
                </div>
            </div>
            <aside class="point-expiry-panel {{ $item->expire_at && $item->expire_at->isPast() ? 'is-expired' : '' }}">
                <div class="point-expiry-icon"><i class="far fa-clock" aria-hidden="true"></i></div>
                <div>
                    <span class="point-card-label">{{ trans('points_ui.expiry_date') }}</span>
                    @if ($item->expire_at)
                        <strong>{{ $item->expire_at->format('Y/m/d · H:i') }}</strong>
                        <small>{{ $item->expire_at->isPast() ? trans('points_ui.expired') : trans('points_ui.not_expired') }}</small>
                    @else
                        <strong>{{ trans('points_ui.no_expiry') }}</strong>
                    @endif
                </div>
            </aside>
        </section>

        <div class="point-detail-grid">
            <section class="point-info-card point-customer-card">
                <div class="point-card-heading">
                    <span class="point-icon-circle"><i class="far fa-user" aria-hidden="true"></i></span>
                    <h2>{{ trans('points_ui.customer') }}</h2>
                </div>
                @if ($item->user)
                    <a class="point-primary-value" href="{{ route('dashboard.users.show', $item->user->id) }}">{{ $item->user->fullname ?: trans('points_ui.no_name') }}</a>
                    <div class="point-secondary-value" dir="ltr">{{ $item->user->phone ?: '—' }}</div>
                    <div class="point-balance-row">
                        <span>{{ trans('points_ui.current_balance') }}</span>
                        <strong>{{ number_format((float) $item->user->points_balance, 2) }} <small>{{ trans('points_ui.points') }}</small></strong>
                    </div>
                @else
                    <div class="point-primary-value">{{ trans('points_ui.account_unavailable') }}</div>
                @endif
            </section>

            <section class="point-info-card">
                <div class="point-card-heading">
                    <span class="point-icon-circle"><i class="fas fa-receipt" aria-hidden="true"></i></span>
                    <h2>{{ trans('points_ui.related_order') }}</h2>
                </div>
                @if ($item->order)
                    <a class="point-order-link" href="{{ route('dashboard.orders.show', $item->order->id) }}">
                        <span>{{ $item->order->reference_id ?: ('#' . $item->order->id) }}</span>
                        <i class="fas fa-external-link-alt" aria-hidden="true"></i>
                    </a>
                @else
                    <div class="point-primary-value">{{ str_contains(strtolower($item->title), 'order') ? $item->display_title : '—' }}</div>
                @endif
            </section>

            <section class="point-info-card">
                <div class="point-card-heading">
                    <span class="point-icon-circle"><i class="far fa-id-badge" aria-hidden="true"></i></span>
                    <h2>{{ trans('points_ui.added_by') }}</h2>
                </div>
                @if ($item->creator)
                    <a class="point-primary-value" href="{{ route('dashboard.users.show', $item->creator->id) }}">{{ $item->creator->fullname ?: trans('points_ui.no_name') }}</a>
                @else
                    <div class="point-primary-value">{{ trans('points_ui.automatic_unknown') }}</div>
                @endif
            </section>
        </div>

        @include('comment::inc.comment-section', ['commentUrl' => route('dashboard.points.comment', $item->id)])
    </div>
@endsection

@push('css')
<style>
    .point-detail-page {
        --point-ink: #1d1d1f;
        --point-muted: #77777d;
        --point-line: #e7e7eb;
        --point-blue: #1677e8;
        --point-green: #168a55;
        --point-red: #c93434;
        max-width: 1280px;
        margin: 0 auto;
        padding: 28px 24px 48px;
        color: var(--point-ink);
        text-align: right;
    }
    .point-page-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 20px;
        margin-bottom: 22px;
    }
    .point-eyebrow, .point-card-label, .point-reason-label {
        color: var(--point-muted);
        font-size: 13px;
        font-weight: 600;
    }
    .point-page-header h1 {
        margin: 5px 0 0;
        color: var(--point-ink);
        font-size: clamp(23px, 2.2vw, 30px);
        font-weight: 700;
        letter-spacing: -0.4px;
    }
    .point-page-header h1 span { color: #8a8a91; font-weight: 500; }
    .point-back-button {
        display: inline-flex;
        align-items: center;
        gap: 9px;
        min-height: 42px;
        padding: 0 16px;
        border: 1px solid var(--point-line);
        border-radius: 13px;
        background: rgba(255,255,255,.9);
        color: #34343a;
        font-size: 14px;
        font-weight: 600;
        text-decoration: none;
        transition: background .16s ease, border-color .16s ease, transform .16s ease;
    }
    .point-back-button:hover { background: #fff; border-color: #c9c9d0; color: var(--point-ink); transform: translateY(-1px); }
    .point-hero-card, .point-info-card {
        border: 1px solid rgba(20,20,30,.06);
        border-radius: 22px;
        background: #fff;
        box-shadow: 0 8px 28px rgba(20,20,35,.055);
    }
    .point-hero-card {
        display: grid;
        grid-template-columns: minmax(0, 1fr) 285px;
        gap: 28px;
        align-items: start;
        padding: 28px;
        margin-bottom: 18px;
    }
    .point-hero-topline { display: flex; align-items: center; justify-content: space-between; gap: 16px; }
    .point-type-badge {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 7px 11px;
        border-radius: 999px;
        font-size: 13px;
        font-weight: 700;
    }
    .point-type-badge.is-credit { background: #eaf7f0; color: var(--point-green); }
    .point-type-badge.is-debit { background: #fff0ef; color: var(--point-red); }
    .point-recorded-at { color: var(--point-muted); font-size: 13px; }
    .point-amount { display: flex; align-items: baseline; gap: 9px; margin: 22px 0 20px; }
    .point-amount span { font-size: clamp(38px, 5vw, 54px); line-height: 1; font-weight: 700; letter-spacing: -1.6px; }
    .point-amount small { color: var(--point-muted); font-size: 16px; font-weight: 600; }
    .point-amount.is-credit { color: var(--point-green); }
    .point-amount.is-debit { color: var(--point-red); }
    .point-reason { display: flex; flex-direction: column; gap: 6px; padding-top: 17px; border-top: 1px solid var(--point-line); }
    .point-reason strong { color: #303036; font-size: 16px; line-height: 1.7; font-weight: 600; }
    .point-expiry-panel {
        display: flex;
        align-items: flex-start;
        align-self: start;
        gap: 13px;
        padding: 15px;
        border: 1px solid #e7edf5;
        border-radius: 17px;
        background: #f7f9fc;
    }
    .point-expiry-panel.is-expired { border-color: #f2d7d5; background: #fff8f7; }
    .point-expiry-icon, .point-icon-circle {
        display: grid;
        place-items: center;
        flex: 0 0 38px;
        width: 38px;
        height: 38px;
        border-radius: 13px;
        background: #eaf2fc;
        color: var(--point-blue);
        font-size: 15px;
    }
    .point-expiry-panel.is-expired .point-expiry-icon { background: #fde9e7; color: var(--point-red); }
    .point-expiry-panel > div:last-child { display: flex; flex-direction: column; gap: 5px; min-width: 0; }
    .point-expiry-panel strong { color: #303036; font-size: 15px; }
    .point-expiry-panel small { color: var(--point-muted); font-size: 12px; }
    .point-detail-grid { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); align-items: start; gap: 16px; margin-bottom: 18px; }
    .point-info-card { min-height: 0; padding: 19px; }
    .point-card-heading { display: flex; align-items: center; gap: 11px; margin-bottom: 17px; }
    .point-card-heading h2 { margin: 0; color: #323238; font-size: 15px; font-weight: 700; }
    .point-icon-circle { width: 34px; height: 34px; flex-basis: 34px; border-radius: 11px; }
    .point-primary-value { display: block; color: #25252a; font-size: 17px; font-weight: 700; text-decoration: none; line-height: 1.5; }
    a.point-primary-value:hover { color: var(--point-blue); }
    .point-secondary-value { margin-top: 5px; color: var(--point-muted); font-size: 13px; line-height: 1.6; overflow-wrap: anywhere; }
    .point-balance-row { display: flex; align-items: center; justify-content: space-between; gap: 12px; margin-top: 19px; padding-top: 15px; border-top: 1px solid var(--point-line); color: var(--point-muted); font-size: 13px; }
    .point-balance-row strong { color: #25252a; font-size: 16px; }
    .point-balance-row small { color: var(--point-muted); font-size: 12px; }
    .point-order-link { display: inline-flex; align-items: center; gap: 9px; color: var(--point-blue); font-size: 17px; font-weight: 700; text-decoration: none; }
    .point-order-link:hover { color: #075fc4; }
    .point-detail-page .comment-section, .point-detail-page .card { border-radius: 18px; }
    @media (max-width: 1050px) {
        .point-detail-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        .point-hero-card { grid-template-columns: minmax(0, 1fr) 245px; }
    }
    @media (max-width: 700px) {
        .point-detail-page { padding: 18px 14px 32px; }
        .point-page-header { align-items: flex-start; }
        .point-page-header h1 { font-size: 22px; }
        .point-back-button { min-height: 38px; padding: 0 12px; }
        .point-back-button span { display: none; }
        .point-hero-card { grid-template-columns: 1fr; gap: 18px; padding: 19px; border-radius: 19px; }
        .point-hero-topline { align-items: flex-start; flex-direction: column; gap: 9px; }
        .point-amount { margin: 20px 0 17px; }
        .point-detail-grid { grid-template-columns: 1fr; gap: 12px; }
        .point-info-card { min-height: 0; padding: 18px; }
    }
    @media (prefers-reduced-motion: reduce) {
        .point-back-button { transition: none; }
    }
</style>
@endpush
