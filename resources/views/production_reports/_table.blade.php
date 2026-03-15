<div class="table-responsive">
    <table class="table table-bordered table-hover align-middle">
        <thead class="table-primary">
            <tr>
                <th>#</th>
                <th>Nama Barang</th>
                <th>Kategori</th>
                <th>Target Qty</th>
                <th>Selesai</th>
                <th>Tanggal Selesai</th>
                <th>Jam Selesai</th>
                <th>Lokasi</th>
            </tr>
        </thead>
        <tbody>
            @if ($operators === null)
                <tr>
                    <td colspan="8" class="text-center text-muted py-4">
                        Silakan gunakan filter di atas untuk menampilkan data.
                    </td>
                </tr>
            @else
                @forelse ($operators as $operator)
                    @php
                        $isWip    = $operator->wip_schedule_id !== null;
                        $schedule = $isWip ? $operator->wipSchedule : $operator->finishGoodSchedule;
                        $targetQty = $isWip ? ($schedule?->qty ?? 0) : ($schedule?->quantity ?? 0);
                    @endphp
                    <tr>
                        <td>{{ $operators->firstItem() + $loop->index }}</td>
                        <td>{{ $schedule?->name ?? '-' }}</td>
                        <td>
                            @if ($isWip)
                                <span class="badge bg-warning text-dark">WIP</span>
                            @else
                                <span class="badge bg-success">Finish Good</span>
                            @endif
                        </td>
                        <td>{{ $targetQty }}</td>
                        <td>
                            {{ $operator->total_selesai }} / {{ $targetQty }}
                            <div class="progress mt-1" style="height: 5px;">
                                <div class="progress-bar bg-success"
                                    style="width: {{ $targetQty > 0 ? ($operator->total_selesai / $targetQty) * 100 : 0 }}%">
                                </div>
                            </div>
                        </td>
                        <td>{{ $operator->tanggal_selesai ? \Carbon\Carbon::parse($operator->tanggal_selesai)->format('d/m/Y') : '-' }}</td>
                        <td>{{ $operator->waktu_selesai ? \Carbon\Carbon::parse($operator->waktu_selesai)->format('H:i') : '-' }}</td>
                        <td>{{ $schedule?->workPlace?->name ?? '-' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted py-4">No results found.</td>
                    </tr>
                @endforelse
            @endif
        </tbody>
    </table>
</div>

@if ($operators !== null)
    <div class="d-flex justify-content-end">
        {{ $operators->links() }}
    </div>
@endif