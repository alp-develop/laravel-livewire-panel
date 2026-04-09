<div>
<div class="panel-page-header">
    <h1>{{ __('panel::messages.dashboard') }}</h1>
</div>

@once
<style>
    .widgets-stats-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 1.25rem;
        margin-bottom: 1.25rem;
    }
    @media (max-width: 1100px) {
        .widgets-stats-grid { grid-template-columns: repeat(2, 1fr); }
    }
    @media (max-width: 640px) {
        .widgets-stats-grid { grid-template-columns: 1fr; }
    }
    .widgets-charts-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1.25rem;
        margin-bottom: 1.25rem;
    }
    @media (max-width: 900px) {
        .widgets-charts-grid { grid-template-columns: 1fr; }
    }
</style>
@endonce

<div class="widgets-stats-grid">
    @if (!empty($stats))
        @foreach ($stats as $i => $stat)
            @livewire('widgets.stats-card', $stat, key('stat-' . $i))
        @endforeach
    @else
        <livewire:widgets.stats-card
            title="Registered users"
            value="1,284"
            icon="users"
            trend="+12% this month"
            trendType="up"
        />
        <livewire:widgets.stats-card
            title="Monthly revenue"
            value="$8,400"
            icon="banknotes"
            trend="+4.6% vs previous"
            trendType="up"
        />
        <livewire:widgets.stats-card
            title="Active sessions"
            value="38"
            icon="computer-desktop"
            trend="-3 today"
            trendType="down"
        />
        <livewire:widgets.stats-card
            title="Conversion rate"
            value="3.8%"
            icon="arrow-trending-up"
            trend="+0.2% vs last week"
            trendType="up"
        />
    @endif
</div>

<div class="widgets-charts-grid">
    <livewire:widgets.chart-widget
        title="Monthly activity"
        type="line"
        :labels="['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul']"
        :datasets="[['label' => 'Visits', 'data' => [65, 78, 90, 81, 87, 110, 98], 'borderColor' => 'rgb(79, 70, 229)', 'backgroundColor' => 'rgba(79,70,229,0.1)', 'tension' => 0.4, 'fill' => true]]"
    />
    <livewire:widgets.chart-widget
        title="User distribution"
        type="doughnut"
        :labels="['Admins', 'Users', 'Editors']"
        :datasets="[['data' => [12, 58, 30], 'backgroundColor' => ['rgb(79,70,229)', 'rgb(16,185,129)', 'rgb(245,158,11)']]]"
    />
</div>

<livewire:widgets.recent-table
    title="Recent activity"
    :headers="['User', 'Action', 'Date']"
    :rows="[
        ['Admin Demo', 'Logged in', now()->subDays(0)->format('Y-m-d H:i')],
        ['Admin Demo', 'Updated settings', now()->subDays(1)->format('Y-m-d H:i')],
        ['Admin Demo', 'Viewed dashboard', now()->subDays(2)->format('Y-m-d H:i')],
        ['Admin Demo', 'Logged out', now()->subDays(3)->format('Y-m-d H:i')],
    ]"
/>
</div>
