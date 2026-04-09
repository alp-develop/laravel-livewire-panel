<div @if($pollSeconds > 0) wire:poll="{{ $pollSeconds * 1000 }}ms" @endif>
    <div style="background:var(--panel-card-bg,#fff);border-radius:10px;box-shadow:0 1px 4px rgba(0,0,0,0.08);border:1px solid var(--panel-card-border,#e8edf2);overflow:hidden">
        <div style="padding:1rem 1.25rem;border-bottom:1px solid var(--panel-card-border,#f3f4f6);font-weight:600;font-size:0.9rem;color:var(--panel-text-primary,#111827)">{{ $title }}</div>
        <div class="widget-table-wrap">
            @if (count($rows) > 0)
                <table class="widget-table">
                    @if (count($headers) > 0)
                        <thead>
                            <tr>
                                @foreach ($headers as $header)
                                    <th>{{ $header }}</th>
                                @endforeach
                            </tr>
                        </thead>
                    @endif
                    <tbody>
                        @foreach (array_slice($rows, 0, $limit) as $row)
                            <tr>
                                @foreach ($row as $cell)
                                    <td>{{ $cell }}</td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="widget-table-empty">{{ $emptyText }}</div>
            @endif
        </div>
    </div>
</div>